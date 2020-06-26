/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */


const $ = require("jquery");

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
//import $ from "jquery";

var id;
var url = location.href;
url = url.toString();
url = url.replace(new RegExp("/", "g"),'-');


$(document).ready(function () {

  $(".context")
    .hide();
    
  
});
$("#myTable td button").contextmenu(function(event) {
  event.preventDefault();

  id = $(this).attr('id');

  $(".context")
    .show()
    .css({
      top: event.pageY,
      left: event.pageX
    });
});
$(".context button.delete").click(function(event) {

  location.href = "/appointment/delete/"+id+"/"+url;
  
});

$(".context button.info-medecin").click(function(event) {

  window.open("/appointment/pdf/"+id,'_blank');
  window.location.replace(window.location.href);
  
});

$(document).click(function() {
  if ($(".context").is(":hover") == false) {
    $(".context").fadeOut("fast");
  };
});