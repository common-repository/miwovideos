vjs.plugin('resolutions', function(options) {
    player = this;
    // 'reduce' utility method
    // @param {Array} array to iterate over
    // @param {Function} iterator function for collector
    // @param {Array|Object|Number|String} initial collector
    // @return collector
    vjs.reduce = function(arr, fn, init, n) {
        if (!arr || arr.length === 0) { return; }
        for (var i=0,j=arr.length; i<j; i++) {
            init = fn.call(arr, init, arr[i], i);
        }
        return init;
    };

    this.resolutions_ = {
        options_: {},

        // it is necessary to remove the sources from the DOM after
        // parsing them because otherwise the native player may be
        // inclined to stream both sources
        removeSources: function(el){
            var videoEl = player.el_.getElementsByTagName("video")[0];

            if (player.techName !== "Html5" || !videoEl) return;

            var srcs = videoEl.getElementsByTagName("source");
            for(var i=0;i<srcs.length;i++){
                videoEl.removeChild(srcs[i]);
            }
        },


        bucketByTypes: function(sources){
            return vjs.reduce(sources, function(init, val, i){
                (init[val.type] = init[val.type] || []).push(val);
                return init;
            }, {}, player);
        },


        selectSource: function(sources){
            this.removeSources();

            var sourcesByType = this.bucketByTypes(sources);
            var typeAndTech   = this.selectTypeAndTech(sources);

            if (!typeAndTech) return false;

            // even though we choose the best resolution for the user here, we
            // should remember the resolutions so that we can potentially
            // change resolution later
            this.options_['sourceResolutions'] = sourcesByType[typeAndTech.type];

            return this.selectResolution(this.options_['sourceResolutions']);
        },


        selectTypeAndTech: function(sources) {
            var techName;
            var tech;

            for (var i=0,j=player.options_['techOrder'];i<j.length;i++) {
                techName = videojs.capitalize(j[i]);
                tech     = window['videojs'][techName];

                // Check if the browser supports this technology
                if (tech.isSupported()) {
                    // Loop through each source object
                    for (var a=0,b=sources;a<b.length;a++) {
                        var source = b[a];
                        // Check if source can be played with this technology
                        if (tech['canPlaySource'](source)) {
                            return { type: source.type, tech: techName };
                        }
                    }
                }
            }
        },


        selectResolution: function(typeSources) {
            var defaultRes = 0;
            var supportsLocalStorage = !!window.localStorage;

            // check to see if any sources are marked as default
            videojs.obj.each(typeSources, function(i, s){
                // add the index here so we can reference it later
                s.index = parseInt(i, 10);

                if (s['data-default']) defaultRes = s.index;
            }, player);

            // if the user has previously selected a preference, check if
            // that preference is available. if not, use the source marked
            // default
            var preferredRes = defaultRes;

            // trying to follow the videojs code conventions of if statements
            if (supportsLocalStorage){
                var storedRes = parseInt(window.localStorage.getItem('videojs_preferred_res'), 10);

                if (!isNaN(storedRes))
                    preferredRes = storedRes;
            }

            var maxRes    = (typeSources.length - 1);
            var actualRes = preferredRes > maxRes ? maxRes : preferredRes;

            return typeSources[actualRes];
        }
    };

    // convenience method
    // @return {String} cached resolution label:
    // "SD"
    player.resolution = function(){
        return this.cache_.src.res;
    };

    player.changeResolution = function(new_source){
        // has the exact same source been chosen?
        if (this.cache_.src === new_source.src){
            this.trigger('resolutionchange');
            return this; // basically a no-op
        }

        // remember our position and playback state
        var curTime      = this.currentTime();
        var remainPaused = this.paused();

        // pause playback
        this.pause();

        // attempts to stop the download of the existing video
        //this.resolutions_.stopStream();

        // HTML5 tends to not recover from reloading the tech but it can
        // generally handle changing src.  Flash generally cannot handle
        // changing src but can reload its tech.
        if (this.techName === "Html5"){
            this.src(new_source.src);
        } else {
            this.loadTech(this.techName, {src: new_source.src});
        }

        // when the technology is re-started, kick off the new stream
        this.ready(function() {
            this.one('loadeddata', vjs.bind(this, function() {
                this.currentTime(curTime);
            }));

            this.trigger('resolutionchange');

            if (!remainPaused) {
                this.load();
                this.play();
            }

            // remember this selection
            vjs.setLocalStorage('videojs_preferred_res', parseInt(new_source.index, 10));
        });
    };

    /* Resolution Menu Items
     ================================================================================ */
    var ResolutionMenuItem = videojs.MenuItem.extend({
        init: function(player, options){
            // Modify options for parent MenuItem class's init.
            options['label'] = options.source['data-res'];
            videojs.MenuItem.call(this, player, options);

            this.source = options.source;
            this.resolution = options.source['data-res'];

            this.player_.one('loadstart', vjs.bind(this, this.update));
            this.player_.on('resolutionchange', vjs.bind(this, this.update));
        }
    });

    ResolutionMenuItem.prototype.onClick = function(){
        videojs.MenuItem.prototype.onClick.call(this);
        this.player_.changeResolution(this.source);
    };

    ResolutionMenuItem.prototype.update = function(){
        var player = this.player_;
        if ((player.cache_['src'] === this.source.src)) {
            this.selected(true);
        } else {
            this.selected(false);
        }
    };

    /* Resolutions Button
     ================================================================================ */
    var ResolutionButton = videojs.MenuButton.extend({
        init: function(player, options) {
            videojs.MenuButton.call(this, player, options);

            if (this.items.length <= 1) {
                this.hide();
            }
        }
    });

    ResolutionButton.prototype.sourceResolutions_;

    ResolutionButton.prototype.sourceResolutions = function() {
        return this.sourceResolutions_;
    };

    ResolutionButton.prototype.onClick = function(e){
        // Only proceed if the target of the click was a DIV (just the button and its inner div, not the menu)
        // This prevents the menu from opening and closing when one of the menu items is clicked.
        if (e.target.className.match(/vjs-control-content/)) {

            // Toggle the 'touched' class
            this[this.el_.className.match(/touched/) ? "removeClass" : "addClass"]("touched");
        } else {

            // Remove the 'touched' class from all control bar buttons with menus to hide any already visible...
            var buttons = document.getElementsByClassName('vjs-menu-button');
            for(var i=0;i<buttons.length;i++){
                videojs.removeClass(buttons[i], 'touched');
            }

            this.removeClass('touched');
        }
    };

    ResolutionButton.prototype.createItems = function(){
        var resolutions = this.sourceResolutions_ = this.player_.resolutions_.options_['sourceResolutions'];
        var items = [];
        for (var i = 0; i < resolutions.length; i++) {
            items.push(new ResolutionMenuItem(this.player_, {
                'source': this.sourceResolutions_[i]
            }));
        }
        return items;
    };

    /**
     * @constructor
     */
    ResolutionsButton = ResolutionButton.extend({
        /** @constructor */
        init: function(player, options, ready){
            ResolutionButton.call(this, player, options, ready);
            this.el_.setAttribute('aria-label','Resolutions Menu');
            this.el_.setAttribute('id',"vjs-resolutions-button");
        }
    });

    ResolutionsButton.prototype.kind_ = 'resolutions';
    ResolutionsButton.prototype.buttonText = 'Resolutions';
    ResolutionsButton.prototype.className = 'vjs-resolutions-button';

    // Add Button to controlBar
    videojs.obj.merge(player.controlBar.options_['children'], {
        'resolutionsButton': {}
    });

    // let's get the party started!
    // we have to grab the parsed sources and select the source with our
    // resolution-aware source selector
    var source = player.resolutions_.selectSource(player.options_['sources']);

    // when the player is ready, add the resolution button to the control bar
    player.ready(function(){
        player.changeResolution(source);
        var button = new ResolutionsButton(player, options);
        player.controlBar.addChild(button);
    });
});

vjs.plugin('watchlater', function(options) {
    var player = this;
    /**
     * Toggle watchlater video
     * @param {videojs.Player|Object} player
     * @param {Object=} options
     * @class
     * @extends videojs.Button
     */
    videojs.WatchlaterToggle = videojs.MenuButton.extend({
        /**
         * @constructor
         * @memberof videojs.WatchlaterToggle
         * @instance
         */
        init: function(player, options){
            videojs.MenuButton.call(this, player, options);
            this.el_.setAttribute('aria-label','Watch Later');
            this.el_.setAttribute('aria-haspopup','true');
        }
    });

    videojs.WatchlaterToggle.prototype.kind_ = 'watchlater';
    videojs.WatchlaterToggle.prototype.buttonText = 'Watch Later';

    videojs.WatchlaterToggle.prototype.buildCSSClass = function(){
        if (options.already_added) {
            return videojs.Button.prototype.buildCSSClass.call(this)+'vjs-watchlater-control vjs-menu-button watchlater-success';
        } else {
            return videojs.Button.prototype.buildCSSClass.call(this)+'vjs-watchlater-control vjs-menu-button';
        }
    };

    var WatchlaterPopup = videojs.MenuItem.extend({
        init: function(player, options){
            // Modify options for parent MenuItem class's init.
            videojs.MenuItem.call(this, player, options);
        }
    });

    videojs.WatchlaterToggle.prototype.createItems = function(){
        var items = [];
        items[0] = new WatchlaterPopup(this.player_, {label: 'Watch Later'});
        return items;
    };

    videojs.obj.merge(player.controlBar.options_['children'], {
        'watchlaterToggle': {}
    });

    // when the player is ready, add the watchlater toggle to the control bar
    player.ready(function(){
        var button = new videojs.WatchlaterToggle(player, options);
        player.controlBar.addChild(button);
    });
});

(function() {
  var defaults = {
      0: {
        src: 'example-thumbnail.png'
      }
    },
    extend = function() {
      var args, target, i, object, property;
      args = Array.prototype.slice.call(arguments);
      target = args.shift() || {};
      for (i in args) {
        object = args[i];
        for (property in object) {
          if (object.hasOwnProperty(property)) {
            if (typeof object[property] === 'object') {
              target[property] = extend(target[property], object[property]);
            } else {
              target[property] = object[property];
            }
          }
        }
      }
      return target;
    };

  /**
   * register the thubmnails plugin
   */
  videojs.plugin('thumbnails', function(options) {

    var div, divtip, settings, img, player, progressControl, duration;
    settings = extend({}, defaults, options)
    player = this;

    // create the thumbnail
    div = document.createElement('div');
    divtip = document.createElement('div');
    div.className = 'vjs-thumbnail-holder';
    divtip.id = 'vjs-tip-inner';
    img = document.createElement('img');
    div.appendChild(img);
    div.appendChild(divtip);
    img.src = settings['0'].src;
    img.className = 'vjs-thumbnail';
    extend(img.style, settings['0'].style);

    // center the thumbnail over the cursor if an offset wasn't provided
    if (!img.style.left && !img.style.right) {
      img.onload = function() {
        img.style.left = -(img.naturalWidth / 2) + 'px';
      }
    };

    // keep track of the duration to calculate correct thumbnail to display
    duration = player.duration();
    player.on('durationchange', function(event) {
      duration = player.duration();
    });

    // add the thumbnail to the player
    progressControl = player.controlBar.progressControl;
    progressControl.el().appendChild(div);

    // update the thumbnail while hovering
    progressControl.el().addEventListener('mousemove', function(event) {
      var mouseTime, time, active, left, setting;
      active = 0;

        //Timestamp
        var minutes, seconds, seekBar, timeInSeconds;
        seekBar = player.controlBar.progressControl.seekBar;
        timeInSeconds = seekBar.calculateDistance(event) * seekBar.player_.duration();
        if (timeInSeconds === seekBar.player_.duration()) {
            timeInSeconds = timeInSeconds - 0.1;
        }
        minutes = Math.floor(timeInSeconds / 60);
        seconds = Math.floor(timeInSeconds - minutes * 60);
        if (seconds < 10) {
            seconds = "0" + seconds;
        }
        jQuery('#vjs-tip-inner').html("" + minutes + ":" + seconds);
        jQuery("#vjs-tip-inner").css("visibility", "visible");

      // find the page offset of the mouse
      left = event.pageX || (event.clientX + document.body.scrollLeft + document.documentElement.scrollLeft);
      // subtract the page offset of the progress control
      left -= progressControl.el().getBoundingClientRect().left + window.pageXOffset;
      if(left > 0) {
          div.style.left = left + 'px';
      }
        if (!event) event = window.event;
        var x = event.offsetX == undefined ? event.layerX : event.offsetX;

      // apply updated styles to the thumbnail if necessary
      mouseTime = Math.floor(x / progressControl.width() * duration);
      for (time in settings) {
        if (mouseTime > time) {
          active = Math.max(active, time);
        }
      }
      setting = settings[active];
      if (setting.src && img.src != setting.src) {
        img.src = setting.src;
      }
      if (setting.style && img.style != setting.style) {
        extend(img.style, setting.style);
      }
    }, false);
    jQuery(".vjs-progress-control").on("mouseout", function() {
        jQuery("#vjs-tip-inner").css("visibility", "hidden");
    });
  });
})();

(function(vjs) {
    var extend = function(obj) {
            var arg, i, k;
            for (i = 1; i < arguments.length; i++) {
                arg = arguments[i];
                for (k in arg) {
                    if (arg.hasOwnProperty(k)) {
                        obj[k] = arg[k];
                    }
                }
            }
            return obj;
        },
        defaults = [
            {
                imageSrc: '',
                title:   '',
                url:     ''
            }
        ];

    vjs.plugin('relatedCarousel', function(options, settings) {
        var player = this;
        settings = extend({}, defaults, {});

        var holderDiv = document.createElement('div');
        holderDiv.className = 'vjs-related-carousel-holder';

        var title = document.createElement('h5');
        title.innerHTML = 'Related Videos';
        holderDiv.appendChild(title);

        player.el().appendChild(holderDiv);

        for (var i in settings) {
            var img = document.createElement('img');
            img.src = settings[i].imageSrc;
            img.className = 'vjs-carousel-thumbnail';
            img.alt = settings[i].title;

            var anchor = document.createElement('a');
            anchor.href = settings[i].url;
            anchor.appendChild(img);
            anchor.title = settings[i].title;

            holderDiv.appendChild(anchor);
        }


        /* Menu Button */
        var RelatedCarouselButton = vjs.Button.extend({
            init: function(player, options) {
                vjs.Button.call(this, player, options);
            }
        });

        RelatedCarouselButton.prototype.buttonText = 'Related Videos';

        RelatedCarouselButton.prototype.buildCSSClass = function(){
            return 'vjs-related-carousel-button ' + vjs.Button.prototype.buildCSSClass.call(this);
        };

        RelatedCarouselButton.prototype.onClick = function(e){
            holderDiv.classList.toggle('active');
        };

        player.ready(function(){
            var button = new RelatedCarouselButton(player, options);
            player.controlBar.addChild(button);
        });
    });
}(window.videojs));
!function (e) {
    if ("object" == typeof exports) module.exports = e();
    else if ("function" == typeof define && define.amd) define(e);
    else {
        var f;
        "undefined" != typeof window ? f = window : "undefined" != typeof global ? f = global : "undefined" != typeof self && (f = self), f.DMVAST = e()
    }
}(function () {
    var define, module, exports;
    return (function e(t, n, r) {
        function s(o, u) {
            if (!n[o]) {
                if (!t[o]) {
                    var a = typeof require == "function" && require;
                    if (!u && a) return a(o, !0);
                    if (i) return i(o, !0);
                    throw new Error("Cannot find module '"+o+"'")
                }
                var f = n[o] = {
                    exports: {}
                };
                t[o][0].call(f.exports, function (e) {
                    var n = t[o][1][e];
                    return s(n ? n : e)
                }, f, f.exports, e, t, n, r)
            }
            return n[o].exports
        }

        var i = typeof require == "function" && require;
        for (var o = 0; o < r.length; o++) s(r[o]);
        return s
    })({
        1: [
            function (_dereq_, module, exports) {
                // Copyright Joyent, Inc. and other Node contributors.
                //
                // Permission is hereby granted, free of charge, to any person obtaining a
                // copy of this software and associated documentation files (the
                // "Software"), to deal in the Software without restriction, including
                // without limitation the rights to use, copy, modify, merge, publish,
                // distribute, sublicense, and/or sell copies of the Software, and to permit
                // persons to whom the Software is furnished to do so, subject to the
                // following conditions:
                //
                // The above copyright notice and this permission notice shall be included
                // in all copies or substantial portions of the Software.
                //
                // THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
                // OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
                // MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
                // NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
                // DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
                // OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
                // USE OR OTHER DEALINGS IN THE SOFTWARE.

                function EventEmitter() {
                    this._events = this._events || {};
                    this._maxListeners = this._maxListeners || undefined;
                }

                module.exports = EventEmitter;

                // Backwards-compat with node 0.10.x
                EventEmitter.EventEmitter = EventEmitter;

                EventEmitter.prototype._events = undefined;
                EventEmitter.prototype._maxListeners = undefined;

                // By default EventEmitters will print a warning if more than 10 listeners are
                // added to it. This is a useful default which helps finding memory leaks.
                EventEmitter.defaultMaxListeners = 10;

                // Obviously not all Emitters should be limited to 10. This function allows
                // that to be increased. Set to zero for unlimited.
                EventEmitter.prototype.setMaxListeners = function (n) {
                    if (!isNumber(n) || n < 0 || isNaN(n))
                        throw TypeError('n must be a positive number');
                    this._maxListeners = n;
                    return this;
                };

                EventEmitter.prototype.emit = function (type) {
                    var er, handler, len, args, i, listeners;

                    if (!this._events)
                        this._events = {};

                    // If there is no 'error' event listener then throw.
                    if (type === 'error') {
                        if (!this._events.error ||
                            (isObject(this._events.error) && !this._events.error.length)) {
                            er = arguments[1];
                            if (er instanceof Error) {
                                throw er; // Unhandled 'error' event
                            } else {
                                throw TypeError('Uncaught, unspecified "error" event.');
                            }
                            return false;
                        }
                    }

                    handler = this._events[type];

                    if (isUndefined(handler))
                        return false;

                    if (isFunction(handler)) {
                        switch (arguments.length) {
                            // fast cases
                            case 1:
                                handler.call(this);
                                break;
                            case 2:
                                handler.call(this, arguments[1]);
                                break;
                            case 3:
                                handler.call(this, arguments[1], arguments[2]);
                                break;
                            // slower
                            default:
                                len = arguments.length;
                                args = new Array(len-1);
                                for (i = 1; i < len; i++)
                                    args[i-1] = arguments[i];
                                handler.apply(this, args);
                        }
                    } else if (isObject(handler)) {
                        len = arguments.length;
                        args = new Array(len-1);
                        for (i = 1; i < len; i++)
                            args[i-1] = arguments[i];

                        listeners = handler.slice();
                        len = listeners.length;
                        for (i = 0; i < len; i++)
                            listeners[i].apply(this, args);
                    }

                    return true;
                };

                EventEmitter.prototype.addListener = function (type, listener) {
                    var m;

                    if (!isFunction(listener))
                        throw TypeError('listener must be a function');

                    if (!this._events)
                        this._events = {};

                    // To avoid recursion in the case that type === "newListener"! Before
                    // adding it to the listeners, first emit "newListener".
                    if (this._events.newListener)
                        this.emit('newListener', type,
                            isFunction(listener.listener) ?
                                listener.listener : listener);

                    if (!this._events[type])
                    // Optimize the case of one listener. Don't need the extra array object.
                        this._events[type] = listener;
                    else if (isObject(this._events[type]))
                    // If we've already got an array, just append.
                        this._events[type].push(listener);
                    else
                    // Adding the second element, need to change to array.
                        this._events[type] = [this._events[type], listener];

                    // Check for listener leak
                    if (isObject(this._events[type]) && !this._events[type].warned) {
                        var m;
                        if (!isUndefined(this._maxListeners)) {
                            m = this._maxListeners;
                        } else {
                            m = EventEmitter.defaultMaxListeners;
                        }

                        if (m && m > 0 && this._events[type].length > m) {
                            this._events[type].warned = true;
                            console.error('(node) warning: possible EventEmitter memory '+
                                'leak detected. %d listeners added. '+
                                'Use emitter.setMaxListeners() to increase limit.',
                                this._events[type].length);
                            console.trace();
                        }
                    }

                    return this;
                };

                EventEmitter.prototype.on = EventEmitter.prototype.addListener;

                EventEmitter.prototype.once = function (type, listener) {
                    if (!isFunction(listener))
                        throw TypeError('listener must be a function');

                    var fired = false;

                    function g() {
                        this.removeListener(type, g);

                        if (!fired) {
                            fired = true;
                            listener.apply(this, arguments);
                        }
                    }

                    g.listener = listener;
                    this.on(type, g);

                    return this;
                };

                // emits a 'removeListener' event iff the listener was removed
                EventEmitter.prototype.removeListener = function (type, listener) {
                    var list, position, length, i;

                    if (!isFunction(listener))
                        throw TypeError('listener must be a function');

                    if (!this._events || !this._events[type])
                        return this;

                    list = this._events[type];
                    length = list.length;
                    position = -1;

                    if (list === listener ||
                        (isFunction(list.listener) && list.listener === listener)) {
                        delete this._events[type];
                        if (this._events.removeListener)
                            this.emit('removeListener', type, listener);

                    } else if (isObject(list)) {
                        for (i = length; i-- > 0;) {
                            if (list[i] === listener ||
                                (list[i].listener && list[i].listener === listener)) {
                                position = i;
                                break;
                            }
                        }

                        if (position < 0)
                            return this;

                        if (list.length === 1) {
                            list.length = 0;
                            delete this._events[type];
                        } else {
                            list.splice(position, 1);
                        }

                        if (this._events.removeListener)
                            this.emit('removeListener', type, listener);
                    }

                    return this;
                };

                EventEmitter.prototype.removeAllListeners = function (type) {
                    var key, listeners;

                    if (!this._events)
                        return this;

                    // not listening for removeListener, no need to emit
                    if (!this._events.removeListener) {
                        if (arguments.length === 0)
                            this._events = {};
                        else if (this._events[type])
                            delete this._events[type];
                        return this;
                    }

                    // emit removeListener for all listeners on all events
                    if (arguments.length === 0) {
                        for (key in this._events) {
                            if (key === 'removeListener') continue;
                            this.removeAllListeners(key);
                        }
                        this.removeAllListeners('removeListener');
                        this._events = {};
                        return this;
                    }

                    listeners = this._events[type];

                    if (isFunction(listeners)) {
                        this.removeListener(type, listeners);
                    } else {
                        // LIFO order
                        while (listeners.length)
                            this.removeListener(type, listeners[listeners.length-1]);
                    }
                    delete this._events[type];

                    return this;
                };

                EventEmitter.prototype.listeners = function (type) {
                    var ret;
                    if (!this._events || !this._events[type])
                        ret = [];
                    else if (isFunction(this._events[type]))
                        ret = [this._events[type]];
                    else
                        ret = this._events[type].slice();
                    return ret;
                };

                EventEmitter.listenerCount = function (emitter, type) {
                    var ret;
                    if (!emitter._events || !emitter._events[type])
                        ret = 0;
                    else if (isFunction(emitter._events[type]))
                        ret = 1;
                    else
                        ret = emitter._events[type].length;
                    return ret;
                };

                function isFunction(arg) {
                    return typeof arg === 'function';
                }

                function isNumber(arg) {
                    return typeof arg === 'number';
                }

                function isObject(arg) {
                    return typeof arg === 'object' && arg !== null;
                }

                function isUndefined(arg) {
                    return arg === void 0;
                }

            }, {}
        ],
        2: [
            function (_dereq_, module, exports) {
                // Generated by CoffeeScript 1.7.1
                var VASTAd;

                VASTAd = (function () {
                    function VASTAd() {
                        this.errorURLTemplates = [];
                        this.impressionURLTemplates = [];
                        this.creatives = [];
                    }

                    return VASTAd;

                })();

                module.exports = VASTAd;

            }, {}
        ],
        3: [
            function (_dereq_, module, exports) {
                // Generated by CoffeeScript 1.7.1
                var VASTClient, VASTParser, VASTUtil;

                VASTParser = _dereq_('./parser.coffee');

                VASTUtil = _dereq_('./util.coffee');

                VASTClient = (function () {
                    function VASTClient() {
                    }

                    VASTClient.cappingFreeLunch = 0;

                    VASTClient.cappingMinimumTimeInterval = 0;

                    VASTClient.timeout = 0;

                    VASTClient.get = function (url, cb) {
                        var now;
                        now = +new Date();
                        if (this.totalCallsTimeout < now) {
                            this.totalCalls = 1;
                            this.totalCallsTimeout = now+(60 * 60 * 1000);
                        } else {
                            this.totalCalls++;
                        }
                        if (this.cappingFreeLunch >= this.totalCalls) {
                            cb(null);
                            return;
                        }
                        if (now-this.lastSuccessfullAd < this.cappingMinimumTimeInterval) {
                            cb(null);
                            return;
                        }
                        return VASTParser.parse(url, (function (_this) {
                            return function (response) {
                                return cb(response);
                            };
                        })(this));
                    };

                    (function () {
                        var defineProperty, storage;
                        storage = VASTUtil.storage;
                        defineProperty = Object.defineProperty;
                        ['lastSuccessfullAd', 'totalCalls', 'totalCallsTimeout'].forEach(function (property) {
                            defineProperty(VASTClient, property, {
                                get: function () {
                                    return storage.getItem(property);
                                },
                                set: function (value) {
                                    return storage.setItem(property, value);
                                },
                                configurable: false,
                                enumerable: true
                            });
                        });
                        if (VASTClient.totalCalls == null) {
                            VASTClient.totalCalls = 0;
                        }
                        if (VASTClient.totalCallsTimeout == null) {
                            VASTClient.totalCallsTimeout = 0;
                        }
                    })();

                    return VASTClient;

                })();

                module.exports = VASTClient;

            }, {
                "./parser.coffee": 8,
                "./util.coffee": 14
            }
        ],
        4: [
            function (_dereq_, module, exports) {
                // Generated by CoffeeScript 1.7.1
                var VASTCompanionAd;

                VASTCompanionAd = (function () {
                    function VASTCompanionAd() {
                        this.id = null;
                        this.width = 0;
                        this.height = 0;
                        this.type = null;
                        this.staticResource = null;
                        this.companionClickThroughURLTemplate = null;
                        this.trackingEvents = {};
                    }

                    return VASTCompanionAd;

                })();

                module.exports = VASTCompanionAd;

            }, {}
        ],
        5: [
            function (_dereq_, module, exports) {
                // Generated by CoffeeScript 1.7.1
                var VASTCreative, VASTCreativeCompanion, VASTCreativeLinear, VASTCreativeNonLinear,
                    __hasProp = {}.hasOwnProperty,
                    __extends = function (child, parent) {
                        for (var key in parent) {
                            if (__hasProp.call(parent, key)) child[key] = parent[key];
                        }

                        function ctor() {
                            this.constructor = child;
                        }

                        ctor.prototype = parent.prototype;
                        child.prototype = new ctor();
                        child.__super__ = parent.prototype;
                        return child;
                    };

                VASTCreative = (function () {
                    function VASTCreative() {
                        this.trackingEvents = {};
                    }

                    return VASTCreative;

                })();

                VASTCreativeLinear = (function (_super) {
                    __extends(VASTCreativeLinear, _super);

                    function VASTCreativeLinear() {
                        VASTCreativeLinear.__super__.constructor.apply(this, arguments);
                        this.type = "linear";
                        this.duration = 0;
                        this.skipDelay = null;
                        this.mediaFiles = [];
                        this.videoClickThroughURLTemplate = null;
                        this.videoClickTrackingURLTemplate = null;
                    }

                    return VASTCreativeLinear;

                })(VASTCreative);

                VASTCreativeNonLinear = (function (_super) {
                    __extends(VASTCreativeNonLinear, _super);

                    function VASTCreativeNonLinear() {
                        return VASTCreativeNonLinear.__super__.constructor.apply(this, arguments);
                    }

                    return VASTCreativeNonLinear;

                })(VASTCreative);

                VASTCreativeCompanion = (function () {
                    function VASTCreativeCompanion() {
                        this.type = "companion";
                        this.variations = [];
                    }

                    return VASTCreativeCompanion;

                })();

                module.exports = {
                    VASTCreativeLinear: VASTCreativeLinear,
                    VASTCreativeNonLinear: VASTCreativeNonLinear,
                    VASTCreativeCompanion: VASTCreativeCompanion
                };

            }, {}
        ],
        6: [
            function (_dereq_, module, exports) {
                // Generated by CoffeeScript 1.7.1
                module.exports = {
                    client: _dereq_('./client.coffee'),
                    tracker: _dereq_('./tracker.coffee'),
                    parser: _dereq_('./parser.coffee'),
                    util: _dereq_('./util.coffee')
                };

            }, {
                "./client.coffee": 3,
                "./parser.coffee": 8,
                "./tracker.coffee": 10,
                "./util.coffee": 14
            }
        ],
        7: [
            function (_dereq_, module, exports) {
                // Generated by CoffeeScript 1.7.1
                var VASTMediaFile;

                VASTMediaFile = (function () {
                    function VASTMediaFile() {
                        this.fileURL = null;
                        this.deliveryType = "progressive";
                        this.mimeType = null;
                        this.codec = null;
                        this.bitrate = 0;
                        this.minBitrate = 0;
                        this.maxBitrate = 0;
                        this.width = 0;
                        this.height = 0;
                    }

                    return VASTMediaFile;

                })();

                module.exports = VASTMediaFile;

            }, {}
        ],
        8: [
            function (_dereq_, module, exports) {
                // Generated by CoffeeScript 1.7.1
                var URLHandler, VASTAd, VASTCompanionAd, VASTCreativeCompanion, VASTCreativeLinear, VASTMediaFile, VASTParser, VASTResponse, VASTUtil,
                    __indexOf = [].indexOf || function (item) {
                            for (var i = 0, l = this.length; i < l; i++) {
                                if (i in this && this[i] === item) return i;
                            }
                            return -1;
                        };

                URLHandler = _dereq_('./urlhandler.coffee');

                VASTResponse = _dereq_('./response.coffee');

                VASTAd = _dereq_('./ad.coffee');

                VASTUtil = _dereq_('./util.coffee');

                VASTCreativeLinear = _dereq_('./creative.coffee').VASTCreativeLinear;

                VASTCreativeCompanion = _dereq_('./creative.coffee').VASTCreativeCompanion;

                VASTMediaFile = _dereq_('./mediafile.coffee');

                VASTCompanionAd = _dereq_('./companionad.coffee');

                VASTParser = (function () {
                    var URLTemplateFilters;

                    function VASTParser() {
                    }

                    URLTemplateFilters = [];

                    VASTParser.addURLTemplateFilter = function (func) {
                        if (typeof func === 'function') {
                            URLTemplateFilters.push(func);
                        }
                    };

                    VASTParser.removeURLTemplateFilter = function () {
                        return URLTemplateFilters.pop();
                    };

                    VASTParser.countURLTemplateFilters = function () {
                        return URLTemplateFilters.length;
                    };

                    VASTParser.clearUrlTemplateFilters = function () {
                        return URLTemplateFilters = [];
                    };

                    VASTParser.parse = function (url, cb) {
                        return this._parse(url, null, function (err, response) {
                            return cb(response);
                        });
                    };

                    VASTParser._parse = function (url, parentURLs, cb) {
                        var filter, _i, _len;
                        for (_i = 0, _len = URLTemplateFilters.length; _i < _len; _i++) {
                            filter = URLTemplateFilters[_i];
                            url = filter(url);
                        }
                        if (parentURLs == null) {
                            parentURLs = [];
                        }
                        parentURLs.push(url);
                        return URLHandler.get(url, (function (_this) {
                            return function (err, xml) {
                                var ad, complete, loopIndex, node, response, _j, _k, _len1, _len2, _ref, _ref1;
                                if (err != null) {
                                    return cb(err);
                                }
                                response = new VASTResponse();
                                if (!(((xml != null ? xml.documentElement : void 0) != null) && xml.documentElement.nodeName === "VAST")) {
                                    return cb();
                                }
                                _ref = xml.documentElement.childNodes;
                                for (_j = 0, _len1 = _ref.length; _j < _len1; _j++) {
                                    node = _ref[_j];
                                    if (node.nodeName === 'Error') {
                                        response.errorURLTemplates.push(_this.parseNodeText(node));
                                    }
                                }
                                _ref1 = xml.documentElement.childNodes;
                                for (_k = 0, _len2 = _ref1.length; _k < _len2; _k++) {
                                    node = _ref1[_k];
                                    if (node.nodeName === 'Ad') {
                                        ad = _this.parseAdElement(node);
                                        if (ad != null) {
                                            response.ads.push(ad);
                                        } else {
                                            VASTUtil.track(response.errorURLTemplates, {
                                                ERRORCODE: 101
                                            });
                                        }
                                    }
                                }
                                complete = function () {
                                    var _l, _len3, _ref2;
                                    if (!response) {
                                        return;
                                    }
                                    _ref2 = response.ads;
                                    for (_l = 0, _len3 = _ref2.length; _l < _len3; _l++) {
                                        ad = _ref2[_l];
                                        if (ad.nextWrapperURL != null) {
                                            return;
                                        }
                                    }
                                    if (response.ads.length === 0) {
                                        VASTUtil.track(response.errorURLTemplates, {
                                            ERRORCODE: 303
                                        });
                                        response = null;
                                    }
                                    return cb(null, response);
                                };
                                loopIndex = response.ads.length;
                                while (loopIndex--) {
                                    ad = response.ads[loopIndex];
                                    if (ad.nextWrapperURL == null) {
                                        continue;
                                    }
                                    (function (ad) {
                                        var baseURL, _ref2;
                                        if (parentURLs.length >= 10 || (_ref2 = ad.nextWrapperURL, __indexOf.call(parentURLs, _ref2) >= 0)) {
                                            VASTUtil.track(ad.errorURLTemplates, {
                                                ERRORCODE: 302
                                            });
                                            response.ads.splice(response.ads.indexOf(ad), 1);
                                            complete();
                                            return;
                                        }
                                        if (ad.nextWrapperURL.indexOf('://') === -1) {
                                            baseURL = url.slice(0, url.lastIndexOf('/'));
                                            ad.nextWrapperURL = ""+baseURL+"/"+ad.nextWrapperURL;
                                        }
                                        return _this._parse(ad.nextWrapperURL, parentURLs, function (err, wrappedResponse) {
                                            var creative, eventName, index, wrappedAd, _base, _l, _len3, _len4, _len5, _m, _n, _ref3, _ref4, _ref5;
                                            if (err != null) {
                                                VASTUtil.track(ad.errorURLTemplates, {
                                                    ERRORCODE: 301
                                                });
                                                response.ads.splice(response.ads.indexOf(ad), 1);
                                            } else if (wrappedResponse == null) {
                                                VASTUtil.track(ad.errorURLTemplates, {
                                                    ERRORCODE: 303
                                                });
                                                response.ads.splice(response.ads.indexOf(ad), 1);
                                            } else {
                                                response.errorURLTemplates = response.errorURLTemplates.concat(wrappedResponse.errorURLTemplates);
                                                index = response.ads.indexOf(ad);
                                                response.ads.splice(index, 1);
                                                _ref3 = wrappedResponse.ads;
                                                for (_l = 0, _len3 = _ref3.length; _l < _len3; _l++) {
                                                    wrappedAd = _ref3[_l];
                                                    wrappedAd.errorURLTemplates = ad.errorURLTemplates.concat(wrappedAd.errorURLTemplates);
                                                    wrappedAd.impressionURLTemplates = ad.impressionURLTemplates.concat(wrappedAd.impressionURLTemplates);
                                                    if (ad.trackingEvents != null) {
                                                        _ref4 = wrappedAd.creatives;
                                                        for (_m = 0, _len4 = _ref4.length; _m < _len4; _m++) {
                                                            creative = _ref4[_m];
                                                            _ref5 = Object.keys(ad.trackingEvents);
                                                            for (_n = 0, _len5 = _ref5.length; _n < _len5; _n++) {
                                                                eventName = _ref5[_n];
                                                                (_base = creative.trackingEvents)[eventName] || (_base[eventName] = []);
                                                                creative.trackingEvents[eventName] = creative.trackingEvents[eventName].concat(ad.trackingEvents[eventName]);
                                                            }
                                                        }
                                                    }
                                                    response.ads.splice(index, 0, wrappedAd);
                                                }
                                            }
                                            delete ad.nextWrapperURL;
                                            return complete();
                                        });
                                    })(ad);
                                }
                                return complete();
                            };
                        })(this));
                    };

                    VASTParser.childByName = function (node, name) {
                        var child, _i, _len, _ref;
                        _ref = node.childNodes;
                        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                            child = _ref[_i];
                            if (child.nodeName === name) {
                                return child;
                            }
                        }
                    };

                    VASTParser.childsByName = function (node, name) {
                        var child, childs, _i, _len, _ref;
                        childs = [];
                        _ref = node.childNodes;
                        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                            child = _ref[_i];
                            if (child.nodeName === name) {
                                childs.push(child);
                            }
                        }
                        return childs;
                    };

                    VASTParser.parseAdElement = function (adElement) {
                        var adTypeElement, _i, _len, _ref;
                        _ref = adElement.childNodes;
                        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                            adTypeElement = _ref[_i];
                            if (adTypeElement.nodeName === "Wrapper") {
                                return this.parseWrapperElement(adTypeElement);
                            } else if (adTypeElement.nodeName === "InLine") {
                                return this.parseInLineElement(adTypeElement);
                            }
                        }
                    };

                    VASTParser.parseWrapperElement = function (wrapperElement) {
                        var ad, wrapperCreativeElement, wrapperURLElement;
                        ad = this.parseInLineElement(wrapperElement);
                        wrapperURLElement = this.childByName(wrapperElement, "VASTAdTagURI");
                        if (wrapperURLElement != null) {
                            ad.nextWrapperURL = this.parseNodeText(wrapperURLElement);
                        }
                        wrapperCreativeElement = ad.creatives[0];
                        if ((wrapperCreativeElement != null) && (wrapperCreativeElement.trackingEvents != null)) {
                            ad.trackingEvents = wrapperCreativeElement.trackingEvents;
                        }
                        if (ad.nextWrapperURL != null) {
                            return ad;
                        }
                    };

                    VASTParser.parseInLineElement = function (inLineElement) {
                        var ad, creative, creativeElement, creativeTypeElement, node, _i, _j, _k, _len, _len1, _len2, _ref, _ref1, _ref2;
                        ad = new VASTAd();
                        _ref = inLineElement.childNodes;
                        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                            node = _ref[_i];
                            switch (node.nodeName) {
                                case "Error":
                                    ad.errorURLTemplates.push(this.parseNodeText(node));
                                    break;
                                case "Impression":
                                    ad.impressionURLTemplates.push(this.parseNodeText(node));
                                    break;
                                case "Creatives":
                                    _ref1 = this.childsByName(node, "Creative");
                                    for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
                                        creativeElement = _ref1[_j];
                                        _ref2 = creativeElement.childNodes;
                                        for (_k = 0, _len2 = _ref2.length; _k < _len2; _k++) {
                                            creativeTypeElement = _ref2[_k];
                                            switch (creativeTypeElement.nodeName) {
                                                case "Linear":
                                                    creative = this.parseCreativeLinearElement(creativeTypeElement);
                                                    if (creative) {
                                                        ad.creatives.push(creative);
                                                    }
                                                    break;
                                                case "CompanionAds":
                                                    creative = this.parseCompanionAd(creativeTypeElement);
                                                    if (creative) {
                                                        ad.creatives.push(creative);
                                                    }
                                            }
                                        }
                                    }
                            }
                        }
                        return ad;
                    };

                    VASTParser.parseCreativeLinearElement = function (creativeElement) {
                        var creative, eventName, mediaFile, mediaFileElement, mediaFilesElement, percent, skipOffset, trackingElement, trackingEventsElement, trackingURLTemplate, videoClicksElement, _base, _i, _j, _k, _l, _len, _len1, _len2, _len3, _ref, _ref1, _ref2, _ref3;
                        creative = new VASTCreativeLinear();
                        creative.duration = this.parseDuration(this.parseNodeText(this.childByName(creativeElement, "Duration")));
                        if (creative.duration === -1 && creativeElement.parentNode.parentNode.parentNode.nodeName !== 'Wrapper') {
                            return null;
                        }
                        skipOffset = creativeElement.getAttribute("skipoffset");
                        if (skipOffset == null) {
                            creative.skipDelay = null;
                        } else if (skipOffset.charAt(skipOffset.length-1) === "%") {
                            percent = parseInt(skipOffset, 10);
                            creative.skipDelay = creative.duration * (percent / 100);
                        } else {
                            creative.skipDelay = this.parseDuration(skipOffset);
                        }
                        videoClicksElement = this.childByName(creativeElement, "VideoClicks");
                        if (videoClicksElement != null) {
                            creative.videoClickThroughURLTemplate = this.parseNodeText(this.childByName(videoClicksElement, "ClickThrough"));
                            creative.videoClickTrackingURLTemplate = this.parseNodeText(this.childByName(videoClicksElement, "ClickTracking"));
                        }
                        _ref = this.childsByName(creativeElement, "TrackingEvents");
                        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                            trackingEventsElement = _ref[_i];
                            _ref1 = this.childsByName(trackingEventsElement, "Tracking");
                            for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
                                trackingElement = _ref1[_j];
                                eventName = trackingElement.getAttribute("event");
                                trackingURLTemplate = this.parseNodeText(trackingElement);
                                if ((eventName != null) && (trackingURLTemplate != null)) {
                                    if ((_base = creative.trackingEvents)[eventName] == null) {
                                        _base[eventName] = [];
                                    }
                                    creative.trackingEvents[eventName].push(trackingURLTemplate);
                                }
                            }
                        }
                        _ref2 = this.childsByName(creativeElement, "MediaFiles");
                        for (_k = 0, _len2 = _ref2.length; _k < _len2; _k++) {
                            mediaFilesElement = _ref2[_k];
                            _ref3 = this.childsByName(mediaFilesElement, "MediaFile");
                            for (_l = 0, _len3 = _ref3.length; _l < _len3; _l++) {
                                mediaFileElement = _ref3[_l];
                                mediaFile = new VASTMediaFile();
                                mediaFile.fileURL = this.parseNodeText(mediaFileElement);
                                mediaFile.deliveryType = mediaFileElement.getAttribute("delivery");
                                mediaFile.codec = mediaFileElement.getAttribute("codec");
                                mediaFile.mimeType = mediaFileElement.getAttribute("type");
                                mediaFile.bitrate = parseInt(mediaFileElement.getAttribute("bitrate") || 0);
                                mediaFile.minBitrate = parseInt(mediaFileElement.getAttribute("minBitrate") || 0);
                                mediaFile.maxBitrate = parseInt(mediaFileElement.getAttribute("maxBitrate") || 0);
                                mediaFile.width = parseInt(mediaFileElement.getAttribute("width") || 0);
                                mediaFile.height = parseInt(mediaFileElement.getAttribute("height") || 0);
                                creative.mediaFiles.push(mediaFile);
                            }
                        }
                        return creative;
                    };

                    VASTParser.parseCompanionAd = function (creativeElement) {
                        var companionAd, companionResource, creative, eventName, staticElement, trackingElement, trackingEventsElement, trackingURLTemplate, _base, _i, _j, _k, _l, _len, _len1, _len2, _len3, _ref, _ref1, _ref2, _ref3;
                        creative = new VASTCreativeCompanion();
                        _ref = this.childsByName(creativeElement, "Companion");
                        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                            companionResource = _ref[_i];
                            companionAd = new VASTCompanionAd();
                            companionAd.id = companionResource.getAttribute("id") || null;
                            companionAd.width = companionResource.getAttribute("width");
                            companionAd.height = companionResource.getAttribute("height");
                            _ref1 = this.childsByName(companionResource, "StaticResource");
                            for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
                                staticElement = _ref1[_j];
                                companionAd.type = staticElement.getAttribute("creativeType") || 0;
                                companionAd.staticResource = this.parseNodeText(staticElement);
                            }
                            _ref2 = this.childsByName(companionResource, "TrackingEvents");
                            for (_k = 0, _len2 = _ref2.length; _k < _len2; _k++) {
                                trackingEventsElement = _ref2[_k];
                                _ref3 = this.childsByName(trackingEventsElement, "Tracking");
                                for (_l = 0, _len3 = _ref3.length; _l < _len3; _l++) {
                                    trackingElement = _ref3[_l];
                                    eventName = trackingElement.getAttribute("event");
                                    trackingURLTemplate = this.parseNodeText(trackingElement);
                                    if ((eventName != null) && (trackingURLTemplate != null)) {
                                        if ((_base = companionAd.trackingEvents)[eventName] == null) {
                                            _base[eventName] = [];
                                        }
                                        companionAd.trackingEvents[eventName].push(trackingURLTemplate);
                                    }
                                }
                            }
                            companionAd.companionClickThroughURLTemplate = this.parseNodeText(this.childByName(companionResource, "CompanionClickThrough"));
                            creative.variations.push(companionAd);
                        }
                        return creative;
                    };

                    VASTParser.parseDuration = function (durationString) {
                        var durationComponents, hours, minutes, seconds, secondsAndMS;
                        if (!(durationString != null)) {
                            return -1;
                        }
                        durationComponents = durationString.split(":");
                        if (durationComponents.length !== 3) {
                            return -1;
                        }
                        secondsAndMS = durationComponents[2].split(".");
                        seconds = parseInt(secondsAndMS[0]);
                        if (secondsAndMS.length === 2) {
                            seconds += parseFloat("0."+secondsAndMS[1]);
                        }
                        minutes = parseInt(durationComponents[1] * 60);
                        hours = parseInt(durationComponents[0] * 60 * 60);
                        if (isNaN(hours || isNaN(minutes || isNaN(seconds || minutes > 60 * 60 || seconds > 60)))) {
                            return -1;
                        }
                        return hours+minutes+seconds;
                    };

                    VASTParser.parseNodeText = function (node) {
                        return node && (node.textContent || node.text);
                    };

                    return VASTParser;

                })();

                module.exports = VASTParser;

            }, {
                "./ad.coffee": 2,
                "./companionad.coffee": 4,
                "./creative.coffee": 5,
                "./mediafile.coffee": 7,
                "./response.coffee": 9,
                "./urlhandler.coffee": 11,
                "./util.coffee": 14
            }
        ],
        9: [
            function (_dereq_, module, exports) {
                // Generated by CoffeeScript 1.7.1
                var VASTResponse;

                VASTResponse = (function () {
                    function VASTResponse() {
                        this.ads = [];
                        this.errorURLTemplates = [];
                    }

                    return VASTResponse;

                })();

                module.exports = VASTResponse;

            }, {}
        ],
        10: [
            function (_dereq_, module, exports) {
                // Generated by CoffeeScript 1.7.1
                var EventEmitter, VASTClient, VASTCreativeLinear, VASTTracker, VASTUtil,
                    __hasProp = {}.hasOwnProperty,
                    __extends = function (child, parent) {
                        for (var key in parent) {
                            if (__hasProp.call(parent, key)) child[key] = parent[key];
                        }

                        function ctor() {
                            this.constructor = child;
                        }

                        ctor.prototype = parent.prototype;
                        child.prototype = new ctor();
                        child.__super__ = parent.prototype;
                        return child;
                    };

                VASTClient = _dereq_('./client.coffee');

                VASTUtil = _dereq_('./util.coffee');

                VASTCreativeLinear = _dereq_('./creative.coffee').VASTCreativeLinear;

                EventEmitter = _dereq_('events').EventEmitter;

                VASTTracker = (function (_super) {
                    __extends(VASTTracker, _super);

                    function VASTTracker(ad, creative) {
                        var eventName, events, _ref;
                        this.ad = ad;
                        this.creative = creative;
                        this.muted = false;
                        this.impressed = false;
                        this.skipable = false;
                        this.skipDelayDefault = -1;
                        this.trackingEvents = {};
                        this.emitAlwaysEvents = ['creativeView', 'start', 'firstQuartile', 'midpoint', 'thirdQuartile', 'complete', 'rewind', 'skip', 'closeLinear', 'close'];
                        _ref = creative.trackingEvents;
                        for (eventName in _ref) {
                            events = _ref[eventName];
                            this.trackingEvents[eventName] = events.slice(0);
                        }
                        if (creative instanceof VASTCreativeLinear) {
                            this.assetDuration = creative.duration;
                            this.quartiles = {
                                'firstQuartile': Math.round(25 * this.assetDuration) / 100,
                                'midpoint': Math.round(50 * this.assetDuration) / 100,
                                'thirdQuartile': Math.round(75 * this.assetDuration) / 100
                            };
                            this.skipDelay = creative.skipDelay;
                            this.linear = true;
                            this.clickThroughURLTemplate = creative.videoClickThroughURLTemplate;
                            this.clickTrackingURLTemplate = creative.videoClickTrackingURLTemplate;
                        } else {
                            this.skipDelay = -1;
                            this.linear = false;
                        }
                        this.on('start', function () {
                            VASTClient.lastSuccessfullAd = +new Date();
                        });
                    }

                    VASTTracker.prototype.setProgress = function (progress) {
                        var eventName, events, percent, quartile, skipDelay, time, _i, _len, _ref;
                        skipDelay = this.skipDelay === null ? this.skipDelayDefault : this.skipDelay;
                        if (skipDelay !== -1 && !this.skipable) {
                            if (skipDelay > progress) {
                                this.emit('skip-countdown', skipDelay-progress);
                            } else {
                                this.skipable = true;
                                this.emit('skip-countdown', 0);
                            }
                        }
                        if (this.linear && this.assetDuration > 0) {
                            events = [];
                            if (progress > 0) {
                                events.push("start");
                                percent = Math.round(progress / this.assetDuration * 100);
                                events.push("progress-"+percent+"%");
                                _ref = this.quartiles;
                                for (quartile in _ref) {
                                    time = _ref[quartile];
                                    if ((time <= progress && progress <= (time+1))) {
                                        events.push(quartile);
                                    }
                                }
                            }
                            for (_i = 0, _len = events.length; _i < _len; _i++) {
                                eventName = events[_i];
                                this.track(eventName, true);
                            }
                            if (progress < this.progress) {
                                this.track("rewind");
                            }
                        }
                        return this.progress = progress;
                    };

                    VASTTracker.prototype.setMuted = function (muted) {
                        if (this.muted !== muted) {
                            this.track(muted ? "muted" : "unmuted");
                        }
                        return this.muted = muted;
                    };

                    VASTTracker.prototype.setPaused = function (paused) {
                        if (this.paused !== paused) {
                            this.track(paused ? "pause" : "resume");
                        }
                        return this.paused = paused;
                    };

                    VASTTracker.prototype.setFullscreen = function (fullscreen) {
                        if (this.fullscreen !== fullscreen) {
                            this.track(fullscreen ? "fullscreen" : "exitFullscreen");
                        }
                        return this.fullscreen = fullscreen;
                    };

                    VASTTracker.prototype.setSkipDelay = function (duration) {
                        if (typeof duration === 'number') {
                            return this.skipDelay = duration;
                        }
                    };

                    VASTTracker.prototype.load = function () {
                        if (!this.impressed) {
                            this.impressed = true;
                            this.trackURLs(this.ad.impressionURLTemplates);
                            return this.track("creativeView");
                        }
                    };

                    VASTTracker.prototype.errorWithCode = function (errorCode) {
                        return this.trackURLs(this.ad.errorURLTemplates, {
                            ERRORCODE: errorCode
                        });
                    };

                    VASTTracker.prototype.complete = function () {
                        return this.track("complete");
                    };

                    VASTTracker.prototype.stop = function () {
                        return this.track(this.linear ? "closeLinear" : "close");
                    };

                    VASTTracker.prototype.skip = function () {
                        this.track("skip");
                        return this.trackingEvents = [];
                    };

                    VASTTracker.prototype.click = function () {
                        var clickThroughURL, variables;
                        if (this.clickTrackingURLTemplate != null) {
                            this.trackURLs([this.clickTrackingURLTemplate]);
                        }
                        if (this.clickThroughURLTemplate != null) {
                            if (this.linear) {
                                variables = {
                                    CONTENTPLAYHEAD: this.progressFormated()
                                };
                            }
                            clickThroughURL = VASTUtil.resolveURLTemplates([this.clickThroughURLTemplate], variables)[0];
                            return this.emit("clickthrough", clickThroughURL);
                        }
                    };

                    VASTTracker.prototype.track = function (eventName, once) {
                        var idx, trackingURLTemplates;
                        if (once == null) {
                            once = false;
                        }
                        if (eventName === 'closeLinear' && ((this.trackingEvents[eventName] == null) && (this.trackingEvents['close'] != null))) {
                            eventName = 'close';
                        }
                        trackingURLTemplates = this.trackingEvents[eventName];
                        idx = this.emitAlwaysEvents.indexOf(eventName);
                        if (trackingURLTemplates != null) {
                            this.emit(eventName, '');
                            this.trackURLs(trackingURLTemplates);
                        } else if (idx !== -1) {
                            this.emit(eventName, '');
                        }
                        if (once === true) {
                            delete this.trackingEvents[eventName];
                            if (idx > -1) {
                                this.emitAlwaysEvents.splice(idx, 1);
                            }
                        }
                    };

                    VASTTracker.prototype.trackURLs = function (URLTemplates, variables) {
                        if (variables == null) {
                            variables = {};
                        }
                        if (this.linear) {
                            variables["CONTENTPLAYHEAD"] = this.progressFormated();
                        }
                        return VASTUtil.track(URLTemplates, variables);
                    };

                    VASTTracker.prototype.progressFormated = function () {
                        var h, m, ms, s, seconds;
                        seconds = parseInt(this.progress);
                        h = seconds / (60 * 60);
                        if (h.length < 2) {
                            h = "0"+h;
                        }
                        m = seconds / 60 % 60;
                        if (m.length < 2) {
                            m = "0"+m;
                        }
                        s = seconds % 60;
                        if (s.length < 2) {
                            s = "0"+m;
                        }
                        ms = parseInt((this.progress-seconds) * 100);
                        return ""+h+":"+m+":"+s+"."+ms;
                    };

                    return VASTTracker;

                })(EventEmitter);

                module.exports = VASTTracker;

            }, {
                "./client.coffee": 3,
                "./creative.coffee": 5,
                "./util.coffee": 14,
                "events": 1
            }
        ],
        11: [
            function (_dereq_, module, exports) {
                // Generated by CoffeeScript 1.7.1
                var URLHandler, flash, xhr;

                xhr = _dereq_('./urlhandlers/xmlhttprequest.coffee');

                flash = _dereq_('./urlhandlers/flash.coffee');

                URLHandler = (function () {
                    function URLHandler() {
                    }

                    URLHandler.get = function (url, cb) {
                        if (typeof window === "undefined" || window === null) {
                            return _dereq_('./urlhandlers/'+'node.coffee').get(url, cb);
                        } else if (xhr.supported()) {
                            return xhr.get(url, cb);
                        } else if (flash.supported()) {
                            return flash.get(url, cb);
                        } else {
                            return cb();
                        }
                    };

                    return URLHandler;

                })();

                module.exports = URLHandler;

            }, {
                "./urlhandlers/flash.coffee": 12,
                "./urlhandlers/xmlhttprequest.coffee": 13
            }
        ],
        12: [
            function (_dereq_, module, exports) {
                // Generated by CoffeeScript 1.7.1
                var FlashURLHandler;

                FlashURLHandler = (function () {
                    function FlashURLHandler() {
                    }

                    FlashURLHandler.xdr = function () {
                        var xdr;
                        if (window.XDomainRequest) {
                            xdr = new XDomainRequest();
                        }
                        return xdr;
                    };

                    FlashURLHandler.supported = function () {
                        return !!this.xdr();
                    };

                    FlashURLHandler.get = function (url, cb) {
                        var xdr, xmlDocument;
                        if (xmlDocument = typeof window.ActiveXObject === "function" ? new window.ActiveXObject("Microsoft.XMLDOM") : void 0) {
                            xmlDocument.async = false;
                        } else {
                            return cb();
                        }
                        xdr = this.xdr();
                        xdr.open('GET', url);
                        xdr.send();
                        return xdr.onload = function () {
                            xmlDocument.loadXML(xdr.responseText);
                            return cb(null, xmlDocument);
                        };
                    };

                    return FlashURLHandler;

                })();

                module.exports = FlashURLHandler;

            }, {}
        ],
        13: [
            function (_dereq_, module, exports) {
                // Generated by CoffeeScript 1.7.1
                var XHRURLHandler;

                XHRURLHandler = (function () {
                    function XHRURLHandler() {
                    }

                    XHRURLHandler.xhr = function () {
                        var xhr;
                        xhr = new window.XMLHttpRequest();
                        if ('withCredentials' in xhr) {
                            return xhr;
                        }
                    };

                    XHRURLHandler.supported = function () {
                        return !!this.xhr();
                    };

                    XHRURLHandler.get = function (url, cb) {
                        var xhr;
                        xhr = this.xhr();
                        xhr.open('GET', url);
                        xhr.send();
                        return xhr.onreadystatechange = function () {
                            if (xhr.readyState === 4) {
                                return cb(null, xhr.responseXML);
                            }
                        };
                    };

                    return XHRURLHandler;

                })();

                module.exports = XHRURLHandler;

            }, {}
        ],
        14: [
            function (_dereq_, module, exports) {
                // Generated by CoffeeScript 1.7.1
                var VASTUtil;

                VASTUtil = (function () {
                    function VASTUtil() {
                    }

                    VASTUtil.track = function (URLTemplates, variables) {
                        var URL, URLs, i, _i, _len, _results;
                        URLs = this.resolveURLTemplates(URLTemplates, variables);
                        _results = [];
                        for (_i = 0, _len = URLs.length; _i < _len; _i++) {
                            URL = URLs[_i];
                            if (typeof window !== "undefined" && window !== null) {
                                i = new Image();
                                _results.push(i.src = URL);
                            } else {

                            }
                        }
                        return _results;
                    };

                    VASTUtil.resolveURLTemplates = function (URLTemplates, variables) {
                        var URLTemplate, URLs, key, macro1, macro2, resolveURL, value, _i, _len;
                        URLs = [];
                        if (variables == null) {
                            variables = {};
                        }
                        if (!("CACHEBUSTING" in variables)) {
                            variables["CACHEBUSTING"] = Math.round(Math.random() * 1.0e+10);
                        }
                        variables["random"] = variables["CACHEBUSTING"];
                        for (_i = 0, _len = URLTemplates.length; _i < _len; _i++) {
                            URLTemplate = URLTemplates[_i];
                            resolveURL = URLTemplate;
                            for (key in variables) {
                                value = variables[key];
                                macro1 = "["+key+"]";
                                macro2 = "%%"+key+"%%";
                                resolveURL = resolveURL.replace(macro1, value);
                                resolveURL = resolveURL.replace(macro2, value);
                            }
                            URLs.push(resolveURL);
                        }
                        return URLs;
                    };

                    VASTUtil.storage = (function () {
                        var data, isDisabled, storage, storageError;
                        try {
                            storage = typeof window !== "undefined" && window !== null ? window.localStorage || window.sessionStorage : null;
                        } catch (_error) {
                            storageError = _error;
                            storage = null;
                        }
                        isDisabled = function (store) {
                            var e, testValue;
                            try {
                                testValue = '__VASTUtil__';
                                store.setItem(testValue, testValue);
                                if (store.getItem(testValue) !== testValue) {
                                    return true;
                                }
                            } catch (_error) {
                                e = _error;
                                return true;
                            }
                            return false;
                        };
                        if ((storage == null) || isDisabled(storage)) {
                            data = {};
                            storage = {
                                length: 0,
                                getItem: function (key) {
                                    return data[key];
                                },
                                setItem: function (key, value) {
                                    data[key] = value;
                                    this.length = Object.keys(data).length;
                                },
                                removeItem: function (key) {
                                    delete data[key];
                                    this.length = Object.keys(data).length;
                                },
                                clear: function () {
                                    data = {};
                                    this.length = 0;
                                }
                            };
                        }
                        return storage;
                    })();

                    return VASTUtil;

                })();

                module.exports = VASTUtil;

            }, {}
        ]
    }, {}, [6])
    (6)
});
/**
 * Basic Ad support plugin for video.js.
 *
 * Common code to support ad integrations.
 */

(function () {
    videojs.plugin('seek', function (options) {
        var getNamedParameterValue, toSeconds, seekParam, seekValue, _ref;
        if (options == null) {
            options = {};
        }
        getNamedParameterValue = function (name, share) {
            var match, searchString;
            if (share) {
                searchString = jQuery('#share_url').val();
            }
            else {
                searchString = window.location.search;
            }
            match = RegExp('[?&]'+name+'=([^&]*)').exec(searchString);
            return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
        };

        toSeconds = function (str) {
            var p = str.split(':'),
                s = 0, m = 1;

            while (p.length > 0) {
                s += m * parseInt(p.pop(), 10);
                m *= 60;
            }

            return s;
        };

        function refreshShareLink() {
            jQuery('#start-time-checkbox').attr('checked', 'checked');
            var seconds = toSeconds(jQuery('#share_at_time').val());
            if (seconds) {
                if (!parseInt(getNamedParameterValue('t', true))) {
                    if (jQuery('#share_url').val().indexOf('?') > 0) {
                        jQuery('#share_url').val(jQuery('#share_url').val()+'&t='+seconds);
                    }
                    else {
                        jQuery('#share_url').val(jQuery('#share_url').val()+'?t='+seconds);
                    }
                }
                else {
                    jQuery('#share_url').val(jQuery('#share_url').val().replace(/([?&])t=([^&]*)/, '$1t='+seconds));
                }
            }
        }

        seekParam = options['seek_param'] || JSON.parse((_ref = this.options()['data-setup']) != null ? _ref : '{}')['seek_param'] || 't';
        seekValue = parseInt(getNamedParameterValue(seekParam, false));

        this.on('timeupdate', function () {
            var currentTime = vjs.formatTime(this.currentTime());
            jQuery('#share_at_time').val(currentTime);
            if (jQuery('#start-time-checkbox').is(':checked')) {
                refreshShareLink();
            }
        });

        jQuery('#start-time-checkbox').on('click', function () {
            if (jQuery(this).is(':checked')) {
                refreshShareLink();
            }
            else {
                jQuery('#share_url').val(jQuery('#share_url').val().replace(/([?&])t=([^&]*)/, ''));
            }
        });

        jQuery('#share_at_time').on('focusout', refreshShareLink);

        if (seekValue) {
            return this.ready(function () {
                return this.one('playing', function () {
                    return this.currentTime(seekValue);
                });
            });
        }
    });

}).call(this);

videojs.plugin('loopbutton', function (options) {
    var player = this;

    var LoopButton = vjs.Button.extend({
        init: function (player, options) {
            vjs.Button.call(this, player, options);
        }
    });

    LoopButton.prototype.buttonText = 'Loop';

    LoopButton.prototype.buildCSSClass = function () {
        if (player.options_['loop'] == true) {
            return 'vjs-loop-button vjs-menu-button vjs-control-active';
        }
        else {
            return 'vjs-loop-button vjs-menu-button';
        }
    };

    LoopButton.prototype.onClick = function (e) {
        if (player.options_['loop'] == true) {
            player.options_['loop'] = false;
            this.removeClass('vjs-control-active');
        } else {
            player.options_['loop'] = true;
            this.addClass('vjs-control-active');
        }
    };

    player.ready(function () {
        var button = new LoopButton(player, options);
        player.controlBar.addChild(button);

        player.on('ended', function () {
            if (player.options_['loop'] == true) {
                player.play();
            }
        });
    });
});




