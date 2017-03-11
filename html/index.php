 <?php
header('Content-Type: text/html; charset=utf-8');

require('vendor/autoload.php');

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection(
    [
    'driver'    => 'pgsql',
    'host'      => 'localhost',
    'database'  => 'osmosis',
    'username'  => 'osm',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    ]
);

$capsule->setAsGlobal();

$capsule->bootEloquent();

Flight::route(
    '/',
    function () {
        Flight::render('map.php', [ ]);
    }
);

Flight::route(
    '/get/one',
    function () {
	$one = Capsule::table('wikidata')
		->first();
        Flight::json($one);
    }
);

Flight::route(
    '/get/bbox',
    function () {
	    $latmin = min($_GET['lat_right'], $_GET['lat_left']);
	    $latmax = max($_GET['lat_right'], $_GET['lat_left']);
        $lonmin = min($_GET['lon_right'], $_GET['lon_left']);
        $lonmax = max($_GET['lon_right'], $_GET['lon_left']);

        $one = Capsule::table('wikidata')
		->leftJoin('wiki_osm', 'wikidata.id', '=', 'wiki_osm.wikidata_id')
		->whereRaw('(geom && ST_MakeEnvelope('. $lonmin .','. $latmin .','. $lonmax .','. $latmax .', 4326))');

	    $one = $one
		->select('wikidata.*', 'wiki_osm.id as osm_id', 'wiki_osm.type as osm_type')
        ->get();
        Flight::json($one);
    }
);

Flight::route(
    '/set/notMappable',
    function () {
	$one = Capsule::table('wikidata')
	->where('id', $_GET['id'])
	->update(['status' => 'not_mappable']);
    }
);

Flight::route(
    '/set/mappable',
    function () {
        $one = Capsule::table('wikidata')
        ->where('id', $_GET['id'])
        ->update(['status' => '']);
    }
);

Flight::route(
    '/set/done',
    function () {
        $one = Capsule::table('wikidata')
        ->where('id', $_GET['id'])
        ->update(['status' => 'done']);
    }
);


Flight::start();
