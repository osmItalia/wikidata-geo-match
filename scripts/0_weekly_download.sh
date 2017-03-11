#!/bin/bash

cd /srv/wikidata/scripts

./1_download.sh > update_wiki.log

./2_process.sh >> update_wiki.log

./3_load_db.sh >> update_wiki.log

