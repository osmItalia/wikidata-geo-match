
DROP TABLE IF EXISTS wikidata ;
DROP TABLE IF EXISTS temp_wikidata ;
DROP TABLE IF EXISTS wikidata_coord ;
DROP TABLE IF EXISTS wikidata_stats ;

CREATE TABLE wikidata (
        id text NOT NULL,
        label text,
        lon decimal,
        lat decimal,
	status text,
        osm_id bigint,
        osm_type text
);


CREATE TABLE temp_wikidata (
        id text NOT NULL,
        label text,
        lon decimal,
        lat decimal
);

SELECT AddGeometryColumn ('wikidata','geom',4326,'POINT',2);
create index i_wikidata_id on wikidata(id);



CREATE TABLE wikidata_coord (
    id text NOT NULL,
	lon1 decimal,
	lat1 decimal,
	guid1 text,
	precision1 int,	
	lon2 decimal,
	lat2 decimal,
	guid2 text,
	precision2 int,	
	lon3 decimal,
	lat3 decimal,
	guid3 text,
	precision3 int	
);


CREATE TABLE wikidata_stats (
	data text,
	totosm bigint,
	count bigint,
	PRIMARY KEY(data)
);



create materialized view wiki_osm as 
	select 'node' as type, id, tags->'wikidata' AS wikidata_id  from nodes where tags ? 'wikidata'
   union
	select 'way' as type, id, tags->'wikidata' AS wikidata_id  from ways where tags ? 'wikidata'
   union
  	select 'relation' as type, id, tags->'wikidata' AS wikidata_id  from relations where tags ? 'wikidata';

create index i_wiki_osm_id on wiki_osm(wikidata_id);



