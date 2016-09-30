<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/triplestore/triple_store.php');

$query = (isset($_GET['query']) ? $_GET['query'] : '');

if ($query) 
{
	// Triple store
	global $store_config;
	
	$query = str_replace("\\", "", $query);
	
	$r = $store->query($query);
	
	header("Content-type: text/html; charset=utf-8\n\n");
	
	echo '<html>
	<head>
		<style>
			body { font-family:sans-serif; padding:10px; };
			tbody { font-size:12px; }
			th { color:white;background-color:rgb(192,192,192); }
			td { border:1px solid rgb(192,192,192); }
			
		</style>
	</head>
	<body>';	
		
	echo '<pre>' . htmlentities ($query) . '</pre>';
	
	switch ($r['query_type'])
	{
		case 'load':
			echo '<pre>';
			print_r($r['result']);
			echo '</pre>';
			echo '<p>' . 'Query took ' . $r['query_time'] . ' seconds.' . '</p>';	
			break;
	
		case 'describe':
/*			echo '<pre>';
			print_r($r['result']);
			echo '</pre>';*/
			echo "<table border=\"0\">";
			echo '<tbody>';
			echo '<tr><th>Predicate</th><th>Object</th></tr>';
			foreach ($r['result'] as $rows)
			{
				foreach ($rows as $k => $v)
				{
					echo '<tr>';
					
					echo '<td>' . $k . '</td>';
					echo '<td>';
					foreach ($v as $value)
					{
						echo $value['value'];
						echo ' [' . $value['type'] . ']';
						
						if (isset($value['lang']))
						{
							echo ' [' . $value['lang'] . ']';
						}
						echo '<br/>';
						
						if ($k == 'http://xmlns.com/foaf/0.1/depiction')
						{
							echo '<img src="' . $value['value'] . '">';
						}
						
						
					}
					echo '</td>';
					
					echo '</tr>';
				}
			}
			echo '</tbody>';
			echo '</table>';
			echo '<p>' . 'Query took ' . $r['query_time'] . ' seconds.' . '</p>';
			break;
			
		case 'select':
			if (count($r['result']['rows']) == 0)
			{
				echo "Nothing matches query";
			}
			else
			{
				echo "<table border=\"0\">";
				echo '<tbody>';
				echo '<tr>';
				foreach ($r['result']['rows'][0] as $k => $v)
				{
					echo '<th>' . $k . '</th>';
				}	
				echo '</tr>';
				foreach ($r['result']['rows'] as $row)
				{
					echo '<tr>';
					foreach ($row as $item)
					{
						echo '<td>' . $item . '</td>';
					}
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
			}
			echo '<p>' . 'Query took ' . $r['query_time'] . ' seconds.' . '</p>';
			break;
			
		default:
			echo '<pre>';
			print_r($r['result']);
			echo '</pre>';
			echo '<p>' . 'Query took ' . $r['query_time'] . ' seconds.' . '</p>';	
			break;
	}
	
	echo '</body></html>';
}
else
{
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
 "http://www.w3.org/TR/REC-html40/loose.dtd">
<html>
  <head>
    <title>SPARQL Query Service</title>
 <!--   <link rel="stylesheet" type="text/css" href="../style.css"  /> -->
<!-- <link rel="shortcut icon" type="image/ico" href="../images/favicon.ico" /> -->
    <script language="JavaScript" src="editor.js"></script>
  </head>
<body onload="init()" style="font-family: Verdana,Arial,Helvetica, sans-serif; font-size: 12px;">
<form id="theForm" action="./" method="get" onsubmit="validateQueryForm(this)">

    <table>	
    <tr>
	<td colspan="2">
<select name="insertPrefix"
     onchange="insertAtStart(this.options[this.selectedIndex].value)">
		    <option value="">-- Prefixes --</option>
<option value="PREFIX cc: &lt;http://web.resource.org/cc/&gt;">CC</option>
<option value="PREFIX connotea: &lt;http://www.connotea.org/2005/01/schema#&gt;">CONNOTEA</option>
<option value="PREFIX dataview: &lt;http://www.w3.org/2003/g/data-view#&gt;">DATAVIEW</option>
<option value="PREFIX dc: &lt;http://purl.org/dc/elements/1.1/&gt;">DC</option>
<option value="PREFIX dcterms: &lt;http://purl.org/dc/terms/&gt;">DCTERMS</option>
<option value="PREFIX dbpprop: &lt;http://dbpedia.org/property/&gt;">DBPPROP</option>

<option value="PREFIX foaf: &lt;http://xmlns.com/foaf/0.1/&gt;">FOAF</option>
<option value="PREFIX geo: &lt;http://www.w3.org/2003/01/geo/wgs84_pos#&gt;">GEO</option>
<option value="PREFIX owl: &lt;http://www.w3.org/2002/07/owl#&gt;">OWL</option>
<option value="PREFIX prism: &lt;http://prismstandard.org/namespaces/2.0/basic/&gt;">PRISM</option>
 

<option value="PREFIX rdf: &lt;http://www.w3.org/1999/02/22-rdf-syntax-ns#&gt;">RDF</option>
<option value="PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;">RDFS</option>
<option value="PREFIX rss: &lt;http://purl.org/rss/1.0/&gt;">RSS</option>
<option value="PREFIX sioc: &lt;http://rdfs.org/sioc/ns#&gt;">SIOC</option>
<option value="PREFIX sioct: &lt;http://rdfs.org/sioc/types#&gt;">SIOCT</option>
<option value="PREFIX skos: &lt;http://www.w3.org/2004/02/skos/core#&gt;">SKOS</option>
<option value="PREFIX tag: &lt;http://www.holygoat.co.uk/owl/redwood/0.1/tags/&gt;">TAGS</option>
<option value="PREFIX vs: &lt;http://www.w3.org/2003/06/sw-vocab-status/ns#&gt;">VS</option>
<option value="PREFIX wot: &lt;http://xmlns.com/wot/0.1/&gt;">WOT</option>
<option value="PREFIX xhtml: &lt;http://www.w3.org/1999/xhtml&gt;">XHTML</option>
<option value="PREFIX xsd: &lt;http://www.w3.org/2001/XMLSchema#&gt;">XSD</option>
</select>		  
<select name="insertTemplate"
     onchange="insert(this.options[this.selectedIndex].value)">
		    <option value="">-- Template --</option>
<option value="SELECT DISTINCT ?s ?p ?o

WHERE 
{ 
   ?s ?p ?o .
}">Select</option>
<option value="CONSTRUCT 
{
   ?s ?p ?o . 
}
WHERE 
{ 
   ?s ?p ?o .
}">Construct</option>
<option value="ASK

WHERE 
{ 
   ?s ?p ?o .
}">Ask</option>
<option value="DESCRIBE <...>">Describe</option>

		  </select>
		  
<!--        <select name="stylesheet">
          <option value="">-- Stylesheet --</option>
          <option value="result-to-html.xsl">HTML table</option>
        </select>-->


<!-- do something with query -->
<input type="submit" value="Execute" />


		</td>
    </tr>	
	<tr>
	  <td>	  
<textarea id="editArea" name="query" cols="80" rows="30"></textarea>	   
	  </td>
<td style="width:120">
  <input type="button" class="button"
         value="Comment Region"  onclick="comment()" 
         onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" 
         onmousedown="mouseDown(this)" onmouseup="mouseUp(this)" />
  <input type="button" class="button"
         value="Uncomment Region"  onclick="unComment()" 
         onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" 
         onmousedown="mouseDown(this)" onmouseup="mouseUp(this)" />
  <input type="button" class="button"
         value="Make Optional"  onclick="optional()" 
         onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" 
         onmousedown="mouseDown(this)" onmouseup="mouseUp(this)" />
  <input type="button" class="button"
         value="Indent Region"  onclick="indent()" 
         onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" 
         onmousedown="mouseDown(this)" onmouseup="mouseUp(this)" />
	
  <input type="button" class="button"
         value="BASE"  onclick="insert('BASE &lt;http://example.org/base&gt;')" 
         onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" 
         onmousedown="mouseDown(this)" onmouseup="mouseUp(this)" />
  <input type="button" class="button"
         value="FROM"  onclick="insert('FROM &lt;http://example.org/from&gt;')" 
         onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" 
         onmousedown="mouseDown(this)" onmouseup="mouseUp(this)" />
  <input type="button" class="button"
         value="FROM NAMED"  onclick="insert('FROM NAMED &lt;http://example.org/named&gt;')" 
         onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" 
         onmousedown="mouseDown(this)" onmouseup="mouseUp(this)" />
  <input type="button" class="button"
         value="UNION"  onclick="insert('UNION')" 
         onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" 
         onmousedown="mouseDown(this)" onmouseup="mouseUp(this)" />
  <input type="button" class="button"
         value="GRAPH"  onclick="insert('GRAPH')" 
         onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" 
         onmousedown="mouseDown(this)" onmouseup="mouseUp(this)" />
  <input type="button" class="button"
         value="ORDER BY"  onclick="insert('ORDER BY')" 
         onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" 
         onmousedown="mouseDown(this)" onmouseup="mouseUp(this)" />
  <input type="button" class="button"
         value="ORDER BY ASC()"  onclick="insert('ORDER BY ASC(?x)')" 
         onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" 
         onmousedown="mouseDown(this)" onmouseup="mouseUp(this)" />
  <input type="button" class="button"
         value="ORDER BY DESC()"  onclick="insert('ORDER BY DESC(?x)')" 
         onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" 
         onmousedown="mouseDown(this)" onmouseup="mouseUp(this)" />
  <input type="button" class="button"
         value="LIMIT"  onclick="insert('LIMIT 10')" 
         onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" 
         onmousedown="mouseDown(this)" onmouseup="mouseUp(this)" />
  <input type="button" class="button"
         value="OFFSET"  onclick="insert('OFFSET 10')" 
         onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" 
         onmousedown="mouseDown(this)" onmouseup="mouseUp(this)" />

  <input type="button" class="button"
         value="Simple Filter"  onclick="insert('FILTER ( ?x &lt; 3 ) .')" 
         onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" 
         onmousedown="mouseDown(this)" onmouseup="mouseUp(this)" />	    
  <input type="button" class="button"
         value="Regex Filter"  onclick="insert('FILTER regex( ?name, &#34;Jane&#34;, &#34;i&#34; ) .')" 
         onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" 
         onmousedown="mouseDown(this)" onmouseup="mouseUp(this)" />
  <input type="button" class="button"
         value="Bound Filter"  onclick="insert('FILTER ( bound(?x) ) .')" 
         onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" 
         onmousedown="mouseDown(this)" onmouseup="mouseUp(this)" />
  <input type="button" class="button"
         value="Date Filter"  onclick="insert('FILTER ( ?date &gt; &#34;2005-01-01T00:00:00Z&#34;^^xsd:dateTime ) .')" 
         onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" 
         onmousedown="mouseDown(this)" onmouseup="mouseUp(this)" />
  <input type="button" class="button"
         value="Clear All"  onclick="clearAll()" 
         onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" 
         onmousedown="mouseDown(this)" onmouseup="mouseUp(this)" />
</td>
</tr>
<tr><td><p style="font-size:11px;">Editor interface based on Danny Ayers' <a href="http://dannyayers.com/2006/09/27/javascript-sparql-editor">Javascript SPARQL editor</a>, combined with <a href="http://arc.semsol.org/">ARC</a>.</p></td></tr>
</table>
</form>
</body>
</html>

<?php
}
?>