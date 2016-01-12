function scrollToPosition(){
    jQuery(document).ready(function(){
    	// 'catTopPosition' is the amount of pixels #cat
    	// is from the top of the document
    	// When #scroll is clicked
    	jQuery('#text-scroll').click(function(){
        	var catTopPosition = jQuery('[id$=item-type-metadata-text]').offset().top;
    		// Scroll down to 'catTopPosition'
    		jQuery('html, body').animate({scrollTop:catTopPosition - 50}, 'slow');
    		// Stop the link from acting like a normal anchor link
    		return false;
    	});
    });
}


function toggleSlides(){
    jQuery('.toggler').click(function(e){
        var id=jQuery(this).attr('id');
        var widgetId=id.substring(id.indexOf('-')+1,id.length);
        jQuery('#'+widgetId).slideToggle("fast");
        jQuery(this).toggleClass('sliderExpanded');
        jQuery('.closeSlider').click(function(){
            jQuery(this).parent().hide('slow');
            var relatedToggler='toggler-'+jQuery(this).parent().attr('id');
            jQuery('#'+relatedToggler).removeClass('sliderExpanded');
        });
    });
    
    jQuery('#slidetoggle.down').click(function(e){
        jQuery('.slider').slideUp(200);
        jQuery(this).hide();
        jQuery('#slidetoggle.up').show();
    });
    
    jQuery('#slidetoggle.up').click(function(e){
        jQuery('.slider').slideDown(200);
        jQuery(this).hide();
        jQuery('#slidetoggle.down').show();
    });
};

function infoMenu(){
    jQuery(document).ready(function () {
        jQuery(".hoverli").hover(function () {
            jQuery('ul.file_menu').slideDown('fast');
        },
        function () {
            jQuery('ul.file_menu').slideUp('fast');
        }
    );
        jQuery(".file_menu li").hover(function () {
            jQuery(this).children("ul").slideDown('fast');
        },
        function () {
            jQuery(this).children("ul").slideUp('fast');
        }
    );
    });
};

jQuery(function(){
    toggleSlides();
    infoMenu();
    scrollToPosition();
});