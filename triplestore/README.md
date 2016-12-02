# triplestore

## Simple triplestore based on arc2

## Jena-Fuseki

Run in docker container, locally http://192.168.99.100:32768

## Loading data

In ARC
```
LOAD <uri>
```

e.g load CrossRef triples from CouchDB view:

```
LOAD <http://127.0.0.1:5984/rdf_kg/_design/crossref/_list/n-triples/nt>
```

Load Mendeley group data 
```
LOAD <http://127.0.0.1:5984/rdf_kg/_design/mendeley_group/_list/n-triples/nt>
```



## Example queries

```
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>

SELECT * WHERE {
?id <http://schema.org/name> ?name
} LIMIT 10
```

### List funders and grants

```
SELECT DISTINCT ?name ?agency

WHERE 
{ 
 ?award  <http://schema.org/roleName> ?name .
 ?award <http://schema.org/funder> ?funder .
 ?funder  <http://schema.org/name> ?agency .
} order by (?agency)
```

### Funders of papers in GBIF Mendeley group that used GBIF

Mendeley group has UUID dcb8ff61-dbc0-3519-af76-2072f22bc22f, tag “GBIF_used” flags papers that use GBIF data.

```
SELECT DISTINCT ?identifier ?title ?agency ?grant

WHERE 
{ 
   <urn:uuid:dcb8ff61-dbc0-3519-af76-2072f22bc22f> <http://schema.org/itemListElement> ?work .
   
   ?work <http://purl.org/dc/terms/identifier> ?identifier .
   ?work <http://schema.org/about> “GBIF_used” .
   ?work <http://schema.org/name> ?title .

   ?identifier <http://schema.org/funder> ?award .
   ?award <http://schema.org/funder> ?funder .
   ?funder  <http://schema.org/name> ?agency .


   OPTIONAL
  {
   ?award <http://schema.org/roleName> ?grant .
  }

} ORDER BY ASC(?agency)
```

### Work foundered by named funder

```
SELECT ?funder ?work ?grant

WHERE 
{ 
   ?funder  <http://schema.org/name> “NSF” .
   ?award <http://schema.org/funder> ?funder .
   ?work <http://schema.org/funder> ?award .
   ?work <http://schema.org/name> ?name .


OPTIONAL
{
   ?award <http://schema.org/roleName> ?grant .

}

}
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

### Get different names for same author

The same author may appear in different datasets, such as CrossRef and ORCID. If work has same identifier, and author appears in same order in each source, then we can use a standard identifier work_id#author_n to group names, e.g.

```
SELECT DISTINCT ?author ?name
WHERE 
{ 
   ?author  <http://purl.org/dc/terms/identifier> <http://identifiers.org/doi/10.1073/pnas.1013136108#author_1> .
   ?author <http://schema.org/name> ?name .
}
```

### ORCIDs for GBIF-related papers

Use regexp to filter on ORCIDs

```
SELECT *

WHERE 
{ 
   <urn:uuid:dcb8ff61-dbc0-3519-af76-2072f22bc22f> <http://schema.org/itemListElement> ?work .
   
   ?work <http://purl.org/dc/terms/identifier> ?identifier .
   ?work <http://schema.org/about> “GBIF_used” .
   ?work <http://schema.org/name> ?title .

   ?identifier <http://schema.org/author> ?author .

   FILTER regex(str(?author), “orcid”, “i” ) .

   OPTIONAL
   {
     ?author <http://schema.org/name> ?aname .
   }
}
```

### GBIF-related people with ORCIDs

```
SELECT DISTINCT ?author ?aname

WHERE 
{ 

   <urn:uuid:dcb8ff61-dbc0-3519-af76-2072f22bc22f> <http://schema.org/itemListElement> ?work .
   
   ?work <http://purl.org/dc/terms/identifier> ?identifier .
   ?work <http://schema.org/about> “GBIF_used” .
   ?work <http://schema.org/name> ?title .

   ?identifier <http://schema.org/author> ?author .

   FILTER regex( str(?author), “orcid”, “i” ) .

   OPTIONAL
   {
     ?author <http://schema.org/name> ?aname .
   }

} ORDER BY ASC(?author)
```

### GBIF-related work by one person (identified by their ORCID)

```
SELECT DISTINCT *

WHERE 
{ 

   <urn:uuid:dcb8ff61-dbc0-3519-af76-2072f22bc22f> <http://schema.org/itemListElement> ?work .
   
   ?work <http://purl.org/dc/terms/identifier> ?identifier .
   ?work <http://schema.org/about> “GBIF_used” .
   ?work <http://schema.org/name> ?title .

   ?identifier <http://schema.org/author> <http://orcid.org/0000-0003-4197-0794> .


OPTIONAL
{
   <http://orcid.org/0000-0003-4197-0794> <http://schema.org/name> ?aname .
}

} 
```

### IPNI LSID

Find people that have IPNI LSIDs for authors

```
SELECT DISTINCT ?author  ?identifier ?aname

WHERE 
{ 
   ?author <http://purl.org/dc/terms/identifier> ?identifier .
   ?author <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://schema.org/Person> .

FILTER regex( ?identifier, “lsid”, “i” ) .

 
   OPTIONAL
   {
     ?author <http://schema.org/name> ?aname .
   }

}  LIMIT 10
```
### Citation counts for a paper

```
SELECT (COUNT(?cited_by) as ?count)
WHERE {
  ?work <http://purl.org/dc/terms/identifier> <http://identifiers.org/pubmed/11524383> .
  ?work <http://schema.org/name> ?name .
  ?cited_by <http://schema.org/citation> ?work . 
}
```

### Find specimens with same codes

```
SELECT *
WHERE {
  ?x <http://schema.org/alternateName> ?name . 
  ?y <http://schema.org/alternateName> ?name . 
  ?x <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://rs.tdwg.org/dwc/terms/Occurrence> .

  FILTER (?x != ?y)  
  
} LIMIT 10

```


### Find BHL page that has a name

```

```

### Find BHL pages that have a pair of names (potential synonyms)

```
SELECT ?page
WHERE {
?body1 <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> “Salanx ariakensis” . 
?annotation1  <http://www.w3.org/ns/oa#hasBody> ?body1 .
?annotation1 <http://www.w3.org/ns/oa#hasTarget> ?target1 .
?target1 <http://www.w3.org/ns/oa#hasScope> ?page . 

?body2 <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> “Parasalanx annitae” . 
?annotation2  <http://www.w3.org/ns/oa#hasBody> ?body2 .
?annotation2 <http://www.w3.org/ns/oa#hasTarget> ?target2 .
?target2 <http://www.w3.org/ns/oa#hasScope> ?page . 
}
```

### How many objects of each type do we have?

```
SELECT ?type (COUNT(?type) as ?typeCount)
WHERE {
  ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?type .
}
GROUP BY ?type
```


## Examples of data to play with

### GBIF dataset BDJ DOI linked to funder

A taxonomic monograph of the assassin bug genus Zelus Fabricius (Hemiptera: Reduviidae): 71 species based on 10,000 specimens

GBIF with paper http://www.gbif.org/dataset/a739f783-08c1-4d47-a8cc-2e9e6e874202

Paper with funder https://api.crossref.org/v1/works/http://dx.doi.org/10.3897/bdj.4.e8150

Need to parse the GBIF database metadata to get this

### PANGEA linked to publication

10.1038/ngeo1209

http://www.gbif.org/dataset/0db1837b-0125-4242-a018-9913da96e68b







