function grow(element) 
{
	var textarea = document.getElementById(element.id);
	var newHeight = textarea.scrollHeight;
	var currentHeight = textarea.clientHeight;
	if (newHeight > currentHeight) 
	{
		textarea.style.height = newHeight + 5 * 1 + 'px';
	}
}
function growload()
{
	var textarea = document.getElementById("content");
	if(textarea)
	{
		var newHeight = textarea.scrollHeight;
		textarea.style.height = newHeight+'px';
		textarea.style.overflow = 'hidden';
	}
}
function addEvent(obj, evType, fn)
{ 
	if (obj.addEventListener)
	{ 
		obj.addEventListener(evType, fn, false); 
		return true; 
	} else if (obj.attachEvent)
	{ 
		var r = obj.attachEvent("on"+evType, fn); 
		return r; 
	} else 
	{ 
		return false; 
	}
}
addEvent(window, 'load', growload);