op.ns('data.collection').Album = Backbone.Collection.extend({
  model         :op.data.model.Album,
  localStorage  :'op-albums'
});