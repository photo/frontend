(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};
  
  function Callbacks() {
    this.albumCreate = function(response) {
      var result = response.result;
      if(response.code === 201 || response.code === 409) {
        // if we're on the upload form then we insert into the selection list
        var $sel = $('form.upload select[name="albums"]');
        if($sel.length === 1) {
          $sel.append($('<option>', {value: result.id, text: result.name, selected: 'selected'}));
          if(response.code === 201) {
            TBX.notification.show('Your album was created and we\'ve gone ahead and selected it for you.', 'flash', 'confirm');
          } else {
            TBX.notification.show('We found an album with that name so we\'ve gone ahead and selected it for you.', 'flash', 'confirm');
          }
        } else {
          if(response.code === 201) {
            TBX.notification.show('Your album was created. You can <a href="/photos/upload?album='+result.id+'"><i class="icon-upload"></i> upload photos</a> or <a href="#" class="albumInviteUploaders" data-id="'+result.id+'"><i class="icon-exchange"></i> invite others</a> to upload their photos.', 'flash', 'confirm');
          } else {
            TBX.notification.show('We found an album with that name so we didn\'t create another.  You can <a href="/photos/upload?album='+result.id+'"><i class="icon-upload"></i> upload photos</a> or <a href="#" class="albumInviteUploaders" data-id="'+result.id+'"><i class="icon-exchange"></i> invite others</a> to upload their photos.', 'flash', 'confirm');
          }
        }
      } else {
        TBX.notification.show('Sorry, an error occured when trying to create your album.', 'flash', 'error');
      }
      $('.batchHide').trigger('click');
    };
    this.batch = function(response) { // this is the form params
      var id, model, ids = this.ids.split(','), photoCount = ids.length, store = op.data.store.Photos, action = response.code === 204 ? 'deleted' : 'updated';
      if(response.code === 200 || response.code === 204) {
        for(i in ids) {
          if(ids.hasOwnProperty(i)) {
            id = ids[i];
            model = store.get(id);
            if(model) {
              // on update we fetch, on delete we destroy
              if(response.code === 200) {
                model.fetch();
              } else {
                OP.Util.fire('callback:photo-destroy', model);
              }
            }
          }
        }

        // deleted, let's clear the batch queue
        if(response.code === 204) {
          var delIds = _.intersection(ids, OP.Batch.ids());
          for(i in delIds) {
            OP.Batch.remove(OP.Batch.getKey(delIds[i]));
          }
        }

        TBX.notification.show(photoCount + ' photo ' + (photoCount>1?'s were':'was') + ' ' + action + '.', 'flash', 'confirm');
      } else {
        TBX.notification.show('Sorry, an error occured when trying to update your photos.', 'flash', 'error');
      }
      $('.batchHide').trigger('click');
    };
    this.credentialDelete = function(response) {
      if(response.code === 204) {
        this.closest('tr').slideUp('medium');
        TBX.notification.show('Your app was successfully deleted.', null, 'confirm');
      } else {
        TBX.notification.show('There was a problem deleting your app.', null, 'error');
      }
    };
    this.credentialView = function(response) {
      if(response.code === 200) {
        $('.secondary-flyout').html(response.result.markup).slideDown();
      } else {
        TBX.notification.show('There was a problem retrieving your app.', null, 'error');
      }
    };
    this.loginSuccess = function() {
      var redirect = $('input[name="r"]', this).val();
      window.location.href = redirect;
    };
    this.personaSuccess = function(assertion) {
      var params = {assertion: assertion};
      OP.Util.makeRequest('/user/browserid/login.json', params, TBX.callbacks.loginProcessed);
    };
    this.loginProcessed = function(response) {
      if(response.code != 200) {
        TBX.notification.show('Sorry, we could not log you in.', 'flash', 'error');
        return;
      }
      
      var url = $('input[name="r"]', $('form.login')).val();
      location.href = url;
    };
    this.passwordReset = function(response) {
      var $button = this;
      if(response.code !== 200) {
        TBX.notification.show('We could not update your password.', 'flash', 'error');
        OP.Util.fire('callback:replace-spinner', {button: $button, icon:'icon-warning-sign'});
        return;
      }
      location.href = '/';
    };
    this.personaSuccess = function(assertion) {
      var params = {assertion: assertion};
      OP.Util.makeRequest('/user/browserid/login.json', params, TBX.callbacks.loginProcessed);
    };
    this.photoNext = function(ev) {
      if(!op.Lightbox.getInstance().isOpen())
        $('.pagination .arrow-next').click();
    };
    this.photoPrevious = function(ev) {
      if(!op.Lightbox.getInstance().isOpen())
        $('.pagination .arrow-prev').click();
    };
    this.pluginStatusToggle = function(response) {
      var a = $(this),
          div = a.parent(),
          container = div.parent();
      if(response.code === 200) {
        $('div', container).removeClass('hide');
        div.addClass('hide');
      } else {
        TBX.notification.show('Could not update the status of this plugin.', 'flash', 'error');
      }
    };
    this.pluginUpdate = function(response) {
      if(response.code === 200) {
        TBX.notification.show('Your plugin was successfully updated.', 'flash', 'confirm');
        $('.batchHide').trigger('click');
      } else {
        TBX.notification.show('Could not update the status of this plugin.', 'flash', 'error');
      }
      $("#modal").modal('hide');
    };
    this.pluginView = function(response) {
      if(response.code === 200) {
        $(".secondary-flyout").html(response.result.markup).fadeIn();
      } else {
        opTheme.message.error('Unable to load this plugin for editing.');
      }
    };
    this.profilesSuccess = function(owner, viewer, profiles) {
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
    };
    this.removeSpinners = function() {
      var $icons = $('button i.icon-spinner');
      $icons.each(function(i, el) { $(el).remove(); });
    };
    this.replaceSpinner = function(args) {
      var $icon = $('i.icon-spinner', args.button), cls = 'icon-ok';
      if(typeof(args['icon']) !== 'undefined')
        cls = args.icon;

      $icon.removeClass('icon-spinner icon-spin');
      $icon.addClass(cls);
    };
    this.rotate = function(response) {
      var model = this.model, id = this.id, size = this.size, code = response.code, src = response.result['path'+size], $img = $('img.photo-img-'+id);
      model.fetch();
      if(response.code === 200) {
        $img.fadeOut('fast', function() { $img.attr('src', src).fadeIn('fast'); });
      }
    };
    this.selectAll = function(i, el) {
      var id = $(el).attr('data-id'), photo = op.data.store.Photos.get(id).toJSON();
      OP.Batch.add(id, photo);
    };
    this.setAlbumCover = function(response) {
      if(response.code === 200) {
        TBX.notification.show('Your album cover was updated successfully.', 'flash', 'confirm');
        return;
      }
      TBX.notification.show('There was a problem updating your album cover.', 'flash', 'error');
    };
    this.share = function(response) {
      var result = response.result;
      if(response.code !== 200) {
        TBX.notification.show('There was a problem generating your sharing token.', 'flash', 'error');
        return;
      }

      $('.secondary-flyout').html(result.markup).slideDown('fast');
    };
    this.shareEmailSuccess = function(response) {
      var result = response.result;
      $('a.batchHide').trigger('click');
      TBX.notification.show('Your photo was successfully emailed.', 'flash', 'confirm');
    };
    this.showKeyboardShortcuts = function(ev) {
      if(!ev.shiftKey)
        return;
      var markup = $('script#keyboard-shortcuts').html();
      $('.secondary-flyout').html(markup).slideDown('fast');
    };
    this.tokenDelete = function(response) {
      if(response.code === 204) {
        this.closest('tr').slideUp('medium');
        TBX.notification.show('Your sharing token was successfully deleted.', null, 'confirm');
      } else {
        TBX.notification.show('There was a problem deleting your sharing token.', null, 'error');
      }
    };
    this.tutorialUpdate = function(response) {
      $('.navbar-inner-secondary ul li.info').fadeOut();
    };
    this.upload = function(ev) {
      ev.preventDefault();
      var uploader = $("#uploader").pluploadQueue();
      if (typeof(uploader.files) != 'undefined' && uploader.files.length > 0) {
        uploader.start();
      } else {
        // TODO something that doesn't suck
        //opTheme.message.error('Please select at least one photo to upload.');
      }
    };
    this.uploadCompleteSuccess = function(photoResponse) {
      photoResponse.crumb = TBX.crumb();
      $("form.upload").fadeOut('fast', function() {
        OP.Util.makeRequest('/photos/upload/confirm.json', photoResponse, TBX.callbacks.uploadConfirm, 'json', 'post');
      });
    };
    this.uploadConfirm = function(response) {
      var $el, container, model, view, item, result = response.result, success = result.data.successPhotos;
      $(".upload-container").fadeOut('fast', function() { $(".upload-confirm").fadeIn('fast'); });
      $(".upload-confirm").html(result.tpl).show('fast', function(){
        if(success.length > 0) {
          var batchModel = TBX.init.pages.photos.batchModel, $batchEl = $('.batch-meta');
          $('body').addClass('upload-confirm');
          (new op.data.view.BatchIndicator({model:batchModel, el: $batchEl})).render();
          op.data.store.Photos.add(success);
          container = $('.upload-preview.success');
          Gallery.showImages(container, success);
        }
      });
    };
    this.uploadSendNotification = function(args) {
      OP.Util.makeRequest(TBX.format.sprintf('/photos/upload/%s/notify.json', args.token), {uploader:args.by, count:(args.photosUploaded.success.length+args.photosUploaded.duplicate.length)}, function(){}, 'json', 'post');
    };
    this.uploaderReady = function() {
      var form = $('form.upload');
      if(typeof OPU === 'object')
        OPU.init();
    };
  }
  
  TBX.callbacks = new Callbacks;
})(jQuery);
