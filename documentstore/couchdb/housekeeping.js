{
   "_id": "_design/housekeeping",
   "_rev": "5-6fc029fba68ee19f1bb08cab9115edef",
   "language": "javascript",
   "views": {
       "ids": {
           "map": "\nfunction(doc) {\n  emit(null, doc._id);\n}"
       },
       "orcid_external_ids": {
           "map": "function message(doc) {\n if (doc.message) {\n\n  // works, get identifiers \n\n  var works = doc.message['orcid-profile']['orcid-activities']['orcid-works']['orcid-work'];\n\t  for (var i in works) {\n\n\t\t// identifiers\n\t\tif (works[i]['work-external-identifiers']) {\n\t\t  \n\t\t  for (var j in works[i]['work-external-identifiers']['work-external-identifier']) {\n\t\t\tvar identifier = works[i]['work-external-identifiers']['work-external-identifier'][j];\n\t\t\tswitch (identifier['work-external-identifier-type']) {\n\t\t\t\n\t\t\t  case 'DOI':\n\t\t\t\tvar doi = identifier['work-external-identifier-id'].value;\n\t\t\t\t\n\t\t\t\t//doi = doi.replace(/^doi:/, '');\n\t\t\t\t//doi = doi.replace(/\\.$/, '');\n\t\t\t\t//doi = doi.replace(/^http:\\/\\/(dx.)?doi.org\\//, '');\n\t\t\t\t//doi = doi.replace(/\\s+/g, '');\n\t\t\t \n\t\t\t\temit (doi, 1);\n\t\t\t\tbreak;\n\t\t\t\t\n\t\t\t  case 'PMID':\n\t\t\t\tvar pmid = identifier['work-external-identifier-id'].value;\n\t\t\t\temit (pmid, 1);\n\t\t\t\tbreak;\n\t\t\t\t\n\t\t\t  case 'ISBN':\n\t\t\t\tvar isbn = identifier['work-external-identifier-id'].value;\n\t\t\t\t\n\t\t\t\t//isbn = isbn.replace(/^13:\\s*/g, '');\n\t\t\t\t//isbn = isbn.replace(/\\s+/g, '');\n\t\t\t\t\n\t\t\t\temit (isbn, 1);\n\t\t\t\tbreak;\n\t\t\t\t\n\t\t\t  case 'ISSN':\n\t\t\t\tvar list = identifier['work-external-identifier-id'].value.split(';');\n\t\t\t\temit (list, 1);\n\t\t\t\tbreak;\n\t\t\t\t\n\t\t\t  default:\n\t\t\t\tbreak;\n\t\t\t}\n\t\t  }\n\t\t}\n    }\n  }\n}\n\n\nfunction(doc) {\n  if (doc['message-format']) {\n    if (doc['message-format'] == 'application/vnd.orcid+json') {\n      message(doc);\n    }\n  }\n}",
           "reduce": "_sum"
       }
   }
}