<?php
/**
 * KAudioComponent permet d'instancier un lecteur audio
 * s'appuyant sur la librarie wavesurfer.js
 * @see https://wavesurfer-js.org/
 * @author maxime.tueux@efa.gr
 */
class KAudioComponent extends KComponent
{
   // protected Documents $doc;
   // protected bool $public;
    protected KURL $url;    
    protected KFile $audioFile;
    protected bool $playerControl;

    function __construct(KFile $audioFile,KURL $url, bool $playerControl = true)
    {
        parent::__construct();

        $this->audioFile = $audioFile;
        $this->url=$url;
        $this->playerControl = $playerControl;

        $layout = KApp::getInstance()->getLayout();
        $layout->addJsFileToBuffer(__DIR__."/js/wavesurfer.js");
        
        $this->setName("KAudio_".KRandom::makeRandom());
    }

    public function draw(): string
    {

        $html = $this->wavesurferHtml();

        $playBtn = new KTitleButton("", KTitleButton::$TYPE_SUCCESS, "fa fa-play");
        $playBtn->addClassName("player-play-btn");
        $playBtn = $playBtn->draw();

        $skipBackwardBtn = new KTitleButton("", KTitleButton::$TYPE_INFO, "fa fa-backward");
        $skipBackwardBtn->addClassName("player-skip-backward-btn");
        $skipBackwardBtn = $skipBackwardBtn->draw();

        $skipForwardBtn = new KTitleButton("", KTitleButton::$TYPE_INFO, "fa fa-forward");
        $skipForwardBtn->addClassName("player-skip-forward-btn");
        $skipForwardBtn = $skipForwardBtn->draw();

        $rewindBtn = new KTitleButton("", KTitleButton::$TYPE_PRIMARY, "fa fa-backward-step");
        $rewindBtn->addClassName("player-rewind-btn");
        $rewindBtn = $rewindBtn->draw();

        $stopBtn = new KTitleButton("", KTitleButton::$TYPE_DANGER, "fa fa-stop");
        $stopBtn->addClassName("player-stop-btn");
        $stopBtn = $stopBtn->draw();

        $playbackSpeedBtn = new KTitleButton("<span id='player-playback-speed'>x1.0</span>", KTitleButton::$TYPE_SECONDARY, "");
        $playbackSpeedBtn->addClassName("player-playback-speed-btn");
        $playbackSpeedBtn = $playbackSpeedBtn->draw();
        
        $dlBtn = new KTitleButton("", KTitleButton::$TYPE_PRIMARY, "fa fa-download");
        $dlBtn->setActionKURL($this->url);
        //$this->url->printURLWithoutAmp()
        $dlBtn = $dlBtn->draw();        
        

        $duration = new HTMLComponent("<b>Durée :</b> <span id='player-duration'></span>");
        $duration = $duration->draw();

        $fileType = new HTMLComponent("<b>Type de fichier :</b> ".strtoupper($this->audioFile->getExtension())."");
        $fileType = $fileType->draw();

        $fileSize = new HTMLComponent("<b>Taille du fichier :</b> ".str_replace('.', ',', $this->audioFile->length())."");
        $fileSize = $fileSize->draw();
        
        
        $html = $html."<br> ".$playBtn." &nbsp;&nbsp;".$skipBackwardBtn." ".$skipForwardBtn." &nbsp;&nbsp;".$rewindBtn." ".$stopBtn." ".$playbackSpeedBtn." &nbsp;".$dlBtn."<br><br>".$duration."<br>".$fileType."<br>".$fileSize;

        $script = $this->wavesurferScript();

        $this->addJSText($script);

        return $html;
    }

    private function wavesurferScript(): string
    {
        $script = "
    $(function() 
    {
        const fileURL = '".$this->url->printURLWithoutAmp()."';

        // Create a new instance of Wavesurfer
        const wavesurfer = WaveSurfer.create({
            container: '#".$this->getName()."',
            waveColor: '".StyleManager::getInstance()->main_colour."',
            progressColor: '".StyleManager::getInstance()->colour_2."',
            cursorColor: '".StyleManager::getInstance()->colour_2."',
            barWidth: 3,
            barRadius: 3,
            cursorWidth: 3,
            height: 200,
            barGap: 3
        });
        

        let playing = false;
        
        // Load the audio file
        wavesurfer.load(fileURL);
    
        // Get the play, pause, and rewind buttons
        const playBtn = $('.player-play-btn')[0];
        const playBtnInnerIcon = $('.player-play-btn i')[0];
        const rewindBtn = $('.player-rewind-btn')[0];
        const stopBtn = $('.player-stop-btn')[0];

        const skipBackwardBtn = $('.player-skip-backward-btn')[0];
        const skipForwardBtn = $('.player-skip-forward-btn')[0];

        const playbackSpeedBtn = $('.player-playback-speed-btn')[0];

        const durationElement = $('#player-duration');

        // Display duration in second with two decimals max if less than 60s otherwise in minutes
        function getPrintableDuration(duration) {
            let prefix = '';
            let tmp = 0;

            if (duration < 60) {
                tmp = duration.toFixed(2);

                if (tmp > 1) {
                    prefix = 'secondes';
                } else {
                    prefix = 'seconde';
                }

            } else {
                tmp = (duration / 60).toFixed(2);

                if (tmp > 1) {
                    prefix = 'minutes';
                } else {
                    prefix = 'minute';
                }
            }

            tmp = tmp.replace('.', ',');

            return tmp + ' ' + prefix;
        }
    
        // Add event listeners to the buttons
        playBtn.addEventListener('click', function() {
            if (playing) {
                wavesurfer.pause();
                playing = false;
                playBtnInnerIcon.classList.remove('fa-pause');
                playBtnInnerIcon.classList.add('fa-play');
            } else {
                wavesurfer.play();
                playing = true;
                playBtnInnerIcon.classList.remove('fa-play');
                playBtnInnerIcon.classList.add('fa-pause');
            }
        });
    
        stopBtn.addEventListener('click', function() {
            playBtnInnerIcon.classList.remove('fa-pause');
            playBtnInnerIcon.classList.add('fa-play');
            wavesurfer.stop();
            playing = false;
        });
    
        rewindBtn.addEventListener('click', function() {
            wavesurfer.stop();
            wavesurfer.play();
            playing = true;
            playBtnInnerIcon.classList.remove('fa-play');
            playBtnInnerIcon.classList.add('fa-pause');
        });

        skipBackwardBtn.addEventListener('click', function() {
          wavesurfer.skipBackward();
        });

        skipForwardBtn.addEventListener('click', function() {
          wavesurfer.skipForward();
        });

        let currentSpeedIndex = 0;
        // Attach the click event listener to the button
        playbackSpeedBtn.addEventListener('click', function () {

            let playbackSpeedElt = $('#player-playback-speed');
            
            let speeds = [1.0, 1.25, 1.5, 0.5, 0.75];

            
            currentSpeedIndex = (currentSpeedIndex + 1) % speeds.length;
            
            playbackSpeedElt.html('x' + speeds[currentSpeedIndex].toFixed(2));
            wavesurfer.setPlaybackRate(speeds[currentSpeedIndex]);
        });
    
        // Set the custom cursor style
        wavesurfer.on('ready', function() {
          const container = document.querySelector('.audio-player-container');
          container.classList.add('wavesurfer-cursor');

          const duration = wavesurfer.getDuration();
          durationElement.html(getPrintableDuration(duration));


        });

        wavesurfer.on('finish', function() {
            playBtnInnerIcon.classList.remove('fa-pause');
            playBtnInnerIcon.classList.add('fa-play');
            playing = false;
        });
    });";

        return $script;
    }

    private function wavesurferHtml(): string
    {
        $html = '
        <div class="audio-player-container">
            <div id="'.$this->getName().'"></div>
        </div>
        ';

        return $html;
    }
    
    
//    #[\Override]
//    public static function testMe(): ?static
//    {
//        //string $datatable_id,array $arrayColumns,array $arrayLines
//        //KFile $audioFile,KURL $url, bool $playerControl = true
//        $class=new static(new KFile(__FILE__),new KURL("https://file-examples.com/wp-content/storage/2017/11/file_example_OOG_1MG.ogg"),true);
//        $class->addStyleCode("background-color:#FFFFFF;height:400px;width:400px;");
//        return $class;
//    }    
}