Linked data plants example




IPNI - IPNI author
IPNI - DOI
IPNI - types

types - GBIF
DOI - ORCID
IPNI author - Wikidata
DOI - PMID

type search 

type species for genus

type specimen for species

http://api.gbif.org/v1/occurrence/search?taxonkey=7454391&typeStatus=*


infer IPNI author - ORCID

TPL - IPNI
GBIF - IPNI
GBIF - TPL

IPNI/TPL - NCBI taxonomy

DOI - treebase

Compute list of taxon authors with ORCIDs
Funding for taxonomic work
Papers linked to specimens (via types)
Bibliographies for species based on synonyms
Heterotypic synonyms
Who publishes taxonomic information?
Publishers and institutions (eg., Kew as publisher, Kew as author affiliation)
-- need ids for institutions (Ringold, Goog KG, GrBio)
How many species described using sequences (map articles to Pubmed, and/or extract sequences for each species name)
How much taxonomy is open access?
Where are new species being discovered?
How much literaure online is new versus old (publishing versus creating dates in CrossRef)
How many papers are published with DOIs?
What name changes are based on evolutuonary trees? (DOi -> TreeBase)

- how can we enhance any record in a biodiversity database?

- can we use linked data for cleaning, e.g. merging institution names, filtering on text, using linked items 


services

DOI - ORCID
IPNI author - Wikidata author

resolvers/importers

IPNI LSID/bulk import
ORCID reoslver
DOI reoslver
JSTOR, other refs resolvers
IPNI author resolver
Darwin Core importer
Wikidata resolvers (can we process Wikidata in Javascript in CouchDB?


## Using

Linking an IPNI name to types via GBIF, and georeferencing a paper as a result.

```
select *
where
{
  VALUES ?canonicalName { “Stylosanthes falconensis” }

  # IPNI record with this name
  ?name <http://rs.tdwg.org/ontology/voc/TaxonName#nameComplete> ?canonicalName .
  ?name <http://rs.tdwg.org/ontology/voc/Common#publishedInCitation> ?work .
  
  # Get types via GBIF
  ?gbif <http://rs.tdwg.org/dwc/terms/canonicalName> ?canonicalName .
  ?gbif <http://rs.tdwg.org/ontology/voc/TaxonName#typifiedBy> ?type .
  ?type <http://schema.org/name> ?code .
  
  # locality info (means we can link publication to place on map)
  ?type <http://rs.tdwg.org/dwc/terms/locationID> ?loc .
  ?loc <http://schema.org/hasMap> ?map .
}
```

### Linking GBIF occurrence to GenBank

```
select *
where {
  VALUES ?occurence1 { <http://www.gbif.org/occurrence/1258410712> }
  ?occurence1 <http://schema.org/alternateName> ?name  .
  ?occurrence2 <http://schema.org/alternateName> ?name  .
  ?occurrence2 <http://rs.tdwg.org/dwc/terms/associatedSequences> ?sequence .
  ?occurrence2 <http://rs.tdwg.org/dwc/terms/associatedReferences> ?work .
  ?occurrence2 <http://rs.tdwg.org/dwc/terms/associatedReferences> ?work .
  
  ?occurrence2 <http://rs.tdwg.org/dwc/terms/identificationID> ?id2 .
  ?id2 <http://rs.tdwg.org/dwc/terms/scientificName> ?species2 .
  
   ?occurence1 <http://rs.tdwg.org/dwc/terms/identificationID> ?id1 .
  ?id1 <http://rs.tdwg.org/dwc/terms/scientificName> ?species1 .
 
} 
```

### Linking GBIF to GenBank and georeferencing

```
select *
where {
  VALUES ?occurence1 { <http://www.gbif.org/occurrence/1258951616> }
  ?occurence1 <http://schema.org/alternateName> ?name  .
  ?occurrence2 <http://schema.org/alternateName> ?name  .
  ?occurrence2 <http://rs.tdwg.org/dwc/terms/associatedSequences> ?sequence .
  ?occurrence2 <http://rs.tdwg.org/dwc/terms/associatedReferences> ?work .
  ?occurrence2 <http://rs.tdwg.org/dwc/terms/associatedReferences> ?work .
  
  ?occurrence2 <http://rs.tdwg.org/dwc/terms/identificationID> ?id2 .
  ?id2 <http://rs.tdwg.org/dwc/terms/scientificName> ?species2 .
  
   ?occurence1 <http://rs.tdwg.org/dwc/terms/identificationID> ?id1 .
  ?id1 <http://rs.tdwg.org/dwc/terms/scientificName> ?species1 .
 OPTIONAL {
   ?occurence1 <http://rs.tdwg.org/dwc/terms/locationID> ?loc .
  ?loc <http://schema.org/hasMap> ?map .

  }
} 
```

```
select *
where {
  VALUES ?occurence1 { <http://www.gbif.org/occurrence/543590847> }
  ?occurence1 <http://schema.org/alternateName> ?name  .
  ?occurrence2 <http://schema.org/alternateName> ?name  .
  #?occurrence2 <http://rs.tdwg.org/dwc/terms/associatedSequences> ?sequence .
  #?occurrence2 <http://rs.tdwg.org/dwc/terms/associatedReferences> ?work .
  #?occurrence2 <http://rs.tdwg.org/dwc/terms/associatedReferences> ?work .
  
 ?occurrence2 <http://rs.tdwg.org/dwc/terms/identificationID> ?id2 .
  ?id2 <http://rs.tdwg.org/dwc/terms/scientificName> ?species2 .
  
   ?occurence1 <http://rs.tdwg.org/dwc/terms/identificationID> ?id1 .
  ?id1 <http://rs.tdwg.org/dwc/terms/scientificName> ?species1 .
 OPTIONAL {
   ?occurence1 <http://rs.tdwg.org/dwc/terms/locationID> ?loc .
  ?loc <http://schema.org/hasMap> ?map .

  }
} 
```

