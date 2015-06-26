
// these two functions could be better combined. they share a lot in common, obviously. 
// will combine after I'm done writing js for this, in case this is used for additional things


document.addEventListener('DOMContentLoaded', function (){

function openWindow() {
    var id = this.id;
    id = id.substr(id.lastIndexOf("-")+1);
    var windowID = "js-window-" + id;
    var popWindow = document.getElementById(windowID);
  if (!this.classList.contains("inactive")) {
    this.classList.add("inactive");
    popWindow.classList.add("active-window");
  }
  else {
    this.classList.remove("inactive");
    popWindow.classList.remove("active-window");
  }
}



function openClose() {
  var openButtons = document.querySelectorAll(".js-opener");
  for (var i = 0; i < openButtons.length; i++) {
    openButtons[i].addEventListener("click", openWindow);
  }
}



openClose();


// open close for degrees and certs

function openClass() {
    var id = this.id;
    id = id.substr(id.lastIndexOf("-")+1);
    var windowID = "js-class-popup-window-" + id;
    var popWindow = document.getElementById(windowID);
    popWindow.classList.add("active-window");
    document.body.classList.add("body-inactive");

}

function closeClass() {
  var closeID = this.id;
  console.log(closeID);
  closeID = closeID.substr(closeID.lastIndexOf("-")+1);
  
  var windowID = "js-class-popup-window-" + closeID;
  var popWindow = document.getElementById(windowID);
  popWindow.classList.remove("active-window");
  document.body.classList.remove("body-inactive");
}



function classOpenClose() {
  var openButtons = document.querySelectorAll(".class-popup");
  for (var i = 0; i < openButtons.length; i++) {
    openButtons[i].addEventListener("click", openClass);
  }

  var closeButtons = document.querySelectorAll(".class-popup-window-close");
  for (var j = 0; j < closeButtons.length; j++) {
    closeButtons[j].addEventListener("click", closeClass);
  }
}

classOpenClose();

});











