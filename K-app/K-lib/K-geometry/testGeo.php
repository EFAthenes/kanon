<?php

include_once('geoPHP/geoPHP.inc');


/*
  Min X Y Z:                   611901.811 4137317.302 6.670
  Max X Y Z:                   612059.763 4137500.220 32.226
 */

// Polygon WKT example
// 612059.763 4137500.220, 611901.811 4137317.302
//$polygon = geoPHP::load('POLYGON((612059.763 4137500.220, 612059.763 4137317.302 , 611901.811 4137317.302, 611901.811 4137500.220))','wkt');
//$polygon = geoPHP::load('POLYGON((611901.811 4137317.302 6.670, 611901.811 4137500.220 6.670 , 612059.763 4137500.220 32.226, 612059.763 4137317.302 32.226))','wkt');

//$polygon = geoPHP::load('POLYGON((611901.811 4137317.302, 612059.763 4137317.302, 612059.763 4137500.220, 611901.811 4137500.220))','wkt');

$polygon = geoPHP::load('POLYGON((25.265609692926954 37.37843040800553, 25.267393347227266 37.37841130510286, 25.26742108919725 37.38005968885204, 25.265637395879946 37.3800787928869))','wkt');

$area = $polygon->getArea();
$centroid = $polygon->getCentroid();
$centX = $centroid->getX();
$centY = $centroid->getY();

print "This polygon has an area of ".$area." and a centroid with X=".$centX." and Y=".$centY;

print "<br />";

$polygon->setSRID("2100");

print $polygon->out("json");

print "<br />";
//print $polygon->out("google_geocode");

$polygon->setSRID("4326");

print $polygon->out("json");

print "<br />";
print $polygon->out("geojson");


print "<br />";
print $polygon->out("kml");

print "<br />";
print $polygon->out("wkt");

if($polygon->geos())
{
    print "<br />";
    print "TRUE";
}
else
{
    print "<br />";
    print "FALSE";
}



// 25.26651536173927, 37.379244955939356, 19

// 25.266533  