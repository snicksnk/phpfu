jQuery(document).ready(function() {

    jQuery('a[href^="#"]').click(function(){
        var el = jQuery(this).attr('href');
        jQuery('body').animate({
            scrollTop: jQuery(el).offset().top}, 500);
        return false;
    });

});