(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};
  
  function Handlers() {
    var state = {};

    // CLICK
    this.click = {};
    this.click.addSpinner = function(ev) {
      var $el = $(ev.target);
      // remove existing icons if any
      $el.find('i').remove();
      $('<i class="icon-spinner icon-spin"></i>').prependTo($el);
    };
    this.click.batchAlbumMode = function(ev) {
      var $el = $(ev.target), $form = $el.closest('form'), $albums = $('select.albums', $form);
      if($el.val() === 'add')
        $albums.attr('name', 'albumsAdd');
      else
        $albums.attr('name', 'albumsRemove');
    };
    this.click.batchHide = function(ev) {
      ev.preventDefault();
      $('.secondary-flyout').slideUp('fast');
    };
    this.click.batchTagMode = function(ev) {
      var $el = $(ev.target), $form = $el.closest('form'), $tags = $('input.tags', $form);
      if($el.val() === 'add')
        $tags.attr('name', 'tagsAdd');
      else
        $tags.attr('name', 'tagsRemove');
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
    this.click.loadMorePhotos = function(ev) {
      ev.preventDefault();
      TBX.init.pages.photos.load();
    };
    this.click.loadMoreAlbums = function(ev) {
      ev.preventDefault();
      TBX.init.pages.albums.load();
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
      var $el = $(ev.target), id = $el.attr('data-id'), $a = $el.closest('a'), $container = $el.closest('.imageContainer'), $pin = $('.pin.edit', $container), url = $a.attr('href'), router = op.data.store.Router, lightbox;
      
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
        lightbox = op.Lightbox.getInstance();
        lightbox._pathWithQuery = location.pathname+location.search;
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
      var $els = $('.photo-grid .imageContainer .pin.edit'), batch = OP.Batch, count;
      $els.each(TBX.callbacks.selectAll);
      count = batch.length();
      TBX.notification.show(TBX.format.sprintf(TBX.strings.batchConfirm, count, count > 1 ? 's' : ''), 'flash', 'confirm');
    };
    this.click.setAlbumCover = function(ev) {
      ev.preventDefault();
      var $el = $(ev.target), photoId = $el.attr('data-id'), albumId = TBX.util.getPathParam('album');
      OP.Util.makeRequest(TBX.format.sprintf('/album/%s/cover/%s/update.json', albumId, photoId), {crumb: TBX.crumb()}, TBX.callbacks.setAlbumCover, 'json', 'post');
    };
    this.click.shareAlbum = function(ev) {
      ev.preventDefault();
      var $el = $(ev.target), id = $el.attr('data-id');
      OP.Util.makeRequest('/share/album/'+id+'/view.json', {crumb: TBX.crumb()}, TBX.callbacks.share, 'json', 'get');
    };
    this.click.sharePopup = function(ev) {
      ev.preventDefault();
      var $el = $(ev.target), url = $el.attr('href'), w=575, h=300, l, t, opts;
      l = parseInt(($(window).width()  - w)  / 2);
      t = parseInt(($(window).height() - h) / 2);
      opts = 'location=0,toolbar=0,status=0,width='+w+',height='+h+',left='+l+',top='+t;
      window.open(url, 'TB', opts);
    };
    this.click.showBatchForm = function(ev) {
      ev.preventDefault();
      var $el = $(ev.target), params = {}, model;
      if($el.hasClass('photo')) {
        model = TBX.init.pages.photos.batchModel;
        model.set('loading', true);
        params.action = $el.attr('data-action');
        // we allow overriding the batch queue by passing in an id
        if($el.attr('data-ids'))
          params.ids = $el.attr('data-ids');

        OP.Util.makeRequest('/photos/update.json', params, function(response) {
          var result = response.result;
          model.set('loading', false);
          $('.secondary-flyout').html(result.markup).slideDown('fast');
          if(typeof(params.action) !== "undefined" && params.action === "tags") {
            new op.data.view.TagSearch({el: $('form.batch input.typeahead')});
          }
        }, 'json', 'get');
      } else if($el.hasClass('album')) {
        model = TBX.init.pages.albums.batchModel;
        model.set('loading', true);
        OP.Util.makeRequest('/album/form.json', params, function(response) {
          var result = response.result;
          model.set('loading', false);
          $('.secondary-flyout').html(result.markup).slideDown('fast');
        }, 'json', 'get');
      }
      return;
    };
    this.click.toggle = function(ev) {
      // use data-type to support other toggles
      ev.preventDefault();
      var $el = $(ev.target), $tgt = $($el.attr('data-target'));
      $tgt.slideToggle();
    };
    this.click.tokenDelete = function(ev) {
      ev.preventDefault();
      var el = $(ev.target),
          url = el.attr('href')+'.json',
          params = {crumb: TBX.crumb()};

      OP.Util.makeRequest(url, params, TBX.callbacks.tokenDelete.bind(el));
      return false;
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
    this.click.tutorial = function(ev) {
      ev.preventDefault();
      TBX.tutorial.run();
    };
    this.click.uploadBeta = function(ev) {
      ev.preventDefault();
      TBX.upload.start();
    };

    this.keydown = { };
    this.keyup = { };
    this.keyup["27"] = "keyup:escape";
    this.keyup["191"] = "keyup:slash";
    this.keyup["37"] = "keyup:left";
    this.keyup["39"] = "keyup:right";
    this.mouseover = { };
    this.submit = {};
    this.submit.albumCreate = function(ev) {
      ev.preventDefault();
      var $form = $(ev.target), params = {name: $('input[name="name"]', $form).val(), crumb: TBX.crumb()};
      $('button', $form).prepend('<i class="icon-spinner icon-spin"></i> ');
      OP.Util.makeRequest('/album/create.json', params, TBX.callbacks.albumCreate, 'json', 'post');
    };
    this.submit.batch = function(ev) {
      ev.preventDefault();
      var $form = $(ev.target), url = '/photos/update.json', formParams = $form.serializeArray(), batch = OP.Batch, idsArr = batch.ids(), params = {ids: idsArr.join(','), crumb: TBX.crumb()};
      $('button', $form).prepend('<i class="icon-spinner icon-spin"></i> ');
      for(i in formParams) {
        if(formParams.hasOwnProperty(i)) {
          // we allow the batch ids to be overridden by a parameter named ids
          if(formParams[i].name === 'ids')
            params.ids = formParams[i].value;

          if(formParams[i].name === 'albumsAdd') {
            url = '/album/'+formParams[i].value+'/photo/add.json';
          } else if(formParams[i].name === 'albumsRemove') {
            url = '/album/'+formParams[i].value+'/photo/remove.json';
          } else if(formParams[i].name === 'delete') {
            if($('input[name="confirm"]', $form).attr('checked') === 'checked' && $('input[name="confirm2"]', $form).attr('checked') === 'checked') {
              url = '/photos/delete.json';
            } else {
              TBX.notification.show("Check the appropriate checkboxes so we know you're serious.", 'flash', 'error');
              OP.Util.fire('callback:remove-spinners');
              return; // don't continue
            }
          } else if(formParams[i].name === 'dateAdjust') {
            var dateAdjustedValue = formParams[i].value;
            for(var innerI=0; innerI<idsArr.length; innerI++) {
              op.data.store.Photos.get(idsArr[innerI])
              .set({dateTaken: dateAdjustedValue}, {silent: true})
              .save();
            }
            OP.Util.fire('callback:remove-spinners');
            // TODO gh-321 figure out how to handle errors
            return; // don't continue
          }

          params[formParams[i].name] = formParams[i].value;
        }
      }
      OP.Util.makeRequest(url, params, TBX.callbacks.batch.bind(params), 'json', 'post');
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
        TBX.notification.show('Nothing to upload.', 'flash', 'error');
        OP.Util.fire('callback:remove-spinners');
      }
    };

    // custom
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
    this.custom.tutorialUpdate = function() {
      var params = {section: this.section, key: this.key, crumb: TBX.crumb()};
      OP.Util.makeRequest('/plugin/Tutorial/update.json', params, TBX.callbacks.tutorialUpdate, 'json', 'post');
    };
    this.custom.uploaderBetaReady = function() {
      TBX.upload.init();
    };
  }
  
  TBX.handlers = new Handlers;
})(jQuery);
