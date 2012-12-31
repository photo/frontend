(function($){
  op.ns('data.view').PhotoGallery = op.data.view.Editable.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
    },
    model: this.model,
    className: 'photo-meta',
    template    :_.template($('#photo-meta').html()),
    editable    : {
      '.title a' : {
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
      }
    },
    events: {
      'click .permission.edit': 'permission',
      'click .title.edit': 'title',
      'click .profile.edit': 'profile'
    },
    permission: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), model = this.model/*arguments[0].view.Photos.get(id)*/, view = this;
      model.set('permission', model.get('permission') == 0 ? 1 : 0, {silent:false});
      model.save();
    },
    profile: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), profileModel = op.data.store.Profiles.get(TBX.profiles.getOwner());
      profileModel.set('photoId', id, {silent:true});
      profileModel.save();
    },
    title: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), model = this.model, currentTitle = model.get('title');
      var newTitle = prompt("Change your name", currentTitle);
      if(newTitle === null || newTitle === currentTitle)
        return;

      model.set('title', newTitle, {silent:true});
      model.save();
    },
    modelChanged: function() {
      this.render();
    }
  });
})(jQuery);