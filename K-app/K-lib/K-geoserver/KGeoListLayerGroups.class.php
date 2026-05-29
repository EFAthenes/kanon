<?php declare(strict_types=1);

class KGeoListLayerGroups extends HashMap
{

    public function addGroupLayer(string $name,KGeoLayerGroup $layer) : bool
    {
        return $this->put($name, $layer);
    }

    public function getGroupLayer(string $name) : ?KGeoLayerGroup
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