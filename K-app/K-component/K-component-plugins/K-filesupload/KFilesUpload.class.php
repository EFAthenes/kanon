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
/**
 * Description of KFilesUpload
 * 
 * Using Filepond
 *
 */
class KFilesUpload extends KComponent
{       
    private string $filepond_component_name="";
    private string $route_url="";
    
    private int $max_files=10;
    private int $max_file_size=40;
    private bool $allow_browse=true;
    private bool $allowMultiple=false;
    private bool $allowFileEncode=false;
    private bool $allowFileSizeValidation=true;
    private bool $allowFileTypeValidation=true;
    private bool $allowImagePreview=false;
    private string $labelIdle='';
    private string $labelFileWaitingForSize='';
    private string $labelFileSizeNotAvailable='';
    private string $labelFileLoading='';
    private string $labelFileLoadError='';
    private string $labelFileProcessing='';
    private string $labelFileProcessingComplet='';
    private string $labelFileProcessingAborted='';
    private string $labelFileProcessingError='';
    private string $labelTapToCancel='';
    private string $labelTapToRetry='';
    private string $labelTapToUndo='';
    private string $labelButtonRemoveItem='';
    private string $labelButtonAbortItemLoad='';
    private string $labelButtonAbortItemProcessing='';
    private string $labelButtonUndoItemProcessing='';
    private string $labelButtonRetryItemProcessing='';
    private string $labelButtonProcessItem=''; 
    private string $labelMaxFileSizeExceeded='';
    private string $labelMaxFileSize='';
    private string $labelTotalFileSizeExceeded='';
    private string $labelMaxTotalFileSize='';    
    private string $labelFileProcessingComplete='';
    private string $labelButtonRetryItemLoad='';
    
    /**
     * 
     * @var array<int,string>
     */
    private array $acceptedFileTypes=[];
    
    /**
     * 
     * @var array<int,string>
     */
    private array $listOfAcceptedFileTypes=[];
    
    public static string $FILE_TYPE_PDF="application/pdf";
    public static string $FILE_TYPE_JPG="image/jpeg";
    public static string $FILE_TYPE_PNG="image/png";
    
    function __construct(string $filepond_component,?string $route_url=null)
    {        
        parent::__construct();
        $this->setNone();
        
        $this->addCssFile("css/filepond.css");
        $this->addCssFile("css/filepond-image-preview.css");
        
        $this->addJSFile("js/filepond-plugin-file-validate-size.js");
        $this->addJSFile("js/filepond-plugin-image-preview.js");
        $this->addJSFile("js/filepond-plugin-file-encode.js");
        $this->addJSFile("js/filepond-plugin-image-exif-orientation.js");
        $this->addJSFile("js/filepond-plugin-file-validate-type.js");      
        $this->addJSFile("js/filepond.min.js");
        
        $this->filepond_component_name=$filepond_component;
        
        if(is_null($route_url))
        {
            $this->route_url=KRoute::makeActionURL(RoutesItems::$UPLOAD_FILES);
        }
        else
        {
            $this->route_url=$route_url;
        }
        $this->setLanguage();
        
        $this->acceptedFileTypes[]=self::$FILE_TYPE_JPG;
        $this->acceptedFileTypes[]=self::$FILE_TYPE_PNG;
        
        $this->listOfAcceptedFileTypes[]=self::$FILE_TYPE_JPG;
        $this->listOfAcceptedFileTypes[]=self::$FILE_TYPE_PNG;
        $this->listOfAcceptedFileTypes[]=self::$FILE_TYPE_PDF;
    }
    
    public function addAcceptedFileType(string $file_type) : bool
    {
        if(in_array($file_type,$this->listOfAcceptedFileTypes))
        {
            $this->acceptedFileTypes[]=$file_type;
            return true;
        }
        return false;
    }
          
    public function clearAcceptedFileType() : void
    {
        $this->acceptedFileTypes=[];
    }
    
    private function setLanguage() : void
    {
        $language=LanguageManager::getInstance()->getLanguage();
        if($language==LanguageManager::$LANGUAGE_FR)
        {
            $this->labelIdle='Glisser-Déposer vos fichiers ou <span class="filepond--label-action"> Parcourir </span>';
            $this->labelFileWaitingForSize='En attente de la vérification de la taille des fichiers';
            $this->labelFileSizeNotAvailable='Taille des fichiers non disponible';
            $this->labelFileLoading='Chargement';
            $this->labelFileLoadError='Une erreur est survenue lors du chargement';
            $this->labelFileProcessing='Chargement';
            $this->labelFileProcessingComplet='Chargement terminé';
            $this->labelFileProcessingAborted='Chargement annulé';
            $this->labelFileProcessingError='Erreur lors du chargement';
            $this->labelTapToCancel='Cliquer pour annuler';
            $this->labelTapToRetry='Cliquer pour relancer';
            $this->labelTapToUndo='Cliquer pour défaire';
            $this->labelButtonRemoveItem='Supprimer';
            $this->labelButtonAbortItemLoad='Annuler';
            $this->labelButtonAbortItemProcessing='Annuler';
            $this->labelButtonUndoItemProcessing='Revenir en arrière';
            $this->labelButtonRetryItemProcessing='Réessayer';
            $this->labelButtonProcessItem='Chargement'; 
            
            $this->labelMaxFileSizeExceeded='La taille du fichier est trop importante';
            $this->labelMaxFileSize='La taille maximum est : {filesize}';
            $this->labelTotalFileSizeExceeded='Taille maximale dépassée';
            $this->labelMaxTotalFileSize='La taille maximale est :{filesize}';
        }
        else
        {
            $this->labelIdle='Drag & Drop your files or <span class="filepond--label-action"> Browse </span>';
            $this->labelFileWaitingForSize='Waiting for size';
            $this->labelFileSizeNotAvailable='Size not available';
            $this->labelFileLoading='Loading';
            $this->labelFileLoadError='Error during load';
            $this->labelFileProcessing='Uploading';
            $this->labelFileProcessingComplet='Upload complete';
            $this->labelFileProcessingAborted='Upload cancelled';
            $this->labelFileProcessingError='Error during upload';
            $this->labelTapToCancel='tap to cancel';
            $this->labelTapToRetry='tap to retry';
            $this->labelTapToUndo='tap to undo';
            $this->labelButtonRemoveItem='Remove';
            $this->labelButtonAbortItemLoad='Abort';
            $this->labelButtonAbortItemProcessing='Cancel';
            $this->labelButtonUndoItemProcessing='Undo';
            $this->labelButtonRetryItemProcessing='Retry';
            $this->labelButtonProcessItem='Upload';    
            
            $this->labelMaxFileSizeExceeded='File is too large';
            $this->labelMaxFileSize='Maximum file size is {filesize}';
            $this->labelTotalFileSizeExceeded='Maximum total size exceeded';
            $this->labelMaxTotalFileSize='Maximum total file size is {filesize}';            
        }
    }
    
    
    
    
    public function draw()  : string
    {      
        $accepted_file_type_string="";
        foreach($this->acceptedFileTypes as $string)
        {
            if($accepted_file_type_string!="")
            {
                $accepted_file_type_string.=",";
            }
            $accepted_file_type_string.='"'.$string.'"';
        }
        
        $html='      
<input type="file" id="'.$this->filepond_component_name.'" name="'.$this->filepond_component_name.'[]"   >

<script>

FilePond.registerPlugin(
  FilePondPluginFileEncode,
  FilePondPluginFileValidateType,
  FilePondPluginFileValidateSize,
  FilePondPluginImageExifOrientation,
  FilePondPluginImagePreview,
);


const inputElement_'.$this->filepond_component_name.' = document.querySelector("#'.$this->filepond_component_name.'");
const pond_'.$this->filepond_component_name.' = FilePond.create( inputElement_'.$this->filepond_component_name.' ,
    {
        maxFiles: 10,
        allowBrowse: '.convertBoolToString($this->allow_browse).',
        allowMultiple : '.convertBoolToString($this->allowMultiple).',
        allowImagePreview : '.convertBoolToString($this->allowImagePreview).',
        allowFileEncode : '.convertBoolToString($this->allowFileEncode).',
        allowFileTypeValidation : '.convertBoolToString($this->allowFileTypeValidation).',
        acceptedFileTypes : ['.$accepted_file_type_string.'],
        allowFileSizeValidation: '.convertBoolToString($this->allowFileSizeValidation).',
        maxTotalFileSize: "'.$this->max_file_size.'MB",
        labelTotalFileSizeExceeded:"'.addslashes($this->labelTotalFileSizeExceeded).'", 
        labelIdle:"'.addslashes($this->labelIdle).'",
        labelFileWaitingForSize:"'.addslashes($this->labelFileWaitingForSize).'",
        labelFileSizeNotAvailable:"'.addslashes($this->labelFileSizeNotAvailable).'",
        labelFileLoading:"'.addslashes($this->labelFileLoading).'",
        labelFileLoadError:"'.addslashes($this->labelFileLoadError).'",           
        labelFileProcessing:"'.addslashes($this->labelFileProcessing).'",
        labelFileProcessingComplete:"'.addslashes($this->labelFileProcessingComplete).'",
        labelFileProcessingAborted:"'.addslashes($this->labelFileProcessingAborted).'",
        labelFileProcessingError:"'.addslashes($this->labelFileProcessingError).'",  
        labelTapToCancel:"'.addslashes($this->labelTapToCancel).'",
        labelTapToRetry:"'.addslashes($this->labelTapToRetry).'",
        labelTapToUndo:"'.addslashes($this->labelTapToUndo).'",
        labelButtonRemoveItem:"'.addslashes($this->labelButtonRemoveItem).'",           
        labelButtonAbortItemLoad:"'.addslashes($this->labelButtonAbortItemLoad).'",
        labelButtonRetryItemLoad:"'.addslashes($this->labelButtonRetryItemLoad).'",
        labelButtonAbortItemProcessing:"'.addslashes($this->labelButtonAbortItemProcessing).'",
        labelButtonUndoItemProcessing:"'.addslashes($this->labelButtonUndoItemProcessing).'",             
        labelButtonRetryItemProcessing:"'.addslashes($this->labelButtonRetryItemProcessing).'",
        labelButtonProcessItem:"'.addslashes($this->labelButtonProcessItem).'",
        labelMaxFileSizeExceeded:"'.addslashes($this->labelMaxFileSizeExceeded).'",
        labelMaxFileSize:"'.addslashes($this->labelMaxFileSize).'",             
        labelTotalFileSizeExceeded:"'.addslashes($this->labelTotalFileSizeExceeded).'",
        labelMaxTotalFileSize:"'.addslashes($this->labelMaxTotalFileSize).'",
        
        server: {
            url: "'.$this->route_url.'",             
        }       
    }
);
</script>       
';        
        // //files: [{source:"./img/sig-delos.jpg"}],
        return  parent::draw().$html;
    }  
    

    function getFilepond_component_name() : string
    {
        return $this->filepond_component_name;
    }

    function getRoute_url() : string
    {
        return $this->route_url;
    }

    function getMax_files() : int
    {
        return $this->max_files;
    }

    function getMax_file_size() : int
    {
        return $this->max_file_size;
    }

    function getAllow_browse() : bool
    {
        return $this->allow_browse;
    }

    function getAllowMultiple(): bool
    {
        return $this->allowMultiple;
    }

    function getAllowFileEncode(): bool
    {
        return $this->allowFileEncode;
    }

    function getAllowFileSizeValidation(): bool
    {
        return $this->allowFileSizeValidation;
    }

    function setFilepond_component_name(string $filepond_component_name) : void
    {
        $this->filepond_component_name=$filepond_component_name;
    }

    function setRoute_url(string $route_url): void
    {
        $this->route_url=$route_url;
    }

    function setMax_files(int $max_files): void
    {
        $this->max_files=$max_files;
    }

    function setMax_file_size(int $max_file_size): void
    {
        $this->max_file_size=$max_file_size;
    }

    function setAllow_browse(bool $allow_browse): void
    {
        $this->allow_browse=$allow_browse;
    }

    function setAllowMultiple(bool $allowMultiple): void
    {
        $this->allowMultiple=$allowMultiple;
    }

    function setAllowFileEncode(bool $allowFileEncode): void
    {
        $this->allowFileEncode=$allowFileEncode;
    }

    function setAllowFileSizeValidation(bool $allowFileSizeValidation): void
    {
        $this->allowFileSizeValidation=$allowFileSizeValidation;
    }
    
    function getAllowImagePreview() : bool
    {
        return $this->allowImagePreview;
    }

    function setAllowImagePreview(bool $allowImagePreview): void
    {
        $this->allowImagePreview=$allowImagePreview;
    }

    function getLabelIdle() : string
    {
        return $this->labelIdle;
    }

    function getLabelFileWaitingForSize() : string
    {
        return $this->labelFileWaitingForSize;
    }

    function getLabelFileSizeNotAvailable() : string
    {
        return $this->labelFileSizeNotAvailable;
    }

    function getLabelFileLoading() : string
    {
        return $this->labelFileLoading;
    }

    function getLabelFileLoadError() : string
    {
        return $this->labelFileLoadError;
    }

    function getLabelFileProcessing() : string
    {
        return $this->labelFileProcessing;
    }

    function getLabelFileProcessingComplet() : string
    {
        return $this->labelFileProcessingComplet;
    }

    function getLabelFileProcessingAborted() : string
    {
        return $this->labelFileProcessingAborted;
    }

    function getLabelFileProcessingError() : string
    {
        return $this->labelFileProcessingError;
    }

    function getLabelTapToCancel() : string
    {
        return $this->labelTapToCancel;
    }

    function getLabelTapToRetry() : string
    {
        return $this->labelTapToRetry;
    }

    function getLabelTapToUndo() : string
    {
        return $this->labelTapToUndo;
    }

    function getLabelButtonRemoveItem() : string
    {
        return $this->labelButtonRemoveItem;
    }

    function getLabelButtonAbortItemLoad() : string
    {
        return $this->labelButtonAbortItemLoad;
    }

    function getLabelButtonAbortItemProcessing() : string
    {
        return $this->labelButtonAbortItemProcessing;
    }

    function getLabelButtonUndoItemProcessing() : string
    {
        return $this->labelButtonUndoItemProcessing;
    }

    function getLabelButtonRetryItemProcessing() : string
    {
        return $this->labelButtonRetryItemProcessing;
    }

    function getLabelButtonProcessItem() : string
    {
        return $this->labelButtonProcessItem;
    }

    function getLabelMaxFileSizeExceeded() : string
    {
        return $this->labelMaxFileSizeExceeded;
    }

    function getLabelMaxFileSize() : string
    {
        return $this->labelMaxFileSize;
    }

    function getLabelTotalFileSizeExceeded() : string
    {
        return $this->labelTotalFileSizeExceeded;
    }

    function getLabelMaxTotalFileSize() : string
    {
        return $this->labelMaxTotalFileSize;
    }

    function setLabelIdle(string $labelIdle) : void
    {
        $this->labelIdle=$labelIdle;
    }

    function setLabelFileWaitingForSize(string $labelFileWaitingForSize) : void
    {
        $this->labelFileWaitingForSize=$labelFileWaitingForSize;
    }

    function setLabelFileSizeNotAvailable(string $labelFileSizeNotAvailable) : void
    {
        $this->labelFileSizeNotAvailable=$labelFileSizeNotAvailable;
    }

    function setLabelFileLoading(string $labelFileLoading) : void
    {
        $this->labelFileLoading=$labelFileLoading;
    }

    function setLabelFileLoadError(string $labelFileLoadError) : void
    {
        $this->labelFileLoadError=$labelFileLoadError;
    }

    function setLabelFileProcessing(string $labelFileProcessing) : void
    {
        $this->labelFileProcessing=$labelFileProcessing;
    }

    function setLabelFileProcessingComplet(string $labelFileProcessingComplet) : void
    {
        $this->labelFileProcessingComplet=$labelFileProcessingComplet;
    }

    function setLabelFileProcessingAborted(string $labelFileProcessingAborted) : void
    {
        $this->labelFileProcessingAborted=$labelFileProcessingAborted;
    }

    function setLabelFileProcessingError(string $labelFileProcessingError) : void
    {
        $this->labelFileProcessingError=$labelFileProcessingError;
    }

    function setLabelTapToCancel(string $labelTapToCancel) : void
    {
        $this->labelTapToCancel=$labelTapToCancel;
    }

    function setLabelTapToRetry(string $labelTapToRetry) : void
    {
        $this->labelTapToRetry=$labelTapToRetry;
    }

    function setLabelTapToUndo(string $labelTapToUndo) : void
    {
        $this->labelTapToUndo=$labelTapToUndo;
    }

    function setLabelButtonRemoveItem(string $labelButtonRemoveItem) : void
    {
        $this->labelButtonRemoveItem=$labelButtonRemoveItem;
    }

    function setLabelButtonAbortItemLoad(string $labelButtonAbortItemLoad) : void
    {
        $this->labelButtonAbortItemLoad=$labelButtonAbortItemLoad;
    }

    function setLabelButtonAbortItemProcessing(string $labelButtonAbortItemProcessing) : void
    {
        $this->labelButtonAbortItemProcessing=$labelButtonAbortItemProcessing;
    }

    function setLabelButtonUndoItemProcessing(string $labelButtonUndoItemProcessing) : void
    {
        $this->labelButtonUndoItemProcessing=$labelButtonUndoItemProcessing;
    }

    function setLabelButtonRetryItemProcessing(string $labelButtonRetryItemProcessing) : void
    {
        $this->labelButtonRetryItemProcessing=$labelButtonRetryItemProcessing;
    }

    function setLabelButtonProcessItem(string $labelButtonProcessItem) : void
    {
        $this->labelButtonProcessItem=$labelButtonProcessItem;
    }

    function setLabelMaxFileSizeExceeded(string $labelMaxFileSizeExceeded) : void
    {
        $this->labelMaxFileSizeExceeded=$labelMaxFileSizeExceeded;
    }

    function setLabelMaxFileSize(string $labelMaxFileSize) : void
    {
        $this->labelMaxFileSize=$labelMaxFileSize;
    }

    function setLabelTotalFileSizeExceeded(string $labelTotalFileSizeExceeded) : void
    {
        $this->labelTotalFileSizeExceeded=$labelTotalFileSizeExceeded;
    }

    function setLabelMaxTotalFileSize(string $labelMaxTotalFileSize) : void
    {
        $this->labelMaxTotalFileSize=$labelMaxTotalFileSize;
    } 
    function getLabelFileProcessingComplete() : string
    {
        return $this->labelFileProcessingComplete;
    }

    function getLabelButtonRetryItemLoad() : string
    {
        return $this->labelButtonRetryItemLoad;
    }

    function setLabelFileProcessingComplete(string $labelFileProcessingComplete) : void
    {
        $this->labelFileProcessingComplete=$labelFileProcessingComplete;
    }

    function setLabelButtonRetryItemLoad(string $labelButtonRetryItemLoad) : void
    {
        $this->labelButtonRetryItemLoad=$labelButtonRetryItemLoad;
    }

    /**
     * 
     * @return array<int,string>
     */
    function getAcceptedFileTypes() : array
    {
        return $this->acceptedFileTypes;
    }

    function getFilepondComponentName(): string
    {
        return $this->filepond_component_name;
    }

    function getAllowBrowse() : bool
    {
        return $this->allow_browse;
    }

}
