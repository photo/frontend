/**
* Batch editing utility for OpenPhoto.
* Depends on openphoto-lib-{library}.js
*/
(function() {
  Storage.prototype.setObject = function(key, value) {
      this.setItem(key, JSON.stringify(value));
  }

  Storage.prototype.getObject = function(key) {
      var value = this.getItem(key);
      return value && JSON.parse(value);
  }
  //OP and OP.Util are already defined at this point, so just modify directly
  var OU = OP.Util.constructor.prototype,
      lib = OP.Util.lib,
      log = function(msg) { if(typeof(console) !== 'undefined') {  console.log(msg); } };


  function Batch() {
    var that = this;
    this._callbackAdd = function(response) {
      var r = response.result;
      that.collection.add(r.id, r);
      OP.Util.fire('callback:batch-add', r);
    };

    this.add = function(id) {
      var args = {};
      if(arguments.length > 1)
        args = arguments[1];
      log("[Util][Batch] adding " + id);
      OU.makeRequest('/photo/'+id+'/view.json', args, this._callbackAdd, 'json', 'get');
    };

    this.remove = function(id) {
      log("[Util][Batch] removing " + id);
      that.collection.remove(id);
      OP.Util.fire('callback:batch-remove', id);
    };


    this.collection = (function() {
      var length = 0,
          items,
          namespace = 'items';

      items = localStorage.getObject(namespace) || {};

      return {
        add: function(key, value) {
          items[key] = value;
          localStorage.setObject(namespace, items);
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
          localStorage.setObject(namespace, items);
          length--;
        }
      };
    })();
  }
  //store the util instance
  OP.Batch = new Batch();
}());
