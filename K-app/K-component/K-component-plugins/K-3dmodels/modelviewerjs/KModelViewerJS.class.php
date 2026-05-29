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
 * Description of KModelViewerJS
 *
 * @author Mulot Louis
 */
class KModelViewerJS extends KComponent
{
    private int $type=0;
    public const GLTF=1;
    public const GLB=2;
    private string $url="";
    private bool $addStyle=true;
            
    final function __construct(string $name,string $url,int $type)
    {
        parent::__construct();       
        $this->setNone();
        $this->setName($name);
        $this->url=$url;
        $this->type=$type;

        $layout=KApp::getInstance()->getLayout();       
        $layout->addJsFileModuleToBuffer(__DIR__."/js/model-viewer-wo-three.3.4.0.js");
        $layout->addJsFileModuleToBuffer(__DIR__."/js/model-viewer-effect.3.4.0.js");
        
        $js='
  "imports": {
    "three": "https://cdn.jsdelivr.net/npm/three@^0.160.0/build/three.module.min.js"
  }        
';
        
        $layout->addScriptImportMap($js);
        
        /*
         * .demo model-viewer {
  width: 100%;
  height: 100%;
  background-color: #eee;
}
         */
    }
    
    public function getType(): int
    {
        return $this->type;
    }

        
    private function addStyle() : string
    {
        if(!$this->addStyle)
        {
            return "";
        }
/*
 *     #'. $this->getName().' {
width: 800px;
height: 800px;
    }
model-viewer
{
    width:1200px;
    height: 800px;
}
    
 */        
        
        $style = '
<style>

 model-viewer
{
    width:100%;
    height: 800px;
}   

    
  .app {
    display: flex;
    flex-direction: column;
    height: 100vh;
  }

  .header {
    align-items: center;
    display: flex;
    flex: 0 0 auto;
    height: 48px;
    justify-content: space-between;
    padding: 0 12px;
  }

  /* Container for renderer (left) and tabs (right). */
  .editor-body-root {
    display: flex;
    flex: auto; /* Grows to fill space under header */
    height: 0;
  }

  .mvContainer {
    align-items: stretch;
    display: flex;
    flex-direction: column;
    height: 100%;
    position: relative;
    width: 100%;
  }

  model-viewer-preview {
    height: 100%;
  }

  me-tabs {
    background-color: #202124;  /* GOOGLE_GREY_900 */
    padding: 0;
    width: 360px;
    min-width: 360px;
    max-width: 360px;
  }

  me-tabs a {
    color: #dadcff; /* GOOGLE_GREY_300, but.. bluer */
  }

  me-tabbed-panel {
    max-height: calc(100vh - 105px);
    overflow-y: auto;
    display: block;
  }
  .privacy {
    color:#BDBDBD;
    padding: 5px 16px;
    position: absolute;
    bottom: 0;
    font-size: small;
    z-index: 0;
    background-color: rgba(32, 33, 36, .75);
  }
  @media only screen and (max-height: 600px) {
    .privacy {
      z-index: -1;
    }
  }
  .privacy-link {
    color: #BDBDBD;
  }


</style>
        ';
        
        return $style;
    }    
    
    function draw(): string
    {
        //$this->url='http://192.168.11.98/louis/test/3dViewer/Astronaut.glb';
        
/*
<script async src="https://ga.jspm.io/npm:es-module-shims@1.7.1/dist/es-module-shims.js"></script>
<script type="importmap">
{
  "imports": {
    "three": "https://cdn.jsdelivr.net/npm/three@^0.160.0/build/three.module.min.js"
  }
}
</script>    

<!-- Import the <model-viewer> component without three bundled -->
<script type="module" src="https://modelviewer.dev/node_modules/@google/model-viewer/dist/model-viewer-module.min.js"></script>

<!-- Import the <model-viewer-effects> addon -->
<script type="module" src="https://modelviewer.dev/node_modules/@google/model-viewer-effects/dist/model-viewer-effects.min.js"></script>
 
 */        
        
        $html='

<style>
model-viewer 
{
    background-color: white;
}
</style>

<div class="row">
    <div class="col-12">
    
        <model-viewer id="'. $this->getName().'" camera-controls enable-pan alt="dfsd" 
        src="'.$this->url.'" style="'.$this->getStyleCode().'"   >     
            <effect-composer render-mode="quality" msaa="8">
             <!-- <bloom-effect></bloom-effect> -->
                <color-grade-effect contrast="0.5" saturation="-1" opacity="0" blend-mode="skip"></color-grade-effect>
            </effect-composer>
            <div class="model-viewer-controls row">
                <div class="col-12">
                    <label for="opacity">Opacity</label>
                    <input id="opacity" type="range" min="0" max="1" step="0.01" value="1">
                </div>
                <div class="col-12">
                    <label for="blend-mode">Blend Mode:</label>
                    <select id="blend-mode">
                          <option value="skip">Skip</option>
                        <option value="default">Default</option>

                        <option value="add">Add</option>
                        <option value="subtract">Subtract</option>
                        <option value="divide">Divide</option>
                        <option value="negation">Negation</option>
                    </select>
               </div>
          
               <div class="col-12">
                    <!--
                    <label for="colorgrading">Color Grading</label>
                    <input type="checkbox" id="colorgrading" checked>
                    -->
                    <label for="tonemapping">Tonemapping:</label>
                    <select id="tonemapping">
                        <option value="aces_filmic">Aces Filmic</option>
                        <option value="reinhard">Reinhard</option>
                        <option value="reinhard2">Reinhard2</option>
                        <option value="reinhard2_adaptive">Reinhard2 Adaptive</option>
                        <option value="optimized_cineon">Optimized Cineon</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <label for="neutral">Neutral:</label>
                    <input id="neutral" type="checkbox" checked="true">
                    <br />
                    <button id="openfullscreen" onclick="openFullscreen();">Open Fullscreen</button>
                    <button id="closefullscreen" onclick="closeFullscreen();">Close Fullscreen</button>
                </div>    
            </div>  
        </model-viewer>
    </div>
</div>        

<script type="module">

const blendViewer = document.querySelector("model-viewer#'. $this->getName().'");
const blendEffect = blendViewer.querySelector("color-grade-effect");
const opacity = blendViewer.querySelector("#opacity");
const mode = blendViewer.querySelector("#blend-mode");

opacity.addEventListener("input", 
(e) => 
{
    blendEffect.opacity = e.target.value;
}
);
mode.addEventListener("change", (e) => blendEffect.blendMode = e.target.value);




const colorGradeEffect = blendViewer.querySelector("color-grade-effect");
//const colorGrading = blendViewer.querySelector("#colorgrading");
const tonemapping = blendViewer.querySelector("#tonemapping");

//colorGrading.addEventListener("change", (e) => colorGradeEffect.blendMode = e.target.checked ? "default" : "skip");
tonemapping.addEventListener("input", (e) => colorGradeEffect.tonemapping = e.target.value);


const checkbox = document.querySelector("#neutral");
checkbox.addEventListener("change",() => {
    blendViewer.environmentImage = checkbox.checked ? "" : "legacy";
});
</script>    

<script>
var elem = '. $this->getName().';
function openFullscreen() {
  if (elem.requestFullscreen) {
    elem.requestFullscreen();
  } else if (elem.mozRequestFullScreen) { /* Firefox */
    elem.mozRequestFullScreen();
  } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
    elem.webkitRequestFullscreen();
  } else if (elem.msRequestFullscreen) { /* IE/Edge */
    elem.msRequestFullscreen();
  }
  hideEl("openfullscreen");
  showEl("closefullscreen");
}

function closeFullscreen() {
  if (document.exitFullscreen) {
    document.exitFullscreen();
  } else if (document.mozCancelFullScreen) {
    document.mozCancelFullScreen();
  } else if (document.webkitExitFullscreen) {
    document.webkitExitFullscreen();
  } else if (document.msExitFullscreen) {
    document.msExitFullscreen();
  }
  hideEl("closefullscreen");
  showEl("openfullscreen"); 
}

function showEl(id)
{
    var el = document.getElementById(id);
    el.style.display = "block";
    el.style.visibility = "visible";  
}

function hideEl(id)
{
    var el = document.getElementById(id);
    el.style.display = "none";
    el.style.visibility = "hidden";  
}

hideEl("closefullscreen")

</script>        

';
        //src="'.$this->url.'"
        // src="http://192.168.10.48/louis/kprojectv4/kproject/Archimage2/utils/N914-002(2).glb"
        /*
        $html='
            
<model-editor>
  <div class="app">
    <div class="editor-body-root">
      <div class="mvContainer">
        <model-viewer camera-controls alt="dfsd" 
        src="http://192.168.10.48/louis/kprojectv4/kproject/Archimage2/utils/N914-002(2).glb"
        >
        </model-viewer>
      </div>

<me-tabs>
        <me-tabbed-panel icon="import_export">
          <me-export-panel></me-export-panel>
          <div style="margin-bottom: 70px;"></div>
          <div class="privacy">
            This &lt;model-viewer&gt; editor does not send any imported content to servers except to deploy to your mobile device:
            <a href="https://policies.google.com/privacy" class="privacy-link" target="_blank">
              Privacy
            </a>
          </div>
        </me-tabbed-panel>
        <me-tabbed-panel icon="create">
          <me-export-panel header></me-export-panel>
          <me-ibl-selector></me-ibl-selector>
          <me-animation-controls></me-animation-controls>
          <me-hotspot-panel></me-hotspot-panel>
        </me-tabbed-panel>
        <me-tabbed-panel icon="photo_camera">
          <me-export-panel header></me-export-panel>
          <me-camera-settings></me-camera-settings>
        </me-tabbed-panel>
        <me-tabbed-panel icon="color_lens">
          <me-materials-panel></me-materials-panel>
        </me-tabbed-panel>
        <me-tabbed-panel icon="search">
          <me-inspector-panel></me-inspector-panel>
        </me-tabbed-panel>
      </me-tabs>
    </div>
  </div>
</model-editor>

';        
        */
        $html.=parent::draw();
        return $html.$this->addStyle();
    }
    
    public function setAddStyle(bool $status) : void
    {
        $this->addStyle=$status;
    }
    
    #[\Override]
    public static function testMe(): ?static
    {
        //string $datatable_id,array $arrayColumns,array $arrayLines
        $class=new static("modelViewerId","https://archimage.efa.gr/action.php?r=troisd_download_public&id=959892&ext=.glb",2);
        $class->setAddStyle(false);
        $class->addStyleCode("background-color:#FFFFFF;height:400px;width:400px;");
        return $class;
    }      
}