#!/bin/bash


zgrep '"P625":' ../dump/latest-all.json.gz | php stdin_to_JSON_polygon.php | gzip > ../dump/filtered.json.gz

php wikidata_extract_table.php ../dump/filtered.json.gz > ../dump/filtered.csv

