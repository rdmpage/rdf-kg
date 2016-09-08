# Document store

Use CouchDB to store documents and do conversions. Also supports searching and geospatial search.

## Javascript views

Beautify Javascript http://jsbeautifier.org


## Convert native JSON to n-triples

Use mime-type as flag to determine JSON format, output simple n-triples for export to RDF triple store. Need @context variables if we want to convert to JSON-LD for export.

### MIME types

- Mendeley list of documents application/vnd.mendeley-document.1+json
- ORCID application/vnd.orcid+json
- CrossRef application/vnd.crossref-api-message+json



To get n-triples, go to 

http://127.0.0.1:5984/rdf_kg/_design/triples/_list/n-triples/nt

