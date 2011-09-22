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
          	url = el.attr('href')+'.json';
      
        OP.Util.makeRequest(url, el.parent().serializeArray(), function(response) {
          if(response.code === 200)
            $(".action-container-"+response.result).hide('medium', function(){ $(this).remove(); });
          else
            opTheme.message.error('Could not delete the photo.');
        }, 'json');
        
        return false;
        
      },
      commentJump: function(ev) {
        ev.preventDefault();
        $.scrollTo($('div.comment-form'), 200);
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
          if(response.code === 200)
            el.html('This photo has been deleted');
          else
            opTheme.message.error('Could not delete the photo.');
        }, 'json');
        
        return false;
      
      },
      searchBarToggle: function(ev) {
        $("div#searchbar").slideToggle('medium');
        return false;
      },
      searchByTags: function(ev) {
        ev.preventDefault();
        var form = $(ev.target).parent(),
          tags = $(form.find('input[name=tags]')[0]).val();

        if(tags.length > 0)
          location.href = '/photos/tags-'+tags;
        else
          location.href = '/photos';
        return false;
      },
      settings: function(ev) {
        $("div#settingsbar").slideToggle('medium');
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

			passesDate: function(obj) {
				var regex = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
				return regex.test(obj.val());
			},

			passesEmail: function(obj) {
				var regex = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
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

		messageBox: function(messageHtml) {
			$('a.message-close').live('click', opTheme.messageBoxClose);
			if(timeoutId != undefined) {
				clearTimeout(timeoutId);
				timeoutId = undefined;
				$('#message-box').html('<div><a class="message-close">close</a>' + messageHtml + '</div>');
				timeoutId = setTimeout(function() {
					$('#message-box').animate({height:'toggle'}, 500, function() {
						$('#message-box').remove();
						timeoutId = undefined;
					});
				}, 7000);
			} else {
				$('html').append('<section id="message-box" style="display:none;"><div><a class="message-close">close</a>' + messageHtml + '</div></section>');
				$('#message-box').animate({height:'toggle'}, 500, function() {
					timeoutId = setTimeout(function() {
						$('#message-box').animate({height:'toggle'}, 500, function() {
							$('#message-box').remove();
							timeoutId = undefined;
						});
					}, 7000);
				});
			}
		},

		messageBoxClose: function() {
			if(timeoutId != undefined) {
				clearTimeout(timeoutId);
				timeoutId = undefined;
				$('#message-box').animate({height:'toggle'}, 500, function() {
					$('#message-box').remove();
				});
			}
		},
    init: {
      attach: function() {
        OP.Util.on('click:action-jump', opTheme.callback.commentJump);
        OP.Util.on('click:login', opTheme.callback.login);
        OP.Util.on('click:photo-delete', opTheme.callback.photoDelete);
        OP.Util.on('click:nav-item', opTheme.callback.searchBarToggle);
        OP.Util.on('click:search', opTheme.callback.searchByTags);
        OP.Util.on('click:action-delete', opTheme.callback.actionDelete);
        OP.Util.on('click:settings', opTheme.callback.settings);
        OP.Util.on('keydown:browse-next', opTheme.callback.keyBrowseNext);
        OP.Util.on('keydown:browse-previous', opTheme.callback.keyBrowsePrevious);
        opTheme.front.init($('div.front-slideshow'));

        $("form#upload-form").fileupload({
          url: '/photo/upload.json',
          singleFileUploads: true,
          autoUpload: false
        })
        .bind('fileuploadadd', opTheme.upload.handlers.added)
        .bind('fileuploaddone', opTheme.upload.handlers.done)
        .bind('fileuploadprogressall', opTheme.upload.handlers.progressall)
        .bind('fileuploadprogress', opTheme.upload.handlers.progress);

        $('form.validate').each(opTheme.formHandlers.init);
      }
    },
    message: {
      error: function(msg) {
        alert(msg);
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
					opTheme.messageBox(messageHtml);
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
        OP.Util.makeRequest('/user/login.json', params, opTheme.user.loginProcessed, 'json');
      }
    },
    upload: {
      handlers: {
        added: function(e, data) {
          var files = data.files
            html = '<li id="%id"><div><div class="img"><label>%label</label></div><div class="progress"><div></div></div></li>';
          data.id=parseInt(Math.random()*1000);
          for(i=0; i<files.length; i++) {
            $(html.replace('%id', data.id).replace('%label', files[i].fileName))
              .prependTo("ul#upload-queue");
          }
        },
        done: function(e, data) {
          var resp = data.result,
            id = resp.result.id,
            img = resp.result.path100x100;
          $("#"+data.id+" div.progress div.img").css("width", "").css("height", "");
          $("#"+data.id+" div.progress div").css("width", "100%").addClass('complete');
          $("#"+data.id+" div.img").replaceWith('<a href="/photo/'+id+'"><img src="'+img+'"></a>');
        },
        progress: function(e, data) {
          var pct = parseInt(data.loaded/data.total*100);
          $("#"+data.id+" div.progress div").css("width", pct+"%");
          if(pct > 95)
            $("#"+data.id+" div.img").html("Crunching...");
          else if(pct > 85)
            $("#"+data.id+" div.img").html("Backing up...");


        },
        progressall: function(e, data) {
          var pct = parseInt(data.loaded/data.total*100);
          $("#upload-progress").html("%s% completed".replace('%s', pct));
        }
      }
    },
  };
}());
