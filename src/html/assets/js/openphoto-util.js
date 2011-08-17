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
	var PLUGIN_FILE_PREFIX = 'openphoto-lib';

        
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
		* @type {object}
		* @property config
		*/
		this.config = {
			baseURL: 'http://localhost/openphoto',
			jsLocation: '/assets/js',
			css: [],
			js: []
		};
		
		/**
		* the event map for click events
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
		* initialization method
		* @param {object} lib - the library to use
		* @param {object} config - the configuration object
		* @method init
		*/
		this.init = function(lib, config) {
			
			this.config = this.merge(this.config, config);
						
			// we specify what library type in the .ini file
			// either jQuery or YUI - and then the user can load
			// additional css/js assets by specifying the files in the 
			// js config - as specified by the plugin file (that will be user generated).
				
			//the library is a requirement, by default jQuery will be loaded
			this.lib = lib;
			this.libType = 'jQuery';
		
			// get the library plugin file that maps library functions to a normalized
			// naming so that we can use whatever library that is specified
			this.getLibraryPlugin();		
		
		};
		
		/**
		* Now that the library plugin has been loaded, we add all event handlers
		* @return {void}
		* @method _init
		*/
		this._init = function() {
			
			//attach events
			this.attachEvent( document.getElementsByTagName('html')[0], 'click', this.onviewevent, this);
			
		};	
		
		/**
		* handles events - delegates based on className
		* @param {Event} e
		* @return {void}
		* @method onviewevent
		*/
		this.onviewevent = function(e) {
		
			var targ = e.target,
				classes = targ.className.split(" "),
				length = classes.length,
				map = this.eventMap,
				cls;
				
			while (length--) {
				cls = classes[length];
				if (map[cls]) {
					map[cls].call(this, e);
					e.preventDefault();
					return false; //this should be done later - may want to trigger multiple events
				}			
			}		
			
			
		};
				
		/* -------------------------------------------------
        *               Utilities
        * ------------------------------------------------- */


		/**
		* Get the library plugin which will create a normalized interface
		* for the libraries so that they can be used properly. Onload of the
		* library plugin, we will attach our event listeners
		* @return {void}
		* @method getLibraryPlugin
		*/
		this.getLibraryPlugin = function() {
		
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
			
			var head = document.getElementsByTagName('head')[0],
				script = document.createElement('script'),
				scope,
				callback;
								
			script.type = "text/javascript";
			script.src = url;
			
			//callback function was specified - add the onload handlers
			if (typeof(fn) !== 'undefined') {
				
				scope = scope || window,
				callback = function() {
					return fn.apply(scope);
				};
				
				script.onload = callback;
				script.onreadystatechange = function() {
					if (this.readyState === 'complete') {
						callback();
					}
				}
				
			}
			
			head.appendChild(script);
			
		};	
		

        /* -------------------------------------------------
        *               Custom Events
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