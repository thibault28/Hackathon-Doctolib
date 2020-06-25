/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "../css/bootstrap.scss";
import "../css/app.scss";

const $ = require("jquery");
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require("bootstrap");

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
$(".context button").click(function(event) {



  location.href = "/appointment/"+id+"/"+url;
  
});
$(document).click(function() {
  if ($(".context").is(":hover") == false) {
    $(".context").fadeOut("fast");
  };
});