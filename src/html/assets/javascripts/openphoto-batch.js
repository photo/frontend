/**
* Batch editing utility for OpenPhoto.
* Depends on openphoto-lib-{library}.js
*/
(function() {
  //OP and OP.Util are already defined at this point, so just modify directly
  var OU = OP.Util.constructor.prototype,
      lib = OP.Util.lib,
      log = function(msg) { if(typeof(console) !== 'undefined') {  console.log(msg); } };


  function Batch() {
    var that = this;
    this._callbackAdd = function(response) {
      var r = response.result;
      OP.Util.fire('openphoto-batch-add', r);
      that.collection.add(r.id, r);
    };

    this.add = function(id) {
      var args = {};
      if(arguments.length > 1)
        args = arguments[1];
      log("[Util][Batch] adding " + id);
      OU.makeRequest('/photo/'+id+'/view.json', args, this._callbackAdd, 'json', 'get');
    };

    this.collection = (function() {
      var length = 0,
          items,
          namespace = 'items';

      items = localStorage.getItem(namespace) || {};

      return {
        add: function(key, value) {
          items[key] = value;
          localStorage.setItem(namespace, items);
          length++;
        },
        getAll: function() {
          return items;
        },
        getIds: function() {
          var retval = [];
          for( i in items) {
            retval.push(i);
          }
          return retval;
        },
        remove: function(key) {
          delete items[key];
          localStorage.setItem(namespace, items);
          length--;
        }
      };
    })();
  }
  //store the util instance
  OP.Batch = new Batch();
}());
