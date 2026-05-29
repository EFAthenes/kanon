<?php
require_once __ROOT__.'/K-composer/vendor/autoload.php';
class KRdf
{
    public function __construct()
    {
    }
    /**
     * 
     * @param string $url
     * @return array<mixed,mixed>|null
     */
    public function readRdf(string $url): ?array
    {
        $foaf = new \EasyRdf\Graph($url);
        if($foaf->load()>0)
        {
            $res=$foaf->toRdfPhp();
            if(count($res))
            {
                return $res;
            }
        }
        return null;
    }
}
