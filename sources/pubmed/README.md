# PubMed

## JSON

### Get article metadata

https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi?db=pubmed&id=26752961&retmode=json

### Get what article cites using PMC

https://eutils.ncbi.nlm.nih.gov/entrez/eutils/elink.fcgi?db=pubmed&dbfrom=pmc&id=4698514&retmode=json

```
{
    “header”: {
        “type”: “elink”,
        “version”: “0.3”
    },
    “linksets”: [
        {
            “dbfrom”: “pmc”,
            “ids”: [
                4698514
            ],
            “linksetdbs”: [
                {
                    “dbto”: “pubmed”,
                    “linkname”: “pmc_pubmed”,
                    “links”: [
                        26752961
                    ]
                },
                {
                    “dbto”: “pubmed”,
                    “linkname”: “pmc_refs_pubmed”,
                    “links”: [
                        26140015,
                        25561669,
                        24132122,
                        22684966,
                        19841276,
                        19666622,
                        16243770,
                        15258291,
                        3447015
                    ]
                }
            ]
        }
    ]
}
```

