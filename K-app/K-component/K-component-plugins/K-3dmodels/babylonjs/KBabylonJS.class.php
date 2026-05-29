<?php
/**
 * Description of KBabylonJS
 *
 * @author Mulot Louis
 */
class KBabylonJS extends KComponent
{
    private int $type=0;
    public const GLTF=1;
    public const GLB=2;
    private string $url="";
    function __construct(string $name,string $url,int $type)
    {
        parent::__construct();       
        $this->setNone();
        $this->setName("babylonjs_".$name);
        $this->url=$url;
        $this->type=$type;

        $layout=KApp::getInstance()->getLayout();
//        if(false)
//        {
//            $layout->addJsFileToBuffer(__DIR__."/js/v5.5/ammo.js",true);
//            $layout->addJsFileToBuffer(__DIR__."/js/v5.5/cannon.js",true);
//            $layout->addJsFileToBuffer(__DIR__."/js/v5.5/Oimo.js",true);
//            $layout->addJsFileToBuffer(__DIR__."/js/v5.5/babylon.js",true);
//            $layout->addJsFileToBuffer(__DIR__."/js/v5.5/babylonjs.loaders.min.js",true);
//            $layout->addJsFileToBuffer(__DIR__."/js/v5.5/babylonjs.serializers.min.js",true);
//            $layout->addJsFileToBuffer(__DIR__."/js/v5.5/babylonjs.materials.min.js",true);
//            $layout->addJsFileToBuffer(__DIR__."/js/v5.5/babylon.gui.min.js",true);
//            $layout->addJsFileToBuffer(__DIR__."/js/v5.5/babylon.inspector.bundle.js",true);
//
//        }
//        else
//        {
            $layout->addJsFileToBuffer(__DIR__."/js/babylon.viewer.js",true);
            $layout->addJsFileToBuffer(__DIR__."/js/babylon.gui.min.js",true);
            $layout->addJsFileToBuffer(__DIR__."/js/babylon.glTFFileLoader.min.js",true);
            $layout->addJsFileToBuffer(__DIR__."/js/babylon.gridMaterial.min.js",true);
            $layout->addJsFileToBuffer(__DIR__."/js/babylon.viewer.js",true);
            $layout->addJsFileToBuffer(__DIR__."/js/babylon.inspector.bundle.js",true);
//        }
    }
    
    function draw(): string
    {
        $html='<canvas id="'. $this->getName().'"></canvas>';
        $html.=parent::draw();
        return $html.$this->addStyle().$this->makeTheScript();
    }
    
    private function addStyle() : string
    {
        $style = '
<style>
    #'. $this->getName().' {
        width   : 100%;
        height: 800px;    
        background-color:black;
        touch-action: none;
    }
</style>
        ';
        
        return $style;
    }
        
    private function makeTheScript() : string
    { 
        $script = '
<script>
window.addEventListener("DOMContentLoaded", function()
{

    var canvas = document.getElementById("'. $this->getName().'");
    var engine = new BABYLON.Engine(canvas, true, { preserveDrawingBuffer: true, stencil: true });
    var isFullScreen = false;

    document.addEventListener("fullscreenchange", onFullScreenChange, false);
    document.addEventListener("mozfullscreenchange", onFullScreenChange, false);
    document.addEventListener("webkitfullscreenchange", onFullScreenChange, false);
    document.addEventListener("msfullscreenchange", onFullScreenChange, false);

    function onFullScreenChange() 
    {
        if (document.fullscreen !== undefined) 
        {
            isFullScreen = document.fullscreen;
        } 
        else if (document.mozFullScreen !== undefined) 
        {
            isFullScreen = document.mozFullScreen;
        } 
        else if (document.webkitIsFullScreen !== undefined) 
        {
            isFullScreen = document.webkitIsFullScreen;
        } 
        else if (document.msIsFullScreen !== undefined) 
        {
            isFullScreen = document.msIsFullScreen;
        }
    }
    
    function switchFullscreen() 
    {
        if (!isFullScreen) 
        {
            engine.enterFullscreen();
        }
        else 
        {
            engine.exitFullscreen();
        }
    };

    var advancedTexture;
    var loadingScreen = (function(){
        function loadingScreen(scene){
            if(!advancedTexture){
                advancedTexture = BABYLON.GUI.AdvancedDynamicTexture.CreateFullscreenUI("UI");
            }
            this._scene = scene;
           
            this._container = new BABYLON.GUI.Container();
            this._container.zIndex = 999;

            this._background = new BABYLON.GUI.Rectangle();
            this._background.width = 1;
            this._background.height = 1;
            this._background.background = "black";

            this._text = new BABYLON.GUI.TextBlock(null, "Loading");
            this._text.color = "white";
            this._text.fontSize = "28px";
            this._text.height = 0.25;
            this._text.verticalAlignment = BABYLON.GUI.Control.VERTICAL_ALIGNMENT_CENTER;

            advancedTexture.addControl(this._container);
            this._container.addControl(this._background);
            this._container.addControl(this._text);

            this.alpha = 0;

            var _this = this;
            this._loadingAnimation = function(){
                _this.alpha++;

                if(_this.alpha % 101 === 0){
                    _this.alpha = 1;
                    _this._text.text = "Loading";
                }
                else if(_this.alpha % 25 === 0){
                    _this._text.text += "."
                }
            };
            
        }
        loadingScreen.prototype.displayLoadingUI = function(){
            var _this = this;
            if(_this._scene){
                _this._scene.registerBeforeRender(_this._loadingAnimation);
            }
            _this._container.isVisible = true;

        };
        loadingScreen.prototype.hideLoadingUI = function(){
            var _this = this;
            if(_this._scene){
                _this._scene.unregisterBeforeRender(_this._loadingAnimation);
            }
            _this._container.isVisible = false;
        };

        return loadingScreen;
    }());

    var createScene = function(base64_model_content)
    {
        var scene = new BABYLON.Scene(engine);
        var light = new BABYLON.PointLight("Omni", new BABYLON.Vector3(20, 20, 100), scene);
        
        var camera = new BABYLON.ArcRotateCamera("Camera", 0, 0.8, 10, BABYLON.Vector3.Zero(), scene);
        camera.attachControl(canvas, false);
        
        engine.loadingScreen = new loadingScreen(scene);
        engine.displayLoadingUI();
        
        let url="";
        let object="";
        if(base64_model_content!="")
        {
            object=base64_model_content;
        }
        else
        {
            url="' . $this->url  . '";
            object="&ext=model_'.$this->id.'.gltf";
        }
        

        BABYLON.SceneLoader.Append(url,object, scene, function (scene)
        {        
            var img_troisd = scene.meshes[1];
            var dimensions = img_troisd.getBoundingInfo().boundingBox.extendSize;
            var dimX = dimensions.x;
            var dimY = dimensions.y;
            var dimZ = dimensions.z;
            
            scene.createDefaultCameraOrLight(true, true, true);
            scene.activeCamera.upVector = new BABYLON.Vector3(0,0,1);
            
            scene.activeCamera.setPosition(new BABYLON.Vector3(0,0,dimZ*12));
            scene.activeCamera.storeState();
            scene.activeCamera.upperRadiusLimit = dimZ*15;
            scene.activeCamera.lowerRadiusLimit = 0;

            scene.activeCamera.lowerBetaLimit = null;
            scene.activeCamera.upperBetaLimit = null;
            scene.activeCamera.allowUpsideDown = true;
            
            scene.activeCamera.keysLeft = [37,81];            
            scene.activeCamera.keysUp = [38,90];
            scene.activeCamera.keysRight = [39, 68];
            scene.activeCamera.keysDown = [40,83];

            scene.activeCamera.angularSensibilityX = 4000;
            scene.activeCamera.angularSensibilityY = 4000;
            scene.activeCamera.wheelPrecision = 1400/(dimZ*12);         
            scene.activeCamera.panningSensibility = 8000/dimX;
            scene.activeCamera.panningDistanceLimit = dimX+dimY;
            scene.activeCamera.inputs.attached.keyboard.angularSpeed = 0.005;
            
            engine.hideLoadingUI();
        });

        scene.registerBeforeRender(function () {
            light.position = camera.position;
        });
        
        return scene;
    }
';
        if($this->type==self::GLTF)
        {
            $script.= '
            var scene = createScene("");

            engine.runRenderLoop(function(){
                scene.render();
            });

            window.addEventListener("resize", function(){
                engine.resize();
            });

            var butFullScreen = document.getElementById("btn-FullScreen");
            butFullScreen.addEventListener("click", switchFullscreen);

            var butReset = document.getElementById("btn-Reset");
            butReset.addEventListener("click", function(){scene.activeCamera.restoreState();});

            var butInspector = document.getElementById("btn-Inspector");
            butInspector.addEventListener("click", function(){scene.debugLayer.show({ showExplorer : false});});                
';
        }
        else if($this->type==self::GLB)
        {
            $script.= '
    $.ajax({
        url: "' .$this->url . '",
        success: function(data)
        {
            if(data!="")
            {
                var scene = createScene(data);

                engine.runRenderLoop(function(){
                    scene.render();
                });

                window.addEventListener("resize", function(){
                    engine.resize();
                });

                var butFullScreen = document.getElementById("btn-FullScreen");
                butFullScreen.addEventListener("click", switchFullscreen);

                var butReset = document.getElementById("btn-Reset");
                butReset.addEventListener("click", function(){scene.activeCamera.restoreState();});

                var butInspector = document.getElementById("btn-Inspector");
                butInspector.addEventListener("click", function(){scene.debugLayer.show({ showExplorer : false});});
                

//scene.debugLayer.show({
//    overlay:false, 
//   
//});



            }
            else
            {
                alert("Error with the 3D model");
            }
        },    
        error: function(data)
        {
            alert("Error with the 3D model");
        }
    });                
';            
        }

        $script.= '
});
</script>
        ';
   
        //var base64_model_content2 = "data:base64,Z2xURgIAAAD4CAAAlAUAAEpTT057ImFjY2Vzc29ycyI6W3sibmFtZSI6IjJmdHg0ZnRfMV9wb3NpdGlvbnMiLCJjb21wb25lbnRUeXBlIjo1MTI2LCJjb3VudCI6MjQsIm1pbiI6Wy0yNCwwLC0xMl0sIm1heCI6WzI0LDIsMTJdLCJ0eXBlIjoiVkVDMyIsImJ1ZmZlclZpZXciOjAsImJ5dGVPZmZzZXQiOjB9LHsibmFtZSI6IjJmdHg0ZnRfMV9ub3JtYWxzIiwiY29tcG9uZW50VHlwZSI6NTEyNiwiY291bnQiOjI0LCJtaW4iOlstMSwtMSwtMV0sIm1heCI6WzEsMSwxXSwidHlwZSI6IlZFQzMiLCJidWZmZXJWaWV3IjowLCJieXRlT2Zmc2V0IjoyODh9LHsibmFtZSI6IjJmdHg0ZnRfMV90ZXhjb29yZHMiLCJjb21wb25lbnRUeXBlIjo1MTI2LCJjb3VudCI6MjQsIm1pbiI6Wy0xLjM0MDcwMDAzMDMyNjg0MzMsLTEuNjgxMzk5OTQxNDQ0Mzk3XSwibWF4IjpbNS4zNjI4MDAxMjEzMDczNzMsMy42ODE0MDAwNjA2NTM2ODY1XSwidHlwZSI6IlZFQzIiLCJidWZmZXJWaWV3IjoxLCJieXRlT2Zmc2V0IjowfSx7Im5hbWUiOiIyZnR4NGZ0XzFfMF9pbmRpY2VzIiwiY29tcG9uZW50VHlwZSI6NTEyMywiY291bnQiOjM2LCJtaW4iOlswXSwibWF4IjpbMjNdLCJ0eXBlIjoiU0NBTEFSIiwiYnVmZmVyVmlldyI6MiwiYnl0ZU9mZnNldCI6MH1dLCJhc3NldCI6eyJnZW5lcmF0b3IiOiJvYmoyZ2x0ZiIsInZlcnNpb24iOiIyLjAifSwiYnVmZmVycyI6W3sibmFtZSI6ImlucHV0IiwiYnl0ZUxlbmd0aCI6ODQwfV0sImJ1ZmZlclZpZXdzIjpbeyJuYW1lIjoiYnVmZmVyVmlld18wIiwiYnVmZmVyIjowLCJieXRlTGVuZ3RoIjo1NzYsImJ5dGVPZmZzZXQiOjAsImJ5dGVTdHJpZGUiOjEyLCJ0YXJnZXQiOjM0OTYyfSx7Im5hbWUiOiJidWZmZXJWaWV3XzEiLCJidWZmZXIiOjAsImJ5dGVMZW5ndGgiOjE5MiwiYnl0ZU9mZnNldCI6NTc2LCJieXRlU3RyaWRlIjo4LCJ0YXJnZXQiOjM0OTYyfSx7Im5hbWUiOiJidWZmZXJWaWV3XzIiLCJidWZmZXIiOjAsImJ5dGVMZW5ndGgiOjcyLCJieXRlT2Zmc2V0Ijo3NjgsInRhcmdldCI6MzQ5NjN9XSwibWF0ZXJpYWxzIjpbeyJuYW1lIjoid2lyZV8xOTExOTExOTEiLCJwYnJNZXRhbGxpY1JvdWdobmVzcyI6eyJiYXNlQ29sb3JGYWN0b3IiOlswLjUsMC41LDAuNSwxXSwibWV0YWxsaWNGYWN0b3IiOjAsInJvdWdobmVzc0ZhY3RvciI6MX0sImVtaXNzaXZlRmFjdG9yIjpbMCwwLDBdLCJhbHBoYU1vZGUiOiJPUEFRVUUiLCJkb3VibGVTaWRlZCI6ZmFsc2V9XSwibWVzaGVzIjpbeyJuYW1lIjoiMmZ0eDRmdF8xIiwicHJpbWl0aXZlcyI6W3siYXR0cmlidXRlcyI6eyJQT1NJVElPTiI6MCwiTk9STUFMIjoxLCJURVhDT09SRF8wIjoyfSwiaW5kaWNlcyI6MywibWF0ZXJpYWwiOjAsIm1vZGUiOjR9XX1dLCJub2RlcyI6W3sibmFtZSI6IjJmdHg0ZnQiLCJtZXNoIjowfV0sInNjZW5lIjowLCJzY2VuZXMiOlt7Im5vZGVzIjpbMF19XX1IAwAAQklOAAAAwMEAAACAAABAQQAAwMEAAABAAABAwQAAwMEAAAAAAABAwQAAwMEAAABAAABAQQAAwMEAAAAAAABAwQAAwEEAAABAAABAwQAAwEEAAAAAAABAwQAAwMEAAABAAABAwQAAwEEAAAAAAABAwQAAwEEAAABAAABAQQAAwEEAAACAAABAQQAAwEEAAABAAABAwQAAwEEAAACAAABAQQAAwMEAAABAAABAQQAAwMEAAACAAABAQQAAwEEAAABAAABAQQAAwEEAAABAAABAQQAAwMEAAABAAABAwQAAwMEAAABAAABAQQAAwEEAAABAAABAwQAAwEEAAACAAABAQQAAwMEAAAAAAABAwQAAwEEAAAAAAABAwQAAwMEAAACAAABAQQAAgL8AAAAAAAAAgAAAgL8AAAAAAAAAgAAAgL8AAAAAAAAAgAAAgL8AAAAAAAAAgAAAAIAAAIC/AAAAgAAAAIAAAIC/AAAAgAAAAIAAAIC/AAAAgAAAAIAAAIC/AAAAgAAAgD8AAACAAAAAgAAAgD8AAACAAAAAgAAAgD8AAACAAAAAgAAAgD8AAACAAAAAgAAAAAAAAIA/AAAAAAAAAAAAAIA/AAAAAAAAAAAAAIA/AAAAAAAAAAAAAIA/AAAAAAAAAAAAAAAAAACAvwAAAAAAAAAAAACAvwAAAAAAAAAAAACAvwAAAAAAAAAAAACAvwAAAAAAAAAAAACAPwAAAAAAAAAAAACAPwAAAAAAAAAAAACAPwAAAAAAAAAAAACAPw+cK0AAAIA/AAAAALTIRj8AAAAAAACAPw+cK0C0yEY/D5yrQAAAgD8AAAAAtMhGPwAAAAAAAIA/D5yrQLTIRj8PnCtAAACAPwAAAIC0yEY/AAAAgAAAgD8PnCtAtMhGPw+cq0AAAIA/AAAAALTIRj8AAAAAAACAPw+cq0C0yEY/D5yrPx04178PnKu/D5xrQA+cqz8PnGtAD5yrvx04178Xt9E4HTjXv7KdK0APnGtAsp0rQB04178Xt9E4D5xrQAAAAQACAAEAAAADAAQABQAGAAUABAAHAAgACQAKAAkACAALAAwADQAOAA0ADAAPABAAEQASABEAEAATABQAFQAWABUAFAAXAA==";
        return $script;         
    }
}