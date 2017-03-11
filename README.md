wikidata-geo-match
====
This service enables the analysis of Wikidata elements with at least one coordinate (P625 property) and the manual correlation in OpenStreetMap.

Installation
----
The folder needs to be in /srv/wikidata/, and Apache needs to have a virtualhost serving /srv/wikidata/html/.

Server
----
Steps:

1. Database: pgsql with postgis extensions, with wikidata tag in the hstore column `tags`
2. Run `0_create_wikidata_table.sql` to add tables to the osmosis database
2. Osmosis replication
3. Setup weekly cronjob for Wikidata dump download (`0_weekly_download.sh`), dump will be downloaded in /srv/wikidata/dump/
4. Setup cronjob to refresh the views after Osmosis applies the diff to database (`4_update_data.sh`)
5. Change the bounding box in scripts/stdin_to_JSON_polygon.php (defaults to Italy)
6. Run `composer update` both in html and scripts folder

Client
----
PHP application which queries the database and shows entries, classified by their status (no action, action taken, already mapped, not mappable). Allows to update such status.

License
------

This project (except the assets folder containing third party code), is released under WTFPL.
