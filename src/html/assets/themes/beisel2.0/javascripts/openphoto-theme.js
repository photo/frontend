var opTheme = (function() {
  var crumb, log, markup, pushstate;

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
    if(console !== undefined && console.log !== undefined)
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
             '<div class="modal-footer">' + footer + '</div>';
    }
  };

  // pushstate
  pushstate = (function() {
    var url = null,
        parse;

    parse = function(markup) {
      var dom = $(markup),
          bodyClass = $('body', dom).attr('class'),
          content = $('.content', dom).html();
      return {bodyClass: bodyClass, content: content};
    };

    return {
      clickHandler: function(ev) { // anchorBinder
        var el = ev.currentTarget,
            url = $(el).attr('href');
        
        if(History.enabled && url.search('#') == -1 && (url.match(/^http/) === null || url.search(document.location.hostname) != -1)) {
          ev.preventDefault();
          get(url);
        }
      },
      get: function(url) {
        pushstate.url = url;
        $.get(pushstate.url, pushstate.store);
      },
      render: function(result) {
        if(result.content === undefined) {
          window.location.reload();
        } else {
          $('body').attr('class', result.bodyClass);
          $('.content').fadeTo('fast', .25, function() { $(this).html(result.content).fadeTo('fast', 1); });
        }
      },
      store: function(response) {
        History.pushState(parse(response),'',pushstate.url);
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
          case 'delete':
            tgt.html(opTheme.ui.batchFormFields.empty());
            break;
          case 'groups':
            tgt.html(opTheme.ui.batchFormFields.groups());
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
              '    <label>Property</label>' +
              '    <div class="input">' +
              '      <select id="batch-key" class="batch-field-change" name="property">' +
              '        <option value="tagsAdd">Add Tags</option>' +
              '        <option value="tagsRemove">Remove Tags</option>' +
              '        <option value="groups">Groups</option>' +
              '        <option value="permission">Permission</option>' +
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
      credentailDelete: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            url = el.attr('href')+'.json';

        OP.Util.makeRequest(url, {}, function(response) {
          if(response.code === 204) {
            el.parent().parent().slideUp('medium', function() { this.remove(); });
            opTheme.message.confirm('Application successfully deleted.');
          } else {
            opTheme.message.error('Could not delete Application.');
          }
        });
        return false;
      },
      groupCheckbox: function(ev) {
        var el = $(ev.target);
        if(el.hasClass("none") && el.is(":checked")) {
          $("input.group-checkbox:not(.none)").removeAttr("checked");
        } else if(el.is(":checked")) {
          $("input.group-checkbox.none").removeAttr("checked");
        }
      },
      groupDelete: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            url = el.attr('href')+'.json',
            form = el.parent();

        OP.Util.makeRequest(url, function(response) {
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
            tgt = $('ul.group-emails-add-list');
        $('<li><span class="group-email-queue">'+el.val()+'</span> <a href="#" class="group-email-remove-click"><i class="group-email-remove-click icon-minus-sign"></i></a></li>').prependTo(tgt);
        el.val('');
      },
      groupEmailRemove: function(ev) {
        ev.preventDefault();
        var el = $(ev.target).parent().parent();
        el.remove();
      },
      groupPost: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            form = el.parent(),
            url = form.attr('action')+'.json',
            isCreate = (url.search('create') > -1),
            emails,
            params = {name: $('input[name="name"]', form).val()};
        emails = [];
        $('span.group-email-queue', form).each(function(i, el) {
          emails.push($(el).html());
        });
        params.members = emails.join(',');

        OP.Util.makeRequest(url, params, function(response) {
          if(response.code === 200 || response.code === 201) {
            if(isCreate)
              location.href = '/manage/groups?m=group-created';
            else
              opTheme.message.confirm('Group updated successfully.');
          } else {
            opTheme.message.error('Could not update group.');
          }
        });
        return false;
      },
      keyBrowseNext: function(ev) {
        if(ev.ctrlKey || ev.altKey || ev.metaKey)
          return;
        
        $(".next-photo").click();
      },
      keyBrowsePrevious: function(ev) {
        if(ev.ctrlKey || ev.altKey || ev.metaKey)
          return;

        $(".previous-photo").click();
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
      modalClose: function(ev) {
        ev.preventDefault();
        var el = $(ev.target).parent().parent();
        el.slideUp('fast', function() { $(this).remove(); });
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
                  '<a href="#" class="btn photo-update-click">Save</a>'
                );
            el.html(html).modal();  
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
            url = el.attr('href');
        if($('body').hasClass('photo-details')) {
          pushstate.get(url);
        } else {
          var modal = $('#modal-photo-detail'),
              photoContainer = $('#modal-photo-detail .photo-view');
          photoContainer.fadeTo('fast', .25, function() {
            modal.load(url + ' .photo-view', function() {
              photoContainer.fadeTo('fast', 1, function() {
                modal.scrollTo(this);
              });
            });
          });
        }
        return false;
      },
      photoViewModal: function(ev) {
        ev.preventDefault();
        var el = $(ev.target).parent(),
            photoEl = $('.photo-view'),
            url = el.attr('href');
        $('#modal-photo-detail').load(url + ' .photo-view').modal();
        return false;
      },
      photoUpdate: function() {
        var form = $("#photo-edit-form"),
            action = form.attr('action') + '.json',
            params = form.serialize();
        OP.Util.makeRequest(action, params, opTheme.callback.photoUpdateCb, 'json', 'post');
      },
      photoUpdateBatch: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            key = $("#batch-key").val(),
            fields = $("form#batch-edit").find("*[name='value']"),
            value;

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
      pluginStatus: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            url = el.attr('href')+'.json';
        OP.Util.makeRequest(url, {}, function(response){
          if(response.code === 200)
            window.location.reload();
          else
            opTheme.message.error('Could not update the status of this plugin.');
        }, 'json', 'post');
        return false;
      },
      pluginUpdate: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            form = el.parent(),
            url = form.attr('action')+'.json';
        OP.Util.makeRequest(url, form.serializeArray(), function(response){
          if(response.code === 200)
            opTheme.message.confirm('Your plugin was successfully updated.');
          else
            opTheme.message.error('Could not update the status of this plugin.');
        }, 'json', 'post');
        return false;
      },
      searchByTags: function(ev) {
        ev.preventDefault();
        var form = $(ev.target).parent().parent(),
          tags = $($('input[name=tags]', form)[0]).val(),
          url = form.attr('action');

        if(tags.length > 0) {
          if(url.search('/list') > 0) {
            location.href = url.replace('/list', '')+'/tags-'+tags+'/list';
          } else {
            form.submit();
          }
        }
        return false;
      },
      settings: function(ev) {
        $("ul#settingsbar").slideToggle('medium');
        $("li#nav-signin").toggleClass('active');
        return false;
      },
      uploadCompleteSuccess: function() {
        $("form.upload").fadeOut('fast', function() {
          $(".upload-progress").fadeOut('fast', function() { $(".upload-complete").fadeIn('fast'); });
          $(".upload-share").fadeIn('fast');
        });
      },
      uploadCompleteFailure: function() {
        $("form.upload").fadeOut('fast', function() {
          $(".upload-progress").fadeOut('fast', function() { $(".upload-warning .failed").html(failed); $(".upload-warning .total").html(total); $(".upload-warning").fadeIn('fast'); });
        });
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
        crumb.set(_crumb);

        $('.dropdown-toggle').dropdown();

        if(location.pathname === '/')
          opTheme.init.pages.front();
        else if(location.pathname === '/manage')
          opTheme.init.pages.manage();
        else if(location.pathname.search(/^\/photos(.*)\/list/) === 0)
          opTheme.init.pages.photos.load();
      },
      attach: function() {
        OP.Util.on('click:action-delete', opTheme.callback.actionDelete);
        OP.Util.on('click:action-jump', opTheme.callback.commentJump);
        OP.Util.on('click:batch-modal', opTheme.callback.batchModal);
        OP.Util.on('click:credential-delete', opTheme.callback.credentailDelete);
        OP.Util.on('click:group-checkbox', opTheme.callback.groupCheckbox);
        OP.Util.on('click:group-delete', opTheme.callback.groupDelete);
        OP.Util.on('click:group-email-add', opTheme.callback.groupEmailAdd);
        OP.Util.on('click:group-email-remove', opTheme.callback.groupEmailRemove);
        OP.Util.on('click:group-post', opTheme.callback.groupPost);
        OP.Util.on('click:login', opTheme.callback.login);
        OP.Util.on('click:login-modal', opTheme.callback.loginModal);
        OP.Util.on('click:modal-close', opTheme.callback.modalClose);
        OP.Util.on('click:nav-item', opTheme.callback.searchBarToggle);
        OP.Util.on('click:photo-delete', opTheme.callback.photoDelete);
        OP.Util.on('click:photo-edit', opTheme.callback.photoEdit);
        OP.Util.on('click:plugin-status', opTheme.callback.pluginStatus);
        OP.Util.on('click:plugin-update', opTheme.callback.pluginUpdate);
        OP.Util.on('click:photo-update', opTheme.callback.photoUpdate);
        OP.Util.on('click:photo-update-batch', opTheme.callback.photoUpdateBatch);
        OP.Util.on('click:photo-view', opTheme.callback.photoView);
        OP.Util.on('click:photo-view-modal', opTheme.callback.photoViewModal);
        OP.Util.on('click:pin', opTheme.callback.pinClick);
        OP.Util.on('click:pin-clear', opTheme.callback.pinClearClick);
        OP.Util.on('click:search', opTheme.callback.searchByTags);
        OP.Util.on('click:settings', opTheme.callback.settings);
        OP.Util.on('click:webhook-delete', opTheme.callback.webhookDelete);
        OP.Util.on('keydown:browse-next', opTheme.callback.keyBrowseNext);
        OP.Util.on('keydown:browse-previous', opTheme.callback.keyBrowsePrevious);
        OP.Util.on('change:batch-field', opTheme.callback.batchField);

        OP.Util.on('callback:batch-add', opTheme.callback.batchAdd);
        OP.Util.on('callback:batch-remove', opTheme.callback.batchRemove);
        OP.Util.on('callback:batch-clear', opTheme.callback.batchClear);

        OP.Util.on('upload:complete-success', opTheme.callback.uploadCompleteSuccess);
        OP.Util.on('upload:complete-failure', opTheme.callback.uploadCompleteFailure);

        History.Adapter.bind(window,'statechange',function(){
          var State = History.getState();
          pushstate.render(State.data);
        });

        if(typeof OPU === 'object')
          OPU.init();
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
        manage: function() {
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
        },
        photos: {
          page: null,
          end: false,
          running: false,
          load: function() {
            var _this = opTheme.init.pages.photos; loc = location;
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

            var api = location.pathname+'.json';
                params = {}, qs = window.location.search.replace('?', '');
            
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

            $.getJSON(api, params, _this.loadCb);
            // TODO move
            $(window).scroll(function(){
              if($(window).scrollTop() == $(document).height() - $(window).height()){
                _this.load();
              }
            });
          },
          loadCb: function(response) {
            var items = response.result, _page = opTheme.init.pages.photos;
            if(items[0].totalPages >= _page.page) {
              GPlusGallery.showImages($(".photo-grid-justify"), items);
              _page.page++;
              _page.running = false;
            } else {
              _page.end = true;
              $("p.page-hr:last").remove();
            }
          }
        }
      }
    }, // init
    
    message: {
      append: function(html) {
        var el = $(".message:first").clone(false),
            last = $(".message:last");

        el.addClass('alert alert-info').html(html);
        last.after(el).slideDown();
      },
      confirm: function(messageHtml) {
        opTheme.message.show(messageHtml, 'confirm');
      },
      error: function(messageHtml) {
        opTheme.message.show(messageHtml, 'error');
      },
      show: function(messageHtml, type) {
        opTheme.message.append(markup.message(messageHtml, type));
      }
    }, // message
    ui: {
      batchFormFields: {
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
                for(i in response.result) {
                  if(response.result.hasOwnProperty(i)) {
                    group = response.result[i];
                    html += '<label class="checkbox inline">' +
                        '<input type="checkbox" name="value" value="'+group.id+'" class="group-checkbox-click group-checkbox">' +
                        group.name + 
                      '</label>';
                  }
                }
              }
            }
          });
          return html;
        },
        permission: function() {
          return '  <div class="clearfix">' +
                 '    <label>Value</label>' +
                 '    <div class="input">' +
                 '      <ul class="inputs-list">' +
                 '        <li>' +
                 '          <label>' +
                 '            <input type="radio" name="value" value="1" checked="checked">' +
                 '            <span>Public</span>' +
                 '          </label>' +
                 '        </li>' +
                 '        <li>' +
                 '          <label>' +
                 '            <input type="radio" name="value" value="0"> ' +
                 '            <span>Private</span>' +
                 '          </label>' +
                 '        </li>' +
                 '    </div>' +
                 '  </div>';
        },
        tags: function() {
          return '  <div class="clearfix">' +
                 '    <label>Tags</label>' +
                 '    <div class="input">' +
                 '      <input type="text" name="value" class="tags-autocomplete" placeholder="A comma separated list of tags" value="">' +
                 '    </div>' +
                 '  </div>';
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
            markup.message(
              '  <a id="batch-message"></a>You have <span id="batch-count">'+idsLength+'</span> photos pinned.' +
              '  <div><a class="btn small info batch-modal-click" data-controls-modal="modal" data-backdrop="static">Batch edit</a>&nbsp;<a href="#" class="btn small pin-clear-click">Or clear pins</a></div>'
            )
          );
        }
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

/* Author: Florian Maul */

var GPlusGallery = (function($) {

	/* ------------ PRIVATE functions ------------ */

	/** Utility function that returns a value or the defaultvalue if the value is null */
	var $nz = function(value, defaultvalue) {
		if( typeof (value) === undefined || value == null) {
			return defaultvalue;
		}
		return value;
	};
	
	/**
	 * Distribute a delta (integer value) to n items based on
	 * the size (width) of the items thumbnails.
	 * 
	 * @method calculateCutOff
	 * @property len the sum of the width of all thumbnails
	 * @property delta the delta (integer number) to be distributed
	 * @property items an array with items of one row
	 */
	var calculateCutOff = function(len, delta, items) {
		// resulting distribution
		var cutoff = [];
		var cutsum = 0;

		// distribute the delta based on the proportion of
		// thumbnail size to length of all thumbnails.
		for(var i in items) {
			var item = items[i];
			var fractOfLen = item['photo960x180'][1] / len;
			cutoff[i] = Math.floor(fractOfLen * delta);
			cutsum += cutoff[i];
		}

		// still more pixel to distribute because of decimal
		// fractions that were omitted.
		var stillToCutOff = delta - cutsum;
		while(stillToCutOff > 0) {
			for(i in cutoff) {
				// distribute pixels evenly until done
				cutoff[i]++;
				stillToCutOff--;
				if (stillToCutOff == 0) break;
			}
		}
		return cutoff;
	};
	
	/**
	 * Takes images from the items array (removes them) as 
	 * long as they fit into a width of maxwidth pixels.
	 *
	 * @method buildImageRow
	 */
	var buildImageRow = function(maxwidth, items) {
		var row = [], len = 0;
		
		// each image a has a 3px margin, i.e. it takes 6px additional space
		var marginsOfImage = 6;

		// Build a row of images until longer than maxwidth
		while(items.length > 0 && len < maxwidth) {
			var item = items.shift();
			row.push(item);
			len += (item['photo960x180'][1] + marginsOfImage);
		}

		// calculate by how many pixels too long?
		var delta = len - maxwidth;

		// if the line is too long, make images smaller
		if(row.length > 0 && delta > 0) {

			// calculate the distribution to each image in the row
			var cutoff = calculateCutOff(len, delta, row);
			for(var i in row) {
				var pixelsToRemove = cutoff[i];
				item = row[i];

				// move the left border inwards by half the pixels
				item.vx = Math.floor(pixelsToRemove / 2);

				// shrink the width of the image by pixelsToRemove
				item.vwidth = item['photo960x180'][1] - pixelsToRemove;
			}
		} else {
			// all images fit in the row, set vx and vwidth
			for(var i in row) {
				item = row[i];
				item.vx = 0;
				item.vwidth = item['photo960x180'][1];
			}
		}

		return row;
	};
	
	/**
	 * Creates a new thumbail in the image area. An attaches a fade in animation
	 * to the image. 
	 */
	var createImageElement = function(parent, item) {
		var imageContainer = $('<div class="imageContainer"/>');

		var overflow = $("<div/>");
		overflow.css("width", ""+$nz(item.vwidth, 120)+"px");
		overflow.css("height", ""+$nz(item['path960x180'][1], 120)+"px");
		overflow.css("overflow", "hidden");

		var link = $('<a/>');
    link.attr('href', item.url);
		
		var img = $("<img/>");
		img.attr("src", item.path960x180);
    img.attr('class', 'photo-view-modal-click');
		img.attr("title", item.title);
		img.css("width", "" + $nz(item['path960x180'][1], 120) + "px");
		img.css("height", "" + $nz(item['path960x180'][2], 120) + "px");
		img.css("margin-left", "" + (item.vx ? (-item.vx) : 0) + "px");
		img.css("margin-top", "" + 0 + "px");
		img.hide();

		link.append(img);
		overflow.append(link);
		imageContainer.append(overflow);

		// fade in the image after load
		img.bind("load", function () { 
			$(this).fadeIn(500); 
		});

		//parent.find("p.page-hr:last").after(imageContainer);
		parent.append(imageContainer);
		item.el = imageContainer;
		return imageContainer;
	};
	
	/**
	 * Updates an exisiting tthumbnail in the image area. 
	 */
	var updateImageElement = function(item) {
		var overflow = item.el.find("div:first");
		var img = overflow.find("img:first");

		overflow.css("width", "" + $nz(item.vwidth, 120) + "px");
		overflow.css("height", "" + $nz(item.theight, 120) + "px");

		img.css("margin-left", "" + (item.vx ? (-item.vx) : 0) + "px");
		img.css("margin-top", "" + 0 + "px");
	};	
		
	/* ------------ PUBLIC functions ------------ */
	return {
		
		showImages : function(imageContainer, realItems) {

			// reduce width by 1px due to layout problem in IE
			var containerWidth = imageContainer.width() - 1;
			
			// Make a copy of the array
			var items = realItems.slice();
		
			// calculate rows of images which each row fitting into
			// the specified windowWidth.
			var rows = [];
			while(items.length > 0) {
				rows.push(buildImageRow(containerWidth, items));
			}  

			for(var r in rows) {
				for(var i in rows[r]) {
					var item = rows[r][i];
					if(item.el) {
						// this image is already on the screen, update it
						updateImageElement(item);
					} else {
						// create this image
						createImageElement(imageContainer, item);
					}
				}
			}
      imageContainer.append('<p class="page-hr">Page '+(parseInt(opTheme.init.pages.photos.page)+1)+'</p>');
		}
	}
})(jQuery);
