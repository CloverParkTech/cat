// this is the custom lightbox for the class-schedule popups. 
// when a course title is clicked, it opens a lightbox window with the description.
// create pure js document ready function. 
// this should probably move to a base js file if I add more functions

//this needs to be cleaned up. don't need all this status nonsense. This can be done more simply, I think
function ready(fn) {
    if (document.readyState != 'loading') {
      fn();
    } else if (document.addEventListener) {
      document.addEventListener('DOMContentLoaded', fn);
    } else {
      document.attachEvent('onreadystatechange', function() {
        if (document.readyState != 'loading') fn();
      });
    }
  }
  // call ready function
ready(function() {
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

