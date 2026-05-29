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
class KCli
{
    /**
     * 
     * @var array<string,string>|null
     */
    private ?array $foreground_color=null;
    /**
     * 
     * @var array<string,string>|null
     */
    private ?array $background_color=null;
    
    private string $foreground_selected='white';
    private ?string $background_selected=null;
    private bool $outputToBuffer=false;
    /**
     * 
     * @var array<int,string>
     */
    private array $buffer=[];
    
    public static string $F_BLACK='black';
    public static string $F_DARK_GRAY='dark gray';
    public static string $F_BLUE='blue';
    public static string $F_LIGHT_BLUE='light blue';
    public static string $F_GREEN='green';
    public static string $F_LIGHT_GREEN='light green';
    public static string $F_CYAN='cyan';
    public static string $F_LIGHT_CYAN='light cyan';
    public static string $F_RED='red';
    public static string $F_LIGHT_RED='light red';
    public static string $F_PURPLE='purple';
    public static string $F_LIGHT_PURPLE='light purple';
    public static string $F_BROWN='brown';
    public static string $F_YELLOW='yellow';
    public static string $F_LIGHT_GRAY='light gray';
    public static string $F_WHITE='white';
    
    public static string $B_BLACK='black';
    public static string $B_RED='red';
    public static string $B_GREEN='green';
    public static string $B_YELLOW='yellow';
    public static string $B_BLUE='blue';
    public static string $B_MAGENTA='magenta';
    public static string $B_CYAN='cyan';
    public static string $B_LIGHT_GRAY='light gray';
    
    public static string $BR_WEB="<br/>";
    public static string $BR_CLI="\n";
    
    private static string $BR_VALUE="";
    
    public function __construct()
    {
        
        $this->foreground_color = array(
            KCli::$F_BLACK => '0;30',
            KCli::$F_DARK_GRAY => '1;30',
            KCli::$F_BLUE => '0;34',
            KCli::$F_LIGHT_BLUE => '1;34',
            KCli::$F_GREEN => '0;32',
            KCli::$F_LIGHT_GREEN => '1;32',
            KCli::$F_CYAN => '0;36',
            KCli::$F_LIGHT_CYAN => '1;36',
            KCli::$F_RED => '0;31',
            KCli::$F_LIGHT_RED => '1;31',
            KCli::$F_PURPLE => '0;35',
            KCli::$F_LIGHT_PURPLE => '1;35',
            KCli::$F_BROWN => '0;33',
            KCli::$F_YELLOW => '1;33',
            KCli::$F_LIGHT_GRAY => '0;37',
            KCli::$F_WHITE => '1;37');
        
        $this->background_color = array(
            KCli::$B_BLACK => '40',
            KCli::$B_RED => '41',
            KCli::$B_GREEN => '42',
            KCli::$B_YELLOW => '43',
            KCli::$B_BLUE => '44',
            KCli::$B_MAGENTA => '45',
            KCli::$B_CYAN => '46',
            KCli::$B_LIGHT_GRAY => '47');  
        
        self::$BR_VALUE=self::$BR_CLI;
        
    }
    
    public function __destruct()
    {
    }
    
    public function outputToBuffer(bool $outputToBuffer) : void
    {
        $this->outputToBuffer=$outputToBuffer;
    }
    
    public static function br(int $value=1) : string
    {
        if($value>1)
        {
            $string="";
            for($i=0; $i<$value ; $i++)
            {
                $string.=self::$BR_VALUE;
            }
            return $string;
        }
        return self::$BR_VALUE;
    }
    
    public function setBrWeb() : void
    {
        self::$BR_VALUE=self::$BR_CLI;
    }
    public function setBrCLI() : void
    {
        self::$BR_VALUE=self::$BR_CLI;
    }
    
    public function setColors(string $foreground,?string $background=null) : void
    {
        $this->setForeground($foreground);
        if(!is_null($background))
        {
            $this->setBackground($background);
        }
    }
    
    public function setForeground(string $color) : void
    {
        if(array_key_exists($color,$this->foreground_color))
        {
            $this->foreground_selected=$color;
        }        
    }
    public function setBackground(string $color) : void
    {
        if($color==null)
        {
            $this->background_selected=null;
        }
        else if(array_key_exists($color,$this->background_color))
        {
            $this->background_selected=$color;
        }
    }  
    
    public function printStringCustom(string $string,string $foreground,?string $background=null) : void
    {
        if(!array_key_exists($foreground,$this->background_color))
        {
            $foreground='white';
        }
        if($background!=null&&!in_array($background,$this->background_color))
        {
            $background=null;
        }
        if($this->outputToBuffer)
        {
            $this->buffer[]=$string;
        }
        else
        {
            echo $this->makeString($string,$foreground,$background);
        }
    }
    public function printLineCustom(string $string,string $foreground,?string $background=null) : void
    {
        if(!in_array($foreground,$this->background_color))
        {
            $foreground='white';
        }
        if($background!=null&&!array_key_exists($background,$this->background_color))
        {
            $background=null;
        } 
        if($this->outputToBuffer)
        {
            $this->buffer[]=$string."\n";
        }
        else
        {        
            echo $this->makeString($string,$foreground,$background)."\n";
        }
    }    
    
    public function printString(string $string) : void
    {
        if($this->outputToBuffer)
        {
            $this->buffer[]=$string;
        }
        else
        {
            echo $this->makeString($string,$this->foreground_selected,$this->background_selected);
        }
    }
    public function printLine(string $string) : void
    {
        if($this->outputToBuffer)
        {
            $this->buffer[]=$string."\n";
        }
        else
        {        
            echo $this->makeString($string,$this->foreground_selected,$this->background_selected)."\n";
        }
    }
    
    public function launchPrompt(bool $return_only_line=true) : string
    {
        $fin = fopen ("php://stdin","r");
        $response="".fgets($fin);         
        if($return_only_line)
        {
            $response=trim($response);
        }
        return $response;
    }
        
    /**
     * 
     * @param array<mixed,mixed>|null $array
     * @param int|null $count_max
     * @param KCli|null $cli
     * @param string $message
     * @return bool|null
     */
    public function waitPromptConfirmation(?array$array=null,?int $count_max=null, ?KCli $cli=null,string $message="") : ?bool
    {
        if(is_null($array) || !array_key_exists("yes" , $array) ||!array_key_exists("no" , $array) )
        {
            $array=["yes" => "y","no" => "n"];
        } 
        if(is_null($count_max))
        {
            $count_max=3;
        }
        if(empty($message))
        {
            $message="command not recognized!";
        }
        $count=1;
        $wait=true;
        while($wait)
        {
            $response=$this->launchPrompt();
            //echo $response."//".$array["yes"]."//".$array["no"];
            if(strcmp($response,$array["yes"])==0)
            {
                return true;
            }
            else if(strcmp($response,$array["no"])==0)
            {
                return false;
            }
            else
            {
                if(!is_null($cli))
                {
                    $cli->printLine($message);
                }
                $count++;
            }
            
            if($count>$count_max)
            {
                $wait=false;
            }
        }
        echo $response;
        return null;
    }    
    
    private function makeString(string $string,string $foreground_selected,?string $background_selected) : string
    {
        $stringRender= "\033[{$this->foreground_color[$foreground_selected]}m";
        if($this->background_selected!=null)
        {
            $stringRender.="\033[{$this->background_color[$background_selected]}m";
        }
        $stringRender.=$string."\033[0m";    
        return $stringRender;
    }
     
    public function possibleCommands() : void
    {
        $this->printString("Possible commands : ".KCli::br());   
        $this->printString("create_project => ".KCli::br());   
        $this->printString("set_project => ".KCli::br()); 
        $this->printString("make_db => ".KCli::br()); 
        $this->printString("check => ".KCli::br());    
        $this->printString("build =>  ".KCli::br());    
        $this->printString("add_columns => ".KCli::br());
        $this->printString("export_data_json => ".KCli::br());
        $this->printString("import_data_json => ".KCli::br());
        $this->printString("export_model_json => ".KCli::br());
        $this->printString("import_model_json => ".KCli::br());
        $this->printString("create_model_db => ".KCli::br());
        $this->printString("initialize_kapp_tables => ".KCli::br());
        $this->printString("make_indexes_klink => ".KCli::br());
        $this->printString("clean_cache => ".KCli::br());
        $this->printString("test_code => ".KCli::br());
        
    }
    
    public function possibleCommandsUsersAndGroups() : void
    {
        $this->printString("Possible commands : ".KCli::br());
        $this->printString("add_user => ".KCli::br());
        $this->printString("change_user_psswd => ".KCli::br());
        $this->printString("add_group =>  ".KCli::br());
        $this->printString("list_users =>  ".KCli::br());
        $this->printString("list_groups =>  ".KCli::br());
        $this->printString("test_email =>  ".KCli::br());        
    }    
    
    public function strToNbChar(?string $string,int $numbers=20) : string
    {
        if(!empty($string))
        {
            $count=strlen($string);   

            if($count>$numbers)
            {
                return substr($string,$numbers);
            }
            else
            {
                for($i=0; $i<($numbers-$count); $i++)
                {
                    $string.=" ";
                }
            }
            return $string;  
        }
        else
        {
            $string="";
            for($i=0; $i<($numbers); $i++)
            {
                $string.=" ";
            } 
            return $string; 
        }
    }
    
    /**
     * 
     * @return array<int,string>
     */
    public function getBuffer() : array
    {
        return $this->buffer;
    }
    public function getBufferToString() : string
    {
        $string="";
        foreach ($this->buffer as $line)
        {
            $string.=$line;
        }
        return $string;
    }     
}