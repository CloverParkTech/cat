
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
    document.documentElement.classList.add("no-scroll");
    var siteFooter = document.getElementById("js-footer");
    siteFooter.classList.add("footer-hidden");

    // need to make this not global
    window.scrollPos = document.body.scrollTop;
    console.log(window.scrollPos);


}

function closeClass(scrollPos) {
  var closeID = this.id;
  console.log(closeID);
  closeID = closeID.substr(closeID.lastIndexOf("-")+1);
  var windowID = "js-class-popup-window-" + closeID;
  var popWindow = document.getElementById(windowID);
  popWindow.classList.remove("active-window");
  var jsWrapper = document.getElementById("js-overflow-wrapper");
  document.body.classList.remove("body-inactive");
  var siteFooter = document.getElementById("js-footer");
  document.documentElement.classList.remove("no-scroll");
    siteFooter.classList.remove("footer-hidden");

    document.body.scrollTop = window.scrollPos;
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












// this is the custom lightbox for the class-schedule popups. 
// when a course title is clicked, it opens a lightbox window with the description.
// create pure js document ready function. 
// this should probably move to a base js file if I add more functions

//this needs to be cleaned up. don't need all this status nonsense. This can be done more simply, I think
/*
document.addEventListener('DOMContentLoaded', function () {
      var status = 0;
      var popupWindowActiveClass = "class-popup-window-active";
      // create function to open windows when a popup link is clicked
      function popupClick(el) {
        el.onclick = function() {
         if (status === 0) {
            var id = this.id;
            id = id.substr(id.lastIndexOf("-")+1);
            console.log(id);
            var windowId = "js-class-popup-window-" + id;
            console.log(windowId);
            var closeButtonId = "js-class-popup-window-close-" + id;
            var popupWindow = document.getElementById(windowId);
            popupWindow.classList.add(popupWindowActiveClass);
             status = 1;
          }
        };
      }

      function closeWindow(foo) {
        foo.onclick = function() {
            if (status === 1) {
              var openWindows = document.getElementsByClassName(
                popupWindowActiveClass);
              for (var h = 0; h < openWindows.length; h++) {
                openWindow = openWindows[h];
                openWindow.classList = openWindow.classList.remove(
                  popupWindowActiveClass);
              }
            }
            status = 0;
          };
      }
          // use popClick function for each element with class of 'class-popup'

       function closeWindows() {
        var closeButtons = document.getElementsByClassName('class-popup-window-close');
        for (var j = 0; j < closeButtons.length; j++) {
          var closeButton = closeButtons[j];
          closeWindow(closeButton);
        }
    }


    function openWindows() {
        var popupLinks = document.getElementsByClassName('class-popup');
    
        for (var i = 0; i < popupLinks.length; i++) {
          var popupLink = popupLinks[i];
          popupClick(popupLink);
        }
    }

    openWindows();
    closeWindows();
     






        });
*/

