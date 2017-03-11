

truncate table temp_wikidata;

\copy temp_wikidata(id,label,lon,lat) FROM '/srv/wikidata/dump/filtered.csv' DELIMITER ';' CSV



with temp_wikidata(id,label,lon,lat) AS (
	select * from temp_wikidata),
	updated AS (
	update wikidata set label=t.label, lon=t.lon, lat=t.lat 
		from temp_wikidata t where wikidata.id=t.id
		returning wikidata.id
	)
	insert into wikidata
	select *
	from temp_wikidata v
	where not exists (
		select 1
		from updated u
		where u.id = v.id
	);

delete from wikidata where not exists (select 1 from temp_wikidata where wikidata.id=temp_wikidata.id);


truncate table temp_wikidata;


REFRESH MATERIALIZED VIEW wiki_osm;

update wikidata set geom = ST_SetSRID(ST_MakePoint(lon,lat),4326);


insert into wikidata_stats  
	SELECT current_date as data, COUNT(DISTINCT wiki_osm.id) AS totosm,
	COUNT(DISTINCT wikidata.id) FROM wiki_osm RIGHT JOIN wikidata 
		ON wiki_osm.wikidata_id = wikidata.id;

