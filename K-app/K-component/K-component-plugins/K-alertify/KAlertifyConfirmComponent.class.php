<?php

/**
 * Description of KAlertifyConfirmComponent
 *
 * @author Hippolyte
 */
class KAlertifyConfirmComponent extends KAlertifyComponent
{
    protected ?string $messageOnCancel;
    protected string $defaultFocus="ok";
    protected string $cancelLabel="Annuler";
    protected bool $defaultFocusOff=false;
    protected bool $reverseButtons=false;

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
                .'"defaultFocus":"'.$this->defaultFocus.'",'
                .'"defaultFocusOff":'.convertBoolToString($this->defaultFocusOff).','
                .'"reverseButtons":'.convertBoolToString($this->reverseButtons).','
                .'"onok": function(){'.$this->messageOnValidate.';},'
                .'"oncancel": function(){'.$this->messageOnCancel.';}'
                .'}).show()';
        $js.='</script>';
        return $js.$this->getKComponentDraw();
    }

    /**
     * Définit le nom du bouton sur lequel le focus est fait
     * Valeur par défaut : "ok"
     * @param string $defaultFocus
     * @return void
     */
    public function setDefaultFocus(string $defaultFocus): void
    {
        $this->defaultFocus=$defaultFocus;
    }

    /**
     * Définit l'utilisation du focus
     * Valeur par défaut : false
     * @param bool $defaultFocusOff
     * @return void
     */
    public function setDefaultFocusOff(bool $defaultFocusOff): void
    {
        $this->defaultFocusOff=$defaultFocusOff;
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
//        $class=new static("Confirm","component","MesageOnCancel","MessageOnValidate");
//        return $class;
        return null;
    }  
}