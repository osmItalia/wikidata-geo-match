#!/bin/bash

echo Process started

echo Update view and table
psql -h localhost -U osm -d osmosis -f ./sql/1_update_geom_wikidata.sql

echo Finished
