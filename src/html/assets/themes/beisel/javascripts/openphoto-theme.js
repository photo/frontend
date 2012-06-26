var opTheme = (function() {
  var crumb = null;
  var setCrumb = function(_crumb) {
    crumb = _crumb;
  }
  var getCrumb = function() {
    return crumb;
  };
  var log = function(msg) {
    if(console !== undefined && console.log !== undefined)
      console.log(msg);
  };
  var modalMarkup = function(header, body, footer) {
    return '<div class="modal-header">' +
           '  <a href="#" class="close">&times;</a>' +
           '  <h3>'+header+'</h3>' +
           '</div>' +
           '<div class="modal-body">' +
           '  <p>'+body+'</p>' +
           '</div>' +
           '<div class="modal-footer">' + footer + '</div>';
  };
  var messageMarkup = function(message) {
    var cls = '';
    if(arguments.length > 1) {
      if(arguments[1] == 'error')
        cls = 'error';
      else if(arguments[1] == 'confirm')
        cls = 'success';
    }
    return '<div class="alert-message block-message '+cls+'"><a class="modal-close-click close" href="#">x</a>' + message + '</div>'
  };
  var tokenPreProcess = function(results) {
    var term = $(document.activeElement).val(), result = results.result, retval = [];
    for(i in result){ 
      if(result.hasOwnProperty(i))
        retval.push({id:result[i].id, name:result[i].id + ' ('+result[i].count+')'}); 
    } 
    // we need to append the current term into the list to allow users to use "new" tags
    retval.push({id: term, name: term});
    return retval;
  };
  var tokenFormatter = function(item) {
    return '<li>'+item.name+'</li>';
  };
  var timeoutId = undefined;
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
        var el = $(".id-"+photo.id);
        el.removeClass("unpinned").addClass("pinned");
        opTheme.ui.batchMessage();
        log("Adding photo " + photo.id);
      },
      batchClear: function() {
        var el = $("#batch-message").parent();
        $(".pinned").removeClass("pinned").addClass("unpinned").children().filter(".pin").fadeOut();
        el.slideUp('fast', function(){ $(this).remove(); });
      },
      batchField: function(ev) {
        var el = $(ev.target),
            val = el.val(),
            tgt = $("form#batch-edit .form-fields");
        switch(val) {
          case 'delete':
            tgt.html(opTheme.ui.batchFormFields.empty());
            break;
          case 'permission':
            tgt.html(opTheme.ui.batchFormFields.permission());
            break;
          case 'tagsAdd':
          case 'tagsRemove':
            tgt.html(opTheme.ui.batchFormFields.tags());
            OP.Util.fire('callback:tags-autocomplete');
            break;
        }
      },
      batchModal: function() {
        var el = $("#modal"),
            fieldMarkup = {},
            html = modalMarkup(
              'Batch edit your pinned photos',
              '<form id="batch-edit">' +
              '  <div class="clearfix">' +
              '    <label>Property</label>' +
              '    <div class="input">' +
              '      <select id="batch-key" class="batch-field-change" name="property">' +
              '        <option value="tagsAdd">Add Tags</option>' +
              '        <option value="tagsRemove">Remove Tags</option>' +
              '        <option value="permission">Permission</option>' +
              '        <option value="delete">Delete</option>' +
              '      </select>' +
              '    </div>' +
              '  </div>' +
              '  <div class="form-fields">'+opTheme.ui.batchFormFields.tags()+'</div>' +
              '</form>',
              '<a href="#" class="btn photo-update-batch-click">Submit</a>'
            );
        el.html(html);
        OP.Util.fire('callback:tags-autocomplete');
      },
      batchRemove: function(id) {
        var el = $(".id-"+id);
        el.addClass("unpinned").removeClass("pinned");
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
            el.parent().remove();
            opTheme.message.confirm('Credential successfully deleted.');
          } else {
            opTheme.message.error('Could not delete credential.');
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
      groupPost: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            form = el.parent().parent(),
            url = form.attr('action')+'.json',
            isCreate = (url.search('create') > -1);
        OP.Util.makeRequest(url, form.serializeArray(), function(response) {
          if(response.code === 200) {
            if(isCreate)
              location.href = location.href;
            else
              opTheme.message.confirm('Group updated successfully.');
          } else {
            opTheme.message.error('Could not update group.');
          }
        });
        return false;
      },
      keyBrowseNext: function(ev) {
          var ref;
          ref = $(".image-pagination .next a").attr("href");
          if (ref) {
              location.href = ref;
          }
      },
      keyBrowsePrevious: function(ev) {
          var ref;
          ref = $(".image-pagination .previous a").attr("href");
          if (ref) {
              location.href = ref;
          }
      },
      login: function(ev) {
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
      },
      modalClose: function(ev) {
        ev.preventDefault();
        var el = $(ev.target).parent();
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
          	url = el.attr('href')+'.json';
        if($("div.owner-edit").length == 1) {
          $.scrollTo($('div.owner-edit'), 200);
        } else {
          
          OP.Util.makeRequest(url, {}, function(response){
            if(response.code === 200) {
              $("#main").append(response.result.markup);
              $.scrollTo($('div.owner-edit'), 200);
              OP.Util.fire('callback:tags-autocomplete');
            } else {
              opTheme.message.error('Could not load the form to edit this photo.');
            }
          }, 'json', 'get');
          
        }
        return false;
      },
      photoUpdateBatch: function(ev) {
        ev.preventDefault();
        var el = $(ev.target),
            key = $("#batch-key").val(),
            fields = $("form#batch-edit").find("*[name='value']"),
            value;

        if(fields.length == 1)
          value = fields.val();
        else
          value = $("form#batch-edit").find("*[name='value']:checked").val();

        params = {'crumb':crumb};
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
          opTheme.message.append(messageMarkup('Your photos were successfully updated.', 'confirm'));
        } else if(response.code == 204) {
          OP.Batch.clear();
          opTheme.message.append(messageMarkup('Your photos were successfully deleted.', 'confirm'));
        } else {
          opTheme.message.append(messageMarkup('There was a problem updating your photos.', 'error'));
        }
        $("#modal").modal('hide');
      },
      pinClick: function(ev) {
        var el = $(ev.target),
            id = el.attr('data-id'),
            container = el.parent();;
        // if the parent has class="unpinned" then add, else remove
        if(container.hasClass("unpinned")) {
          OP.Batch.add(id);
        } else {
          OP.Batch.remove(id);
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
        var form = $(ev.target).parent(),
          tags = $(form.find('input[name=tags]')[0]).val(),
          url = $(form).attr('action');

        if(tags.length > 0)
          location.href = url.replace('/list', '')+'/tags-'+tags+'/list';
        else
          location.href = url;
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
    },
    formHandlers: {
			hasErrors: function(form, attribute) {
				var errors = new Array();

				form.children('input, textarea').each(function() {
					var child = $(this);
					// remove any old error classes
					child.prev().removeClass('error');
					var dataValidation = child.attr(attribute);
					if(dataValidation != undefined) {
						var dataValidationArray = dataValidation.split(' ');
						for(var i = 0; i < dataValidationArray.length; i++) {
							if(dataValidationArray[i] == 'date') {
								if(!opTheme.formHandlers.passesDate(child)) {
									var message = child.prev().html() + ' is not a valid date';
									errors.push(new Array(child, message));
								}
							}

							if(dataValidationArray[i] == 'email') {
								if(!opTheme.formHandlers.passesEmail(child)) {
									var message = child.prev().html() + ' is not a valid email address';
									errors.push(new Array(child, message));
								}
							}

							if(dataValidationArray[i] == 'ifexists') {
								if(child.val() != '' && child.val() != undefined) {
									$.merge(errors, opTheme.formHandlers.hasErrors(form, 'data-ifexists'));
								}
							}

							if(dataValidationArray[i] == 'integer') {
								if(!opTheme.formHandlers.passesInteger(child)) {
									var message = child.prev().html() + ' is not a number';
									errors.push(new Array(child, message));
								}
							}

							if(dataValidationArray[i] == 'match') {
								var matchId = child.attr('data-match');
								if(!opTheme.formHandlers.passesMatch(child, matchId)) {
									var message = child.prev().html() + ' does not match ' + $('#' + matchId).prev().html();
									errors.push(new Array(child, message));
								}
							}

							if(dataValidationArray[i] == 'required') {
								if(!opTheme.formHandlers.passesRequired(child)) {
									var message = child.prev().html() + ' is required';
									errors.push(new Array(child, message));
								}
							}

							if(dataValidationArray[i] == 'alphanumeric') {
								if(!opTheme.formHandlers.passesAlphaNumeric(child)) {
									var message = child.prev().html() + ' can only contain alpha-numeric characters';
									errors.push(new Array(child, message));
								}
							}
						}
					}
				});

				return errors;
			},

			init: function(index) {
				$(this).submit(opTheme.submitHandlers.siteForm);
				opTheme.formHandlers.showPlaceholders();
				$('input[data-placeholder]').live('focus', opTheme.formHandlers.placeholderFocus);
				$('input[data-placeholder]').live('blur', opTheme.formHandlers.placeholderBlur);
			},

      passesAlphaNumeric: function(obj) {
				var regex = /^[a-zA-Z0-9]+$/;
				return regex.test(obj.val());
      },

			passesDate: function(obj) {
				var regex = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
				return regex.test(obj.val());
			},

			passesEmail: function(obj) {
				var regex = /^([\w-\.+]+@([\w-]+\.)+[\w-]{2,4})?$/;
				return regex.test(obj.val());
			},

			passesInteger: function(obj) {
				var regex = /^\d+$/;
				return regex.test(obj.val());
			},

			passesMatch: function(obj, matchId) {
				return obj.val() == $('#' + matchId).val();
			},

			passesRequired: function(obj) {
				if(obj.is('textarea') || (obj.is('input') && (obj.attr('type') == 'text' || obj.attr('type') == 'password')))
					return obj.val() != '' && obj.val() != undefined;
				else if(obj.is('checkbox'))
					return obj.is(':checked');
				else
					return true;
			},

			placeholderBlur: function() {
				var obj = $(this);
				if(obj.val() == '') {
					obj.val(obj.attr('data-placeholder'));
					obj.addClass('placeholder');
				}
			},

			placeholderFocus: function() {
				var obj = $(this);
				if(obj.val() == obj.attr('data-placeholder')) {
					obj.val('');
					obj.removeClass('placeholder');
				}
			},

			removePlaceholders: function() {
				$('input[data-placeholder]').each(function() {
					var obj = $(this);
					if(obj.val() == obj.attr('data-placeholder')) {
						obj.val('');
						obj.removeClass('placeholder');
					}
				});
			},

			showPlaceholders: function() {
				$('input[data-placeholder]').each(function() {
					var obj = $(this);
					if(obj.val() == '') {
						obj.val(obj.attr('data-placeholder'));
						obj.addClass('placeholder');
					}
				});
			}
		},

    front: {
      init: function(el) {
        if(el.length > 0) {
          el.cycle({ fx: 'fade' }).find('img').click(
            function(ev) {
              var img = ev.target;
              location.href=$(img).attr('data-origin');
            }
          );
        }
      }
    },
    
    init: {
      load: function(_crumb) {
        setCrumb(_crumb);
        if($("section#slideshow").length > 0) {
          $(window).load(function() {
            $('.flexslider').flexslider({
              animation: "slide",
              controlsContainer: ".flex-container",
              controlNav: true,
              pausePlay: false,
              directionNav: true,
              nextText: "<span title='Next'>Next</span>",
              prevText: "<span title='Previous'>Previous</span>"
            });
          });
        }
        $("#modal").modal({keyboard:true});
      },
      attach: function() {
        OP.Util.on('click:action-delete', opTheme.callback.actionDelete);
        OP.Util.on('click:action-jump', opTheme.callback.commentJump);
        OP.Util.on('click:batch-modal', opTheme.callback.batchModal);
        OP.Util.on('click:credential-delete', opTheme.callback.credentailDelete);
        OP.Util.on('click:group-checkbox', opTheme.callback.groupCheckbox);
        OP.Util.on('click:group-update', opTheme.callback.groupPost);
        OP.Util.on('click:login', opTheme.callback.login);
        OP.Util.on('click:modal-close', opTheme.callback.modalClose);
        OP.Util.on('click:nav-item', opTheme.callback.searchBarToggle);
        OP.Util.on('click:photo-delete', opTheme.callback.photoDelete);
        OP.Util.on('click:photo-edit', opTheme.callback.photoEdit);
        OP.Util.on('click:photo-update-batch', opTheme.callback.photoUpdateBatch);
        OP.Util.on('click:plugin-status', opTheme.callback.pluginStatus);
        OP.Util.on('click:plugin-update', opTheme.callback.pluginUpdate);
        OP.Util.on('click:pin', opTheme.callback.pinClick);
        OP.Util.on('click:pin-clear', opTheme.callback.pinClearClick);
        OP.Util.on('click:search', opTheme.callback.searchByTags);
        OP.Util.on('click:settings', opTheme.callback.settings);
        OP.Util.on('click:webhook-delete', opTheme.callback.webhookDelete);
        OP.Util.on('keydown:browse-next', opTheme.callback.keyBrowseNext);
        OP.Util.on('keydown:browse-previous', opTheme.callback.keyBrowsePrevious);
        OP.Util.on('change:batch-field', opTheme.callback.batchField);

        OP.Util.on('callback:tags-autocomplete', opTheme.init.tags.autocomplete);
        OP.Util.on('callback:batch-add', opTheme.callback.batchAdd);
        OP.Util.on('callback:batch-remove', opTheme.callback.batchRemove);
        OP.Util.on('callback:batch-clear', opTheme.callback.batchClear);

        OP.Util.on('upload:complete-success', opTheme.callback.uploadCompleteSuccess);
        OP.Util.on('upload:complete-failure', opTheme.callback.uploadCompleteFailure);

        OP.Util.fire('callback:tags-autocomplete');

        if(typeof OPU === 'object')
          OPU.init();
        // TODO standardize this somehow
        $('form.validate').each(opTheme.formHandlers.init);
      },
      photos: function() {
        var ids = OP.Batch.collection.getAll(),
            idsLength = OP.Batch.collection.getLength(),
            els = $(".unpinned"),
            cls,
            el,
            parts;

        if(idsLength > 0)
          opTheme.ui.batchMessage();

        els.each(function(i, el) {
          el = $(el);
          cls = el.attr('class');
          parts = cls.match(/ id-([a-z0-9]+)/);
          if(parts.length == 2) {
            if(ids[parts[1]] !== undefined)
              el.removeClass("unpinned").addClass("pinned");
          }
        });
      },
      tags: {
        autocomplete: function() {
          var config = {};
          config.queryParam = 'search';
          config.propertyToSearch = 'id';
          config.preventDuplicates = true;
          config.onResult = tokenPreProcess;
          config.resultsFormatter = tokenFormatter;
          $("input[class~='tags-autocomplete']").each(function(i, el) {
            var cfg = config, el = $(el), val = el.attr('value');
            // check if this input has been tokenized already
            if(el.css('display') == 'none')
              return;

            if(val != '') {
              var tags = val.split(','), prePopulate = [];
              for(i=0; i<tags.length; i++) {
                prePopulate.push({id: tags[i], name: tags[i]});
              }
              config.prePopulate = prePopulate;
            }
            $(el).tokenInput("/tags/list.json", config);
          });
        }
      }
    },
    
    message: {
      append: function(html) {
        $("#message").append(html).slideDown();
      },
      close: function() {
        if(timeoutId != undefined) {
          clearTimeout(timeoutId);
          timeoutId = undefined;
          $('#message-box').animate({height:'toggle'}, 500, function() {
            $('#message-box').remove();
          });
        }
      },
      confirm: function(messageHtml) {
        opTheme.message.show(messageHtml, 'confirm');
      },
      error: function(messageHtml) {
        opTheme.message.show(messageHtml, 'error');
      },
      show: function(messageHtml, type) {
        var thisType = type,
          removeType = type == 'error' ? 'confirm' : 'error';
        if(timeoutId != undefined) {
          clearTimeout(timeoutId);
          timeoutId = undefined;
          $('#message-box').
            removeClass(removeType).
            addClass(thisType).
            html('<div><a class="message-close">close</a>' + messageHtml + '</div>');
          timeoutId = setTimeout(function() {
            $('#message-box').animate({height:'toggle'}, 500, function() {
              $('#message-box').remove();
              timeoutId = undefined;
            });
          }, 7000);
        } else {
          $('html').append('<section id="message-box" style="display:none;"><div><a class="message-close">close</a>' + messageHtml + '</div></section>');
          $('#message-box').
            removeClass(removeType).
            addClass(thisType).
            animate({height:'toggle'}, 500, function() {
              timeoutId = setTimeout(function() {
                $('#message-box').animate({height:'toggle'}, 500, function() {
                  $('#message-box').remove();
                  timeoutId = undefined;
                });
              }, 7000);
            }
          );
        }
        $('a.message-close').click(opTheme.message.close);
      }
    },
    submitHandlers: {
			siteForm: function(event) {
				var form = $(this);
				event.preventDefault();
				opTheme.formHandlers.removePlaceholders();
				var errors = opTheme.formHandlers.hasErrors(form, 'data-validation');
				opTheme.formHandlers.showPlaceholders();

				if(errors.length == 0) {
					// submit the form
					this.submit();
				} else {
					var messageHtml = '<ul>';
					for(var i = 0; i < errors.length; i++) {
						// highlight all errors
						errors[i][0].prev().addClass('error');
						messageHtml += '<li>' + errors[i][1] + '</li>';
					}
					messageHtml += '</ul>';

					// scroll to the topmost error and focus
					$('html').animate({scrollTop: errors[0][0].offset().top-30}, 500);
					errors[0][0].focus();

					// bring up the error message box
					opTheme.message.error(messageHtml);
				}
			}
		}, 
    ui: {
      batchFormFields: {
        empty: function() {
          return '';
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
          $("#batch-message").parent().slideUp('fast', function() { $(this).remove(); });
          return;
        } else {
          opTheme.message.append(
            messageMarkup(
              '  <a id="batch-message"></a>You have <span id="batch-count">'+idsLength+'</span> photos pinned.' +
              '  <div class="alert-actions"><a class="btn small info batch-modal-click" data-controls-modal="modal" data-backdrop="static">Batch edit</a><a href="#" class="btn small pin-selectall-click">Select all pins</a><a href="#" class="btn small pin-clear-click">Or clear pins</a></div>'
            )
          );
        }
      }
    },
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
    }
  };
}());
