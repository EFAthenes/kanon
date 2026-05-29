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
class PublicComponents extends KController
{
    /**
     * 
     * @var array<int,string>
     */
    private array $compNames=[];
    
    /**
     * 
     * @var array<int,mixed>
     */
//    private array $compDesc=[];   
//    
//    
    private MainComponent $div;
//    
    #[\Override]
    public function execute(): bool
    {

//        $this->addComponent(new TitleComponent("KApp"));
        $this->div=new MainComponent("list","");
        $require_cache=__ROOT__.'/K-cache/a_require_cache.php';
        $file=new KFile($require_cache);
        $array=$file->readFileToArray();
        if(count($array)==0)
        {
            $this->addComponent(new KAlertComponent("Error","No Components found for KApp!!",KAlertComponent::$TYPE_ERROR));
            //. no File
            return true;
        }
        foreach($array as $num =>$content)
        {
            $className=$this->getPHPClassName($content);
            if(!is_null($className))
            {
                //$this->addString($content."<br />");
                $this->printKComponent($className);
            }
        }
        
//        $this->addComponent(new TitleComponent("Application"));
//        $path_cache=KApp::getInstance()->makeKAppCachePath();
//        $file=new KFile($path_cache);
//        $array=$file->readFileToArray();
//        if(count($array)==0)
//        {
//            $this->addComponent(new KAlertComponent("Error","No Components found for Application!!",KAlertComponent::$TYPE_ERROR));
//            //. no File
//            return true;
//        }
        
        //KDebugger::getInstance()->dump($array);
        
//        foreach($array as $num =>$content)
//        {
//            $className=$this->getPHPClassName($content);
//            $this->printKComponent($className);
//        }
        
        
        if(count($this->compNames))
        {
            $row=new RowComponent();
            $row->addClassName("container-xl kanov-ct");
            
            $col1=new ColComponent("-md-3");
            $col1->addComponent(new KTemplateForTwigComponent("/view/public/components/aside.php",["compNames"=>$this->compNames]));
            
            $col2=new ColComponent("-md-9");
            $col2->addComponent($this->div);
            
            $row->addComponent($col1);
            $row->addComponent($col2);
            $this->addComponent($row);
        }
        
        
        return true;
    }
    
    private function getPHPClassName(string $s) : ?string
    {
        $array=rexplode("/",$s);
        $temp=$array[0];
        $array2=explode(".class.php", $temp);
        $className=$array2[0];//.".php";
        //$this->addString($className."<br />");
        if(class_exists($className)&& is_subclass_of($className,"KComponent"))
        {
            return $className;
        }
        return null;    
    }
    
    private function printKComponent(?string $className) : void
    {
        if(!is_null($className))
        {
            //$this->addString($className."<br />");
            $comp=$className::testMe($this);
            if(!is_null($comp))
            {
                $this->compNames[]=$className;
                //$this->addComponent(new TitleComponent($className,false,5));
                $reflection = new ReflectionClass($className);
                $constructor = $reflection->getConstructor();
                $params = $constructor->getParameters();
                //KDebugger::getInstance()->dump($params);
                $description='';
                $args='';
                foreach ($params as $param)
                {
                    $name = $param->getName();

                    // Parameter type (check if type exists)
                    $type = $param->getType();
                    /* @phpstan-ignore-next-line */

                        $typeName = $type;// ? $type->getName() : 'mixed';

                        // Check if the parameter is optional
                        $isOptional = $param->isOptional() ? ' (optional)' : '';

                        $description.= "Parameter: \${$name}, Type: {$typeName}{$isOptional}<br />";                        

                        //var_dump($param);
                        //
                        //$name = $param->getName();
                        //$type='';//(string) $comp->{$name};
                        //KDebugger::getInstance()->dump($param);
                        //$description .= $name."=>".$type . '<br>';
                        if(!empty($args))
                        {
                            $args.=', ';
                        }
                        $args.=$typeName." $".$name;
                }

                $usage = '$comp = new '.$className.'('.$args.');<br />';
                
                //$this->compDesc[]=[$usage,$description]
                $container=new DivClassComponent("bd-code-snippet");

                $container->addComponent(new KTemplateForTwigComponent("/view/public/components/title.php",["name"=>$className]));
                
                
                $container->addComponent(
                        new KCodeComponent(
                                codeType:KCodeComponent::PHP_LANG,
                                codeContent:$usage));
                $desc=new DivClassComponent("");
                $desc->addComponent(new HTMLComponent($description));
                $container->addComponent($desc);

                $container->addComponent($comp);
                
                $this->div->addComponent($container);
                $this->div->addComponent(new HrComponent());
            }
        }
    }
}