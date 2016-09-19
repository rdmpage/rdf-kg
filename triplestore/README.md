# triplestore

## Simple triplestore based on arc2

## Jena-Fuseki

Run in docker container, locally http://192.168.99.100:32768


## Example queries

```
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>

SELECT * WHERE {
?id <http://schema.org/name> ?name
} LIMIT 10
```


### Find funders of a GBIF dataset via publication linked to dataset by Plaza

```
SELECT DISTINCT ?data ?work ?fundername ?awardname

WHERE 
{ 
   ?data <http://purl.org/dc/terms/identifier> ?doi .
   ?data <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://schema.org/Dataset> .
   ?work <http://purl.org/dc/terms/identifier> ?doi .
   ?work <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://schema.org/ScholarlyArticle> .
   ?work <http://schema.org/funder> ?award .
   ?award <http://schema.org/funder> ?funder .
   ?funder <http://schema.org/name> ?fundername .

OPTIONAL
{
 ?award <http://schema.org/roleName> ?awardname . 
}
}
```


### Discover identifiers for funders

paper DOI 10.1111/syen.12181 has grant “DEB-1354996” from “NSF” but no DOI for NSF (this paper has data in GBIF)

paper DOI 10.1111/1755-0998.12328 has grant “DEB-1354996” from  “National Science Foundation” DOI 10.13039/100000001 (this paper is “Target enrichment of ultra conserved elements from arthropods provides a genomic perspective on relationships among Hymenoptera”

So, we could query using grant name and discover that NSF = National Science Foundation 

```
SELECT DISTINCT *

WHERE 
{ 
   ?award <http://schema.org/roleName> “DEB-1354996” .
   ?award <http://schema.org/funder> ?funder . 
   ?funder <http://schema.org/name> ?name . 
}
```

