
// Process a Mendeley document in a group list
function mendeley_group(doc) {
  for (var i in doc.message) {
    if (doc.message[i].identifiers) {
      for (var j in doc.message[i].identifiers) {
        switch (j) {
          case 'doi':
            doi = doc.message[i].identifiers[j];
            // clean
            doi = doi.replace(/DOI:\s*/i, '');
            doi = doi.replace(/\s+/g, '');
            emit(doc.message[i].id, doi);
            break;
          default:
            break;
         }
      }
    }
  }
}


function(doc) {
  if (doc['message-format']) {
    switch (doc['message-format']) {
      case 'application/vnd.mendeley-document.1+json':
        mendeley_group(doc);
        break;
      default:
        break;
    }
  }
}