// The maxattachment limit should be enforced both in javascript
// and in php.
// Taken from email sendmail.php js code with a few modifications

var upload_number = 1;
function addFileInput(txt,max) {
	
	if (upload_number != max ) {
    	var d = document.createElement("div");
    	d.setAttribute("id", "id_FILE_"+upload_number);
    	var file = document.createElement("input");
    	file.setAttribute("type", "file");
    	file.setAttribute("name", "FILE_"+upload_number);
    	file.setAttribute("id", "FILE_"+upload_number);
		//    file.setAttribute("onchange", "addFileInput('"+txt+"')");
    	d.appendChild(file);
    	var a = document.createElement("a");
    	a.setAttribute("href", "javascript:removeFileInput('id_FILE_"+upload_number+"');");
    	a.appendChild(document.createTextNode(txt));
    	d.appendChild(a);
    	document.getElementById("id_FILE_"+(upload_number-1)).parentNode.appendChild(d);
    	upload_number++;
	} else {
		alert("You are at your max attachment size of " + max);	
	}
}

function removeFileInput(i) {
    var elm = document.getElementById(i);
    document.getElementById(i).parentNode.removeChild(elm);
    upload_number--;
}

