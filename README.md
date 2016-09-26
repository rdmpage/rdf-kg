# Knowledge Graph (again)


### ORCID login

http://bionames.org/~rpage/orcid-php/index.php


## Ideas

We harvest data. Many sources will be native JSON, store these as documents in CouchDB with source URL as _id, use view to convert to triples. Create one view per data type (to keep Javascript code manageable). Use MIME-type to distinguish between different data types. Can also harvest native RDF if sues acceptable vocabulary. Triples would be good native format for “linking” data sets. Also support JSON-LD, either in CouchDB, or convert to n-triples and import directly.

Triples are imported into triple store and queried.

Use “groups” as convenient way to restrict queries, e.g. Mendeley literature group, EOL taxon group. Treat as schema.org itemLists.

Recode multiple identifiers as dc:identifier, use indirection (“object that has identifier”) as way to query across multiple sources of data, rather than try and resolve “sameAs” links. 

b-nodes always encoded as full, if arbitrary, URIs, means we can refer to them later when mapping to actual identifiers. For example, if we have [’s’, dc:identifier, ‘doi’] and [‘q’, dc:identifier, ‘doi’] then s==q and we can access both sets of triples by [‘x’, dc:identifier, ‘doi’] .

Treat data as messages, if message body is empty we need to fetch data, so we have a message queue which we continually poll and add missing data.

