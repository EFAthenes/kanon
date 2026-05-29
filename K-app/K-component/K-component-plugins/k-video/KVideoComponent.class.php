<?php
/**
 * KVideoComponent permet d'instancier un lecteur vidéo
 * s'appuyant sur la librarie Plyr
 * @see https://plyr.io/
 * @author maxime.tueux@efa.gr
 */
class KVideoComponent extends KComponent
{
    protected KURL $url;
    protected KFile $videoFile;    
    protected bool $playerControl;

    function __construct(KFile $videoFile, KURL $url, bool $playerControl = true)
    {
        parent::__construct();

        $this->url = $url;
        $this->videoFile = $videoFile;
        $this->playerControl = $playerControl;

        $layout = KApp::getInstance()->getLayout();
        $layout->addCssFileToBuffer(__DIR__."/css/plyr.css");
        $layout->addJsFileToBuffer(__DIR__."/js/plyr.js");
        
        $this->setName("KvideoPlayer_".KRandom::makeRandom());
    }

    public function draw(): string
    {

        $html = $this->wavesurferHtml();
        $script = $this->wavesurferScript();

        $this->addJSTextOnDocumentReady($script);

        return $html;
    }

    private function wavesurferScript(): string
    {
        $script = "
var controls =
[
    'play-large', // The large play button in the center
    'restart', // Restart playback
    'rewind', // Rewind by the seek time (default 10 seconds)
    'play', // Play/pause playback
    'fast-forward', // Fast forward by the seek time (default 10 seconds)
    'progress', // The progress bar and scrubber for playback and buffering
    'current-time', // The current time of playback
    'duration', // The full duration of the media
    'mute', // Toggle mute
    'volume', // Volume control
    'captions', // Toggle captions
    'settings', // Settings menu
    'pip', // Picture-in-picture (currently Safari only)
    'airplay', // Airplay (currently Safari only)
    'download', // Show a download button with a link to either the current source or a custom URL you specify in your options
    'fullscreen' // Toggle fullscreen
];            
const player = new Plyr('#".$this->getName()."', { controls });";

        return $script;
    }

    private function wavesurferHtml(): string
    {
        
        $sourceType = "video/".$this->videoFile->getExtension();

        $html = '
        <video id="'.$this->getName().'" controls style="width: 100%; --plyr-color-main: '.StyleManager::getInstance()->main_colour.';">
          <source src="'.$this->url->printURLWithoutAmp().'" type="'.$sourceType.'">
        </video>
        ';

        return $html;
    }
}