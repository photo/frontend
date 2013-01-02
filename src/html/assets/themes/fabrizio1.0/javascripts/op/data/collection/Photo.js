op.ns('data.collection').Photo = Backbone.Collection.extend({
  model         :op.data.model.Photo,
  localStorage  :'op-photos'
});