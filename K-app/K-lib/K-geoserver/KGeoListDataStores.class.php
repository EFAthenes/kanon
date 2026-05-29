<?php declare(strict_types=1);

class KGeoListDataStores extends HashMap
{

    public function addDataStore(string $name, KGeoDataStore $datastore): bool
    {
        return $this->put($name, $datastore);
    }

    public function getDataStore(string $name): ?KGeoDataStore
    {
        return $this->get($name);
    }

    public function toString(string $delimitor = ""): string
    {
        $print = "KGeoListDataStores::toString()" . $delimitor;
        foreach ($this->array as $key => $value)
        {
            $print .= $delimitor . "DataStore Name [" . $key . "] " . $delimitor . $value->toString($delimitor) . "";
        }
        return $print;
    }

}