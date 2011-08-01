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
        
    /**
    * Class that contains all utility functions for OpenPhoto
    * @class Util
    */
    OP.Util = {
    
    
        /* -------------------------------------------------
        *               Custom Events
        * ------------------------------------------------- */
        
        _customEvents: {},
        
        
        /**
        * Subscribe to a custom event - the callback will be executed when the custom event is fired 
        * @param {string} eventName - the name of the custom event to subscribe to
        * @param {Function} callback - the callback function that will be executed when the event is fired
        * @param {Object} scope - the scope of the callback function (what this will refer to)
        * @return {void}
        * @method on
        */
        on: function(eventName, callback, scope) {
        
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
        
        },
        
        /**
        * A little less terse name, but removes an event listener if it exists
        * @param {string} eventName - the name of the custom event to unsubscribe from
        * @param {Function} callback - the callback function that would have been executed on fire of the custom event
        * @return {void}
        * @method unsubscribe
        */
        unsubscribe: function(eventName, callback) {
        
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
            

        },
        
        /**
        * Fire a custom event - invoke all listeners passing whatever optional arguments
        * @param {string} eventName - name of the event to fire
        * @return {void}
        * @method fire
        */
        fire: function(eventName, arg){
        
            var callbacks = this._customEvents[eventName],
                arg = arg || {},
                i, j;
                
            if (!!callbacks) {
                for ( i=0, j=callbacks.length; i<j; i++ ) {
                    callbacks[i].fn.call(callbacks[i].scope, arg);
                }
            }
        
        }
    
    };


}());