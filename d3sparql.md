# d3sparql

Could use d3sparql to visualise data http://biohackathon.org/d3sparql/

http://rdmpage-jena-fuseki-v.sloppy.zone/dataone/query

## Tree

```
SELECT ?root_name ?parent_name ?child_name
WHERE
{  
      VALUES ?root_name { “Hypsibiidae” }
       ?root <http://rs.tdwg.org/dwc/terms/canonicalName> ?root_name .
       ?child <http://rs.tdwg.org/dwc/terms/parentNameUsageID>+ ?root .
       ?child <http://rs.tdwg.org/dwc/terms/parentNameUsageID> ?parent .
       ?child <http://rs.tdwg.org/dwc/terms/canonicalName> ?child_name .
       ?parent <http://rs.tdwg.org/dwc/terms/canonicalName> ?parent_name .
}
```


## Map

Generate a simple map from occurrences.

http://rdmpage-jena-fuseki-v.sloppy.zone/dataone/query

```
SELECT ?item ?lat ?lng
WHERE {
  ?item <http://rs.tdwg.org/dwc/terms/locationID> ?loc .
  ?loc <http://rs.tdwg.org/dwc/terms/decimalLatitude> ?lat .
  ?loc <http://rs.tdwg.org/dwc/terms/decimalLongitude> ?lng .
}
```

## Hash table

List values for a record

```
SELECT *
WHERE {
<http://www.gbif.org/occurrence/624171312> <http://schema.org/name> ?name .
<http://www.gbif.org/occurrence/624171312> <http://rs.tdwg.org/dwc/terms/institutionCode> ?institutionCode .
<http://www.gbif.org/occurrence/624171312> <http://rs.tdwg.org/dwc/terms/catalogNumber> ?catalogNumber .
<http://www.gbif.org/occurrence/624171312> <http://rs.tdwg.org/dwc/terms/datasetID> ?datasetID .
}
```

## Named graph query local data from Uniprot

```
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX up: <http://purl.uniprot.org/core/>
SELECT ?root_name ?parent_name ?child_name
FROM <http://sparql.uniprot.org/taxonomy>
WHERE
{
  VALUES ?root_name { “Apodidae” }
  ?root up:scientificName ?root_name .
  ?child rdfs:subClassOf+ ?root .
  ?child rdfs:subClassOf ?parent .
  ?child up:scientificName ?child_name .
  ?parent up:scientificName ?parent_name .
}
```

