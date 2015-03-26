# openDistributor
A set of scripts to extract electricity distributor information


# Victoria
The territory is divided into 5 DNSPs:
* Powercor
* Jemena
* CitiPower
* United Energy
* Ausnet Services (previously SP Ausnet)

The services used to reverse-engineer the DNSPs boundaries:
- Address geo-dataset provided by Victorian Government (data.gov.au)

Download from data.gov.au for a given area (for instance, a LGA)
Load the data in a database:

```shp2pgsql -s 4283 -D -g the_geom ADDRESS.shp vmadd | sudo -u postgres psql -d opendata```

Creates a minified distributor table:

```create table vmadd_distributor as select pfi,ezi_add,the_geom,''::character varying as distributor from vmadd```


- Address to DNSP service provided by Victorian government at this [page](http://www.energyandresources.vic.gov.au/energy/electricity/electricity-distributors): [web service](http://tools.energyandresources.vic.gov.au/energyapi/energytest1.php?housenumber=91&unit=&streetname=Kellett&streettype=Street&locality=Northcote&postcode=)
