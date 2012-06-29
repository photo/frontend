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

  function Tag() {
    var self = this,
        tags,
        lastModified,
        namespace = 'tags',
        initCb,
        getTime;

    getTime = function() {
      return Date.now() / 1000;
    };

    initCb = function(response) {
      var tagObjects = response.result,
          code = response.code;

      tags = [];
      if(code !== 200 || tagObjects === false)
        return;
      for(var i=0; i<tagObjects.length; i++) {
        if(tagObjects[i].id != '')
          tags.push(tagObjects[i].id);
      }

      lastModified = getTime();
      localStorage.setObject(namespace, tags);
      localStorage.setItem(namespace+'-lastModified', lastModified);
      OP.Util.fire('callback:tags-initialized');
      OP.Util.fire('callback:tags-updated');
    };
    
    this.init = function() {
      var force = arguments[0] || false;
      tags = localStorage.getObject(namespace) || null;
      lastModified = parseInt(localStorage.getItem(namespace+'-lastModified')) || 0;
      if(force || tags === null || tags.length === 0 || lastModified < (getTime()-1800))
        OP.Util.makeRequest('/tags/list.json', {}, initCb, 'json', 'get');
      else
        OP.Util.fire('callback:tags-initialized');
    };
    
    this.getTags = function() {
      return tags;
    };
  }

  function Batch() {
    var self = this;
    this._callbackAdd = function(response) {
      var r = response.result;
      self.collection.add(r.id, r);
      OP.Util.fire('callback:batch-add', r);
    };

    this.add = function(id) {
      var args = {};
      if(arguments.length > 1)
        args = arguments[1];
      log("[Util][Batch] adding " + id);
      OU.makeRequest('/photo/'+id+'/view.json', args, self._callbackAdd, 'json', 'get');
    };

    this.remove = function(id) {
      log("[Util][Batch] removing " + id);
      self.collection.remove(id);
    };

    this.clear = function() {
      log("[Util][Batch] clearing");
      this.collection.clear();
    };


    this.collection = (function() {
      var length,
          items,
          namespace = 'items';

      items = localStorage.getObject(namespace) || {};

      return {
        add: function(key, value) {
          items[key] = value;
          localStorage.setObject(namespace, items);
          length++;
        },
        clear: function() {
          items = {};
          localStorage.setObject(namespace, {});
          length = 0;
          OP.Util.fire('callback:batch-clear');
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
        getLength: function() {
          var size = 0, key;
          for (key in items) {
              if (items.hasOwnProperty(key)) size++;
          }
          return size;
        },
        remove: function(key) {
          delete items[key];
          localStorage.setObject(namespace, items);
          length--;
          OP.Util.fire('callback:batch-remove', key);
        }
      };
    })();
  }
  //store the util instance
  OP.Batch = new Batch();
  OP.Tag = new Tag();
}());
