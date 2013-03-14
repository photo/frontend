var opTheme = (function() {
  var crumb, log, markup, pushstate, tags, pathname, util;

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

  log = function(msg) {
    if(typeof(console) !== 'undefined' && typeof(console.log) !== 'undefined')
      console.log(msg);
  };

  // markup helpers
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
  };

  // pushstate
  pushstate = (function() {
    var url = null;

    return {
      clickHandler: function(ev) { // anchorBinder
        var el = ev.currentTarget,
            url = $(el).attr('href');
        
        if(History.enabled && url.search('#') == -1 && (url.match(/^http/) === null || url.search(document.location.hostname) != -1)) {
          ev.preventDefault();
          get(url);
        }
      },
      get: function(url) { // fetch and render
        pushstate.url = url;
        $.get(pushstate.url, pushstate.store);
      },
      replace: function(url) { // update state without fetching or rendering
        var data = arguments[1] || {};
        data.type = 'replace';
        History.replaceState(data,'',url);
      },
      insert: function(url) { // update state and store in history but do not render
        var data = arguments[1] || {};
        data.type = 'insert';
        History.pushState(data,'',url);
      },
      parse: function(markup) {
        var dom = $(markup),
            bodyClass = $('body', dom).attr('class'),
            content = $('.content', dom).html();
        return {bodyClass: bodyClass, content: content};
      },
      render: function(result) {
        if(result.type === 'replace') {
          // for the moment replace is only when exiting the photo viewing modal
          $('.modal').modal('hide');
          return;
        }

        if(result.content === undefined) {
          //window.location.reload();
          //$('.modal').modal('hide');
        } else {
          //$('body').attr('class', result.bodyClass);
          var sel = (typeof(result.bodyClass) === 'undefined' || result.bodyClass === 'photo-details') ? '#modal-photo-detail' : '.content';
          $(sel).fadeTo('fast', .25, function() { $(this).html(result.content).fadeTo('fast', 1); });
        }
      },
      store: function() {
        var response = arguments[0] || {},
            data = pushstate.parse(response);
        data.type = 'store';
        History.pushState(data,'',pushstate.url);
      }
    };
  })();

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
      }
    };
  })();

  return {
    callback: {
      actionDelete: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            url = el.attr('href')+'.json',
            id = el.attr('data-id');
        OP.Util.makeRequest(url, el.parent().serializeArray(), function(response) {
          if(response.code === 204)
            $(".action-container-"+id).hide('medium', function(){ $(this).remove(); });
          else
            opTheme.message.error('Could not delete the photo.');
        });
        return false;
      },
      actionPost: function(ev) {
        ev.preventDefault();
        var form = $('#favorite-form'),
            url = form.attr('action')+'.json',
            params = form.serialize();
        $.ajax({
            url: url,
            type: 'POST',
            data: params,
            dataType: 'json',
            success: function(data) {
              if(data.code === 201)
                location.href = location.pathname + '?c=favorited';
              else
                location.href = location.pathname + '?e=unknown';
            }
          }
        );
      },
      albumDelete: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            url = el.attr('href')+'.json',
            form = el.parent(),
            params = {crumb:crumb.get()};

        OP.Util.makeRequest(url, params, function(response) {
          if(response.code === 204) {
            form.slideUp('medium', function() { this.remove(); });
          } else {
            opTheme.message.error('We could not delete this group.');
          }
        });
      },
      albumForm: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            url = el.attr('href') + '.json';
        OP.Util.makeRequest(url, {modal:'true',dynamic:'true'}, function(response){
          if(response.code === 200) {
            var el = $("#modal"),
                html = markup.modal(
                  'Create an album',
                  response.result.markup,
                  null
                );
            el.html(html).modal();  
            $('.typeahead', el).chosen();
          } else {
            opTheme.message.error('Could not load the form to create an album.');
          }
        }, 'json', 'get');
        return false;
      },
      albumPost: function(ev) {
        ev.preventDefault();
        var form = $(ev.target),
            url = form.attr('action')+'.json',
            isCreate = (url.search('create') > -1),
            isDynamic = parseInt($('input[name="dynamic"]', form).val()),
            params = {};

        params['name'] = $('input[name="name"]', form).val();
        params['visible'] = $('input[name="visible"]:checked', form).val();
        params['crumb'] = crumb.get();
        if(params['name'].length == 0) {
          opTheme.message.error('Please enter a name for your album.');
          return;
        }

        // TODO decide if this needs to be anonymous because of form
        OP.Util.makeRequest(url, params, function(response) {
          var form = form;
          if(response.code === 200 || response.code === 201) {
            if(isDynamic)
              opTheme.callback.albumPostDynamicCb(form, response.result);
            else if(isCreate)
              location.href = '/manage/albums?m=album-created&rand='+parseInt(Math.random()*100000)+'#album-' + response.result.id;
            else
              opTheme.message.confirm('Album updated successfully.');
          } else {
            opTheme.message.error('Could not update album.');
          }
        });
      },
      albumPostDynamicCb: function(form, album) {
        var select = $('select[name="albums"]', form);
        $('.modal').modal('hide');
        $('<option value="'+album.id+'" selected="selected">'+album.name+'</option>').prependTo(select);
        select.trigger("liszt:updated");
      },
      batchAdd: function(photo) {
        var el = $(".pin.photo-"+photo.id);
        el.addClass("revealed pinned");
        opTheme.ui.batchMessage();
        log("Adding photo " + photo.id);
      },
      batchClear: function() {
        $(".pin.pinned").removeClass("revealed pinned");
        opTheme.ui.batchMessage();
      },
      batchField: function(ev) {
        var el = $(ev.target),
            val = el.val(),
            tgt = $("form#batch-edit .form-fields");
        switch(val) {
          case 'albumsAdd':
            tgt.html(opTheme.ui.batchFormFields.albums());
            $('select', tgt).chosen();
            break;
          case 'delete':
            tgt.html(opTheme.ui.batchFormFields.empty());
            break;
          case 'groups':
            tgt.html(opTheme.ui.batchFormFields.groups());
            $('select', tgt).chosen();
            break;
          case 'permission':
            tgt.html(opTheme.ui.batchFormFields.permission());
            break;
          case 'tagsAdd':
          case 'tagsRemove':
            tgt.html(opTheme.ui.batchFormFields.tags());
            break;
        }
      },
      batchModal: function() {
        var el = $("#modal"),
            fieldMarkup = {},
            html = markup.modal(
              'Batch edit your pinned photos',
              '<form id="batch-edit">' +
              '  <div class="clearfix">' +
              '    <label>What would you like to do?</label>' +
              '    <div class="input">' +
              '      <select id="batch-key" class="batch-field-change" name="property">' +
              '        <option value="tagsAdd">Add Tags</option>' +
              '        <option value="tagsRemove">Remove Tags</option>' +
              '        <option value="albumsAdd">Add Albums</option>' +
              '        <option value="groups">Update Groups</option>' +
              '        <option value="permission">Update Permissions</option>' +
              '        <option value="delete">Delete</option>' +
              '      </select>' +
              '    </div>' +
              '  </div>' +
              '  <div class="form-fields">'+opTheme.ui.batchFormFields.tags()+'</div>' +
              '</form>',
              '<a href="#" class="btn photo-update-batch-click">Submit</a>'
            );
        el.html(html).modal();
      },
      batchRemove: function(id) {
        var el = $(".pin.photo-"+id);
        el.removeClass("pinned revealed");
        opTheme.ui.batchMessage();
        log("Removing photo " + id);
      },
      commentJump: function(ev) {
        ev.preventDefault();
        $.scrollTo($('div.comment-form'), 200);
        return false;
      },
      credentialView: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            url = el.attr('href')+'.json';
        OP.Util.makeRequest(url, {}, function(response) {
          if(response.code === 200) {
            var el = $("#modal"),
            html = markup.modal(
              response.result.name,
              '<div class="clearfix">' +
              '  <label>Consumer Key</label>' +
              '  <p>' + response.result.id + '</p>' + // Credential.php l. 125
              '  <label>Consumer Secret</label>' +
              '  <p>' + response.result.clientSecret + '</p>' + // Credential.php l. 137
              '  <label>Access Token</label>' +
              '  <p>' + response.result.userToken + '</p>' + // by elimination
              '  <label>Access Token Secret</label>' +
              '  <p>' + response.result.userSecret + '</p>' + // Credential.php l. 207
              '</div>',
              '<a href="#" class="btn" data-dismiss="modal">OK</a>'
            );
            el.html(html).modal();
          } else {
            opTheme.message.error('Could not load Application crendentials.');
          }
        }, 'json', 'get');
        return false;
      },
      credentialDelete: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            url = el.attr('href')+'.json',
            params = {crumb: crumb.get()};

        OP.Util.makeRequest(url, params, function(response) {
          if(response.code === 204) {
            el.parent().parent().slideUp('medium', function() { this.remove(); });
            opTheme.message.confirm('Application successfully deleted.');
          } else {
            opTheme.message.error('Could not delete Application.');
          }
        });
        return false;
      },
      featuresPost: function(ev) {
        ev.preventDefault();
        var form = $(ev.target),
            action = form.attr('action') + '.json',
            params = {};

        params = {'crumb':crumb.get()};
        params['allowDuplicate'] = $('input[name="allowDuplicate"]:checked', form).length;
        params['downloadOriginal'] = $('input[name="downloadOriginal"]:checked', form).length;
        params['hideFromSearchEngines'] = $('input[name="hideFromSearchEngines"]:checked', form).length;
        OP.Util.makeRequest(action, params, opTheme.callback.featuresPostCb);
      },
      featuresPostCb: function(response) {
        if(response.code === 200)
          opTheme.message.confirm('Your features were successfully saved.');
        else
          opTheme.message.error('We could not save your features.');
      },
      groupDelete: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            url = el.attr('href')+'.json',
            form = el.parent(),
            params = {crumb:crumb.get()};

        OP.Util.makeRequest(url, params, function(response) {
          if(response.code === 204) {
            form.slideUp('medium', function() { this.remove(); });
          } else {
            opTheme.message.error('We could not delete this group.');
          }
        });
      },
      groupEmailAdd: function(ev) {
        ev.preventDefault();
        var el = $(ev.target).prev(),
            tgt = $('ul.group-emails-add-list', el.parent()),
            val = el.val();
        if(val === '')
          return;

        $('<li><span class="group-email-queue">'+val+'</span> <a href="#" class="group-email-remove-click"><i class="group-email-remove-click icon-minus-sign"></i></a></li>').prependTo(tgt);
        el.val('');
      },
      groupEmailRemove: function(ev) {
        ev.preventDefault();
        var el = $(ev.target).parent().parent(),
            form = el.closest('form');
        el.remove();
        form.submit();
      },
      groupForm: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            url = el.attr('href') + '.json';
        OP.Util.makeRequest(url, {modal:'true',dynamic:'true'}, function(response){
          if(response.code === 200) {
            var el = $("#modal"),
                html = markup.modal(
                  'Create a group',
                  response.result.markup,
                  null
                );
            el.html(html).modal();  
          } else {
            opTheme.message.error('Could not load the form to create an album.');
          }
        }, 'json', 'get');
        return false;
      },
      groupPost: function(ev) {
        ev.preventDefault();
        var form = $(ev.target),
            url = form.attr('action')+'.json',
            isCreate = (url.search('create') > -1),
            isDynamic = $('input[name="dynamic"]', form).val() == "1",
            emails,
            params = {name: $('input[name="name"]', form).val()};

        params['crumb'] = crumb.get();
        $('.group-email-add-click', form).trigger('click');
        emails = [];
        $('span.group-email-queue', form).each(function(i, el) {
          emails.push($(el).html());
        });
        params.members = emails.join(',');

        // TODO decide if this needs to be anonymous because of isCreate
        OP.Util.makeRequest(url, params, function(response) {
          var form = form;
          if(response.code === 200 || response.code === 201) {
            if(isDynamic) {
              opTheme.callback.groupPostDynamicCb(form, response.result);
            } else if(isCreate) {
              window.location.href = '/manage/groups?m=group-created&rnd='+Math.random()+'#group-'+response.result.id;
            } else {
              opTheme.message.confirm('Group updated successfully.');
            }
          } else {
            opTheme.message.error('Could not update group.');
          }
        });
      },
      groupPostDynamicCb: function(form, group) {
        var select = $('select[name="groups"]', form);
        $('.modal').modal('hide');
        $('<option value="'+group.id+'" selected="selected">'+group.name+'</option>').prependTo(select);
        select.trigger("liszt:updated");
      },
      keyBrowseNext: function(ev) {
        if(ev.ctrlKey || ev.altKey || ev.metaKey)
          return;
        
        $("img.next-photo").click();
      },
      keyBrowsePrevious: function(ev) {
        if(ev.ctrlKey || ev.altKey || ev.metaKey)
          return;

        $("img.previous-photo").click();
      },
      login: function(ev) {
        ev.preventDefault();
        var el = $(ev.target);
        if(el.hasClass('browserid')) {
          navigator.id.getVerifiedEmail(function(assertion) {
              if (assertion) {
                opTheme.user.browserid.loginSuccess(assertion);
              } else {
                opTheme.user.browserid.loginFailure(assertion);
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
      loginModal: function(ev) {
        ev.preventDefault();
        $('#loginBox').modal();
      },
      loginOpenPhoto: function(ev) {
        ev.preventDefault();
        var form = $(ev.target),
            params = form.serialize();
        params += '&httpCodes=403';
        $.ajax(
          {
            url: '/user/openphoto/login.json',
            dataType:'json',
            data:params,
            type:'POST',
            success: opTheme.user.base.loginProcessed,
            error: opTheme.callback.loginOpenPhotoFailedCb,
            context: form
          }
        );
        return false;
      },
      loginOpenPhotoFailedCb: function(response) {
        var fields = $(this).find('.control-group');
        fields.each(function(i, el) {
          $(el).addClass('error');
        });
      },
      modalClose: function(ev) {
        ev.preventDefault();
        var el = $(ev.target).parent().parent();
        el.slideUp('fast', function() { $(this).remove(); });
      },
      modalShown: function(ev) {
        var inputs = $('input[type="text"]', ev.target);
        if(inputs.length > 0)
          inputs[0].focus();
      },
      modalUnload: function(ev) {
        ev.preventDefault();
        $('#modal-photo-detail').html('');
        pushstate.replace(pathname);
      },
      passwordRequest: function(ev) {
        ev.preventDefault();
        var form = $(ev.target).parent(),
            email = $('#login-email', form).val();
        OP.Util.makeRequest('/user/password/request.json', {email: email}, opTheme.callback.passwordRequestCb);
      },
      passwordRequestCb: function(response) {
        $('#loginBox').modal('hide');
        if(response.result) {
          opTheme.message.confirm('An email has been sent with a link to reset your password.');
        } else {
          opTheme.message.error('We were unable to send you a password request. Make sure you entered your email address correctly and that you are the owner of this site.');
        }
      },
      passwordReset: function(ev) {
        ev.preventDefault();
        var form = $(ev.target).parent(),
            params = form.serializeArray();
        if($('.input-password', form).val() != $('.input-password-confirm', form).val())
          opTheme.message.error('Your passwords did not match.');
        else
          OP.Util.makeRequest('/user/password/reset.json', params, opTheme.callback.passwordResetCb);
      },
      passwordResetCb: function(response) {
        if(response.result)
          opTheme.message.confirm('Your password was updated successfully. You can now log in to your site.');
        else
          opTheme.message.error('We were unable to update your password. Try requesting a new reset link.');
      },
      photoDelete: function(ev) {
      
        ev.preventDefault();
        var el = $(ev.target),
            url = el.parent().attr('action')+'.json';
      
        OP.Util.makeRequest(url, el.parent().serializeArray(), function(response) {
          if(response.code === 204) {
            el.html('This photo has been deleted');
            opTheme.message.confirm('This photo has been deleted.');
          } else {
            opTheme.message.error('Could not delete the photo.');
          }
        });
        return false;
      },
      photoEdit: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            id = el.attr('data-id'),
            url = '/photo/'+id+'/edit.json';
        OP.Util.makeRequest(url, {}, function(response){
          if(response.code === 200) {
            var el = $("#modal"),
                html = markup.modal(
                  'Edit this photo',
                  response.result.markup,
                  null
                );
            el.html(html).modal();  
            $('.typeahead', el).chosen();
          } else {
            opTheme.message.error('Could not load the form to edit this photo.');
          }
        }, 'json', 'get');
        return false;
      },
      photoView: function(ev) {
        ev.preventDefault();
        var el = $(ev.target).parent(),
            photoEl = $('.photo-view'),
            url = el.attr('href'),
            urlAjax = url;
        if($('body').hasClass('photo-details')) {
          //pushstate.get(url);
          location.href = url;
        } else {
          var modal = $('#modal-photo-detail'),
          photoContainer = $('#modal-photo-detail .photo-view');

          if(urlAjax.indexOf('?') === -1)
            urlAjax += '?modal=true';
          else
            urlAjax += '&modal=true';

          modal.load(urlAjax + ' .photo-view', function(response) {
            var nextPhoto = $('img.next-photo'), prevPhoto = $('img.previous-photo');
            pushstate.insert(url, pushstate.parse(response));
            util.fetchAndCacheNextPrevious();
            OP.Util.fire('photo:viewed', {url: location.href});
          });
        }
        return false;
      },
      photoViewModal: function(ev) {
        if(util.getDeviceWidth() < 900)
          return;

        ev.preventDefault();
        var el = $(ev.target).parent(),
            photoEl = $('.photo-view'),
            url = el.attr('href'),
            urlAjax = url,
            modalEl = $('#modal-photo-detail');

        if(urlAjax.indexOf('?') === -1)
          urlAjax += '?modal=true';
        else
          urlAjax += '&modal=true';
        // we call update the path without storing it
        // the callback from load() stores the response for navigation
        //pushstate.replace(url);
        location.hash=url;
        modalEl.load(urlAjax + ' .photo-view', opTheme.callback.photoViewModalCb).modal().on('hidden', opTheme.callback.modalUnload);
        return false;
      },
      photoViewModalCb: function(response) {
        util.fetchAndCacheNextPrevious();
        pushstate.insert(location.hash, pushstate.parse(response));
        OP.Util.fire('photo:viewed', {url: location.href});
      },
      photoUpdate: function(ev) {
        ev.preventDefault();
        var form = $(ev.target),
            action = form.attr('action') + '.json',
            params = form.serialize();
        if($('select[name^="groups"] option:checked', form).length === 0)
          params += '&groups=';
        if($('select[name^="albums"] option:checked', form).length === 0)
          params += '&albums=';
        OP.Util.makeRequest(action, params, opTheme.callback.photoUpdateCb, 'json', 'post');
      },
      photoUpdateBatch: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            key = $("#batch-key").val(),
            fields = $("form#batch-edit").find("*[name='value']"),
            value,
            params;

        el.html('Submitting...').attr("disabled", "disabled");
        if(fields.length == 1) {
          value = fields.val();
        } else {
          var checked = $("form#batch-edit").find("*[name='value']:checked");
          if(checked.length == 1) {
            value = checked.val();
          } else {
            value = [];
            checked.each(function(i, el) {
              value.push($(el).val());
            });
            value = value.join(',');
          }
        }

        params = {'crumb':crumb.get()};
        params[key] = value;
        params['ids'] = OP.Batch.collection.getIds().join(',');
        if(key !== 'delete') {
          OP.Util.makeRequest('/photos/update.json', params, opTheme.callback.photoUpdateBatchCb, 'json', 'post');
        } else {
          OP.Util.makeRequest('/photos/delete.json', params, opTheme.callback.photoUpdateBatchCb, 'json', 'post');
        }
      },
      photoUpdateBatchCb: function(response) {
        if(response.code == 200) {
          opTheme.message.append(markup.message('Your photos were successfully updated.', 'confirm'));
        } else if(response.code == 204) {
          OP.Batch.clear();
          opTheme.message.append(markup.message('Your photos were successfully deleted.', 'confirm'));
        } else {
          opTheme.message.append(markup.message('There was a problem updating your photos.', 'error'));
        }
        $("#modal").modal('hide');
      },
      photoUpdateCb: function(response) {
        if(response.code === 200) {
          opTheme.message.confirm('Your photo was successfully updated.');
        } else {
          opTheme.message.error('We could not update your photo.');
        }
        $("#modal").modal('hide');
      },
      photoUpload: function(ev) {
        ev.preventDefault();
        var form = $(ev.target),
            action = form.attr('action') + '.json',
            params = form.serialize();
      },
      photosViewMore: function(ev) {
        ev.preventDefault();
        opTheme.init.pages.photos.load();
      },
      pinClick: function(ev) {
        var el = $(ev.target),
            id = el.attr('data-id');
        // if the el has class="pinned" then remove, else add
        if(el.hasClass("pinned")) {
          OP.Batch.remove(id);
        } else {
          OP.Batch.add(id);
        }
      },
      pinClearClick: function(ev) {
        ev.preventDefault();
        OP.Batch.clear();
      },
      pinSelectAll: function(ev) {
        ev.preventDefault();
        $(".pin").each(function(index){
          id=$(this).attr('data-id');
          container=$(this).parent();
          if(!container.hasClass("pinned")) {
            OP.Batch.add(id);
          }
        });
      },
      pluginView: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            url = el.attr('href')+'.json',
            params = {crumb: crumb.get()},
            urlParts = url.match(/\/plugin\/(.*)\/view.json$/);
        OP.Util.makeRequest('/plugin/'+urlParts[1]+'/view.json', params, function(response){
          if(response.code === 200) {
            $("#modal").html(markup.modal(
                'Update ' + urlParts[1],
                response.result.markup,
                null
            )).modal();
          } else {
            opTheme.message.error('Unable to load this plugin for editing.');
          }
        }, 'json', 'get');
      },
      pluginStatusToggle: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            url = el.attr('href')+'.json',
            params = {crumb: crumb.get()};
        OP.Util.makeRequest(url, params, function(response){
          var a = $(el),
              div = a.parent(),
              container = div.parent();
          if(response.code === 200) {
            $('div', container).removeClass('hide');
            div.addClass('hide');
          } else {
            opTheme.message.error('Could not update the status of this plugin.');
          }
        }, 'json', 'post');
        return false;
      },
      pluginUpdate: function(ev) {
        ev.preventDefault();
        var form = $(ev.target),
            url = form.attr('action')+'.json';
        OP.Util.makeRequest(url, form.serializeArray(), function(response){
          if(response.code === 200) {
            opTheme.message.confirm('Your plugin was successfully updated.');
          } else {
            opTheme.message.error('Could not update the status of this plugin.');
          }
          $("#modal").modal('hide');
        }, 'json', 'post');
        return false;
      },
      preloadPhoto: function(obj) {
        OP.Util.makeRequest('/photo/'+obj.id+'/view.json', {returnSizes: obj.sizes, generate: 'true'}, opTheme.callback.preloadPhotoCb, 'json', 'get');
      },
      preloadPhotoCb: function(response) {
        var result = response.result, code = response.code;
        if(code !== 200)
          return;

        for(i in result) {
          if(result.hasOwnProperty(i) && /^path[0-9]/.test(i) === true) {
            util.fetchAndCache(result[i]);
          }
        }
      },
      popup: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            w = el.attr('data-width'),
            h = el.attr('data-height'),
            url = el.attr('href');
        window.open(url, 'openphoto', 'toolbar=0,menubar=0,location=1,directories=0,channelmode=0,width='+w+',height='+h);
      },
      searchByTags: function(ev) {
        ev.preventDefault();
        var form = $(ev.target),
          tags = $('select[name=tags]', form).val().join(','), // TODO do we need the nested jquery objects?
          url = form.attr('action');

        if(tags.length > 0) {
          if(url.search('/list') > 0) {
            location.href = url.replace('/list', '')+'/tags-'+tags+'/list';
          } else {
            location.href = url + '?tags=' + tags;
          }
        }
        return false;
      },
      settings: function(ev) {
        $("ul#settingsbar").slideToggle('medium');
        $("li#nav-signin").toggleClass('active');
        return false;
      },
      share: {
        facebook: function(ev) {
          ev.preventDefault();
          var el = $(ev.target),
              params = {};
          params.method = 'feed';
          params.display = 'popup';
          params.link = el.attr('data-link');
          params.picture = el.attr('data-picture');
          params.name = el.attr('data-name');
          params.description = el.attr('data-description');
          FB.ui(params, function() { window.close(); });
        }
      },
      tagsInitialized: function() {
        var tags = OP.Tag.getTags(),
            markup = '';
        if(tags !== null  && tags.length > 0) {
          for(i in tags)
            markup += '<option value="'+tags[i]+'">'+tags[i]+"</option>";  
        }
        $(".typeahead-tags").html(markup).chosen();
      },
      uploadCompleteSuccess: function(photoResponse) {
        photoResponse.crumb = crumb.get();
        $("form.upload").fadeOut('fast', function() {
          OP.Util.makeRequest('/photos/upload/confirm.json', photoResponse, opTheme.callback.uploadConfirm, 'json', 'post');
        });
      },
      uploadConfirm: function(response) {
        $("body.upload .upload-container").fadeOut('fast', function() { $(".upload-confirm").fadeIn('fast'); });
        $("body.upload .upload-confirm").html(response.result).show('fast');
      },
      uploaderReady: function() {
        var form = $('form.upload');
        if(typeof OPU === 'object')
          OPU.init();

        $("select.typeahead").chosen();
        //$('select.typeahead-tags').chosen({create_option:true,persistent_create_option:true})
      },
      webhookDelete: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            url = el.attr('href')+'.json';

        OP.Util.makeRequest(url, {}, function(response) {
          if(response.code === 204) {
            el.parent().remove();
            opTheme.message.confirm('Credential successfully deleted.');
          } else {
            opTheme.message.error('Could not delete credential.');
          }
        });
        return false;
      }
    }, // callback
    
    init: {
      load: function(_crumb) {
        // http://stackoverflow.com/a/6974186
        // http://stackoverflow.com/questions/6421769/popstate-on-pages-load-in-chrome/10651028#10651028
        var popped = ('state' in window.history && window.history.state !== null), initialURL = location.href;

        crumb.set(_crumb);
        OP.Tag.init();
        pathname = location.pathname;

        History.Adapter.bind(window,'statechange',function(){
          var State = History.getState(),
              initialPop = !popped && location.href == initialURL;
          popped = true;
          if(initialPop)
            return;

          pushstate.render(State.data);
        });

        $('.dropdown-toggle').dropdown();
        $('.modal').on('shown', opTheme.callback.modalShown);

        if(location.pathname === '/')
          opTheme.init.pages.front();
        else if(location.pathname === '/manage/photos')
          opTheme.init.pages.manage.photos();
        else if(location.pathname.search(/^\/photos(.*)\/list/) === 0)
          opTheme.init.pages.photos.init();
        else if(location.pathname.search(/^\/p\/[a-z0-9]+/) === 0 || location.pathname.search(/^\/photo\/[a-z0-9]+\/?(.*)\/view/) === 0)
          opTheme.init.pages.photo.init();
        else if(location.pathname === '/photos/upload')
          opTheme.init.pages.upload();
      },
      attach: function() {
        OP.Util.on('click:action-delete', opTheme.callback.actionDelete);
        OP.Util.on('click:action-jump', opTheme.callback.commentJump);
        OP.Util.on('click:action-post', opTheme.callback.actionPost);
        OP.Util.on('click:album-delete', opTheme.callback.albumDelete);
        OP.Util.on('click:album-form', opTheme.callback.albumForm);
        OP.Util.on('click:batch-modal', opTheme.callback.batchModal);
        OP.Util.on('click:credential-view', opTheme.callback.credentialView);
        OP.Util.on('click:credential-delete', opTheme.callback.credentialDelete);
        OP.Util.on('click:group-delete', opTheme.callback.groupDelete);
        OP.Util.on('click:group-email-add', opTheme.callback.groupEmailAdd);
        OP.Util.on('click:group-email-remove', opTheme.callback.groupEmailRemove);
        OP.Util.on('click:group-form', opTheme.callback.groupForm);
        OP.Util.on('click:login', opTheme.callback.login);
        OP.Util.on('click:login-modal', opTheme.callback.loginModal);
        OP.Util.on('click:manage-password-request', opTheme.callback.passwordRequest);
        OP.Util.on('click:manage-password-reset', opTheme.callback.passwordReset);
        OP.Util.on('click:modal-close', opTheme.callback.modalClose);
        OP.Util.on('click:nav-item', opTheme.callback.searchBarToggle);
        OP.Util.on('click:photo-delete', opTheme.callback.photoDelete);
        OP.Util.on('click:photo-edit', opTheme.callback.photoEdit);
        OP.Util.on('click:photo-update-batch', opTheme.callback.photoUpdateBatch);
        OP.Util.on('click:photo-view', opTheme.callback.photoView);
        OP.Util.on('click:photo-view-modal', opTheme.callback.photoViewModal);
        OP.Util.on('click:photos-load-more', opTheme.callback.photosViewMore);
        OP.Util.on('click:plugin-view', opTheme.callback.pluginView);
        OP.Util.on('click:plugin-status-toggle', opTheme.callback.pluginStatusToggle);
        OP.Util.on('click:pin', opTheme.callback.pinClick);
        OP.Util.on('click:pin-clear', opTheme.callback.pinClearClick);
        OP.Util.on('click:pin-select-all', opTheme.callback.pinSelectAll);
        OP.Util.on('click:popup', opTheme.callback.popup);
        OP.Util.on('click:settings', opTheme.callback.settings);
        OP.Util.on('click:share-facebook', opTheme.callback.share.facebook);
        OP.Util.on('click:webhook-delete', opTheme.callback.webhookDelete);

        OP.Util.on('submit:album-post', opTheme.callback.albumPost);
        OP.Util.on('submit:features-post', opTheme.callback.featuresPost);
        OP.Util.on('submit:group-post', opTheme.callback.groupPost);
        OP.Util.on('submit:login-openphoto', opTheme.callback.loginOpenPhoto);
        OP.Util.on('submit:photo-update', opTheme.callback.photoUpdate);
        OP.Util.on('submit:plugin-update', opTheme.callback.pluginUpdate);
        // in openphoto-upload.js
        // OP.Util.on('submit:photo-upload', opTheme.callback.photoUpload);
        OP.Util.on('submit:search', opTheme.callback.searchByTags);

        OP.Util.on('keydown:browse-next', opTheme.callback.keyBrowseNext);
        OP.Util.on('keydown:browse-previous', opTheme.callback.keyBrowsePrevious);

        OP.Util.on('change:batch-field', opTheme.callback.batchField);

        OP.Util.on('callback:batch-add', opTheme.callback.batchAdd);
        OP.Util.on('callback:batch-remove', opTheme.callback.batchRemove);
        OP.Util.on('callback:batch-clear', opTheme.callback.batchClear);
        OP.Util.on('callback:tags-initialized', opTheme.callback.tagsInitialized);


        OP.Util.on('upload:complete-success', opTheme.callback.uploadCompleteSuccess);
        OP.Util.on('upload:complete-failure', opTheme.callback.uploadCompleteFailure);
        OP.Util.on('upload:uploader-ready', opTheme.callback.uploaderReady);

        OP.Util.on('tags:autocomplete', opTheme.callback.tagsAutocomplete);

        OP.Util.on('preload:photo', opTheme.callback.preloadPhoto);
      },
      pages: {
        front: function() {
          var swipeLeft = function(event, direction) {
            if(typeof newBG === "undefined" || newBG === "undefined") {
              $('.carousel').carousel('next');
            }
          };
          var swipeRight = function (event, direction) {
            if(typeof newBG === "undefined" || newBG === "undefined") {
              $('.carousel').carousel('prev');
            }
          };
          var swipeOptions = {
            swipeLeft:swipeLeft,
            swipeRight:swipeRight,
            threshold:100
          };

          $('.carousel.front').swipe(swipeOptions);
          $('.carousel.front').carousel({interval: 7000});
          $('.carousel.front').on('slid', function() {
            var el = $('.carousel.front .active'),
                ind = parseInt(el.attr('data-index'))+1;
            $('.carouselthumbs .active').removeClass('active');
            $('.carouselthumbs li:nth-child('+ind+') a').addClass('active');
          });
          $('.carousel.feed').carousel();
          $('.carousel.feed').carousel('pause');
        },
        manage: {
          photos: function() {
            var ids = OP.Batch.collection.getAll(),
                idsLength = OP.Batch.collection.getLength(),
                els = $(".pin"),
                cls,
                el,
                parts;

            if(idsLength > 0)
              opTheme.ui.batchMessage();

            els.each(function(i, el) {
              el = $(el);
              cls = el.attr('class');
              parts = cls.match(/ photo-([a-z0-9]+)/);
              if(parts.length == 2) {
                if(ids[parts[1]] !== undefined)
                  el.addClass("revealed pinned");
              }
            });
          }
        },
        photo: {
          init: function() { 
            util.fetchAndCacheNextPrevious(); 
            OP.Util.fire('photo:viewed', {url: location.href});
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
            var _this = opTheme.init.pages.photos;
            $(window).scroll(_this.scrollCb);
            _this.load();
          },
          scrollCb: function(){
            var _this = opTheme.init.pages.photos;
            // don't autoload if the width is narrow
            //  crude way to check if we're on a mobile device
            //  See #778
            if(util.getDeviceWidth() < 900)
              return;

            if($(window).scrollTop() > $(document).height() - $(window).height() - 200){
              _this.load();
            }
          },
          load: function() {
            var _this = opTheme.init.pages.photos; loc = location;
            // we define initData at runtime to avoid having to make an HTTP call on load
            // all subsequent calls run through the http API
            if(typeof(_this.initData) === "undefined") {
              if(_this.end || _this.running)
                return;

              _this.running = true;

              if(_this.page === null) {
                var qsMatch = loc.href.match('page=([0-9]+)');
                if(qsMatch !== null) {
                  _this.page = qsMatch[1];
                } else {
                  var uriMatch = loc.pathname.match(/\/page-([0-9]+)/);
                  if(uriMatch !== null) {
                    _this.page = uriMatch[1];
                  }
                }

                if(_this.page === null)
                  _this.page = 1;
              }

              var api = _this.pageLocation.pathname+'.json';
                  params = {}, qs = _this.pageLocation.search.replace('?', '');
              
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
              params.page = _this.page;

              // for mobile devices limit the number pages before a full page refresh. See #778
              if(_this.pageCount > _this.maxMobilePageCount && util.getDeviceWidth() < 900) {
                location.href = _this.pageLocation.pathname + '?' + decodeURIComponent($.param(params));
              } else {
                $.getJSON(api, params, _this.loadCb);
              }
            } else {
              delete _this.initData;
              _this.page = 1;
              var response = {code:200, result:initData};
              _this.loadCb(response);
            }
          },
          loadCb: function(response) {
            var items = response.result, _this = opTheme.init.pages.photos, infobar = $('.infobar'),
                minDate = $('.startdate', infobar), maxDate = $('.enddate', infobar),
                minDateVal = parseInt(minDate.attr('data-time')), maxDateVal = parseInt(maxDate.attr('data-time')),
                ui = opTheme.ui, i;
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

              Gallery.showImages($(".photo-grid-justify"), items);
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
          OP.Util.fire('upload:uploader-ready');
        }
      }
    }, // init
    
    message: {
      append: function(html/*, isStatic*/) {
        var el = $(".message:first").clone(false),
            last = $(".message:last"),
            isStatic = arguments[1] || false;

        // TODO differentiate on type #962
        el.addClass('alert alert-info').html(html);
        last.after(el).slideDown();
        if(!isStatic)
          el.delay(5000).slideUp(function(){ $(this).remove(); });
      },
      confirm: function(messageHtml/*, isStatic*/) {
        var isStatic = arguments[1] || false;
        opTheme.message.show(messageHtml, 'confirm', isStatic);
      },
      error: function(messageHtml/*, isStatic*/) {
        var isStatic = arguments[1] || false;
        opTheme.message.show(messageHtml, 'error', isStatic);
      },
      show: function(messageHtml, type/*, isStatic*/) {
        var isStatic = arguments[2] || false;
        opTheme.message.append(markup.message(messageHtml, type), isStatic);
      }
    }, // message
    ui: {
      batchFormFields: {
        albums: function() {
          var albumsArr, album, html = '';
          $.ajax({
            url: '/albums/list.json',
            data: {pageSize: '0'},
            async: false,
            success: function(response) {
              if(response.code === 200) {
                html += '<label>Select albums</label><select name="value" multiple>';
                for(i in response.result) {
                  if(response.result.hasOwnProperty(i)) {
                    album = response.result[i];
                    html += '<option value="' + album.id + '">' + album.name + '</option>';
                  }
                }
                html += '</select>';
              }
            }
          });
          return html;
        },
        empty: function() {
          return '';
        },
        groups: function() {
          var groupsArr, group, html = '';
          $.ajax({
            url: '/groups/list.json',
            async: false,
            success: function(response) {
              if(response.code === 200) {
                html += '<label>Select groups</label><select name="value" multiple>';
                for(i in response.result) {
                  if(response.result.hasOwnProperty(i)) {
                    group = response.result[i];
                    html += '<option value="' + group.id + '">' + group.name + '</option>';
                  }
                }
                html += '</select>';
              }
            }
          });
          return html;
        },
        permission: function() {
          return '  <label class="radio">' +
                 '    <input type="radio" name="value" value="1" checked="checked">' +
                 '    Public' +
                 '  </label>' +
                 '  <label class="radio">' +
                 '    <input type="radio" name="value" value="0"> ' +
                 '    Private' +
                 '  </label>';
        },
        tags: function() {
          return '  <label>Tags</label>' +
                 '  <input type="text" name="value" class="tags-autocomplete" placeholder="A comma separated list of tags" value="">';
        }
      },
      batchMessage: function() {
        var idsLength = OP.Batch.collection.getLength();
        if($("#batch-message").length > 0 && idsLength > 0) {
          $("#batch-count").html(idsLength);
          return;
        } else if(idsLength == 0) {
          $("#batch-message").parent().parent().slideUp('fast', function() { $(this).html('').removeClass(); });
          return;
        } else {
          opTheme.message.append(
            '<div class="batch-message-container">' +
              markup.message(
                '  <a id="batch-message"></a>You have <span id="batch-count">'+idsLength+'</span> photos pinned.' +
                '  <div><a class="btn small info batch-modal-click" data-controls-modal="modal" data-backdrop="static">Batch edit</a>&nbsp;<a href="#" class="btn small pin-clear-click">Or clear pins</a></div>'
              ) +
            '</div>'
          , true);
        }
      },
      fadeAndSet: function(el, html) {
        var _this = $(el);
        _this.fadeOut('fast', function() { _this.html(html).fadeIn('fast'); });
      }
    }, // ui
    user: {
      base: {
        loginProcessed: function(response) {
          if(response.code != 200) {
            log('processing of login failed');
            // TODO do something here to handle failed login
            return;
          }

          log('login processing succeeded');
          window.location.reload();
        }
      },
      browserid: {
        loginFailure: function(assertion) {
          log('login failed');
          // TODO something here to handle failed login
        },
        loginSuccess: function(assertion) {
          var params = {assertion: assertion};
          OP.Util.makeRequest('/user/browserid/login.json', params, opTheme.user.base.loginProcessed);
        }
      }
    } // user
  };
}());
