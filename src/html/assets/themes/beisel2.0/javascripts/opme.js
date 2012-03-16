var swipeOptions= {
	swipeLeft:swipeLeft,
	swipeRight:swipeRight,
	threshold:100
}

//swipe controls
function swipeLeft(event, direction)
{
  if(typeof newBG === "undefined" || newBG === "undefined") {
    $('.carousel').carousel('next');
  }
}
function swipeRight(event, direction)
{
  if(typeof newBG === "undefined" || newBG === "undefined") {
    $('.carousel').carousel('prev');
  }
}

$(function() { 
	$('.dropdown-toggle').dropdown();
	$('.carousel').carousel({interval: 7000});
	$('.carousel').swipe(swipeOptions);
});
