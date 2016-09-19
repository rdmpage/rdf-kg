/*

Shared code


*/
		
//----------------------------------------------------------------------------------------
// Store a triple with optional language code
function triple(subject, predicate, object, language) {
  var triple = [];
  triple[0] = subject;
  triple[1] = predicate;
  triple[2] = object;
  
  if (typeof language === 'undefined') {
  } else {
    triple[3] = language;
  }
  
  return triple;
}

//----------------------------------------------------------------------------------------
// Store a quad (not used at present)
function quad(subject, predicate, object, context) {
  var triple = [];
  triple[0] = $subject;
  triple[1] = $predicate;
  triple[2] = $object;
  triple[3] = $context;
  
  return triple;
}

//----------------------------------------------------------------------------------------
// Enclose triple in suitable wrapping for HTML display or triplet output
function wrap(s, html) {
  if (s.match(/^(http|urn|_:)/)) {
    if (html) {
      s = '&lt;' + s + '&gt;';
    } else {
      s = '<' + s + '>';
    }
  } else {
    s = '"' + s.replace(/"/, '\"') + '"';
  }
  return s;
}

//----------------------------------------------------------------------------------------
// https://css-tricks.com/snippets/javascript/htmlentities-for-javascript/
function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

//----------------------------------------------------------------------------------------
function output(doc, triples) {
  if (1) {
	  // Output triples
	  
	  var nquads = '';
	  
	  var html = '<table width="100%">';
	  for (var i in triples) {
		var s = 0;
		var p = 1;
		var o = 2;
		var lang = 3;
		// SPO
		//console.log(JSON.stringify(triples[i]));
	
	
	
		//html += '<tr><td>' + wrap(triples[i][s], true) + '</td><td>' + wrap(triples[i][p], true) + '</td><td>' + wrap(triples[i][o], true) + ' .</td></tr>';
	
	    nquads += wrap(triples[i][s], false) 
	    	+ ' ' + wrap(triples[i][p], false) 
	    	+ ' ' + wrap(triples[i][o], false);
	    	
	    if (triples[i][lang]) {
	    	nquads += '@' + triples[i][lang];
	    }
	    	
	    nquads += ' .' + "\n";
	
	
	  }
	  html += '</table>';
	  
	  html += '<pre>' + htmlEntities(nquads) + '</pre>';
	  
	  $('#output').html(html);
	  
	   // convert RDF to JSON-LD
jsonld.fromRDF(nquads, {format: 'application/nquads'}, function(err, j) {

//  $('#jsonld').html(JSON.stringify(j, null, 2));

// make nice
 var context = {
  "@vocab" : "http://schema.org/",
  
  // RDF syntax
   "type": "http://www.w3.org/1999/02/22-rdf-syntax-ns#type",
  
  
  // Dublin Core
  "identifier" :"http://purl.org/dc/terms/identifier",
//  "title" : "http://purl.org/dc/terms/title",

  // Bibio
  "volume" : "http://purl.org/ontology/bibo/volume",
  "issue" : "http://purl.org/ontology/bibo/issue",
  "pages" : "http://purl.org/ontology/bibo/pages",
  
  // Open Annotation
  "oa" : "http://www.w3.org/ns/oa#",  
  
  // Identifiers
  "DOI" : "http://identifiers.org/doi/",
  "ISSN": "http://www.worldcat.org/issn/",
  "ORCID": "http://orcid.org/",
  "PMID" : "http://identifiers.org/pmid/"
  
};

jsonld.compact(j, context, function(err, compacted) {
  $('#jsonld').html('<pre>' + JSON.stringify(compacted, null, 2) + '</pre>');
  });
  
  
}); 
  
  
  } else {
      // CouchDB
	  for (var i in triples) {
		var s = 0;
		var p = 1;
		var o = 2;
		//emit([wrap(triples[i][s], false), wrap(triples[i][p], false), wrap(triples[i][o], false)], 1);
	  }
    
    
  }
}
