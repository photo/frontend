var op = (function(){
  var _this = {
  };
  return {
    handlers: {
      photoDelete: function(event) {
        var el = $(this),
          url = el.attr('href')+'.json';
          $.post(url, function(response) {
            if(response.code === 200)
              $(".photo-container-"+response.result).hide('medium', function(){ $(this).remove(); });
            else
              op.message.error('Could not delete the photo.');
          }, 'json');
          return false;
      },
      photoLink: function(event) {
        var el = this;
        if(event.type == 'click') {
          console.log(el);
        } else if(event.type == 'mouseover') {
          console.log('mousover');
        }
      },
      searchBarToggle: function(event) {
        $("div#searchbar").toggle();
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
        $('.search-bar-toggle').click(op.handlers.searchBarToggle);
        $('form#form-tag-search').submit(op.handlers.searchByTags);
      }
    },
    message: {
      error: function(msg) {
        alert(msg);
      }
    },
    upload: {
      handlers: {
        added: function(e, data) {
          var files = data.files
            html = '<li id="%id"><div><label>%label</label><div class="img">Uploading...</div><div class="progress"><div></div></div></li>';
          data.id=parseInt(Math.random()*1000);
          for(i=0; i<files.length; i++) {
            $(html.replace('%id', data.id).replace('%label', files[i].fileName))
              .prependTo("ul#upload-queue");
          }
        },
        done: function(e, data) {
          var resp = jQuery.parseJSON(data.result),
            img = "http://"+resp.result.host+resp.result.requestedUrl;
          $("#"+data.id+" div.progress div").css("width", "100%");
          $("#"+data.id+" div.img").replaceWith('<img src="'+img+'">');
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
    photos: {
      search: function(tags) {
        //$.get('/photos/');
      }
    }
  };
})();
