/* Thomas Productions
*
* PROJECT:
* - object.js
*
* VERSION:
* - 1.0.2
*
* USE: 
* - simple HTML/XML library allowing easy creation of objects
*/

function setObject(obj,array){
	for(key in array){
		var prp = key;
		var val = array[key];
		obj[prp] = val;
	}
}

function createText(content){
	return document.createTextNode(content);
}

function createElement(type, atr, content, inEle){
	var ele = document.createElement(type);

	for(key in atr){
		var prp = key;
		var val = atr[key];
		ele.setAttribute(prp,val);
	}
	
	if(content != undefined){
		var node = document.createTextNode(content);
		ele.appendChild(node);
	}
	
	if(inEle != undefined){
		ele.appendChild(inEle);
	}
	
	return ele;
}

function setElement(ele, atr){
	for(key in atr){
		var prp = key;
		var val = atr[key];
		ele.setAttribute(prp,val);
	}
}

function insertElementAt(ele, parent){
	parent.appendChild(ele);
	
	return ele;
}

function removeElement(ele){
    ele.parentNode.removeChild(ele);
}

function updateElementContent(ele,content){
	ele.innerHTML='';
	var node = document.createTextNode(content);
	ele.appendChild(node);
}

function cloneArray(a){
	out = new Array();
	for ( var i = 0; i < a.length; i++ )
	out.push(a[i]);
	return out;
}
