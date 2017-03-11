<html>
<head>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.0.3/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.0.3/dist/leaflet.js"></script>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>

<link rel="stylesheet" href="assets/leaflet-beautify-marker-icon.css">
<script src="assets/leaflet-beautify-marker-icon.js"></script>
<script src="assets/leaflet-beautify-marker.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-hash/0.2.1/leaflet-hash.min.js"></script>

<link rel="stylesheet" href="assets/Leaflet.EditInOSM.css" />
<script src="assets/Leaflet.EditInOSM.js"></script>
<style>
.btn {margin:5px;padding:5px; border:1px solid black; border-radius:5px;}
</style>
</head>
<body>
<div id="map" style="position:absolute;top:0px;bottom:0px;right:0px;left:0px;"></div>

<div id="loading" style="position:fixed;z-index:99999;top:250px;left:250px"></div>

<div id="panel" style="position:fixed;z-index:99999;top:50px;right:50px">

<button onclick="getData()">Get data</button>
</div>
</body>
<script>
var map = L.map('map', {
	    editInOSMControl: true,
            editInOSMControlOptions: {
                position: "bottomright"
            }
}).setView([44.40, 8.99], 13);

var hash = new L.Hash(map);

L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

function getData() {

  document.getElementById('loading').innerHTML = '<h1>Loading... please wait</h1>';


  var bbox = map.getBounds();
  var bboxargs = {
	lon_left: bbox._northEast.lng,
	lat_left: bbox._northEast.lat,
	lon_right: bbox._southWest.lng,
	lat_right: bbox._southWest.lat
	};

  $.get('/wikidata/get/bbox',  bboxargs , function (res) {
	$.each(res, function(i, v) {
      		var color = '#8A90B4';

                if (v['status'] == 'not_mappable') {
                        color = 'red';
		}

                if (v['status'] == 'done') {
                        color = 'yellow';
                }

                if(v['osm_id'] !== null) {
                        color = 'green';
                }

		options = { iconShape: 'circle-dot', borderWidth: 10, borderColor: color};
		var marker = L.marker([v['lat'], v['lon']], { icon: L.BeautifyIcon.icon(options)}).addTo(map);

		var popup = "<b>"+v['label']+"</b><br/><a href='https://www.wikidata.org/wiki/"+v['id']+"'>Wikidata</a>";

		if(v['osm_id'] !== null) {
		  popup += '<br/><a href="https://www.openstreetmap.org/'+v['osm_type']+'/'+v['osm_id']+'">OSM</a>';
		} else {
		  popup += '<br/>wikidata='+v['id'];
		}
		if (v['status'] == 'not_mappable') {
                  popup += "<br/><button onclick='mappable(\""+v['id']+"\")'>Segna come mappabile</button>";
		}

		if (v['status'] != 'not_mappable' && v['status'] != 'done' && v['osm_id'] === null) {
			popup += "<br/><button onclick='notMappable(\""+v['id']+"\")'>Segna come non mappabile</button>";
			popup += "<br/><button onclick='doDone(\""+v['id']+"\")'>Segna come fatto (temporaneo)</button>";
		}


		marker.bindPopup(popup);

	});
		document.getElementById('loading').innerHTML = '';
  });
}

function notMappable(id) {
	$.get('/wikidata/set/notMappable',  {id: id} , function (res) {
	});
}

function mappable(id) {
    $.get('/wikidata/set/mappable',  {id: id} , function (res) {
    });
}

function doDone(id) {
    $.get('/wikidata/set/done',  {id: id} , function (res) {
    });
}


map.on('popupopen', function(e) {
  var marker = e.popup._source;
});
</script>
</html>
