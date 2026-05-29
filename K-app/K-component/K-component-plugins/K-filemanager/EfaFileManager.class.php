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
declare(strict_types=1);
/**
 * Description of EfaFileManager
 *
 * @author Mulot Louis
 */
class EfaFileManager extends KComponent
{
    
    private const string PATH="path";
    private const string ACTION="action";
    private const string ITEM="item";
    private const string ITEM2="item2";
    private const string UPLOADED_PATH="uploaded_path";
    
    public const string ACTION_DOWNLOAD="download";
    public const string ACTION_RENAME="rename";
    public const string ACTION_MOVE="move";
    public const string ACTION_DELETE="delete";
    public const string ACTION_CREATE_DIR="create_dir";
    public const string ACTION_UPLOAD="upload_files";
    public const string ACTION_DOWNLOAD_ALL="download_all";
    
    private const string BACK_DIR="??-1??";
    
    private const string CONTAINER_ID="container-ui";
    private const string UPLOADER_ID="uploader-ui";
    private const string TABLE_ID="container-files_list";
    private const string DATATABLE_ID="files_list";
    
    private const string TOOLBAR_CONTAINER="toolbar_container";
    private const string TOOLBAR_1="toolbar_1";
    private const string TOOLBAR_2="toolbar_2";
       
    /**
     * 
     * @var array<int,string>
     */
    private array $arrayDirName=[];
    private bool $isRootDir=true;
    
    private ?string $path=null;
    private ?KFile $dir=null;
    
    private ?string $action_realized=null;
    private ?string $action_realized_description=null;
    
    private bool $debug=false;
    private bool $fakeIt=false;
    
    final function __construct(string $path,bool $fakeIt=false)
    {
        parent::__construct();
        $this->setNone(); 
        $this->path=$path;
        $this->setFakeIt($fakeIt);
        
        if(!$this->managePath())
        {
            return;
        }

        if(!$this->manageAction())
        {
            return;
        }
        
        $this->makeUI(); 
    }
    
    public function setFakeIt(bool $fakeIt): void
    {
        $this->fakeIt = $fakeIt;
    }

        
    private function makeUI() : void
    {
        $this->addComponent($this->makeToolBar());
        $this->addComponent($this->makeFileViewer());
              
        $layout=KApp::getInstance()->getLayout();      
        $layout->addJsFileToBuffer(__DIR__."/js/iframeResizerContentWindow.js");   
        $layout->addCSSFileToBuffer(__DIR__."/js/dropzone.css");       
        $layout->addJsFileToBuffer(__DIR__."/js/dropzone.js"); 
        $this->addJSText($this->makeJS());
        
        if($this->debug)
        {
            $this->addComponent(new PostGetComponent());
        }        
    }
    
    private function makeFileViewer() : KComponent
    {
        $array=$this->makeArrayOfFiles($this->dir);       
        $columns=["Ordre","Nom","Type","Taille","date modification","Actions"]; 
        $datatable=new DataTableSimpleTableComponent(self::DATATABLE_ID,$columns, $array);

        $divId=new DivIdComponent(self::CONTAINER_ID);
        $tableDivId=new DivIdComponent(self::TABLE_ID);
        $tableDivId->addComponent($datatable);
        $tableDivId->addComponent($this->makeDirStatsComponent($this->dir));
        $divId->addComponent($tableDivId);
        $divId->addComponent($this->makeDropFilesZone());
        
        return $divId;
    }    
    
    private function makeToolBar() : KComponent
    {
        $toolBar= new RowColsComponent();
        $toolBar->addColComponent($this->makeBreadCrumbs());
        $toolBar->addColComponent($this->addToolBar());
        return $toolBar;
    }
    
    private function managePath() : bool
    {
        $root_dir= new KFile($this->path);
        
        if(!$root_dir->exists() || $root_dir->isFile())
        {
            // doesn't exists
            return false;
        }
        
        $followPath="";
        $this->dir=$root_dir;
        if(KInput::checkInputGet(self::PATH,KInput::$VARIABLE_STRING,$followPath))
        {
            $testDir=new KFile($root_dir->getPath().KFile::separator().$followPath);
            if($testDir->exists() && $testDir->isDirectory())
            {
                $this->isRootDir=false;
                $this->dir=$testDir;
            }
        }   
        return true;
    }
    
    private function makeBreadCrumbs() : KComponent
    {
        $url=new KURL();
        $url->removeArg(self::ACTION);
        $url->removeArg(self::ITEM);
        $url->removeArg(self::ITEM2);        
        $url->removeArg(self::PATH);
        $bread=new BreadCrumbsComponent("file_manager_breadcrumb");
        $bread->addItemComponentString(new HTMLComponent('<i class="fas fa-home"></i>'),$url->printURLWithoutAmp());
        $get_path=str_replace($this->path,"",$this->dir->getPath());
        $crumbs=explode(KFile::separator(), $get_path);
        foreach ($crumbs as $crumb)
        {
            if($crumb!="")
            {
                $tempPath=$url->getArgValue(self::PATH);
                $url->addOrReplace(self::PATH,$tempPath.KFile::separator().$crumb);
                $bread->addItemComponentString(new HTMLComponent($crumb),$url->printURLWithoutAmp());
            }
        } 
        return $bread;
    }
    
    /**
     * 
     * @param KFile $dir
     * @return array<int,array<int,mixed>>
     */
    private function makeArrayOfFiles(KFile $dir) : array
    {
        $url=new KURL();
        $url->removeArg(self::ACTION);
        $url->removeArg(self::ITEM);
        $url->removeArg(self::ITEM2);
        
        $arrayKFiles=$dir->listFilesToArray();
   
        $mapSize=new HashMap();
        $arrayDirs=[];
        $arrayFiles=[];
        /* @var $file KFile */
        if(!is_null($arrayKFiles))
        {
            foreach ($arrayKFiles as $file)
            {
                $line=[];
                if($file->isDirectory())
                {
                    $get_path=str_replace($this->path,"",$file->getPath());
                    $url->addOrReplace(self::PATH,$get_path);
                    $line[]=new HTMLComponent('<i class="fa fa-folder"></i> <a href="'.$url->printURLWithoutAmp().'">'.$file->getName()."</a>");                
                    $line[]="Dossier";
                    $length=$file->getDirectoryLength();
                    //$line[]=new HTMLComponent('<span style="display:none">'.$length.'</span>'.$file->sizeFormat($length));
                    $line[]=$length;
                    $mapSize->put($length, $length);
                    $line[]=$file->getLastModifiedNoSpace();
                    $line[]=$this->makeColumnAction($file->getName(),true);

                    $arrayDirs[]=$line;
                    $this->arrayDirName[]=$file->getName();
                }
                elseif($file->isFile())
                {
                    $line[]=new HTMLComponent('<i class="fa fa-file"></i> '.$file->getName());
                    $line[]="".$file->getExtension();
                    $length=$file->getFileSize();
                    //$line[]=new HTMLComponent('<span style="display:none">'.$length.'</span>'.$file->sizeFormat($length));
                    $line[]=$length;
                    $mapSize->put($length, $length);
                    $line[]=$file->getLastModifiedNoSpace();
                    $line[]=$this->makeColumnAction($file->getName(),false);

                    $arrayFiles[]=$line;
                }
            }
        }
        
        $listSize=$mapSize->toArrayList();
        
        
        $array=[];
        $count=0;
        foreach($arrayDirs as $item)
        {
            $count++;
            $rankSize=$this->findRankSize($listSize,$item[2]);
            $item[2]=new HTMLComponent('<span style="display:none">'.$rankSize.'</span>'.$dir->sizeFormat($item[2]));
            array_unshift($item ,$count);
            $array[]=$item;
        }        
        foreach($arrayFiles as $item)
        {
            $count++;
            $rankSize=$this->findRankSize($listSize,$item[2]);
            $item[2]=new HTMLComponent('<span style="display:none">'.$rankSize.'</span>'.$dir->sizeFormat($item[2]));            
            array_unshift($item ,$count);
            $array[]=$item;
        }
             
        return $array;
    }
    
    private function manageAction() : bool
    {
        // MANAGE ACTION
        $action="";
        if(KInput::checkInputGet(self::ACTION,KInput::$VARIABLE_STRING,$action))
        {
            if($action==self::ACTION_UPLOAD)
            {
                $response=[];
                $tempPath='';
                KApp::getInstance()->setLayout(new JsonLayout());
                if(isset($_FILES['file']['name']) && isset($_FILES['file']['tmp_name'])
                        && KInput::checkInputPost(self::UPLOADED_PATH,KInput::$VARIABLE_STRING,$tempPath))
                {
                    $uploadedFile=new KFile($_FILES['file']['tmp_name']);
                    if($uploadedFile->exists())
                    {
                        $destination=new KFile($this->dir->getPath().KFile::separator().$tempPath);
                        if(!$destination->exists())
                        {
                            $uploadedFile->copyToKFile($destination);
                            /** @phpstan-ignore-next-line */
                            if($destination->exists()&&$destination->isFile())
                            {
                                $response['status']="success";
                                $response['info']="ok";
                                // fichier ajouté
                                $this->action_realized=$action;
                                $this->action_realized_description=$destination->getName();
                            }
                            else
                            {
                                $response['status']="error";
                                $response['info']="copy failed";                                
                            }
                        }
                        else
                        {
                            $response['status']="error";
                            $response['info']="destination file already exists";                                
                        }
                    }
                    else
                    {
                        $response['status']="error";
                        $response['info']="file upload not present => ".$uploadedFile->getPath();                          
                    }
                }
                
                if(count($response)==0)
                {
                    $response['status']="error";
                    $response['info']="unknown problem";
                }
                
                $this->addComponent(new HTMLComponent(json_encode($response)));
                return false;
            }
            else if($action==self::ACTION_DOWNLOAD || $action==self::ACTION_DOWNLOAD_ALL)
            {
                $item="";
                if(KInput::checkInputGet(self::ITEM,KInput::$VARIABLE_STRING,$item) || $action==self::ACTION_DOWNLOAD_ALL)
                {
                    return false;
                    /*
                    $dl_file=null;
                    if($action==self::ACTION_DOWNLOAD_ALL)
                    {
                        $dl_file=new KFile($this->dir->getPath());           
                    }
                    else
                    {
                        $dl_file=new KFile($this->dir->getPath().KFile::separator().$item);                        
                    }
                    if($dl_file->exists()&&$dl_file->isFile())
                    {
                        session_write_close();
                        KApp::getInstance()->setLayout(new TextPlainLayout());
                        header("Content-disposition: attachment; filename=\"" . $dl_file->getName() . "\"");
                        header("Content-Type: application/force-download");
                        header("Content-Transfer-Encoding: application/" . $dl_file->getExtension() . "\n");
                        header("Content-Length: " . $dl_file->getSize());
                        header("Pragma: no-cache");
                        header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
                        header("Expires: 0");
                        readfile($dl_file->getPath());                        
                        return false;
                    }
                    else if ($dl_file->exists()&&$dl_file->isDirectory())
                    {
                        session_write_close();
                        $rand=KRandom::makeRandom();
                        $zipName="/tmp/"."archimage_".$rand.".zip";
                        if($dl_file->toZipCLI($zipName))
                        {
                            $zipFile=new KFile($zipName);
                            header("Content-disposition: attachment; filename=\"" . $zipFile->getName(). "\"");
                            header("Content-Type: application/force-download");
                            header("Content-Transfer-Encoding: application/zip\n");
                            header("Content-Length: " . $zipFile->getSize());
                            header("Pragma: no-cache");
                            header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
                            header("Expires: 0");
                            readfile($zipFile->getPath());                           
                            $zipFile->delete();
                            return false;
                        }
                        else if($dl_file->toZip($zipName))
                        {
                            $zipFile=new KFile($zipName);
                            header("Content-disposition: attachment; filename=\"" . $zipFile->getName(). "\"");
                            header("Content-Type: application/force-download");
                            header("Content-Transfer-Encoding: application/zip\n");
                            header("Content-Length: " . $zipFile->getSize());
                            header("Pragma: no-cache");
                            header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
                            header("Expires: 0");
                            readfile($zipFile->getPath());                           
                            $zipFile->delete();
                            return false;                            
                        }
                    }
                     * 
                     */
                }
            }
            else if($action==self::ACTION_DELETE)
            {
                $item="";
                if(KInput::checkInputGet(self::ITEM,KInput::$VARIABLE_STRING,$item))
                {
                    $delete_file=new KFile($this->dir->getPath().KFile::separator().$item);
                    if($delete_file->exists())
                    {
                        $type_name= $delete_file->isFile() ? "fichier" : "dossier";
                        if($delete_file->delete())
                        {
                            // Fichier // dossier supprimé
                            $this->action_realized=$action;
                            $this->action_realized_description=$delete_file->getName();
                            $alert= new KNotify("Opération réussie!","Le ".$type_name." suivant a été supprimé => ".$delete_file->getName(),KNotify::$TYPE_SUCCESS);
                            $this->addComponent($alert);
                        }
                        else
                        {
                            $alert= new KNotify("Echec de l'opération!","Le ".$type_name." suivant n'a pas été supprimé => ".$delete_file->getName(),KNotify::$TYPE_DANGER);
                            $this->addComponent($alert);
                        }
                    }
                }
            }
            else if($action==self::ACTION_RENAME)
            {
                $item="";
                $item2="";
                if(
                    KInput::checkInputGet(self::ITEM,KInput::$VARIABLE_STRING,$item)
                    &&
                    KInput::checkInputGet(self::ITEM2,KInput::$VARIABLE_STRING,$item2)
                    )
                {                    
                     // Check If name is ok
                    if(isNameAuthorizedForFolderAndFile($item2)) 
                    {
                        $rename_file=new KFile($this->dir->getPath().KFile::separator().$item);
                        $old_name=$rename_file->getName();
                        $type_name=$rename_file->isFile() ? "fichier" : "dossier";
                        if($rename_file->renameTo($this->dir->getPath().KFile::separator().$item2))
                        {
                            //Fichier renommé
                            $this->action_realized=$action;
                            $this->action_realized_description=$old_name." => ".$item2;
                            $alert= new KNotify("Opération réussie!","Le ".$type_name." suivant a été renommé => ".$old_name." en ".$item2,KNotify::$TYPE_SUCCESS);
                            $this->addComponent($alert);                        
                        }
                        else
                        {
                            $alert= new KNotify("Echec de l'opération!","Le ".$type_name." suivant n'a pas été renommé => ".$old_name." en ".$item2,KNotify::$TYPE_DANGER);
                            $this->addComponent($alert);                        
                        }                      
                    }
                    else 
                    {
                        $alert= new KNotify("Echec de l'opération!","Le nom du fichier contient des caractères interdits",KNotify::$TYPE_DANGER);
                        $this->addComponent($alert);                          
                    }
                }
            }
            else if($action==self::ACTION_CREATE_DIR)
            {
                $item="";
                $item2="";
                if(KInput::checkInputGet(self::ITEM,KInput::$VARIABLE_STRING,$item))
                {                  
                     // Check If name is ok
                    if(isNameAuthorizedForFolderAndFile($item)) 
                    {
                        $new_folder=new KFile($this->dir->getPath().KFile::separator().$item);
                        if(!$new_folder->exists()&&$new_folder->mkdir())             
                        {
                            // Dossier créé
                            $this->action_realized=$action;
                            $this->action_realized_description=$item;
                            $alert= new KNotify("Opération réussie!","Le dossier a été créé.",KNotify::$TYPE_SUCCESS);
                            $this->addComponent($alert);
                        }
                        else
                        {
                            $alert= new KNotify("Echec de l'opération!","Le dossier n'a pas pu être créé.",KNotify::$TYPE_DANGER);
                            $this->addComponent($alert);   
                        }
                    }
                    else 
                    {
                        $alert= new KNotify("Echec de l'opération!","Le nom du fichier contient des caractères interdits",KNotify::$TYPE_DANGER);
                        $this->addComponent($alert);                          
                    }
                }
            }
            else if($action==self::ACTION_MOVE)
            {
                $item="";
                $item2="";
                if(
                    KInput::checkInputGet(self::ITEM,KInput::$VARIABLE_STRING,$item)
                    &&
                    KInput::checkInputGet(self::ITEM2,KInput::$VARIABLE_STRING,$item2)
                    )
                { 
                    
                    $move_file=new KFile($this->dir->getPath().KFile::separator().$item);
                    if($move_file->exists())
                    {
                        $type_name=$move_file->isFile() ? "fichier" : "dossier";
                        if($item2==self::BACK_DIR)
                        {
                            if(!$this->isRootDir)
                            {
                                $parent=$this->dir->getParentKFile();
                                if($move_file->renameTo($parent->getPath().KFile::separator().$item))
                                {
                                    // Fichier dossier déplacé
                                    $this->action_realized=$action;
                                    $this->action_realized_description=$item;
                                    $alert= new KNotify("Opération réussie!","Le ".$type_name." ".$item." a été déplacé.",KNotify::$TYPE_SUCCESS);
                                    $this->addComponent($alert);                                  
                                }
                                else
                                {
                                    $alert= new KNotify("Echec de l'opération!","Le ".$type_name." ".$item." n'a pas été déplacé.",KNotify::$TYPE_DANGER);
                                    $this->addComponent($alert);    
                                }
                            }
                        }
                        else
                        {
                            $destination_move=new KFile($this->dir->getPath().KFile::separator().$item2);
                            if($destination_move->exists()&&$destination_move->isDirectory())
                            {
                                if($move_file->renameTo($destination_move->getPath().KFile::separator().$item))
                                {
                                    // Fichier dossier déplacé
                                    $this->action_realized=$action;
                                    $this->action_realized_description=$item;
                                    $alert= new KNotify("Opération réussie!","Le ".$type_name." ".$item." a été déplacé.",KNotify::$TYPE_SUCCESS);
                                    $this->addComponent($alert);                                  
                                }
                                else
                                {
                                    $alert= new KNotify("Echec de l'opération!","Le ".$type_name." ".$item." n'a pas été déplacé.",KNotify::$TYPE_DANGER);
                                    $this->addComponent($alert);    
                                }
                            }
                        }
                    }
                }   
            }
        }
        return true;
    }
    
    
    private function makeDirStatsComponent(KFile $dir) : KComponent
    {
        $stat=$dir->getDirectoryStat();
        $comp = new DivClassComponent("dir_stats");
        $comp->addHTML('Taille : <span class="badge badge-pill badge-primary">'.$dir->sizeFormat($stat[0]).'</span>');
        $comp->addHTML(' | Nombre de dossier(s) : <span class="badge badge-pill badge-secondary">'.$stat[1].'</span>');
        $comp->addHTML(' | Nombre de fichier(s) : <span class="badge badge-pill badge-success">'.$stat[2].'</span>');
        return  $comp;
    }
    
    
    private function makeDropFilesZone() : KComponent
    {
        $url=new KURL();
        $url->removeArg(self::ITEM);
        $url->removeArg(self::ITEM2);
        
        $url->addOrReplace(self::ACTION, self::ACTION_UPLOAD);
        $html='
<div id="'.self::UPLOADER_ID.'" style="display:none">
    <form action="'.$url->printURLWithoutAmp().'" class="dropzone" id="kfileUploader">
        <input type="hidden" name="'.self::UPLOADED_PATH.'" id="'.self::UPLOADED_PATH.'" value="">
        <div class="dz-message" data-dz-message><span>Glisser // Déposer vos fichiers ou votre arborescence de dossiers</span></div>
        <div class="fallback">
            <input name="file" enctype="multipart/form-data" />
        </div>
    </form>
    <hr />
    <div id="kfileUploader-result" class="kfileUploader-result">
    </div>
</div>
';
              
        $drop=new HTMLComponent($html);
        
        $max_execution_time=0;
        //$max_execution_time=ini_get('max_execution_time');       
        $upload_max_file_size=0;
        //$upload_max_file_size=ini_get("upload_max_filesize");

        $js='
let previewTemplate =`
<div class="dz-preview">
    
    <div class="row">
        <div class="col-5">
            Nom : <span data-dz-name></span>
        </div>
        <div class="col-2">
            Taille : <span data-dz-size></span>
        </div>
        <div class="col-2">
            <div class="dz-progress" style="width: 100%;">en attente du chargement <span class="dz-upload" data-dz-uploadprogress></span></div>
            <div class="dz-success-final-ko">
                ✘
            </div>        
            <div class="dz-success-final-ok">
                ✔
            </div>             
        </div>  
        <div class="col-3">
            <div class="dz-error-message"><span data-dz-errormessage></span></div>
            <div class="dz-error-message-specific"></div>
            <div class="dz-ok-message-specific"></div>
        </div>  
    </div>  
    <hr />
</div> 
`;

let countValidFiles=0;
let countErrors=0;

Dropzone.options.kfileUploader = {
    timeout: '.$max_execution_time.',
    maxFilesize:'.$upload_max_file_size.',
    previewsContainer: "#kfileUploader-result",
    previewTemplate: previewTemplate,
    init: function () 
    {
        this.on("sending", function (file, xhr, formData) {
            let _path = (file.fullPath) ? file.fullPath : file.name;
            document.getElementById("'.self::UPLOADED_PATH.'").value = _path;
            xhr.ontimeout = (function() {
                alert("Error: Server Timeout");
            });
        }).on("success", function (res) 
        {   
            console.log(res.xhr.response);
            let _response = JSON.parse(res.xhr.response);
            console.log(res);
            console.log(_response.status);
            if(_response.status == "error")
            {
                $(res.previewElement).find(".dz-error-message-specific").html(_response.info);
                $(res.previewElement).find(".dz-success-final-ko").show();
                countErrors++;
            }
            else
            {
                countValidFiles++;
                $(res.previewElement).find(".dz-ok-message-specific").html("Chargement terminé");
                $(res.previewElement).find(".dz-success-final-ok").show();
            }
        }).on("error", function(file, response) 
        {
            countErrors++;
            //alert(response);
        });
    }
};
';
        $drop->addJSText($js);

        return $drop;
    }
    
    private function makeJS() : string
    {
        if($this->fakeIt)
        {
            return "";
        }
        $url=new KURL();
        $url->addOrReplace(self::ACTION, self::ACTION_DELETE);
        $url->removeArg(self::ITEM);
        $js = '         
function EfaFileManagerDelete(name,folder)
{
    let message = document.createElement("div");
    if(folder)
    {
        message.innerHTML = "Voulez vous supprimer le dossier : <br /><br /> "+name+" <br /><br /> Continuer  ?";
    }
    else
    {
        message.innerHTML = "Voulez vous supprimer le fichier : <br /><br /> "+name+" <br /><br /> Continuer  ?";
    }
    

    swal(
    {
        content: message,
        buttons: ["Annuler", "Confirmer"],
    }).then((result) =>
    {
        if (result) 
        {
            window.location.href = "'.$url->printURLWithoutAmp()."&".self::ITEM.'="+encodeURIComponent(name);
        }
    });
}   
';
        
        $url=new KURL();
        $url->addOrReplace(self::ACTION, self::ACTION_RENAME);
        $url->removeArg(self::ITEM);
        $url->removeArg(self::ITEM2);
        $js.='
function EfaFileManagerRename(name,folder)
{
    let div = document.createElement("div");
    let message = document.createElement("div");
    let input = document.createElement("input");
    input.setAttribute("type", "text");
    input.setAttribute("value", name);

    if(folder)
    {
        message.innerHTML = "Comment voulez vous renommer le dossier : <br /><br /> "+name+" <br /><br /> Saisir le nouveau nom :";
    }
    else
    {
        message.innerHTML = "Comment voulez vous renommer le fichier : <br /><br /> "+name+" <br /><br /> Saisir le nouveau nom :";
    }
    
    div.appendChild(message); 
    div.appendChild(input); 
    swal(
    {
        content: div,
        buttons: ["Annuler", "Confirmer"],
    }).then((result) =>
    {
        if (result) 
        {
            window.location.href = "'.$url->printURLWithoutAmp()."&".self::ITEM.'="+encodeURIComponent(name)+"&'.self::ITEM2.'="+encodeURIComponent(input.value);
        }
    });
}   
';
        
        $url=new KURL();
        $url->addOrReplace(self::ACTION, self::ACTION_CREATE_DIR);
        $url->removeArg(self::ITEM2);
        $js.='
function EfaFileManagerCreateDir()
{
    let div = document.createElement("div");
    let message = document.createElement("div");
    let input = document.createElement("input");
    input.setAttribute("type", "text");

    message.innerHTML = "Un nouveau dossier va être créé <br /> Veuillez saisir son nom :";

    div.appendChild(message); 
    div.appendChild(input); 
    swal(
    {
        content: div,
        buttons: ["Annuler", "Confirmer"],
    }).then((result) =>
    {
        if (result) 
        {
            window.location.href = "'.$url->printURLWithoutAmp().'&'.self::ITEM.'="+encodeURIComponent(input.value);
        }
    });
}   
';    
        
        
        $url=new KURL();
        $url->addOrReplace(self::ACTION, self::ACTION_MOVE);
        $url->removeArg(self::ITEM2);
        
        $arrayDirName="";
        foreach ($this->arrayDirName as $dir)
        {
            if($arrayDirName!="")
            {
                $arrayDirName.=",";
            }
            $arrayDirName.='"'.$dir.'"';
        }
        
        $js.='    
function EfaFileManagerMove(name,folder)
{
    let div = document.createElement("div");
    let message = document.createElement("div");
    let input = document.createElement("select");
    
    let arrayDir =['.$arrayDirName.'];
';
    if(!$this->isRootDir)
    {
        $js.='
    let option = document.createElement("option");
    option.value = "'.self::BACK_DIR.'";
    option.text = "<-- précédent";
    input.appendChild(option); 
';
    }
        $js.=' 
    for (let i = 0; i < arrayDir.length; i++) 
    {
        if(name!=arrayDir[i])
        {
            let option = document.createElement("option");
            option.value = arrayDir[i];
            option.text = arrayDir[i];
            input.appendChild(option);
        }
    }

    if(folder)
    {
        message.innerHTML = "Ou voulez vous déplacer le dossier : <br /> "+name+" <br /> vers le dossier : <br />";
    }
    else
    {
        message.innerHTML = "Ou voulez vous déplacer le fichier : <br /><br /> "+name+" <br /><br /> Saisir le nouveau nom :";
    }
    
    div.appendChild(message); 
    div.appendChild(input); 
    swal(
    {
        content: div,
        buttons: ["Annuler", "Confirmer"],
    }).then((result) =>
    {
        if (result) 
        {
            //alert(input.value);
            window.location.href = "'.$url->printURLWithoutAmp().'&'.self::ITEM.'="+encodeURIComponent(name)+"&'.self::ITEM2.'="+encodeURIComponent(input.value);
        }
    });
}   

';         
      
        $url=new KURL();
        $url->addOrReplace(self::ACTION, self::ACTION_DOWNLOAD_ALL);
        $url->removeArg(self::ITEM2);     
        $js.='
           
function EfaFileDownloadAll()
{
    let message = document.createElement("div");
    message.innerHTML = "Voulez vous télécharger tout le dossier sous format zip ?<br /> (cette action peut prendre un certain temps)";

    swal(
    {
        content: message,
        buttons: ["Annuler", {text: "Confirmer",closeModal: false}],
    }).then((result) => {
        if(result)
        {
            let url ="'.$url->printURLWithoutAmp().'";
            return fetch(url);
        }
        else
        {
            throw null;
        }
    }).then(function(response) 
    {
        return response.blob();
    }).then((bytes) => {
        let elm = document.createElement("a");
        elm.href = URL.createObjectURL(bytes);
        elm.setAttribute("download","download_folder.zip");
        elm.click();
        swal.stopLoading();
        swal.close();
    }).catch((error) => {
        console.log(error);
    });   
}
';
        
        
        
        $js.='
let uploaded_file=false;
function EfaFileShowHideUploadUi()
{          
    if($("#'.self::UPLOADER_ID.'").is(":visible"))
    {
        if(countValidFiles)
        {
            location.reload(); 
        }
        else
        {
            $("#'.self::UPLOADER_ID.'").hide();
            $("#'.self::TABLE_ID.'").show();   
            $("#'.self::TOOLBAR_1.'").show();   
            $("#'.self::TOOLBAR_2.'").hide();   
        }
    }
    else
    {
        $("#'.self::UPLOADER_ID.'").show();
        $("#'.self::TABLE_ID.'").hide(); 
            
       $("#'.self::TOOLBAR_1.'").hide();   
       $("#'.self::TOOLBAR_2.'").show();  
    }
    
}
 ';
        
         return $js;
    }
    
    private function findRankSize(ArrayList $listSize,mixed $size_file) : string
    {
        $count=0;
        foreach ($listSize as $size)
        {
            $count++;
            if($size_file==$size)
            { //$bin = sprintf( "%08d", decbin( 26 ));
                return sprintf( "%08d", decbin($count));
            }
        }
        return '0';
    }
    
    private function makeColumnAction(string $filename,bool $isDir) : KComponent
    {
        $url=new KURL();
        //$rowCol=new RowColsComponent();
        $rowCol=new DivClassComponent("btn-group");
        //Download
        if($isDir&&KFile::isZipExtensionInstalled() || !$isDir)
        {
            $url->addOrReplace(self::ACTION, self::ACTION_DOWNLOAD);
            $url->addOrReplace(self::ITEM, $filename);
            $link=$url->printURLWithoutAmp();
            if($this->fakeIt)
            {
                $rowCol->addComponent(new HTMLComponent('<a href="javascript:void();"><i class="fas fa-file-download"></i></a>'));
            }
            else
            {
                $rowCol->addComponent(new HTMLComponent('<a href="'.$link.'"><i class="fas fa-file-download"></i></a>'));
            }
            $rowCol->addComponent(new NbspComponent(4));
        }
        else
        {
            $rowCol->addComponent(new HTMLComponent('<span><i class="fas fa-file-download"></i></span>'));
            $rowCol->addComponent(new NbspComponent(4));            
        }
        // Rename
        $url->addOrReplace(self::ACTION, self::ACTION_RENAME);
        $rowCol->addComponent(new HTMLComponent('<a onclick="EfaFileManagerRename(\''.htmlspecialchars($filename).'\',\''. convertBoolToStringNumber($isDir).'\')" href="javascript:void();"><i class="fas fa-pen"></i></a>'));
        $rowCol->addComponent(new NbspComponent(4));
        // Move
        $url->addOrReplace(self::ACTION, self::ACTION_MOVE);
        $rowCol->addComponent(new HTMLComponent('<a onclick="EfaFileManagerMove(\''.htmlspecialchars($filename).'\',\''.convertBoolToStringNumber($isDir).'\')" href="javascript:void();"><i class="fas fa-arrows-alt"></i></a>'));
        $rowCol->addComponent(new NbspComponent(4));
        
        // Delete
        $url->addOrReplace(self::ACTION, self::ACTION_DELETE);
        $rowCol->addComponent(new HTMLComponent('<a onclick="EfaFileManagerDelete(\''.htmlspecialchars($filename).'\',\''.convertBoolToStringNumber($isDir).'\')" href="javascript:void();"><i  class="far fa-trash-alt"></i></a>'));
        
        return $rowCol;
    }
    
    private function addToolBar() : KComponent
    {
        $divClass=new DivClassComponent(self::TOOLBAR_CONTAINER);
        
        $divId1=new DivIdComponent(self::TOOLBAR_1);
        $rowCol=new DivClassComponent("btn-group");   
        $create_folder=new ButtonComponent("Créer un dossier",ButtonComponent::$TYPE_PRIMARY,"fas fa-folder-plus");
        $create_folder->setClickAction("EfaFileManagerCreateDir()");
        $rowCol->addComponent($create_folder);
        
        $upload_ui=new ButtonComponent("Charger des fichiers",ButtonComponent::$TYPE_SUCCESS,"fas fa-upload");
        $upload_ui->setClickAction("EfaFileShowHideUploadUi()");
        $rowCol->addComponent($upload_ui);
        
        $url=new KURL();
        $url->addOrReplace(self::ACTION, self::ACTION_DOWNLOAD_ALL);
        $url->removeArg(self::ITEM2);
        $download_all=new ButtonComponent("Télécharger le dossier",ButtonComponent::$TYPE_SECONDARY,"fas fa-file-download");
        $download_all->setClickAction("EfaFileDownloadAll()");
        $download_all->setDisable(!KFile::isZipExtensionInstalled());
        $rowCol->addComponent($download_all);      
        $divId1->addComponent($rowCol);
        
        
        $divId2=new DivIdComponent(self::TOOLBAR_2);
        $divId2->setStyleCode("display:none");
        $rowCol2=new DivClassComponent("btn-group");   //<i class="fas fa-list"></i>
        $show_tree=new ButtonComponent("Afficher l'arborescence",ButtonComponent::$TYPE_WARNING,"fas fa-list");
        $show_tree->setClickAction("EfaFileShowHideUploadUi()");
        //$show_tree->setClickAction("EfaFileManagerCreateDir()");
        $rowCol2->addComponent($show_tree);
        $divId2->addComponent($rowCol2);
        
        $divClass->addComponent($divId1);
        $divClass->addComponent($divId2);
        
        return $divClass;
    }
    
    public function isActionRealizedAndCompleted() : bool
    {
        return !is_null($this->action_realized);
    }
    
    /**
     * 
     * @return array<int,string>|null
     */
    public function getActionRealizedDescription() : ?array
    {
        $action_description=null;
        if(!is_null($this->action_realized))
        {
            $action_description=[$this->action_realized,$this->action_realized_description];
        }
        return $action_description;
    }
    
    #[\Override]
    public static function testMe() : ?static
    { 
        $class=new static(dirname(__FILE__),true);
        return $class;
    }    
}
