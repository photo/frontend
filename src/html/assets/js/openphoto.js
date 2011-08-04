var op = (function(){
  var log = function(msg) {
    if(console !== undefined && console.log !== undefined)
      console.log(msg);
  };
  var _this = {};
  return {
    handlers: {
      actionDelete: function(event) {
        var el = $(this),
          url = el.attr('href')+'.json';
          $.post(url, function(response) {
            if(response.code === 200)
              $(".action-container-"+response.result).hide('medium', function(){ $(this).remove(); });
            else
              op.message.error('Could not delete the photo.');
          }, 'json');
          return false;
      },
      inputSelect: function(event) {
        $(this).select().focus();
      },
      login: function() {
        log('login');
        navigator.id.getVerifiedEmail(function(assertion) {
            if (assertion) {
              op.user.loginSuccess(assertion);
            } else {
              op.user.loginFailure(assertion);
            }
        });
        return false;
      },
      photoDelete: function(event) {
        var el = $(this),
          url = el.parent().attr('action')+'.json';
          $.post(url, function(response) {
            if(response.code === 200)
              $(el).html('This photo has been deleted');
            else
              op.message.error('Could not delete the photo.');
          }, 'json');
          return false;
      },
      photoLink: function(event) {
        var el = this;
        if(event.type == 'click') {
          log(el);
        } else if(event.type == 'mouseover') {
          log('mousover');
        }
      },
      searchBarToggle: function(event) {
        $("div#searchbar").slideToggle('medium');
        return false;
      },
      searchByTags: function(event) {
        var form = this,
          tags = $($(form).find('input[name=tags]')[0]).val();
        // TODO ajaxify
        /*if(tags.length > 0)
          location.href = location.pathname + '#/photos/tags-'+tags;
        else
          location.href = location.pathname + '#/photos';*/

        if(tags.length > 0)
          location.href = '/photos/tags-'+tags;
        else
          location.href = '/photos';
        return false;
      },
      setupContinue: function(event) {
        var el = $(this),
          step = el.attr('data-step');
        if(step == 1) {
          $("div#setup ol#setup-steps li[class=current]").removeClass('current');
          $("div#setup ol#setup-steps li:nth-child(2)").addClass('current');
          $("div#setup #form-step-1").hide('medium');
          $("div#setup #form-step-2").show('medium');
        } else if(step == 2) {
          $("div#setup ol#setup-steps li[class=current]").removeClass('current');
          $("div#setup ol#setup-steps li:nth-child(3)").addClass('current');
          $("div#setup #form-step-2").hide('medium');
          $("div#setup #form-step-3").show('medium');
        }
      }
    },
    init: {
      attach: function() {
        $('.photo-link').live('click mouseover', op.handlers.photoLink);
        $('.photo-delete').live('click', op.handlers.photoDelete);
        $('.action-delete').live('click', op.handlers.actionDelete);
        $('.search-bar-toggle').click(op.handlers.searchBarToggle);
        $('form#form-tag-search').submit(op.handlers.searchByTags);
        $('.login').click(op.handlers.login);
        $('input.select').live('click', op.handlers.inputSelect);
      }
    },
    message: {
      error: function(msg) {
        alert(msg);
      }
    },
    photos: {
      search: function(tags) {
        //$.get('/photos/');
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
          var resp = jQuery.parseJSON(data.result),
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
        $.post('/user/login.json', params, op.user.loginProcessed, 'json');
      }
    }
  };
})();
