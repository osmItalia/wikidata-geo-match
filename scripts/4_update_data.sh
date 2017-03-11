#!/bin/bash

echo Process started

echo Update view 
psql -h localhost -U osm -d osmosis -c "REFRESH MATERIALIZED VIEW wiki_osm;"

echo Finished
