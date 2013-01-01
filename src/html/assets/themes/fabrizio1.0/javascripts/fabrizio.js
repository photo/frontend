var TBX = (function() {
  var callbacks, crumb, log, markup, profiles, pushstate, tags, pathname, util;

  callbacks = {
    loginSuccess: function() {
      var redirect = $('input[name="r"]', this).val();
      window.location.href = redirect;
    },
    profilesSuccess: function(owner, viewer) {
      var ownerId = owner.id, viewerId = viewer.id;
      profiles.owner = owner;
      if(viewer !== undefined)
        profiles.viewer = viewer;

      // create model(s)
      op.data.store.Profiles.add(profiles.owner);
      // only if the viewer !== owner do we create two models
      if(viewer !== undefined && owner.isOwner === false)
        op.data.store.Profiles.add(profiles.viewer);

      $('.profile-name-meta').each(function(i, el) {
        (new op.data.view.ProfileName({model:op.data.store.Profiles.get(ownerId), el: el})).render();
      });
      $('.profile-photo-meta').each(function(i, el) {
        (new op.data.view.ProfilePhoto({model:op.data.store.Profiles.get(ownerId), el: el})).render();
      });
      (new op.data.view.ProfilePhoto({model:op.data.store.Profiles.get(viewerId), el: $('.profile-photo-header-meta')})).render();
    }
  };
  crumb = (function() {
    var value = null;
    return {
      get: function() {
        return value;
      },
      set: function(crumb) {
        value = crumb;
      }
    };
  })();
  profiles = {
    owner: { },
    viewer: { },
    load: function() {
      // TODO cache this somehow
      $.get('/user/profile.json', {includeViewer: '1'}, function(response) {
        if(response.code !== 200)
          return;

        var result = response.result, id = result.id, owner = result, viewer = result.viewer || null;
        if(owner.viewer !== undefined)
          delete owner.viewer;
        callbacks.profilesSuccess(owner, viewer);
      }, 'json');
    }
  };
  util = (function() {
    return {
      getDeviceWidth: function() {
        return $(window).width();
      },
      fetchAndCache: function(src) {
        $('<img />').attr('src', src).appendTo('body').css('display', 'none').on('load', function(ev) { $(ev.target).remove(); });
      },
      fetchAndCacheNextPrevious: function() {
        var nextPhoto = $('img.next-photo'), prevPhoto = $('img.previous-photo');
        if(prevPhoto.length > 0)
          OP.Util.fire('preload:photo', {id: prevPhoto.attr('data-id'), sizes:'870x550'});
        if(nextPhoto.length > 0)
          OP.Util.fire('preload:photo', {id: nextPhoto.attr('data-id'), sizes:'870x550'});
      },
      load: function(context) {
        // we define initData at runtime to avoid having to make an HTTP call on load
        // all subsequent calls run through the http API
        if(typeof(context.initData) === "undefined") {
          if(context.end || context.running)
            return;

          context.running = true;

          if(context.page === null) {
            var qsMatch = loc.href.match('page=([0-9]+)');
            if(qsMatch !== null) {
              context.page = qsMatch[1];
            } else {
              var uriMatch = loc.pathname.match(/\/page-([0-9]+)/);
              if(uriMatch !== null) {
                context.page = uriMatch[1];
              }
            }

            if(context.page === null)
              context.page = 1;
          }

          var api = context.pageLocation.pathname+'.json';
              params = {}, qs = context.pageLocation.search.replace('?', '');
          
          if(qs.length > 0) {
            var qsKeyValueStrings = qs.split('&'), qsKeyAndValue;
            for(i in qsKeyValueStrings) {
              if(qsKeyValueStrings.hasOwnProperty(i)) {
                qsKeyAndValue = qsKeyValueStrings[i].split('=');
                if(qsKeyAndValue.length === 2) {
                  params[qsKeyAndValue[0]] = qsKeyAndValue[1];
                }
              }
            }
          }

          params.returnSizes = '960x180';
          params.page = context.page;

          // for mobile devices limit the number pages before a full page refresh. See #778
          if(context.pageCount > context.maxMobilePageCount && util.getDeviceWidth() < 900) {
            location.href = context.pageLocation.pathname + '?' + decodeURIComponent($.param(params));
          } else {
            $.getJSON(api, params, context.loadCb);
          }
        } else {
          delete context.initData;
          context.page = 1;
          var response = {code:200, result:initData};
          context.loadCb(response);
        }
      },
      scrollCb: function(context) {
        // don't autoload if the width is narrow
        //  crude way to check if we're on a mobile device
        //  See #778
        if(util.getDeviceWidth() < 900)
          return;

        if($(window).scrollTop() > $(document).height() - $(window).height() - 200){
          context.load();
        }
      },
    };
  })();

  return {
    crumb: function() { return crumb.get(); },
    handlers: {
      submit: {
        login: function(ev) {
          ev.preventDefault();
          var form = $(ev.target),
              params = form.serialize();
          params += '&httpCodes=403';
          $.ajax(
            {
              url: '/user/self/login.json',
              dataType:'json',
              data:params,
              type:'POST',
              success: callbacks.loginSuccess,
              error: function() { console.log('f'); },//opTheme.callback.loginOpenPhotoFailedCb,
              context: form
            }
          );
          return false;
        }
      },
      click: {
      },
      keydown: {
      },
      keyup: {
      },
      mouseover: {
      }
    },
    init: {
      load: function(_crumb) {
        // http://stackoverflow.com/a/6974186
        // http://stackoverflow.com/questions/6421769/popstate-on-pages-load-in-chrome/10651028#10651028
        var popped = ('state' in window.history && window.history.state !== null);

        crumb.set(_crumb);
        OP.Tag.init();
        pathname = location.pathname;

        /* jm History.Adapter.bind(window,'statechange',function(){
          var State = History.getState(),
              initialPop = !popped && location.href == initialURL;
          popped = true;
          if(initialPop)
            return;

          pushstate.render(State.data);
        });*/

        // TODO cache in local storage

        profiles.load();

        if(location.pathname === '/')
          TBX.init.pages.front();
        else if(location.pathname.search(/^\/albums(.*)\/list/) === 0)
          TBX.init.pages.albums.init();
        else if(location.pathname === '/manage/photos')
          TBX.init.pages.manage.photos();
        else if(location.pathname.search(/^\/photos(.*)\/list/) === 0)
          TBX.init.pages.photos.init();
        else if(location.pathname.search(/^\/p\/[a-z0-9]+/) === 0 || location.pathname.search(/^\/photo\/[a-z0-9]+\/?(.*)\/view/) === 0)
          TBX.init.pages.photo.init();
        else if(location.pathname === '/photos/upload')
          TBX.init.pages.upload();
      },
      pages: {
        albums: {
          initData: typeof(initData) === "undefined" ? undefined : initData,
          filterOpts: typeof(filterOpts) === "undefined" ? undefined : filterOpts,
          page: null,
          pageCount: 0,
          pageLocation: window.location,
          maxMobilePageCount: 5,
          end: false,
          running: false,
          addAlbums: function(albums) {
            var album, model, view, $el;
            for(i in albums) {
              if(albums.hasOwnProperty(i)) {
                $el = $('<li class="album" />').appendTo($('ul.albums'))
                album = albums[i];
                op.data.store.Albums.add( album );
                model = op.data.store.Albums.get(album.id);
                view = new op.data.view.AlbumCover({model: model, el: $el});
                view.render();
              }
            }
          },
          init: function() {
            var _pages = TBX.init.pages, _this = _pages.albums;
            $(window).scroll(function() { util.scrollCb(_this); });
            util.load(_this);
          },
          load: function() {
            var _this = TBX.init.pages.albums; loc = location;
            util.load(_this);
          },
          loadCb: function(response) {
            var items = response.result, _this = TBX.init.pages.albums;
            op.data.store.Albums.add( items );
            if(items.length >= _this.page) {
              _this.addAlbums(items);
              _this.page++;
              _this.pageCount++;
              _this.running = false;
            } else {
              $('.load-more').hide();
              _this.end = true;
            }
          }
        },
        photos: {
          // TODO have a better way of sending data into the JS framework. See #780
          initData: typeof(initData) === "undefined" ? undefined : initData,
          filterOpts: typeof(filterOpts) === "undefined" ? undefined : filterOpts,
          page: null,
          pageCount: 0,
          pageLocation: window.location,
          maxMobilePageCount: 5,
          end: false,
          running: false,
          init: function() {
            var _pages = TBX.init.pages, _this = _pages.photos;
            $(window).scroll(function() { util.scrollCb(_this); });
            _this.load();
          },
          load: function() {
            var _this = TBX.init.pages.photos; loc = location;
            Gallery.setConfig('marginsOfImage', 10);
            util.load(_this);
          },
          loadCb: function(response) {
            var items = response.result, _this = TBX.init.pages.photos, infobar = $('.infobar'),
                minDate = $('.startdate', infobar), maxDate = $('.enddate', infobar),
                minDateVal = parseInt(minDate.attr('data-time')), maxDateVal = parseInt(maxDate.attr('data-time')),
                ui = TBX.ui, i;
            op.data.store.Photos.add( items );
            if(items[0].totalPages >= _this.page) {

              var thisTaken;
              for(i=0; i<items.length; i++) {
                thisTaken = parseInt(items[i].dateTaken);
                if(thisTaken > maxDateVal) {
                  ui.fadeAndSet(maxDate, phpjs.date('l F jS, Y', thisTaken));
                  maxDate.attr('data-time', thisTaken);
                  maxDateVal = thisTaken;
                } else if(parseInt(items[i].dateTaken) < parseInt(minDate.attr('data-time'))) {
                  ui.fadeAndSet(minDate, phpjs.date('l F jS, Y', thisTaken));
                  minDate.attr('data-time', thisTaken);
                  minDateVal = thisTaken;
                }
              }

              Gallery.showImages($(".photo-grid"), items);
              _this.page++;
              _this.pageCount++;
              _this.running = false;
            } else {
              $('.load-more').hide();
              _this.end = true;
            }
          }
        }
      }
    },
    profiles: {
      getOwner: function() {
        return profiles.owner.id;
      },
      getViewer: function() {
        return profiles.viewer.id;
      }
    }
  };
})();
