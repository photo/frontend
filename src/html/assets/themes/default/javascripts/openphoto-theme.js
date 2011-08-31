var opTheme = (function() {
  var log = function(msg) {
    if(console !== undefined && console.log !== undefined)
      console.log(msg);
  };
  return {
    callback: {
      actionDelete: function(ev) {
        var el = $(ev.target),
          url = el.attr('href')+'.json';
          $.post(url, function(response) {
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
        var el = $(ev.target),
          url = el.parent().attr('action')+'.json';
          $.post(url, function(response) {
            if(response.code === 200)
              el.html('This photo has been deleted');
            else
              opTheme.message.error('Could not delete the photo.');
          }, 'json');
          return false;
      },
      searchBarToggle: function(ev) {
        console.log('foobar');
        $("div#searchbar").slideToggle('medium');
        return false;
      },
      searchByTags: function(ev) {
        var form = $(ev.target).parent(),
          tags = $(form.find('input[name=tags]')[0]).val();
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
      }
    },
    init: {
      attach: function() {
        OP.Util.on('click:action-jump', opTheme.callback.commentJump);
        OP.Util.on('click:action-delete', opTheme.callback.commentJump);
        OP.Util.on('click:login', opTheme.callback.login);
        OP.Util.on('click:photo-delete', opTheme.callback.photoDelete);
        OP.Util.on('click:nav-item', opTheme.callback.searchBarToggle);
        OP.Util.on('click:search', opTheme.callback.searchByTags);
        OP.Util.on('click:action-delete', opTheme.callback.actionDelete);
        $("form#upload-form").fileupload({
          url: '/photo/upload.json',
          singleFileUploads: true,
          autoUpload: false
        })
        .bind('fileuploadadd', opTheme.upload.handlers.added)
        .bind('fileuploaddone', opTheme.upload.handlers.done)
        .bind('fileuploadprogressall', opTheme.upload.handlers.progressall)
        .bind('fileuploadprogress', opTheme.upload.handlers.progress);
      }
    },
    message: {
      error: function(msg) {
        alert(msg);
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
        $.post('/user/login.json', params, opTheme.user.loginProcessed, 'json');
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
