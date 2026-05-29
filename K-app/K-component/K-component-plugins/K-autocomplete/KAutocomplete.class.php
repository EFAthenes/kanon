<?php
/**
 * Description of KAutocomplete
 *
 * @author Mulot Louis
 */
class KAutocomplete extends InputStringComponent
{
    private ?KURL $urlAction=null;
    private int $minLength=3;
    private int $limitResult=8;
    private bool $highlight=true;
    private string $queryTag="QUERY";
    private string $queryParam="words";
    private string $actionJS="";
    private string $actionJS_selected="";
    private string $actionJS_closed="";
    private string $replaceInJS="";
    private string $var_engine="";
    
    function __construct(?string $inputValue,string $inputName, string $urlAction,?string $label=null,?string $placeholder=null, bool $require = false,bool $readOnly=false,?int $colLabel=1,?int $colInput=11,?array $class_names = [])
    {
        parent::__construct(
                $inputValue,
                $inputName,
                $label,
                $placeholder,
                $require,
                $readOnly,
                $colLabel,
                $colInput,
                $class_names
                );  
        $this->urlAction=new KURL($urlAction);
        $this->urlAction->addOrReplace($this->queryParam,$this->queryTag);
        
        $this->addClass_Name("typeahead");
        
        $layout=KApp::getInstance()->getLayout();
        $layout->addJsFileToBuffer(__DIR__."/js/typeahead.bundle.min.js");
        $layout->addCSSFileToBuffer(__DIR__."/css/typeahead.css");
        $this->var_engine="engine_".KRandom::makeRandomString();

    } 
    
    public function getMinLength(): int
    {
        return $this->minLength;
    }

    public function getLimitResult(): int
    {
        return $this->limitResult;
    }

    public function getHighlight(): bool
    {
        return $this->highlight;
    }

    public function getQueryTag(): string
    {
        return $this->queryTag;
    }

    public function getQueryParam(): string
    {
        return $this->queryParam;
    }

    public function setMinLength(int $minLength): void
    {
        $this->minLength = $minLength;
    }

    public function setLimitResult(int $limitResult): void
    {
        $this->limitResult = $limitResult;
    }

    public function setHighlight(bool $highlight): void
    {
        $this->highlight = $highlight;
    }

    public function setQueryTag(string $queryTag): void
    {
        $this->queryTag = $queryTag;
        $this->urlAction->addOrReplace($this->queryParam, $this->queryTag);
    }

    public function setQueryParam(string $queryParam): void
    {
        $this->urlAction->removeArg($this->queryParam);
        $this->queryParam = $queryParam;
        $this->urlAction->addOrReplace($this->queryParam, $this->queryTag);
    }
    
    public function setActionOnSelect(string $actionJS) : void
    {
        $this->actionJS=$actionJS;
    }
    
    public function setActionOnSelected(string $actionJS) : void
    {
        $this->actionJS_selected=$actionJS;
    } 
    
    public function setActionOnClosed(string $actionJS) : void
    {
        $this->actionJS_closed=$actionJS;
    }      

    public function setReplaceFunctionInURL(string $replaceInJS) : void
    {
        $this->replaceInJS=$replaceInJS;
    }


    private function makeScript() : string
    {
        $script = '
<script>
var '.$this->var_engine.';
$(document).ready(function () 
{
    '.$this->var_engine.' = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: "'.$this->urlAction->printURLWithoutAmp().'",
            wildcard: "'.$this->queryTag.'",
';
        if($this->replaceInJS!="")
        {
            $script.='
            replace:function (url, query)
            {
                '.$this->replaceInJS.'
            }
';
        }
        $script.='
        },
    });
    
    $("#'.$this->getInputName().'").typeahead(
        {
            highlight: '.convertBoolToString($this->highlight).',
            hint: false,
            minLength: '.$this->minLength.',
        },
        {
            name: "'.$this->var_engine.'",
            limit: '.$this->limitResult.',
            source: '.$this->var_engine.'
        }
    );
';
        if(!empty($this->actionJS))
        {
            $script.= '
    $("#'.$this->getInputName().'").bind(\'typeahead:select\', function(ev, suggestion) {
        '.$this->actionJS.'(suggestion);
    });
';
        }
        if(!empty($this->actionJS_selected))
        {
        $script.= '
    $("#'.$this->getInputName().'").on(\'typeahead:selected\', function(evt, item) {
         '.$this->actionJS_selected.'(item);
    })  
';
        }
        if(!empty($this->actionJS_closed))
        {
        $script.= '
    $("#'.$this->getInputName().'").on(\'typeahead:closed \', function(evt, item) {
         '.$this->actionJS_closed.'(item);
    })  
';
        }     
        
        $script.= '
});
</script>
';        
        return $script;        
    }
    
    public function draw(): string
    {
        return parent::draw().$this->makeScript();
    }
    
    public function getJsRefreshString() : string
    {
        $js='
    $("#'.$this->getInputName().'").typeahead("destroy").typeahead({
        highlight: '.convertBoolToString($this->highlight).',
        hint: false,
        minLength: '.$this->minLength.',
    }, 
    {
        name: "'.$this->var_engine.'",
        limit: '.$this->limitResult.',
        source: '.$this->var_engine.'
    });
';
        return $js;
    }
    
//    #[\Override]
//    public static function testMe() : ?static
//    {
//        //KDebugger::getInstance()->dump("louis");
//        $class=new static("autocomplete","autocomplete_name","#");
//        return $class;
//    }    
}
