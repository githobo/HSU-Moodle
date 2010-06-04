function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      oldonload();
      func();
    }
  }
}

function addClass(element,value) {
  if (!element.className) {
    element.className = value;
  } else {
    element.className+= " ";
    element.className+= value;
  }
}

function activeLinks(){
  if (!document.getElementsByTagName("a")) return false;
    var links = document.getElementsByTagName("a");
    var page = window.location.href;
    for(var i=0;i<links.length;i++)
        if (page.search(links[i].href) >= 0)
          addClass(links[i].parentNode,"active");
}

// by Paul@YellowPencil.com and Scott@YellowPencil.com
function setTall() {
  if (!document.getElementById("nav-secondary")) return false;
  if (!document.getElementById("text")) return false;
	if (document.getElementById) {
		var divs = new Array(document.getElementById('nav-main'), document.getElementById('text'), document.getElementById('nav-secondary'));
		
		var maxHeight = 0;
		for (var i = 0; i < divs.length; i++) {
			if (divs[i].offsetHeight > maxHeight) maxHeight = divs[i].offsetHeight;
		}
		
		for (var i = 0; i < divs.length; i++) {
			divs[i].style.height = maxHeight + 'px';

			if (divs[i].offsetHeight > maxHeight) {
				divs[i].style.height = (maxHeight - (divs[i].offsetHeight - maxHeight)) + 'px';
			}
		}
	}
}

window.onresize = function() {
	setTall();
}
function toForm() {
    usernameEl = document.getElementById('username');
    usernameEl.focus();
}
addLoadEvent(setTall);
addLoadEvent(activeLinks);