(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};
  
  function Handlers() {
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
      var $el = $(ev.target), url = '/p/'+$el.attr('data-id'), router = op.data.store.Router;
      router.navigate(url, {trigger:true});
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
      var $els = $('.photo-grid .imageContainer .pin.edit'), batch = OP.Batch, preCount = batch.length(), addedCount;
      $els.each(TBX.callbacks.selectAll);
      addedCount = batch.length() - preCount;
      TBX.notification.show(addedCount + ' new photos were <em>added</em> to your <i class="icon-cogs"></i> batch queue.', 'flash', 'confirm');
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
          util.fetchAndCache(photos[i]);
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
          if(formParams[i].name === 'albumsAdd')
            url = '/album/'+formParams[i].value+'/photo/add.json';
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
