/**
 * plupload.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */

// JSLint defined globals
/*global window:false, escape:false */

/*!@@version@@*/

(function() {
	var count = 0, runtimes = [], i18n = {}, mimes = {},
		xmlEncodeChars = {'<' : 'lt', '>' : 'gt', '&' : 'amp', '"' : 'quot', '\'' : '#39'},
		xmlEncodeRegExp = /[<>&\"\']/g, undef, delay = window.setTimeout,
		// A place to store references to event handlers
		eventhash = {},
		uid;

	// IE W3C like event funcs
	function preventDefault() {
		this.returnValue = false;
	}

	function stopPropagation() {
		this.cancelBubble = true;
	}

	// Parses the default mime types string into a mimes lookup map
	(function(mime_data) {
		var items = mime_data.split(/,/), i, y, ext;

		for (i = 0; i < items.length; i += 2) {
			ext = items[i + 1].split(/ /);

			for (y = 0; y < ext.length; y++) {
				mimes[ext[y]] = items[i];
			}
		}
	})(
		"application/msword,doc dot," +
		"application/pdf,pdf," +
		"application/pgp-signature,pgp," +
		"application/postscript,ps ai eps," +
		"application/rtf,rtf," +
		"application/vnd.ms-excel,xls xlb," +
		"application/vnd.ms-powerpoint,ppt pps pot," +
		"application/zip,zip," +
		"application/x-shockwave-flash,swf swfl," +
		"application/vnd.openxmlformats-officedocument.wordprocessingml.document,docx," +
		"application/vnd.openxmlformats-officedocument.wordprocessingml.template,dotx," +
		"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,xlsx," +
		"application/vnd.openxmlformats-officedocument.presentationml.presentation,pptx," + 
		"application/vnd.openxmlformats-officedocument.presentationml.template,potx," +
		"application/vnd.openxmlformats-officedocument.presentationml.slideshow,ppsx," +
		"application/x-javascript,js," +
		"application/json,json," +
		"audio/mpeg,mpga mpega mp2 mp3," +
		"audio/x-wav,wav," +
		"audio/mp4,m4a," +
		"image/bmp,bmp," +
		"image/gif,gif," +
		"image/jpeg,jpeg jpg jpe," +
		"image/photoshop,psd," +
		"image/png,png," +
		"image/svg+xml,svg svgz," +
		"image/tiff,tiff tif," +
		"text/plain,asc txt text diff log," +
		"text/html,htm html xhtml," +
		"text/css,css," +
		"text/csv,csv," +
		"text/rtf,rtf," +
		"video/mpeg,mpeg mpg mpe," +
		"video/quicktime,qt mov," +
		"video/mp4,mp4," +
		"video/x-m4v,m4v," +
		"video/x-flv,flv," +
		"video/x-ms-wmv,wmv," +
		"video/avi,avi," +
		"video/webm,webm," +
		"video/vnd.rn-realvideo,rv," +
		"application/vnd.oasis.opendocument.formula-template,otf," +
		"application/octet-stream,exe"
	);

	/**
	 * Plupload class with some global constants and functions.
	 *
	 * @example
	 * // Encode entities
	 * console.log(plupload.xmlEncode("My string &lt;&gt;"));
	 *
	 * // Generate unique id
	 * console.log(plupload.guid());
	 *
	 * @static
	 * @class plupload
	 */
	var plupload = {
		/**
		 * Plupload version will be replaced on build.
		 */
		VERSION : '@@version@@',

		/**
		 * Inital state of the queue and also the state ones it's finished all it's uploads.
		 *
		 * @property STOPPED
		 * @final
		 */
		STOPPED : 1,

		/**
		 * Upload process is running
		 *
		 * @property STARTED
		 * @final
		 */
		STARTED : 2,

		/**
		 * File is queued for upload
		 *
		 * @property QUEUED
		 * @final
		 */
		QUEUED : 1,

		/**
		 * File is being uploaded
		 *
		 * @property UPLOADING
		 * @final
		 */
		UPLOADING : 2,

		/**
		 * File has failed to be uploaded
		 *
		 * @property FAILED
		 * @final
		 */
		FAILED : 4,

		/**
		 * File has been uploaded successfully
		 *
		 * @property DONE
		 * @final
		 */
		DONE : 5,

		// Error constants used by the Error event

		/**
		 * Generic error for example if an exception is thrown inside Silverlight.
		 *
		 * @property GENERIC_ERROR
		 * @final
		 */
		GENERIC_ERROR : -100,

		/**
		 * HTTP transport error. For example if the server produces a HTTP status other than 200.
		 *
		 * @property HTTP_ERROR
		 * @final
		 */
		HTTP_ERROR : -200,

		/**
		 * Generic I/O error. For exampe if it wasn't possible to open the file stream on local machine.
		 *
		 * @property IO_ERROR
		 * @final
		 */
		IO_ERROR : -300,

		/**
		 * Generic I/O error. For exampe if it wasn't possible to open the file stream on local machine.
		 *
		 * @property SECURITY_ERROR
		 * @final
		 */
		SECURITY_ERROR : -400,

		/**
		 * Initialization error. Will be triggered if no runtime was initialized.
		 *
		 * @property INIT_ERROR
		 * @final
		 */
		INIT_ERROR : -500,

		/**
		 * File size error. If the user selects a file that is too large it will be blocked and an error of this type will be triggered.
		 *
		 * @property FILE_SIZE_ERROR
		 * @final
		 */
		FILE_SIZE_ERROR : -600,

		/**
		 * File extension error. If the user selects a file that isn't valid according to the filters setting.
		 *
		 * @property FILE_EXTENSION_ERROR
		 * @final
		 */
		FILE_EXTENSION_ERROR : -601,
		
		/**
		 * Runtime will try to detect if image is proper one. Otherwise will throw this error.
		 *
		 * @property IMAGE_FORMAT_ERROR
		 * @final
		 */
		IMAGE_FORMAT_ERROR : -700,
		
		/**
		 * While working on the image runtime will try to detect if the operation may potentially run out of memeory and will throw this error.
		 *
		 * @property IMAGE_MEMORY_ERROR
		 * @final
		 */
		IMAGE_MEMORY_ERROR : -701,
		
		/**
		 * Each runtime has an upper limit on a dimension of the image it can handle. If bigger, will throw this error.
		 *
		 * @property IMAGE_DIMENSIONS_ERROR
		 * @final
		 */
		IMAGE_DIMENSIONS_ERROR : -702,
		

		/**
		 * Mime type lookup table.
		 *
		 * @property mimeTypes
		 * @type Object
		 * @final
		 */
		mimeTypes : mimes,
		
		/**
		 * In some cases sniffing is the only way around :(
		 */
		ua: (function() {
			var nav = navigator, userAgent = nav.userAgent, vendor = nav.vendor, webkit, opera, safari;
			
			webkit = /WebKit/.test(userAgent);
			safari = webkit && vendor.indexOf('Apple') !== -1;
			opera = window.opera && window.opera.buildNumber;
			
			return {
				windows: navigator.platform.indexOf('Win') !== -1,
				ie: !webkit && !opera && (/MSIE/gi).test(userAgent) && (/Explorer/gi).test(nav.appName),
				webkit: webkit,
				gecko: !webkit && /Gecko/.test(userAgent),
				safari: safari,
				opera: !!opera
			};
		}()),
		
		/**
		 * Gets the true type of the built-in object (better version of typeof).
		 * @credits Angus Croll (http://javascriptweblog.wordpress.com/)
		 *
		 * @param {Object} o Object to check.
		 * @return {String} Object [[Class]]
		 */
		typeOf: function(o) {
			return ({}).toString.call(o).match(/\s([a-z|A-Z]+)/)[1].toLowerCase();
		},

		/**
		 * Extends the specified object with another object.
		 *
		 * @method extend
		 * @param {Object} target Object to extend.
		 * @param {Object..} obj Multiple objects to extend with.
		 * @return {Object} Same as target, the extended object.
		 */
		extend : function(target) {
			plupload.each(arguments, function(arg, i) {
				if (i > 0) {
					plupload.each(arg, function(value, key) {
						target[key] = value;
					});
				}
			});

			return target;
		},

		/**
		 * Cleans the specified name from national characters (diacritics). The result will be a name with only a-z, 0-9 and _.
		 *
		 * @method cleanName
		 * @param {String} s String to clean up.
		 * @return {String} Cleaned string.
		 */
		cleanName : function(name) {
			var i, lookup;

			// Replace diacritics
			lookup = [
				/[\300-\306]/g, 'A', /[\340-\346]/g, 'a', 
				/\307/g, 'C', /\347/g, 'c',
				/[\310-\313]/g, 'E', /[\350-\353]/g, 'e',
				/[\314-\317]/g, 'I', /[\354-\357]/g, 'i',
				/\321/g, 'N', /\361/g, 'n',
				/[\322-\330]/g, 'O', /[\362-\370]/g, 'o',
				/[\331-\334]/g, 'U', /[\371-\374]/g, 'u'
			];

			for (i = 0; i < lookup.length; i += 2) {
				name = name.replace(lookup[i], lookup[i + 1]);
			}

			// Replace whitespace
			name = name.replace(/\s+/g, '_');

			// Remove anything else
			name = name.replace(/[^a-z0-9_\-\.]+/gi, '');

			return name;
		},

		/**
		 * Adds a specific upload runtime like for example flash or gears.
		 *
		 * @method addRuntime
		 * @param {String} name Runtime name for example flash.
		 * @param {Object} obj Object containing init/destroy method.
		 */
		addRuntime : function(name, runtime) {			
			runtime.name = name;
			runtimes[name] = runtime;
			runtimes.push(runtime);

			return runtime;
		},

		/**
		 * Generates an unique ID. This is 99.99% unique since it takes the current time and 5 random numbers.
		 * The only way a user would be able to get the same ID is if the two persons at the same exact milisecond manages
		 * to get 5 the same random numbers between 0-65535 it also uses a counter so each call will be guaranteed to be page unique.
		 * It's more probable for the earth to be hit with an ansteriod. You can also if you want to be 100% sure set the plupload.guidPrefix property
		 * to an user unique key.
		 *
		 * @method guid
		 * @return {String} Virtually unique id.
		 */
		guid : function() {
			var guid = new Date().getTime().toString(32), i;

			for (i = 0; i < 5; i++) {
				guid += Math.floor(Math.random() * 65535).toString(32);
			}

			return (plupload.guidPrefix || 'p') + guid + (count++).toString(32);
		},

		/**
		 * Builds a full url out of a base URL and an object with items to append as query string items.
		 *
		 * @param {String} url Base URL to append query string items to.
		 * @param {Object} items Name/value object to serialize as a querystring.
		 * @return {String} String with url + serialized query string items.
		 */
		buildUrl : function(url, items) {
			var query = '';

			plupload.each(items, function(value, name) {
				query += (query ? '&' : '') + encodeURIComponent(name) + '=' + encodeURIComponent(value);
			});

			if (query) {
				url += (url.indexOf('?') > 0 ? '&' : '?') + query;
			}

			return url;
		},

		/**
		 * Executes the callback function for each item in array/object. If you return false in the
		 * callback it will break the loop.
		 *
		 * @param {Object} obj Object to iterate.
		 * @param {function} callback Callback function to execute for each item.
		 */
		each : function(obj, callback) {
			var length, key, i;

			if (obj) {
				length = obj.length;

				if (length === undef) {
					// Loop object items
					for (key in obj) {
						if (obj.hasOwnProperty(key)) {
							if (callback(obj[key], key) === false) {
								return;
							}
						}
					}
				} else {
					// Loop array items
					for (i = 0; i < length; i++) {
						if (callback(obj[i], i) === false) {
							return;
						}
					}
				}
			}
		},

		/**
		 * Formats the specified number as a size string for example 1024 becomes 1 KB.
		 *
		 * @method formatSize
		 * @param {Number} size Size to format as string.
		 * @return {String} Formatted size string.
		 */
		formatSize : function(size) {
			if (size === undef || /\D/.test(size)) {
				return plupload.translate('N/A');
			}
			
			// GB
			if (size > 1073741824) {
				return Math.round(size / 1073741824, 1) + " GB";
			}

			// MB
			if (size > 1048576) {
				return Math.round(size / 1048576, 1) + " MB";
			}

			// KB
			if (size > 1024) {
				return Math.round(size / 1024, 1) + " KB";
			}

			return size + " b";
		},

		/**
		 * Returns the absolute x, y position of an Element. The position will be returned in a object with x, y fields.
		 *
		 * @method getPos
		 * @param {Element} node HTML element or element id to get x, y position from.
		 * @param {Element} root Optional root element to stop calculations at.
		 * @return {object} Absolute position of the specified element object with x, y fields.
		 */
		 getPos : function(node, root) {
			var x = 0, y = 0, parent, doc = document, nodeRect, rootRect;

			node = node;
			root = root || doc.body;

			// Returns the x, y cordinate for an element on IE 6 and IE 7
			function getIEPos(node) {
				var bodyElm, rect, x = 0, y = 0;

				if (node) {
					rect = node.getBoundingClientRect();
					bodyElm = doc.compatMode === "CSS1Compat" ? doc.documentElement : doc.body;
					x = rect.left + bodyElm.scrollLeft;
					y = rect.top + bodyElm.scrollTop;
				}

				return {
					x : x,
					y : y
				};
			}

			// Use getBoundingClientRect on IE 6 and IE 7 but not on IE 8 in standards mode
			if (node && node.getBoundingClientRect && (navigator.userAgent.indexOf('MSIE') > 0 && doc.documentMode !== 8)) {
				nodeRect = getIEPos(node);
				rootRect = getIEPos(root);

				return {
					x : nodeRect.x - rootRect.x,
					y : nodeRect.y - rootRect.y
				};
			}

			parent = node;
			while (parent && parent != root && parent.nodeType) {
				x += parent.offsetLeft || 0;
				y += parent.offsetTop || 0;
				parent = parent.offsetParent;
			}

			parent = node.parentNode;
			while (parent && parent != root && parent.nodeType) {
				x -= parent.scrollLeft || 0;
				y -= parent.scrollTop || 0;
				parent = parent.parentNode;
			}

			return {
				x : x,
				y : y
			};
		},

		/**
		 * Returns the size of the specified node in pixels.
		 *
		 * @param {Node} node Node to get the size of.
		 * @return {Object} Object with a w and h property.
		 */
		getSize : function(node) {
			return {
				w : node.offsetWidth || node.clientWidth,
				h : node.offsetHeight || node.clientHeight
			};
		},

		/**
		 * Parses the specified size string into a byte value. For example 10kb becomes 10240.
		 *
		 * @method parseSize
		 * @param {String/Number} size String to parse or number to just pass through.
		 * @return {Number} Size in bytes.
		 */
		parseSize : function(size) {
			var mul;

			if (typeof(size) == 'string') {
				size = /^([0-9]+)([mgk]?)$/.exec(size.toLowerCase().replace(/[^0-9mkg]/g, ''));
				mul = size[2];
				size = +size[1];

				if (mul == 'g') {
					size *= 1073741824;
				}

				if (mul == 'm') {
					size *= 1048576;
				}

				if (mul == 'k') {
					size *= 1024;
				}
			}

			return size;
		},

		/**
		 * Encodes the specified string.
		 *
		 * @method xmlEncode
		 * @param {String} s String to encode.
		 * @return {String} Encoded string.
		 */
		xmlEncode : function(str) {
			return str ? ('' + str).replace(xmlEncodeRegExp, function(chr) {
				return xmlEncodeChars[chr] ? '&' + xmlEncodeChars[chr] + ';' : chr;
			}) : str;
		},

		/**
		 * Forces anything into an array.
		 *
		 * @method toArray
		 * @param {Object} obj Object with length field.
		 * @return {Array} Array object containing all items.
		 */
		toArray : function(obj) {
			var i, arr = [];

			for (i = 0; i < obj.length; i++) {
				arr[i] = obj[i];
			}

			return arr;
		},
		
		/**
		 * Find an element in array and return it's index if present, otherwise return -1.
		 *
		 * @method inArray
		 * @param {mixed} needle Element to find
		 * @param {Array} array
		 * @return {Int} Index of the element, or -1 if not found
		 */
		inArray : function(needle, array) {			
			if (array) {
				if (Array.prototype.indexOf) {
					return Array.prototype.indexOf.call(array, needle);
				}
			
				for (var i = 0, length = array.length; i < length; i++) {
					if (array[i] === needle) {
						return i;
					}
				}
			}
			return -1;
		},

		/**
		 * Extends the language pack object with new items.
		 *
		 * @param {Object} pack Language pack items to add.
		 * @return {Object} Extended language pack object.
		 */
		addI18n : function(pack) {
			return plupload.extend(i18n, pack);
		},

		/**
		 * Translates the specified string by checking for the english string in the language pack lookup.
		 *
		 * @param {String} str String to look for.
		 * @return {String} Translated string or the input string if it wasn't found.
		 */
		translate : function(str) {
			return i18n[str] || str;
		},
		
		/**
		 * Checks if object is empty.
		 *
		 * @param {Object} obj Object to check.
		 * @return {Boolean}
		 */
		isEmptyObj : function(obj) {
			if (obj === undef) return true;
			
			for (var prop in obj) {
				return false;	
			}
			return true;
		},
		
		/**
		 * Checks if specified DOM element has specified class.
		 *
		 * @param {Object} obj DOM element like object to add handler to.
		 * @param {String} name Class name
		 */
		hasClass : function(obj, name) {
			var regExp;
		
			if (obj.className == '') {
				return false;
			}

			regExp = new RegExp("(^|\\s+)"+name+"(\\s+|$)");

			return regExp.test(obj.className);
		},
		
		/**
		 * Adds specified className to specified DOM element.
		 *
		 * @param {Object} obj DOM element like object to add handler to.
		 * @param {String} name Class name
		 */
		addClass : function(obj, name) {
			if (!plupload.hasClass(obj, name)) {
				obj.className = obj.className == '' ? name : obj.className.replace(/\s+$/, '')+' '+name;
			}
		},
		
		/**
		 * Removes specified className from specified DOM element.
		 *
		 * @param {Object} obj DOM element like object to add handler to.
		 * @param {String} name Class name
		 */
		removeClass : function(obj, name) {
			var regExp = new RegExp("(^|\\s+)"+name+"(\\s+|$)");
			
			obj.className = obj.className.replace(regExp, function($0, $1, $2) {
				return $1 === ' ' && $2 === ' ' ? ' ' : '';
			});
		},
    
		/**
		 * Returns a given computed style of a DOM element.
		 *
		 * @param {Object} obj DOM element like object.
		 * @param {String} name Style you want to get from the DOM element
		 */
		getStyle : function(obj, name) {
			if (obj.currentStyle) {
				return obj.currentStyle[name];
			} else if (window.getComputedStyle) {
				return window.getComputedStyle(obj, null)[name];
			}
		},

		/**
		 * Adds an event handler to the specified object and store reference to the handler
		 * in objects internal Plupload registry (@see removeEvent).
		 *
		 * @param {Object} obj DOM element like object to add handler to.
		 * @param {String} name Name to add event listener to.
		 * @param {Function} callback Function to call when event occurs.
		 * @param {String} (optional) key that might be used to add specifity to the event record.
		 */
		addEvent : function(obj, name, callback) {
			var func, events, types, key;
			
			// if passed in, event will be locked with this key - one would need to provide it to removeEvent
			key = arguments[3];
						
			name = name.toLowerCase();
						
			// Initialize unique identifier if needed
			if (uid === undef) {
				uid = 'Plupload_' + plupload.guid();
			}

			// Add event listener
			if (obj.addEventListener) {
				func = callback;
				
				obj.addEventListener(name, func, false);
			} else if (obj.attachEvent) {
				
				func = function() {
					var evt = window.event;

					if (!evt.target) {
						evt.target = evt.srcElement;
					}

					evt.preventDefault = preventDefault;
					evt.stopPropagation = stopPropagation;

					callback(evt);
				};
				obj.attachEvent('on' + name, func);
			} 
			
			// Log event handler to objects internal Plupload registry
			if (obj[uid] === undef) {
				obj[uid] = plupload.guid();
			}
			
			if (!eventhash.hasOwnProperty(obj[uid])) {
				eventhash[obj[uid]] = {};
			}
			
			events = eventhash[obj[uid]];
			
			if (!events.hasOwnProperty(name)) {
				events[name] = [];
			}
					
			events[name].push({
				func: func,
				orig: callback, // store original callback for IE
				key: key
			});
		},
		
		
		/**
		 * Remove event handler from the specified object. If third argument (callback)
		 * is not specified remove all events with the specified name.
		 *
		 * @param {Object} obj DOM element to remove event listener(s) from.
		 * @param {String} name Name of event listener to remove.
		 * @param {Function|String} (optional) might be a callback or unique key to match.
		 */
		removeEvent: function(obj, name) {
			var type, callback, key;
			
			// match the handler either by callback or by key	
			if (typeof(arguments[2]) == "function") {
				callback = arguments[2];
			} else {
				key = arguments[2];
			}
						
			name = name.toLowerCase();
			
			if (obj[uid] && eventhash[obj[uid]] && eventhash[obj[uid]][name]) {
				type = eventhash[obj[uid]][name];
			} else {
				return;
			}
			
				
			for (var i=type.length-1; i>=0; i--) {
				// undefined or not, key should match			
				if (type[i].key === key || type[i].orig === callback) {
										
					if (obj.detachEvent) {
						obj.detachEvent('on'+name, type[i].func);
					} else if (obj.removeEventListener) {
						obj.removeEventListener(name, type[i].func, false);		
					}
					
					type[i].orig = null;
					type[i].func = null;
					
					type.splice(i, 1);
					
					// If callback was passed we are done here, otherwise proceed
					if (callback !== undef) {
						break;
					}
				}			
			}	
			
			// If event array got empty, remove it
			if (!type.length) {
				delete eventhash[obj[uid]][name];
			}
			
			// If Plupload registry has become empty, remove it
			if (plupload.isEmptyObj(eventhash[obj[uid]])) {
				delete eventhash[obj[uid]];
				
				// IE doesn't let you remove DOM object property with - delete
				try {
					delete obj[uid];
				} catch(e) {
					obj[uid] = undef;
				}
			}
		},
		
		
		/**
		 * Remove all kind of events from the specified object
		 *
		 * @param {Object} obj DOM element to remove event listeners from.
		 * @param {String} (optional) unique key to match, when removing events.
		 */
		removeAllEvents: function(obj) {
			var key = arguments[1];
			
			if (obj[uid] === undef || !obj[uid]) {
				return;
			}
			
			plupload.each(eventhash[obj[uid]], function(events, name) {
				plupload.removeEvent(obj, name, key);
			});		
		}
	};
	

	/**
	 * Uploader class, an instance of this class will be created for each upload field.
	 *
	 * @example
	 * var uploader = new plupload.Uploader({
	 *     runtimes : 'gears,html5,flash',
	 *     browse_button : 'button_id'
	 * });
	 *
	 * uploader.bind('Init', function(up) {
	 *     alert('Supports drag/drop: ' + (!!up.features.dragdrop));
	 * });
	 *
	 * uploader.bind('FilesAdded', function(up, files) {
	 *     alert('Selected files: ' + files.length);
	 * });
	 *
	 * uploader.bind('QueueChanged', function(up) {
	 *     alert('Queued files: ' + uploader.files.length);
	 * });
	 *
	 * uploader.init();
	 *
	 * @class plupload.Uploader
	 */

	/**
	 * Constructs a new uploader instance.
	 *
	 * @constructor
	 * @method Uploader
	 * @param {Object} settings Initialization settings, to be used by the uploader instance and runtimes.
	 */
	plupload.Uploader = function(settings) {
		var events = {}, total, files = [], startTime, disabled = false;

		// Inital total state
		total = new plupload.QueueProgress();

		// Default settings
		settings = plupload.extend({
			chunk_size : 0,
			multipart : true,
			multi_selection : true,
			file_data_name : 'file',
			filters : []
		}, settings);

		// Private methods
		function uploadNext() {
			var file, count = 0, i;

			if (this.state == plupload.STARTED) {
				// Find first QUEUED file
        files = this.files;
				for (i = 0; i < files.length; i++) {
					if (!file && files[i].status == plupload.QUEUED) {
						file = files[i];
						file.status = plupload.UPLOADING;
						if (this.trigger("BeforeUpload", file)) {
							this.trigger("UploadFile", file);
						}
					} else {
						count++;
					}
				}

				// All files are DONE or FAILED
				if (count == files.length) {
					this.stop();
					this.trigger("UploadComplete", files);
				}
			}
		}

		function calc() {
			var i, file;

			// Reset stats
			total.reset();

			// Check status, size, loaded etc on all files
			for (i = 0; i < files.length; i++) {
				file = files[i];

				if (file.size !== undef) {
					total.size += file.size;
					total.loaded += file.loaded;
				} else {
					total.size = undef;
				}

				if (file.status == plupload.DONE) {
					total.uploaded++;
				} else if (file.status == plupload.FAILED) {
					total.failed++;
				} else {
					total.queued++;
				}
			}

			// If we couldn't calculate a total file size then use the number of files to calc percent
			if (total.size === undef) {
				total.percent = files.length > 0 ? Math.ceil(total.uploaded / files.length * 100) : 0;
			} else {
				total.bytesPerSec = Math.ceil(total.loaded / ((+new Date() - startTime || 1) / 1000.0));
				total.percent = total.size > 0 ? Math.ceil(total.loaded / total.size * 100) : 0;
			}
		}

		// Add public methods
		plupload.extend(this, {
			/**
			 * Current state of the total uploading progress. This one can either be plupload.STARTED or plupload.STOPPED.
			 * These states are controlled by the stop/start methods. The default value is STOPPED.
			 *
			 * @property state
			 * @type Number
			 */
			state : plupload.STOPPED,
			
			/**
			 * Current runtime name.
			 *
			 * @property runtime
			 * @type String
			 */
			runtime: '',

			/**
			 * Map of features that are available for the uploader runtime. Features will be filled
			 * before the init event is called, these features can then be used to alter the UI for the end user.
			 * Some of the current features that might be in this map is: dragdrop, chunks, jpgresize, pngresize.
			 *
			 * @property features
			 * @type Object
			 */
			features : {},

			/**
			 * Current upload queue, an array of File instances.
			 *
			 * @property files
			 * @type Array
			 * @see plupload.File
			 */
			files : files,

			/**
			 * Object with name/value settings.
			 *
			 * @property settings
			 * @type Object
			 */
			settings : settings,

			/**
			 * Total progess information. How many files has been uploaded, total percent etc.
			 *
			 * @property total
			 * @type plupload.QueueProgress
			 */
			total : total,

			/**
			 * Unique id for the Uploader instance.
			 *
			 * @property id
			 * @type String
			 */
			id : plupload.guid(),

			/**
			 * Initializes the Uploader instance and adds internal event listeners.
			 *
			 * @method init
			 */
			init : function() {
				var self = this, i, runtimeList, a, runTimeIndex = 0, items;

				if (typeof(settings.preinit) == "function") {
					settings.preinit(self);
				} else {
					plupload.each(settings.preinit, function(func, name) {
						self.bind(name, func);
					});
				}

				settings.page_url = settings.page_url || document.location.pathname.replace(/\/[^\/]+$/g, '/');

				// If url is relative force it absolute to the current page
				if (!/^(\w+:\/\/|\/)/.test(settings.url)) {
					settings.url = settings.page_url + settings.url;
				}

				// Convert settings
				settings.chunk_size = plupload.parseSize(settings.chunk_size);
				settings.max_file_size = plupload.parseSize(settings.max_file_size);

				// Add files to queue
				self.bind('FilesAdded', function(up, selected_files) {
					var i, file, count = 0, extensionsRegExp, filters = settings.filters;

					// Convert extensions to regexp
					if (filters && filters.length) {
						extensionsRegExp = [];
						
						plupload.each(filters, function(filter) {
							plupload.each(filter.extensions.split(/,/), function(ext) {
								if (/^\s*\*\s*$/.test(ext)) {
									extensionsRegExp.push('\\.*');
								} else {
									extensionsRegExp.push('\\.' + ext.replace(new RegExp('[' + ('/^$.*+?|()[]{}\\'.replace(/./g, '\\$&')) + ']', 'g'), '\\$&'));
								}
							});
						});
						
						extensionsRegExp = new RegExp(extensionsRegExp.join('|') + '$', 'i');
					}

					for (i = 0; i < selected_files.length; i++) {
						file = selected_files[i];
						file.loaded = 0;
						file.percent = 0;
						file.status = plupload.QUEUED;

						// Invalid file extension
						if (extensionsRegExp && !extensionsRegExp.test(file.name)) {
							up.trigger('Error', {
								code : plupload.FILE_EXTENSION_ERROR,
								message : plupload.translate('File extension error.'),
								file : file
							});

							continue;
						}

						// Invalid file size
						if (file.size !== undef && file.size > settings.max_file_size) {
							up.trigger('Error', {
								code : plupload.FILE_SIZE_ERROR,
								message : plupload.translate('File size error.'),
								file : file
							});

							continue;
						}

						// Add valid file to list
						files.push(file);
						count++;
					}

					// Only trigger QueueChanged event if any files where added
					if (count) {
						delay(function() {
							self.trigger("QueueChanged");
							self.refresh();
						}, 1);
					} else {
						return false; // Stop the FilesAdded event from immediate propagation
					}
				});

				// Generate unique target filenames
				if (settings.unique_names) {
					self.bind("UploadFile", function(up, file) {
						var matches = file.name.match(/\.([^.]+)$/), ext = "tmp";

						if (matches) {
							ext = matches[1];
						}

						file.target_name = file.id + '.' + ext;
					});
				}

				self.bind('UploadProgress', function(up, file) {
					file.percent = file.size > 0 ? Math.ceil(file.loaded / file.size * 100) : 100;
					calc();
				});

				self.bind('StateChanged', function(up) {
					if (up.state == plupload.STARTED) {
						// Get start time to calculate bps
						startTime = (+new Date());
						
					} else if (up.state == plupload.STOPPED) {						
						// Reset currently uploading files
						for (i = up.files.length - 1; i >= 0; i--) {
							if (up.files[i].status == plupload.UPLOADING) {
								up.files[i].status = plupload.QUEUED;
								calc();
							}
						}
					}
				});

				self.bind('QueueChanged', calc);

				self.bind("Error", function(up, err) {
					// Set failed status if an error occured on a file
					if (err.file) {
						err.file.status = plupload.FAILED;
						calc();

						// Upload next file but detach it from the error event
						// since other custom listeners might want to stop the queue
						if (up.state == plupload.STARTED) {
							delay(function() {
								uploadNext.call(self);
							}, 1);
						}
					}
				});

				self.bind("FileUploaded", function(up, file) {
					file.status = plupload.DONE;
					file.loaded = file.size;
					up.trigger('UploadProgress', file);

					// Upload next file but detach it from the error event
					// since other custom listeners might want to stop the queue
					delay(function() {
						uploadNext.call(self);
					}, 1);
				});

				// Setup runtimeList
				if (settings.runtimes) {
					runtimeList = [];
					items = settings.runtimes.split(/\s?,\s?/);

					for (i = 0; i < items.length; i++) {
						if (runtimes[items[i]]) {
							runtimeList.push(runtimes[items[i]]);
						}
					}
				} else {
					runtimeList = runtimes;
				}

				// Call init on each runtime in sequence
				function callNextInit() {
					var runtime = runtimeList[runTimeIndex++], features, requiredFeatures, i;

					if (runtime) {
						features = runtime.getFeatures();

						// Check if runtime supports required features
						requiredFeatures = self.settings.required_features;
						if (requiredFeatures) {
							requiredFeatures = requiredFeatures.split(',');

							for (i = 0; i < requiredFeatures.length; i++) {
								// Specified feature doesn't exist
								if (!features[requiredFeatures[i]]) {
									callNextInit();
									return;
								}
							}
						}

						// Try initializing the runtime
						runtime.init(self, function(res) {
							if (res && res.success) {
								// Successful initialization
								self.features = features;
								self.runtime = runtime.name;
								self.trigger('Init', {runtime : runtime.name});
								self.trigger('PostInit');
								self.refresh();
							} else {
								callNextInit();
							}
						});
					} else {
						// Trigger an init error if we run out of runtimes
						self.trigger('Error', {
							code : plupload.INIT_ERROR,
							message : plupload.translate('Init error.')
						});
					}
				}

				callNextInit();

				if (typeof(settings.init) == "function") {
					settings.init(self);
				} else {
					plupload.each(settings.init, function(func, name) {
						self.bind(name, func);
					});
				}
			},

			/**
			 * Refreshes the upload instance by dispatching out a refresh event to all runtimes.
			 * This would for example reposition flash/silverlight shims on the page.
			 *
			 * @method refresh
			 */
			refresh : function() {
				this.trigger("Refresh");
			},

			/**
			 * Starts uploading the queued files.
			 *
			 * @method start
			 */
			start : function() {
				if (this.state != plupload.STARTED) {
					this.state = plupload.STARTED;
					this.trigger("StateChanged");	
					
					uploadNext.call(this);				
				}
			},

			/**
			 * Stops the upload of the queued files.
			 *
			 * @method stop
			 */
			stop : function() {
				if (this.state != plupload.STOPPED) {
					this.state = plupload.STOPPED;	
					this.trigger("CancelUpload");				
					this.trigger("StateChanged");
				}
			},
			
			/** 
			 * Disables/enables browse button on request.
			 *
			 * @method disableBrowse
			 * @param {Boolean} disable Whether to disable or enable (default: true)
			 */
			disableBrowse : function() {
				disabled = arguments[0] !== undef ? arguments[0] : true;
				this.trigger("DisableBrowse", disabled);
			},

			/**
			 * Returns the specified file object by id.
			 *
			 * @method getFile
			 * @param {String} id File id to look for.
			 * @return {plupload.File} File object or undefined if it wasn't found;
			 */
			getFile : function(id) {
				var i;

				for (i = files.length - 1; i >= 0; i--) {
					if (files[i].id === id) {
						return files[i];
					}
				}
			},

			/**
			 * Removes a specific file.
			 *
			 * @method removeFile
			 * @param {plupload.File} file File to remove from queue.
			 */
			removeFile : function(file) {
				var i;

				for (i = files.length - 1; i >= 0; i--) {
					if (files[i].id === file.id) {
						return this.splice(i, 1)[0];
					}
				}
			},

			/**
			 * Removes part of the queue and returns the files removed. This will also trigger the FilesRemoved and QueueChanged events.
			 *
			 * @method splice
			 * @param {Number} start (Optional) Start index to remove from.
			 * @param {Number} length (Optional) Lengh of items to remove.
			 * @return {Array} Array of files that was removed.
			 */
			splice : function(start, length) {
				var removed;

				// Splice and trigger events
				removed = files.splice(start === undef ? 0 : start, length === undef ? files.length : length);

				this.trigger("FilesRemoved", removed);
				this.trigger("QueueChanged");

				return removed;
			},

			/**
			 * Dispatches the specified event name and it's arguments to all listeners.
			 *
			 *
			 * @method trigger
			 * @param {String} name Event name to fire.
			 * @param {Object..} Multiple arguments to pass along to the listener functions.
			 */
			trigger : function(name) {
				var list = events[name.toLowerCase()], i, args;

				// console.log(name, arguments);

				if (list) {
					// Replace name with sender in args
					args = Array.prototype.slice.call(arguments);
					args[0] = this;

					// Dispatch event to all listeners
					for (i = 0; i < list.length; i++) {
						// Fire event, break chain if false is returned
						if (list[i].func.apply(list[i].scope, args) === false) {
							return false;
						}
					}
				}

				return true;
			},
			
			/**
			 * Check whether uploader has any listeners to the specified event.
			 *
			 * @method hasEventListener
			 * @param {String} name Event name to check for.
			 */
			hasEventListener : function(name) {
				return !!events[name.toLowerCase()];
			},

			/**
			 * Adds an event listener by name.
			 *
			 * @method bind
			 * @param {String} name Event name to listen for.
			 * @param {function} func Function to call ones the event gets fired.
			 * @param {Object} scope Optional scope to execute the specified function in.
			 */
			bind : function(name, func, scope) {
				var list;

				name = name.toLowerCase();
				list = events[name] || [];
				list.push({func : func, scope : scope || this});
				events[name] = list;
			},

			/**
			 * Removes the specified event listener.
			 *
			 * @method unbind
			 * @param {String} name Name of event to remove.
			 * @param {function} func Function to remove from listener.
			 */
			unbind : function(name) {
				name = name.toLowerCase();

				var list = events[name], i, func = arguments[1];

				if (list) {
					if (func !== undef) {
						for (i = list.length - 1; i >= 0; i--) {
							if (list[i].func === func) {
								list.splice(i, 1);
									break;
							}
						}
					} else {
						list = [];
					}

					// delete event list if it has become empty
					if (!list.length) {
						delete events[name];
					}
				}
			},

			/**
			 * Removes all event listeners.
			 *
			 * @method unbindAll
			 */
			unbindAll : function() {
				var self = this;
				
				plupload.each(events, function(list, name) {
					self.unbind(name);
				});
			},
			
			/**
			 * Destroys Plupload instance and cleans after itself.
			 *
			 * @method destroy
			 */
			destroy : function() {	
				this.stop();						
				this.trigger('Destroy');
				
				// Clean-up after uploader itself
				this.unbindAll();
			}

			/**
			 * Fires when the current RunTime has been initialized.
			 *
			 * @event Init
			 * @param {plupload.Uploader} uploader Uploader instance sending the event.
			 */

			/**
			 * Fires after the init event incase you need to perform actions there.
			 *
			 * @event PostInit
			 * @param {plupload.Uploader} uploader Uploader instance sending the event.
			 */

			/**
			 * Fires when the silverlight/flash or other shim needs to move.
			 *
			 * @event Refresh
			 * @param {plupload.Uploader} uploader Uploader instance sending the event.
			 */
	
			/**
			 * Fires when the overall state is being changed for the upload queue.
			 *
			 * @event StateChanged
			 * @param {plupload.Uploader} uploader Uploader instance sending the event.
			 */

			/**
			 * Fires when a file is to be uploaded by the runtime.
			 *
			 * @event UploadFile
			 * @param {plupload.Uploader} uploader Uploader instance sending the event.
			 * @param {plupload.File} file File to be uploaded.
			 */

			/**
			 * Fires when just before a file is uploaded. This event enables you to override settings
			 * on the uploader instance before the file is uploaded.
			 *
			 * @event BeforeUpload
			 * @param {plupload.Uploader} uploader Uploader instance sending the event.
			 * @param {plupload.File} file File to be uploaded.
			 */

			/**
			 * Fires when the file queue is changed. In other words when files are added/removed to the files array of the uploader instance.
			 *
			 * @event QueueChanged
			 * @param {plupload.Uploader} uploader Uploader instance sending the event.
			 */
	
			/**
			 * Fires while a file is being uploaded. Use this event to update the current file upload progress.
			 *
			 * @event UploadProgress
			 * @param {plupload.Uploader} uploader Uploader instance sending the event.
			 * @param {plupload.File} file File that is currently being uploaded.
			 */

			/**
			 * Fires while a file was removed from queue.
			 *
			 * @event FilesRemoved
			 * @param {plupload.Uploader} uploader Uploader instance sending the event.
			 * @param {Array} files Array of files that got removed.
			 */

			/**
			 * Fires while when the user selects files to upload.
			 *
			 * @event FilesAdded
			 * @param {plupload.Uploader} uploader Uploader instance sending the event.
			 * @param {Array} files Array of file objects that was added to queue/selected by the user.
			 */

			/**
			 * Fires when a file is successfully uploaded.
			 *
			 * @event FileUploaded
			 * @param {plupload.Uploader} uploader Uploader instance sending the event.
			 * @param {plupload.File} file File that was uploaded.
			 * @param {Object} response Object with response properties.
			 */

			/**
			 * Fires when file chunk is uploaded.
			 *
			 * @event ChunkUploaded
			 * @param {plupload.Uploader} uploader Uploader instance sending the event.
			 * @param {plupload.File} file File that the chunk was uploaded for.
			 * @param {Object} response Object with response properties.
			 */

			/**
			 * Fires when all files in a queue are uploaded.
			 *
			 * @event UploadComplete
			 * @param {plupload.Uploader} uploader Uploader instance sending the event.
			 * @param {Array} files Array of file objects that was added to queue/selected by the user.
			 */

			/**
			 * Fires when a error occurs.
			 *
			 * @event Error
			 * @param {plupload.Uploader} uploader Uploader instance sending the event.
			 * @param {Object} error Contains code, message and sometimes file and other details.
			 */
			 
			 /**
			 * Fires when destroy method is called.
			 *
			 * @event Destroy
			 * @param {plupload.Uploader} uploader Uploader instance sending the event.
			 */
		});
	};

	/**
	 * File instance.
	 *
	 * @class plupload.File
	 * @param {String} name Name of the file.
	 * @param {Number} size File size.
	 */

	/**
	 * Constructs a new file instance.
	 *
	 * @constructor
	 * @method File
	 * @param {String} id Unique file id.
	 * @param {String} name File name.
	 * @param {Number} size File size in bytes.
	 */
	plupload.File = function(id, name, size) {
		var self = this; // Setup alias for self to reduce code size when it's compressed

		/**
		 * File id this is a globally unique id for the specific file.
		 *
		 * @property id
		 * @type String
		 */
		self.id = id;

		/**
		 * File name for example "myfile.gif".
		 *
		 * @property name
		 * @type String
		 */
		self.name = name;

		/**
		 * File size in bytes.
		 *
		 * @property size
		 * @type Number
		 */
		self.size = size;

		/**
		 * Number of bytes uploaded of the files total size.
		 *
		 * @property loaded
		 * @type Number
		 */
		self.loaded = 0;

		/**
		 * Number of percentage uploaded of the file.
		 *
		 * @property percent
		 * @type Number
		 */
		self.percent = 0;

		/**
		 * Status constant matching the plupload states QUEUED, UPLOADING, FAILED, DONE.
		 *
		 * @property status
		 * @type Number
		 * @see plupload
		 */
		self.status = 0;
	};

	/**
	 * Runtime class gets implemented by each upload runtime.
	 *
	 * @class plupload.Runtime
	 * @static
	 */
	plupload.Runtime = function() {
		/**
		 * Returns a list of supported features for the runtime.
		 *
		 * @return {Object} Name/value object with supported features.
		 */
		this.getFeatures = function() {
		};

		/**
		 * Initializes the upload runtime. This method should add necessary items to the DOM and register events needed for operation. 
		 *
		 * @method init
		 * @param {plupload.Uploader} uploader Uploader instance that needs to be initialized.
		 * @param {function} callback Callback function to execute when the runtime initializes or fails to initialize. If it succeeds an object with a parameter name success will be set to true.
		 */
		this.init = function(uploader, callback) {
		};
	};

	/**
	 * Runtime class gets implemented by each upload runtime.
	 *
	 * @class plupload.QueueProgress
	 */

	/**
	 * Constructs a queue progress.
	 *
	 * @constructor
	 * @method QueueProgress
	 */
	 plupload.QueueProgress = function() {
		var self = this; // Setup alias for self to reduce code size when it's compressed

		/**
		 * Total queue file size.
		 *
		 * @property size
		 * @type Number
		 */
		self.size = 0;

		/**
		 * Total bytes uploaded.
		 *
		 * @property loaded
		 * @type Number
		 */
		self.loaded = 0;

		/**
		 * Number of files uploaded.
		 *
		 * @property uploaded
		 * @type Number
		 */
		self.uploaded = 0;

		/**
		 * Number of files failed to upload.
		 *
		 * @property failed
		 * @type Number
		 */
		self.failed = 0;

		/**
		 * Number of files yet to be uploaded.
		 *
		 * @property queued
		 * @type Number
		 */
		self.queued = 0;

		/**
		 * Total percent of the uploaded bytes.
		 *
		 * @property percent
		 * @type Number
		 */
		self.percent = 0;

		/**
		 * Bytes uploaded per second.
		 *
		 * @property bytesPerSec
		 * @type Number
		 */
		self.bytesPerSec = 0;

		/**
		 * Resets the progress to it's initial values.
		 *
		 * @method reset
		 */
		self.reset = function() {
			self.size = self.loaded = self.uploaded = self.failed = self.queued = self.percent = self.bytesPerSec = 0;
		};
	};

	// Create runtimes namespace
	plupload.runtimes = {};

	// Expose plupload namespace
	window.plupload = plupload;
})();

/**
 * plupload.html5.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */

// JSLint defined globals
/*global plupload:false, File:false, window:false, atob:false, FormData:false, FileReader:false, ArrayBuffer:false, Uint8Array:false, BlobBuilder:false, unescape:false */

(function(window, document, plupload, undef) {
	var html5files = {}, // queue of original File objects
		fakeSafariDragDrop;

	function readFileAsDataURL(file, callback) {
		var reader;

		// Use FileReader if it's available
		if ("FileReader" in window) {
			reader = new FileReader();
			reader.readAsDataURL(file);
			reader.onload = function() {
				callback(reader.result);
			};
		} else {
			return callback(file.getAsDataURL());
		}
	}

	function readFileAsBinary(file, callback) {
		var reader;

		// Use FileReader if it's available
		if ("FileReader" in window) {
			reader = new FileReader();
			reader.readAsBinaryString(file);
			reader.onload = function() {
				callback(reader.result);
			};
		} else {
			return callback(file.getAsBinary());
		}
	}

	function scaleImage(file, resize, mime, callback) {
		var canvas, context, img, scale,
			up = this;
			
		readFileAsDataURL(html5files[file.id], function(data) {
			// Setup canvas and context
			canvas = document.createElement("canvas");
			canvas.style.display = 'none';
			document.body.appendChild(canvas);
			context = canvas.getContext('2d');

			// Load image
			img = new Image();
			img.onerror = img.onabort = function() {
				// Failed to load, the image may be invalid
				callback({success : false});
			};
			img.onload = function() {
				var width, height, percentage, jpegHeaders, exifParser;
				
				if (!resize['width']) {
					resize['width'] = img.width;
				}
				
				if (!resize['height']) {
					resize['height'] = img.height;	
				}
				
				scale = Math.min(resize.width / img.width, resize.height / img.height);

				if (scale < 1 || (scale === 1 && mime === 'image/jpeg')) {
					width = Math.round(img.width * scale);
					height = Math.round(img.height * scale);

					// Scale image and canvas
					canvas.width = width;
					canvas.height = height;
					context.drawImage(img, 0, 0, width, height);
					
					// Preserve JPEG headers
					if (mime === 'image/jpeg') {
						jpegHeaders = new JPEG_Headers(atob(data.substring(data.indexOf('base64,') + 7)));
						if (jpegHeaders['headers'] && jpegHeaders['headers'].length) {
							exifParser = new ExifParser();			
											
							if (exifParser.init(jpegHeaders.get('exif')[0])) {
								// Set new width and height
								exifParser.setExif('PixelXDimension', width);
								exifParser.setExif('PixelYDimension', height);
																							
								// Update EXIF header
								jpegHeaders.set('exif', exifParser.getBinary());
								
								// trigger Exif events only if someone listens to them
								if (up.hasEventListener('ExifData')) {
									up.trigger('ExifData', file, exifParser.EXIF());
								}
								
								if (up.hasEventListener('GpsData')) {
									up.trigger('GpsData', file, exifParser.GPS());
								}
							}
						}
						
						if (resize['quality']) {							
							// Try quality property first
							try {
								data = canvas.toDataURL(mime, resize['quality'] / 100);	
							} catch (e) {
								data = canvas.toDataURL(mime);	
							}
						}
					} else {
						data = canvas.toDataURL(mime);
					}

					// Remove data prefix information and grab the base64 encoded data and decode it
					data = data.substring(data.indexOf('base64,') + 7);
					data = atob(data);

					// Restore JPEG headers if applicable
					if (jpegHeaders && jpegHeaders['headers'] && jpegHeaders['headers'].length) {
						data = jpegHeaders.restore(data);
						jpegHeaders.purge(); // free memory
					}

					// Remove canvas and execute callback with decoded image data
					canvas.parentNode.removeChild(canvas);
					callback({success : true, data : data});
				} else {
					// Image does not need to be resized
					callback({success : false});
				}
			};

			img.src = data;
		});
	}

	/**
	 * HMTL5 implementation. This runtime supports these features: dragdrop, jpgresize, pngresize.
	 *
	 * @static
	 * @class plupload.runtimes.Html5
	 * @extends plupload.Runtime
	 */
	plupload.runtimes.Html5 = plupload.addRuntime("html5", {
		/**
		 * Returns a list of supported features for the runtime.
		 *
		 * @return {Object} Name/value object with supported features.
		 */
		getFeatures : function() {
			var xhr, hasXhrSupport, hasProgress, canSendBinary, dataAccessSupport, sliceSupport;

			hasXhrSupport = hasProgress = dataAccessSupport = sliceSupport = false;
			
			if (window.XMLHttpRequest) {
				xhr = new XMLHttpRequest();
				hasProgress = !!xhr.upload;
				hasXhrSupport = !!(xhr.sendAsBinary || xhr.upload);
			}

			// Check for support for various features
			if (hasXhrSupport) {
				canSendBinary = !!(xhr.sendAsBinary || (window.Uint8Array && window.ArrayBuffer));
				
				// Set dataAccessSupport only for Gecko since BlobBuilder and XHR doesn't handle binary data correctly				
				dataAccessSupport = !!(File && (File.prototype.getAsDataURL || window.FileReader) && canSendBinary);
				sliceSupport = !!(File && (File.prototype.mozSlice || File.prototype.webkitSlice || File.prototype.slice)); 
			}

			// sniff out Safari for Windows and fake drag/drop
			fakeSafariDragDrop = plupload.ua.safari && plupload.ua.windows;

			return {
				html5: hasXhrSupport, // This is a special one that we check inside the init call
				dragdrop: (function() {
					// this comes directly from Modernizr: http://www.modernizr.com/
					var div = document.createElement('div');
					return ('draggable' in div) || ('ondragstart' in div && 'ondrop' in div);
				}()),
				jpgresize: dataAccessSupport,
				pngresize: dataAccessSupport,
				multipart: dataAccessSupport || !!window.FileReader || !!window.FormData,
				canSendBinary: canSendBinary,
				// gecko 2/5/6 can't send blob with FormData: https://bugzilla.mozilla.org/show_bug.cgi?id=649150 
				cantSendBlobInFormData: !!(plupload.ua.gecko && window.FormData && window.FileReader && !FileReader.prototype.readAsArrayBuffer),
				progress: hasProgress,
				chunks: sliceSupport,
				// Safari on Windows has problems when selecting multiple files
				multi_selection: !(plupload.ua.safari && plupload.ua.windows),
				// WebKit and Gecko 2+ can trigger file dialog progrmmatically
				triggerDialog: (plupload.ua.gecko && window.FormData || plupload.ua.webkit) 
			};
		},

		/**
		 * Initializes the upload runtime.
		 *
		 * @method init
		 * @param {plupload.Uploader} uploader Uploader instance that needs to be initialized.
		 * @param {function} callback Callback to execute when the runtime initializes or fails to initialize. If it succeeds an object with a parameter name success will be set to true.
		 */
		init : function(uploader, callback) {
			var features, xhr;

			function addSelectedFiles(native_files) {
				var file, i, files = [], id, fileNames = {};

				// Add the selected files to the file queue
				for (i = 0; i < native_files.length; i++) {
					file = native_files[i];
										
					// Safari on Windows will add first file from dragged set multiple times
					// @see: https://bugs.webkit.org/show_bug.cgi?id=37957
					if (fileNames[file.name]) {
						continue;
					}
					fileNames[file.name] = true;

					// Store away gears blob internally
					id = plupload.guid();
					html5files[id] = file;

					// Expose id, name and size
					files.push(new plupload.File(id, file.fileName || file.name, file.fileSize || file.size)); // fileName / fileSize depricated
				}

				// Trigger FilesAdded event if we added any
				if (files.length) {
					uploader.trigger("FilesAdded", files);
				}
			}

			// No HTML5 upload support
			features = this.getFeatures();
			if (!features.html5) {
				callback({success : false});
				return;
			}

			uploader.bind("Init", function(up) {
				var inputContainer, browseButton, mimes = [], i, y, filters = up.settings.filters, ext, type, container = document.body, inputFile;

				// Create input container and insert it at an absolute position within the browse button
				inputContainer = document.createElement('div');
				inputContainer.id = up.id + '_html5_container';

				plupload.extend(inputContainer.style, {
					position : 'absolute',
					background : uploader.settings.shim_bgcolor || 'transparent',
					width : '100px',
					height : '100px',
					overflow : 'hidden',
					zIndex : 99999,
					opacity : uploader.settings.shim_bgcolor ? '' : 0 // Force transparent if bgcolor is undefined
				});
				inputContainer.className = 'plupload html5';

				if (uploader.settings.container) {
					container = document.getElementById(uploader.settings.container);
					if (plupload.getStyle(container, 'position') === 'static') {
						container.style.position = 'relative';
					}
				}

				container.appendChild(inputContainer);
				
				// Convert extensions to mime types list
				no_type_restriction:
				for (i = 0; i < filters.length; i++) {
					ext = filters[i].extensions.split(/,/);

					for (y = 0; y < ext.length; y++) {
						
						// If there's an asterisk in the list, then accept attribute is not required
						if (ext[y] === '*') {
							mimes = [];
							break no_type_restriction;
						}
						
						type = plupload.mimeTypes[ext[y]];

						if (type && plupload.inArray(type, mimes) === -1) {
							mimes.push(type);
						}
					}
				}


				// Insert the input inside the input container
				inputContainer.innerHTML = '<input id="' + uploader.id + '_html5" ' + ' style="font-size:999px"' +
											' type="file" accept="' + mimes.join(',') + '" ' +
											(uploader.settings.multi_selection && uploader.features.multi_selection ? 'multiple="multiple"' : '') + ' />';

				inputContainer.scrollTop = 100;
				inputFile = document.getElementById(uploader.id + '_html5');
				
				if (up.features.triggerDialog) {
					plupload.extend(inputFile.style, {
						position: 'absolute',
						width: '100%',
						height: '100%'
					});
				} else {
					// shows arrow cursor instead of the text one, bit more logical
					plupload.extend(inputFile.style, {
						cssFloat: 'right', 
						styleFloat: 'right'
					});
				}
				
				inputFile.onchange = function() {
					// Add the selected files from file input
					addSelectedFiles(this.files);
					
					// Clearing the value enables the user to select the same file again if they want to
					this.value = '';
				};
				
				/* Since we have to place input[type=file] on top of the browse_button for some browsers (FF, Opera),
				browse_button loses interactivity, here we try to neutralize this issue highlighting browse_button
				with a special classes
				TODO: needs to be revised as things will change */
				browseButton = document.getElementById(up.settings.browse_button);
				if (browseButton) {				
					var hoverClass = up.settings.browse_button_hover,
						activeClass = up.settings.browse_button_active,
						topElement = up.features.triggerDialog ? browseButton : inputContainer;
					
					if (hoverClass) {
						plupload.addEvent(topElement, 'mouseover', function() {
							plupload.addClass(browseButton, hoverClass);	
						}, up.id);
						plupload.addEvent(topElement, 'mouseout', function() {
							plupload.removeClass(browseButton, hoverClass);	
						}, up.id);
					}
					
					if (activeClass) {
						plupload.addEvent(topElement, 'mousedown', function() {
							plupload.addClass(browseButton, activeClass);	
						}, up.id);
						plupload.addEvent(document.body, 'mouseup', function() {
							plupload.removeClass(browseButton, activeClass);	
						}, up.id);
					}

					// Route click event to the input[type=file] element for supporting browsers
					if (up.features.triggerDialog) {
						plupload.addEvent(browseButton, 'click', function(e) {
							var input = document.getElementById(up.id + '_html5');
							if (input && !input.disabled) { // for some reason FF (up to 8.0.1 so far) lets to click disabled input[type=file]
								input.click();
							}
							e.preventDefault();
						}, up.id); 
					}
				}
			});

			// Add drop handler
			uploader.bind("PostInit", function() {
				var dropElm = document.getElementById(uploader.settings.drop_element);

				if (dropElm) {
					// Lets fake drag/drop on Safari by moving a input type file in front of the mouse pointer when we drag into the drop zone
					// TODO: Remove this logic once Safari has official drag/drop support
					if (fakeSafariDragDrop) {
						plupload.addEvent(dropElm, 'dragenter', function(e) {
							var dropInputElm, dropPos, dropSize;

							// Get or create drop zone
							dropInputElm = document.getElementById(uploader.id + "_drop");
							if (!dropInputElm) {
								dropInputElm = document.createElement("input");
								dropInputElm.setAttribute('type', "file");
								dropInputElm.setAttribute('id', uploader.id + "_drop");
								dropInputElm.setAttribute('multiple', 'multiple');

								plupload.addEvent(dropInputElm, 'change', function() {
									// Add the selected files from file input
									addSelectedFiles(this.files);
																		
									// Remove input element
									plupload.removeEvent(dropInputElm, 'change', uploader.id);
									dropInputElm.parentNode.removeChild(dropInputElm);									
								}, uploader.id);
								
								dropElm.appendChild(dropInputElm);
							}

							dropPos = plupload.getPos(dropElm, document.getElementById(uploader.settings.container));
							dropSize = plupload.getSize(dropElm);
							
							if (plupload.getStyle(dropElm, 'position') === 'static') {
								plupload.extend(dropElm.style, {
									position : 'relative'
								});
							}
              
							plupload.extend(dropInputElm.style, {
								position : 'absolute',
								display : 'block',
								top : 0,
								left : 0,
								width : dropSize.w + 'px',
								height : dropSize.h + 'px',
								opacity : 0
							});							
						}, uploader.id);

						return;
					}

					// Block browser default drag over
					plupload.addEvent(dropElm, 'dragover', function(e) {
						e.preventDefault();
					}, uploader.id);

					// Attach drop handler and grab files
					plupload.addEvent(dropElm, 'drop', function(e) {
						var dataTransfer = e.dataTransfer;

						// Add dropped files
						if (dataTransfer && dataTransfer.files) {
							addSelectedFiles(dataTransfer.files);
						}

						e.preventDefault();
					}, uploader.id);
				}
			});

			uploader.bind("Refresh", function(up) {
				var browseButton, browsePos, browseSize, inputContainer, zIndex;
					
				browseButton = document.getElementById(uploader.settings.browse_button);
				if (browseButton) {
					browsePos = plupload.getPos(browseButton, document.getElementById(up.settings.container));
					browseSize = plupload.getSize(browseButton);
					inputContainer = document.getElementById(uploader.id + '_html5_container');
	
					plupload.extend(inputContainer.style, {
						top : browsePos.y + 'px',
						left : browsePos.x + 'px',
						width : browseSize.w + 'px',
						height : browseSize.h + 'px'
					});
					
					// for WebKit place input element underneath the browse button and route onclick event 
					// TODO: revise when browser support for this feature will change
					if (uploader.features.triggerDialog) {
						if (plupload.getStyle(browseButton, 'position') === 'static') {
							plupload.extend(browseButton.style, {
								position : 'relative'
							});
						}
						
						zIndex = parseInt(plupload.getStyle(browseButton, 'z-index'), 10);
						if (isNaN(zIndex)) {
							zIndex = 0;
						}						
							
						plupload.extend(browseButton.style, {
							zIndex : zIndex
						});						
											
						plupload.extend(inputContainer.style, {
							zIndex : zIndex - 1
						});
					}				
				}
			});
			
			uploader.bind("DisableBrowse", function(up, disabled) {
				var input = document.getElementById(up.id + '_html5');
				if (input) {
					input.disabled = disabled;	
				}
			});
			
			uploader.bind("CancelUpload", function() {
				if (xhr.abort) {
					xhr.abort();	
				}
			});

			uploader.bind("UploadFile", function(up, file) {
				var settings = up.settings, nativeFile, resize;
					
				function w3cBlobSlice(blob, start, end) {
					var blobSlice;
					
					if (File.prototype.slice) {
						try {
							blob.slice();	// depricated version will throw WRONG_ARGUMENTS_ERR exception
							return blob.slice(start, end);
						} catch (e) {
							// depricated slice method
							return blob.slice(start, end - start); 
						}
					// slice method got prefixed: https://bugzilla.mozilla.org/show_bug.cgi?id=649672	
					} else if (blobSlice = File.prototype.webkitSlice || File.prototype.mozSlice) {
						return blobSlice.call(blob, start, end);	
					} else {
						return null; // or throw some exception	
					}
				}	

				function sendBinaryBlob(blob) {
					var chunk = 0, loaded = 0,
						fr = ("FileReader" in window) ? new FileReader : null;
						

					function uploadNextChunk() {
						var chunkBlob, br, chunks, args, chunkSize, curChunkSize, mimeType, url = up.settings.url;													

						
						function prepareAndSend(bin) {
							var multipartDeltaSize = 0,
								boundary = '----pluploadboundary' + plupload.guid(), formData, dashdash = '--', crlf = '\r\n', multipartBlob = '';
								
							xhr = new XMLHttpRequest;
															
							// Do we have upload progress support
							if (xhr.upload) {
								xhr.upload.onprogress = function(e) {
									file.loaded = Math.min(file.size, loaded + e.loaded - multipartDeltaSize); // Loaded can be larger than file size due to multipart encoding
									up.trigger('UploadProgress', file);
								};
							}
	
							xhr.onreadystatechange = function() {
								var httpStatus, chunkArgs;
																	
								if (xhr.readyState == 4 && up.state !== plupload.STOPPED) {
									// Getting the HTTP status might fail on some Gecko versions
									try {
										httpStatus = xhr.status;
									} catch (ex) {
										httpStatus = 0;
									}
	
									// Is error status
									if (httpStatus >= 400) {
										up.trigger('Error', {
											code : plupload.HTTP_ERROR,
											message : plupload.translate('HTTP Error.'),
											file : file,
											status : httpStatus
										});
									} else {
										// Handle chunk response
										if (chunks) {
											chunkArgs = {
												chunk : chunk,
												chunks : chunks,
												response : xhr.responseText,
												status : httpStatus
											};
	
											up.trigger('ChunkUploaded', file, chunkArgs);
											loaded += curChunkSize;
	
											// Stop upload
											if (chunkArgs.cancelled) {
												file.status = plupload.FAILED;
												return;
											}
	
											file.loaded = Math.min(file.size, (chunk + 1) * chunkSize);
										} else {
											file.loaded = file.size;
										}
	
										up.trigger('UploadProgress', file);
										
										bin = chunkBlob = formData = multipartBlob = null; // Free memory
										
										// Check if file is uploaded
										if (!chunks || ++chunk >= chunks) {
											file.status = plupload.DONE;
																						
											up.trigger('FileUploaded', file, {
												response : xhr.responseText,
												status : httpStatus
											});										
										} else {										
											// Still chunks left
											uploadNextChunk();
										}
									}																	
								}
							};
							
	
							// Build multipart request
							if (up.settings.multipart && features.multipart) {
								
								args.name = file.target_name || file.name;
								
								xhr.open("post", url, true);
								
								// Set custom headers
								plupload.each(up.settings.headers, function(value, name) {
									xhr.setRequestHeader(name, value);
								});
								
								
								// if has FormData support like Chrome 6+, Safari 5+, Firefox 4, use it
								if (typeof(bin) !== 'string' && !!window.FormData) {
									formData = new FormData();
	
									// Add multipart params
									plupload.each(plupload.extend(args, up.settings.multipart_params), function(value, name) {
										formData.append(name, value);
									});
	
									// Add file and send it
									formData.append(up.settings.file_data_name, bin);								
									xhr.send(formData);
	
									return;
								}  // if no FormData we can still try to send it directly as last resort (see below)
								
								
								if (typeof(bin) === 'string') {
									// Trying to send the whole thing as binary...
		
									// multipart request
									xhr.setRequestHeader('Content-Type', 'multipart/form-data; boundary=' + boundary);
		
									// append multipart parameters
									plupload.each(plupload.extend(args, up.settings.multipart_params), function(value, name) {
										multipartBlob += dashdash + boundary + crlf +
											'Content-Disposition: form-data; name="' + name + '"' + crlf + crlf;
		
										multipartBlob += unescape(encodeURIComponent(value)) + crlf;
									});
		
									mimeType = plupload.mimeTypes[file.name.replace(/^.+\.([^.]+)/, '$1').toLowerCase()] || 'application/octet-stream';
		
									// Build RFC2388 blob
									multipartBlob += dashdash + boundary + crlf +
										'Content-Disposition: form-data; name="' + up.settings.file_data_name + '"; filename="' + unescape(encodeURIComponent(file.name)) + '"' + crlf +
										'Content-Type: ' + mimeType + crlf + crlf +
										bin + crlf +
										dashdash + boundary + dashdash + crlf;
		
									multipartDeltaSize = multipartBlob.length - bin.length;
									bin = multipartBlob;
								
							
									if (xhr.sendAsBinary) { // Gecko
										xhr.sendAsBinary(bin);
									} else if (features.canSendBinary) { // WebKit with typed arrays support
										var ui8a = new Uint8Array(bin.length);
										for (var i = 0; i < bin.length; i++) {
											ui8a[i] = (bin.charCodeAt(i) & 0xff);
										}
										xhr.send(ui8a.buffer);
									}
									return; // will return from here only if shouldn't send binary
								} 							
							}
							
							// if no multipart, or last resort, send as binary stream
							url = plupload.buildUrl(up.settings.url, plupload.extend(args, up.settings.multipart_params));
							
							xhr.open("post", url, true);
							
							xhr.setRequestHeader('Content-Type', 'application/octet-stream'); // Binary stream header
								
							// Set custom headers
							plupload.each(up.settings.headers, function(value, name) {
								xhr.setRequestHeader(name, value);
							});
												
							xhr.send(bin); 
						} // prepareAndSend


						// File upload finished
						if (file.status == plupload.DONE || file.status == plupload.FAILED || up.state == plupload.STOPPED) {
							return;
						}

						// Standard arguments
						args = {name : file.target_name || file.name};

						// Only add chunking args if needed
						if (settings.chunk_size && file.size > settings.chunk_size && (features.chunks || typeof(blob) == 'string')) { // blob will be of type string if it was loaded in memory 
							chunkSize = settings.chunk_size;
							chunks = Math.ceil(file.size / chunkSize);
							curChunkSize = Math.min(chunkSize, file.size - (chunk * chunkSize));

							// Blob is string so we need to fake chunking, this is not
							// ideal since the whole file is loaded into memory
							if (typeof(blob) == 'string') {
								chunkBlob = blob.substring(chunk * chunkSize, chunk * chunkSize + curChunkSize);
							} else {
								// Slice the chunk
								chunkBlob = w3cBlobSlice(blob, chunk * chunkSize, chunk * chunkSize + curChunkSize);
							}

							// Setup query string arguments
							args.chunk = chunk;
							args.chunks = chunks;
						} else {
							curChunkSize = file.size;
							chunkBlob = blob;
						}
						
						// workaround Gecko 2,5,6 FormData+Blob bug: https://bugzilla.mozilla.org/show_bug.cgi?id=649150
						if (up.settings.multipart && features.multipart && typeof(chunkBlob) !== 'string' && fr && features.cantSendBlobInFormData && features.chunks && up.settings.chunk_size) { // Gecko 2,5,6
							fr.onload = function() {
								prepareAndSend(fr.result);
							}
							fr.readAsBinaryString(chunkBlob);
						} else {
							prepareAndSend(chunkBlob);
						}
							
					}

					// Start uploading chunks
					uploadNextChunk();
				}

				nativeFile = html5files[file.id];
								
				// Resize image if it's a supported format and resize is enabled
				if (features.jpgresize && up.settings.resize && /\.(png|jpg|jpeg)$/i.test(file.name)) {
					scaleImage.call(up, file, up.settings.resize, /\.png$/i.test(file.name) ? 'image/png' : 'image/jpeg', function(res) {
						// If it was scaled send the scaled image if it failed then
						// send the raw image and let the server do the scaling
						if (res.success) {
							file.size = res.data.length;
							sendBinaryBlob(res.data);
						} else if (features.chunks) {
							sendBinaryBlob(nativeFile); 
						} else {
							readFileAsBinary(nativeFile, sendBinaryBlob); // for browsers not supporting File.slice (e.g. FF3.6)
						}
					});
				// if there's no way to slice file without preloading it in memory, preload it
				} else if (!features.chunks && features.jpgresize) { 
					readFileAsBinary(nativeFile, sendBinaryBlob); 
				} else {
					sendBinaryBlob(nativeFile); 
				}
			});
			
			
			uploader.bind('Destroy', function(up) {
				var name, element, container = document.body,
					elements = {
						inputContainer: up.id + '_html5_container',
						inputFile: up.id + '_html5',
						browseButton: up.settings.browse_button,
						dropElm: up.settings.drop_element
					};

				// Unbind event handlers
				for (name in elements) {
					element = document.getElementById(elements[name]);
					if (element) {
						plupload.removeAllEvents(element, up.id);
					}
				}
				plupload.removeAllEvents(document.body, up.id);
				
				if (up.settings.container) {
					container = document.getElementById(up.settings.container);
				}
				
				// Remove mark-up
				container.removeChild(document.getElementById(elements.inputContainer));
			});

			callback({success : true});
		}
	});
	
	function BinaryReader() {
		var II = false, bin;

		// Private functions
		function read(idx, size) {
			var mv = II ? 0 : -8 * (size - 1), sum = 0, i;

			for (i = 0; i < size; i++) {
				sum |= (bin.charCodeAt(idx + i) << Math.abs(mv + i*8));
			}

			return sum;
		}

		function putstr(segment, idx, length) {
			var length = arguments.length === 3 ? length : bin.length - idx - 1;
			
			bin = bin.substr(0, idx) + segment + bin.substr(length + idx);
		}

		function write(idx, num, size) {
			var str = '', mv = II ? 0 : -8 * (size - 1), i;

			for (i = 0; i < size; i++) {
				str += String.fromCharCode((num >> Math.abs(mv + i*8)) & 255);
			}

			putstr(str, idx, size);
		}

		// Public functions
		return {
			II: function(order) {
				if (order === undef) {
					return II;
				} else {
					II = order;
				}
			},

			init: function(binData) {
				II = false;
				bin = binData;
			},

			SEGMENT: function(idx, length, segment) {				
				switch (arguments.length) {
					case 1: 
						return bin.substr(idx, bin.length - idx - 1);
					case 2: 
						return bin.substr(idx, length);
					case 3: 
						putstr(segment, idx, length);
						break;
					default: return bin;	
				}
			},

			BYTE: function(idx) {
				return read(idx, 1);
			},

			SHORT: function(idx) {
				return read(idx, 2);
			},

			LONG: function(idx, num) {
				if (num === undef) {
					return read(idx, 4);
				} else {
					write(idx, num, 4);
				}
			},

			SLONG: function(idx) { // 2's complement notation
				var num = read(idx, 4);

				return (num > 2147483647 ? num - 4294967296 : num);
			},

			STRING: function(idx, size) {
				var str = '';

				for (size += idx; idx < size; idx++) {
					str += String.fromCharCode(read(idx, 1));
				}

				return str;
			}
		};
	}
	
	function JPEG_Headers(data) {
		
		var markers = {
				0xFFE1: {
					app: 'EXIF',
					name: 'APP1',
					signature: "Exif\0" 
				},
				0xFFE2: {
					app: 'ICC',
					name: 'APP2',
					signature: "ICC_PROFILE\0" 
				},
				0xFFED: {
					app: 'IPTC',
					name: 'APP13',
					signature: "Photoshop 3.0\0" 
				}
			},
			headers = [], read, idx, marker = undef, length = 0, limit;
			
		
		read = new BinaryReader();
		read.init(data);
				
		// Check if data is jpeg
		if (read.SHORT(0) !== 0xFFD8) {
			return;
		}
		
		idx = 2;
		limit = Math.min(1048576, data.length);	
			
		while (idx <= limit) {
			marker = read.SHORT(idx);
			
			// omit RST (restart) markers
			if (marker >= 0xFFD0 && marker <= 0xFFD7) {
				idx += 2;
				continue;
			}
			
			// no headers allowed after SOS marker
			if (marker === 0xFFDA || marker === 0xFFD9) {
				break;	
			}	
			
			length = read.SHORT(idx + 2) + 2;	
			
			if (markers[marker] && 
				read.STRING(idx + 4, markers[marker].signature.length) === markers[marker].signature) {
				headers.push({ 
					hex: marker,
					app: markers[marker].app.toUpperCase(),
					name: markers[marker].name.toUpperCase(),
					start: idx,
					length: length,
					segment: read.SEGMENT(idx, length)
				});
			}
			idx += length;			
		}
					
		read.init(null); // free memory
						
		return {
			
			headers: headers,
			
			restore: function(data) {
				read.init(data);
				
				// Check if data is jpeg
				var jpegHeaders = new JPEG_Headers(data);
				
				if (!jpegHeaders['headers']) {
					return false;
				}	
				
				// Delete any existing headers that need to be replaced
				for (var i = jpegHeaders['headers'].length; i > 0; i--) {
					var hdr = jpegHeaders['headers'][i - 1];
					read.SEGMENT(hdr.start, hdr.length, '')
				}
				jpegHeaders.purge();
				
				idx = read.SHORT(2) == 0xFFE0 ? 4 + read.SHORT(4) : 2;
								
				for (var i = 0, max = headers.length; i < max; i++) {
					read.SEGMENT(idx, 0, headers[i].segment);						
					idx += headers[i].length;
				}
				
				return read.SEGMENT();
			},
			
			get: function(app) {
				var array = [];
								
				for (var i = 0, max = headers.length; i < max; i++) {
					if (headers[i].app === app.toUpperCase()) {
						array.push(headers[i].segment);
					}
				}
				return array;
			},
			
			set: function(app, segment) {
				var array = [];
				
				if (typeof(segment) === 'string') {
					array.push(segment);	
				} else {
					array = segment;	
				}
				
				for (var i = ii = 0, max = headers.length; i < max; i++) {
					if (headers[i].app === app.toUpperCase()) {
						headers[i].segment = array[ii];
						headers[i].length = array[ii].length;
						ii++;
					}
					if (ii >= array.length) break;
				}
			},
			
			purge: function() {
				headers = [];
				read.init(null);
			}
		};		
	}
	
	
	function ExifParser() {
		// Private ExifParser fields
		var data, tags, offsets = {}, tagDescs;

		data = new BinaryReader();

		tags = {
			tiff : {
				/*
				The image orientation viewed in terms of rows and columns.
	
				1 - The 0th row is at the visual top of the image, and the 0th column is the visual left-hand side.
				2 - The 0th row is at the visual top of the image, and the 0th column is the visual left-hand side.
				3 - The 0th row is at the visual top of the image, and the 0th column is the visual right-hand side.
				4 - The 0th row is at the visual bottom of the image, and the 0th column is the visual right-hand side.
				5 - The 0th row is at the visual bottom of the image, and the 0th column is the visual left-hand side.
				6 - The 0th row is the visual left-hand side of the image, and the 0th column is the visual top.
				7 - The 0th row is the visual right-hand side of the image, and the 0th column is the visual top.
				8 - The 0th row is the visual right-hand side of the image, and the 0th column is the visual bottom.
				9 - The 0th row is the visual left-hand side of the image, and the 0th column is the visual bottom.
				*/
				0x0112: 'Orientation',
				0x8769: 'ExifIFDPointer',
				0x8825:	'GPSInfoIFDPointer'
			},
			exif : {
				0x9000: 'ExifVersion',
				0xA001: 'ColorSpace',
				0xA002: 'PixelXDimension',
				0xA003: 'PixelYDimension',
				0x9003: 'DateTimeOriginal',
				0x829A: 'ExposureTime',
				0x829D: 'FNumber',
				0x8827: 'ISOSpeedRatings',
				0x9201: 'ShutterSpeedValue',
				0x9202: 'ApertureValue'	,
				0x9207: 'MeteringMode',
				0x9208: 'LightSource',
				0x9209: 'Flash',
				0xA402: 'ExposureMode',
				0xA403: 'WhiteBalance',
				0xA406: 'SceneCaptureType',
				0xA404: 'DigitalZoomRatio',
				0xA408: 'Contrast',
				0xA409: 'Saturation',
				0xA40A: 'Sharpness'
			},
			gps : {
				0x0000: 'GPSVersionID',
				0x0001: 'GPSLatitudeRef',
				0x0002: 'GPSLatitude',
				0x0003: 'GPSLongitudeRef',
				0x0004: 'GPSLongitude'
			}
		};

		tagDescs = {
			'ColorSpace': {
				1: 'sRGB',
				0: 'Uncalibrated'
			},

			'MeteringMode': {
				0: 'Unknown',
				1: 'Average',
				2: 'CenterWeightedAverage',
				3: 'Spot',
				4: 'MultiSpot',
				5: 'Pattern',
				6: 'Partial',
				255: 'Other'
			},

			'LightSource': {
				1: 'Daylight',
				2: 'Fliorescent',
				3: 'Tungsten',
				4: 'Flash',
				9: 'Fine weather',
				10: 'Cloudy weather',
				11: 'Shade',
				12: 'Daylight fluorescent (D 5700 - 7100K)',
				13: 'Day white fluorescent (N 4600 -5400K)',
				14: 'Cool white fluorescent (W 3900 - 4500K)',
				15: 'White fluorescent (WW 3200 - 3700K)',
				17: 'Standard light A',
				18: 'Standard light B',
				19: 'Standard light C',
				20: 'D55',
				21: 'D65',
				22: 'D75',
				23: 'D50',
				24: 'ISO studio tungsten',
				255: 'Other'
			},

			'Flash': {
				0x0000: 'Flash did not fire.',
				0x0001: 'Flash fired.',
				0x0005: 'Strobe return light not detected.',
				0x0007: 'Strobe return light detected.',
				0x0009: 'Flash fired, compulsory flash mode',
				0x000D: 'Flash fired, compulsory flash mode, return light not detected',
				0x000F: 'Flash fired, compulsory flash mode, return light detected',
				0x0010: 'Flash did not fire, compulsory flash mode',
				0x0018: 'Flash did not fire, auto mode',
				0x0019: 'Flash fired, auto mode',
				0x001D: 'Flash fired, auto mode, return light not detected',
				0x001F: 'Flash fired, auto mode, return light detected',
				0x0020: 'No flash function',
				0x0041: 'Flash fired, red-eye reduction mode',
				0x0045: 'Flash fired, red-eye reduction mode, return light not detected',
				0x0047: 'Flash fired, red-eye reduction mode, return light detected',
				0x0049: 'Flash fired, compulsory flash mode, red-eye reduction mode',
				0x004D: 'Flash fired, compulsory flash mode, red-eye reduction mode, return light not detected',
				0x004F: 'Flash fired, compulsory flash mode, red-eye reduction mode, return light detected',
				0x0059: 'Flash fired, auto mode, red-eye reduction mode',
				0x005D: 'Flash fired, auto mode, return light not detected, red-eye reduction mode',
				0x005F: 'Flash fired, auto mode, return light detected, red-eye reduction mode'
			},

			'ExposureMode': {
				0: 'Auto exposure',
				1: 'Manual exposure',
				2: 'Auto bracket'
			},

			'WhiteBalance': {
				0: 'Auto white balance',
				1: 'Manual white balance'
			},

			'SceneCaptureType': {
				0: 'Standard',
				1: 'Landscape',
				2: 'Portrait',
				3: 'Night scene'
			},

			'Contrast': {
				0: 'Normal',
				1: 'Soft',
				2: 'Hard'
			},

			'Saturation': {
				0: 'Normal',
				1: 'Low saturation',
				2: 'High saturation'
			},

			'Sharpness': {
				0: 'Normal',
				1: 'Soft',
				2: 'Hard'
			},

			// GPS related
			'GPSLatitudeRef': {
				N: 'North latitude',
				S: 'South latitude'
			},

			'GPSLongitudeRef': {
				E: 'East longitude',
				W: 'West longitude'
			}
		};

		function extractTags(IFD_offset, tags2extract) {
			var length = data.SHORT(IFD_offset), i, ii,
				tag, type, count, tagOffset, offset, value, values = [], hash = {};

			for (i = 0; i < length; i++) {
				// Set binary reader pointer to beginning of the next tag
				offset = tagOffset = IFD_offset + 12 * i + 2;

				tag = tags2extract[data.SHORT(offset)];

				if (tag === undef) {
					continue; // Not the tag we requested
				}

				type = data.SHORT(offset+=2);
				count = data.LONG(offset+=2);

				offset += 4;
				values = [];

				switch (type) {
					case 1: // BYTE
					case 7: // UNDEFINED
						if (count > 4) {
							offset = data.LONG(offset) + offsets.tiffHeader;
						}

						for (ii = 0; ii < count; ii++) {
							values[ii] = data.BYTE(offset + ii);
						}

						break;

					case 2: // STRING
						if (count > 4) {
							offset = data.LONG(offset) + offsets.tiffHeader;
						}

						hash[tag] = data.STRING(offset, count - 1);

						continue;

					case 3: // SHORT
						if (count > 2) {
							offset = data.LONG(offset) + offsets.tiffHeader;
						}

						for (ii = 0; ii < count; ii++) {
							values[ii] = data.SHORT(offset + ii*2);
						}

						break;

					case 4: // LONG
						if (count > 1) {
							offset = data.LONG(offset) + offsets.tiffHeader;
						}

						for (ii = 0; ii < count; ii++) {
							values[ii] = data.LONG(offset + ii*4);
						}

						break;

					case 5: // RATIONAL
						offset = data.LONG(offset) + offsets.tiffHeader;

						for (ii = 0; ii < count; ii++) {
							values[ii] = data.LONG(offset + ii*4) / data.LONG(offset + ii*4 + 4);
						}

						break;

					case 9: // SLONG
						offset = data.LONG(offset) + offsets.tiffHeader;

						for (ii = 0; ii < count; ii++) {
							values[ii] = data.SLONG(offset + ii*4);
						}

						break;

					case 10: // SRATIONAL
						offset = data.LONG(offset) + offsets.tiffHeader;

						for (ii = 0; ii < count; ii++) {
							values[ii] = data.SLONG(offset + ii*4) / data.SLONG(offset + ii*4 + 4);
						}

						break;

					default:
						continue;
				}

				value = (count == 1 ? values[0] : values);

				if (tagDescs.hasOwnProperty(tag) && typeof value != 'object') {
					hash[tag] = tagDescs[tag][value];
				} else {
					hash[tag] = value;
				}
			}

			return hash;
		}

		function getIFDOffsets() {
			var Tiff = undef, idx = offsets.tiffHeader;

			// Set read order of multi-byte data
			data.II(data.SHORT(idx) == 0x4949);

			// Check if always present bytes are indeed present
			if (data.SHORT(idx+=2) !== 0x002A) {
				return false;
			}
		
			offsets['IFD0'] = offsets.tiffHeader + data.LONG(idx += 2);
			Tiff = extractTags(offsets['IFD0'], tags.tiff);

			offsets['exifIFD'] = ('ExifIFDPointer' in Tiff ? offsets.tiffHeader + Tiff.ExifIFDPointer : undef);
			offsets['gpsIFD'] = ('GPSInfoIFDPointer' in Tiff ? offsets.tiffHeader + Tiff.GPSInfoIFDPointer : undef);

			return true;
		}
		
		// At the moment only setting of simple (LONG) values, that do not require offset recalculation, is supported
		function setTag(ifd, tag, value) {
			var offset, length, tagOffset, valueOffset = 0;
			
			// If tag name passed translate into hex key
			if (typeof(tag) === 'string') {
				var tmpTags = tags[ifd.toLowerCase()];
				for (hex in tmpTags) {
					if (tmpTags[hex] === tag) {
						tag = hex;
						break;	
					}
				}
			}
			offset = offsets[ifd.toLowerCase() + 'IFD'];
			length = data.SHORT(offset);
						
			for (i = 0; i < length; i++) {
				tagOffset = offset + 12 * i + 2;

				if (data.SHORT(tagOffset) == tag) {
					valueOffset = tagOffset + 8;
					break;
				}
			}
			
			if (!valueOffset) return false;

			
			data.LONG(valueOffset, value);
			return true;
		}
		

		// Public functions
		return {
			init: function(segment) {
				// Reset internal data
				offsets = {
					tiffHeader: 10
				};
				
				if (segment === undef || !segment.length) {
					return false;
				}

				data.init(segment);

				// Check if that's APP1 and that it has EXIF
				if (data.SHORT(0) === 0xFFE1 && data.STRING(4, 5).toUpperCase() === "EXIF\0") {
					return getIFDOffsets();
				}
				return false;
			},
			
			EXIF: function() {
				var Exif;
				
				// Populate EXIF hash
				Exif = extractTags(offsets.exifIFD, tags.exif);

				// Fix formatting of some tags
				if (Exif.ExifVersion && plupload.typeOf(Exif.ExifVersion) === 'array') {
					for (var i = 0, exifVersion = ''; i < Exif.ExifVersion.length; i++) {
						exifVersion += String.fromCharCode(Exif.ExifVersion[i]);	
					}
					Exif.ExifVersion = exifVersion;
				}

				return Exif;
			},

			GPS: function() {
				var GPS;
				
				GPS = extractTags(offsets.gpsIFD, tags.gps);
				
				// iOS devices (and probably some others) do not put in GPSVersionID tag (why?..)
				if (GPS.GPSVersionID) { 
					GPS.GPSVersionID = GPS.GPSVersionID.join('.');
				}

				return GPS;
			},
			
			setExif: function(tag, value) {
				// Right now only setting of width/height is possible
				if (tag !== 'PixelXDimension' && tag !== 'PixelYDimension') return false;
				
				return setTag('exif', tag, value);
			},


			getBinary: function() {
				return data.SEGMENT();
			}
		};
	};
})(window, document, plupload);

/**
 * jquery.plupload.queue.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */

// JSLint defined globals
/*global plupload:false, jQuery:false, alert:false */

(function($) {
	var uploaders = {};

	function _(str) {
		return plupload.translate(str) || str;
	}

	function renderUI(id, target) {
		// Remove all existing non plupload items
		target.contents().each(function(i, node) {
			node = $(node);

			if (!node.is('.plupload')) {
				node.remove();
			}
		});

		target.prepend(
			'<div class="plupload_wrapper plupload_scroll">' +
				'<div id="' + id + '_container" class="plupload_container">' +
					'<div class="plupload">' +
						'<div class="plupload_header">' +
							'<div class="plupload_header_content">' +
								'<div class="plupload_header_title">' + _('Select photos') + '</div>' +
								'<div class="plupload_header_text">' + _('Add photos to the upload queue and click the start button.') + '</div>' +
							'</div>' +
						'</div>' +

						'<div class="plupload_content">' +
							'<div class="plupload_filelist_footer">' +
                '<div class="plupload_file_name">' +
                  '<div class="plupload_buttons">' +
                    //'<a href="#" class="plupload_button plupload_add hidden-phone">' + _('Add photos') + '</a>' +
                    (window.innerWidth > 480 ? '<a href="" class="btn btn-theme-tertiary" id="uploader_browse"><i class="icon-paperclip"></i> Select photos</a>&nbsp;&nbsp;<button type="submit" class="btn btn-brand upload-button addSpinner">Start uploading</button>' : '') +
                    '<a href="#" class="plupload_button plupload_start">' + _('Start upload') + '</a>' +
                  '</div>' +
                  '<span class="plupload_upload_status"></span>' +
                '</div>' +
								'<div class="plupload_file_action"></div>' +
								'<div class="plupload_file_status"><span class="plupload_total_status">0%</span></div>' +
								'<div class="plupload_file_size"><span class="plupload_total_file_size">0 b</span></div>' +
								'<div class="plupload_progress">' +
									'<div class="plupload_progress_container">' +
										'<div class="plupload_progress_bar"></div>' +
									'</div>' +
								'</div>' +
								'<div class="plupload_clearer">&nbsp;</div>' +
							'</div>' +

							'<div class="plupload_filelist_header">' +
								'<div class="plupload_file_name">' + _('Filename') + '</div>' +
								'<div class="plupload_file_action">&nbsp;</div>' +
								'<div class="plupload_file_status"><span>' + _('Status') + '</span></div>' +
								'<div class="plupload_file_size">' + _('Size') + '</div>' +
								'<div class="plupload_clearer">&nbsp;</div>' +
							'</div>' +

							'<ul id="' + id + '_filelist" class="plupload_filelist"></ul>' +

						'</div>' +
					'</div>' +
				'</div>' +
				'<input type="hidden" id="' + id + '_count" name="' + id + '_count" value="0" />' +
			'</div>'
		);
	}

	$.fn.pluploadQueue = function(settings) {
		if (settings) {
			this.each(function() {
				var uploader, target, id;

				target = $(this);
				id = target.attr('id');

				if (!id) {
					id = plupload.guid();
					target.attr('id', id);
				}

				uploader = new plupload.Uploader($.extend({
					dragdrop : true,
					container : id
				}, settings));

				uploaders[id] = uploader;

				function handleStatus(file) {
					var actionClass;

					if (file.status == plupload.DONE) {
						actionClass = 'plupload_done';
					}

					if (file.status == plupload.FAILED) {
						actionClass = 'plupload_failed';
					}

					if (file.status == plupload.QUEUED) {
						actionClass = 'plupload_delete';
					}

					if (file.status == plupload.UPLOADING) {
						actionClass = 'plupload_uploading';
					}

					var icon = $('#' + file.id).attr('class', actionClass).find('a').css('display', 'block');
					if (file.hint) {
						icon.attr('title', file.hint);	
					}
				}

				function updateTotalProgress() {
					$('span.plupload_total_status', target).html(uploader.total.percent + '%');
					$('div.plupload_progress_bar', target).css('width', uploader.total.percent + '%');
					$('span.plupload_upload_status', target).text(
						_('Uploaded %d/%d files').replace(/%d\/%d/, uploader.total.uploaded+'/'+uploader.files.length)
					);
				}

				function updateList() {
					var fileList = $('ul.plupload_filelist', target).html(''), inputCount = 0, inputHTML;

					$.each(uploader.files, function(i, file) {
						inputHTML = '';

						if (file.status == plupload.DONE) {
							if (file.target_name) {
								inputHTML += '<input type="hidden" name="' + id + '_' + inputCount + '_tmpname" value="' + plupload.xmlEncode(file.target_name) + '" />';
							}

							inputHTML += '<input type="hidden" name="' + id + '_' + inputCount + '_name" value="' + plupload.xmlEncode(file.name) + '" />';
							inputHTML += '<input type="hidden" name="' + id + '_' + inputCount + '_status" value="' + (file.status == plupload.DONE ? 'done' : 'failed') + '" />';
	
							inputCount++;

							$('#' + id + '_count').val(inputCount);
						}

						fileList.append(
							'<li id="' + file.id + '">' +
								'<div class="plupload_file_name"><span>' + file.name + '</span></div>' +
								'<div class="plupload_file_action"><a href="#"></a></div>' +
								'<div class="plupload_file_status">' + file.percent + '%</div>' +
								'<div class="plupload_file_size">' + plupload.formatSize(file.size) + '</div>' +
								'<div class="plupload_clearer">&nbsp;</div>' +
								inputHTML +
							'</li>'
						);

						handleStatus(file);

						$('#' + file.id + '.plupload_delete a').click(function(e) {
							$('#' + file.id).remove();
							uploader.removeFile(file);

							e.preventDefault();
						});
					});

					$('span.plupload_total_file_size', target).html(plupload.formatSize(uploader.total.size));

					if (uploader.total.queued === 0) {
						$('span.plupload_add_text', target).text(_('Add photos.'));
					} else {
						$('span.plupload_add_text', target).text(uploader.total.queued + ' photos queued.');
					}

					$('a.plupload_start', target).toggleClass('plupload_disabled', uploader.files.length == (uploader.total.uploaded + uploader.total.failed));

					// Scroll to end of file list
					fileList[0].scrollTop = fileList[0].scrollHeight;

					updateTotalProgress();

					// Re-add drag message if there is no files or droptext is configured to be permanent
					if (settings.keep_droptext || (!uploader.files.length && uploader.features.dragdrop && uploader.settings.dragdrop)) {
						$('#' + id + '_filelist').append('<li class="plupload_droptext">' + _("Drag photos here.") + '</li>');
					}
				}

				uploader.bind("UploadFile", function(up, file) {
					$('#' + file.id).addClass('plupload_current_file');
				});

				uploader.bind('Init', function(up, res) {
					renderUI(id, target);

					// Enable rename support
					if (!settings.unique_names && settings.rename) {
						$('#' + id + '_filelist div.plupload_file_name span', target).live('click', function(e) {
							var targetSpan = $(e.target), file, parts, name, ext = "";

							// Get file name and split out name and extension
							file = up.getFile(targetSpan.parents('li')[0].id);
							name = file.name;
							parts = /^(.+)(\.[^.]+)$/.exec(name);
							if (parts) {
								name = parts[1];
								ext = parts[2];
							}

							// Display input element
							targetSpan.hide().after('<input type="text" />');
							targetSpan.next().val(name).focus().blur(function() {
								targetSpan.show().next().remove();
							}).keydown(function(e) {
								var targetInput = $(this);

								if (e.keyCode == 13) {
									e.preventDefault();

									// Rename file and glue extension back on
									file.name = targetInput.val() + ext;
									targetSpan.text(file.name);
									targetInput.blur();
								}
							});
						});
					}

					$('a.plupload_add', target).attr('id', id + '_browse');

					up.settings.browse_button = id + '_browse';

					// Enable drag/drop
					if (up.features.dragdrop && up.settings.dragdrop) {
						up.settings.drop_element = id + '_filelist';
						$('#' + id + '_filelist').append('<li class="plupload_droptext">' + _("Drag photos here.") + '</li>');
					}

					$('#' + id + '_container').attr('title', 'Using runtime: ' + res.runtime);

					$('a.plupload_start', target).click(function(e) {
						if (!$(this).hasClass('plupload_disabled')) {
							uploader.start();
						}

						e.preventDefault();
					});

					$('a.plupload_stop', target).click(function(e) {
						e.preventDefault();
						uploader.stop();
					});

					$('a.plupload_start', target).addClass('plupload_disabled');
				});

				uploader.init();

				uploader.bind("Error", function(up, err) {
					var file = err.file, message;

					if (file) {
						message = err.message;

						if (err.details) {
							message += " (" + err.details + ")";
						}

						if (err.code == plupload.FILE_SIZE_ERROR) {
							alert(_("Error: File too large: ") + file.name);
						}

						if (err.code == plupload.FILE_EXTENSION_ERROR) {
							alert(_("Error: Invalid file extension: ") + file.name);
						}
						
						file.hint = message;
						$('#' + file.id).attr('class', 'plupload_failed').find('a').css('display', 'block').attr('title', message);
					}
				});

				uploader.bind('StateChanged', function() {
					if (uploader.state === plupload.STARTED) {
						$('li.plupload_delete a,div.plupload_buttons', target).hide();
						$('span.plupload_upload_status,div.plupload_progress,a.plupload_stop', target).css('display', 'block');
						$('span.plupload_upload_status', target).text('Uploaded ' + uploader.total.uploaded + '/' + uploader.files.length + ' files');

						if (settings.multiple_queues) {
							$('span.plupload_total_status,span.plupload_total_file_size', target).show();
						}
					} else {
						updateList();
						$('a.plupload_stop,div.plupload_progress', target).hide();
						$('a.plupload_delete', target).css('display', 'block');
					}
				});

				uploader.bind('QueueChanged', updateList);

				uploader.bind('FileUploaded', function(up, file) {
					handleStatus(file);
				});

				uploader.bind("UploadProgress", function(up, file) {
					// Set file specific progress
					$('#' + file.id + ' div.plupload_file_status', target).html(file.percent + '%');

					handleStatus(file);
					updateTotalProgress();

					if (settings.multiple_queues && uploader.total.uploaded + uploader.total.failed == uploader.files.length) {
						$(".plupload_buttons,.plupload_upload_status", target).css("display", "inline");
						$(".plupload_start", target).addClass("plupload_disabled");
						$('span.plupload_total_status,span.plupload_total_file_size', target).hide();
					}
				});

				// Call setup function
				if (settings.setup) {
					settings.setup(uploader);
				}
			});

			return this;
		} else {
			// Get uploader instance for specified element
			return uploaders[$(this[0]).attr('id')];
		}
	};
})(jQuery);

/**
* Upload utility for OpenPhoto.
* Supports drag/drop with plupload
*/
OPU = (function() {
  var sortByFilename = function(a, b) {
    var aName = a.name;
    var bName = b.name;
    return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
  };
  var photosUploaded = {success: [], failure: [], duplicate: [], ids: []};
  return {
      init: function() {
        if(typeof plupload === "undefined")
          return;

        var uploaderEl = $("#uploader");
        if(uploaderEl.length === 0)
          return;
     
        if(typeof(uploaderEl.pluploadQueue) == 'undefined') {
          $("#uploader .insufficient").show();
          return;
        }

        uploaderEl.pluploadQueue({
            // General settings
            runtimes : 'html5',
            url : '/photo/upload.json', // omit 409 since it's somewhat idempotent
            max_file_size : '32mb',
            file_data_name : 'photo',
            //chunk_size : '1mb',
            unique_names : true,
            keep_droptext : true,
     
            // Specify what files to browse for
            filters : [
                {title : "Photos", extensions : "jpg,jpeg,gif,png"}
            ],
     
            // Flash settings
            flash_swf_url : 'plupload.flash.swf',
            multipart_params:{
              crumb: $("form.upload input.crumb").val()
            },
            preinit: {
              BeforeUpload: function() {
                var uploader = $("#uploader").pluploadQueue();
                $(".upload-progress .total").html(uploader.files.length);
                $(".upload-progress .completed").html(uploader.total.uploaded+1);
                $(".upload-progress").slideDown('fast');
              },
              Error: function(uploader, error) {
                opTheme.message.error('Uh oh, we encountered a problem. ('+error.message+')');
                return false;
              },
              FilesAdded: function(uploader, files) {
                var queue = uploader.files.concat(files);
                queue.sort(sortByFilename);
                uploader.files = queue;
              },
              FileUploaded: function(uploader, file, response) {
                var apiResponse = $.parseJSON(response.response),
                    code = apiResponse.code,
                    result = apiResponse.result;
                if(code === 201) {
                  OP.Log.info('Successfully uploaded ' + file.name + ' at ' + result.url);
                  photosUploaded.success.push(result);
                  photosUploaded.ids.push(result.id);
                } else if(code === 409) {
                  OP.Log.info('Detected a duplicate of ' + file.name);
                  photosUploaded.duplicate.push(result);
                  photosUploaded.ids.push(result.id);
                } else {
                  OP.Log.error('Unable to upload ' + file.name);
                  photosUploaded.failure.push(file.name);
                }
              },
              UploadComplete: function(uploader, files) {
                var i, file, failed = 0, total = 0;
                for(i in files) {
                  if(files.hasOwnProperty(i)) {
                    total++;
                    file = files[i];
                    if(file.status !== plupload.DONE)
                      failed++;
                  }
                }

                OP.Util.fire('upload:complete-success', photosUploaded);
              },
              UploadFile: function() {
                var uploader = $("#uploader").pluploadQueue(),
                    form = $('form.upload'),
                    license = $("select[name='license'] :selected", form).val(),
                    permission = $("input[name='permission']:checked", form).val(),
                    albums = $("select[name='albums']", form).val(),
                    tags = $("input[name='tags']", form).val(),
                    crumb = $("input[name='crumb']", form).val(),
                    // http://stackoverflow.com/a/6116631
                    // groups = $("input[name='groups[]']:checked", form).map(function () {return this.value;}).get().join(",");
                    groups = $("select[name='groups']", form).val();

                if(typeof(albums) === "undefined")
                  albums = '';

                if(typeof(groups) === "undefined")
                  groups = '';
                else if(groups !== null)
                  groups = groups.join(',');
                
                uploader.settings.multipart_params.license = license;
                uploader.settings.multipart_params.tags = tags;
                uploader.settings.multipart_params.permission = permission;
                uploader.settings.multipart_params.albums = albums;
                uploader.settings.multipart_params.groups = groups;
                uploader.settings.multipart_params.crumb = crumb;
              }
            }
        });
      }
    };
}());

;(function(){

/**
 * Require the given path.
 *
 * @param {String} path
 * @return {Object} exports
 * @api public
 */

function require(path, parent, orig) {
  var resolved = require.resolve(path);

  // lookup failed
  if (null == resolved) {
    orig = orig || path;
    parent = parent || 'root';
    var err = new Error('Failed to require "' + orig + '" from "' + parent + '"');
    err.path = orig;
    err.parent = parent;
    err.require = true;
    throw err;
  }

  var module = require.modules[resolved];

  // perform real require()
  // by invoking the module's
  // registered function
  if (!module.exports) {
    module.exports = {};
    module.client = module.component = true;
    module.call(this, module.exports, require.relative(resolved), module);
  }

  return module.exports;
}

/**
 * Registered modules.
 */

require.modules = {};

/**
 * Registered aliases.
 */

require.aliases = {};

/**
 * Resolve `path`.
 *
 * Lookup:
 *
 *   - PATH/index.js
 *   - PATH.js
 *   - PATH
 *
 * @param {String} path
 * @return {String} path or null
 * @api private
 */

require.resolve = function(path) {
  if (path.charAt(0) === '/') path = path.slice(1);
  var index = path + '/index.js';

  var paths = [
    path,
    path + '.js',
    path + '.json',
    path + '/index.js',
    path + '/index.json'
  ];

  for (var i = 0; i < paths.length; i++) {
    var path = paths[i];
    if (require.modules.hasOwnProperty(path)) return path;
  }

  if (require.aliases.hasOwnProperty(index)) {
    return require.aliases[index];
  }
};

/**
 * Normalize `path` relative to the current path.
 *
 * @param {String} curr
 * @param {String} path
 * @return {String}
 * @api private
 */

require.normalize = function(curr, path) {
  var segs = [];

  if ('.' != path.charAt(0)) return path;

  curr = curr.split('/');
  path = path.split('/');

  for (var i = 0; i < path.length; ++i) {
    if ('..' == path[i]) {
      curr.pop();
    } else if ('.' != path[i] && '' != path[i]) {
      segs.push(path[i]);
    }
  }

  return curr.concat(segs).join('/');
};

/**
 * Register module at `path` with callback `definition`.
 *
 * @param {String} path
 * @param {Function} definition
 * @api private
 */

require.register = function(path, definition) {
  require.modules[path] = definition;
};

/**
 * Alias a module definition.
 *
 * @param {String} from
 * @param {String} to
 * @api private
 */

require.alias = function(from, to) {
  if (!require.modules.hasOwnProperty(from)) {
    throw new Error('Failed to alias "' + from + '", it does not exist');
  }
  require.aliases[to] = from;
};

/**
 * Return a require function relative to the `parent` path.
 *
 * @param {String} parent
 * @return {Function}
 * @api private
 */

require.relative = function(parent) {
  var p = require.normalize(parent, '..');

  /**
   * lastIndexOf helper.
   */

  function lastIndexOf(arr, obj) {
    var i = arr.length;
    while (i--) {
      if (arr[i] === obj) return i;
    }
    return -1;
  }

  /**
   * The relative require() itself.
   */

  function localRequire(path) {
    var resolved = localRequire.resolve(path);
    return require(resolved, parent, path);
  }

  /**
   * Resolve relative to the parent.
   */

  localRequire.resolve = function(path) {
    var c = path.charAt(0);
    if ('/' == c) return path.slice(1);
    if ('.' == c) return require.normalize(p, path);

    // resolve deps by returning
    // the dep in the nearest "deps"
    // directory
    var segs = parent.split('/');
    var i = lastIndexOf(segs, 'deps') + 1;
    if (!i) i = 0;
    path = segs.slice(0, i + 1).join('/') + '/deps/' + path;
    return path;
  };

  /**
   * Check if module is defined at `path`.
   */

  localRequire.exists = function(path) {
    return require.modules.hasOwnProperty(localRequire.resolve(path));
  };

  return localRequire;
};
require.register("component-emitter/index.js", function(exports, require, module){

/**
 * Expose `Emitter`.
 */

module.exports = Emitter;

/**
 * Initialize a new `Emitter`.
 *
 * @api public
 */

function Emitter(obj) {
  if (obj) return mixin(obj);
};

/**
 * Mixin the emitter properties.
 *
 * @param {Object} obj
 * @return {Object}
 * @api private
 */

function mixin(obj) {
  for (var key in Emitter.prototype) {
    obj[key] = Emitter.prototype[key];
  }
  return obj;
}

/**
 * Listen on the given `event` with `fn`.
 *
 * @param {String} event
 * @param {Function} fn
 * @return {Emitter}
 * @api public
 */

Emitter.prototype.on = function(event, fn){
  this._callbacks = this._callbacks || {};
  (this._callbacks[event] = this._callbacks[event] || [])
    .push(fn);
  return this;
};

/**
 * Adds an `event` listener that will be invoked a single
 * time then automatically removed.
 *
 * @param {String} event
 * @param {Function} fn
 * @return {Emitter}
 * @api public
 */

Emitter.prototype.once = function(event, fn){
  var self = this;
  this._callbacks = this._callbacks || {};

  function on() {
    self.off(event, on);
    fn.apply(this, arguments);
  }

  fn._off = on;
  this.on(event, on);
  return this;
};

/**
 * Remove the given callback for `event` or all
 * registered callbacks.
 *
 * @param {String} event
 * @param {Function} fn
 * @return {Emitter}
 * @api public
 */

Emitter.prototype.off =
Emitter.prototype.removeListener =
Emitter.prototype.removeAllListeners = function(event, fn){
  this._callbacks = this._callbacks || {};
  var callbacks = this._callbacks[event];
  if (!callbacks) return this;

  // remove all handlers
  if (1 == arguments.length) {
    delete this._callbacks[event];
    return this;
  }

  // remove specific handler
  var i = callbacks.indexOf(fn._off || fn);
  if (~i) callbacks.splice(i, 1);
  return this;
};

/**
 * Emit `event` with the given args.
 *
 * @param {String} event
 * @param {Mixed} ...
 * @return {Emitter}
 */

Emitter.prototype.emit = function(event){
  this._callbacks = this._callbacks || {};
  var args = [].slice.call(arguments, 1)
    , callbacks = this._callbacks[event];

  if (callbacks) {
    callbacks = callbacks.slice(0);
    for (var i = 0, len = callbacks.length; i < len; ++i) {
      callbacks[i].apply(this, args);
    }
  }

  return this;
};

/**
 * Return array of callbacks for `event`.
 *
 * @param {String} event
 * @return {Array}
 * @api public
 */

Emitter.prototype.listeners = function(event){
  this._callbacks = this._callbacks || {};
  return this._callbacks[event] || [];
};

/**
 * Check if this emitter has `event` handlers.
 *
 * @param {String} event
 * @return {Boolean}
 * @api public
 */

Emitter.prototype.hasListeners = function(event){
  return !! this.listeners(event).length;
};

});
require.register("dropzone/index.js", function(exports, require, module){


/**
 * Exposing dropzone
 */
module.exports = require("./lib/dropzone.js");

});
require.register("dropzone/lib/dropzone.js", function(exports, require, module){
/*
#
# More info at [www.dropzonejs.com](http://www.dropzonejs.com)
# 
# Copyright (c) 2012, Matias Meno  
# 
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
# 
# The above copyright notice and this permission notice shall be included in
# all copies or substantial portions of the Software.
# 
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
# THE SOFTWARE.
#
*/


(function() {
  var Dropzone, Em, camelize, contentLoaded, noop, without,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    __slice = [].slice,
    __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  Em = typeof Emitter !== "undefined" && Emitter !== null ? Emitter : require("emitter");

  noop = function() {};

  Dropzone = (function(_super) {
    __extends(Dropzone, _super);

    /*
    This is a list of all available events you can register on a dropzone object.
    
    You can register an event handler like this:
    
        dropzone.on("dragEnter", function() { });
    */


    Dropzone.prototype.events = ["drop", "dragstart", "dragend", "dragenter", "dragover", "dragleave", "selectedfiles", "addedfile", "removedfile", "thumbnail", "error", "processingfile", "uploadprogress", "totaluploadprogress", "sending", "success", "complete", "reset"];

    Dropzone.prototype.defaultOptions = {
      url: null,
      method: "post",
      parallelUploads: 2,
      maxFilesize: 256,
      paramName: "file",
      createImageThumbnails: true,
      maxThumbnailFilesize: 10,
      thumbnailWidth: 100,
      thumbnailHeight: 100,
      params: {},
      clickable: true,
      acceptedMimeTypes: null,
      acceptParameter: null,
      enqueueForUpload: true,
      previewsContainer: null,
      dictDefaultMessage: "Drop files here to upload",
      dictFallbackMessage: "Your browser does not support drag'n'drop file uploads.",
      dictFallbackText: "Please use the fallback form below to upload your files like in the olden days.",
      dictFileTooBig: "File is too big ({{filesize}}MB). Max filesize: {{maxFilesize}}MB.",
      dictInvalidFileType: "You can't upload files of this type.",
      dictResponseError: "Server responded with {{statusCode}} code.",
      accept: function(file, done) {
        return done();
      },
      init: function() {
        return noop;
      },
      forceFallback: false,
      fallback: function() {
        var child, messageElement, span, _i, _len, _ref;

        this.element.className = "" + this.element.className + " dz-browser-not-supported";
        _ref = this.element.getElementsByTagName("div");
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          child = _ref[_i];
          if (/(^| )message($| )/.test(child.className)) {
            messageElement = child;
            child.className = "dz-message";
            continue;
          }
        }
        if (!messageElement) {
          messageElement = Dropzone.createElement("<div class=\"dz-message\"><span></span></div>");
          this.element.appendChild(messageElement);
        }
        span = messageElement.getElementsByTagName("span")[0];
        if (span) {
          span.textContent = this.options.dictFallbackMessage;
        }
        return this.element.appendChild(this.getFallbackForm());
      },
      resize: function(file) {
        var info, srcRatio, trgRatio;

        info = {
          srcX: 0,
          srcY: 0,
          srcWidth: file.width,
          srcHeight: file.height
        };
        srcRatio = file.width / file.height;
        trgRatio = this.options.thumbnailWidth / this.options.thumbnailHeight;
        if (file.height < this.options.thumbnailHeight || file.width < this.options.thumbnailWidth) {
          info.trgHeight = info.srcHeight;
          info.trgWidth = info.srcWidth;
        } else {
          if (srcRatio > trgRatio) {
            info.srcHeight = file.height;
            info.srcWidth = info.srcHeight * trgRatio;
          } else {
            info.srcWidth = file.width;
            info.srcHeight = info.srcWidth / trgRatio;
          }
        }
        info.srcX = (file.width - info.srcWidth) / 2;
        info.srcY = (file.height - info.srcHeight) / 2;
        return info;
      },
      /*
      Those functions register themselves to the events on init and handle all
      the user interface specific stuff. Overwriting them won't break the upload
      but can break the way it's displayed.
      You can overwrite them if you don't like the default behavior. If you just
      want to add an additional event handler, register it on the dropzone object
      and don't overwrite those options.
      */

      drop: function(e) {
        return this.element.classList.remove("dz-drag-hover");
      },
      dragstart: noop,
      dragend: function(e) {
        return this.element.classList.remove("dz-drag-hover");
      },
      dragenter: function(e) {
        return this.element.classList.add("dz-drag-hover");
      },
      dragover: function(e) {
        return this.element.classList.add("dz-drag-hover");
      },
      dragleave: function(e) {
        return this.element.classList.remove("dz-drag-hover");
      },
      selectedfiles: function(files) {
        if (this.element === this.previewsContainer) {
          return this.element.classList.add("dz-started");
        }
      },
      reset: function() {
        return this.element.classList.remove("dz-started");
      },
      addedfile: function(file) {
        file.previewElement = Dropzone.createElement(this.options.previewTemplate);
        file.previewTemplate = file.previewElement;
        this.previewsContainer.appendChild(file.previewElement);
        file.previewElement.querySelector("[data-dz-name]").textContent = file.name;
        return file.previewElement.querySelector("[data-dz-size]").innerHTML = this.filesize(file.size);
      },
      removedfile: function(file) {
        return file.previewElement.parentNode.removeChild(file.previewElement);
      },
      thumbnail: function(file, dataUrl) {
        var thumbnailElement;

        file.previewElement.classList.remove("dz-file-preview");
        file.previewElement.classList.add("dz-image-preview");
        thumbnailElement = file.previewElement.querySelector("[data-dz-thumbnail]");
        thumbnailElement.alt = file.name;
        return thumbnailElement.src = dataUrl;
      },
      error: function(file, message) {
        file.previewElement.classList.add("dz-error");
        return file.previewElement.querySelector("[data-dz-errormessage]").textContent = message;
      },
      processingfile: function(file) {
        return file.previewElement.classList.add("dz-processing");
      },
      uploadprogress: function(file, progress, bytesSent) {
        return file.previewElement.querySelector("[data-dz-uploadprogress]").style.width = "" + progress + "%";
      },
      totaluploadprogress: noop,
      sending: noop,
      success: function(file) {
        return file.previewElement.classList.add("dz-success");
      },
      complete: noop,
      previewTemplate: "<div class=\"dz-preview dz-file-preview\">\n  <div class=\"dz-details\">\n    <div class=\"dz-filename\"><span data-dz-name></span></div>\n    <div class=\"dz-size\" data-dz-size></div>\n    <img data-dz-thumbnail />\n  </div>\n  <div class=\"dz-progress\"><span class=\"dz-upload\" data-dz-uploadprogress></span></div>\n  <div class=\"dz-success-mark\"><span>✔</span></div>\n  <div class=\"dz-error-mark\"><span>✘</span></div>\n  <div class=\"dz-error-message\"><span data-dz-errormessage></span></div>\n</div>"
    };

    function Dropzone(element, options) {
      var elementOptions, extend, fallback, _ref;

      this.element = element;
      this.version = Dropzone.version;
      this.defaultOptions.previewTemplate = this.defaultOptions.previewTemplate.replace(/\n*/g, "");
      if (typeof this.element === "string") {
        this.element = document.querySelector(this.element);
      }
      if (!(this.element && (this.element.nodeType != null))) {
        throw new Error("Invalid dropzone element.");
      }
      if (this.element.dropzone) {
        throw new Error("Dropzone already attached.");
      }
      Dropzone.instances.push(this);
      element.dropzone = this;
      elementOptions = (_ref = Dropzone.optionsForElement(this.element)) != null ? _ref : {};
      extend = function() {
        var key, object, objects, target, val, _i, _len;

        target = arguments[0], objects = 2 <= arguments.length ? __slice.call(arguments, 1) : [];
        for (_i = 0, _len = objects.length; _i < _len; _i++) {
          object = objects[_i];
          for (key in object) {
            val = object[key];
            target[key] = val;
          }
        }
        return target;
      };
      this.options = extend({}, this.defaultOptions, elementOptions, options != null ? options : {});
      if (this.options.url == null) {
        this.options.url = this.element.action;
      }
      if (!this.options.url) {
        throw new Error("No URL provided.");
      }
      if (this.options.acceptParameter && this.options.acceptedMimeTypes) {
        throw new Error("You can't provide both 'acceptParameter' and 'acceptedMimeTypes'. 'acceptParameter' is deprecated.");
      }
      this.options.method = this.options.method.toUpperCase();
      if (this.options.forceFallback || !Dropzone.isBrowserSupported()) {
        return this.options.fallback.call(this);
      }
      if ((fallback = this.getExistingFallback()) && fallback.parentNode) {
        fallback.parentNode.removeChild(fallback);
      }
      if (this.options.previewsContainer) {
        this.previewsContainer = Dropzone.getElement(this.options.previewsContainer, "previewsContainer");
      } else {
        this.previewsContainer = this.element;
      }
      if (this.options.clickable) {
        if (this.options.clickable === true) {
          this.clickableElements = [this.element];
        } else {
          this.clickableElements = Dropzone.getElements(this.options.clickable, "clickable");
        }
      } else {
        this.clickableElements = [];
      }
      this.init();
    }

    Dropzone.prototype.init = function() {
      var eventName, noPropagation, setupHiddenFileInput, _i, _len, _ref, _ref1,
        _this = this;

      if (this.element.tagName === "form") {
        this.element.setAttribute("enctype", "multipart/form-data");
      }
      if (this.element.classList.contains("dropzone") && !this.element.querySelector("[data-dz-message]")) {
        this.element.appendChild(Dropzone.createElement("<div class=\"dz-default dz-message\" data-dz-message><span>" + this.options.dictDefaultMessage + "</span></div>"));
      }
      if (this.clickableElements.length) {
        setupHiddenFileInput = function() {
          if (_this.hiddenFileInput) {
            document.body.removeChild(_this.hiddenFileInput);
          }
          _this.hiddenFileInput = document.createElement("input");
          _this.hiddenFileInput.setAttribute("type", "file");
          _this.hiddenFileInput.setAttribute("multiple", "multiple");
          if (_this.options.acceptedMimeTypes != null) {
            _this.hiddenFileInput.setAttribute("accept", _this.options.acceptedMimeTypes);
          }
          if (_this.options.acceptParameter != null) {
            _this.hiddenFileInput.setAttribute("accept", _this.options.acceptParameter);
          }
          _this.hiddenFileInput.style.visibility = "hidden";
          _this.hiddenFileInput.style.position = "absolute";
          _this.hiddenFileInput.style.top = "0";
          _this.hiddenFileInput.style.left = "0";
          _this.hiddenFileInput.style.height = "0";
          _this.hiddenFileInput.style.width = "0";
          document.body.appendChild(_this.hiddenFileInput);
          return _this.hiddenFileInput.addEventListener("change", function() {
            var files;

            files = _this.hiddenFileInput.files;
            if (files.length) {
              _this.emit("selectedfiles", files);
              _this.handleFiles(files);
            }
            return setupHiddenFileInput();
          });
        };
        setupHiddenFileInput();
      }
      this.files = [];
      this.acceptedFiles = [];
      this.filesQueue = [];
      this.filesProcessing = [];
      this.URL = (_ref = window.URL) != null ? _ref : window.webkitURL;
      _ref1 = this.events;
      for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
        eventName = _ref1[_i];
        this.on(eventName, this.options[eventName]);
      }
      this.on("uploadprogress", function(file) {
        var totalBytes, totalBytesSent, totalUploadProgress, _j, _len1, _ref2;

        totalBytesSent = 0;
        totalBytes = 0;
        _ref2 = _this.acceptedFiles;
        for (_j = 0, _len1 = _ref2.length; _j < _len1; _j++) {
          file = _ref2[_j];
          totalBytesSent += file.upload.bytesSent;
          totalBytes += file.upload.total;
        }
        totalUploadProgress = 100 * totalBytesSent / totalBytes;
        return _this.emit("totaluploadprogress", totalUploadProgress, totalBytes, totalBytesSent);
      });
      noPropagation = function(e) {
        e.stopPropagation();
        if (e.preventDefault) {
          return e.preventDefault();
        } else {
          return e.returnValue = false;
        }
      };
      this.listeners = [
        {
          element: this.element,
          events: {
            "dragstart": function(e) {
              return _this.emit("dragstart", e);
            },
            "dragenter": function(e) {
              noPropagation(e);
              return _this.emit("dragenter", e);
            },
            "dragover": function(e) {
              noPropagation(e);
              return _this.emit("dragover", e);
            },
            "dragleave": function(e) {
              return _this.emit("dragleave", e);
            },
            "drop": function(e) {
              noPropagation(e);
              _this.drop(e);
              return _this.emit("drop", e);
            },
            "dragend": function(e) {
              return _this.emit("dragend", e);
            }
          }
        }
      ];
      this.clickableElements.forEach(function(clickableElement) {
        return _this.listeners.push({
          element: clickableElement,
          events: {
            "click": function(evt) {
              if ((clickableElement !== _this.element) || (evt.target === _this.element || Dropzone.elementInside(evt.target, _this.element.querySelector(".dz-message")))) {
                return _this.hiddenFileInput.click();
              }
            }
          }
        });
      });
      this.enable();
      return this.options.init.call(this);
    };

    Dropzone.prototype.getFallbackForm = function() {
      var existingFallback, fields, fieldsString, form;

      if (existingFallback = this.getExistingFallback()) {
        return existingFallback;
      }
      fieldsString = "<div class=\"dz-fallback\">";
      if (this.options.dictFallbackText) {
        fieldsString += "<p>" + this.options.dictFallbackText + "</p>";
      }
      fieldsString += "<input type=\"file\" name=\"" + this.options.paramName + "[]\" multiple=\"multiple\" /><button type=\"submit\">Upload!</button></div>";
      fields = Dropzone.createElement(fieldsString);
      if (this.element.tagName !== "FORM") {
        form = Dropzone.createElement("<form action=\"" + this.options.url + "\" enctype=\"multipart/form-data\" method=\"" + this.options.method + "\"></form>");
        form.appendChild(fields);
      } else {
        this.element.setAttribute("enctype", "multipart/form-data");
        this.element.setAttribute("method", this.options.method);
      }
      return form != null ? form : fields;
    };

    Dropzone.prototype.getExistingFallback = function() {
      var fallback, getFallback, tagName, _i, _len, _ref;

      getFallback = function(elements) {
        var el, _i, _len;

        for (_i = 0, _len = elements.length; _i < _len; _i++) {
          el = elements[_i];
          if (/(^| )fallback($| )/.test(el.className)) {
            return el;
          }
        }
      };
      _ref = ["div", "form"];
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        tagName = _ref[_i];
        if (fallback = getFallback(this.element.getElementsByTagName(tagName))) {
          return fallback;
        }
      }
    };

    Dropzone.prototype.setupEventListeners = function() {
      var elementListeners, event, listener, _i, _len, _ref, _results;

      _ref = this.listeners;
      _results = [];
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        elementListeners = _ref[_i];
        _results.push((function() {
          var _ref1, _results1;

          _ref1 = elementListeners.events;
          _results1 = [];
          for (event in _ref1) {
            listener = _ref1[event];
            _results1.push(elementListeners.element.addEventListener(event, listener, false));
          }
          return _results1;
        })());
      }
      return _results;
    };

    Dropzone.prototype.removeEventListeners = function() {
      var elementListeners, event, listener, _i, _len, _ref, _results;

      _ref = this.listeners;
      _results = [];
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        elementListeners = _ref[_i];
        _results.push((function() {
          var _ref1, _results1;

          _ref1 = elementListeners.events;
          _results1 = [];
          for (event in _ref1) {
            listener = _ref1[event];
            _results1.push(elementListeners.element.removeEventListener(event, listener, false));
          }
          return _results1;
        })());
      }
      return _results;
    };

    Dropzone.prototype.disable = function() {
      this.clickableElements.forEach(function(element) {
        return element.classList.remove("dz-clickable");
      });
      this.removeEventListeners();
      this.filesProcessing = [];
      return this.filesQueue = [];
    };

    Dropzone.prototype.enable = function() {
      this.clickableElements.forEach(function(element) {
        return element.classList.add("dz-clickable");
      });
      return this.setupEventListeners();
    };

    Dropzone.prototype.filesize = function(size) {
      var string;

      if (size >= 100000000000) {
        size = size / 100000000000;
        string = "TB";
      } else if (size >= 100000000) {
        size = size / 100000000;
        string = "GB";
      } else if (size >= 100000) {
        size = size / 100000;
        string = "MB";
      } else if (size >= 100) {
        size = size / 100;
        string = "KB";
      } else {
        size = size * 10;
        string = "b";
      }
      return "<strong>" + (Math.round(size) / 10) + "</strong> " + string;
    };

    Dropzone.prototype.drop = function(e) {
      var files;

      if (!e.dataTransfer) {
        return;
      }
      files = e.dataTransfer.files;
      this.emit("selectedfiles", files);
      if (files.length) {
        return this.handleFiles(files);
      }
    };

    Dropzone.prototype.handleFiles = function(files) {
      var file, _i, _len, _results;

      _results = [];
      for (_i = 0, _len = files.length; _i < _len; _i++) {
        file = files[_i];
        _results.push(this.addFile(file));
      }
      return _results;
    };

    Dropzone.prototype.accept = function(file, done) {
      if (file.size > this.options.maxFilesize * 1024 * 1024) {
        return done(this.options.dictFileTooBig.replace("{{filesize}}", Math.round(file.size / 1024 / 10.24) / 100).replace("{{maxFilesize}}", this.options.maxFilesize));
      } else if (!Dropzone.isValidMimeType(file.type, this.options.acceptedMimeTypes)) {
        return done(this.options.dictInvalidFileType);
      } else {
        return this.options.accept.call(this, file, done);
      }
    };

    Dropzone.prototype.addFile = function(file) {
      var _this = this;

      file.upload = {
        progress: 0,
        total: file.size,
        bytesSent: 0
      };
      this.files.push(file);
      this.emit("addedfile", file);
      if (this.options.createImageThumbnails && file.type.match(/image.*/) && file.size <= this.options.maxThumbnailFilesize * 1024 * 1024) {
        this.createThumbnail(file);
      }
      return this.accept(file, function(error) {
        if (error) {
          file.accepted = false;
          return _this.errorProcessing(file, error);
        } else {
          file.accepted = true;
          _this.acceptedFiles.push(file);
          if (_this.options.enqueueForUpload) {
            _this.filesQueue.push(file);
            return _this.processQueue();
          }
        }
      });
    };

    Dropzone.prototype.removeFile = function(file) {
      if (file.processing) {
        throw new Error("Can't remove file currently processing");
      }
      this.files = without(this.files, file);
      this.filesQueue = without(this.filesQueue, file);
      this.emit("removedfile", file);
      if (this.files.length === 0) {
        return this.emit("reset");
      }
    };

    Dropzone.prototype.removeAllFiles = function() {
      var file, _i, _len, _ref;

      _ref = this.files.slice();
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        file = _ref[_i];
        if (__indexOf.call(this.filesProcessing, file) < 0) {
          this.removeFile(file);
        }
      }
      return null;
    };

    Dropzone.prototype.createThumbnail = function(file) {
      var fileReader,
        _this = this;

      fileReader = new FileReader;
      fileReader.onload = function() {
        var img;

        img = new Image;
        img.onload = function() {
          var canvas, ctx, resizeInfo, thumbnail, _ref, _ref1, _ref2, _ref3, _ref4, _ref5;

          file.width = img.width;
          file.height = img.height;
          resizeInfo = _this.options.resize.call(_this, file);
          if ((_ref = resizeInfo.trgWidth) == null) {
            resizeInfo.trgWidth = _this.options.thumbnailWidth;
          }
          if ((_ref1 = resizeInfo.trgHeight) == null) {
            resizeInfo.trgHeight = _this.options.thumbnailHeight;
          }
          canvas = document.createElement("canvas");
          ctx = canvas.getContext("2d");
          canvas.width = resizeInfo.trgWidth;
          canvas.height = resizeInfo.trgHeight;
          ctx.drawImage(img, (_ref2 = resizeInfo.srcX) != null ? _ref2 : 0, (_ref3 = resizeInfo.srcY) != null ? _ref3 : 0, resizeInfo.srcWidth, resizeInfo.srcHeight, (_ref4 = resizeInfo.trgX) != null ? _ref4 : 0, (_ref5 = resizeInfo.trgY) != null ? _ref5 : 0, resizeInfo.trgWidth, resizeInfo.trgHeight);
          thumbnail = canvas.toDataURL("image/png");
          return _this.emit("thumbnail", file, thumbnail);
        };
        return img.src = fileReader.result;
      };
      return fileReader.readAsDataURL(file);
    };

    Dropzone.prototype.processQueue = function() {
      var i, parallelUploads, processingLength;

      parallelUploads = this.options.parallelUploads;
      processingLength = this.filesProcessing.length;
      i = processingLength;
      while (i < parallelUploads) {
        if (!this.filesQueue.length) {
          return;
        }
        this.processFile(this.filesQueue.shift());
        i++;
      }
    };

    Dropzone.prototype.processFile = function(file) {
      this.filesProcessing.push(file);
      file.processing = true;
      this.emit("processingfile", file);
      return this.uploadFile(file);
    };

    Dropzone.prototype.uploadFile = function(file) {
      var formData, handleError, input, inputName, inputType, key, progressObj, response, value, xhr, _i, _len, _ref, _ref1, _ref2,
        _this = this;

      xhr = new XMLHttpRequest();
      xhr.open(this.options.method, this.options.url, true);
      response = null;
      handleError = function() {
        return _this.errorProcessing(file, response || _this.options.dictResponseError.replace("{{statusCode}}", xhr.status), xhr);
      };
      xhr.onload = function(e) {
        var _ref;

        response = xhr.responseText;
        if (xhr.getResponseHeader("content-type") && ~xhr.getResponseHeader("content-type").indexOf("application/json")) {
          try {
            response = JSON.parse(response);
          } catch (_error) {
            e = _error;
            response = "Invalid JSON response from server.";
          }
        }
        if (!((200 <= (_ref = xhr.status) && _ref < 300))) {
          return handleError();
        } else {
          return _this.finished(file, response, e);
        }
      };
      xhr.onerror = function() {
        return handleError();
      };
      progressObj = (_ref = xhr.upload) != null ? _ref : xhr;
      progressObj.onprogress = function(e) {
        var progress;

        file.upload = {
          progress: progress,
          total: e.total,
          bytesSent: e.loaded
        };
        progress = 100 * e.loaded / e.total;
        return _this.emit("uploadprogress", file, progress, e.loaded);
      };
      xhr.setRequestHeader("Accept", "application/json");
      xhr.setRequestHeader("Cache-Control", "no-cache");
      xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
      xhr.setRequestHeader("X-File-Name", file.name);
      formData = new FormData();
      if (this.options.params) {
        _ref1 = this.options.params;
        for (key in _ref1) {
          value = _ref1[key];
          formData.append(key, value);
        }
      }
      if (this.element.tagName === "FORM") {
        _ref2 = this.element.querySelectorAll("input, textarea, select, button");
        for (_i = 0, _len = _ref2.length; _i < _len; _i++) {
          input = _ref2[_i];
          inputName = input.getAttribute("name");
          inputType = input.getAttribute("type");
          if (!inputType || (inputType.toLowerCase() !== "checkbox" && inputType.toLowerCase() !== "radio") || input.checked) {
            formData.append(inputName, input.value);
          }
        }
      }
      this.emit("sending", file, xhr, formData);
      formData.append(this.options.paramName, file);
      return xhr.send(formData);
    };

    Dropzone.prototype.finished = function(file, responseText, e) {
      this.filesProcessing = without(this.filesProcessing, file);
      file.processing = false;
      this.processQueue();
      this.emit("success", file, responseText, e);
      this.emit("finished", file, responseText, e);
      return this.emit("complete", file);
    };

    Dropzone.prototype.errorProcessing = function(file, message, xhr) {
      this.filesProcessing = without(this.filesProcessing, file);
      file.processing = false;
      this.processQueue();
      this.emit("error", file, message, xhr);
      return this.emit("complete", file);
    };

    return Dropzone;

  })(Em);

  Dropzone.version = "3.2.0";

  Dropzone.options = {};

  Dropzone.optionsForElement = function(element) {
    if (element.id) {
      return Dropzone.options[camelize(element.id)];
    } else {
      return void 0;
    }
  };

  Dropzone.instances = [];

  Dropzone.forElement = function(element) {
    var _ref;

    if (typeof element === "string") {
      element = document.querySelector(element);
    }
    return (_ref = element.dropzone) != null ? _ref : null;
  };

  Dropzone.autoDiscover = true;

  Dropzone.discover = function() {
    var checkElements, dropzone, dropzones, _i, _len, _results;

    if (!Dropzone.autoDiscover) {
      return;
    }
    if (document.querySelectorAll) {
      dropzones = document.querySelectorAll(".dropzone");
    } else {
      dropzones = [];
      checkElements = function(elements) {
        var el, _i, _len, _results;

        _results = [];
        for (_i = 0, _len = elements.length; _i < _len; _i++) {
          el = elements[_i];
          if (/(^| )dropzone($| )/.test(el.className)) {
            _results.push(dropzones.push(el));
          } else {
            _results.push(void 0);
          }
        }
        return _results;
      };
      checkElements(document.getElementsByTagName("div"));
      checkElements(document.getElementsByTagName("form"));
    }
    _results = [];
    for (_i = 0, _len = dropzones.length; _i < _len; _i++) {
      dropzone = dropzones[_i];
      if (Dropzone.optionsForElement(dropzone) !== false) {
        _results.push(new Dropzone(dropzone));
      } else {
        _results.push(void 0);
      }
    }
    return _results;
  };

  Dropzone.blacklistedBrowsers = [/opera.*Macintosh.*version\/12/i];

  Dropzone.isBrowserSupported = function() {
    var capableBrowser, regex, _i, _len, _ref;

    capableBrowser = true;
    if (window.File && window.FileReader && window.FileList && window.Blob && window.FormData && document.querySelector) {
      if (!("classList" in document.createElement("a"))) {
        capableBrowser = false;
      } else {
        _ref = Dropzone.blacklistedBrowsers;
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          regex = _ref[_i];
          if (regex.test(navigator.userAgent)) {
            capableBrowser = false;
            continue;
          }
        }
      }
    } else {
      capableBrowser = false;
    }
    return capableBrowser;
  };

  without = function(list, rejectedItem) {
    var item, _i, _len, _results;

    _results = [];
    for (_i = 0, _len = list.length; _i < _len; _i++) {
      item = list[_i];
      if (item !== rejectedItem) {
        _results.push(item);
      }
    }
    return _results;
  };

  camelize = function(str) {
    return str.replace(/[\-_](\w)/g, function(match) {
      return match[1].toUpperCase();
    });
  };

  Dropzone.createElement = function(string) {
    var div;

    div = document.createElement("div");
    div.innerHTML = string;
    return div.childNodes[0];
  };

  Dropzone.elementInside = function(element, container) {
    if (element === container) {
      return true;
    }
    while (element = element.parentNode) {
      if (element === container) {
        return true;
      }
    }
    return false;
  };

  Dropzone.getElement = function(el, name) {
    var element;

    if (typeof el === "string") {
      element = document.querySelector(el);
    } else if (el.nodeType != null) {
      element = el;
    }
    if (element == null) {
      throw new Error("Invalid `" + name + "` option provided. Please provide a CSS selector or a plain HTML element.");
    }
    return element;
  };

  Dropzone.getElements = function(els, name) {
    var e, el, elements, _i, _j, _len, _len1, _ref;

    if (els instanceof Array) {
      elements = [];
      try {
        for (_i = 0, _len = els.length; _i < _len; _i++) {
          el = els[_i];
          elements.push(this.getElement(el, name));
        }
      } catch (_error) {
        e = _error;
        elements = null;
      }
    } else if (typeof els === "string") {
      elements = [];
      _ref = document.querySelectorAll(els);
      for (_j = 0, _len1 = _ref.length; _j < _len1; _j++) {
        el = _ref[_j];
        elements.push(el);
      }
    } else if (els.nodeType != null) {
      elements = [els];
    }
    if (!((elements != null) && elements.length)) {
      throw new Error("Invalid `" + name + "` option provided. Please provide a CSS selector, a plain HTML element or a list of those.");
    }
    return elements;
  };

  Dropzone.isValidMimeType = function(mimeType, acceptedMimeTypes) {
    var baseMimeType, validMimeType, _i, _len;

    if (!acceptedMimeTypes) {
      return true;
    }
    acceptedMimeTypes = acceptedMimeTypes.split(",");
    baseMimeType = mimeType.replace(/\/.*$/, "");
    for (_i = 0, _len = acceptedMimeTypes.length; _i < _len; _i++) {
      validMimeType = acceptedMimeTypes[_i];
      validMimeType = validMimeType.trim();
      if (/\/\*$/.test(validMimeType)) {
        if (baseMimeType === validMimeType.replace(/\/.*$/, "")) {
          return true;
        }
      } else {
        if (mimeType === validMimeType) {
          return true;
        }
      }
    }
    return false;
  };

  if (typeof jQuery !== "undefined" && jQuery !== null) {
    jQuery.fn.dropzone = function(options) {
      return this.each(function() {
        return new Dropzone(this, options);
      });
    };
  }

  if (typeof module !== "undefined" && module !== null) {
    module.exports = Dropzone;
  } else {
    window.Dropzone = Dropzone;
  }

  /*
  # contentloaded.js
  #
  # Author: Diego Perini (diego.perini at gmail.com)
  # Summary: cross-browser wrapper for DOMContentLoaded
  # Updated: 20101020
  # License: MIT
  # Version: 1.2
  #
  # URL:
  # http://javascript.nwbox.com/ContentLoaded/
  # http://javascript.nwbox.com/ContentLoaded/MIT-LICENSE
  */


  contentLoaded = function(win, fn) {
    var add, doc, done, init, poll, pre, rem, root, top;

    done = false;
    top = true;
    doc = win.document;
    root = doc.documentElement;
    add = (doc.addEventListener ? "addEventListener" : "attachEvent");
    rem = (doc.addEventListener ? "removeEventListener" : "detachEvent");
    pre = (doc.addEventListener ? "" : "on");
    init = function(e) {
      if (e.type === "readystatechange" && doc.readyState !== "complete") {
        return;
      }
      (e.type === "load" ? win : doc)[rem](pre + e.type, init, false);
      if (!done && (done = true)) {
        return fn.call(win, e.type || e);
      }
    };
    poll = function() {
      var e;

      try {
        root.doScroll("left");
      } catch (_error) {
        e = _error;
        setTimeout(poll, 50);
        return;
      }
      return init("poll");
    };
    if (doc.readyState !== "complete") {
      if (doc.createEventObject && root.doScroll) {
        try {
          top = !win.frameElement;
        } catch (_error) {}
        if (top) {
          poll();
        }
      }
      doc[add](pre + "DOMContentLoaded", init, false);
      doc[add](pre + "readystatechange", init, false);
      return win[add](pre + "load", init, false);
    }
  };

  contentLoaded(window, Dropzone.discover);

}).call(this);

});
require.alias("component-emitter/index.js", "dropzone/deps/emitter/index.js");
require.alias("component-emitter/index.js", "emitter/index.js");

if (typeof exports == "object") {
  module.exports = require("dropzone");
} else if (typeof define == "function" && define.amd) {
  define(function(){ return require("dropzone"); });
} else {
  this["Dropzone"] = require("dropzone");
}})();

