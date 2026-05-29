<?php declare(strict_types=1);

class KGeoListLayers extends HashMap
{

    public function addLayer(string $name,KGeoLayer $layer) : bool
    {
        return $this->put($name, $layer);
    }

    public function getLayer(string $name) : ?KGeoLayer
    {
        return $this->get($name);
    }

    public function toString(string $delimitor = "") : string
    {
        $print = "KGeoListLayers::toString()" . $delimitor;
        foreach ($this->array as $key => $value)
        {
            $print .= $delimitor . "Layer Name [" .$key . "] ".$delimitor . $value->toString($delimitor) . "";
        }
        return $print;
    }

}