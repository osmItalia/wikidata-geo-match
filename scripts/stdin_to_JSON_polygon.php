<?php

/////////////////////
//
// JSON rows are read from STDIN and output is JSON of only objects
// inside the specified polygon
//
////////////////////

require('vendor/autoload.php');
use Wikibase\JsonDumpReader\JsonDumpFactory;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Polygon\Polygon;


$polygon = new Polygon(
          [
                [
                        45.630, //est Trieste
                        13.927
                ],
                [
                        45.560, //Istria
                        13.870
                ],
                [
                        45.33, //Largo di Venezia
                        12.97
                ],
                [       40.27, //Puglia
                        19
                ],
                [       36.31, //Sicilia
                        15.20
                ],
                [       36.31, //Sicilia
                        14
                ],
                [       35.26, //sud di Pantelleria
                        12.55
                ],
                [       36.80, //Ovest di Pantelleria
                        11.65
                ],
                [       38.84, //sud Sardegna
                        8
                ],
                [       41.32, //nordovest Sardegna
                        8
                ],
                [       41.32, //nordest Sardegna
                        9.70
                ],
                [       43, //ovest di Capraia
                        9.70
                ],
                [       43.76, //sud di Mentone
                        7.50
                ],
                [       44.22, //ovest Cuneo
                        6.90
                ],
                [       45.12, //ovest Bardonecchia
                        6.60
                ],
                [       46, //nordovest Aosta
                        6.75
                ],
                [       46.50, //
                        8.40
                ],
                [       46.90, //
                        10.46
                ],
                [       47.10, //Alto Adige
                        12.22
                ],
                [       46.50, //est Tarvisio
                        13.74
                ],
                [
                        45.630, //est Trieste
                        13.927
                ]
          ]
);

$polygon->setPrecision(5); // set the comparision precision



$factory = new JsonDumpFactory();

$t=0;

while($jsonLine = fgets(STDIN)){
 	$t++;
    if($t%10000 == 0) {
        $p = $t/1000;
        fwrite(STDERR, $p ." thousand rows\n");
    }


	// remove commas and \n from rows end
	$cleanLine = rtrim($jsonLine, "\n,");

	$obj = json_decode($cleanLine);

     /* check if P625 exists and find if at least one coordinate
        is on Earth (globe Q2) and is inside our bounding box
      */

	 if(isset($obj->claims->P625) ) {
		$count = count($obj->claims->P625);
		for($x=0; $x<$count; $x++) {
		    if( isset($obj->claims->P625[$x]->mainsnak->datavalue->value->globe) &&    
		     substr($obj->claims->P625[$x]->mainsnak->datavalue->value->globe,-2) == 'Q2') {
                $id = $obj->id;
                $lon = $obj->claims->P625[$x]->mainsnak->datavalue->value->longitude;
                $lat = $obj->claims->P625[$x]->mainsnak->datavalue->value->latitude;

                //Check if coordinates are inside the bounding box
                if (  $polygon->pointInPolygon(new Coordinate([$lat, $lon])) ) {
                    print $jsonLine."\n";
				    break;
				}

            }
        }
    }
}
?>

