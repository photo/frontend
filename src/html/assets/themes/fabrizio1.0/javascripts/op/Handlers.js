(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};
  
  function Handlers() {
    var state = {};

    // CLICK
    this.click = {};
    this.click.batchHide = function(ev) {
      ev.preventDefault();
      $('.secondary-flyout').slideUp('fast');
    };
    this.click.credentialDelete = function(ev) {
      ev.preventDefault();
      var el = $(ev.target),
          url = el.attr('href')+'.json',
          params = {crumb: TBX.crumb()};

      OP.Util.makeRequest(url, params, TBX.callbacks.credentialDelete.bind(el));
      return false;
    };
    this.click.credentialView = function(ev) {
      ev.preventDefault();
      var el = $(ev.target),
          url = el.attr('href') + '.json',
          params = {crumb: TBX.crumb()};

      OP.Util.makeRequest(url, params, TBX.callbacks.credentialView, 'json', 'get');
      return false;
    };
    this.click.loginExternal = function(ev) {
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
    };
    this.click.notificationDelete = function(ev) {
      ev.preventDefault();
      OP.Util.makeRequest('/notification/delete.json', {crumb: TBX.crumb()}, null, 'json');
    };
    this.click.photoModal = function(ev) {
      ev.preventDefault();
      var $el = $(ev.target), id = $el.attr('data-id'), $container = $el.closest('.imageContainer'), $pin = $('.pin.edit', $container), url = '/p/'+id, router = op.data.store.Router;
      
      if(ev.altKey && $pin.length > 0) { // alt+click pins the photo
        $pin.trigger('click');
      } else if (ev.shiftKey && $pin.length > 0){ // shift+click pins a range of photos
        // if state.shiftId is undefined then we start the range
        //  else we complete it
        if(typeof(state.shiftId) === 'undefined') {
          state.shiftId = id;
        } else {
          //  if the start and end are the same we omit
          if(id == state.shiftId)
            return;

          var $start = $('.imageContainer.photo-id-'+state.shiftId), $end = $container, range = $.merge([$start, $end], $start.nextUntil($end));
          for(i in range) {
            if(range.hasOwnProperty(i))
              $('.pin.edit', range[i]).trigger('click');
          }
          delete state.shiftId;
        }
      } else {
        router.navigate(url, {trigger:true});
      }
    };
    this.click.pluginStatusToggle = function(ev) {
      ev.preventDefault();
      var el = $(ev.target),
          url = el.attr('href')+'.json',
          params = {crumb: TBX.crumb()};
      OP.Util.makeRequest(url, params, TBX.callbacks.pluginStatusToggle.bind(el), 'json', 'post');
      return false;
    };
    this.click.pluginView = function(ev) {
      ev.preventDefault();
      var el = $(ev.target),
          url = el.attr('href')+'.json',
          params = {crumb: TBX.crumb()},
          urlParts = url.match(/\/plugin\/(.*)\/view.json$/);
      OP.Util.makeRequest('/plugin/'+urlParts[1]+'/view.json', params, TBX.callbacks.pluginView, 'json', 'get');
    };
    this.click.selectAll = function(ev) {
      ev.preventDefault();
      var $els = $('.photo-grid .imageContainer .pin.edit'), batch = OP.Batch, count = batch.length();
      $els.each(TBX.callbacks.selectAll);
      TBX.notification.show(TBX.format.sprintf(TBX.strings.batchConfirm, count, count > 1 ? 's' : ''), 'flash', 'confirm');
    };
    this.click.sharePopup = function(ev) {
      ev.preventDefault();
      var $el = $(ev.target), url = $el.attr('href'), w=575, h=300, l, t, opts;
      l = parseInt(($(window).width()  - w)  / 2);
      t = parseInt(($(window).height() - h) / 2);
      opts = 'location=0,toolbar=0,status=0,width='+w+',height='+h+',left='+l+',top='+t;
      window.open(url, 'TB', opts);
    };
    this.click.triggerDownload = function(ev) {
      ev.preventDefault();
      var $el = $('.download.trigger'), url = $el.attr('href');
      location.href = url;
    },
    this.click.triggerShare = function(ev) { 
      ev.preventDefault();
      var $el = $('.share.trigger');
      $el.trigger('click');
    };

    this.custom = {};
    this.custom.preloadPhotos = function(photos) {
      if(photos.length == 0)
        return;

      for(i in photos) {
        if(photos.hasOwnProperty(i)) { 
          TBX.util.fetchAndCache(photos[i]);
        }
      }
    };

    this.keydown = { },
    this.keyup = { },
    this.mouseover = { },
    this.submit = {};
    this.submit.albumCreate = function(ev) {
      ev.preventDefault();
      var $form = $(ev.target), params = {name: $('input[name="name"]', $form).val(), crumb: TBX.crumb()};
      $('button', $form).prepend('<i class="icon-spinner icon-spin"></i> ');
      OP.Util.makeRequest('/album/create.json', params, TBX.callbacks.albumCreate, 'json', 'post');
    };
    this.submit.batch = function(ev) {
      ev.preventDefault();
      var $form = $(ev.target), url = '/photos/update.json', formParams = $form.serializeArray(), batch = OP.Batch, params = {ids: batch.ids().join(','), crumb: TBX.crumb()};
      $('button', $form).prepend('<i class="icon-spinner icon-spin"></i> ');
      for(i in formParams) {
        if(formParams.hasOwnProperty(i)) {
          if(formParams[i].name === 'albumsAdd') {
            url = '/album/'+formParams[i].value+'/photo/add.json';
          } else if(formParams[i].name === 'albumsRemove') {
            url = '/album/'+formParams[i].value+'/photo/remove.json';
          } else if(formParams[i].name === 'delete') {
            if($('input[name="confirm"]', $form).attr('checked') === 'checked' && $('input[name="confirm2"]', $form).attr('checked') === 'checked') {
              url = '/photos/delete.json';
            } else {
              TBX.notification.show("Check the appropriate checkboxes so we know you're serious.", 'flash', 'error');
              $($('button i', $form)[0]).remove();
              return; // don't continue
            }
          }

          params[formParams[i].name] = formParams[i].value;
        }
      }
      OP.Util.makeRequest(url, params, TBX.callbacks.batch.bind(params), 'json', 'post');
    };
    this.click.batchAlbumMode = function(ev) {
      var $el = $(ev.target), $form = $el.closest('form'), $albums = $('select.albums', $form);
      if($el.val() === 'add')
        $albums.attr('name', 'albumsAdd');
      else
        $albums.attr('name', 'albumsRemove');
    };
    this.click.batchTagMode = function(ev) {
      var $el = $(ev.target), $form = $el.closest('form'), $tags = $('input.tags', $form);
      if($el.val() === 'add')
        $tags.attr('name', 'tagsAdd');
      else
        $tags.attr('name', 'tagsRemove');
    };
    this.submit.login = function(ev) {
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
    };
    this.submit.pluginUpdate = function(ev) {
      ev.preventDefault();
      var form = $(ev.target),
          url = form.attr('action')+'.json';
      OP.Util.makeRequest(url, form.serializeArray(), TBX.callbacks.pluginUpdate, 'json', 'post');
      return false;
    };
    this.submit.shareEmail = function(ev) {
      ev.preventDefault();
      var $form = $(ev.target),
          params = $form.serialize()
          data = $('input[name="data"]', $form).val(),
          type = $('input[name="type"]', $form).val(),
          url = $('input[name="url"]', $form).val(),
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
      console.log(data);
      $.ajax(
        {
          url: '/share/'+type+'/'+data+'/send.json',
          dataType:'json',
          data:params,
          type:'POST',
          success: TBX.callbacks.shareEmailSuccess,
          error: function() { $('button i', $form).remove(); TBX.notification.display.generic.error(); },
          context: $form
        }
      );
      return false;
    };
    this.submit.search = function(ev) {
      ev.preventDefault();
      var $form = $(ev.target), $button = $('button', $form), query = $('input[type="search"]', $form).val(), albums = OP.Album.getAlbums(), 
          albumSearch = _.where(albums, {name: query.replace(/,+$/, '')}), isAlbum  = albumSearch.length > 0, url;
      if(query === '')
        return;

      $button.html('<i class="icon-spinner icon-spin"></i>');
      // trim leading and trailing commas and spaces in between
      if(isAlbum) {
        url = TBX.format.sprintf('/photos/album-%s/list', albumSearch[0].id);
      } else {
        query = query.replace(/^,\W*|,\W*$/g, '').replace(/\W*,\W*/, ',');
        url = TBX.format.sprintf('/photos/tags-%s/list', query);
      }
      location.href = url;
    };
    this.submit.upload = function(ev) {
      ev.preventDefault();
      var uploader = $("#uploader").pluploadQueue();
      if (typeof(uploader.files) != 'undefined' && uploader.files.length > 0) {
        uploader.start();
      } else {
        // TODO something that doesn't suck
        //opTheme.message.error('Please select at least one photo to upload.');
      }
    };
  }
  
  TBX.handlers = new Handlers;
})(jQuery);
