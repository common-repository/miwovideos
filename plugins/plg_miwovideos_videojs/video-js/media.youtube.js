/* global videojs, YT */
/* jshint browser: true */

(function() {
    /**
     * @fileoverview YouTube Media Controller - Wrapper for YouTube Media API
     */

    /**
     * YouTube Media Controller - Wrapper for YouTube Media API
     * @param {videojs.Player|Object} player
     * @param {Object=} options
     * @param {Function=} ready
     * @constructor
     */

    function addEventListener(element, event, cb) {
        if(!element.addEventListener) {
            element.attachEvent(event, cb);
        } else {
            element.addEventListener(event, cb, true);
        }
    }

    videojs.Youtube = videojs.MediaTechController.extend({
        /** @constructor */
        init: function(player, options, ready) {
            // Save this for internal usage
            this.player_ = player;

            // No event is triggering this for YouTube
            this['featuresProgressEvents'] = false;
            this['featuresTimeupdateEvents'] = false;
            // Enable rate changes
            this['featuresPlaybackRate'] = true;

            this['featuresNativeTextTracks'] = true;

            videojs.MediaTechController.call(this, player, options, ready);

            this.isIos = /(iPad|iPhone|iPod)/g.test( navigator.userAgent );
            this.isAndroid = /(Android)/g.test( navigator.userAgent );
            //used to prevent play events on IOS7 and Android > 4.2 until the user has clicked the player
            this.playVideoIsAllowed = !(this.isIos || this.isAndroid);

            // autoplay is disabled for mobile
            if (this.isIos || this.isAndroid) {
                this.player_.options()['autoplay'] = false;
            }

            // Copy the JavaScript options if they exists
            if(typeof options['source'] !== 'undefined') {
                for(var key in options['source']) {
                    if(options['source'].hasOwnProperty(key)) {
                        player.options()[key] = options['source'][key];
                    }
                }
            }

            this.player_.options()['playbackRates'] = [];

            this.userQuality = videojs.Youtube.convertQualityName(player.options()['quality']);

            this.playerEl_ = player.el();
            this.playerEl_.className += ' vjs-youtube';

            // Create the Quality button
            this.qualityButton = document.createElement('div');
            this.qualityButton.setAttribute('class', 'vjs-quality-button vjs-menu-button vjs-control');
            this.qualityButton.setAttribute('tabindex', 0);

            var qualityContent = document.createElement('div');
            qualityContent.setAttribute('class', 'vjs-control-content');
            this.qualityButton.appendChild(qualityContent);

            this.qualityTitle = document.createElement('span');
            this.qualityTitle.setAttribute('class', 'vjs-control-text');
            qualityContent.appendChild(this.qualityTitle);

            if(player.options()['quality'] !== 'undefined') {
                setInnerText(this.qualityTitle, player.options()['quality'] || 'auto');
            }

            var qualityMenu = document.createElement('div');
            qualityMenu.setAttribute('class', 'vjs-menu');
            qualityContent.appendChild(qualityMenu);

            this.qualityMenuContent = document.createElement('ul');
            this.qualityMenuContent.setAttribute('class', 'vjs-menu-content');
            qualityMenu.appendChild(this.qualityMenuContent);

            this.id_ = this.player_.id() + '_youtube_api';

            this.el_ = videojs.Component.prototype.createEl('iframe', {
                id: this.id_,
                className: 'vjs-tech',
                scrolling: 'no',
                marginWidth: 0,
                marginHeight: 0,
                frameBorder: 0
            });

            this.el_.setAttribute('allowFullScreen', '');
            this.el_.setAttribute('onload', 'iframeReady(this)');

            this.playerEl_.insertBefore(this.el_, this.playerEl_.firstChild);

            if(/MSIE (\d+\.\d+);/.test(navigator.userAgent)) {
                var ieVersion = Number(RegExp.$1);
                this.addIframeBlocker(ieVersion);
            } else if(!/(iPad|iPhone|iPod|Android)/g.test(navigator.userAgent)) {
                // the pointer-events: none block the mobile player
                this.el_.className += ' onDesktop';
                this.addIframeBlocker();
            }

            this.parseSrc(player.options()['src']);

            this.playOnReady = this.player_.options()['autoplay'] && this.playVideoIsAllowed;
            this.forceHTML5 = !!(
                typeof this.player_.options()['forceHTML5'] === 'undefined' ||
                this.player_.options()['forceHTML5'] === true
            );

            this.updateIframeSrc();

            var self = this;

            player.ready(function() {
                if (self.player_.options()['controls']) {
                    var controlBar = self.playerEl_.querySelectorAll('.vjs-control-bar')[0];
                    if (controlBar) {
                        controlBar.appendChild(self.qualityButton);
                    }
                }

                if(self.playOnReady && !self.player_.options()['ytcontrols']) {
                    if(typeof self.player_.loadingSpinner !== 'undefined') {
                        self.player_.loadingSpinner.show();
                    }
                    if(typeof self.player_.bigPlayButton !== 'undefined') {
                        self.player_.bigPlayButton.hide();
                    }
                }

                player.trigger('loadstart');
            });

            this.on('dispose', function() {
                if(this.ytplayer) {
                    this.ytplayer.destroy();
                }

                if(!this.player_.options()['ytcontrols']) {
                    this.player_.off('waiting', this.bindedWaiting);
                }

                // Remove the poster
                this.playerEl_.querySelectorAll('.vjs-poster')[0].style.backgroundImage = 'none';

                // If still connected to the DOM, remove it.
                if(this.el_.parentNode) {
                    this.el_.parentNode.removeChild(this.el_);
                }

                // Get rid of the created DOM elements
                if (this.qualityButton.parentNode) {
                    this.qualityButton.parentNode.removeChild(this.qualityButton);
                }

                if(typeof this.player_.loadingSpinner !== 'undefined') {
                    this.player_.loadingSpinner.hide();
                }
                if(typeof this.player_.bigPlayButton !== 'undefined') {
                    this.player_.bigPlayButton.hide();
                }

                if(this.iframeblocker) {
                    this.playerEl_.removeChild(this.iframeblocker);
                }
            });
        }
    });

    // Tries to get the highest resolution thumbnail available for the video
    videojs.Youtube.prototype.loadThumbnailUrl = function(id, callback){

        var uri = 'https://img.youtube.com/vi/' + id + '/maxresdefault.jpg';
        var fallback = 'https://img.youtube.com/vi/' + id + '/0.jpg';

        try{
            var image = new Image();
            image.onload = function(){
                // Onload may still be called if YouTube returns the 120x90 error thumbnail
                if('naturalHeight' in this){
                    if(this.naturalHeight <= 90 || this.naturalWidth <= 120) {
                        this.onerror();
                        return;
                    }
                } else if(this.height <= 90 || this.width <= 120) {
                    this.onerror();
                    return;
                }

                callback(uri);
            };
            image.onerror = function(){
                callback(fallback);
            };
            image.src = uri;
        }
        catch(e){ callback(fallback); }
    };

    videojs.Youtube.prototype.updateIframeSrc = function() {
        var fullscreenControls = (
            typeof this.player_.options()['ytFullScreenControls'] !== 'undefined' &&
            !this.player_.options()['ytFullScreenControls']
        ) ? 0 : 1;

        var params = {
            enablejsapi: 1,
            /*jshint -W106 */
            iv_load_policy: 3,
            /*jshint +W106 */
            playerapiid: this.id(),
            disablekb: 1,
            wmode: 'transparent',
            controls: (this.player_.options()['ytcontrols']) ? 1 : 0,
            fs: fullscreenControls,
            html5: (this.player_.options()['forceHTML5']) ? 1 : null,
            playsinline: (this.player_.options()['playsInline']) ? 1 : 0,
            showinfo: 0,
            rel: 0,
            autoplay: (this.playOnReady) ? 1 : 0,
            loop: (this.player_.options()['loop']) ? 1 : 0,
            list: this.playlistId,
            vq: this.userQuality,
            origin: window.location.protocol + '//' + window.location.host
        };

        var isLocalProtocol = window.location.protocol === 'file:' || window.location.protocol === 'app:';

        // When running with no Web server, we can't specify the origin or it will break the YouTube API messages
        if(isLocalProtocol) {
            delete params.origin;
        }

        // Delete unset properties
        for(var prop in params) {
            if(params.hasOwnProperty(prop) &&
                ( typeof params[ prop ] === 'undefined' || params[ prop ] === null )
            ) {
                delete params[ prop ];
            }
        }
        var self = this;

        if(!this.videoId && !this.playlistId) {
            this.el_.src = 'about:blank';
            setTimeout(function() {
                self.triggerReady();
            }, 500);
        } else {
            this.el_.src = 'https://www.youtube.com/embed/' +
                (this.videoId || 'videoseries') + '?' + videojs.Youtube.makeQueryString(params);

            if(this.player_.options()['ytcontrols']) {
                // Disable the video.js controls if we use the YouTube controls
                this.player_.controls(false);
            } else if(this.videoId && (typeof this.player_.poster() === 'undefined' || this.player_.poster().length === 0)) {
                // Wait here because the tech is still null in constructor
                setTimeout(function() {
                    self.loadThumbnailUrl(self.videoId, function(url){
                        self.player_.poster(url);
                    });
                }, 100);
            }

            this.bindedWaiting = function() {
                self.onWaiting();
            };

            this.player_.on('waiting', this.bindedWaiting);

            if(videojs.Youtube.apiReady) {
                this.loadYoutube();
            } else {
                // Add to the queue because the YouTube API is not ready
                videojs.Youtube.loadingQueue.push(this);

                // Load the YouTube API if it is the first YouTube video
                if(!videojs.Youtube.apiLoading) {
                    var tag = document.createElement('script');
                    tag.onerror = function(e) {
                        self.onError(e);
                    };
                    tag.src = 'https://www.youtube.com/iframe_api';
                    var firstScriptTag = document.getElementsByTagName('script')[0];
                    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
                    videojs.Youtube.apiLoading = true;
                }
            }
        }
    };

    videojs.Youtube.prototype.onWaiting = function(/*e*/) {
        // Make sure to hide the play button while the spinner is there
        if(typeof this.player_.bigPlayButton !== 'undefined') {
            this.player_.bigPlayButton.hide();
        }
    };

    videojs.Youtube.prototype.addIframeBlocker = function(ieVersion) {
        this.iframeblocker = videojs.Component.prototype.createEl('div');

        this.iframeblocker.className = 'iframeblocker';

        this.iframeblocker.style.position = 'absolute';
        this.iframeblocker.style.left = 0;
        this.iframeblocker.style.right = 0;
        this.iframeblocker.style.top = 0;
        this.iframeblocker.style.bottom = 0;

        // Odd quirk for IE8 (doesn't support rgba)
        if(ieVersion && ieVersion < 9) {
            this.iframeblocker.style.opacity = 0.01;
        } else {
            this.iframeblocker.style.background = 'rgba(255, 255, 255, 0.01)';
        }

        var self = this;
        addEventListener(this.iframeblocker, 'mousemove', function(e) {
            if(!self.player_.userActive()) {
                self.player_.userActive(true);
            }

            e.stopPropagation();
            e.preventDefault();
        });

        addEventListener(this.iframeblocker, 'click', function(/*e*/) {
            if(self.paused()) {
                self.play();
            } else {
                self.pause();
            }
        });

        this.playerEl_.insertBefore(this.iframeblocker, this.el_.nextSibling);
    };

    videojs.Youtube.prototype.parseSrc = function(src) {
        this.srcVal = src;

        if(src) {
            // Regex to parse the video ID
            var regId = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
            var match = src.match(regId);

            if(match && match[2].length === 11) {
                this.videoId = match[2];
            } else {
                this.videoId = null;
            }

            // Regex to parse the playlist ID
            var regPlaylist = /[?&]list=([^#\&\?]+)/;
            match = src.match(regPlaylist);

            if(match !== null && match.length > 1) {
                this.playlistId = match[1];
            } else {
                // Make sure their is no playlist
                if(this.playlistId) {
                    delete this.playlistId;
                }
            }

            // Parse video quality option
            var regVideoQuality = /[?&]vq=([^#\&\?]+)/;
            match = src.match(regVideoQuality);

            if(match !== null && match.length > 1) {
                this.userQuality = match[1];
                videojs.Youtube.appendQualityLabel(this.qualityTitle, this.userQuality);
            }
        }
    };

    videojs.Youtube.prototype.src = function(src) {
        if(typeof src !== 'undefined') {
            this.parseSrc(src);

            if(this.el_.src === 'about:blank') {
                this.updateIframeSrc();
                return;
            }

            delete this.defaultQuality;

            if(this.videoId !== null) {
                if(this.player_.options()['autoplay'] && this.playVideoIsAllowed) {
                    this.ytplayer.loadVideoById({
                        videoId: this.videoId,
                        suggestedQuality: this.userQuality
                    });
                } else {
                    this.ytplayer.cueVideoById({
                        videoId: this.videoId,
                        suggestedQuality: this.userQuality
                    });
                }

                var self = this;
                this.loadThumbnailUrl(this.videoId, function(url){
                    // Update the poster
                    self.playerEl_.querySelectorAll('.vjs-poster')[0].style.backgroundImage =
                        'url(' + url + ')';
                    self.player_.poster(url);
                });
            }
            /* else Invalid URL */
        }

        return this.srcVal;
    };

    videojs.Youtube.prototype.load = function() {
    };

    videojs.Youtube.prototype.play = function() {
        if(this.videoId !== null) {

            // Make sure to not display the spinner for mobile
            if(!this.player_.options()['ytcontrols']) {
                // Display the spinner until the video is playing by YouTube
                this.player_.trigger('waiting');
            }

            if(this.isReady_) {
                // Sync the player volume with YouTube
                this.ytplayer.setVolume(this.player_.volume() * 100);

                if(this.volumeVal > 0) {
                    this.ytplayer.unMute();
                } else {
                    this.ytplayer.mute();
                }

                if(this.playVideoIsAllowed) {
                    this.ytplayer.playVideo();
                }
            } else {
                this.playOnReady = true;
            }
        }
    };

    videojs.Youtube.prototype.pause = function() {
        if(this.ytplayer) {
            this.ytplayer.pauseVideo();
        }
    };
    videojs.Youtube.prototype.paused = function() {
        return (this.ytplayer) ?
            (this.lastState !== YT.PlayerState.PLAYING && this.lastState !== YT.PlayerState.BUFFERING)
            : true;
    };
    videojs.Youtube.prototype.currentTime = function() {
        return (this.ytplayer && this.ytplayer.getCurrentTime) ? this.ytplayer.getCurrentTime() : 0;
    };
    videojs.Youtube.prototype.setCurrentTime = function(seconds) {
        if (this.lastState === YT.PlayerState.PAUSED) {
            this.timeBeforeSeek = this.currentTime();
        }

        this.ytplayer.seekTo(seconds, true);
        this.player_.trigger('timeupdate');
        this.player_.trigger('seeking');
        this.isSeeking = true;

        // A seek event during pause does not return an event to trigger a seeked event,
        // so run an interval timer to look for the currentTime to change
        if (this.lastState === YT.PlayerState.PAUSED && this.timeBeforeSeek !== seconds) {
            this.checkSeekedInPauseInterval = setInterval( videojs.bind(this, function() {
                if (this.lastState !== YT.PlayerState.PAUSED || !this.isSeeking) {
                    // If something changed while we were waiting for the currentTime to change,
                    //  clear the interval timer
                    clearInterval(this.checkSeekedInPauseInterval);
                } else if (this.currentTime() !== this.timeBeforeSeek) {
                    this.player_.trigger('timeupdate');
                    this.player_.trigger('seeked');
                    this.isSeeking = false;
                    clearInterval(this.checkSeekedInPauseInterval);
                }
            }), 250);
        }
    };

    videojs.Youtube.prototype.playbackRate = function() {
        return (this.ytplayer && this.ytplayer.getPlaybackRate) ? this.ytplayer.getPlaybackRate() : 1.0;
    };

    videojs.Youtube.prototype.setPlaybackRate = function(suggestedRate) {
        if (this.ytplayer && this.ytplayer.setPlaybackRate) {
            this.ytplayer.setPlaybackRate(suggestedRate);
            var self = this;
            setTimeout(function () {
                self.player_.trigger('ratechange');
            }, 100);
        }
    };

    videojs.Youtube.prototype.duration = function() {
        return (this.ytplayer && this.ytplayer.getDuration) ? this.ytplayer.getDuration() : 0;
    };

    videojs.Youtube.prototype.currentSrc = function() {
        return this.srcVal;
    };

    videojs.Youtube.prototype.ended = function() {
        return (this.ytplayer) ? (this.lastState === YT.PlayerState.ENDED) : false;
    };

    videojs.Youtube.prototype.volume = function() {
        if(this.ytplayer && isNaN(this.volumeVal)) {
            this.volumeVal = this.ytplayer.getVolume() / 100.0;
            this.volumeVal = (isNaN(this.volumeVal)) ? 1 : this.volumeVal;
            this.player_.volume(this.volumeVal);
        }

        return this.volumeVal;
    };

    videojs.Youtube.prototype.setVolume = function(percentAsDecimal) {
        if(typeof(percentAsDecimal) !== 'undefined' && percentAsDecimal !== this.volumeVal) {
            this.ytplayer.setVolume(percentAsDecimal * 100.0);
            this.volumeVal = percentAsDecimal;
            this.player_.trigger('volumechange');
        }
    };

    videojs.Youtube.prototype.muted = function() {
        return this.mutedVal;
    };
    videojs.Youtube.prototype.setMuted = function(muted) {
        if(muted) {
            this.storedVolume = this.volumeVal;
            this.ytplayer.mute();
            this.player_.volume(0);
        } else {
            this.ytplayer.unMute();
            this.player_.volume(this.storedVolume);
        }

        this.mutedVal = muted;

        this.player_.trigger('volumechange');
    };

    videojs.Youtube.prototype.buffered = function() {
        if(this.ytplayer && this.ytplayer.getVideoBytesLoaded) {
            var loadedBytes = this.ytplayer.getVideoBytesLoaded();
            var totalBytes = this.ytplayer.getVideoBytesTotal();
            if(!loadedBytes || !totalBytes) {
                return 0;
            }

            var duration = this.ytplayer.getDuration();
            var secondsBuffered = (loadedBytes / totalBytes) * duration;
            var secondsOffset = (this.ytplayer.getVideoStartBytes() / totalBytes) * duration;

            return videojs.createTimeRange(secondsOffset, secondsOffset + secondsBuffered);
        } else {
            return videojs.createTimeRange(0, 0);
        }
    };

    videojs.Youtube.prototype.supportsFullScreen = function() {
        if (typeof this.el_.webkitEnterFullScreen === 'function') {

            // Seems to be broken in Chromium/Chrome && Safari in Leopard
            if (/Android/.test(videojs.USER_AGENT) || !/Chrome|Mac OS X 10.5/.test(videojs.USER_AGENT)) {
                return true;
            }
        }
        return false;
    };

    // YouTube is supported on all platforms
    videojs.Youtube.isSupported = function() {
        return true;
    };

    // You can use video/youtube as a media in your HTML5 video to specify the source
    videojs.Youtube.canPlaySource = function(srcObj) {
        return (srcObj.type === 'video/youtube');
    };

    // Always can control the volume
    videojs.Youtube.canControlVolume = function() {
        return true;
    };

    ////////////////////////////// YouTube specific functions //////////////////////////////

    // All videos created before YouTube API is loaded
    videojs.Youtube.loadingQueue = [];

    // Create the YouTube player
    videojs.Youtube.prototype.loadYoutube = function() {
        var self = this;
        this.ytplayer = new YT.Player(this.id_, {
            events: {
                onReady: function(e) {
                    e.target.vjsTech.onReady();
                    self.player_.trigger('ratechange');
                },
                onStateChange: function(e) {
                    e.target.vjsTech.onStateChange(e.data);
                },
                onPlaybackQualityChange: function(e) {
                    e.target.vjsTech.onPlaybackQualityChange(e.data);
                },
                onError: function(e) {
                    var err = videojs.Youtube.errorMessages[e.data] || 'Unknown YouTube error, code ' + e.data;
                    e.target.vjsTech.onError(err);
                }
            }
        });

        this.ytplayer.vjsTech = this;
    };

    // See https://developers.google.com/youtube/iframe_api_reference#Events
    videojs.Youtube.errorMessages = {
        2: 'The request contains an invalid parameter value.',
        // For example, this error occurs if you specify a video ID that
        //  does not have 11 characters, or if the video ID contains
        //  invalid characters, such as exclamation points or asterisks.
        5: 'The requested content cannot be played in an HTML5 player.',
        // ... or another error related to the HTML5 player has occurred
        100: 'The video requested was not found.',
        // This error occurs when a video has been removed (for any reason)
        //  or has been marked as private.
        101: 'The owner of the requested video does not allow it to be played in embedded players.',
        150: 'The owner of the requested video does not allow it to be played in embedded players.'
    };


    // Transform a JavaScript object into URL params
    videojs.Youtube.makeQueryString = function(args) {
        var array = ['modestbranding=1'];
        for(var key in args) {
            if(args.hasOwnProperty(key)) {
                array.push(key + '=' + args[key]);
            }
        }

        return array.join('&');
    };

    // Called when YouTube API is ready to be used
    window.onYouTubeIframeAPIReady = function() {
        var yt;
        while((yt = videojs.Youtube.loadingQueue.shift())) {
            yt.loadYoutube();
        }
        videojs.Youtube.loadingQueue = [];
        videojs.Youtube.apiReady = true;
    };

    videojs.Youtube.prototype.onReady = function() {
        this.isReady_ = true;
        this.triggerReady();

        this.player_.options()['playbackRates'] = this.ytplayer.getAvailablePlaybackRates();
        if (this.player_.controlBar.playbackRateMenuButton) {
            this.player_.controlBar.playbackRateMenuButton.update();
        }

        this.player_.trigger('loadedmetadata');

        // The duration is loaded so we might as well fire off the timeupdate and duration events
        // this allows for the duration of the video (timeremaining) to be displayed if styled
        // to show the control bar initially. This gives the user the ability to see how long the video
        // is before clicking play
        this.player_.trigger('durationchange');
        this.player_.trigger('timeupdate');

        // Let the player take care of itself as soon as the YouTube is ready
        // The loading spinner while waiting for the tech would be impossible otherwise
        if (typeof this.player_.loadingSpinner !== 'undefined' && !this.isIos && !this.isAndroid) {
            this.player_.loadingSpinner.hide();
        }

        if(this.player_.options()['muted']) {
            this.setMuted(true);
        }

        // Set the poster of the first video of the playlist if not specified
        if (!this.videoId && this.playlistId) {
            this.videoId = this.ytplayer.getPlaylist()[0];
            var self = this;
            this.loadThumbnailUrl(this.videoId, function(url){
                self.player_.poster(url);
            });
        }

        // Play ASAP if they clicked play before it's ready
        if(this.playOnReady) {
            this.playOnReady = false;
            this.play();
        }
    };


    videojs.Youtube.prototype.updateCaptions = function() {
        this.ytplayer.loadModule('captions');
        this.ytplayer.loadModule('cc');

        var options = this.ytplayer.getOptions();
        // The name of the captions module: 'captions' for html5 or 'cc' for flash
        var cc = options.indexOf('captions') >= 0? 'captions'
            : (options.indexOf('cc') >= 0? 'cc' : null);

        if(cc !== null && !this.tracked_){

            var tracks = this.ytplayer.getOption(cc, 'tracklist');

            if(tracks && tracks.length > 0){

                var tt;
                for(var i = 0; i < tracks.length; i++){
                    tt = this.addTextTrack('captions', tracks[i].displayName, tracks[i].languageCode);
                }

                var self = this;
                this.textTracks().on('change', function(){
                    var code = null;
                    for(var i = 0; i < this.length; i++){
                        if(this[i].mode === 'showing'){
                            code = this[i].language;
                            break;
                        }
                    }

                    if(code !== null){
                        self.ytplayer.setOption(cc, 'track', {'languageCode': code});
                    }
                    else{
                        self.ytplayer.setOption(cc, 'track', {});
                    }

                });

                this.tracked_ = true;
            }
        }
    };

    videojs.Youtube.prototype.updateQualities = function() {

        function setupEventListener(el) {
            addEventListener(el, 'click', function() {
                var quality = this.getAttribute('data-val');
                self.ytplayer.setPlaybackQuality(quality);

                self.userQuality = quality;
                videojs.Youtube.appendQualityLabel(self.qualityTitle, quality);

                var selected = self.qualityMenuContent.querySelector('.vjs-selected');
                if(selected) {
                    videojs.Youtube.removeClass(selected, 'vjs-selected');
                }

                videojs.Youtube.addClass(this, 'vjs-selected');
            });
        }

        var qualities = this.ytplayer.getAvailableQualityLevels();
        var self = this;

        if(qualities.indexOf(this.userQuality) < 0) {
            videojs.Youtube.appendQualityLabel(self.qualityTitle, this.defaultQuality);
        }

        if(qualities.length === 0) {
            this.qualityButton.style.display = 'none';
        } else {
            this.qualityButton.style.display = '';

            while(this.qualityMenuContent.hasChildNodes()) {
                this.qualityMenuContent.removeChild(this.qualityMenuContent.lastChild);
            }

            for(var i = 0; i < qualities.length; ++i) {
                var el = document.createElement('li');
                el.setAttribute('class', 'vjs-menu-item');
                el.setAttribute('data-val', qualities[i]);
                videojs.Youtube.appendQualityLabel(el, qualities[i]);
                if(qualities[i] === this.quality) {
                    videojs.Youtube.addClass(el, 'vjs-selected');
                }
                setupEventListener(el);

                this.qualityMenuContent.appendChild(el);
            }
        }
    };

    videojs.Youtube.prototype.onStateChange = function(state) {
        if(state !== this.lastState) {
            switch(state) {
                case -1:
                    this.player_.trigger('durationchange');
                    break;

                case YT.PlayerState.ENDED:
                    var stopPlaying = true;

                    // Stop the playlist when it is starting over
                    if (this.playlistId && !this.player_.options()['loop']) {
                        stopPlaying = this.ytplayer.getPlaylistIndex() === 0;
                    }

                    if (stopPlaying) {
                        // Replace YouTube play button by our own
                        if(!this.player_.options()['ytcontrols']) {
                            this.playerEl_.querySelectorAll('.vjs-poster')[0].style.display = 'block';
                            if(typeof this.player_.bigPlayButton !== 'undefined') {
                                this.player_.bigPlayButton.show();
                            }
                        }

                        this.player_.trigger('pause');
                        this.player_.trigger('ended');
                    }

                    break;

                case YT.PlayerState.PLAYING:
                    this.playerEl_.querySelectorAll('.vjs-poster')[0].style.display = 'none';

                    this.playVideoIsAllowed = true;
                    this.updateQualities();
                    this.updateCaptions();
                    this.player_.trigger('timeupdate');
                    this.player_.trigger('durationchange');
                    this.player_.trigger('playing');
                    this.player_.trigger('play');

                    if (this.isSeeking) {
                        this.player_.trigger('seeked');
                        this.isSeeking = false;
                    }
                    break;

                case YT.PlayerState.PAUSED:
                    this.player_.trigger('pause');
                    break;

                case YT.PlayerState.BUFFERING:
                    this.player_.trigger('timeupdate');

                    // Make sure to not display the spinner for mobile
                    if(!this.player_.options()['ytcontrols']) {
                        this.player_.trigger('waiting');
                    }
                    break;

                case YT.PlayerState.CUED:
                    break;
            }

            this.lastState = state;
        }
    };

    videojs.Youtube.convertQualityName = function(name) {
        switch(name) {
            case '144p':
                return 'tiny';

            case '240p':
                return 'small';

            case '360p':
                return 'medium';

            case '480p':
                return 'large';

            case '720p':
                return 'hd720';

            case '1080p':
                return 'hd1080';

            case '1440p':
                return 'hd1440';

            case '2160p':
                return 'hd2160';
        }

        return 'auto';
    };

    videojs.Youtube.parseQualityName = function(name) {
        switch(name) {
            case 'tiny':
                return '144p';

            case 'small':
                return '240p';

            case 'medium':
                return '360p';

            case 'large':
                return '480p';

            case 'hd720':
                return '720p';

            case 'hd1080':
                return '1080p';

            case 'hd1440':
                return '1440p';

            case 'hd2160':
                return '2160p';
        }

        return 'auto';
    };

    videojs.Youtube.appendQualityLabel = function(element, quality) {
        setInnerText(element, videojs.Youtube.parseQualityName(quality));

        var label = document.createElement('span');
        label.setAttribute('class', 'vjs-hd-label');

        switch(quality) {
            case 'hd720':
            case 'hd1080':
            case 'hd1440':
                /*setInnerText(label, 'HD');
                element.appendChild(label);*/
                break;

            case 'hd2160':
                setInnerText(label, '4K');
                element.appendChild(label);
                break;
        }
    };

    videojs.Youtube.prototype.onPlaybackQualityChange = function(quality) {
        if(typeof this.defaultQuality === 'undefined') {
            this.defaultQuality = quality;

            if(typeof this.userQuality !== 'undefined') {
                return;
            }
        }

        this.quality = quality;
        videojs.Youtube.appendQualityLabel(this.qualityTitle, quality);

        switch(quality) {
            case 'medium':
                this.player_.videoWidth = 480;
                this.player_.videoHeight = 360;
                break;

            case 'large':
                this.player_.videoWidth = 640;
                this.player_.videoHeight = 480;
                break;

            case 'hd720':
                this.player_.videoWidth = 960;
                this.player_.videoHeight = 720;
                break;

            case 'hd1080':
                this.player_.videoWidth = 1440;
                this.player_.videoHeight = 1080;
                break;

            case 'highres':
                this.player_.videoWidth = 1920;
                this.player_.videoHeight = 1080;
                break;

            case 'small':
                this.player_.videoWidth = 320;
                this.player_.videoHeight = 240;
                break;

            case 'tiny':
                this.player_.videoWidth = 144;
                this.player_.videoHeight = 108;
                break;

            default:
                this.player_.videoWidth = 0;
                this.player_.videoHeight = 0;
                break;
        }

        this.player_.trigger('ratechange');
    };

    videojs.Youtube.prototype.onError = function(error) {
        this.player_.error(error);
    };

    /**
     * Add a CSS class name to an element
     * @param {Element} element    Element to add class name to
     * @param {String} classToAdd Classname to add
     */
    videojs.Youtube.addClass = function(element, classToAdd) {
        if((' ' + element.className + ' ').indexOf(' ' + classToAdd + ' ') === -1) {
            element.className = element.className === '' ? classToAdd : element.className + ' ' + classToAdd;
        }
    };

    /**
     * Remove a CSS class name from an element
     * @param {Element} element    Element to remove from class name
     * @param {String} classToRemove Classname to remove
     */
    videojs.Youtube.removeClass = function(element, classToRemove) {
        var classNames, i;

        if(element.className.indexOf(classToRemove) === -1) {
            return;
        }

        classNames = element.className.split(' ');

        // no arr.indexOf in ie8, and we don't want to add a big shim
        for(i = classNames.length - 1; i >= 0; i--) {
            if(classNames[i] === classToRemove) {
                classNames.splice(i, 1);
            }
        }

        element.className = classNames.join(' ');
    };

    // Cross-browsers support (IE8 wink wink)
    function setInnerText(element, text) {
        if(typeof element === 'undefined') {
            return false;
        }

        var textProperty = ('innerText' in element) ? 'innerText' : 'textContent';

        try {
            element[textProperty] = text;
        } catch(anException) {
            //IE<9 FIX
            element.setAttribute('innerText', text);
        }
    }


    // Stretch the YouTube poster
    var style = document.createElement('style');
    var def = ' ' +
        '.vjs-youtube .vjs-poster { background-size: 100%!important; }' +
        '.vjs-youtube .vjs-poster, ' +
        '.vjs-youtube .vjs-loading-spinner, ' +
        '.vjs-youtube .vjs-big-play-button, .vjs-youtube .vjs-text-track-display{ pointer-events: none !important; }' +
        '.vjs-youtube.vjs-user-active .iframeblocker { display: none !important; }' +
        '.vjs-youtube.vjs-user-inactive .vjs-tech.onDesktop { pointer-events: none; }' +
        '.vjs-quality-button > div:first-child > span:first-child { position:relative;top:7px }';

    style.setAttribute('type', 'text/css');
    document.getElementsByTagName('head')[0].appendChild(style);

    if(style.styleSheet) {
        style.styleSheet.cssText = def;
    } else {
        style.appendChild(document.createTextNode(def));
    }

    // IE8 fix for indexOf
    if(!Array.prototype.indexOf) {
        Array.prototype.indexOf = function(elt /*, from*/) {
            var len = this.length >>> 0; // jshint ignore:line

            var from = Number(arguments[1]) || 0;
            from = (from < 0) ?
                Math.ceil(from)
                : Math.floor(from);
            if(from < 0) {
                from += len;
            }

            for(; from < len; from++) {
                if(from in this && this[from] === elt) {
                    return from;
                }
            }
            return -1;
        };
    }
})();
