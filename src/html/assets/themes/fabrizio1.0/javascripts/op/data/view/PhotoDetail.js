(function($){
  op.ns('data.view').PhotoDetail = op.data.view.Editable.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
    },
    model: this.model,
    className: 'photo-detail-meta',
    template    :_.template($('#photo-detail-meta').html()),
    editable    : {
      'a.title.edit' : {
        name: 'title',
        title: 'Edit Photo Title',
        placement: 'top',
        on: {
          shown: function(){
            // var view = $(this).data('editable').view;
            $(this).parents('.imageContainer').addClass('editing');
            $(this).data('editable').container.setPosition();
            
            // remove the fade effect because we need to toggle the overflow
            // and it looks crappy when it gets cut off during the transition
            $(this).data('editable').container.tip().removeClass('fade');
          },
          hidden : function(){
            $(this).parents('.imageContainer').removeClass('editing');
          }
        }
      },
      'a.description.edit' : {
        name: 'description',
        title: 'Edit Photo Description',
        placement: 'top',
        on: {
          shown: function(){
            // var view = $(this).data('editable').view;
            $(this).parents('.imageContainer').addClass('editing');
            $(this).data('editable').container.setPosition();
            
            // remove the fade effect because we need to toggle the overflow
            // and it looks crappy when it gets cut off during the transition
            $(this).data('editable').container.tip().removeClass('fade');
          },
          hidden : function(){
            $(this).parents('.imageContainer').removeClass('editing');
          }
        }
      }
    },
    events: {
      'click a.paginate': 'paginate'
    },
    paginate: function(ev) {
      var $el = $(ev.currentTarget), id = $el.attr('data-id');
      console.log(id);
      TBX.init.pages.photo.load(id);
    },
    modelChanged: function() {
      this.render();
    }
  });
})(jQuery);

