/**
* Utility plugin file that is pulled in by the util file
* that normalizes the libraries that are used so that OpenPhoto
* can use the methods provided by that library.
*/
(function() {

    //OP and OP.Util are already defined at this point, so just modify directly

    var OU = OP.Util.constructor.prototype,
        lib = OP.Util.lib;


    /**
    * Sets the context / scope of a function
    * @param {Function} fn - the function
    * @param {object} scope - the scope
    * @return {Function} the context applied function
    * @method bind
    */
    OU.bind = function(fn, scope) {
        return lib.proxy(fn, scope);
    }

    /**
    * Adds an event listener to the element
    * @param {HTMLElement} element - the element to add the listener to
    * @param {string} etype - the event type to add the listener to
    * @param {Function} callback - the callback function
    * @param {object} scope - the context of the callback function
    * @return {void}
    * @method attachEvent
    */
    OU.attachEvent = function(element, etype, callback, scope) {
        var scope = scope || window;
        // TODO we should use .on() here but it isn't working for dynamically created elements (i.e. photo edit form)
        lib( element ).live(etype, lib.proxy(callback, scope) );
    }


    /**
    * removes the event listener from the element
    * @param {HTMLElement} element - the element to add the listener to
    * @param {string} type - the event type to add the listener to
    * @return {void}
    * @method detachEvent
    */
    OU.detachEvent = function(element, type) {
        lib( element ).die( type );
    }


    /**
    * Makes an ajax post request
    * @param {string} url to make request to
    * @param {string} post data
    * @param {Function} XHR callback function
    * @param {string} format (json/xml)
    * @return {void}
    * @method makeRequest
    */
    OU.makeRequest = function(url, data, callback, format, method) {

        //default to a post
        var oFormat = format || "json",
            oMethod = method || "post";
        
        if (oMethod === "post") {
            lib.post(url, data, callback, oFormat).error(function(response) { callback(response); });
        } else {
            lib.get(url, data, callback, oFormat).error(function(response) { callback(response); });
        }

    }


}());
