/* From https://css-tricks.com/snippets/jquery/smooth-scrolling/ 

What it does: animates a smooth scroll when a link 
to another part of the same page is clicked. */

$(function() {
	$('a[href*=#]:not([href=#])').click(function() {
    	if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
    		// Get anchor of the current path
      		var target = $(this.hash);
      		target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
     		if (target.length) {
        		$('html,body').animate({
        			scrollTop: target.offset().top
        		}, 500);
        		return false;
      		}
    	}
  	});
});