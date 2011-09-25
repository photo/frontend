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
    var PLUGIN_FILE_PREFIX = 'openphoto-lib-',
        BROWSER_ID_SRC = 'https://browserid.org/include.js',
        log = function(msg) { if(typeof(console) !== 'undefined') {  console.log(msg); } };
       	
    
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
        this.eventMap = {
			
			'click': {
				'action-box-click':'click:action-box',
				'action-delete-click':'click:action-delete',
				'action-jump-click':'click:action-jump',
				'action-post-click':'click:action-post',
				'login-click':'click:login',
				'map-jump-click':'click:map-jump',
				'nav-item-click':'click:nav-item',
				'pagination-click':'click:pagination',
				'photo-delete-click':'click:photo-delete',
				'photo-edit-click':'click:photo-edit',
				'photo-tag-click':'click:tag',
				'photo-thumbnail-click':'click:photo-thumbnail',
				'photo-update-click':'click:photo-update',
				'search-click':'click:search',
				'settings-click':'click:settings'			
			},
			
			'keydown': {
				37: 'keydown:browse-previous',
				39: 'keydown:browse-next'
			}
			
        };

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

            log('[Util] init:');

            //merge the config with the user specified config
            this.config = this.merge(this.config, config);

            //allow the user to override the eventmap if they wish
            if (this.config.eventMap) {
            	for (var eType in this.config.eventMap) {
            		if (this.config.eventMap.hasOwnProperty(eType)) {
            			this.eventMap[eType] = this.merge(this.config.eventMap[eType], this.eventMap[eType]);
            		}
            	}
            }

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

        /**
        * Now that the library plugin has been loaded, we add all event handlers
        * @return {void}
        * @method _init
        */
        this._init = function() {
  
            log('[Util] _init:')

            var js = this.config.js.assets,
                css = this.config.css.assets,
                i,
                length;

            //attach events      
            this.attachEvent( 'body', 'click', this.onviewevent, this);
            this.attachEvent( 'body', 'keydown', this.onkeydownevent, this);

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

            log('[Util] onviewevent: ' + e.target);

            var targ = e.target || e.srcElement,
                classes = targ.className.split(" "),
                length = classes.length,
                map = this.eventMap[e.type],
                cls;
                
            while (length--) {
                cls = classes[length];
                if (map[cls]) {
                    //do not prevent the default action, let the callback
                    //function do it if it wants
                    this.fire( map[cls], e);    
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
        	log('[Util] keydownevent: ' + e.target);
        	
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
        		if (map[keyCode]) {
        			this.fire( map[keyCode], e);
        		}
        		        		
        		//both className and keycode
        		while (length--) {
	                cls = classes[length];
	                if (map[cls] && map[cls][keyCode]) {
	                    //do not prevent the default action, let the callback
	                    //function do it if it wants
	                    this.fire( map[cls][keyCode], e);    
	                }      
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

            log('[Util] getLibraryPlugin');

            var url = this.config.baseUrl + this.config.jsLocation + PLUGIN_FILE_PREFIX + this.libType + ".js";

            //load the script and attach the event handlers onload
            this.loadScript(url, this._init, this);
            this.loadScript(BROWSER_ID_SRC);
          
        };
    
        /**
        * Shallow merge of all objects passed into it in order of the objects passed in
        * this is just needed to merge the config, but will probably be overwritten by
        * the library plugin
        * @return {object} merged object
        * @method merge
        */
        this.merge = function() {

            log('[Util] merge');

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
  
            log('[Util] loadScript');

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


        /**
        * Utility function to dynamically load css
        * @param {string} url of the source of the sstylesheet
        * @param {Function} fn the callback function to execute onload
        * @param {object} scope - the scope of the callback function
        * @return {void}
        * @method loadScript
        */
        this.loadCss = function(url, fn, scope) {
  
            log('[Util] loadCss');

            var head = document.getElementsByTagName('head')[0],
                link = document.createElement('link'),
                scope,
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

            log('[Util] on: ' + eventName)

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

            log('[Util] unsubscribe: ' + callback);

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

            log('[Util] fire: ' + eventName);

            var callbacks = this._customEvents[eventName],
                arg = arg || {},
                i, j;

            if (!!callbacks) {
                for ( i=0, j=callbacks.length; i<j; i++ ) {
                    callbacks[i].fn.call(callbacks[i].scope, arg);
                }
            }

        };
        
        
        /**
        * Object containing everything needed to upload photos
        *
        * See initUpload to get things started
        */
        this.upload = {
            // default options
            options : {
                simultaneousUploadLimit : 1,
                frameId : "uploader-frame",
                dropZoneId : "drop-zone",
                uploadPath : '/photo/upload.json',
                dragEnterCallback : function(){},
                dragLeaveCallback : function(){},
                dragDropCallback : function(){},
                duplicateCallback : function(){},
                notImageCallback : function(){},
                pushToUICallback : function(){},
                uploadStartCallback : function(){},
                uploadProgressCallback : function(){},
                uploadFinishedCallback : function(){},
                photoTags : function(){alert("photoTags")},
                photoLicense : function(){alert("photoLicense")},
                allowDuplicates : false,
                returnSizes : "25x25xCR",
                crumb : null
            },
            
            parent : this,
            droppedFiles : {},
            simultaneousUploads : 0,
            // stack for managing which file gets uploaded next
            uploadQueue : [],
            uploadQueueIndex : 0,
            xhrs : [],
            
            
            /**
            * initialize upload area and functions
            * @param {object} options - object defining options to override defaults
            * @return {void}
            * @method upload.initUpload
            */
            init : function(themeOptions) {
                var that = this;
                that.options = that.parent.merge(that.options, themeOptions);
                if (window.File && window.FileReader) {
                    that.dropZone = document.getElementById(that.options.dropZoneId);
                    this._uploadEventHandlers();
                } else {
                    this._fallbackUploader();
                }
            },
            
            /**
            * enable or disable duplicate photo name checking
            * @param {bool} state should duplicates be allowed?
            * @return {void}
            * @method upload.allowDuplicates
            */
            allowDuplicates : function(state) {
                if (state) {
                    this.options.allowDuplicates = true;
                } else {
                    this.options.allowDuplicates = false;
                }
            },
            
            _uploadEventHandlers : function() {
                /*
                    TODO prevent page from leaving with confirmation if user drops photo in wrong 
                    place or hits key to navigate before all uploads are done
                */
                var that = this;
                that._addListener(that.dropZone, 'dragenter', that._handleDragEnter, that);
                that._addListener(that.dropZone, 'dragover', that._handleDragOver, that);
                that._addListener(that.dropZone, 'dragleave', that._handleDragLeave, that);
                that._addListener(that.dropZone, 'drop', that._handleFileDrop, that);
            },
            
            /**
            * wrapper for addEventListener to enable passing along the 'that' context
            * @param target dom element to attach to
            * @param event event to listen for
            * @param callback the function to call when event listener fires
            * @param cantext to pass along
            **/
            _addListener : function(target, event, callback, context) {
                if (!target) {
                    log("upload.options.dropZoneID probably not properly set or not on page");
                }
                target.addEventListener(event, function(e) {
                    callback(e,context);
                }, false);
            },
            
            _handleDragEnter : function(e, that) {
                e.stopPropagation();
                e.preventDefault();
                that.options.dragEnterCallback()
            },
            
            _handleDragLeave : function(e, that) {
                e.stopPropagation();
                e.preventDefault();
                that.options.dragLeaveCallback();
            },
            
            /**
            * when user's mouse moves over dropzone while draggin files
            *
            * if we don't prevent default, dropping the file 
            * wil cause the browser to redirect to the file location
            **/
            _handleDragOver : function(e, that) {
                e.stopPropagation();
                e.preventDefault();
            },
            
            _handleFileDrop : function(e, that) {
                e.stopPropagation();
                e.preventDefault();
                that.options.dragDropCallback();
                var files = e.dataTransfer.files;
                that._checkForDuplicates(files);
            },
            
            _checkForDuplicates : function(files) {
                var that = this;
                if (!that.allowDuplicates) {
                    var filteredFiles = [];
                    for (var i=0; i < files.length; i++) {
                        if (that.droppedFiles[files[i].name]) {
                            that.options.duplicateCallback();
                        } else {
                            filteredFiles.push(files[i]);
                        }
                        that.droppedFiles[files[i].name] = files[i];
                    }
                    files = filteredFiles;
                }
                that._validateIsImage(files);
            },
            
            _validateIsImage : function(files) {
                var that = this;
                /*
                    TODO actually check here and callback not image handler
                */
                // that.options.notImageCallback();
                that._indexAndStack(files);
            },
            
            /**
            * each file receives a reference number so that it can be correlated to
            * its coresponding UI representation
            */
            _indexAndStack : function(files) {
                var that = this;
                for (var i=0; i < files.length; i++) {
                    files[i]["queueIndex"] = that.uploadQueue.length;
                    that.uploadQueue.push(files[i]);
                }
                that.options.pushToUICallback(files);
            },
            
            
            // theme must call this to start uploading files
            kickOffUploads : function() {
                var that = this;
                // make sure we don't upload more at one time than constrained to
                if (that.simultaneousUploads != that.options.simultaneousUploadLimit) {
                    // check to see if there are files to upload
                    if (that.uploadQueue[that.uploadQueueIndex]) {
                        // pick file to upload
                        var file = that.uploadQueue[that.uploadQueueIndex];
                        that.uploadQueueIndex++;
                        // let theme know we are starting on that file
                        that.options.uploadStartCallback(file.queueIndex);
                        // send to server
                        that._ajaxToServer(file);
                        // if we are still under the simultaneousUploads limit, lets try another
                        that.kickOffUploads();
                    }
                }
            },
            
            _ajaxToServer : function(file) {
                var that = this;
                
                that.simultaneousUploads++;
                var xhr = new XMLHttpRequest();
                that.xhrs.push(xhr);
                
                var formData = new FormData();
                formData.append("crumb",that.options.crumb);
                formData.append("returnSizes",that.options.returnSizes);
                formData.append("photo",file);
                formData.append("license", that.options.photoLicense(file.queueIndex));
                formData.append("tags", that.options.photoTags(file.queueIndex));
                xhr.open("POST", that.options.uploadPath, true);  
                
                xhr.onload = function(e) {
                    that.simultaneousUploads--;
                    // tell theme we finished
                    that.options.uploadFinishedCallback(file.queueIndex, xhr.status, JSON.parse(xhr.response));
                    // rinse and repeat
                    that.kickOffUploads();
                };
                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                        var progress = Math.round((e.loaded / e.total)*100);
                        that.options.uploadProgressCallback(file.queueIndex, progress);
                    }
                };
                
                xhr.send(formData);
            },
            
            _fallbackUploader : function() {
                /*
                    TODO actually fallback to other uploader
                */
                alert("Your browser doesn't support html5 drag and drop : (");
            }
        }; // end of upload function


    }

    //store the util instance
    OP.Util = new Util();

}());
