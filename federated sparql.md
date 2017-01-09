# Federated SPARQL

Can make SPARQL queries to remote servers, for example [Wikidata’s SPARQL endpoint](https://query.wikidata.org/sparql):

```
PREFIX schema: <http://schema.org/>
PREFIX wikibase: <http://wikiba.se/ontology#>
#Wikidata items with a Wikispecies sitelink
#added before 2016-10
#illustrates sitelink selection, ";" notation
SELECT ?item ?article
WHERE
{
  SERVICE <https://query.wikidata.org/sparql> {
	?article 	schema:about ?item ;
           schema:isPartOf <https://species.wikimedia.org/> .
  }
}
LIMIT 10
```

