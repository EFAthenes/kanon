<?php
/**
 * Description of ArrayListKobjects
 *
 * @author Mulot Louis
 
class ArrayListKobjects extends ArrayList
{
    public function get(int $i) : ?KObject
    {
        return parent::get($i);
    }
    
    public function getOrNull(int $i) : ?KObject
    {
        return parent::getOrNull($i);
    }

    public function replace(int $i, KObject $object)
    {
        return parent::replace($i,$object);
    }

    public function add(KObject $object) : void
    {
        parent::add($object);
    }

    public function addList(ArrayListKobjects $list)
    {
        if ($list == null)
        {
            return false;
        }
        for ($i = 0; $i < $list->getSize(); $i++)
        {
            $this->add($list->get($i));
        }
        return parent::replace;
    }

    public function remove($i)
    {
        if (!array_key_exists($i, $this->array))
        {
            return false;
        }
        unset($this->array[$i]);
        $this->array = array_values($this->array);
    }

    public function removeObject($object) : bool
    {
        $status=false;
        $i=0;
        foreach ($this->array as $item)
        {
            if($item===$object)
            {
                unset($this->array[$i]);
                $status=true;
                $this->array = array_values($this->array); 
                break;
            }
            $i++;
        }
        return $status;
    }

    public function getLast()
    {
        if (count($this->array) > 0)
        {
            return end($this->array);
        }
        return null;
    }

    public function toArrayOfString() : array
    {
        $array = array();
        for ($i = 0; $i < count($this->array); $i++)
        {
            $array[] = "" . $this->array[$i];
        }
        return $array;
    }

    public function toString(string $delimitor="") : string
    {
        if ($delimitor == "")
        {
            $delimitor = $this->delimitor;
        }
        $html = "";

        //for($i=0; $i< count($this->array); $i++)
        $i = 0;
        foreach ($this->array as $value)
        {
            if ($value instanceof KObject || $value instanceof KField)
            {
                $html .= $i . "-" . $value->toString($delimitor) . $delimitor;
            }
            else if (is_object($value) && method_exists($value, 'toString'))
            {
                $html .= $i . "-" . $value->toString($delimitor) . $delimitor;
            }
            else if (is_object($value))
            {
                $html .= $i . "-" . get_class($value) . $delimitor;
            }
            else if (!is_object($value))
            {
                $html .= $i . "-" . gettype($value) . "=>" . ($value) . $delimitor;
            }
            else
            {
                $html .= $i . "-" . gettype($value) . $delimitor;
            }
            $i++;
        }

        return $html;
    }
}
*/