
var editArea;

function init(){
   editArea = document.getElementById('editArea');
}

// rdmp
function validateQueryForm (form)
{
	// Required fields for all references
	if (editArea.value == "")
	{
		alert ("Please enter a SPARQL query.");
		editArea.focus();
		return false;
	}
}

function insertText(editField, insertText) {
// IE
   if (document.selection) {
      editField.focus();
      selected = document.selection.createRange();
      selected.text = insertText;
      return;
   } 
 // MOZ
   var length =  editField.value.length;
   var start = editField.selectionStart;
   var end = editField.selectionEnd;

   if ((start != 0) && (end != length)) { 
      var before = editField.value.substring(0, start);
      var after = editField.value.substring(end, length);
      editField.value = before + insertText + after;
   } else {
      editField.value += insertText;
   }
}

function insert(text){
   insertText(editArea, text + "\n");
}

function insertAtTop(editField, insertText) {
// IE - to do
   if (document.selection) {
insertText(editField, insertText);
}

 // MOZ
      editField.value = insertText+editField.value;
}

function insertAtStart(insertText) {
   insertAtTop(editArea, insertText + "\n");
}

// might not work with IE
function inset(editField, insetString) {
   var start = editField.selectionStart;
   var end = editField.selectionEnd;

    for(var c=start;c<end;c++){
   var length =  editField.value.length;
      var before = editField.value.substring(0, c);
      var after = editField.value.substring(c, length);
       if(editField.value.substring(c-1, c) == "\n") {
          editField.value = before + insetString + after;
          end = end+insetString.length;
       }
    }
}

function commentOut(editField) {
   inset(editField, "#");
}

function comment(){
   commentOut(editArea);
}

function indentArea(editField){
   inset(editField, "    ");
}

function indent(){
   indentArea(editArea);
}

// might not work with IE
function makeOptional(editField) {

   var length =  editField.value.length;

   var start = editField.selectionStart;
   var end = editField.selectionEnd;

   var before = editField.value.substring(0, start);
   var after = editField.value.substring(end, length);

   var selection = editField.value.substring(start, end);

    editField.value = before + "\nOPTIONAL\n{\n" + selection + "\n}\n"+after;
}


// quick and dirty - wipes all # in selected region
function unCommentRegion(editField) {
   var length =  editField.value.length;
   var start = editField.selectionStart;
   var end = editField.selectionEnd;

    for(var c=start;c<end;c++){
      var before = editField.value.substring(0, c-1);
      var after = editField.value.substring(c, length);
       if(editField.value.substring(c-1, c) == "#") {
          editField.value = before + after;
       }
    }
}

function unComment(){
   unCommentRegion(editArea);
}





function clearArea(editField){
   editField.value = "";
}

function clearAll(){
   clearArea(editArea);
}

function optional(){
   makeOptional(editArea);
}

// Styling on mouse events - needs fixing
  function mouseOver(ctrl){
//	ctrl.style.borderColor = '#000000';
//	ctrl.style.backgroundColor = '#BBBBBB';	
  }
  
  function mouseOut(ctrl){
//	ctrl.style.borderColor = '#ccc';  
//	ctrl.style.backgroundColor = '#CCCCCC';
  }
  
  function mouseDown(ctrl){
//	ctrl.style.backgroundColor = '#8492B5';
  }
  
  function mouseUp(ctrl){
//  	ctrl.style.backgroundColor = '#B5BED6';
  }
