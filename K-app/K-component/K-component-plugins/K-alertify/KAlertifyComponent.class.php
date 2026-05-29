<?php

/**
 * Description of KAlertifyComponent
 * https://alertifyjs.com/
 * @author Hippolyte
 */
class KAlertifyComponent extends KComponent
{
    protected ?string $title;
    protected ?string $messageOnValidate;

    // TRANSITIONS
    public const TRANSITION_SLIDE="slide";
    public const TRANSITION_ZOOM="zoom";
    public const TRANSITION_FLIPX="flipx";
    public const TRANSITION_FLIPY="flipy";
    public const TRANSITION_FADE="fade";
    public const TRANSITION_PULSE="pulse";

    // PROPERTIES
    protected bool $autoReset=true;
    protected bool $basic=false;
    protected bool $closable=true;
    protected bool $closableByDimmer=true;
    protected bool $frameless=false;
    protected bool $invokeOnCloseOff=false;
    protected string $label="Valider";
    protected bool $maximizable=true;
    protected string $message;
    protected bool $modal=true;
    protected bool $movable=true;
    protected bool $moveBounded=false;
    protected bool $overflow=true;
    protected bool $padding=true;
    protected bool $pinnable=true;
    protected bool $resizable=false;
    protected bool $startMaximized=false;
    protected string $transition=self::TRANSITION_PULSE;
    protected bool $transitionOff=false;

    /**
     * 
     * @param string $message : Contenu du pop-up 
     * @param string|null $title : Titre du pop-up
     * @param string|null $messageOnValidate : Message lors d'une fermeture sur validation
     */
    function __construct(string $message,?string $title=null,?string $messageOnValidate="")
    {
        parent::__construct();
        self::includeFilesToBuffer();
        //$this->addJSText($message);
        $this->title=$title;
        $this->message=$message;
        $this->messageOnValidate=empty($messageOnValidate) ? "" : "alertify.success('".$messageOnValidate."')";
    }
    
    public static function includeFilesToBuffer() : void
    {
        $layout=KApp::getInstance()->getLayout();
        $layout->addJsFileToBuffer(__DIR__."/js/alertify.min.js");
        $layout->addJsFileToBuffer(__DIR__."/js/alertify.bootstrap.js");
        $layout->addCssFileToBuffer(__DIR__."/css/alertify.min.css");
        $layout->addCssFileToBuffer(__DIR__."/css/theme/bootstrap.min.css");        
    }

    public function draw(): string
    {
        $js='<script>';
        $js.='alertify.confirm()';
        $title=empty($this->title) ? 'Alerte' : $this->title.'';
        $js.='.setting({'
                .'"title":"'.$title.'",'
                .'"message":"'.$this->message.'",'
                .'"autoReset":'.convertBoolToString($this->autoReset).','
                .'"basic":'.convertBoolToString($this->basic).','
                .'"closable":'.convertBoolToString($this->closable).','
                .'"closableByDimmer":'.convertBoolToString($this->closableByDimmer).','
                .'"frameless":'.convertBoolToString($this->frameless).','
                .'"invokeOnCloseOff":'.convertBoolToString($this->invokeOnCloseOff).','
                .'"label":"'.$this->label.'",'
                .'"maximizable":'.convertBoolToString($this->maximizable).','
                .'"modal":'.convertBoolToString($this->modal).','
                .'"movable":'.convertBoolToString($this->movable).','
                .'"moveBounded":'.convertBoolToString($this->moveBounded).','
                .'"overflow":'.convertBoolToString($this->overflow).','
                .'"padding":'.convertBoolToString($this->padding).','
                .'"pinnable":'.convertBoolToString($this->pinnable).','
                .'"resizable":'.convertBoolToString($this->resizable).','
                .'"startMaximized":'.convertBoolToString($this->startMaximized).','
                .'"transition":"'.$this->transition.'",'
                .'"transitionOff":'.convertBoolToString($this->transitionOff).','
                .'"onok": function(){'.$this->messageOnValidate.';}'
                .'}).show()';
        $js.='</script>';
        return $js.parent::draw();
    }
    
    protected function getKComponentDraw(): string 
    {
        return parent::getClassName().parent::draw();
    }

    /**
     * Définit une valeur indiquant si la boîte de dialogue doit réinitialiser 
     * sa taille/position lors du redimensionnement de la fenêtre.
     * Valeur par défaut : true
     * @param bool $autoReset
     * @return void
     */
    public function setAutoReset(bool $autoReset): void
    {
        $this->autoReset=$autoReset;
    }

    /**
     * Définit le mode d'affichage de base de la boîte de dialogue
     * Si basique, cache l'en-tête et le pied de page 
     * Valeur par défaut : false
     * @param bool $basic
     * @return void
     */
    public function setBasic(bool $basic): void
    {
        $this->basic=$basic;
    }

    /**
     * Définit l'affichage du bouton croix pour fermer la fenêtre
     * Valeur par défaut : true
     * @param bool $closable
     * @return void
     */
    public function setClosable(bool $closable): void
    {
        $this->closable=$closable;
    }

    /**
     * Définit l'activation de la fermeture de l'alerte en cliquant hors de la
     * fenêtre
     * Valeur par défaut : true
     * @param bool $closableByDimmer
     * @return void
     */
    public function setClosableByDimmer(bool $closableByDimmer): void
    {
        $this->closableByDimmer=$closableByDimmer;
    }

    /**
     * Définit le mode d'affichage sans cadre du dialogue
     * L'espace du pied de page n'étant pas réservé, l'en-tête et les 
     * commandes de la boîte de dialogue peuvent empiéter sur le contenu.
     * Valeur par défaut : false
     * @param bool $frameless
     * @return void
     */
    public function setFrameless(bool $frameless): void
    {
        $this->frameless=$frameless;
    }

    /**
     * Définit si on doit différencier la fermeture par bouton et par dimmer 
     * quant au déclanchement de la closeAction
     * Valeur par défaut : false
     * @param bool $invokeOnCloseOff
     * @return void
     */
    public function setInvokeOnCloseOff(bool $invokeOnCloseOff): void
    {
        $this->invokeOnCloseOff=$invokeOnCloseOff;
    }
    
    /**
     * Définit une action lors de la fermeture du pop-up
     * Pas de valeur par défaut (constructeur messageOnValidate)
     * @param string $closeAction
     * @return void
     */
    public function setCloseAction(string $closeAction="alertify.success('Ok')"): void
    {
        $this->messageOnValidate=$closeAction;
    }

    /**
     * Définit le label du bouton OK
     * Valeur par défaut : 'Valider'
     * @param string $label
     * @return void
     */
    public function setLabel(string $label): void
    {
        $this->label=$label;
    }

    /**
     * Définit l'affichage du bouton maximize
     * Valeur par défaut : true
     * @param bool $maximizable
     * @return void
     */
    public function setMaximizable(bool $maximizable): void
    {
        $this->maximizable=$maximizable;
    }

    /**
     * Redéfinit la valeur du message
     * Pas de valeur par défaut (constructeur)
     * @param string $message
     * @return void
     */
    public function setMessage(string $message): void
    {
        $this->message=$message;
    }

    /**
     * Définit l'affichage d'un filtre et le blocage de la page si le pop-up 
     * est actif
     * Valeur par défaut : true
     * @param bool $modal
     * @return void
     */
    public function setModal(bool $modal): void
    {
        $this->modal=$modal;
    }

    /**
     * Définit si le pop-up est déplaçable ou non
     * Valeur par défaut : true
     * @param bool $movable
     * @return void
     */
    public function setMovable(bool $movable): void
    {
        $this->movable=$movable;
    }

    /**
     * Définit si le pop-up est limité par les frontières de l'écran
     * Valeur par défaut : false
     * @param bool $moveBounded
     * @return void
     */
    public function setMoveBounded(bool $moveBounded): void
    {
        $this->moveBounded=$moveBounded;
    }

    /**
     * Définit si le débordement du contenu est géré par le pop-up
     * Valeur par défaut : true
     * @param bool $overflow
     * @return void
     */
    public function setOverflow(bool $overflow): void
    {
        $this->overflow=$overflow;
    }

    /**
     * Définit si le remplissage du contenu est géré par le pop-up
     * Valeur par défaut : true
     * @param bool $padding
     * @return void
     */
    public function setPadding(bool $padding): void
    {
        $this->padding=$padding;
    }

    /**
     * Définit l'affichage du pin pour la boîte de dialogue (le modal doit être
     * à true pour l'utiliser)
     * Valeur par défaut : true
     * @param bool $pinnable
     * @return void
     */
    public function setPinnable(bool $pinnable): void
    {
        $this->pinnable=$pinnable;
    }

    /**
     * Définit si le pop-up peut être redimensionner
     * Valeur par défaut : false
     * @param bool $resizable
     * @return void
     */
    public function setResizable(bool $resizable): void
    {
        $this->resizable=$resizable;
    }

    /**
     * Définit si le pop-up doit être maximisé par défaut
     * Valeur par défaut : false
     * @param bool $startMaximized
     * @return void
     */
    public function setStartMaximized(bool $startMaximized): void
    {
        $this->startMaximized=$startMaximized;
    }

    /**
     * Définit l'effet de transition parmi : TRANSITION_FADE, TRANSITION_FLIPX, 
     * TRANSITION_FLIPY, TRANSITION_PULSE, TRANSITION_SLIDE et TRANSITION_ZOOM
     * Valeur par défaut : KAlertifyComponent::TRANSITION_PULSE
     * @param string $transition
     * @return void
     */
    public function setTransition(string $transition): void
    {
        if($transition==self::TRANSITION_FADE||$transition==self::TRANSITION_FLIPX||$transition==self::TRANSITION_FLIPY||$transition==self::TRANSITION_PULSE||$transition==self::TRANSITION_SLIDE||$transition==self::TRANSITION_ZOOM)
        {
            $this->transition=$transition;
        }
    }

    /**
     * Définit l'utilisation de transition
     * Valeur par défaut : false
     * @param bool $transitionOff
     * @return void
     */
    public function setTransitionOff(bool $transitionOff): void
    {
        $this->transitionOff=$transitionOff;
    }

    #[\Override]
    public static function testMe(): ?static
    {
//        //string $message,?string $title=null,?string $messageOnValidate=""
//        $class=new static("KAlertifyComponent","title");
//        $class->setModal(false);
//        $class->setOverflow(true);
//        $class->setMaximizable(false);
//        return $class;
        return null;
    }  
}