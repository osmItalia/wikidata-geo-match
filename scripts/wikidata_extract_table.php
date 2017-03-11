<?php

/////////////////////
//
// output CSV with objects having more than a coordinate
//
////////////////////


// dump file as first argument

if($argv[1] )
	$nomefile = $argv[1];
else
	die("\nNo file specified\n\n");



require('vendor/autoload.php');
use Wikibase\JsonDumpReader\JsonDumpFactory;

$factory = new JsonDumpFactory();

$dumpReader = $factory->newGzDumpReader( $nomefile );
$dumpIterator = $factory->newStringDumpIterator( $dumpReader );

foreach ( $dumpIterator as $jsonLine ) {

	$obj = json_decode($jsonLine);

        if(isset($obj->claims->P625) ) {
                print $obj->id;

                $labels = (array)$obj->labels;


                if(isset($labels['it'])) {
                   print ";".$labels['it']->value;
                } elseif (isset($labels['en'])) {
                   print ";[en]".$labels['en']->value;
                } elseif(isset($labels['sh'])) {
                   print ";[sh]".$labels['sh']->value;
                } elseif (count($labels) > 0) {
                    $first = array_shift($labels);
                    print ";[".$first->language."]".$first->value;
                } else {
                    print ";[nolabel]";
		        }

			    $validCoord = null;
                for($x=0; $x<count($obj->claims->P625); $x++) {
				    if ($validCoord === null or $obj->claims->P625[$x]->mainsnak->datavalue->value->precision < $validCoord->mainsnak->datavalue->value->precision) {
					    $validCoord = $obj->claims->P625[$x];
				    }
                }
                print ";". $validCoord->mainsnak->datavalue->value->longitude;
                print ";". $validCoord->mainsnak->datavalue->value->latitude;
			    print "\n";
         }
}
?>
