<?php

/**
 * Description of KAlertifyPromptComponent
 *
 * @author Hippolyte
 */
class KAlertifyPromptComponent extends KAlertifyComponent
{
    public const INPUT_TEXT="text";
    public const INPUT_COLOR="color";
    public const INPUT_DATE="date";
    public const INPUT_DATETIME_LOCAL="datetime-local";
    public const INPUT_EMAIL="email";
    public const INPUT_MONTH="month";
    public const INPUT_NUMBER="number";
    public const INPUT_PASSWORD="password";
    public const INPUT_SEARCH="search";
    public const INPUT_TEL="tel";
    public const INPUT_TIME="time";
    public const INPUT_WEEK="week";

    protected ?string $messageOnCancel;
    protected bool $reverseButtons=false;
    protected string $type=self::INPUT_TEXT;
    protected string $cancelLabel="Annuler";    
    /**
     * 
     * @param string $message : Contenu du pop-up 
     * @param string|null $title : Titre du pop-up
     * @param string|null $messageOnValidate : Message lors d'une fermeture sur validation
     * @param string|null $messageOnCancel : Message lors d'une fermeture sur annulation
     */
    function __construct(string $message,?string $title=null,?string $messageOnValidate="",?string $messageOnCancel="")
    {
        parent::__construct($message,$title,$messageOnValidate);
        $this->messageOnCancel=empty($messageOnCancel) ? "" : "alertify.error('".$messageOnCancel."')";
    }

    public function draw(): string
    {
        $js='<script>';
        $js.='alertify.prompt()';
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
                .'"labels":{ok:"'.$this->label.'", cancel:"'.$this->cancelLabel.'"},'
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
                .'"type":"'.$this->type.'",'
                .'"reverseButtons":'.convertBoolToString($this->reverseButtons).','
                .'"onok": function(evt,value){'.$this->messageOnValidate.';},'
                .'"oncancel": function(evt,value){'.$this->messageOnCancel.';}'
                .'}).show()';
        $js.='</script>';
        return $js.$this->getKComponentDraw();
    }

    /**
     * Définit le sens des boutons
     * Valeur par défaut : false
     * @param bool $reverseButtons
     * @return void
     */
    public function setReverseButtons(bool $reverseButtons): void
    {
        $this->reverseButtons=$reverseButtons;
    }

    /**
     * Définit le type input
     * Valeur par défaut : KAlertifyPromptComponent::INPUT_TEXT
     * @param string $type
     * @return void
     */
    public function setType(string $type): void
    {
        if($type==self::INPUT_TEXT||$type==self::INPUT_COLOR||$type==self::INPUT_DATE
                ||$type==self::INPUT_DATETIME_LOCAL||$type==self::INPUT_EMAIL||$type==self::INPUT_MONTH
                ||$type==self::INPUT_NUMBER||$type==self::INPUT_PASSWORD||$type==self::INPUT_SEARCH
                ||$type==self::INPUT_TEL||$type==self::INPUT_TIME||$type==self::INPUT_WEEK)
        {
            $this->type=$type;
        }
    }

    /**
     * Définit une action lors de la fermeture du pop-up d'une annulation
     * Pas de valeur par défaut (constructeur messageOnCancel)
     * @param string $cancelAction
     * @return void
     */
    public function setCloseActionOnCancel(string $cancelAction="alertify.error('Cancel')"): void
    {
        $this->messageOnCancel=$cancelAction;
    }
    
    /**
     * Définit le label du bouton Cancel
     * Valeur par défaut : 'Annuler'
     * @param string $cancelLabel
     * @return void
     */
    public function setCancelLabel(string $cancelLabel): void
    {
        $this->cancelLabel=$cancelLabel;
    }
    
    #[\Override]
    public static function testMe(): ?static
    {
        //string $message,?string $title=null,?string $messageOnValidate=""
//        $class=new static("Confirm","component");
//        $class->setType(self::INPUT_COLOR);
//        return $class;
        return null;
    } 
}