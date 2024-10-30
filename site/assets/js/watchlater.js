/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
jQuery(document).ready(function() {
    var body = jQuery("body");
    body.on('click', '.video_watch_later_button, .vjs-watchlater-control', function () {
        var id = null, video_id;
        var selector = jQuery(this).attr('class');
        if (selector.indexOf('watchlater-success') >= 0) {
            return;
        }
        if (selector == 'vjs-control vjs-watchlater-control vjs-menu-button') {
            video_id = jQuery('video').attr('id').match(/\d+/)[0];
        } else {
            var match = jQuery(this).children().attr("class").match(/(\d+)/g);
            if (match) {
                id = match[0];
            }
            video_id = jQuery(this).attr("class").match(/(\d+)/g)[0];
        }
        jQuery.ajax({
            url : miwiajaxurl+'?action=miwovideos&view=playlists&format=raw&task=addVideoToPlaylist',
            type: 'post',
            data: {playlist_id: id, video_id: video_id},
            dataType: 'json',
            beforeSend: function () {
                // Loading
                jQuery('.deprecated').each(function() {
                    jQuery(this).remove();
                });
                jQuery('head').append('<style class="deprecated">.vjs-default-skin .vjs-watchlater-control:before {font-family: "VideoJS";content: "\\e00a";}}</style>');
                jQuery('head').append('<style class="deprecated">.vjs-control.vjs-watchlater-control:before{-webkit-animation:spin 1.5s linear infinite;-moz-animation:spin 1.5s linear infinite;-o-animation:spin 1.5s linear infinite;animation:spin 1.5s linear infinite;}@-moz-keyframes spin { 100% { -moz-transform: rotate(360deg); } }@-webkit-keyframes spin { 100% { -webkit-transform: rotate(360deg); } }@-o-keyframes spin { 100% { -o-transform: rotate(360deg); } }@keyframes spin { 100% { -webkit-transform: rotate(360deg); transform:rotate(360deg); } }</style>');
            },
            success: function (json) {
                if (json['success']) {
                    if (selector == 'vjs-control vjs-watchlater-control vjs-menu-button') {
                        jQuery('.vjs-watchlater-control').addClass('watchlater-success');
                        jQuery('.deprecated').each(function() {
                            jQuery(this).remove();
                        });
                    } else {
                        var video = jQuery(".miwovideos_video" + video_id);
                        video.removeClass("video_watch_later_button");
                        video.addClass("video_added_button");
                        video.children(".miwovideos_watch_later" + id).removeClass("video_watch_later");
                        video.children(".miwovideos_watch_later" + id).addClass("video_added");
                    }
                }
                if (json['redirect']) {
                    location = json['redirect'];
                }
                if (json['error']) {
                    jQuery('.playlist_notification').html('<div class="miwovideos_warning" style="display: none;"><div class="miwovideos_warning_image"></div>' + json['error'] + '</div>');
                    var warning = jQuery('.miwovideos_warning');
                    warning.fadeIn('slow');
                    warning.delay(5000).fadeOut('slow');
                }
            }
        });
    });
    body.on('click', '.video_added_button, .watchlater-success', function () {
        var selector = jQuery(this).attr('class');
        var id = null, video_id;
        if (selector == 'vjs-control vjs-watchlater-control vjs-menu-button watchlater-success') {
            var regex = new RegExp("[\\?&]video_id=([^&#]*)"),
                result = regex.exec(location.search);
            video_id = decodeURIComponent(result[1].replace(/\+/g, " "));
        } else {
            id = jQuery(this).children().attr("class").match(/(\d+)/g)[0];
            video_id = jQuery(this).attr("class").match(/(\d+)/g)[0];
        }
        jQuery.ajax({
            url : miwiajaxurl+'?action=miwovideos&view=playlists&format=raw&task=removeVideoFromPlaylist',
            type: 'post',
            data: {playlist_id: id, video_id: video_id},
            dataType: 'json',
            beforeSend: function () {
                // Loading
                jQuery('.deprecated').each(function() {
                    jQuery(this).remove();
                });
                jQuery('head').append('<style class="deprecated">div.vjs-control.vjs-watchlater-control.watchlater-success:before {font-family: "VideoJS";content: "\\e00a";}}</style>');
                jQuery('head').append('<style class="deprecated">.vjs-control.vjs-watchlater-control:before{-webkit-animation:spin 1.5s linear infinite;-moz-animation:spin 1.5s linear infinite;-o-animation:spin 1.5s linear infinite;animation:spin 1.5s linear infinite;}@-moz-keyframes spin { 100% { -moz-transform: rotate(360deg); } }@-webkit-keyframes spin { 100% { -webkit-transform: rotate(360deg); } }@-o-keyframes spin { 100% { -o-transform: rotate(360deg); } }@keyframes spin { 100% { -webkit-transform: rotate(360deg); transform:rotate(360deg); } }</style>');
            },
            success: function (json) {
                if (json['success']) {
                    if (selector == 'vjs-control vjs-watchlater-control vjs-menu-button watchlater-success') {
                        jQuery('.vjs-watchlater-control').removeClass('watchlater-success');
                        jQuery('.deprecated').each(function() {
                            jQuery(this).remove();
                        });
                    } else {
                        var video = jQuery(".miwovideos_video"+video_id);
                        video.removeClass("video_added_button");
                        video.addClass("video_watch_later_button");
                        video.children(".miwovideos_watch_later"+id).removeClass("video_added");
                        video.children(".miwovideos_watch_later"+id).addClass("video_watch_later");
                    }
                }
                if (json['redirect']) {
                    location = json['redirect'];
                }
                if (json['error']) {
                    jQuery('.playlist_notification').html('<div class="miwovideos_warning" style="display: none;"><div class="miwovideos_warning_image"></div>' + json['error'] + '</div>');
                    var warning = jQuery('.miwovideos_warning');
                    warning.fadeIn('slow');
                    warning.delay(5000).fadeOut('slow');
                }
            }
        });
    });
});