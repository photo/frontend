/**
* Utility methods for OpenPhoto - this will include the entire framework
* And ability to swap frameworks (YUI or jQuery) in and out.
*/
(function() {
    
    // just make sure that OP, the global namespace for OpenPhoto
    // is defined
    if ( typeof(OP) === "undefined") {
        OP = {};
    }

    //constants
    var PLUGIN_FILE_PREFIX = 'openphoto-lib-';

    /**
    * Class that contains all utility functions for OpenPhoto
    * We can use a Constructor function in this case since we will
    * not have multiple instances of Util.  Also, it makes it easier to
    * extend via prototype.
    * @class Util
    */
    function Util() {

        /**
        * default configuration options
        * user can optionally specify an onComplete attribute in the css/js
        * object which will execute when the assets are loaded.
        * @type {object}
        * @property config
        */
        this.config = {
            baseUrl: '',
            jsLocation: '/assets/javascripts/',
            css: {
                assets: []
            },
            js: {
                assets:[]
            }
        };

        /**
        * the event map for click events, maps the HTML
        * classNames to the custom event names
        * @type {object}
        * @property eventMap
        */
        this.eventMap = {};

        /**
        * A hash of custom events
        * @type {object}
        * @property _customEvents
        */
        this._customEvents = {};

        /**
        * Count of the number of scripts loaded
        * @type {Number}
        * @property _scriptLoadCount
        */
        this._scriptLoadCount = 0;

        /**
        * Count of the number of css loaded
        * @type {Number}
        * @property _cssLoadCount
        */
        this._cssLoadCount = 0;

        /**
        * initialization method
        * @param {object} lib - the library to use
        * @param {object} config - the configuration object
        * @method init
        */
        this.init = function(lib, config) {
            
            OP.Log.info('[Util] init:');

            //merge the config with the user specified config
            this.config = this.merge(this.config, config);

            // we specify what library type in the .ini file
            // either jQuery or YUI - and then the user can load
            // additional css/js assets by specifying the files in the
            // js config - as specified by the plugin file (that will be user generated).

            //the library is a requirement, by default jQuery will be loaded
            this.lib = lib;
            this.libType = this.detectLibrary();

            // get the library plugin file that maps library functions to a normalized
            // naming so that we can use whatever library that is specified
            this.getLibraryPlugin();
        };

        this.addEventMap = function(eventMap) {
          this.eventMap = eventMap;
          for (var eType in this.eventMap) {
            if (this.eventMap.hasOwnProperty(eType)) {
              this.eventMap[eType] = this.merge(this.eventMap[eType], this.eventMap[eType]);
            }
          }
        };

        /**
        * Now that the library plugin has been loaded, we add all event handlers
        * @return {void}
        * @method _init
        */
        this._init = function() {

            OP.Log.info('[Util] _init:')

            var js = this.config.js.assets,
                css = this.config.css.assets,
                i,
                length;

            //attach events
            this.attachEvent( 'body', 'click', this.onviewevent, this);
            this.attachEvent( 'body', 'focus', this.onviewevent, this);
            this.attachEvent( 'body', 'change', this.onviewevent, this);
            this.attachEvent( 'body', 'mouseover', this.onmouseevent, this);
            this.attachEvent( 'body', 'mouseout', this.onmouseevent, this);
            this.attachEvent( 'html', 'keydown', this.onkeydownevent, this);
            this.attachEvent( 'html', 'keyup', this.onkeydownevent, this);
            this.attachEvent( 'form', 'submit', this.onviewevent, this);
            
            //load additional js in order specified
            for(i=0, j=js.length; i<j; i++) {
                this.loadScript( js[i], this._handleScriptLoad, this );
            }

            //load additional css in order specified
            for(i=0, j=css.length; i<j; i++) {
                this.loadCss( css[i], this._handleCssLoad, this );
            }

        };

        /**
        * handles events - delegates based on className
        * @param {Event} e
        * @return {void}
        * @method onviewevent
        */
        this.onviewevent = function(e) {
            OP.Log.info('[Util] ' + e.type + ': ' + e.target);

            var targ = e.target || e.srcElement,
                classes = targ.className.split(" "),
                length = classes.length,
                map = this.eventMap[e.type],
                cls;

            while (length--) {
                cls = classes[length];
                if (map !== undefined && map[cls]) {
                    //do not prevent the default action, let the callback
                    //function do it if it wants
                    map[cls](e, targ);
                }
            }
        };

        /**
        * handles keydown events
        * @param {Event} e
        * @return {void}
        * @method onkeydownevent
        */
        this.onkeydownevent = function(e) {
            OP.Log.info('[Util] ' + e.type + ': ' + e.target);
            
            var targ = e.target || e.srcElement,
                classes = targ.className.split(" "),
                length = classes.length,
                map = this.eventMap[e.type],
                nodeName = targ.nodeName.toLowerCase(),
                keyCode = e.keyCode,
                cls;

            if (nodeName === "textarea" || nodeName === "input") {
            
                //i don't think there is a case where the user needs to know
                //if the user is inputing text, but just in case, lets fire
                //a custom event on user input
                this.fire( 'keydown:user-input', e);
                
            } else {
                
                //the event map for key press can be two dimensional, it can be
                //keycode, or className then keyCode, if keyCode, fire the custom event
                if (typeof(map) === 'object' && map[keyCode]) {
                    this.fire( map[keyCode], e);
                }
                
                //both className and keycode
                while (length--) {
                    cls = classes[length];
                    if (map !== undefined && map[cls] && map[cls][keyCode]) {
                        //do not prevent the default action, let the callback
                        //function do it if it wants
                        this.fire( map[cls][keyCode], e);
                    }
                }
            
            }

        };

        /**
        * handles mouseover/mouseout events
        * @param {Event} e
        * @return {void}
        * @method onkeydownevent
        */
        this.onmouseevent = function(e) {
            
            //OP.Log.info('[Util] ' + e.type + ': ' + e.target);

            var targ = e.target || e.srcElement,
                classes = targ.className.split(" "),
                length = classes.length,
                map = this.eventMap[e.type],
                cls;

            while (length--) {
                cls = classes[length];
                if (map !== undefined && map[cls]) {
                    //do not prevent the default action, let the callback
                    //function do it if it wants
                    this.fire( map[cls], e);
                }
            }
        };



        /* -------------------------------------------------
        *         Utilities
        * ------------------------------------------------- */


        /**
        * Get the library plugin which will create a normalized interface
        * for the libraries so that they can be used properly. Onload of the
        * library plugin, we will attach our event listeners
        * @return {void}
        * @method getLibraryPlugin
        */
        this.getLibraryPlugin = function() {

            OP.Log.info('[Util] getLibraryPlugin');

            var url = this.config.baseUrl + this.config.jsLocation + PLUGIN_FILE_PREFIX + this.libType + ".js";

            //load the script and attach the event handlers onload
            this.loadScript(url, this._init, this);

        };

        /**
        * Shallow merge of all objects passed into it in order of the objects passed in
        * this is just needed to merge the config, but will probably be overwritten by
        * the library plugin
        * @return {object} merged object
        * @method merge
        */
        this.merge = function() {

            OP.Log.info('[Util] merge');

            var merged = {},
                i,
                j,
                obj,
                key;

            for (i=0, j=arguments.length; i<j; i++) {
                obj = arguments[i];
                for (key in obj) {
                    if (obj.hasOwnProperty(key)) {
                        merged[key] = obj[key];
                    }
                }
            }

            return merged;

        };

        /**
        * Utility function to dynamically load a script
        * @param {string} url of the source of the script
        * @param {Function} fn the callback function to execute onload
        * @param {object} scope - the scope of the callback function
        * @return {void}
        * @method loadScript
        */
        this.loadScript = function(url, fn, scope) {

            OP.Log.info('[Util] loadScript');

            var head = document.getElementsByTagName('head')[0],
                script = document.createElement('script'),
                onload, onerror, loaded = false;

            onerror = function() {
              OP.Log.info('[Util] loadScript failed for ' + url);
            };

            script.type = "text/javascript";
            script.src = url;
            script.onerror = onerror;

            //callback function was specified - add the onload handlers
            if (typeof(fn) !== 'undefined') {

                scope = scope || window,
                onload = function() {
                    if( loaded ) return null;
                    loaded = true;
                    return fn.apply(scope);
                };
                script.onload = onload;

                // This is for IE
                script.onreadystatechange = function() {
                    if (this.readyState === 'complete' || this.readyState === 'loaded') {
                        onload();
                        script.onreadystatechange = null;
                    }
                }

            }

            head.appendChild(script);

        };


        /**
        * Utility function to dynamically load css
        * @param {string} url of the source of the sstylesheet
        * @param {Function} fn the callback function to execute onload
        * @param {object} scope - the scope of the callback function
        * @return {void}
        * @method loadScript
        */
        this.loadCss = function(url, fn, scope) {

            OP.Log.info('[Util] loadCss');

            var head = document.getElementsByTagName('head')[0],
                link = document.createElement('link'),
                callback;

            link.type = 'text/css';
            link.rel = 'stylesheet';
            link.href = url;

            //callback function was specified - add the onload handlers
            if (typeof(fn) !== 'undefined') {

                scope = scope || window,
                callback = function() {
                    return fn.apply(scope);
                };

                link.onload = callback;
                link.onreadystatechange = function() {
                    if (this.readyState === 'complete') {
                        callback();
                    }
                }

            }

            head.appendChild(link);

        };

        /**
        * The user can specify a callback to execute when all of the javascript assets
        * have loaded.  This helper method keeps track of the number of javascript assets
        * loaded and executes the onComplete callback when everything is loaded.
        * @return {void}
        * @method _handleScriptLoad
        */
        this._handleScriptLoad = function() {

            this._scriptLoadCount++;

            if ( (this._scriptLoadCount === this.config.js.assets.length) && (typeof(this.config.js.onComplete) !== 'undefined')) {
                this.config.js.onComplete();
            }

        }

        /**
        * The user can specify a callback to execute when all of the css assets
        * have loaded.  This helper method keeps track of the number of css assets
        * loaded and executes the onComplete callback when everything is loaded.
        * @return {void}
        * @method _handleCssLoad
        */
        this._handleCssLoad = function() {

            this._cssLoadCount++;

            if ( (this._cssLoadCount === this.config.css.assets.length) && (typeof(this.config.css.onComplete) !== 'undefined')) {
                this.config.css.onComplete();
            }

        }

        /**
        * Determines the library type - really simplistic rules for now
        * @return {string} library the library type
        * @method detectLibrary
        */
        this.detectLibrary = function() {

            //very simple for now, but we can extend it later
            var lib = '';

            //jQuery
            if ( typeof(jQuery) !== 'undefined' ) {
                lib = 'jquery';
            }  else {

                //YUI2
                if ( typeof(YAHOO) !== 'undefined' ) {
                    lib = 'yui2';
                } else {
                    lib = 'yui3';
                }

            }

            return lib;

        };


        /* -------------------------------------------------
        *         Custom Events
        * ------------------------------------------------- */

        /**
        * Subscribe to a custom event - the callback will be executed when the custom event is fired
        * @param {string} eventName - the name of the custom event to subscribe to
        * @param {Function} callback - the callback function that will be executed when the event is fired
        * @param {Object} scope - the scope of the callback function (what this will refer to)
        * @return {void}
        * @method on
        */
        this.on = function(eventName, callback, scope) {

            OP.Log.info('[Util] on: ' + eventName)

            var events = this._customEvents,
                cEvent = events[eventName],
                scope = scope || window,
                length;

            if (! cEvent ) {
                events[eventName] = [];
                cEvent = events[eventName];
            }

            //make sure the event doesn't exist already - if it does, return without
            //adding the event again
            length = cEvent.length;
            while(length--) {
                if (cEvent[length].fn === callback) {
                    return;
                }
            }

            cEvent[ cEvent.length ] = {
                fn: callback,
                scope: scope
            };

        };

        /**
        * A little less terse name, but removes an event listener if it exists
        * @param {string} eventName - the name of the custom event to unsubscribe from
        * @param {Function} callback - the callback function that would have been executed on fire of the custom event
        * @return {void}
        * @method unsubscribe
        */
        this.unsubscribe = function(eventName, callback) {

            OP.Log.info('[Util] unsubscribe: ' + callback);

            var events = this._customEvents,
                cEvent = events[eventName],
                length;

            if (!!cEvent) {
                length = cEvent.length;
                while (length--) {
                    if (cEvent[length].fn === callback) {
                        cEvent.splice(length, 1);
                        break;
                    }
                }
            }

        };

        /**
        * Fire a custom event - invoke all listeners passing whatever optional arguments
        * @param {string} eventName - name of the event to fire
        * @return {void}
        * @method fire
        */
        this.fire = function(eventName, arg) {

            OP.Log.info('[Util] fire: ' + eventName);

            var callbacks = this._customEvents[eventName],
                arg = arg || {},
                i, j;

            if (!!callbacks) {
                for ( i=0, j=callbacks.length; i<j; i++ ) {
                    callbacks[i].fn.call(callbacks[i].scope, arg);
                }
            }

        };
    }

    //store the util instance
    OP.Util = new Util();

}());
