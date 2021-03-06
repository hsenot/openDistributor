# openDistributor
Tools around electricity distribution


# API
## get.php

Description: retrieves the distributor information for a given address as JSON or JSONP

Endpoint: app.carbongis.com.au/openDistributor/get.php

Parameters:
- unit
- housenumber
- streetname
- streettype
- locality
- postcode
- callback (optional, for JSONP)

Example:

Request:

http://app.carbongis.com.au/openDistributor/get.php?housenumber=210&streetname=high&streettype=street&locality=northcote

Response:

```{"unit":"","housenumber":"210","streetname":"high","streettype":"street","locality":"northcote","postcode":"","distributor":"CITIPOWER"}```

Error:

```{"unit":"","housenumber":"210","streetname":"hogh","streettype":"street","locality":"northcote","postcode":"","distributor":"ERROR"}```


##readwrite.php

Description: The readwrite.php script:
- reads address records from a database, 
- calls the get API, 
- writes the response back to database.

Endpoint: app.carbongis.com.au/openDistributor/readwrite.php

Parameter:
- pfi: address PFI



# Victoria
The territory is divided into 5 DNSPs:
* Powercor
* Jemena
* CitiPower
* United Energy
* Ausnet Services (previously SP Ausnet)


The services used to reverse-engineer the DNSPs boundaries:
### 1st pass
- AGL distributor lookup [tool](http://www.agl.com.au/residential/help-and-support/emergencies-and-outages/electricity-distributor-lookup-tool) and its [web service](http://www.agl.com.au/svc/LookupServiceArea/GetDistributorResults?postcode=3070&suburb=NORTHCOTE&serviceType=EDA&_=1427416594601)
### 2nd pass
- Address geo-dataset provided by Victorian Government (data.gov.au)
- Address to DNSP service provided by Victorian government at this [page](http://www.energyandresources.vic.gov.au/energy/electricity/electricity-distributors): [web service](http://tools.energyandresources.vic.gov.au/energyapi/energytest1.php?housenumber=91&unit=&streetname=Kellett&streettype=Street&locality=Northcote&postcode=)


# Installation

- git clone git@github.com:hsenot/openDistributor.git
- cd openDistributor
- cp inc/credentials.template.php inc/credentials.php
- modify crendentials.php to appropriate DB settings

- Download from data.gov.au for a given area (for instance, a LGA) as shapefile in EPSG:4283
- Load the data in a database:

```shp2pgsql -s 4283 -D -g the_geom ADDRESS.shp vmadd | sudo -u postgres psql -d opendata```

- Creates a minified distributor table where the distributor is going to be recorded:

```create table vmadd_distributor as select pfi,ezi_add,the_geom,''::character varying as distributor from vmadd```

- Scriptable commands to get/write the distributor for a given address PFI:

```wget "http://<SERVER>/openDistributor/readwrite.php?pfi=1234567" -o out.log``` 

- TODO: a wrapper script to loop thru all PFI in a given area

- create a locality/postcode combination table

```CREATE TABLE locality_postcode (id serial NOT NULL,locality character varying,postcode character varying,address_count integer,distributors character varying,CONSTRAINT locality_postcode_pk PRIMARY KEY (id))```

- populate it with the CSV file

```copy locality_postcode (locality,postcode,address_count) from '/var/www/openDistributor/data/address_by_locality_postcode.csv' CSV DELIMITER ';'```

- create a postcode/distributor table

```CREATE TABLE postcode_distributor (id serial NOT NULL,postcode character varying,distributor character varying,CONSTRAINT postcode_distributor_pk PRIMARY KEY (id))```

- populate it with the CSV file

```COPY postcode_distributor (postcode,distributor) from '/var/www/openDistributor/postcode-distributors.csv' CSV HEADER DELIMITER ';'```

- create a derived table

```create table postcode_distributors as SELECT postcode,array_to_string(array_agg(distributor order by distributor ASC), ',') FROM postcode_distributor GROUP BY postcode```

