/*
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
jQuery(window).load(function() {
    var galleryImgs = jQuery('.videos-items-grid-thumb');
    if (galleryImgs.length > 0) {
        galleryImgs.each(function(index) {
            var parent = jQuery(this).parent().parent(".videos-grid-item, .videos-list-item, .playlists-list-item");
            var container = parent.height()/2;
            var margin = (container - (this.height/2));
            jQuery(this).css({'margin-top': margin + 'px'});
        });
    }
	
	jQuery('.miwovideos_iframe_youtube').height(jQuery('.miwovideos_iframe_youtube').width()*0.5625);
});

