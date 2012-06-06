var opTheme = (function() {
  var log = function(msg) {
    if(console !== undefined && console.log !== undefined)
      console.log(msg);
  };
  var timeoutId = undefined;
  return {
    callback: {
      keyboard: function(ev) {
        log("keyboard!!!!");
      },
      actionDelete: function(ev) {

        ev.preventDefault();

        var el = $(ev.target),
          	url = el.attr('href')+'.json'
            id = el.attr('data-id');

        OP.Util.makeRequest(url, el.parent().serializeArray(), function(response) {
          if(response.code === 204)
            $(".action-container-"+id).hide('medium', function(){ $(this).remove(); });
          else
            opTheme.message.error('Could not delete the photo.');
        }, 'json');

        return false;

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
        }, 'json');
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
            form = el.parent(),
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
        }, 'json');
        return false;
      },
      login: function(ev) {
        navigator.id.getVerifiedEmail(function(assertion) {
            if (assertion) {
              opTheme.user.loginSuccess(assertion);
            } else {
              opTheme.user.loginFailure(assertion);
            }
        });
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
        }, 'json');
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
            } else {
              opTheme.message.error('Could not load the form to edit this photo.');
            }
          }, 'json', 'get');
        }
        return false;
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
      searchBarToggle: function(ev) {
        $("div#searchbar").slideToggle('medium');
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
        $("div#settingsbar").slideToggle('medium');
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
        }, 'json');
        return false;
      },
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
      attach: function() {
        OP.Util.on('click:action-delete', opTheme.callback.actionDelete);
        OP.Util.on('click:action-jump', opTheme.callback.commentJump);
        OP.Util.on('click:credential-delete', opTheme.callback.credentailDelete);
        OP.Util.on('click:group-checkbox', opTheme.callback.groupCheckbox);
        OP.Util.on('click:group-update', opTheme.callback.groupPost);
        OP.Util.on('click:login', opTheme.callback.login);
        OP.Util.on('click:photo-delete', opTheme.callback.photoDelete);
        OP.Util.on('click:photo-edit', opTheme.callback.photoEdit);
        OP.Util.on('click:plugin-status', opTheme.callback.pluginStatus);
        OP.Util.on('click:plugin-update', opTheme.callback.pluginUpdate);
        OP.Util.on('click:nav-item', opTheme.callback.searchBarToggle);
        OP.Util.on('click:search', opTheme.callback.searchByTags);
        OP.Util.on('click:settings', opTheme.callback.settings);
        OP.Util.on('click:webhook-delete', opTheme.callback.webhookDelete);
        OP.Util.on('keydown:browse-next', opTheme.callback.keyBrowseNext);
        OP.Util.on('keydown:browse-previous', opTheme.callback.keyBrowsePrevious);

        OP.Util.on('upload:complete-success', opTheme.callback.uploadCompleteSuccess);
        OP.Util.on('upload:complete-failure', opTheme.callback.uploadCompleteFailure);

        opTheme.front.init($('div.front-slideshow'));
        OPU.init();
        // TODO standardize this somehow
        $('form.validate').each(opTheme.formHandlers.init);
      }
    },

    message: {
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
    user: {
      loginFailure: function(assertion) {
        log('login failed');
        // TODO something here to handle failed login
      },
      loginProcessed: function(response) {
        if(response.code != 200) {
          log('processing of login failed');
          // TODO do something here to handle failed login
          return;
        }

        log('login processing succeeded');
        window.location.reload();
      },
      loginSuccess: function(assertion) {
        var params = {assertion: assertion};
        OP.Util.makeRequest('/user/browserid/login.json', params, opTheme.user.loginProcessed, 'json');
      }
    }
  };
}());
