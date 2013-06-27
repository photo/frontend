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
      lib = OP.Util.lib;

  function Log() {
    var log = function(msg) { if(typeof(console) !== 'undefined') {  console.log(msg); } };

    this.info = log;
    this.warn = log;
    this.error = log;
  }

  function Album() {
    var self = this,
        albums,
        lastModified,
        namespace = 'albums',
        initCb,
        getTime;

    getTime = function() {
      return Date.now() / 1000;
    };

    initCb = function(response) {
      var albumObjects = response.result,
          code = response.code;

      albums = [];
      if(code !== 200 || albumObjects === false)
        return;
      for(var i=0; i<albumObjects.length; i++) {
        if(albumObjects[i].id != '')
          albums.push(albumObjects[i]);
      }

      lastModified = getTime();
      localStorage.setObject(namespace, albums);
      localStorage.setItem(namespace+'-lastModified', lastModified);
      OP.Util.fire('callback:albums-initialized');
      OP.Util.fire('callback:albums-updated');
    };
    
    this.init = function() {
      var force = arguments[0] || false;
      albums = localStorage.getObject(namespace) || null;
      lastModified = parseInt(localStorage.getItem(namespace+'-lastModified')) || 0;
      if(force || albums === null || albums.length === 0 || lastModified < (getTime()-1800))
        OP.Util.makeRequest('/albums/list.json', {pageSize: 0}, initCb, 'json', 'get');
      else
        OP.Util.fire('callback:albums-initialized');
    };
    
    this.getAlbums = function() {
      return albums;
    };
  }

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
          tags.push(tagObjects[i]);
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

    this.add = function(id/*, photo */) {
      var photo = arguments[1] || false;
      if(typeof photo === 'object') {
        OP.Log.info("[Util][Batch] adding from argument " + id);
        self._callbackAdd({result: photo});
      } else {
        OP.Log.info("[Util][Batch] adding " + id);
        OU.makeRequest('/photo/'+id+'/view.json', {}, self._callbackAdd, 'json', 'get');
      }
    };

    this.remove = function(id) {
      OP.Log.info("[Util][Batch] removing " + id);
      self.collection.remove(id);
    };

    this.clear = function() {
      OP.Log.info("[Util][Batch] clearing");
      this.collection.clear();
    };

    this.length = function() {
      return this.collection.getLength();
    };

    this.exists = function(id) {
      return this.collection.getKeyOfValue(id) !== false;
    };

    this.getKey = function(id) {
      return this.collection.getKeyOfValue(id);
    };

    this.ids = function() {
      return this.collection.getIds();
    };


    this.collection = (function() {
      var length,
          items,
          namespace = 'items';

      items = localStorage.getObject(namespace) || {};
      length = items.length;

      return {
        add: function(key, value) {
          items[key] = value;
          localStorage.setObject(namespace, items);
          length++;
        },
        clear: function() {
          for (key in items) {
            if (items.hasOwnProperty(key)) {
              OP.Util.fire('callback:batch-remove', items[key].id);
            }
          }
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
        getKeyOfValue: function(id) {
          for (key in items) {
            if (items.hasOwnProperty(key)) {
              if(items[key].id == id)
                return key;
            }
          }
          return false;
        },
        getLength: function() {
          var size = 0, key;
          for (key in items) {
            if (items.hasOwnProperty(key))
              size++;
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
  OP.Log = new Log();
  OP.Batch = new Batch();
  OP.Tag = new Tag();
  OP.Album = new Album();
}());
