if(!window.op) window.op = {};

op.ns = op.namespace = function(ns){
  var parts = ns.split('.')
    , cur = op;
    
  while(parts.length){
    var part = parts.shift();
    if( !cur[part] ) cur[part] = {};
    cur = cur[part];
  }
  
  return cur;
};