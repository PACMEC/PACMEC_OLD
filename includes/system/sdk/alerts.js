/*!
  * SDK PACMEC v0.0.1 (https://managertechnology.com.co/)
  * Copyright 2020-2021 The SDK Authors (https://github.com/feliphegomez)
  * Licensed under MIT (https://managertechnology.com.co/)
  */

function initAlerts(){ var close = document.getElementsByClassName("pacmec-alert-closebtn"), i; 
	for (i = 0; i < close.length; i++) {
		close[i].onclick = function(){
			var div = this.parentElement;
			div.style.opacity = "0";
			setTimeout(function(){ div.style.display = "none"; }, 600);
		}
	}
}
window.addEventListener("load", initAlerts);
