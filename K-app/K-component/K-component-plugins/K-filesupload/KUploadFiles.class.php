<?php
/*
 * @license AGPL-3.0
 * 
 * @copyright Copyright (c) 2026 EFA, Ecole française d'athènes, EFAthenes.
 *
 * @author Louis Mulot <louis.mulot@efa.gr>
 * 
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 * 
 */
class KUploadFiles extends KComponent
{
    private bool $debug=false;
    private int $upload_max_size=30000000000000000;
    const string UPLOAD_TOKEN="UPLOAD_TOKEN";
    public static string $IMPORT_LOADING="import_loading";
    public static int $IMPORT_LOADING_TAG=12345692153;
    private string $message="";
        
    /**
     * 
     * @param KURL $url_end_upload
     * @param bool $multiple
     * @param KComponent $title
     * @param string $message
     * @param bool $url_new_page
     * @param array<int,string> $extension_allowed
     */
    function __construct(KURL $url_end_upload,bool $multiple=false, ?KComponent $title=null,string $message="",bool $url_new_page=false,array $extension_allowed=array())
    {
        parent::__construct();
        $this->setName("KUploadFiles_".KRandom::makeRandom());
        $this->setId();
        $this->message=$message;
        
        $token=KRandom::makeRandomUniquId();       
        
        $route_loading_1=KRoute::makeActionKURL(RoutesItems::$IMAGE_LOADER,["type"=>8])->printURLWithoutAmp();

        $layout=KApp::getInstance()->getLayout();
        $layout->addCssFileToBuffer(__DIR__."/css/jquery.dm-uploader.min.css");
        $layout->addJsFileToBuffer(__DIR__."/js/jquery.dm-uploader.min.js");
        
        SessionMemory::getInstance()->putOrReplace(self::UPLOAD_TOKEN,$token);        
        $url_end_upload->addOrReplace(self::UPLOAD_TOKEN,$token);
        $url_end_upload->addOrReplace(self::$IMPORT_LOADING,self::$IMPORT_LOADING_TAG);  
        
        $url_action_upload=KRoute::makeActionKURL(RoutesItems::$UPLOAD_FILES_ACTION,["UPLOAD_TOKEN"=>$token])->printURLWithoutAmp();
        
        $label_deposer=LanguageManager::_("COMP_KUPLOAD_FILE_LABEL");
        $multiple_string="false";
        if($multiple)
        {
            $multiple_string="true";
            $label_deposer=LanguageManager::_("COMP_KUPLOAD_FILES_LABEL");
        }

        
        if(!is_null($title))
        {
            $this->addComponent($title);
        }
        
        $message_extension="";
        $extension_string="";
        if(count($extension_allowed))
        {
            $message_extension=LanguageManager::_("COMP_KUPLOAD_EXTENSION_ACCEPTED");
           // $message_extension_string="";
            foreach($extension_allowed as $string)
            {
                if($extension_string!="")
                {
                    $message_extension.=", ";
                    $extension_string.=',';
                }
                $extension_string.='"'.$string.'"';
                $message_extension.=$string;
            }
            $extension_string="extFilter: [".$extension_string."],";

            $this->addHtmlComponent('<p class="text-primary">'.$message_extension.'</p>');
        }   
        
        $post_max_size=ini_get('upload_max_filesize');
        
        $this->addHtmlComponent('<p class="text-primary">'.LanguageManager::_("COMP_KUPLOAD_MAX_SIZE").' '.$post_max_size.'</p>');
        
        

        $html='
<div class="upload_block_param">
   
    <div id="upload_result_'.$this->getName().'">
    </div>
    
    <div id="upload_part_'.$this->getName().'">
    
        <div class="row">
            <div class="col-md-6 col-sm-12">        
                <div class="card">

                    <!-- Our markup, the important part here! -->
                    <div id="drag-and-drop-zone_'.$this->getName().'" class="dm-uploader p-5">
                      <h3 class="mb-5 mt-5 text-muted">'.$label_deposer.'</h3>
                          
                         
                        <input type="file" title=\'Click to add Files\' />

                    </div><!-- /uploader -->
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
              <div class="card 1h-100">
                <div class="card-header">
                  Liste des fichiers à charger :
                </div>

                <ul class="list-unstyled p-2 d-flex flex-column col dm-uploader-list-content " id="files_'.$this->getName().'">
                  <li class="text-muted text-center empty">'.LanguageManager::_("COMP_KUPLOAD_LABEL_START").'</li>
                </ul>
              </div>
            </div>
        </div>    
        <div class="row">    
            <div class="col-md-6 col-sm-12" id="button_start">
                <br />
                <button class="btn btn-primary button_search" type="button" onclick="start_upload_'.$this->getName().'();" >
                    <i class="fa fa-upload"></i>
                    &nbsp;<span class="">'.LanguageManager::_("COMP_KUPLOAD_BUTTON_START").'</span>
                </button>

              <br />
              <br />
            </div>
            <div id="button_search_loader"></div>
        </div>
';
        if($this->debug)
        {
            $html.='
        <div class="row">
          <div class="col-12">
             <div class="card h-100">
              <div class="card-header">
                Debug Messages
              </div>

              <ul class="list-group list-group-flush" id="debug_'.$this->getName().'">
                <li class="list-group-item text-muted empty">Loading plugin....</li>
              </ul>
            </div>
          </div>
        </div>
';
        }
        
        
        $html.='
        <!-- File item template -->
        <script type="text/html" id="files-template_'.$this->getName().'">
          <li class="media">
            <div class="media-body mb-1">
              <p class="mb-2">
                <strong>%%filename%%</strong> - '.LanguageManager::_("COMP_KUPLOAD_STATUS_1").': <span class="text-muted">'.LanguageManager::_("COMP_KUPLOAD_STATUS_2").'</span>
              </p>
              <div class="progress mb-2">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                  role="progressbar"
                  style="width: 0%" 
                  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                </div>
              </div>
              <hr class="mt-1 mb-1" />
            </div>
          </li>
        </script>

        <!-- Debug item template -->
        <script type="text/html" id="debug-template_'.$this->getName().'">
          <li class="list-group-item text-%%color%%"><strong>%%date%%</strong>: %%message%%</li>
        </script>
    
    </div>   
</div> 
<br />
<br />
<br />
<br />
<br />
';       
        $this->addHtmlComponent($html);


        $js="    
var almost_one_file_uploaded_".$this->getName()."=false;
var upload_error_".$this->getName()."=false;
var redirect_to_new_page_".$this->getName()."=".convertBoolToString($url_new_page).";
function ui_add_log_".$this->getName()."(message, color)
{
  var d = new Date();

  var dateString = (('0' + d.getHours())).slice(-2) + ':' +
    (('0' + d.getMinutes())).slice(-2) + ':' +
    (('0' + d.getSeconds())).slice(-2);

  color = (typeof color === 'undefined' ? 'muted' : color);

  var template = $('#debug-template_".$this->getName()."').text();
  template = template.replace('%%date%%', dateString);
  template = template.replace('%%message%%', message);
  template = template.replace('%%color%%', color);
  
  $('#debug_".$this->getName()."').find('li.empty').fadeOut(); // remove the 'no messages yet'
  $('#debug_".$this->getName()."').prepend(template);
";
    if($this->debug)
    {
        $js.=" 
  console.log(message);
";
    }
    $js.=" 
}

// Creates a new file and add it to our list
function ui_multi_add_file_".$this->getName()."(id, file)
{
    var template = $('#files-template_".$this->getName()."').text();
    template = template.replace('%%filename%%', file.name);

    template = $(template);
    template.prop('id', 'uploaderFile_".$this->getName()."' + id);
    template.data('file-id', id);

    $('#files_".$this->getName()."').find('li.empty').fadeOut(); // remove the 'no files yet'
    $('#files_".$this->getName()."').prepend(template);
}

function ui_not_multi_add_file_".$this->getName()."(id, file)
{
    var template = $('#files-template_".$this->getName()."').text();
    template = template.replace('%%filename%%', file.name);

    template = $(template);
    template.prop('id', 'uploaderFile_".$this->getName()."' + id);
    template.data('file-id', id);

    $('#files_".$this->getName()."').empty();
    $('#files_".$this->getName()."').prepend(template);
}

// Changes the status messages on our list
function ui_multi_update_file_status_".$this->getName()."(id, status, message)
{
  $('#uploaderFile_".$this->getName()."' + id).find('span').html(message).prop('class', 'status text-' + status);
}

// Updates a file progress, depending on the parameters it may animate it or change the color.
function ui_multi_update_file_progress_".$this->getName()."(id, percent, color, active)
{
  color = (typeof color === 'undefined' ? false : color);
  active = (typeof active === 'undefined' ? true : active);

  var bar = $('#uploaderFile_".$this->getName()."' + id).find('div.progress-bar');

  bar.width(percent + '%').attr('aria-valuenow', percent);
  bar.toggleClass('progress-bar-striped progress-bar-animated', active);

  if (percent === 0){
    bar.html('');
  } else {
    bar.html(percent + '%');
  }

  if (color !== false){
    bar.removeClass('bg-success bg-info bg-warning bg-danger');
    bar.addClass('bg-' + color);
  }
}

function start_upload_".$this->getName()."()
{
    $('#button_start').hide();
    $('#button_search_loader').load('".$route_loading_1."',function()
    {
        $('#drag-and-drop-zone_".$this->getName()."').dmUploader('start');
    });
}

$(function()
{
  $('#drag-and-drop-zone_".$this->getName()."').dmUploader({
    url: '".$url_action_upload."',
    maxFileSize: ".$this->upload_max_size.", 
    auto: false,
    error_string : '',
    queue:true,
    ".$extension_string."
    onDragEnter: function(){
      // Happens when dragging something over the DnD area
      this.addClass('active');
    },
    onDragLeave: function(){
      // Happens when dragging something OUT of the DnD area
      this.removeClass('active');
    },
    onInit: function(){
      // Plugin is ready to use
      ui_add_log_".$this->getName()."('Penguin initialized :)', 'info');
      this.multiple=".$multiple_string.";
    },
    onComplete: function(){
      // All files in the queue are processed (success or error)
      ui_add_log_".$this->getName()."('All pending tranfers finished');
    },
    onNewFile: function(id, file){
      // When a new file is added using the file selector or the DnD area
      //console.log(this);
      ui_add_log_".$this->getName()."('New file added #' + id+ ' | multiple :'+this.multiple);
      if(this.multiple)
      {
        ui_multi_add_file_".$this->getName()."(id, file);
      }
      else
      {     
        $('#drag-and-drop-zone_".$this->getName()."').dmUploader('reset');
        ui_not_multi_add_file_".$this->getName()."(id, file);
      }
    },
    onBeforeUpload: function(id)
    {
      // about tho start uploading a file
      ui_add_log_".$this->getName()."('Starting the upload of #' + id);
      ui_multi_update_file_status_".$this->getName()."(id, 'uploading', '".addslashes(LanguageManager::_("COMP_KUPLOAD_STATUS_3"))."');
      ui_multi_update_file_progress_".$this->getName()."(id, 0, '', true);
    },
    onUploadCanceled: function(id) {
      // Happens when a file is directly canceled by the user.
      ui_multi_update_file_status_".$this->getName()."(id, 'warning', '".addslashes(LanguageManager::_("COMP_KUPLOAD_STATUS_4"))."');
      ui_multi_update_file_progress_".$this->getName()."(id, 0, 'warning', false);
    },
    onUploadProgress: function(id, percent){
      // Updating file progress
      ui_multi_update_file_progress_".$this->getName()."(id, percent);
    },
    onUploadSuccess: function(id, data){
      // A file was successfully uploaded
      ui_add_log_".$this->getName()."('Server Response for file #' + id + ': ' + JSON.stringify(data));
      ui_add_log_".$this->getName()."('Upload of file #' + id + ' COMPLETED', 'success');
      ui_multi_update_file_status_".$this->getName()."(id, 'success', '".addslashes(LanguageManager::_("COMP_KUPLOAD_STATUS_5"))."');
      ui_multi_update_file_progress_".$this->getName()."(id, 100, 'success', false);
      almost_one_file_uploaded_".$this->getName()."=true;
    },
    onUploadError: function(id, xhr, status, message){
      ui_multi_update_file_status_".$this->getName()."(id, 'danger', message);
      ui_multi_update_file_progress_".$this->getName()."(id, 0, 'danger', false);  
      this.error_string=JSON.parse(xhr.responseText);
      upload_error_".$this->getName()."=true;
//      console.log('Error=>onUploadError()');
//      console.log(status);
//      console.log(message);
//      console.log(xhr);
      //alert(xhr.responseText);
    },
    onFallbackMode: function(){
      // When the browser doesn't support this plugin :(
      ui_add_log_".$this->getName()."('Plugin cant be used here, running Fallback callback', 'danger');
    },
    onFileSizeError: function(file){
      ui_add_log_".$this->getName()."('Fichier \'' + file.name + '\' Taille maximale dépassée', 'danger');
    },
    onComplete: function()
    {
        if(almost_one_file_uploaded_".$this->getName().")
        {
            if(upload_error_".$this->getName().")
            {
                Swal.fire({
                    title: \"".addslashes(LanguageManager::_("COMP_KUPLOAD_ERROR_1"))."\",
                });    
            }
            else
            {
                $('#upload_part_".$this->getName()."').hide();
                $('#upload_result_".$this->getName()."').load('".$route_loading_1."',
                    function()
                    {
                        if(redirect_to_new_page_".$this->getName().")
                        {
                            window.location.href ='".$url_end_upload->printURLWithoutAmp()."';
                        }
                        else
                        {
                            $('#upload_result_".$this->getName()."').load('".$url_end_upload->printURLWithoutAmp()."');
                        }
                    }
                );
            }
        }
        else if(upload_error_".$this->getName().")
        {     
            //console.log(this.error_string);
            Swal.fire({
                title: '".addslashes(LanguageManager::_("COMP_KUPLOAD_ERROR_2"))."',
                html: this.error_string.message 
            });
        }
        else
        {
            Swal.fire({
                title: '".addslashes(LanguageManager::_("COMP_KUPLOAD_ERROR_2"))."',
            });
            $('#button_start').show();
            $('#button_search_loader').hide();
        }
    },
  });
});
";
        $this->addJSText($js);        
    }
    
    public function getMessage(): string
    {
        return $this->message;
    }
}