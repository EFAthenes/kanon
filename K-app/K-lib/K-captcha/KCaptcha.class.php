<?php
/**
 * Description of KCarbonDate
 *
 * @author louis.mulot
 */
require_once __ROOT__.'/K-composer/vendor/autoload.php';  
use Gregwar\Captcha\PhraseBuilder;
use Gregwar\Captcha\CaptchaBuilder;

class KCaptcha
{
    private ?CaptchaBuilder $builder=null;
    private int $maxChar=5;
    private string $charsAllowed='';
    public function __construct(int $maxChar=5,string $charsAllowed='')
    {
        if($maxChar>0)
        {
            $this->maxChar=$maxChar;
        }
        if(strlen($charsAllowed))
        {
            $this->charsAllowed=$charsAllowed;
        }
        $this->initBuilder();
    }
    public function reset() : void
    {
        $this->initBuilder();  
    }
    private function initBuilder() : void
    {
        $phraseBuilder = new PhraseBuilder($this->maxChar);    
        if(!empty($this->charsAllowed))
        {
            $phraseBuilder = new PhraseBuilder($this->maxChar,$this->charsAllowed);           
        }
        $this->builder = new CaptchaBuilder(null,$phraseBuilder);  
        $this->builder->build();
    }
    public function saveImage(string $path_filename) : bool
    {
        $this->builder->save($path_filename);
    }    
    public function showImage()
    {
        $this->builder->output();
    }
    public function makeImageBase64() : string
    {
        return $this->builder->inline();
    }    
    public function getPassPhrase() : string
    {
        return $this->builder->getPhrase();
    }
    public function setPassPhraseInSession() : void
    {
        SessionMemory::getInstance()->putOrReplace("KCaptcha",$this->getPassPhrase());
    }
    public static function getLastPassPhraseInSession() : string
    {
        return SessionMemory::getInstance()->get("KCaptcha");
    }    
}