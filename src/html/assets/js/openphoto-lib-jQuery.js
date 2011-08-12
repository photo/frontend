/**
* Utility plugin file that is pulled in by the util file
* that normalizes the libraries that are used so that OpenPhoto
* can use the methods provided by that library.
*/
(function() {

    //OP and OP.Util are already defined at this point, so just modify directly
   	
	var OU = OP.Util,
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
    * @param {string} type - the event type to add the listener to
    * @param {Function} callback - the callback function
    * @param {object} scope - the context of the callback function
    * @return {void}
    * @method attachEvent
    */
    OU.attachEvent = function(element, type, callback, scope) {
        
        var scope = scope || window;
        
        lib( element ).live( OU.bind(callback, scope) );
    
    }


}());
