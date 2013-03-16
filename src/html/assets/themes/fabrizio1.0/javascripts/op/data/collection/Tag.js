op.ns('data.collection').Tag = Backbone.Collection.extend({
  model         :op.data.model.Tag,
  localStorage  :'op-tags'
});