# Experiments with JSON-LD

http://www.easyrdf.org/converter

http://localhost:8080/

## Mendeley

Source triples

http://tinyurl.com/zp3nxcq

```
<http://identifiers.org/doi/10.1016/j.quascirev.2008.09.013> <http://purl.org/dc/terms/identifier> <http://identifiers.org/doi/10.1016/j.quascirev.2008.09.013> .
<http://identifiers.org/doi/10.1016/j.quascirev.2008.09.013> <http://purl.org/dc/terms/title> “Reconstructing ecological niches and geographic distributions of caribou (Rangifer tarandus) and red deer (Cervus elaphus) during the Last Glacial Maximum” . 
```


```
{
  “identifier” :”http://purl.org/dc/terms/identifier”,
  “title” : “http://purl.org/dc/terms/title”,
  “DOI” : “http://identifiers.org/doi/“
}
```
[{“@id”:”http://identifiers.org/doi/10.1016/j.quascirev.2008.09.013”,”http://purl.org/dc/terms/identifier”:[{“@id”:”http://identifiers.org/doi/10.1016/j.quascirev.2008.09.013”}],”http://purl.org/dc/terms/title”:[{“@value”:”Reconstructing ecological niches and geographic distributions of caribou (Rangifer tarandus) and red deer (Cervus elaphus) during the Last Glacial Maximum”}]}]
```

```
{
  “@context”: {
    “identifier”: “http://purl.org/dc/terms/identifier”,
    “title”: “http://purl.org/dc/terms/title”,
    “DOI”: “http://identifiers.org/doi/“
  },
  “@id”: “DOI:10.1016/j.quascirev.2008.09.013”,
  “identifier”: {
    “@id”: “DOI:10.1016/j.quascirev.2008.09.013”
  },
  “title”: “Reconstructing ecological niches and geographic distributions of caribou (Rangifer tarandus) and red deer (Cervus elaphus) during the Last Glacial Maximum”
}
```
