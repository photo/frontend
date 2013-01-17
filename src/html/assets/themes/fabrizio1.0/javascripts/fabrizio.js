var TBX = (function() {
  var crumb, log, markup, profiles, pushstate, tags, pathname, util;

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
  })(); // crumb
  markup = {
    message: function(message) { // messageMarkup
      var cls = '';
      if(arguments.length > 1) {
        if(arguments[1] == 'error')
          cls = 'error';
        else if(arguments[1] == 'confirm')
          cls = 'success';
      }
      return '<div class="alert-message block-message '+cls+'"><a class="modal-close-click close" href="#">x</a>' + message + '</div>'
    },
    modal: function(header, body, footer) { // modalMarkup
      return '<div class="modal-header">' +
             '  <a href="#" class="close" data-dismiss="modal">&times;</a>' +
             '  <h3>'+header+'</h3>' +
             '</div>' +
             '<div class="modal-body">' +
             '  <p>'+body+'</p>' +
             '</div>' +
             (footer ? '<div class="modal-footer">' + footer + '</div>' : '');
    }
  }; // markup
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
        TBX.callbacks.profilesSuccess(owner, viewer);
      }, 'json');
    }
  }; // profiles
  util = (function() {
    return {
      getDeviceWidth: function() {
        return $(window).width();
      },
      fetchAndCache: function(src) {
        $('<img />').attr('src', src).appendTo('body').css('display', 'none').on('load', function(ev) { $(ev.target).remove(); });
      },
      load: function(context) {
        var async = typeof(arguments[1]) === 'undefined' ? true : arguments[1];
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

          params.returnSizes = '960x180,870x870,180x180xCR';
          params.page = context.page;
          // for mobile devices limit the number pages before a full page refresh. See #778
          if(context.pageCount > context.maxMobilePageCount && util.getDeviceWidth() < 900) {
            location.href = context.pageLocation.pathname + '?' + decodeURIComponent($.param(params));
          } else {
            $.ajax({
              async: async,
              dataType: 'json',
              url: api,
              data: params,
              success: context.loadCb
            });
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
  })(); // util

  return {
    crumb: function() { return crumb.get(); },
    callbacks: {
      batch: function(response) { // this is the form params
        var id, model, ids = this.ids.split(','), photoCount = ids.length, store = op.data.store.Photos;
        if(response.code === 200) {
          if(typeof(this.permission) !== 'undefined') {
            for(i in ids) {
              if(ids.hasOwnProperty(i)) {
                id = ids[i];
                model = store.get(id);
                if(model)
                  model.fetch();
              }
            }
          }
          TBX.notification.show('You successfully updated ' + photoCount + ' photo' + (photoCount>1?'s':'') + '.', 'flash', 'confirm');
        } else {
          TBX.notification.show('Sorry, an error occured when trying to add tags to your photos.', 'flash', 'error');
        }
        $('.batchHide').trigger('click');
      },
      credentialDelete: function(response) {
        if(response.code === 204) {
          this.closest('tr').slideUp('medium');
          TBX.notification.show('Your app was successfully deleted.', null, 'error');
        } else {
          TBX.notification.show('There was a problem deleting your app.', null, 'error');
        }
      },
      loginSuccess: function() {
        var redirect = $('input[name="r"]', this).val();
        window.location.href = redirect;
      },
      personaSuccess: function(assertion) {
        var params = {assertion: assertion};
        OP.Util.makeRequest('/user/browserid/login.json', params, TBX.callbacks.loginProcessed);
      },
      loginProcessed: function(response) {
        if(response.code != 200) {
          TBX.notification.show('Sorry, we could not log you in.', 'flash', 'error');
          return;
        }
        
        var url = $('input[name="r"]', $('form.login')).val();
        location.href = url;
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
          
        $('.user-badge-meta').each(function(i, el) {
          (new op.data.view.UserBadge({model:op.data.store.Profiles.get(ownerId), el: el})).render();
        });
        $('.profile-name-meta.owner').each(function(i, el) {
          (new op.data.view.ProfileName({model:op.data.store.Profiles.get(ownerId), el: el})).render();
        });
        $('.profile-photo-meta').each(function(i, el) {
          (new op.data.view.ProfilePhoto({model:op.data.store.Profiles.get(ownerId), el: el})).render();
        });
        (new op.data.view.ProfilePhoto({model:op.data.store.Profiles.get(viewerId), el: $('.profile-photo-header-meta')})).render();
      },
      selectAll: function(i, el) {
        var id = $(el).attr('data-id'), photo = op.data.store.Photos.get(id).toJSON();
        OP.Batch.add(id, photo);
      },
      share: function(response) {
        var result = response.result;
        $('.secondary-flyout').html(result.markup).slideDown('fast');
      },
      shareEmailSuccess: function(response) {
        var result = response.result;
        $('a.batchHide').trigger('click');
        TBX.notification.show('Your photo was successfully emailed.', 'flash', 'confirm');
      },
      upload: function(ev) {
        ev.preventDefault();
        var uploader = $("#uploader").pluploadQueue();
        if (typeof(uploader.files) != 'undefined' && uploader.files.length > 0) {
          uploader.start();
        } else {
          // TODO something that doesn't suck
          //opTheme.message.error('Please select at least one photo to upload.');
        }
      },
      uploadCompleteSuccess: function(photoResponse) {
        photoResponse.crumb = TBX.crumb();
        $("form.upload").fadeOut('fast', function() {
          OP.Util.makeRequest('/photos/upload/confirm.json', photoResponse, TBX.callbacks.uploadConfirm, 'json', 'post');
        });
      },
      uploadConfirm: function(response) {
        var $el, container, model, view, item, result = response.result, success = result.data.successPhotos;
        $(".upload-container").fadeOut('fast', function() { $(".upload-confirm").fadeIn('fast'); });
        $(".upload-confirm").html(result.tpl).show('fast', function(){
          if(success.length > 0) {
            op.data.store.Photos.add(success);
            container = $('.upload-preview.success');
            Gallery.showImages(container, success);
          }
        });
      },
      uploaderReady: function() {
        var form = $('form.upload');
        if(typeof OPU === 'object')
          OPU.init();

        //$("select.typeahead").chosen();
      }
    },
    handlers: {
      click: {
        batchHide: function(ev) {
          ev.preventDefault();
          $('.secondary-flyout').slideUp('fast');
        },
        credentialDelete: function(ev) {
          ev.preventDefault();
          var el = $(ev.target),
              url = el.attr('href')+'.json',
              params = {crumb: TBX.crumb()};

          OP.Util.makeRequest(url, params, TBX.callbacks.credentialDelete.bind(el));
          return false;
        },
        loginExternal: function(ev) {
          ev.preventDefault();
          var el = $(ev.target);
          if(el.hasClass('persona')) {
            navigator.id.getVerifiedEmail(function(assertion) {
                if (assertion) {
                  TBX.callbacks.personaSuccess(assertion);
                } else {
                  TBX.notification.show('Sorry, something went wrong trying to log you in.', 'flash', 'error');
                }
            });
          } else if(el.hasClass('facebook')) {
            FB.login(function(response) {
              if (response.authResponse) {
                log('User logged in, posting to openphoto host.');
                OP.Util.makeRequest('/user/facebook/login.json', opTheme.user.base.loginProcessed);
              } else {
                log('User cancelled login or did not fully authorize.');
              }
            }, {scope: 'email'});
          }
          return false;
        },
        notificationDelete: function(ev) {
          ev.preventDefault();
          OP.Util.makeRequest('/notification/delete.json', {crumb: TBX.crumb()}, null, 'json');
        },
        photoModal: function(ev) {
          ev.preventDefault();
          var $el = $(ev.target), url = '/p/'+$el.attr('data-id'), router = op.data.store.Router;
          router.navigate(url, {trigger:true});
        },
        selectAll: function(ev) {
          ev.preventDefault();
          var $els = $('.photo-grid .imageContainer .pin.edit'), batch = OP.Batch, preCount = batch.length(), addedCount;
          $els.each(TBX.callbacks.selectAll);
          addedCount = batch.length() - preCount;
          TBX.notification.show(addedCount + ' new photos were <em>added</em> to your <i class="icon-cogs"></i> batch queue.', 'flash', 'confirm');
        },
        sharePopup: function(ev) {
          ev.preventDefault();
          var $el = $(ev.target), url = $el.attr('href'), w=575, h=300, l, t, opts;
          l = parseInt(($(window).width()  - w)  / 2);
          t = parseInt(($(window).height() - h) / 2);
          opts = 'location=0,toolbar=0,status=0,width='+w+',height='+h+',left='+l+',top='+t;
          window.open(url, 'TB', opts);
        }
      },
      custom: {
        preloadPhotos: function(photos) {
          if(photos.length == 0)
            return;

          for(i in photos) {
            if(photos.hasOwnProperty(i)) { 
              util.fetchAndCache(photos[i]);
            }
          }
        }
      },
      keydown: { },
      keyup: { },
      mouseover: { },
      submit: {
        batch: function(ev) {
          ev.preventDefault();
          var $form = $(ev.target), formParams = $form.serializeArray(), batch = OP.Batch, params = {ids: batch.ids().join(','), crumb: TBX.crumb()};
          $('button', $form).prepend('<i class="icon-spinner icon-spin"></i> ');
          for(i in formParams) {
            if(formParams.hasOwnProperty(i)) {
              params[formParams[i].name] = formParams[i].value;
            }
          }
          OP.Util.makeRequest('/photos/update.json', params, TBX.callbacks.batch.bind(params), 'json', 'post');
        },
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
              success: TBX.callbacks.loginSuccess,
              error: TBX.notification.display.generic.error,
              context: form
            }
          );
        },
        shareEmail: function(ev) {
          ev.preventDefault();
          var $form = $(ev.target),
              params = $form.serialize()
              ids = $('input[name="ids"]', $form).val(),
              recipients = $('input[name="recipients"]', $form).val(),
              message = $('textarea[name="message"]', $form).val();

          if(recipients.length == 0) {
            TBX.notification.show('Please specify at least one recipient.', 'flash', 'error');
            return;
          } else if(message.length === 0) {
            TBX.notification.show('Please type in a message.', 'flash', 'error');
            return;
          }

          $('button', $form).prepend('<i class="icon-spinner icon-spin"></i> ');
          $.ajax(
            {
              url: '/photos/'+ids+'/share.json',
              dataType:'json',
              data:params,
              type:'POST',
              success: TBX.callbacks.shareEmailSuccess,
              error: function() { $('button i', $form).remove(); TBX.notification.display.generic.error(); },
              context: $form
            }
          );
          return false;
        },
        upload: function(ev) {
          ev.preventDefault();
          var uploader = $("#uploader").pluploadQueue();
          if (typeof(uploader.files) != 'undefined' && uploader.files.length > 0) {
            uploader.start();
          } else {
            // TODO something that doesn't suck
            //opTheme.message.error('Please select at least one photo to upload.');
          }
        }
      }
    }, // handlers
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
        
        // init tooltips

        if(location.pathname === '/')
          TBX.init.pages.front.init();
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
      attachEvents: function() {
        OP.Util.on('preload:photos', TBX.handlers.custom.preloadPhotos);
      },
      pages: {
        albums: {
          initData: typeof(initData) === "undefined" ? undefined : initData,
          filterOpts: typeof(filterOpts) === "undefined" ? undefined : filterOpts,
          page: null,
          pageCount: 0,
          pageLocation: {
            pathname: window.location.pathname,
            search: window.location.search
          },
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
            if(items.length > 0) {
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
        front: {
          
          initData: typeof(initData) === "undefined" ? undefined : initData,
          filterOpts: typeof(filterOpts) === "undefined" ? undefined : filterOpts,
          batchModel: new op.data.model.Batch({count: OP.Batch.length()}),
          page: null,
          pageCount: 0,
          pageLocation: {
            pathname: window.location.pathname,
            search: window.location.search
          },
          maxMobilePageCount: 5,
          end: false,
          running: false,
          init: function() {
            var _pages = TBX.init.pages, _this = _pages.photos, batchModel = _pages.photos.batchModel, $batchEl = $('.batch-meta');
            $(window).scroll(function() { util.scrollCb(_this); });
            _this.load();
            (new op.data.view.BatchIndicator({model:batchModel, el: $batchEl})).render();
            _this.router.init();
          },
          load: function() {
            var _this = TBX.init.pages.photos, async = typeof(arguments[0]) === 'undefined' ? true : arguments[0];
            util.load(_this, async);
          },
          loadCb: function(response) {
            var items = response.result, _this = TBX.init.pages.photos, infobar = $('.infobar'),
                minDate = $('.startdate', infobar), maxDate = $('.enddate', infobar),
                minDateVal = parseInt(minDate.attr('data-time')), maxDateVal = parseInt(maxDate.attr('data-time')),
                ui = TBX.ui, i;

            op.data.store.Photos.add( items );
            if(items.length > 0) {
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
          
        },
        photo: {
          initData: typeof(initData) === "undefined" ? undefined : initData,
          filterOpts: typeof(filterOpts) === "undefined" ? undefined : filterOpts,
          photo: null,
          el: $('.photo-detail'),
          init: function() {
            var options, _this = TBX.init.pages.photo;
            if(_this.initData === undefined) {
              return;
            }

            options = {
              routes: {
                "p/:id": "photoDetail" 
              },
              render: _this.render
            };
            op.data.store.Router = new op.data.route.Routes(options);
            // Start Backbone history a necessary step for bookmarkable URL's
            Backbone.history.start({pushState: true, silent: true});

            _this.photo = initData;
            delete _this.photo.actions;
            _this.render(_this.photo);
            delete _this.initData;
          },
          load: function(id) {
            // TODO don't hard code the returnSizes
            var _this = TBX.init.pages.photo, endpoint, apiParams = {nextprevious:'1', returnSizes:'90x90xCR,870x550'};
            
            if(_this.filterOpts === undefined || _this.filterOpts === null)
              endpoint = '/photo/'+id+'/view.json';
            else
              endpoint = '/photo/'+id+'/'+filterOpts+'/view.json';

            OP.Util.makeRequest(endpoint, apiParams, function(response) {
              _this.render(response.result);
            }, 'json', 'get');
          },
          render: function(photo) {
            var _this = TBX.init.pages.photo, $el = _this.el;
            op.data.store.Photos.add(photo);
            (new op.data.view.PhotoDetail({model: op.data.store.Photos.get(photo.id), el: $el})).render();
          }
        },
        photos: {
          // TODO have a better way of sending data into the JS framework. See #780
          initData: typeof(initData) === "undefined" ? undefined : initData,
          filterOpts: typeof(filterOpts) === "undefined" ? undefined : filterOpts,
          batchModel: new op.data.model.Batch({count: OP.Batch.length()}),
          page: null,
          pageCount: 0,
          pageLocation: {
            pathname: window.location.pathname,
            search: window.location.search
          },
          maxMobilePageCount: 5,
          end: false,
          running: false,
          init: function() {
            var options, _pages = TBX.init.pages, _this = _pages.photos, batchModel = _pages.photos.batchModel, $batchEl = $('.batch-meta');
            $(window).scroll(function() { util.scrollCb(_this); });
            _this.load();
            (new op.data.view.BatchIndicator({model:batchModel, el: $batchEl})).render();

            options = {
              routes: {
                "p/:id": "photoModal",
                "photos/:options/list": "photosList",
                "photos/list": "photosList"
              },
            };
            op.data.store.Router = new op.data.route.Routes(options);
            // Start Backbone history a necessary step for bookmarkable URL's
            Backbone.history.start({pushState: true, silent: true});
          },
          load: function() {
            var _this = TBX.init.pages.photos, async = typeof(arguments[0]) === 'undefined' ? true : arguments[0];
            util.load(_this, async);
          },
          loadCb: function(response) {
            var items = response.result, _this = TBX.init.pages.photos, infobar = $('.infobar'),
                minDate = $('.startdate', infobar), maxDate = $('.enddate', infobar),
                minDateVal = parseInt(minDate.attr('data-time')), maxDateVal = parseInt(maxDate.attr('data-time')),
                ui = TBX.ui, i;

            op.data.store.Photos.add( items );
            if(items.length > 0) {
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
        },
        upload: function() {
          OP.Util.on('upload:complete-success', TBX.callbacks.uploadCompleteSuccess);
          OP.Util.on('upload:complete-failure', TBX.callbacks.uploadCompleteFailure);
          OP.Util.on('upload:uploader-ready', TBX.callbacks.uploaderReady);
          OP.Util.on('submit:photo-upload', TBX.callbacks.upload);
          OP.Util.fire('upload:uploader-ready');
        }
      }
    }, // init
    notification: {
      model: new op.data.model.Notification,
      errorIcon: '<i class="icon-warning-sign"></i>',
      successIcon: '<i class="icon-ok"></i>',
      init: function() {
        var $el = $('.notification-meta'), view = new op.data.view.Notification({model: TBX.notification.model, el: $el});
      },
      show: function(message, type, mode) {
        var model = TBX.notification.model;
        if(mode === 'confirm' || typeof mode === 'undefined')
          message = TBX.notification.successIcon + ' ' + message;
        else
          message = TBX.notification.errorIcon + ' ' + message;

        type = type || 'flash';

        model.set('msg', message, {silent:true});
        model.set('mode', mode, {silent:true});
        model.set('type', type, {silent:true});
        model.save();
      },
      display: {
        generic: {
          error: function() {
            TBX.notification.show('Sorry, an unknown error occurred.', 'flash', 'error');
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
    } // profiles
  };
})();
