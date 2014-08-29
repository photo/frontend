
/* /underscore-min.js */
//     Underscore.js 1.4.3
//     http://underscorejs.org
//     (c) 2009-2012 Jeremy Ashkenas, DocumentCloud Inc.
//     Underscore may be freely distributed under the MIT license.
(function(){var n=this,t=n._,r={},e=Array.prototype,u=Object.prototype,i=Function.prototype,a=e.push,o=e.slice,c=e.concat,l=u.toString,f=u.hasOwnProperty,s=e.forEach,p=e.map,v=e.reduce,h=e.reduceRight,g=e.filter,d=e.every,m=e.some,y=e.indexOf,b=e.lastIndexOf,x=Array.isArray,_=Object.keys,j=i.bind,w=function(n){return n instanceof w?n:this instanceof w?(this._wrapped=n,void 0):new w(n)};"undefined"!=typeof exports?("undefined"!=typeof module&&module.exports&&(exports=module.exports=w),exports._=w):n._=w,w.VERSION="1.4.3";var A=w.each=w.forEach=function(n,t,e){if(null!=n)if(s&&n.forEach===s)n.forEach(t,e);else if(n.length===+n.length){for(var u=0,i=n.length;i>u;u++)if(t.call(e,n[u],u,n)===r)return}else for(var a in n)if(w.has(n,a)&&t.call(e,n[a],a,n)===r)return};w.map=w.collect=function(n,t,r){var e=[];return null==n?e:p&&n.map===p?n.map(t,r):(A(n,function(n,u,i){e[e.length]=t.call(r,n,u,i)}),e)};var O="Reduce of empty array with no initial value";w.reduce=w.foldl=w.inject=function(n,t,r,e){var u=arguments.length>2;if(null==n&&(n=[]),v&&n.reduce===v)return e&&(t=w.bind(t,e)),u?n.reduce(t,r):n.reduce(t);if(A(n,function(n,i,a){u?r=t.call(e,r,n,i,a):(r=n,u=!0)}),!u)throw new TypeError(O);return r},w.reduceRight=w.foldr=function(n,t,r,e){var u=arguments.length>2;if(null==n&&(n=[]),h&&n.reduceRight===h)return e&&(t=w.bind(t,e)),u?n.reduceRight(t,r):n.reduceRight(t);var i=n.length;if(i!==+i){var a=w.keys(n);i=a.length}if(A(n,function(o,c,l){c=a?a[--i]:--i,u?r=t.call(e,r,n[c],c,l):(r=n[c],u=!0)}),!u)throw new TypeError(O);return r},w.find=w.detect=function(n,t,r){var e;return E(n,function(n,u,i){return t.call(r,n,u,i)?(e=n,!0):void 0}),e},w.filter=w.select=function(n,t,r){var e=[];return null==n?e:g&&n.filter===g?n.filter(t,r):(A(n,function(n,u,i){t.call(r,n,u,i)&&(e[e.length]=n)}),e)},w.reject=function(n,t,r){return w.filter(n,function(n,e,u){return!t.call(r,n,e,u)},r)},w.every=w.all=function(n,t,e){t||(t=w.identity);var u=!0;return null==n?u:d&&n.every===d?n.every(t,e):(A(n,function(n,i,a){return(u=u&&t.call(e,n,i,a))?void 0:r}),!!u)};var E=w.some=w.any=function(n,t,e){t||(t=w.identity);var u=!1;return null==n?u:m&&n.some===m?n.some(t,e):(A(n,function(n,i,a){return u||(u=t.call(e,n,i,a))?r:void 0}),!!u)};w.contains=w.include=function(n,t){return null==n?!1:y&&n.indexOf===y?-1!=n.indexOf(t):E(n,function(n){return n===t})},w.invoke=function(n,t){var r=o.call(arguments,2);return w.map(n,function(n){return(w.isFunction(t)?t:n[t]).apply(n,r)})},w.pluck=function(n,t){return w.map(n,function(n){return n[t]})},w.where=function(n,t){return w.isEmpty(t)?[]:w.filter(n,function(n){for(var r in t)if(t[r]!==n[r])return!1;return!0})},w.max=function(n,t,r){if(!t&&w.isArray(n)&&n[0]===+n[0]&&65535>n.length)return Math.max.apply(Math,n);if(!t&&w.isEmpty(n))return-1/0;var e={computed:-1/0,value:-1/0};return A(n,function(n,u,i){var a=t?t.call(r,n,u,i):n;a>=e.computed&&(e={value:n,computed:a})}),e.value},w.min=function(n,t,r){if(!t&&w.isArray(n)&&n[0]===+n[0]&&65535>n.length)return Math.min.apply(Math,n);if(!t&&w.isEmpty(n))return 1/0;var e={computed:1/0,value:1/0};return A(n,function(n,u,i){var a=t?t.call(r,n,u,i):n;e.computed>a&&(e={value:n,computed:a})}),e.value},w.shuffle=function(n){var t,r=0,e=[];return A(n,function(n){t=w.random(r++),e[r-1]=e[t],e[t]=n}),e};var F=function(n){return w.isFunction(n)?n:function(t){return t[n]}};w.sortBy=function(n,t,r){var e=F(t);return w.pluck(w.map(n,function(n,t,u){return{value:n,index:t,criteria:e.call(r,n,t,u)}}).sort(function(n,t){var r=n.criteria,e=t.criteria;if(r!==e){if(r>e||void 0===r)return 1;if(e>r||void 0===e)return-1}return n.index<t.index?-1:1}),"value")};var k=function(n,t,r,e){var u={},i=F(t||w.identity);return A(n,function(t,a){var o=i.call(r,t,a,n);e(u,o,t)}),u};w.groupBy=function(n,t,r){return k(n,t,r,function(n,t,r){(w.has(n,t)?n[t]:n[t]=[]).push(r)})},w.countBy=function(n,t,r){return k(n,t,r,function(n,t){w.has(n,t)||(n[t]=0),n[t]++})},w.sortedIndex=function(n,t,r,e){r=null==r?w.identity:F(r);for(var u=r.call(e,t),i=0,a=n.length;a>i;){var o=i+a>>>1;u>r.call(e,n[o])?i=o+1:a=o}return i},w.toArray=function(n){return n?w.isArray(n)?o.call(n):n.length===+n.length?w.map(n,w.identity):w.values(n):[]},w.size=function(n){return null==n?0:n.length===+n.length?n.length:w.keys(n).length},w.first=w.head=w.take=function(n,t,r){return null==n?void 0:null==t||r?n[0]:o.call(n,0,t)},w.initial=function(n,t,r){return o.call(n,0,n.length-(null==t||r?1:t))},w.last=function(n,t,r){return null==n?void 0:null==t||r?n[n.length-1]:o.call(n,Math.max(n.length-t,0))},w.rest=w.tail=w.drop=function(n,t,r){return o.call(n,null==t||r?1:t)},w.compact=function(n){return w.filter(n,w.identity)};var R=function(n,t,r){return A(n,function(n){w.isArray(n)?t?a.apply(r,n):R(n,t,r):r.push(n)}),r};w.flatten=function(n,t){return R(n,t,[])},w.without=function(n){return w.difference(n,o.call(arguments,1))},w.uniq=w.unique=function(n,t,r,e){w.isFunction(t)&&(e=r,r=t,t=!1);var u=r?w.map(n,r,e):n,i=[],a=[];return A(u,function(r,e){(t?e&&a[a.length-1]===r:w.contains(a,r))||(a.push(r),i.push(n[e]))}),i},w.union=function(){return w.uniq(c.apply(e,arguments))},w.intersection=function(n){var t=o.call(arguments,1);return w.filter(w.uniq(n),function(n){return w.every(t,function(t){return w.indexOf(t,n)>=0})})},w.difference=function(n){var t=c.apply(e,o.call(arguments,1));return w.filter(n,function(n){return!w.contains(t,n)})},w.zip=function(){for(var n=o.call(arguments),t=w.max(w.pluck(n,"length")),r=Array(t),e=0;t>e;e++)r[e]=w.pluck(n,""+e);return r},w.object=function(n,t){if(null==n)return{};for(var r={},e=0,u=n.length;u>e;e++)t?r[n[e]]=t[e]:r[n[e][0]]=n[e][1];return r},w.indexOf=function(n,t,r){if(null==n)return-1;var e=0,u=n.length;if(r){if("number"!=typeof r)return e=w.sortedIndex(n,t),n[e]===t?e:-1;e=0>r?Math.max(0,u+r):r}if(y&&n.indexOf===y)return n.indexOf(t,r);for(;u>e;e++)if(n[e]===t)return e;return-1},w.lastIndexOf=function(n,t,r){if(null==n)return-1;var e=null!=r;if(b&&n.lastIndexOf===b)return e?n.lastIndexOf(t,r):n.lastIndexOf(t);for(var u=e?r:n.length;u--;)if(n[u]===t)return u;return-1},w.range=function(n,t,r){1>=arguments.length&&(t=n||0,n=0),r=arguments[2]||1;for(var e=Math.max(Math.ceil((t-n)/r),0),u=0,i=Array(e);e>u;)i[u++]=n,n+=r;return i};var I=function(){};w.bind=function(n,t){var r,e;if(n.bind===j&&j)return j.apply(n,o.call(arguments,1));if(!w.isFunction(n))throw new TypeError;return r=o.call(arguments,2),e=function(){if(!(this instanceof e))return n.apply(t,r.concat(o.call(arguments)));I.prototype=n.prototype;var u=new I;I.prototype=null;var i=n.apply(u,r.concat(o.call(arguments)));return Object(i)===i?i:u}},w.bindAll=function(n){var t=o.call(arguments,1);return 0==t.length&&(t=w.functions(n)),A(t,function(t){n[t]=w.bind(n[t],n)}),n},w.memoize=function(n,t){var r={};return t||(t=w.identity),function(){var e=t.apply(this,arguments);return w.has(r,e)?r[e]:r[e]=n.apply(this,arguments)}},w.delay=function(n,t){var r=o.call(arguments,2);return setTimeout(function(){return n.apply(null,r)},t)},w.defer=function(n){return w.delay.apply(w,[n,1].concat(o.call(arguments,1)))},w.throttle=function(n,t){var r,e,u,i,a=0,o=function(){a=new Date,u=null,i=n.apply(r,e)};return function(){var c=new Date,l=t-(c-a);return r=this,e=arguments,0>=l?(clearTimeout(u),u=null,a=c,i=n.apply(r,e)):u||(u=setTimeout(o,l)),i}},w.debounce=function(n,t,r){var e,u;return function(){var i=this,a=arguments,o=function(){e=null,r||(u=n.apply(i,a))},c=r&&!e;return clearTimeout(e),e=setTimeout(o,t),c&&(u=n.apply(i,a)),u}},w.once=function(n){var t,r=!1;return function(){return r?t:(r=!0,t=n.apply(this,arguments),n=null,t)}},w.wrap=function(n,t){return function(){var r=[n];return a.apply(r,arguments),t.apply(this,r)}},w.compose=function(){var n=arguments;return function(){for(var t=arguments,r=n.length-1;r>=0;r--)t=[n[r].apply(this,t)];return t[0]}},w.after=function(n,t){return 0>=n?t():function(){return 1>--n?t.apply(this,arguments):void 0}},w.keys=_||function(n){if(n!==Object(n))throw new TypeError("Invalid object");var t=[];for(var r in n)w.has(n,r)&&(t[t.length]=r);return t},w.values=function(n){var t=[];for(var r in n)w.has(n,r)&&t.push(n[r]);return t},w.pairs=function(n){var t=[];for(var r in n)w.has(n,r)&&t.push([r,n[r]]);return t},w.invert=function(n){var t={};for(var r in n)w.has(n,r)&&(t[n[r]]=r);return t},w.functions=w.methods=function(n){var t=[];for(var r in n)w.isFunction(n[r])&&t.push(r);return t.sort()},w.extend=function(n){return A(o.call(arguments,1),function(t){if(t)for(var r in t)n[r]=t[r]}),n},w.pick=function(n){var t={},r=c.apply(e,o.call(arguments,1));return A(r,function(r){r in n&&(t[r]=n[r])}),t},w.omit=function(n){var t={},r=c.apply(e,o.call(arguments,1));for(var u in n)w.contains(r,u)||(t[u]=n[u]);return t},w.defaults=function(n){return A(o.call(arguments,1),function(t){if(t)for(var r in t)null==n[r]&&(n[r]=t[r])}),n},w.clone=function(n){return w.isObject(n)?w.isArray(n)?n.slice():w.extend({},n):n},w.tap=function(n,t){return t(n),n};var S=function(n,t,r,e){if(n===t)return 0!==n||1/n==1/t;if(null==n||null==t)return n===t;n instanceof w&&(n=n._wrapped),t instanceof w&&(t=t._wrapped);var u=l.call(n);if(u!=l.call(t))return!1;switch(u){case"[object String]":return n==t+"";case"[object Number]":return n!=+n?t!=+t:0==n?1/n==1/t:n==+t;case"[object Date]":case"[object Boolean]":return+n==+t;case"[object RegExp]":return n.source==t.source&&n.global==t.global&&n.multiline==t.multiline&&n.ignoreCase==t.ignoreCase}if("object"!=typeof n||"object"!=typeof t)return!1;for(var i=r.length;i--;)if(r[i]==n)return e[i]==t;r.push(n),e.push(t);var a=0,o=!0;if("[object Array]"==u){if(a=n.length,o=a==t.length)for(;a--&&(o=S(n[a],t[a],r,e)););}else{var c=n.constructor,f=t.constructor;if(c!==f&&!(w.isFunction(c)&&c instanceof c&&w.isFunction(f)&&f instanceof f))return!1;for(var s in n)if(w.has(n,s)&&(a++,!(o=w.has(t,s)&&S(n[s],t[s],r,e))))break;if(o){for(s in t)if(w.has(t,s)&&!a--)break;o=!a}}return r.pop(),e.pop(),o};w.isEqual=function(n,t){return S(n,t,[],[])},w.isEmpty=function(n){if(null==n)return!0;if(w.isArray(n)||w.isString(n))return 0===n.length;for(var t in n)if(w.has(n,t))return!1;return!0},w.isElement=function(n){return!(!n||1!==n.nodeType)},w.isArray=x||function(n){return"[object Array]"==l.call(n)},w.isObject=function(n){return n===Object(n)},A(["Arguments","Function","String","Number","Date","RegExp"],function(n){w["is"+n]=function(t){return l.call(t)=="[object "+n+"]"}}),w.isArguments(arguments)||(w.isArguments=function(n){return!(!n||!w.has(n,"callee"))}),w.isFunction=function(n){return"function"==typeof n},w.isFinite=function(n){return isFinite(n)&&!isNaN(parseFloat(n))},w.isNaN=function(n){return w.isNumber(n)&&n!=+n},w.isBoolean=function(n){return n===!0||n===!1||"[object Boolean]"==l.call(n)},w.isNull=function(n){return null===n},w.isUndefined=function(n){return void 0===n},w.has=function(n,t){return f.call(n,t)},w.noConflict=function(){return n._=t,this},w.identity=function(n){return n},w.times=function(n,t,r){for(var e=Array(n),u=0;n>u;u++)e[u]=t.call(r,u);return e},w.random=function(n,t){return null==t&&(t=n,n=0),n+(0|Math.random()*(t-n+1))};var T={escape:{"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#x27;","/":"&#x2F;"}};T.unescape=w.invert(T.escape);var M={escape:RegExp("["+w.keys(T.escape).join("")+"]","g"),unescape:RegExp("("+w.keys(T.unescape).join("|")+")","g")};w.each(["escape","unescape"],function(n){w[n]=function(t){return null==t?"":(""+t).replace(M[n],function(t){return T[n][t]})}}),w.result=function(n,t){if(null==n)return null;var r=n[t];return w.isFunction(r)?r.call(n):r},w.mixin=function(n){A(w.functions(n),function(t){var r=w[t]=n[t];w.prototype[t]=function(){var n=[this._wrapped];return a.apply(n,arguments),z.call(this,r.apply(w,n))}})};var N=0;w.uniqueId=function(n){var t=""+ ++N;return n?n+t:t},w.templateSettings={evaluate:/<%([\s\S]+?)%>/g,interpolate:/<%=([\s\S]+?)%>/g,escape:/<%-([\s\S]+?)%>/g};var q=/(.)^/,B={"'":"'","\\":"\\","\r":"r","\n":"n","	":"t","\u2028":"u2028","\u2029":"u2029"},D=/\\|'|\r|\n|\t|\u2028|\u2029/g;w.template=function(n,t,r){r=w.defaults({},r,w.templateSettings);var e=RegExp([(r.escape||q).source,(r.interpolate||q).source,(r.evaluate||q).source].join("|")+"|$","g"),u=0,i="__p+='";n.replace(e,function(t,r,e,a,o){return i+=n.slice(u,o).replace(D,function(n){return"\\"+B[n]}),r&&(i+="'+\n((__t=("+r+"))==null?'':_.escape(__t))+\n'"),e&&(i+="'+\n((__t=("+e+"))==null?'':__t)+\n'"),a&&(i+="';\n"+a+"\n__p+='"),u=o+t.length,t}),i+="';\n",r.variable||(i="with(obj||{}){\n"+i+"}\n"),i="var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};\n"+i+"return __p;\n";try{var a=Function(r.variable||"obj","_",i)}catch(o){throw o.source=i,o}if(t)return a(t,w);var c=function(n){return a.call(this,n,w)};return c.source="function("+(r.variable||"obj")+"){\n"+i+"}",c},w.chain=function(n){return w(n).chain()};var z=function(n){return this._chain?w(n).chain():n};w.mixin(w),A(["pop","push","reverse","shift","sort","splice","unshift"],function(n){var t=e[n];w.prototype[n]=function(){var r=this._wrapped;return t.apply(r,arguments),"shift"!=n&&"splice"!=n||0!==r.length||delete r[0],z.call(this,r)}}),A(["concat","join","slice"],function(n){var t=e[n];w.prototype[n]=function(){return z.call(this,t.apply(this._wrapped,arguments))}}),w.extend(w.prototype,{chain:function(){return this._chain=!0,this},value:function(){return this._wrapped}})}).call(this);
/* /modernizr.custom.js */
/* hashchange and pushState */
/* Modernizr 2.6.2 (Custom Build) | MIT & BSD
 * Build: http://modernizr.com/download/#-hashchange-history-shiv-cssclasses-hasevent-load
 */
;



window.Modernizr = (function( window, document, undefined ) {

    var version = '2.6.2',

    Modernizr = {},

    enableClasses = true,

    docElement = document.documentElement,

    mod = 'modernizr',
    modElem = document.createElement(mod),
    mStyle = modElem.style,

    inputElem  ,


    toString = {}.toString,    tests = {},
    inputs = {},
    attrs = {},

    classes = [],

    slice = classes.slice,

    featureName,

    isEventSupported = (function() {

      var TAGNAMES = {
        'select': 'input', 'change': 'input',
        'submit': 'form', 'reset': 'form',
        'error': 'img', 'load': 'img', 'abort': 'img'
      };

      function isEventSupported( eventName, element ) {

        element = element || document.createElement(TAGNAMES[eventName] || 'div');
        eventName = 'on' + eventName;

            var isSupported = eventName in element;

        if ( !isSupported ) {
                if ( !element.setAttribute ) {
            element = document.createElement('div');
          }
          if ( element.setAttribute && element.removeAttribute ) {
            element.setAttribute(eventName, '');
            isSupported = is(element[eventName], 'function');

                    if ( !is(element[eventName], 'undefined') ) {
              element[eventName] = undefined;
            }
            element.removeAttribute(eventName);
          }
        }

        element = null;
        return isSupported;
      }
      return isEventSupported;
    })(),


    _hasOwnProperty = ({}).hasOwnProperty, hasOwnProp;

    if ( !is(_hasOwnProperty, 'undefined') && !is(_hasOwnProperty.call, 'undefined') ) {
      hasOwnProp = function (object, property) {
        return _hasOwnProperty.call(object, property);
      };
    }
    else {
      hasOwnProp = function (object, property) { 
        return ((property in object) && is(object.constructor.prototype[property], 'undefined'));
      };
    }


    if (!Function.prototype.bind) {
      Function.prototype.bind = function bind(that) {

        var target = this;

        if (typeof target != "function") {
            throw new TypeError();
        }

        var args = slice.call(arguments, 1),
            bound = function () {

            if (this instanceof bound) {

              var F = function(){};
              F.prototype = target.prototype;
              var self = new F();

              var result = target.apply(
                  self,
                  args.concat(slice.call(arguments))
              );
              if (Object(result) === result) {
                  return result;
              }
              return self;

            } else {

              return target.apply(
                  that,
                  args.concat(slice.call(arguments))
              );

            }

        };

        return bound;
      };
    }

    function setCss( str ) {
        mStyle.cssText = str;
    }

    function setCssAll( str1, str2 ) {
        return setCss(prefixes.join(str1 + ';') + ( str2 || '' ));
    }

    function is( obj, type ) {
        return typeof obj === type;
    }

    function contains( str, substr ) {
        return !!~('' + str).indexOf(substr);
    }


    function testDOMProps( props, obj, elem ) {
        for ( var i in props ) {
            var item = obj[props[i]];
            if ( item !== undefined) {

                            if (elem === false) return props[i];

                            if (is(item, 'function')){
                                return item.bind(elem || obj);
                }

                            return item;
            }
        }
        return false;
    }    tests['hashchange'] = function() {
      return isEventSupported('hashchange', window) && (document.documentMode === undefined || document.documentMode > 7);
    };

    tests['history'] = function() {
      return !!(window.history && history.pushState);
    };
    for ( var feature in tests ) {
        if ( hasOwnProp(tests, feature) ) {
                                    featureName  = feature.toLowerCase();
            Modernizr[featureName] = tests[feature]();

            classes.push((Modernizr[featureName] ? '' : 'no-') + featureName);
        }
    }



     Modernizr.addTest = function ( feature, test ) {
       if ( typeof feature == 'object' ) {
         for ( var key in feature ) {
           if ( hasOwnProp( feature, key ) ) {
             Modernizr.addTest( key, feature[ key ] );
           }
         }
       } else {

         feature = feature.toLowerCase();

         if ( Modernizr[feature] !== undefined ) {
                                              return Modernizr;
         }

         test = typeof test == 'function' ? test() : test;

         if (typeof enableClasses !== "undefined" && enableClasses) {
           docElement.className += ' ' + (test ? '' : 'no-') + feature;
         }
         Modernizr[feature] = test;

       }

       return Modernizr; 
     };


    setCss('');
    modElem = inputElem = null;

    ;(function(window, document) {
        var options = window.html5 || {};

        var reSkip = /^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i;

        var saveClones = /^(?:a|b|code|div|fieldset|h1|h2|h3|h4|h5|h6|i|label|li|ol|p|q|span|strong|style|table|tbody|td|th|tr|ul)$/i;

        var supportsHtml5Styles;

        var expando = '_html5shiv';

        var expanID = 0;

        var expandoData = {};

        var supportsUnknownElements;

      (function() {
        try {
            var a = document.createElement('a');
            a.innerHTML = '<xyz></xyz>';
                    supportsHtml5Styles = ('hidden' in a);

            supportsUnknownElements = a.childNodes.length == 1 || (function() {
                        (document.createElement)('a');
              var frag = document.createDocumentFragment();
              return (
                typeof frag.cloneNode == 'undefined' ||
                typeof frag.createDocumentFragment == 'undefined' ||
                typeof frag.createElement == 'undefined'
              );
            }());
        } catch(e) {
          supportsHtml5Styles = true;
          supportsUnknownElements = true;
        }

      }());        function addStyleSheet(ownerDocument, cssText) {
        var p = ownerDocument.createElement('p'),
            parent = ownerDocument.getElementsByTagName('head')[0] || ownerDocument.documentElement;

        p.innerHTML = 'x<style>' + cssText + '</style>';
        return parent.insertBefore(p.lastChild, parent.firstChild);
      }

        function getElements() {
        var elements = html5.elements;
        return typeof elements == 'string' ? elements.split(' ') : elements;
      }

          function getExpandoData(ownerDocument) {
        var data = expandoData[ownerDocument[expando]];
        if (!data) {
            data = {};
            expanID++;
            ownerDocument[expando] = expanID;
            expandoData[expanID] = data;
        }
        return data;
      }

        function createElement(nodeName, ownerDocument, data){
        if (!ownerDocument) {
            ownerDocument = document;
        }
        if(supportsUnknownElements){
            return ownerDocument.createElement(nodeName);
        }
        if (!data) {
            data = getExpandoData(ownerDocument);
        }
        var node;

        if (data.cache[nodeName]) {
            node = data.cache[nodeName].cloneNode();
        } else if (saveClones.test(nodeName)) {
            node = (data.cache[nodeName] = data.createElem(nodeName)).cloneNode();
        } else {
            node = data.createElem(nodeName);
        }

                                    return node.canHaveChildren && !reSkip.test(nodeName) ? data.frag.appendChild(node) : node;
      }

        function createDocumentFragment(ownerDocument, data){
        if (!ownerDocument) {
            ownerDocument = document;
        }
        if(supportsUnknownElements){
            return ownerDocument.createDocumentFragment();
        }
        data = data || getExpandoData(ownerDocument);
        var clone = data.frag.cloneNode(),
            i = 0,
            elems = getElements(),
            l = elems.length;
        for(;i<l;i++){
            clone.createElement(elems[i]);
        }
        return clone;
      }

        function shivMethods(ownerDocument, data) {
        if (!data.cache) {
            data.cache = {};
            data.createElem = ownerDocument.createElement;
            data.createFrag = ownerDocument.createDocumentFragment;
            data.frag = data.createFrag();
        }


        ownerDocument.createElement = function(nodeName) {
                if (!html5.shivMethods) {
              return data.createElem(nodeName);
          }
          return createElement(nodeName, ownerDocument, data);
        };

        ownerDocument.createDocumentFragment = Function('h,f', 'return function(){' +
          'var n=f.cloneNode(),c=n.createElement;' +
          'h.shivMethods&&(' +
                    getElements().join().replace(/\w+/g, function(nodeName) {
              data.createElem(nodeName);
              data.frag.createElement(nodeName);
              return 'c("' + nodeName + '")';
            }) +
          ');return n}'
        )(html5, data.frag);
      }        function shivDocument(ownerDocument) {
        if (!ownerDocument) {
            ownerDocument = document;
        }
        var data = getExpandoData(ownerDocument);

        if (html5.shivCSS && !supportsHtml5Styles && !data.hasCSS) {
          data.hasCSS = !!addStyleSheet(ownerDocument,
                    'article,aside,figcaption,figure,footer,header,hgroup,nav,section{display:block}' +
                    'mark{background:#FF0;color:#000}'
          );
        }
        if (!supportsUnknownElements) {
          shivMethods(ownerDocument, data);
        }
        return ownerDocument;
      }        var html5 = {

            'elements': options.elements || 'abbr article aside audio bdi canvas data datalist details figcaption figure footer header hgroup mark meter nav output progress section summary time video',

            'shivCSS': (options.shivCSS !== false),

            'supportsUnknownElements': supportsUnknownElements,

            'shivMethods': (options.shivMethods !== false),

            'type': 'default',

            'shivDocument': shivDocument,

            createElement: createElement,

            createDocumentFragment: createDocumentFragment
      };        window.html5 = html5;

        shivDocument(document);

    }(this, document));

    Modernizr._version      = version;



    Modernizr.hasEvent      = isEventSupported;



    docElement.className = docElement.className.replace(/(^|\s)no-js(\s|$)/, '$1$2') +

                                                    (enableClasses ? ' js ' + classes.join(' ') : '');

    return Modernizr;

})(this, this.document);
/*yepnope1.5.4|WTFPL*/
(function(a,b,c){function d(a){return"[object Function]"==o.call(a)}function e(a){return"string"==typeof a}function f(){}function g(a){return!a||"loaded"==a||"complete"==a||"uninitialized"==a}function h(){var a=p.shift();q=1,a?a.t?m(function(){("c"==a.t?B.injectCss:B.injectJs)(a.s,0,a.a,a.x,a.e,1)},0):(a(),h()):q=0}function i(a,c,d,e,f,i,j){function k(b){if(!o&&g(l.readyState)&&(u.r=o=1,!q&&h(),l.onload=l.onreadystatechange=null,b)){"img"!=a&&m(function(){t.removeChild(l)},50);for(var d in y[c])y[c].hasOwnProperty(d)&&y[c][d].onload()}}var j=j||B.errorTimeout,l=b.createElement(a),o=0,r=0,u={t:d,s:c,e:f,a:i,x:j};1===y[c]&&(r=1,y[c]=[]),"object"==a?l.data=c:(l.src=c,l.type=a),l.width=l.height="0",l.onerror=l.onload=l.onreadystatechange=function(){k.call(this,r)},p.splice(e,0,u),"img"!=a&&(r||2===y[c]?(t.insertBefore(l,s?null:n),m(k,j)):y[c].push(l))}function j(a,b,c,d,f){return q=0,b=b||"j",e(a)?i("c"==b?v:u,a,b,this.i++,c,d,f):(p.splice(this.i++,0,a),1==p.length&&h()),this}function k(){var a=B;return a.loader={load:j,i:0},a}var l=b.documentElement,m=a.setTimeout,n=b.getElementsByTagName("script")[0],o={}.toString,p=[],q=0,r="MozAppearance"in l.style,s=r&&!!b.createRange().compareNode,t=s?l:n.parentNode,l=a.opera&&"[object Opera]"==o.call(a.opera),l=!!b.attachEvent&&!l,u=r?"object":l?"script":"img",v=l?"script":u,w=Array.isArray||function(a){return"[object Array]"==o.call(a)},x=[],y={},z={timeout:function(a,b){return b.length&&(a.timeout=b[0]),a}},A,B;B=function(a){function b(a){var a=a.split("!"),b=x.length,c=a.pop(),d=a.length,c={url:c,origUrl:c,prefixes:a},e,f,g;for(f=0;f<d;f++)g=a[f].split("="),(e=z[g.shift()])&&(c=e(c,g));for(f=0;f<b;f++)c=x[f](c);return c}function g(a,e,f,g,h){var i=b(a),j=i.autoCallback;i.url.split(".").pop().split("?").shift(),i.bypass||(e&&(e=d(e)?e:e[a]||e[g]||e[a.split("/").pop().split("?")[0]]),i.instead?i.instead(a,e,f,g,h):(y[i.url]?i.noexec=!0:y[i.url]=1,f.load(i.url,i.forceCSS||!i.forceJS&&"css"==i.url.split(".").pop().split("?").shift()?"c":c,i.noexec,i.attrs,i.timeout),(d(e)||d(j))&&f.load(function(){k(),e&&e(i.origUrl,h,g),j&&j(i.origUrl,h,g),y[i.url]=2})))}function h(a,b){function c(a,c){if(a){if(e(a))c||(j=function(){var a=[].slice.call(arguments);k.apply(this,a),l()}),g(a,j,b,0,h);else if(Object(a)===a)for(n in m=function(){var b=0,c;for(c in a)a.hasOwnProperty(c)&&b++;return b}(),a)a.hasOwnProperty(n)&&(!c&&!--m&&(d(j)?j=function(){var a=[].slice.call(arguments);k.apply(this,a),l()}:j[n]=function(a){return function(){var b=[].slice.call(arguments);a&&a.apply(this,b),l()}}(k[n])),g(a[n],j,b,n,h))}else!c&&l()}var h=!!a.test,i=a.load||a.both,j=a.callback||f,k=j,l=a.complete||f,m,n;c(h?a.yep:a.nope,!!i),i&&c(i)}var i,j,l=this.yepnope.loader;if(e(a))g(a,0,l,0);else if(w(a))for(i=0;i<a.length;i++)j=a[i],e(j)?g(j,0,l,0):w(j)?B(j):Object(j)===j&&h(j,l);else Object(a)===a&&h(a,l)},B.addPrefix=function(a,b){z[a]=b},B.addFilter=function(a){x.push(a)},B.errorTimeout=1e4,null==b.readyState&&b.addEventListener&&(b.readyState="loading",b.addEventListener("DOMContentLoaded",A=function(){b.removeEventListener("DOMContentLoaded",A,0),b.readyState="complete"},0)),a.yepnope=k(),a.yepnope.executeStack=h,a.yepnope.injectJs=function(a,c,d,e,i,j){var k=b.createElement("script"),l,o,e=e||B.errorTimeout;k.src=a;for(o in d)k.setAttribute(o,d[o]);c=j?h:c||f,k.onreadystatechange=k.onload=function(){!l&&g(k.readyState)&&(l=1,c(),k.onload=k.onreadystatechange=null)},m(function(){l||(l=1,c(1))},e),i?k.onload():n.parentNode.insertBefore(k,n)},a.yepnope.injectCss=function(a,c,d,e,g,i){var e=b.createElement("link"),j,c=i?h:c||f;e.href=a,e.rel="stylesheet",e.type="text/css";for(j in d)e.setAttribute(j,d[j]);g||(n.parentNode.insertBefore(e,n),m(c,0))}})(this,document);
Modernizr.load=function(){yepnope.apply(window,[].slice.call(arguments,0));};
;

/* /backbone.js */
//     Backbone.js 0.9.9-pre

//     (c) 2010-2012 Jeremy Ashkenas, DocumentCloud Inc.
//     Backbone may be freely distributed under the MIT license.
//     For all details and documentation:
//     http://backbonejs.org

(function(){

  // Initial Setup
  // -------------

  // Save a reference to the global object (`window` in the browser, `exports`
  // on the server).
  var root = this;

  // Save the previous value of the `Backbone` variable, so that it can be
  // restored later on, if `noConflict` is used.
  var previousBackbone = root.Backbone;

  // Create a local reference to array methods.
  var array = [];
  var push = array.push;
  var slice = array.slice;
  var splice = array.splice;

  // The top-level namespace. All public Backbone classes and modules will
  // be attached to this. Exported for both CommonJS and the browser.
  var Backbone;
  if (typeof exports !== 'undefined') {
    Backbone = exports;
  } else {
    Backbone = root.Backbone = {};
  }

  // Current version of the library. Keep in sync with `package.json`.
  Backbone.VERSION = '0.9.9-pre';

  // Require Underscore, if we're on the server, and it's not already present.
  var _ = root._;
  if (!_ && (typeof require !== 'undefined')) _ = require('underscore');

  // For Backbone's purposes, jQuery, Zepto, or Ender owns the `$` variable.
  Backbone.$ = root.jQuery || root.Zepto || root.ender;

  // Runs Backbone.js in *noConflict* mode, returning the `Backbone` variable
  // to its previous owner. Returns a reference to this Backbone object.
  Backbone.noConflict = function() {
    root.Backbone = previousBackbone;
    return this;
  };

  // Turn on `emulateHTTP` to support legacy HTTP servers. Setting this option
  // will fake `"PUT"` and `"DELETE"` requests via the `_method` parameter and
  // set a `X-Http-Method-Override` header.
  Backbone.emulateHTTP = false;

  // Turn on `emulateJSON` to support legacy servers that can't deal with direct
  // `application/json` requests ... will encode the body as
  // `application/x-www-form-urlencoded` instead and will send the model in a
  // form param named `model`.
  Backbone.emulateJSON = false;

  // Backbone.Events
  // ---------------

  // Regular expression used to split event strings.
  var eventSplitter = /\s+/;

  // Implement fancy features of the Events API such as multiple event
  // names `"change blur"` and jQuery-style event maps `{change: action}`
  // in terms of the existing API.
  var eventsApi = function(obj, action, name, rest) {
    if (!name) return true;
    if (typeof name === 'object') {
      for (var key in name) {
        obj[action].apply(obj, [key, name[key]].concat(rest));
      }
    } else if (eventSplitter.test(name)) {
      var names = name.split(eventSplitter);
      for (var i = 0, l = names.length; i < l; i++) {
        obj[action].apply(obj, [names[i]].concat(rest));
      }
    } else {
      return true;
    }
  };

  // Optimized internal dispatch function for triggering events. Tries to
  // keep the usual cases speedy (most Backbone events have 3 arguments).
  var triggerEvents = function(obj, events, args) {
    for (var i = 0, l = events.length; i < l; i++) {
      var ev = events[i], callback = ev.callback, context = ev.context || obj;
      switch (args.length) {
        case 0:
          callback.call(context);
          break;
        case 1:
          callback.call(context, args[0]);
          break;
        case 2:
          callback.call(context, args[0], args[1]);
          break;
        case 3:
          callback.call(context, args[0], args[1], args[2]);
          break;
        default:
          callback.apply(context, args);
      }
    }
  };

  // A module that can be mixed in to *any object* in order to provide it with
  // custom events. You may bind with `on` or remove with `off` callback
  // functions to an event; `trigger`-ing an event fires all callbacks in
  // succession.
  //
  //     var object = {};
  //     _.extend(object, Backbone.Events);
  //     object.on('expand', function(){ alert('expanded'); });
  //     object.trigger('expand');
  //
  var Events = Backbone.Events = {

    // Bind one or more space separated events, or an events map,
    // to a `callback` function. Passing `"all"` will bind the callback to
    // all events fired.
    on: function(name, callback, context) {
      if (!(eventsApi(this, 'on', name, [callback, context]) && callback)) return this;
      this._events || (this._events = {});
      var list = this._events[name] || (this._events[name] = []);
      list.push({callback: callback, context: context});
      return this;
    },

    // Bind events to only be triggered a single time. After the first time
    // the callback is invoked, it will be removed.
    once: function(name, callback, context) {
      if (!(eventsApi(this, 'once', name, [callback, context]) && callback)) return this;
      var self = this;
      var once = _.once(function() {
        self.off(name, once);
        callback.apply(this, arguments);
      });
      once._callback = callback;
      this.on(name, once, context);
      return this;
    },

    // Remove one or many callbacks. If `context` is null, removes all
    // callbacks with that function. If `callback` is null, removes all
    // callbacks for the event. If `events` is null, removes all bound
    // callbacks for all events.
    off: function(name, callback, context) {
      var list, ev, events, names, i, l, j, k;
      if (!this._events || !eventsApi(this, 'off', name, [callback, context])) return this;
      if (!name && !callback && !context) {
        this._events = {};
        return this;
      }

      names = name ? [name] : _.keys(this._events);
      for (i = 0, l = names.length; i < l; i++) {
        name = names[i];
        if (list = this._events[name]) {
          events = [];
          if (callback || context) {
            for (j = 0, k = list.length; j < k; j++) {
              ev = list[j];
              if ((callback && callback !== (ev.callback._callback || ev.callback)) ||
                  (context && context !== ev.context)) {
                events.push(ev);
              }
            }
          }
          this._events[name] = events;
        }
      }

      return this;
    },

    // Trigger one or many events, firing all bound callbacks. Callbacks are
    // passed the same arguments as `trigger` is, apart from the event name
    // (unless you're listening on `"all"`, which will cause your callback to
    // receive the true name of the event as the first argument).
    trigger: function(name) {
      if (!this._events) return this;
      var args = slice.call(arguments, 1);
      if (!eventsApi(this, 'trigger', name, args)) return this;
      var events = this._events[name];
      var allEvents = this._events.all;
      if (events) triggerEvents(this, events, args);
      if (allEvents) triggerEvents(this, allEvents, arguments);
      return this;
    },

    // An inversion-of-control version of `on`. Tell *this* object to listen to
    // an event in another object ... keeping track of what it's listening to.
    listenTo: function(object, events, callback) {
      var listeners = this._listeners || (this._listeners = {});
      var id = object._listenerId || (object._listenerId = _.uniqueId('l'));
      listeners[id] = object;
      object.on(events, callback || this, this);
      return this;
    },

    // Tell this object to stop listening to either specific events ... or
    // to every object it's currently listening to.
    stopListening: function(object, events, callback) {
      var listeners = this._listeners;
      if (!listeners) return;
      if (object) {
        object.off(events, callback, this);
        if (!events && !callback) delete listeners[object._listenerId];
      } else {
        for (var id in listeners) {
          listeners[id].off(null, null, this);
        }
        this._listeners = {};
      }
      return this;
    }
  };

  // Aliases for backwards compatibility.
  Events.bind   = Events.on;
  Events.unbind = Events.off;

  // Allow the `Backbone` object to serve as a global event bus, for folks who
  // want global "pubsub" in a convenient place.
  _.extend(Backbone, Events);

  // Backbone.Model
  // --------------

  // Create a new model, with defined attributes. A client id (`cid`)
  // is automatically generated and assigned for you.
  var Model = Backbone.Model = function(attributes, options) {
    var defaults;
    var attrs = attributes || {};
    this.cid = _.uniqueId('c');
    this.changed = {};
    this.attributes = {};
    this._changes = [];
    if (options && options.collection) this.collection = options.collection;
    if (options && options.parse) attrs = this.parse(attrs);
    if (defaults = _.result(this, 'defaults')) {
      attrs = _.defaults({}, defaults, attrs);
    }
    this.set(attrs, {silent: true});
    this._currentAttributes = _.clone(this.attributes);
    this._previousAttributes = _.clone(this.attributes);
    this.initialize.apply(this, arguments);
  };

  // Attach all inheritable methods to the Model prototype.
  _.extend(Model.prototype, Events, {

    // A hash of attributes whose current and previous value differ.
    changed: null,

    // The default name for the JSON `id` attribute is `"id"`. MongoDB and
    // CouchDB users may want to set this to `"_id"`.
    idAttribute: 'id',

    // Initialize is an empty function by default. Override it with your own
    // initialization logic.
    initialize: function(){},

    // Return a copy of the model's `attributes` object.
    toJSON: function(options) {
      return _.clone(this.attributes);
    },

    // Proxy `Backbone.sync` by default.
    sync: function() {
      return Backbone.sync.apply(this, arguments);
    },

    // Get the value of an attribute.
    get: function(attr) {
      return this.attributes[attr];
    },

    // Get the HTML-escaped value of an attribute.
    escape: function(attr) {
      return _.escape(this.get(attr));
    },

    // Returns `true` if the attribute contains a value that is not null
    // or undefined.
    has: function(attr) {
      return this.get(attr) != null;
    },

    // Set a hash of model attributes on the object, firing `"change"` unless
    // you choose to silence it.
    set: function(key, val, options) {
      var attr, attrs;
      if (key == null) return this;

      // Handle both `"key", value` and `{key: value}` -style arguments.
      if (_.isObject(key)) {
        attrs = key;
        options = val;
      } else {
        (attrs = {})[key] = val;
      }

      // Extract attributes and options.
      var silent = options && options.silent;
      var unset = options && options.unset;

      // Run validation.
      if (!this._validate(attrs, options)) return false;

      // Check for changes of `id`.
      if (this.idAttribute in attrs) this.id = attrs[this.idAttribute];

      var now = this.attributes;

      // For each `set` attribute...
      for (attr in attrs) {
        val = attrs[attr];

        // Update or delete the current value, and track the change.
        unset ? delete now[attr] : now[attr] = val;
        this._changes.push(attr, val);
      }

      // Signal that the model's state has potentially changed, and we need
      // to recompute the actual changes.
      this._hasComputed = false;

      // Fire the `"change"` events.
      if (!silent) this.change(options);
      return this;
    },

    // Remove an attribute from the model, firing `"change"` unless you choose
    // to silence it. `unset` is a noop if the attribute doesn't exist.
    unset: function(attr, options) {
      return this.set(attr, void 0, _.extend({}, options, {unset: true}));
    },

    // Clear all attributes on the model, firing `"change"` unless you choose
    // to silence it.
    clear: function(options) {
      var attrs = {};
      for (var key in this.attributes) attrs[key] = void 0;
      return this.set(attrs, _.extend({}, options, {unset: true}));
    },

    // Fetch the model from the server. If the server's representation of the
    // model differs from its current attributes, they will be overriden,
    // triggering a `"change"` event.
    fetch: function(options) {
      options = options ? _.clone(options) : {};
      if (options.parse === void 0) options.parse = true;
      var model = this;
      var success = options.success;
      options.success = function(resp, status, xhr) {
        if (!model.set(model.parse(resp), options)) return false;
        if (success) success(model, resp, options);
      };
      return this.sync('read', this, options);
    },

    // Set a hash of model attributes, and sync the model to the server.
    // If the server returns an attributes hash that differs, the model's
    // state will be `set` again.
    save: function(key, val, options) {
      var attrs, current, done;

      // Handle both `"key", value` and `{key: value}` -style arguments.
      if (key == null || _.isObject(key)) {
        attrs = key;
        options = val;
      } else if (key != null) {
        (attrs = {})[key] = val;
      }
      options = options ? _.clone(options) : {};

      // If we're "wait"-ing to set changed attributes, validate early.
      if (options.wait) {
        if (attrs && !this._validate(attrs, options)) return false;
        current = _.clone(this.attributes);
      }

      // Regular saves `set` attributes before persisting to the server.
      var silentOptions = _.extend({}, options, {silent: true});
      if (attrs && !this.set(attrs, options.wait ? silentOptions : options)) {
        return false;
      }

      // Do not persist invalid models.
      if (!attrs && !this._validate(null, options)) return false;

      // After a successful server-side save, the client is (optionally)
      // updated with the server-side state.
      var model = this;
      var success = options.success;
      options.success = function(resp, status, xhr) {
        done = true;
        var serverAttrs = model.parse(resp);
        if (options.wait) serverAttrs = _.extend(attrs || {}, serverAttrs);
        if (!model.set(serverAttrs, options)) return false;
        if (success) success(model, resp, options);
      };

      // Finish configuring and sending the Ajax request.
      var method = this.isNew() ? 'create' : (options.patch ? 'patch' : 'update');
      if (method == 'patch') options.attrs = attrs;
      var xhr = this.sync(method, this, options);

      // When using `wait`, reset attributes to original values unless
      // `success` has been called already.
      if (!done && options.wait) {
        this.clear(silentOptions);
        this.set(current, silentOptions);
      }

      return xhr;
    },

    // Destroy this model on the server if it was already persisted.
    // Optimistically removes the model from its collection, if it has one.
    // If `wait: true` is passed, waits for the server to respond before removal.
    destroy: function(options) {
      options = options ? _.clone(options) : {};
      var model = this;
      var success = options.success;

      var destroy = function() {
        model.trigger('destroy', model, model.collection, options);
      };

      options.success = function(resp) {
        if (options.wait || model.isNew()) destroy();
        if (success) success(model, resp, options);
      };

      if (this.isNew()) {
        options.success();
        return false;
      }

      var xhr = this.sync('delete', this, options);
      if (!options.wait) destroy();
      return xhr;
    },

    // Default URL for the model's representation on the server -- if you're
    // using Backbone's restful methods, override this to change the endpoint
    // that will be called.
    url: function() {
      var base = _.result(this, 'urlRoot') || _.result(this.collection, 'url') || urlError();
      if (this.isNew()) return base;
      return base + (base.charAt(base.length - 1) === '/' ? '' : '/') + encodeURIComponent(this.id);
    },

    // **parse** converts a response into the hash of attributes to be `set` on
    // the model. The default implementation is just to pass the response along.
    parse: function(resp) {
      return resp;
    },

    // Create a new model with identical attributes to this one.
    clone: function() {
      return new this.constructor(this.attributes);
    },

    // A model is new if it has never been saved to the server, and lacks an id.
    isNew: function() {
      return this.id == null;
    },

    // Call this method to manually fire a `"change"` event for this model and
    // a `"change:attribute"` event for each changed attribute.
    // Calling this will cause all objects observing the model to update.
    change: function(options) {
      var changing = this._changing;
      this._changing = true;

      // Generate the changes to be triggered on the model.
      var triggers = this._computeChanges(true);

      this._pending = !!triggers.length;

      for (var i = triggers.length - 2; i >= 0; i -= 2) {
        this.trigger('change:' + triggers[i], this, triggers[i + 1], options);
      }

      if (changing) return this;

      // Trigger a `change` while there have been changes.
      while (this._pending) {
        this._pending = false;
        this.trigger('change', this, options);
        this._previousAttributes = _.clone(this.attributes);
      }

      this._changing = false;
      return this;
    },

    // Determine if the model has changed since the last `"change"` event.
    // If you specify an attribute name, determine if that attribute has changed.
    hasChanged: function(attr) {
      if (!this._hasComputed) this._computeChanges();
      if (attr == null) return !_.isEmpty(this.changed);
      return _.has(this.changed, attr);
    },

    // Return an object containing all the attributes that have changed, or
    // false if there are no changed attributes. Useful for determining what
    // parts of a view need to be updated and/or what attributes need to be
    // persisted to the server. Unset attributes will be set to undefined.
    // You can also pass an attributes object to diff against the model,
    // determining if there *would be* a change.
    changedAttributes: function(diff) {
      if (!diff) return this.hasChanged() ? _.clone(this.changed) : false;
      var val, changed = false, old = this._previousAttributes;
      for (var attr in diff) {
        if (_.isEqual(old[attr], (val = diff[attr]))) continue;
        (changed || (changed = {}))[attr] = val;
      }
      return changed;
    },

    // Looking at the built up list of `set` attribute changes, compute how
    // many of the attributes have actually changed. If `loud`, return a
    // boiled-down list of only the real changes.
    _computeChanges: function(loud) {
      this.changed = {};
      var already = {};
      var triggers = [];
      var current = this._currentAttributes;
      var changes = this._changes;

      // Loop through the current queue of potential model changes.
      for (var i = changes.length - 2; i >= 0; i -= 2) {
        var key = changes[i], val = changes[i + 1];
        if (already[key]) continue;
        already[key] = true;

        // Check if the attribute has been modified since the last change,
        // and update `this.changed` accordingly. If we're inside of a `change`
        // call, also add a trigger to the list.
        if (current[key] !== val) {
          this.changed[key] = val;
          if (!loud) continue;
          triggers.push(key, val);
          current[key] = val;
        }
      }
      if (loud) this._changes = [];

      // Signals `this.changed` is current to prevent duplicate calls from `this.hasChanged`.
      this._hasComputed = true;
      return triggers;
    },

    // Get the previous value of an attribute, recorded at the time the last
    // `"change"` event was fired.
    previous: function(attr) {
      if (attr == null || !this._previousAttributes) return null;
      return this._previousAttributes[attr];
    },

    // Get all of the attributes of the model at the time of the previous
    // `"change"` event.
    previousAttributes: function() {
      return _.clone(this._previousAttributes);
    },

    // Check if the model is currently in a valid state. It's only possible to
    // get into an *invalid* state if you're using silent changes.
    isValid: function(options) {
      return !this.validate || !this.validate(this.attributes, options);
    },

    // Run validation against the next complete set of model attributes,
    // returning `true` if all is well. If a specific `error` callback has
    // been passed, call that instead of firing the general `"error"` event.
    _validate: function(attrs, options) {
      if (!this.validate) return true;
      attrs = _.extend({}, this.attributes, attrs);
      var error = this.validate(attrs, options);
      if (!error) return true;
      if (options && options.error) options.error(this, error, options);
      this.trigger('error', this, error, options);
      return false;
    }

  });

  // Backbone.Collection
  // -------------------

  // Provides a standard collection class for our sets of models, ordered
  // or unordered. If a `comparator` is specified, the Collection will maintain
  // its models in sort order, as they're added and removed.
  var Collection = Backbone.Collection = function(models, options) {
    options || (options = {});
    if (options.model) this.model = options.model;
    if (options.comparator !== void 0) this.comparator = options.comparator;
    this._reset();
    this.initialize.apply(this, arguments);
    if (models) this.reset(models, _.extend({silent: true}, options));
  };

  // Define the Collection's inheritable methods.
  _.extend(Collection.prototype, Events, {

    // The default model for a collection is just a **Backbone.Model**.
    // This should be overridden in most cases.
    model: Model,

    // Initialize is an empty function by default. Override it with your own
    // initialization logic.
    initialize: function(){},

    // The JSON representation of a Collection is an array of the
    // models' attributes.
    toJSON: function(options) {
      return this.map(function(model){ return model.toJSON(options); });
    },

    // Proxy `Backbone.sync` by default.
    sync: function() {
      return Backbone.sync.apply(this, arguments);
    },

    // Add a model, or list of models to the set. Pass **silent** to avoid
    // firing the `add` event for every new model.
    add: function(models, options) {
      var i, args, length, model, existing, needsSort;
      var at = options && options.at;
      var sort = ((options && options.sort) == null ? true : options.sort);
      models = _.isArray(models) ? models.slice() : [models];

      // Turn bare objects into model references, and prevent invalid models
      // from being added.
      for (i = models.length - 1; i >= 0; i--) {
        if(!(model = this._prepareModel(models[i], options))) {
          this.trigger("error", this, models[i], options);
          models.splice(i, 1);
          continue;
        }
        models[i] = model;

        existing = model.id != null && this._byId[model.id];
        // If a duplicate is found, prevent it from being added and
        // optionally merge it into the existing model.
        if (existing || this._byCid[model.cid]) {
          if (options && options.merge && existing) {
            existing.set(model.attributes, options);
            needsSort = sort;
          }
          models.splice(i, 1);
          continue;
        }

        // Listen to added models' events, and index models for lookup by
        // `id` and by `cid`.
        model.on('all', this._onModelEvent, this);
        this._byCid[model.cid] = model;
        if (model.id != null) this._byId[model.id] = model;
      }

      // See if sorting is needed, update `length` and splice in new models.
      if (models.length) needsSort = sort;
      this.length += models.length;
      args = [at != null ? at : this.models.length, 0];
      push.apply(args, models);
      splice.apply(this.models, args);

      // Sort the collection if appropriate.
      if (needsSort && this.comparator && at == null) this.sort({silent: true});

      if (options && options.silent) return this;

      // Trigger `add` events.
      while (model = models.shift()) {
        model.trigger('add', model, this, options);
      }

      return this;
    },

    // Remove a model, or a list of models from the set. Pass silent to avoid
    // firing the `remove` event for every model removed.
    remove: function(models, options) {
      var i, l, index, model;
      options || (options = {});
      models = _.isArray(models) ? models.slice() : [models];
      for (i = 0, l = models.length; i < l; i++) {
        model = this.get(models[i]);
        if (!model) continue;
        delete this._byId[model.id];
        delete this._byCid[model.cid];
        index = this.indexOf(model);
        this.models.splice(index, 1);
        this.length--;
        if (!options.silent) {
          options.index = index;
          model.trigger('remove', model, this, options);
        }
        this._removeReference(model);
      }
      return this;
    },

    // Add a model to the end of the collection.
    push: function(model, options) {
      model = this._prepareModel(model, options);
      this.add(model, _.extend({at: this.length}, options));
      return model;
    },

    // Remove a model from the end of the collection.
    pop: function(options) {
      var model = this.at(this.length - 1);
      this.remove(model, options);
      return model;
    },

    // Add a model to the beginning of the collection.
    unshift: function(model, options) {
      model = this._prepareModel(model, options);
      this.add(model, _.extend({at: 0}, options));
      return model;
    },

    // Remove a model from the beginning of the collection.
    shift: function(options) {
      var model = this.at(0);
      this.remove(model, options);
      return model;
    },

    // Slice out a sub-array of models from the collection.
    slice: function(begin, end) {
      return this.models.slice(begin, end);
    },

    // Get a model from the set by id.
    get: function(obj) {
      if (obj == null) return void 0;
      return this._byId[obj.id != null ? obj.id : obj] || this._byCid[obj.cid || obj];
    },

    // Get the model at the given index.
    at: function(index) {
      return this.models[index];
    },

    // Return models with matching attributes. Useful for simple cases of `filter`.
    where: function(attrs) {
      if (_.isEmpty(attrs)) return [];
      return this.filter(function(model) {
        for (var key in attrs) {
          if (attrs[key] !== model.get(key)) return false;
        }
        return true;
      });
    },

    // Force the collection to re-sort itself. You don't need to call this under
    // normal circumstances, as the set will maintain sort order as each item
    // is added.
    sort: function(options) {
      if (!this.comparator) {
        throw new Error('Cannot sort a set without a comparator');
      }

      if (_.isString(this.comparator) || this.comparator.length === 1) {
        this.models = this.sortBy(this.comparator, this);
      } else {
        this.models.sort(_.bind(this.comparator, this));
      }

      if (!options || !options.silent) this.trigger('sort', this, options);
      return this;
    },

    // Pluck an attribute from each model in the collection.
    pluck: function(attr) {
      return _.invoke(this.models, 'get', attr);
    },

    // Smartly update a collection with a change set of models, adding,
    // removing, and merging as necessary.
    update: function(models, options) {
      var model, i, l, existing;
      var add = [], remove = [], modelMap = {};
      var idAttr = this.model.prototype.idAttribute;
      options = _.extend({add: true, merge: true, remove: true}, options);

      // Allow a single model (or no argument) to be passed.
      if (!_.isArray(models)) models = models ? [models] : [];
      if (options.parse) models = this.parse(models);

      // Proxy to `add` for this case, no need to iterate...
      if (options.add && !options.remove) return this.add(models, options);

      // Determine which models to add and merge, and which to remove.
      for (i = 0, l = models.length; i < l; i++) {
        model = models[i];
        existing = this.get(model.id || model.cid || model[idAttr]);
        if (options.remove && existing) modelMap[existing.cid] = true;
        if ((options.add && !existing) || (options.merge && existing)) {
          add.push(model);
        }
      }
      if (options.remove) {
        for (i = 0, l = this.models.length; i < l; i++) {
          model = this.models[i];
          if (!modelMap[model.cid]) remove.push(model);
        }
      }

      // Remove models (if applicable) before we add and merge the rest.
      if (remove.length) this.remove(remove, options);
      if (add.length) this.add(add, options);
      return this;
    },

    // When you have more items than you want to add or remove individually,
    // you can reset the entire set with a new list of models, without firing
    // any `add` or `remove` events. Fires `reset` when finished.
    reset: function(models, options) {
      options || (options = {});
      if (options.parse) models = this.parse(models);
      for (var i = 0, l = this.models.length; i < l; i++) {
        this._removeReference(this.models[i]);
      }
      options.previousModels = this.models;
      this._reset();
      if (models) this.add(models, _.extend({silent: true}, options));
      if (!options.silent) this.trigger('reset', this, options);
      return this;
    },

    // Fetch the default set of models for this collection, resetting the
    // collection when they arrive. If `add: true` is passed, appends the
    // models to the collection instead of resetting.
    fetch: function(options) {
      options = options ? _.clone(options) : {};
      if (options.parse === void 0) options.parse = true;
      var collection = this;
      var success = options.success;
      options.success = function(resp, status, xhr) {
        var method = options.update ? 'update' : 'reset';
        collection[method](resp, options);
        if (success) success(collection, resp, options);
      };
      return this.sync('read', this, options);
    },

    // Create a new instance of a model in this collection. Add the model to the
    // collection immediately, unless `wait: true` is passed, in which case we
    // wait for the server to agree.
    create: function(model, options) {
      var collection = this;
      options = options ? _.clone(options) : {};
      model = this._prepareModel(model, options);
      if (!model) return false;
      if (!options.wait) collection.add(model, options);
      var success = options.success;
      options.success = function(model, resp, options) {
        if (options.wait) collection.add(model, options);
        if (success) success(model, resp, options);
      };
      model.save(null, options);
      return model;
    },

    // **parse** converts a response into a list of models to be added to the
    // collection. The default implementation is just to pass it through.
    parse: function(resp) {
      return resp;
    },

    // Create a new collection with an identical list of models as this one.
    clone: function() {
      return new this.constructor(this.models);
    },

    // Proxy to _'s chain. Can't be proxied the same way the rest of the
    // underscore methods are proxied because it relies on the underscore
    // constructor.
    chain: function() {
      return _(this.models).chain();
    },

    // Reset all internal state. Called when the collection is reset.
    _reset: function() {
      this.length = 0;
      this.models = [];
      this._byId  = {};
      this._byCid = {};
    },

    // Prepare a model or hash of attributes to be added to this collection.
    _prepareModel: function(attrs, options) {
      if (attrs instanceof Model) {
        if (!attrs.collection) attrs.collection = this;
        return attrs;
      }
      options || (options = {});
      options.collection = this;
      var model = new this.model(attrs, options);
      if (!model._validate(attrs, options)) return false;
      return model;
    },

    // Internal method to remove a model's ties to a collection.
    _removeReference: function(model) {
      if (this === model.collection) delete model.collection;
      model.off('all', this._onModelEvent, this);
    },

    // Internal method called every time a model in the set fires an event.
    // Sets need to update their indexes when models change ids. All other
    // events simply proxy through. "add" and "remove" events that originate
    // in other collections are ignored.
    _onModelEvent: function(event, model, collection, options) {
      if ((event === 'add' || event === 'remove') && collection !== this) return;
      if (event === 'destroy') this.remove(model, options);
      if (model && event === 'change:' + model.idAttribute) {
        delete this._byId[model.previous(model.idAttribute)];
        if (model.id != null) this._byId[model.id] = model;
      }
      this.trigger.apply(this, arguments);
    }

  });

  // Underscore methods that we want to implement on the Collection.
  var methods = ['forEach', 'each', 'map', 'collect', 'reduce', 'foldl',
    'inject', 'reduceRight', 'foldr', 'find', 'detect', 'filter', 'select',
    'reject', 'every', 'all', 'some', 'any', 'include', 'contains', 'invoke',
    'max', 'min', 'sortedIndex', 'toArray', 'size', 'first', 'head', 'take',
    'initial', 'rest', 'tail', 'last', 'without', 'indexOf', 'shuffle',
    'lastIndexOf', 'isEmpty'];

  // Mix in each Underscore method as a proxy to `Collection#models`.
  _.each(methods, function(method) {
    Collection.prototype[method] = function() {
      var args = slice.call(arguments);
      args.unshift(this.models);
      return _[method].apply(_, args);
    };
  });

  // Underscore methods that take a property name as an argument.
  var attributeMethods = ['groupBy', 'countBy', 'sortBy'];

  // Use attributes instead of properties.
  _.each(attributeMethods, function(method) {
    Collection.prototype[method] = function(value, context) {
      var iterator = _.isFunction(value) ? value : function(model) {
        return model.get(value);
      };
      return _[method](this.models, iterator, context);
    };
  });

  // Backbone.Router
  // ---------------

  // Routers map faux-URLs to actions, and fire events when routes are
  // matched. Creating a new one sets its `routes` hash, if not set statically.
  var Router = Backbone.Router = function(options) {
    options || (options = {});
    if (options.routes) this.routes = options.routes;
    this._bindRoutes();
    this.initialize.apply(this, arguments);
  };

  // Cached regular expressions for matching named param parts and splatted
  // parts of route strings.
  var optionalParam = /\((.*?)\)/g;
  var namedParam    = /:\w+/g;
  var splatParam    = /\*\w+/g;
  var escapeRegExp  = /[\-{}\[\]+?.,\\\^$|#\s]/g;

  // Set up all inheritable **Backbone.Router** properties and methods.
  _.extend(Router.prototype, Events, {

    // Initialize is an empty function by default. Override it with your own
    // initialization logic.
    initialize: function(){},

    // Manually bind a single named route to a callback. For example:
    //
    //     this.route('search/:query/p:num', 'search', function(query, num) {
    //       ...
    //     });
    //
    route: function(route, name, callback) {
      if (!_.isRegExp(route)) route = this._routeToRegExp(route);
      if (!callback) callback = this[name];
      Backbone.history.route(route, _.bind(function(fragment) {
        var args = this._extractParameters(route, fragment);
        callback && callback.apply(this, args);
        this.trigger.apply(this, ['route:' + name].concat(args));
        Backbone.history.trigger('route', this, name, args);
      }, this));
      return this;
    },

    // Simple proxy to `Backbone.history` to save a fragment into the history.
    navigate: function(fragment, options) {
      Backbone.history.navigate(fragment, options);
      return this;
    },

    // Bind all defined routes to `Backbone.history`. We have to reverse the
    // order of the routes here to support behavior where the most general
    // routes can be defined at the bottom of the route map.
    _bindRoutes: function() {
      if (!this.routes) return;
      var route, routes = _.keys(this.routes);
      while ((route = routes.pop()) != null) {
        this.route(route, this.routes[route]);
      }
    },

    // Convert a route string into a regular expression, suitable for matching
    // against the current location hash.
    _routeToRegExp: function(route) {
      route = route.replace(escapeRegExp, '\\$&')
                   .replace(optionalParam, '(?:$1)?')
                   .replace(namedParam, '([^\/]+)')
                   .replace(splatParam, '(.*?)');
      return new RegExp('^' + route + '$');
    },

    // Given a route, and a URL fragment that it matches, return the array of
    // extracted parameters.
    _extractParameters: function(route, fragment) {
      return route.exec(fragment).slice(1);
    }

  });

  // Backbone.History
  // ----------------

  // Handles cross-browser history management, based on URL fragments. If the
  // browser does not support `onhashchange`, falls back to polling.
  var History = Backbone.History = function() {
    this.handlers = [];
    _.bindAll(this, 'checkUrl');

    // #1653 - Ensure that `History` can be used outside of the browser.
    if (typeof window !== 'undefined') {
      this.location = window.location;
      this.history = window.history;
    }
  };

  // Cached regex for stripping a leading hash/slash and trailing space.
  var routeStripper = /^[#\/]|\s+$/g;

  // Cached regex for stripping leading and trailing slashes.
  var rootStripper = /^\/+|\/+$/g;

  // Cached regex for detecting MSIE.
  var isExplorer = /msie [\w.]+/;

  // Cached regex for removing a trailing slash.
  var trailingSlash = /\/$/;

  // Has the history handling already been started?
  History.started = false;

  // Set up all inheritable **Backbone.History** properties and methods.
  _.extend(History.prototype, Events, {

    // The default interval to poll for hash changes, if necessary, is
    // twenty times a second.
    interval: 50,

    // Gets the true hash value. Cannot use location.hash directly due to bug
    // in Firefox where location.hash will always be decoded.
    getHash: function(window) {
      var match = (window || this).location.href.match(/#(.*)$/);
      return match ? match[1] : '';
    },

    // Get the cross-browser normalized URL fragment, either from the URL,
    // the hash, or the override.
    getFragment: function(fragment, forcePushState) {
      if (fragment == null) {
        if (this._hasPushState || !this._wantsHashChange || forcePushState) {
          fragment = this.location.pathname;
          var root = this.root.replace(trailingSlash, '');
          if (!fragment.indexOf(root)) fragment = fragment.substr(root.length);
        } else {
          fragment = this.getHash();
        }
      }
      return fragment.replace(routeStripper, '');
    },

    // Start the hash change handling, returning `true` if the current URL matches
    // an existing route, and `false` otherwise.
    start: function(options) {
      if (History.started) throw new Error("Backbone.history has already been started");
      History.started = true;

      // Figure out the initial configuration. Do we need an iframe?
      // Is pushState desired ... is it available?
      this.options          = _.extend({}, {root: '/'}, this.options, options);
      this.root             = this.options.root;
      this._wantsHashChange = this.options.hashChange !== false;
      this._wantsPushState  = !!this.options.pushState;
      this._hasPushState    = !!(this.options.pushState && this.history && this.history.pushState);
      var fragment          = this.getFragment();
      var docMode           = document.documentMode;
      var oldIE             = (isExplorer.exec(navigator.userAgent.toLowerCase()) && (!docMode || docMode <= 7));

      // Normalize root to always include a leading and trailing slash.
      this.root = ('/' + this.root + '/').replace(rootStripper, '/');

      if (oldIE && this._wantsHashChange) {
        this.iframe = Backbone.$('<iframe src="javascript:0" tabindex="-1" />').hide().appendTo('body')[0].contentWindow;
        this.navigate(fragment);
      }

      // Depending on whether we're using pushState or hashes, and whether
      // 'onhashchange' is supported, determine how we check the URL state.
      if (this._hasPushState) {
        Backbone.$(window).bind('popstate', this.checkUrl);
      } else if (this._wantsHashChange && ('onhashchange' in window) && !oldIE) {
        Backbone.$(window).bind('hashchange', this.checkUrl);
      } else if (this._wantsHashChange) {
        this._checkUrlInterval = setInterval(this.checkUrl, this.interval);
      }

      // Determine if we need to change the base url, for a pushState link
      // opened by a non-pushState browser.
      this.fragment = fragment;
      var loc = this.location;
      var atRoot = loc.pathname.replace(/[^\/]$/, '$&/') === this.root;

      // If we've started off with a route from a `pushState`-enabled browser,
      // but we're currently in a browser that doesn't support it...
      if (this._wantsHashChange && this._wantsPushState && !this._hasPushState && !atRoot) {
        this.fragment = this.getFragment(null, true);
        this.location.replace(this.root + this.location.search + '#' + this.fragment);
        // Return immediately as browser will do redirect to new url
        return true;

      // Or if we've started out with a hash-based route, but we're currently
      // in a browser where it could be `pushState`-based instead...
      } else if (this._wantsPushState && this._hasPushState && atRoot && loc.hash) {
        this.fragment = this.getHash().replace(routeStripper, '');
        this.history.replaceState({}, document.title, this.root + this.fragment + loc.search);
      }

      if (!this.options.silent) return this.loadUrl();
    },

    // Disable Backbone.history, perhaps temporarily. Not useful in a real app,
    // but possibly useful for unit testing Routers.
    stop: function() {
      Backbone.$(window).unbind('popstate', this.checkUrl).unbind('hashchange', this.checkUrl);
      clearInterval(this._checkUrlInterval);
      History.started = false;
    },

    // Add a route to be tested when the fragment changes. Routes added later
    // may override previous routes.
    route: function(route, callback) {
      this.handlers.unshift({route: route, callback: callback});
    },

    // Checks the current URL to see if it has changed, and if it has,
    // calls `loadUrl`, normalizing across the hidden iframe.
    checkUrl: function(e) {
      var current = this.getFragment();
      if (current === this.fragment && this.iframe) {
        current = this.getFragment(this.getHash(this.iframe));
      }
      if (current === this.fragment) return false;
      if (this.iframe) this.navigate(current);
      this.loadUrl() || this.loadUrl(this.getHash());
    },

    // Attempt to load the current URL fragment. If a route succeeds with a
    // match, returns `true`. If no defined routes matches the fragment,
    // returns `false`.
    loadUrl: function(fragmentOverride) {
      var fragment = this.fragment = this.getFragment(fragmentOverride);
      var matched = _.any(this.handlers, function(handler) {
        if (handler.route.test(fragment)) {
          handler.callback(fragment);
          return true;
        }
      });
      return matched;
    },

    // Save a fragment into the hash history, or replace the URL state if the
    // 'replace' option is passed. You are responsible for properly URL-encoding
    // the fragment in advance.
    //
    // The options object can contain `trigger: true` if you wish to have the
    // route callback be fired (not usually desirable), or `replace: true`, if
    // you wish to modify the current URL without adding an entry to the history.
    navigate: function(fragment, options) {
      if (!History.started) return false;
      if (!options || options === true) options = {trigger: options};
      fragment = this.getFragment(fragment || '');
      if (this.fragment === fragment) return;
      this.fragment = fragment;
      var url = this.root + fragment;

      // If pushState is available, we use it to set the fragment as a real URL.
      if (this._hasPushState) {
        this.history[options.replace ? 'replaceState' : 'pushState']({}, document.title, url);

      // If hash changes haven't been explicitly disabled, update the hash
      // fragment to store history.
      } else if (this._wantsHashChange) {
        this._updateHash(this.location, fragment, options.replace);
        if (this.iframe && (fragment !== this.getFragment(this.getHash(this.iframe)))) {
          // Opening and closing the iframe tricks IE7 and earlier to push a
          // history entry on hash-tag change.  When replace is true, we don't
          // want this.
          if(!options.replace) this.iframe.document.open().close();
          this._updateHash(this.iframe.location, fragment, options.replace);
        }

      // If you've told us that you explicitly don't want fallback hashchange-
      // based history, then `navigate` becomes a page refresh.
      } else {
        return this.location.assign(url);
      }
      if (options.trigger) this.loadUrl(fragment);
    },

    // Update the hash location, either replacing the current entry, or adding
    // a new one to the browser history.
    _updateHash: function(location, fragment, replace) {
      if (replace) {
        var href = location.href.replace(/(javascript:|#).*$/, '');
        location.replace(href + '#' + fragment);
      } else {
        // #1649 - Some browsers require that `hash` contains a leading #.
        location.hash = '#' + fragment;
      }
    }

  });

  // Create the default Backbone.history.
  Backbone.history = new History;

  // Backbone.View
  // -------------

  // Creating a Backbone.View creates its initial element outside of the DOM,
  // if an existing element is not provided...
  var View = Backbone.View = function(options) {
    this.cid = _.uniqueId('view');
    this._configure(options || {});
    this._ensureElement();
    this.initialize.apply(this, arguments);
    this.delegateEvents();
  };

  // Cached regex to split keys for `delegate`.
  var delegateEventSplitter = /^(\S+)\s*(.*)$/;

  // List of view options to be merged as properties.
  var viewOptions = ['model', 'collection', 'el', 'id', 'attributes', 'className', 'tagName', 'events'];

  // Set up all inheritable **Backbone.View** properties and methods.
  _.extend(View.prototype, Events, {

    // The default `tagName` of a View's element is `"div"`.
    tagName: 'div',

    // jQuery delegate for element lookup, scoped to DOM elements within the
    // current view. This should be prefered to global lookups where possible.
    $: function(selector) {
      return this.$el.find(selector);
    },

    // Initialize is an empty function by default. Override it with your own
    // initialization logic.
    initialize: function(){},

    // **render** is the core function that your view should override, in order
    // to populate its element (`this.el`), with the appropriate HTML. The
    // convention is for **render** to always return `this`.
    render: function() {
      return this;
    },

    // Remove this view by taking the element out of the DOM, and removing any
    // applicable Backbone.Events listeners.
    remove: function() {
      this.$el.remove();
      this.stopListening();
      return this;
    },

    // For small amounts of DOM Elements, where a full-blown template isn't
    // needed, use **make** to manufacture elements, one at a time.
    //
    //     var el = this.make('li', {'class': 'row'}, this.model.escape('title'));
    //
    make: function(tagName, attributes, content) {
      var el = document.createElement(tagName);
      if (attributes) Backbone.$(el).attr(attributes);
      if (content != null) Backbone.$(el).html(content);
      return el;
    },

    // Change the view's element (`this.el` property), including event
    // re-delegation.
    setElement: function(element, delegate) {
      if (this.$el) this.undelegateEvents();
      this.$el = element instanceof Backbone.$ ? element : Backbone.$(element);
      this.el = this.$el[0];
      if (delegate !== false) this.delegateEvents();
      return this;
    },

    // Set callbacks, where `this.events` is a hash of
    //
    // *{"event selector": "callback"}*
    //
    //     {
    //       'mousedown .title':  'edit',
    //       'click .button':     'save'
    //       'click .open':       function(e) { ... }
    //     }
    //
    // pairs. Callbacks will be bound to the view, with `this` set properly.
    // Uses event delegation for efficiency.
    // Omitting the selector binds the event to `this.el`.
    // This only works for delegate-able events: not `focus`, `blur`, and
    // not `change`, `submit`, and `reset` in Internet Explorer.
    delegateEvents: function(events) {
      if (!(events || (events = _.result(this, 'events')))) return;
      this.undelegateEvents();
      for (var key in events) {
        var method = events[key];
        if (!_.isFunction(method)) method = this[events[key]];
        if (!method) throw new Error('Method "' + events[key] + '" does not exist');
        var match = key.match(delegateEventSplitter);
        var eventName = match[1], selector = match[2];
        method = _.bind(method, this);
        eventName += '.delegateEvents' + this.cid;
        if (selector === '') {
          this.$el.bind(eventName, method);
        } else {
          this.$el.delegate(selector, eventName, method);
        }
      }
    },

    // Clears all callbacks previously bound to the view with `delegateEvents`.
    // You usually don't need to use this, but may wish to if you have multiple
    // Backbone views attached to the same DOM element.
    undelegateEvents: function() {
      this.$el.unbind('.delegateEvents' + this.cid);
    },

    // Performs the initial configuration of a View with a set of options.
    // Keys with special meaning *(model, collection, id, className)*, are
    // attached directly to the view.
    _configure: function(options) {
      if (this.options) options = _.extend({}, _.result(this, 'options'), options);
      _.extend(this, _.pick(options, viewOptions));
      this.options = options;
    },

    // Ensure that the View has a DOM element to render into.
    // If `this.el` is a string, pass it through `$()`, take the first
    // matching element, and re-assign it to `el`. Otherwise, create
    // an element from the `id`, `className` and `tagName` properties.
    _ensureElement: function() {
      if (!this.el) {
        var attrs = _.extend({}, _.result(this, 'attributes'));
        if (this.id) attrs.id = _.result(this, 'id');
        if (this.className) attrs['class'] = _.result(this, 'className');
        this.setElement(this.make(_.result(this, 'tagName'), attrs), false);
      } else {
        this.setElement(_.result(this, 'el'), false);
      }
    }

  });

  // Backbone.sync
  // -------------

  // Map from CRUD to HTTP for our default `Backbone.sync` implementation.
  var methodMap = {
    'create': 'POST',
    'update': 'PUT',
    'patch':  'PATCH',
    'delete': 'DELETE',
    'read':   'GET'
  };

  // Override this function to change the manner in which Backbone persists
  // models to the server. You will be passed the type of request, and the
  // model in question. By default, makes a RESTful Ajax request
  // to the model's `url()`. Some possible customizations could be:
  //
  // * Use `setTimeout` to batch rapid-fire updates into a single request.
  // * Send up the models as XML instead of JSON.
  // * Persist models via WebSockets instead of Ajax.
  //
  // Turn on `Backbone.emulateHTTP` in order to send `PUT` and `DELETE` requests
  // as `POST`, with a `_method` parameter containing the true HTTP method,
  // as well as all requests with the body as `application/x-www-form-urlencoded`
  // instead of `application/json` with the model in a param named `model`.
  // Useful when interfacing with server-side languages like **PHP** that make
  // it difficult to read the body of `PUT` requests.
  Backbone.sync = function(method, model, options) {
    var type = methodMap[method];

    // Default options, unless specified.
    _.defaults(options || (options = {}), {
      emulateHTTP: Backbone.emulateHTTP,
      emulateJSON: Backbone.emulateJSON
    });

    // Default JSON-request options.
    var params = {type: type, dataType: 'json'};

    // Ensure that we have a URL.
    if (!options.url) {
      params.url = _.result(model, 'url') || urlError();
    }

    // Ensure that we have the appropriate request data.
    if (options.data == null && model && (method === 'create' || method === 'update' || method === 'patch')) {
      params.contentType = 'application/json';
      params.data = JSON.stringify(options.attrs || model.toJSON(options));
    }

    // For older servers, emulate JSON by encoding the request into an HTML-form.
    if (options.emulateJSON) {
      params.contentType = 'application/x-www-form-urlencoded';
      params.data = params.data ? {model: params.data} : {};
    }

    // For older servers, emulate HTTP by mimicking the HTTP method with `_method`
    // And an `X-HTTP-Method-Override` header.
    if (options.emulateHTTP && (type === 'PUT' || type === 'DELETE' || type === 'PATCH')) {
      params.type = 'POST';
      if (options.emulateJSON) params.data._method = type;
      var beforeSend = options.beforeSend;
      options.beforeSend = function(xhr) {
        xhr.setRequestHeader('X-HTTP-Method-Override', type);
        if (beforeSend) return beforeSend.apply(this, arguments);
      };
    }

    // Don't process data on a non-GET request.
    if (params.type !== 'GET' && !options.emulateJSON) {
      params.processData = false;
    }

    var success = options.success;
    options.success = function(resp, status, xhr) {
      if (success) success(resp, status, xhr);
      model.trigger('sync', model, resp, options);
    };

    var error = options.error;
    options.error = function(xhr, status, thrown) {
      if (error) error(model, xhr, options);
      model.trigger('error', model, xhr, options);
    };

    // Make the request, allowing the user to override any Ajax options.
    var xhr = Backbone.ajax(_.extend(params, options));
    model.trigger('request', model, xhr, options);
    return xhr;
  };

  // Set the default implementation of `Backbone.ajax` to proxy through to `$`.
  Backbone.ajax = function() {
    return Backbone.$.ajax.apply(Backbone.$, arguments);
  };

  // Helpers
  // -------

  // Helper function to correctly set up the prototype chain, for subclasses.
  // Similar to `goog.inherits`, but uses a hash of prototype properties and
  // class properties to be extended.
  var extend = function(protoProps, staticProps) {
    var parent = this;
    var child;

    // The constructor function for the new subclass is either defined by you
    // (the "constructor" property in your `extend` definition), or defaulted
    // by us to simply call the parent's constructor.
    if (protoProps && _.has(protoProps, 'constructor')) {
      child = protoProps.constructor;
    } else {
      child = function(){ parent.apply(this, arguments); };
    }

    // Add static properties to the constructor function, if supplied.
    _.extend(child, parent, staticProps);

    // Set the prototype chain to inherit from `parent`, without calling
    // `parent`'s constructor function.
    var Surrogate = function(){ this.constructor = child; };
    Surrogate.prototype = parent.prototype;
    child.prototype = new Surrogate;

    // Add prototype properties (instance properties) to the subclass,
    // if supplied.
    if (protoProps) _.extend(child.prototype, protoProps);

    // Set a convenience property in case the parent's prototype is needed
    // later.
    child.__super__ = parent.prototype;

    return child;
  };

  // Set up inheritance for the model, collection, router, view and history.
  Model.extend = Collection.extend = Router.extend = View.extend = History.extend = extend;

  // Throw an error when a URL is needed, and none is supplied.
  var urlError = function() {
    throw new Error('A "url" property or function must be specified');
  };

}).call(this);

/* /bootstrap.min.js */
/*!
* Bootstrap.js by @fat & @mdo
* Copyright 2012 Twitter, Inc.
* http://www.apache.org/licenses/LICENSE-2.0.txt
*/
!function(e){"use strict";e(function(){e.support.transition=function(){var e=function(){var e=document.createElement("bootstrap"),t={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd otransitionend",transition:"transitionend"},n;for(n in t)if(e.style[n]!==undefined)return t[n]}();return e&&{end:e}}()})}(window.jQuery),!function(e){"use strict";var t='[data-dismiss="alert"]',n=function(n){e(n).on("click",t,this.close)};n.prototype.close=function(t){function s(){i.trigger("closed").remove()}var n=e(this),r=n.attr("data-target"),i;r||(r=n.attr("href"),r=r&&r.replace(/.*(?=#[^\s]*$)/,"")),i=e(r),t&&t.preventDefault(),i.length||(i=n.hasClass("alert")?n:n.parent()),i.trigger(t=e.Event("close"));if(t.isDefaultPrevented())return;i.removeClass("in"),e.support.transition&&i.hasClass("fade")?i.on(e.support.transition.end,s):s()},e.fn.alert=function(t){return this.each(function(){var r=e(this),i=r.data("alert");i||r.data("alert",i=new n(this)),typeof t=="string"&&i[t].call(r)})},e.fn.alert.Constructor=n,e(document).on("click.alert.data-api",t,n.prototype.close)}(window.jQuery),!function(e){"use strict";var t=function(t,n){this.$element=e(t),this.options=e.extend({},e.fn.button.defaults,n)};t.prototype.setState=function(e){var t="disabled",n=this.$element,r=n.data(),i=n.is("input")?"val":"html";e+="Text",r.resetText||n.data("resetText",n[i]()),n[i](r[e]||this.options[e]),setTimeout(function(){e=="loadingText"?n.addClass(t).attr(t,t):n.removeClass(t).removeAttr(t)},0)},t.prototype.toggle=function(){var e=this.$element.closest('[data-toggle="buttons-radio"]');e&&e.find(".active").removeClass("active"),this.$element.toggleClass("active")},e.fn.button=function(n){return this.each(function(){var r=e(this),i=r.data("button"),s=typeof n=="object"&&n;i||r.data("button",i=new t(this,s)),n=="toggle"?i.toggle():n&&i.setState(n)})},e.fn.button.defaults={loadingText:"loading..."},e.fn.button.Constructor=t,e(document).on("click.button.data-api","[data-toggle^=button]",function(t){var n=e(t.target);n.hasClass("btn")||(n=n.closest(".btn")),n.button("toggle")})}(window.jQuery),!function(e){"use strict";var t=function(t,n){this.$element=e(t),this.options=n,this.options.slide&&this.slide(this.options.slide),this.options.pause=="hover"&&this.$element.on("mouseenter",e.proxy(this.pause,this)).on("mouseleave",e.proxy(this.cycle,this))};t.prototype={cycle:function(t){return t||(this.paused=!1),this.options.interval&&!this.paused&&(this.interval=setInterval(e.proxy(this.next,this),this.options.interval)),this},to:function(t){var n=this.$element.find(".item.active"),r=n.parent().children(),i=r.index(n),s=this;if(t>r.length-1||t<0)return;return this.sliding?this.$element.one("slid",function(){s.to(t)}):i==t?this.pause().cycle():this.slide(t>i?"next":"prev",e(r[t]))},pause:function(t){return t||(this.paused=!0),this.$element.find(".next, .prev").length&&e.support.transition.end&&(this.$element.trigger(e.support.transition.end),this.cycle()),clearInterval(this.interval),this.interval=null,this},next:function(){if(this.sliding)return;return this.slide("next")},prev:function(){if(this.sliding)return;return this.slide("prev")},slide:function(t,n){var r=this.$element.find(".item.active"),i=n||r[t](),s=this.interval,o=t=="next"?"left":"right",u=t=="next"?"first":"last",a=this,f;this.sliding=!0,s&&this.pause(),i=i.length?i:this.$element.find(".item")[u](),f=e.Event("slide",{relatedTarget:i[0]});if(i.hasClass("active"))return;if(e.support.transition&&this.$element.hasClass("slide")){this.$element.trigger(f);if(f.isDefaultPrevented())return;i.addClass(t),i[0].offsetWidth,r.addClass(o),i.addClass(o),this.$element.one(e.support.transition.end,function(){i.removeClass([t,o].join(" ")).addClass("active"),r.removeClass(["active",o].join(" ")),a.sliding=!1,setTimeout(function(){a.$element.trigger("slid")},0)})}else{this.$element.trigger(f);if(f.isDefaultPrevented())return;r.removeClass("active"),i.addClass("active"),this.sliding=!1,this.$element.trigger("slid")}return s&&this.cycle(),this}},e.fn.carousel=function(n){return this.each(function(){var r=e(this),i=r.data("carousel"),s=e.extend({},e.fn.carousel.defaults,typeof n=="object"&&n),o=typeof n=="string"?n:s.slide;i||r.data("carousel",i=new t(this,s)),typeof n=="number"?i.to(n):o?i[o]():s.interval&&i.cycle()})},e.fn.carousel.defaults={interval:5e3,pause:"hover"},e.fn.carousel.Constructor=t,e(document).on("click.carousel.data-api","[data-slide]",function(t){var n=e(this),r,i=e(n.attr("data-target")||(r=n.attr("href"))&&r.replace(/.*(?=#[^\s]+$)/,"")),s=e.extend({},i.data(),n.data());i.carousel(s),t.preventDefault()})}(window.jQuery),!function(e){"use strict";var t=function(t,n){this.$element=e(t),this.options=e.extend({},e.fn.collapse.defaults,n),this.options.parent&&(this.$parent=e(this.options.parent)),this.options.toggle&&this.toggle()};t.prototype={constructor:t,dimension:function(){var e=this.$element.hasClass("width");return e?"width":"height"},show:function(){var t,n,r,i;if(this.transitioning)return;t=this.dimension(),n=e.camelCase(["scroll",t].join("-")),r=this.$parent&&this.$parent.find("> .accordion-group > .in");if(r&&r.length){i=r.data("collapse");if(i&&i.transitioning)return;r.collapse("hide"),i||r.data("collapse",null)}this.$element[t](0),this.transition("addClass",e.Event("show"),"shown"),e.support.transition&&this.$element[t](this.$element[0][n])},hide:function(){var t;if(this.transitioning)return;t=this.dimension(),this.reset(this.$element[t]()),this.transition("removeClass",e.Event("hide"),"hidden"),this.$element[t](0)},reset:function(e){var t=this.dimension();return this.$element.removeClass("collapse")[t](e||"auto")[0].offsetWidth,this.$element[e!==null?"addClass":"removeClass"]("collapse"),this},transition:function(t,n,r){var i=this,s=function(){n.type=="show"&&i.reset(),i.transitioning=0,i.$element.trigger(r)};this.$element.trigger(n);if(n.isDefaultPrevented())return;this.transitioning=1,this.$element[t]("in"),e.support.transition&&this.$element.hasClass("collapse")?this.$element.one(e.support.transition.end,s):s()},toggle:function(){this[this.$element.hasClass("in")?"hide":"show"]()}},e.fn.collapse=function(n){return this.each(function(){var r=e(this),i=r.data("collapse"),s=typeof n=="object"&&n;i||r.data("collapse",i=new t(this,s)),typeof n=="string"&&i[n]()})},e.fn.collapse.defaults={toggle:!0},e.fn.collapse.Constructor=t,e(document).on("click.collapse.data-api","[data-toggle=collapse]",function(t){var n=e(this),r,i=n.attr("data-target")||t.preventDefault()||(r=n.attr("href"))&&r.replace(/.*(?=#[^\s]+$)/,""),s=e(i).data("collapse")?"toggle":n.data();n[e(i).hasClass("in")?"addClass":"removeClass"]("collapsed"),e(i).collapse(s)})}(window.jQuery),!function(e){"use strict";function r(){e(t).each(function(){i(e(this)).removeClass("open")})}function i(t){var n=t.attr("data-target"),r;return n||(n=t.attr("href"),n=n&&/#/.test(n)&&n.replace(/.*(?=#[^\s]*$)/,"")),r=e(n),r.length||(r=t.parent()),r}var t="[data-toggle=dropdown]",n=function(t){var n=e(t).on("click.dropdown.data-api",this.toggle);e("html").on("click.dropdown.data-api",function(){n.parent().removeClass("open")})};n.prototype={constructor:n,toggle:function(t){var n=e(this),s,o;if(n.is(".disabled, :disabled"))return;return s=i(n),o=s.hasClass("open"),r(),o||(s.toggleClass("open"),n.focus()),!1},keydown:function(t){var n,r,s,o,u,a;if(!/(38|40|27)/.test(t.keyCode))return;n=e(this),t.preventDefault(),t.stopPropagation();if(n.is(".disabled, :disabled"))return;o=i(n),u=o.hasClass("open");if(!u||u&&t.keyCode==27)return n.click();r=e("[role=menu] li:not(.divider) a",o);if(!r.length)return;a=r.index(r.filter(":focus")),t.keyCode==38&&a>0&&a--,t.keyCode==40&&a<r.length-1&&a++,~a||(a=0),r.eq(a).focus()}},e.fn.dropdown=function(t){return this.each(function(){var r=e(this),i=r.data("dropdown");i||r.data("dropdown",i=new n(this)),typeof t=="string"&&i[t].call(r)})},e.fn.dropdown.Constructor=n,e(document).on("click.dropdown.data-api touchstart.dropdown.data-api",r).on("click.dropdown touchstart.dropdown.data-api",".dropdown form",function(e){e.stopPropagation()}).on("click.dropdown.data-api touchstart.dropdown.data-api",t,n.prototype.toggle).on("keydown.dropdown.data-api touchstart.dropdown.data-api",t+", [role=menu]",n.prototype.keydown)}(window.jQuery),!function(e){"use strict";var t=function(t,n){this.options=n,this.$element=e(t).delegate('[data-dismiss="modal"]',"click.dismiss.modal",e.proxy(this.hide,this)),this.options.remote&&this.$element.find(".modal-body").load(this.options.remote)};t.prototype={constructor:t,toggle:function(){return this[this.isShown?"hide":"show"]()},show:function(){var t=this,n=e.Event("show");this.$element.trigger(n);if(this.isShown||n.isDefaultPrevented())return;this.isShown=!0,this.escape(),this.backdrop(function(){var n=e.support.transition&&t.$element.hasClass("fade");t.$element.parent().length||t.$element.appendTo(document.body),t.$element.show(),n&&t.$element[0].offsetWidth,t.$element.addClass("in").attr("aria-hidden",!1),t.enforceFocus(),n?t.$element.one(e.support.transition.end,function(){t.$element.focus().trigger("shown")}):t.$element.focus().trigger("shown")})},hide:function(t){t&&t.preventDefault();var n=this;t=e.Event("hide"),this.$element.trigger(t);if(!this.isShown||t.isDefaultPrevented())return;this.isShown=!1,this.escape(),e(document).off("focusin.modal"),this.$element.removeClass("in").attr("aria-hidden",!0),e.support.transition&&this.$element.hasClass("fade")?this.hideWithTransition():this.hideModal()},enforceFocus:function(){var t=this;e(document).on("focusin.modal",function(e){t.$element[0]!==e.target&&!t.$element.has(e.target).length&&t.$element.focus()})},escape:function(){var e=this;this.isShown&&this.options.keyboard?this.$element.on("keyup.dismiss.modal",function(t){t.which==27&&e.hide()}):this.isShown||this.$element.off("keyup.dismiss.modal")},hideWithTransition:function(){var t=this,n=setTimeout(function(){t.$element.off(e.support.transition.end),t.hideModal()},500);this.$element.one(e.support.transition.end,function(){clearTimeout(n),t.hideModal()})},hideModal:function(e){this.$element.hide().trigger("hidden"),this.backdrop()},removeBackdrop:function(){this.$backdrop.remove(),this.$backdrop=null},backdrop:function(t){var n=this,r=this.$element.hasClass("fade")?"fade":"";if(this.isShown&&this.options.backdrop){var i=e.support.transition&&r;this.$backdrop=e('<div class="modal-backdrop '+r+'" />').appendTo(document.body),this.$backdrop.click(this.options.backdrop=="static"?e.proxy(this.$element[0].focus,this.$element[0]):e.proxy(this.hide,this)),i&&this.$backdrop[0].offsetWidth,this.$backdrop.addClass("in"),i?this.$backdrop.one(e.support.transition.end,t):t()}else!this.isShown&&this.$backdrop?(this.$backdrop.removeClass("in"),e.support.transition&&this.$element.hasClass("fade")?this.$backdrop.one(e.support.transition.end,e.proxy(this.removeBackdrop,this)):this.removeBackdrop()):t&&t()}},e.fn.modal=function(n){return this.each(function(){var r=e(this),i=r.data("modal"),s=e.extend({},e.fn.modal.defaults,r.data(),typeof n=="object"&&n);i||r.data("modal",i=new t(this,s)),typeof n=="string"?i[n]():s.show&&i.show()})},e.fn.modal.defaults={backdrop:!0,keyboard:!0,show:!0},e.fn.modal.Constructor=t,e(document).on("click.modal.data-api",'[data-toggle="modal"]',function(t){var n=e(this),r=n.attr("href"),i=e(n.attr("data-target")||r&&r.replace(/.*(?=#[^\s]+$)/,"")),s=i.data("modal")?"toggle":e.extend({remote:!/#/.test(r)&&r},i.data(),n.data());t.preventDefault(),i.modal(s).one("hide",function(){n.focus()})})}(window.jQuery),!function(e){"use strict";var t=function(e,t){this.init("tooltip",e,t)};t.prototype={constructor:t,init:function(t,n,r){var i,s;this.type=t,this.$element=e(n),this.options=this.getOptions(r),this.enabled=!0,this.options.trigger=="click"?this.$element.on("click."+this.type,this.options.selector,e.proxy(this.toggle,this)):this.options.trigger!="manual"&&(i=this.options.trigger=="hover"?"mouseenter":"focus",s=this.options.trigger=="hover"?"mouseleave":"blur",this.$element.on(i+"."+this.type,this.options.selector,e.proxy(this.enter,this)),this.$element.on(s+"."+this.type,this.options.selector,e.proxy(this.leave,this))),this.options.selector?this._options=e.extend({},this.options,{trigger:"manual",selector:""}):this.fixTitle()},getOptions:function(t){return t=e.extend({},e.fn[this.type].defaults,t,this.$element.data()),t.delay&&typeof t.delay=="number"&&(t.delay={show:t.delay,hide:t.delay}),t},enter:function(t){var n=e(t.currentTarget)[this.type](this._options).data(this.type);if(!n.options.delay||!n.options.delay.show)return n.show();clearTimeout(this.timeout),n.hoverState="in",this.timeout=setTimeout(function(){n.hoverState=="in"&&n.show()},n.options.delay.show)},leave:function(t){var n=e(t.currentTarget)[this.type](this._options).data(this.type);this.timeout&&clearTimeout(this.timeout);if(!n.options.delay||!n.options.delay.hide)return n.hide();n.hoverState="out",this.timeout=setTimeout(function(){n.hoverState=="out"&&n.hide()},n.options.delay.hide)},show:function(){var e,t,n,r,i,s,o;if(this.hasContent()&&this.enabled){e=this.tip(),this.setContent(),this.options.animation&&e.addClass("fade"),s=typeof this.options.placement=="function"?this.options.placement.call(this,e[0],this.$element[0]):this.options.placement,t=/in/.test(s),e.detach().css({top:0,left:0,display:"block"}).insertAfter(this.$element),n=this.getPosition(t),r=e[0].offsetWidth,i=e[0].offsetHeight;switch(t?s.split(" ")[1]:s){case"bottom":o={top:n.top+n.height,left:n.left+n.width/2-r/2};break;case"top":o={top:n.top-i,left:n.left+n.width/2-r/2};break;case"left":o={top:n.top+n.height/2-i/2,left:n.left-r};break;case"right":o={top:n.top+n.height/2-i/2,left:n.left+n.width}}e.offset(o).addClass(s).addClass("in")}},setContent:function(){var e=this.tip(),t=this.getTitle();e.find(".tooltip-inner")[this.options.html?"html":"text"](t),e.removeClass("fade in top bottom left right")},hide:function(){function r(){var t=setTimeout(function(){n.off(e.support.transition.end).detach()},500);n.one(e.support.transition.end,function(){clearTimeout(t),n.detach()})}var t=this,n=this.tip();return n.removeClass("in"),e.support.transition&&this.$tip.hasClass("fade")?r():n.detach(),this},fixTitle:function(){var e=this.$element;(e.attr("title")||typeof e.attr("data-original-title")!="string")&&e.attr("data-original-title",e.attr("title")||"").removeAttr("title")},hasContent:function(){return this.getTitle()},getPosition:function(t){return e.extend({},t?{top:0,left:0}:this.$element.offset(),{width:this.$element[0].offsetWidth,height:this.$element[0].offsetHeight})},getTitle:function(){var e,t=this.$element,n=this.options;return e=t.attr("data-original-title")||(typeof n.title=="function"?n.title.call(t[0]):n.title),e},tip:function(){return this.$tip=this.$tip||e(this.options.template)},validate:function(){this.$element[0].parentNode||(this.hide(),this.$element=null,this.options=null)},enable:function(){this.enabled=!0},disable:function(){this.enabled=!1},toggleEnabled:function(){this.enabled=!this.enabled},toggle:function(t){var n=e(t.currentTarget)[this.type](this._options).data(this.type);n[n.tip().hasClass("in")?"hide":"show"]()},destroy:function(){this.hide().$element.off("."+this.type).removeData(this.type)}},e.fn.tooltip=function(n){return this.each(function(){var r=e(this),i=r.data("tooltip"),s=typeof n=="object"&&n;i||r.data("tooltip",i=new t(this,s)),typeof n=="string"&&i[n]()})},e.fn.tooltip.Constructor=t,e.fn.tooltip.defaults={animation:!0,placement:"top",selector:!1,template:'<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',trigger:"hover",title:"",delay:0,html:!1}}(window.jQuery),!function(e){"use strict";var t=function(e,t){this.init("popover",e,t)};t.prototype=e.extend({},e.fn.tooltip.Constructor.prototype,{constructor:t,setContent:function(){var e=this.tip(),t=this.getTitle(),n=this.getContent();e.find(".popover-title")[this.options.html?"html":"text"](t),e.find(".popover-content > *")[this.options.html?"html":"text"](n),e.removeClass("fade top bottom left right in")},hasContent:function(){return this.getTitle()||this.getContent()},getContent:function(){var e,t=this.$element,n=this.options;return e=t.attr("data-content")||(typeof n.content=="function"?n.content.call(t[0]):n.content),e},tip:function(){return this.$tip||(this.$tip=e(this.options.template)),this.$tip},destroy:function(){this.hide().$element.off("."+this.type).removeData(this.type)}}),e.fn.popover=function(n){return this.each(function(){var r=e(this),i=r.data("popover"),s=typeof n=="object"&&n;i||r.data("popover",i=new t(this,s)),typeof n=="string"&&i[n]()})},e.fn.popover.Constructor=t,e.fn.popover.defaults=e.extend({},e.fn.tooltip.defaults,{placement:"right",trigger:"click",content:"",template:'<div class="popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'})}(window.jQuery),!function(e){"use strict";function t(t,n){var r=e.proxy(this.process,this),i=e(t).is("body")?e(window):e(t),s;this.options=e.extend({},e.fn.scrollspy.defaults,n),this.$scrollElement=i.on("scroll.scroll-spy.data-api",r),this.selector=(this.options.target||(s=e(t).attr("href"))&&s.replace(/.*(?=#[^\s]+$)/,"")||"")+" .nav li > a",this.$body=e("body"),this.refresh(),this.process()}t.prototype={constructor:t,refresh:function(){var t=this,n;this.offsets=e([]),this.targets=e([]),n=this.$body.find(this.selector).map(function(){var t=e(this),n=t.data("target")||t.attr("href"),r=/^#\w/.test(n)&&e(n);return r&&r.length&&[[r.position().top,n]]||null}).sort(function(e,t){return e[0]-t[0]}).each(function(){t.offsets.push(this[0]),t.targets.push(this[1])})},process:function(){var e=this.$scrollElement.scrollTop()+this.options.offset,t=this.$scrollElement[0].scrollHeight||this.$body[0].scrollHeight,n=t-this.$scrollElement.height(),r=this.offsets,i=this.targets,s=this.activeTarget,o;if(e>=n)return s!=(o=i.last()[0])&&this.activate(o);for(o=r.length;o--;)s!=i[o]&&e>=r[o]&&(!r[o+1]||e<=r[o+1])&&this.activate(i[o])},activate:function(t){var n,r;this.activeTarget=t,e(this.selector).parent(".active").removeClass("active"),r=this.selector+'[data-target="'+t+'"],'+this.selector+'[href="'+t+'"]',n=e(r).parent("li").addClass("active"),n.parent(".dropdown-menu").length&&(n=n.closest("li.dropdown").addClass("active")),n.trigger("activate")}},e.fn.scrollspy=function(n){return this.each(function(){var r=e(this),i=r.data("scrollspy"),s=typeof n=="object"&&n;i||r.data("scrollspy",i=new t(this,s)),typeof n=="string"&&i[n]()})},e.fn.scrollspy.Constructor=t,e.fn.scrollspy.defaults={offset:10},e(window).on("load",function(){e('[data-spy="scroll"]').each(function(){var t=e(this);t.scrollspy(t.data())})})}(window.jQuery),!function(e){"use strict";var t=function(t){this.element=e(t)};t.prototype={constructor:t,show:function(){var t=this.element,n=t.closest("ul:not(.dropdown-menu)"),r=t.attr("data-target"),i,s,o;r||(r=t.attr("href"),r=r&&r.replace(/.*(?=#[^\s]*$)/,""));if(t.parent("li").hasClass("active"))return;i=n.find(".active:last a")[0],o=e.Event("show",{relatedTarget:i}),t.trigger(o);if(o.isDefaultPrevented())return;s=e(r),this.activate(t.parent("li"),n),this.activate(s,s.parent(),function(){t.trigger({type:"shown",relatedTarget:i})})},activate:function(t,n,r){function o(){i.removeClass("active").find("> .dropdown-menu > .active").removeClass("active"),t.addClass("active"),s?(t[0].offsetWidth,t.addClass("in")):t.removeClass("fade"),t.parent(".dropdown-menu")&&t.closest("li.dropdown").addClass("active"),r&&r()}var i=n.find("> .active"),s=r&&e.support.transition&&i.hasClass("fade");s?i.one(e.support.transition.end,o):o(),i.removeClass("in")}},e.fn.tab=function(n){return this.each(function(){var r=e(this),i=r.data("tab");i||r.data("tab",i=new t(this)),typeof n=="string"&&i[n]()})},e.fn.tab.Constructor=t,e(document).on("click.tab.data-api",'[data-toggle="tab"], [data-toggle="pill"]',function(t){t.preventDefault(),e(this).tab("show")})}(window.jQuery),!function(e){"use strict";var t=function(t,n){this.$element=e(t),this.options=e.extend({},e.fn.typeahead.defaults,n),this.matcher=this.options.matcher||this.matcher,this.sorter=this.options.sorter||this.sorter,this.highlighter=this.options.highlighter||this.highlighter,this.updater=this.options.updater||this.updater,this.$menu=e(this.options.menu).appendTo("body"),this.source=this.options.source,this.shown=!1,this.listen()};t.prototype={constructor:t,select:function(){var e=this.$menu.find(".active").attr("data-value");return this.$element.val(this.updater(e)).change(),this.hide()},updater:function(e){return e},show:function(){var t=e.extend({},this.$element.offset(),{height:this.$element[0].offsetHeight});return this.$menu.css({top:t.top+t.height,left:t.left}),this.$menu.show(),this.shown=!0,this},hide:function(){return this.$menu.hide(),this.shown=!1,this},lookup:function(t){var n;return this.query=this.$element.val(),!this.query||this.query.length<this.options.minLength?this.shown?this.hide():this:(n=e.isFunction(this.source)?this.source(this.query,e.proxy(this.process,this)):this.source,n?this.process(n):this)},process:function(t){var n=this;return t=e.grep(t,function(e){return n.matcher(e)}),t=this.sorter(t),t.length?this.render(t.slice(0,this.options.items)).show():this.shown?this.hide():this},matcher:function(e){return~e.toLowerCase().indexOf(this.query.toLowerCase())},sorter:function(e){var t=[],n=[],r=[],i;while(i=e.shift())i.toLowerCase().indexOf(this.query.toLowerCase())?~i.indexOf(this.query)?n.push(i):r.push(i):t.push(i);return t.concat(n,r)},highlighter:function(e){var t=this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,"\\$&");return e.replace(new RegExp("("+t+")","ig"),function(e,t){return"<strong>"+t+"</strong>"})},render:function(t){var n=this;return t=e(t).map(function(t,r){return t=e(n.options.item).attr("data-value",r),t.find("a").html(n.highlighter(r)),t[0]}),t.first().addClass("active"),this.$menu.html(t),this},next:function(t){var n=this.$menu.find(".active").removeClass("active"),r=n.next();r.length||(r=e(this.$menu.find("li")[0])),r.addClass("active")},prev:function(e){var t=this.$menu.find(".active").removeClass("active"),n=t.prev();n.length||(n=this.$menu.find("li").last()),n.addClass("active")},listen:function(){this.$element.on("blur",e.proxy(this.blur,this)).on("keypress",e.proxy(this.keypress,this)).on("keyup",e.proxy(this.keyup,this)),this.eventSupported("keydown")&&this.$element.on("keydown",e.proxy(this.keydown,this)),this.$menu.on("click",e.proxy(this.click,this)).on("mouseenter","li",e.proxy(this.mouseenter,this))},eventSupported:function(e){var t=e in this.$element;return t||(this.$element.setAttribute(e,"return;"),t=typeof this.$element[e]=="function"),t},move:function(e){if(!this.shown)return;switch(e.keyCode){case 9:case 13:case 27:e.preventDefault();break;case 38:e.preventDefault(),this.prev();break;case 40:e.preventDefault(),this.next()}e.stopPropagation()},keydown:function(t){this.suppressKeyPressRepeat=!~e.inArray(t.keyCode,[40,38,9,13,27]),this.move(t)},keypress:function(e){if(this.suppressKeyPressRepeat)return;this.move(e)},keyup:function(e){switch(e.keyCode){case 40:case 38:case 16:case 17:case 18:break;case 9:case 13:if(!this.shown)return;this.select();break;case 27:if(!this.shown)return;this.hide();break;default:this.lookup()}e.stopPropagation(),e.preventDefault()},blur:function(e){var t=this;setTimeout(function(){t.hide()},150)},click:function(e){e.stopPropagation(),e.preventDefault(),this.select()},mouseenter:function(t){this.$menu.find(".active").removeClass("active"),e(t.currentTarget).addClass("active")}},e.fn.typeahead=function(n){return this.each(function(){var r=e(this),i=r.data("typeahead"),s=typeof n=="object"&&n;i||r.data("typeahead",i=new t(this,s)),typeof n=="string"&&i[n]()})},e.fn.typeahead.defaults={source:[],items:8,menu:'<ul class="typeahead dropdown-menu"></ul>',item:'<li><a href="#"></a></li>',minLength:1},e.fn.typeahead.Constructor=t,e(document).on("focus.typeahead.data-api",'[data-provide="typeahead"]',function(t){var n=e(this);if(n.data("typeahead"))return;t.preventDefault(),n.typeahead(n.data())})}(window.jQuery),!function(e){"use strict";var t=function(t,n){this.options=e.extend({},e.fn.affix.defaults,n),this.$window=e(window).on("scroll.affix.data-api",e.proxy(this.checkPosition,this)).on("click.affix.data-api",e.proxy(function(){setTimeout(e.proxy(this.checkPosition,this),1)},this)),this.$element=e(t),this.checkPosition()};t.prototype.checkPosition=function(){if(!this.$element.is(":visible"))return;var t=e(document).height(),n=this.$window.scrollTop(),r=this.$element.offset(),i=this.options.offset,s=i.bottom,o=i.top,u="affix affix-top affix-bottom",a;typeof i!="object"&&(s=o=i),typeof o=="function"&&(o=i.top()),typeof s=="function"&&(s=i.bottom()),a=this.unpin!=null&&n+this.unpin<=r.top?!1:s!=null&&r.top+this.$element.height()>=t-s?"bottom":o!=null&&n<=o?"top":!1;if(this.affixed===a)return;this.affixed=a,this.unpin=a=="bottom"?r.top-n:null,this.$element.removeClass(u).addClass("affix"+(a?"-"+a:""))},e.fn.affix=function(n){return this.each(function(){var r=e(this),i=r.data("affix"),s=typeof n=="object"&&n;i||r.data("affix",i=new t(this,s)),typeof n=="string"&&i[n]()})},e.fn.affix.Constructor=t,e.fn.affix.defaults={offset:0},e(window).on("load",function(){e('[data-spy="affix"]').each(function(){var t=e(this),n=t.data();n.offset=n.offset||{},n.offsetBottom&&(n.offset.bottom=n.offsetBottom),n.offsetTop&&(n.offset.top=n.offsetTop),t.affix(n)})})}(window.jQuery);
/* /jquery.color.js */
(function(d){d.each(["backgroundColor","borderBottomColor","borderLeftColor","borderRightColor","borderTopColor","color","outlineColor"],function(f,e){d.fx.step[e]=function(g){if(!g.colorInit){g.start=c(g.elem,e);g.end=b(g.end);g.colorInit=true}g.elem.style[e]="rgb("+[Math.max(Math.min(parseInt((g.pos*(g.end[0]-g.start[0]))+g.start[0]),255),0),Math.max(Math.min(parseInt((g.pos*(g.end[1]-g.start[1]))+g.start[1]),255),0),Math.max(Math.min(parseInt((g.pos*(g.end[2]-g.start[2]))+g.start[2]),255),0)].join(",")+")"}});function b(f){var e;if(f&&f.constructor==Array&&f.length==3){return f}if(e=/rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(f)){return[parseInt(e[1]),parseInt(e[2]),parseInt(e[3])]}if(e=/rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(f)){return[parseFloat(e[1])*2.55,parseFloat(e[2])*2.55,parseFloat(e[3])*2.55]}if(e=/#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(f)){return[parseInt(e[1],16),parseInt(e[2],16),parseInt(e[3],16)]}if(e=/#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(f)){return[parseInt(e[1]+e[1],16),parseInt(e[2]+e[2],16),parseInt(e[3]+e[3],16)]}if(e=/rgba\(0, 0, 0, 0\)/.exec(f)){return a.transparent}return a[d.trim(f).toLowerCase()]}function c(g,e){var f;do{f=d.css(g,e);if(f!=""&&f!="transparent"||d.nodeName(g,"body")){break}e="backgroundColor"}while(g=g.parentNode);return b(f)}var a={aqua:[0,255,255],azure:[240,255,255],beige:[245,245,220],black:[0,0,0],blue:[0,0,255],brown:[165,42,42],cyan:[0,255,255],darkblue:[0,0,139],darkcyan:[0,139,139],darkgrey:[169,169,169],darkgreen:[0,100,0],darkkhaki:[189,183,107],darkmagenta:[139,0,139],darkolivegreen:[85,107,47],darkorange:[255,140,0],darkorchid:[153,50,204],darkred:[139,0,0],darksalmon:[233,150,122],darkviolet:[148,0,211],fuchsia:[255,0,255],gold:[255,215,0],green:[0,128,0],indigo:[75,0,130],khaki:[240,230,140],lightblue:[173,216,230],lightcyan:[224,255,255],lightgreen:[144,238,144],lightgrey:[211,211,211],lightpink:[255,182,193],lightyellow:[255,255,224],lime:[0,255,0],magenta:[255,0,255],maroon:[128,0,0],navy:[0,0,128],olive:[128,128,0],orange:[255,165,0],pink:[255,192,203],purple:[128,0,128],violet:[128,0,128],red:[255,0,0],silver:[192,192,192],white:[255,255,255],yellow:[255,255,0],transparent:[255,255,255]}})(jQuery);

/* /x-editable/bootstrap-editable/js/bootstrap-editable.js */
/*! X-editable - v1.3.0 
* In-place editing with Twitter Bootstrap, jQuery UI or pure jQuery
* http://github.com/vitalets/x-editable
* Copyright (c) 2012 Vitaliy Potapov; Licensed MIT */

/**
Form with single input element, two buttons and two states: normal/loading.
Applied as jQuery method to DIV tag (not to form tag!). This is because form can be in loading state when spinner shown.
Editableform is linked with one of input types, e.g. 'text', 'select' etc.

@class editableform
@uses text
@uses textarea
**/
(function ($) {

    var EditableForm = function (div, options) {
        this.options = $.extend({}, $.fn.editableform.defaults, options);
        this.$div = $(div); //div, containing form. Not form tag! Not editable-element.
        if(!this.options.scope) {
            this.options.scope = this;
        }
        this.initInput();
    };

    EditableForm.prototype = {
        constructor: EditableForm,
        initInput: function() {  //called once
            var TypeConstructor, typeOptions;

            //create input of specified type
            if(typeof $.fn.editabletypes[this.options.type] === 'function') {
                TypeConstructor = $.fn.editabletypes[this.options.type];
                typeOptions = $.fn.editableutils.sliceObj(this.options, $.fn.editableutils.objectKeys(TypeConstructor.defaults));
                this.input = new TypeConstructor(typeOptions);
            } else {
                $.error('Unknown type: '+ this.options.type);
                return; 
            }          

            this.value = this.input.str2value(this.options.value); 
        },
        initTemplate: function() {
            this.$form = $($.fn.editableform.template); 
        },
        initButtons: function() {
            this.$form.find('.editable-buttons').append($.fn.editableform.buttons);
        },
        /**
        Renders editableform

        @method render
        **/        
        render: function() {
            this.$loading = $($.fn.editableform.loading);        
            this.$div.empty().append(this.$loading);
            this.showLoading();
            
            //init form template and buttons
            this.initTemplate(); 
            if(this.options.showbuttons) {
                this.initButtons();
            } else {
                this.$form.find('.editable-buttons').remove();
            }

            /**        
            Fired when rendering starts
            @event rendering 
            @param {Object} event event object
            **/            
            this.$div.triggerHandler('rendering');

            //render input
            $.when(this.input.render())
            .then($.proxy(function () {
                //input
                this.$form.find('div.editable-input').append(this.input.$input);

                //automatically submit inputs when no buttons shown
                if(!this.options.showbuttons) {
                    this.input.autosubmit(); 
                }
                
                //"clear" link
                if(this.input.$clear) {
                    this.$form.find('div.editable-input').append($('<div class="editable-clear">').append(this.input.$clear));  
                }                

                //append form to container
                this.$div.append(this.$form);
                 
                //attach 'cancel' handler
                this.$form.find('.editable-cancel').click($.proxy(this.cancel, this));

                if(this.input.error) {
                    this.error(this.input.error);
                    this.$form.find('.editable-submit').attr('disabled', true);
                    this.input.$input.attr('disabled', true);
                    //prevent form from submitting
                    this.$form.submit(function(e){ e.preventDefault(); });
                } else {
                    this.error(false);
                    this.input.$input.removeAttr('disabled');
                    this.$form.find('.editable-submit').removeAttr('disabled');
                    this.input.value2input(this.value);
                    //attach submit handler
                    this.$form.submit($.proxy(this.submit, this));
                }

                /**        
                Fired when form is rendered
                @event rendered
                @param {Object} event event object
                **/            
                this.$div.triggerHandler('rendered');                

                this.showForm();
            }, this));
        },
        cancel: function() {   
            /**        
            Fired when form was cancelled by user
            @event cancel 
            @param {Object} event event object
            **/              
            this.$div.triggerHandler('cancel');
        },
        showLoading: function() {
            var w;
            if(this.$form) {
                //set loading size equal to form 
                this.$loading.width(this.$form.outerWidth());
                this.$loading.height(this.$form.outerHeight());
                this.$form.hide();
            } else {
                //stretch loading to fill container width
                w = this.$loading.parent().width();
                if(w) {
                    this.$loading.width(w);
                }
            }
            this.$loading.show(); 
        },

        showForm: function(activate) {
            this.$loading.hide();
            this.$form.show();
            if(activate !== false) {
                this.input.activate(); 
            }
            /**        
            Fired when form is shown
            @event show 
            @param {Object} event event object
            **/                    
            this.$div.triggerHandler('show');
        },

        error: function(msg) {
            var $group = this.$form.find('.control-group'),
            $block = this.$form.find('.editable-error-block');

            if(msg === false) {
                $group.removeClass($.fn.editableform.errorGroupClass);
                $block.removeClass($.fn.editableform.errorBlockClass).empty().hide(); 
            } else {
                $group.addClass($.fn.editableform.errorGroupClass);
                $block.addClass($.fn.editableform.errorBlockClass).text(msg).show();
            }
        },

        submit: function(e) {
            e.stopPropagation();
            e.preventDefault();
            
            var error,
                newValue = this.input.input2value(); //get new value from input

            //validation
            if (error = this.validate(newValue)) {
                this.error(error);
                this.showForm();
                return;
            } 
            
            //if value not changed --> trigger 'nochange' event and return
            /*jslint eqeq: true*/
            if (!this.options.savenochange && this.input.value2str(newValue) == this.input.value2str(this.value)) {
            /*jslint eqeq: false*/                
                /**        
                Fired when value not changed but form is submitted. Requires savenochange = false.
                @event nochange 
                @param {Object} event event object
                **/                    
                this.$div.triggerHandler('nochange');            
                return;
            } 

            //sending data to server
            $.when(this.save(newValue))
            .done($.proxy(function(response) {
                //run success callback
                var res = typeof this.options.success === 'function' ? this.options.success.call(this.options.scope, response, newValue) : null;
                
                //if success callback returns false --> keep form open and do not activate input
                if(res === false) {
                    this.error(false);
                    this.showForm(false);
                    return;
                }     
                
                //if success callback returns string -->  keep form open, show error and activate input               
                if(typeof res === 'string') {
                    this.error(res);
                    this.showForm();
                    return;
                }     
                
                //if success callback returns object like {newValue: <something>} --> use that value instead of submitted
                if(res && typeof res === 'object' && res.hasOwnProperty('newValue')) {
                    newValue = res.newValue;
                }                            

                //clear error message
                this.error(false);   
                this.value = newValue;
                /**        
                Fired when form is submitted
                @event save 
                @param {Object} event event object
                @param {Object} params additional params
                @param {mixed} params.newValue submitted value
                @param {Object} params.response ajax response

                @example
                $('#form-div').on('save'), function(e, params){
                    if(params.newValue === 'username') {...}
                });                    
                **/                
                this.$div.triggerHandler('save', {newValue: newValue, response: response});
            }, this))
            .fail($.proxy(function(xhr) {
                this.error(typeof xhr === 'string' ? xhr : xhr.responseText || xhr.statusText || 'Unknown error!'); 
                this.showForm();  
            }, this));
        },

        save: function(newValue) {
            //convert value for submitting to server
            var submitValue = this.input.value2submit(newValue);
            
            //try parse composite pk defined as json string in data-pk 
            this.options.pk = $.fn.editableutils.tryParseJson(this.options.pk, true); 
            
            var pk = (typeof this.options.pk === 'function') ? this.options.pk.call(this.options.scope) : this.options.pk,
            send = !!(typeof this.options.url === 'function' || (this.options.url && ((this.options.send === 'always') || (this.options.send === 'auto' && pk)))),
            params;

            if (send) { //send to server
                this.showLoading();

                //standard params
                params = {
                    name: this.options.name || '',
                    value: submitValue,
                    pk: pk 
                };

                //additional params
                if(typeof this.options.params === 'function') {
                    params = this.options.params.call(this.options.scope, params);  
                } else {
                    //try parse json in single quotes (from data-params attribute)
                    this.options.params = $.fn.editableutils.tryParseJson(this.options.params, true);   
                    $.extend(params, this.options.params);
                }

                if(typeof this.options.url === 'function') { //user's function
                    return this.options.url.call(this.options.scope, params);
                } else {  
                    //send ajax to server and return deferred object
                    return $.ajax($.extend({
                        url     : this.options.url,
                        data    : params,
                        type    : 'POST'
                    }, this.options.ajaxOptions));
                }
            }
        }, 

        validate: function (value) {
            if (value === undefined) {
                value = this.value;
            }
            if (typeof this.options.validate === 'function') {
                return this.options.validate.call(this.options.scope, value);
            }
        },

        option: function(key, value) {
            this.options[key] = value;
            if(key === 'value') {
                this.setValue(value);
            }
        },

        setValue: function(value, convertStr) {
            if(convertStr) {
                this.value = this.input.str2value(value);
            } else {
                this.value = value;
            }
        }               
    };

    /*
    Initialize editableform. Applied to jQuery object.

    @method $().editableform(options)
    @params {Object} options
    @example
    var $form = $('&lt;div&gt;').editableform({
        type: 'text',
        name: 'username',
        url: '/post',
        value: 'vitaliy'
    });

    //to display form you should call 'render' method
    $form.editableform('render');     
    */
    $.fn.editableform = function (option) {
        var args = arguments;
        return this.each(function () {
            var $this = $(this), 
            data = $this.data('editableform'), 
            options = typeof option === 'object' && option; 
            if (!data) {
                $this.data('editableform', (data = new EditableForm(this, options)));
            }

            if (typeof option === 'string') { //call method 
                data[option].apply(data, Array.prototype.slice.call(args, 1));
            } 
        });
    };

    //keep link to constructor to allow inheritance
    $.fn.editableform.Constructor = EditableForm;    

    //defaults
    $.fn.editableform.defaults = {
        /* see also defaults for input */

        /**
        Type of input. Can be <code>text|textarea|select|date|checklist</code>

        @property type 
        @type string
        @default 'text'
        **/
        type: 'text',
        /**
        Url for submit, e.g. <code>'/post'</code>  
        If function - it will be called instead of ajax. Function can return deferred object to run fail/done callbacks.

        @property url 
        @type string|function
        @default null
        @example
        url: function(params) {
            if(params.value === 'abc') {
                var d = new $.Deferred;
                return d.reject('field cannot be "abc"'); //returning error via deferred object
            } else {
                someModel.set(params.name, params.value); //save data in some js model
            }
        } 
        **/        
        url:null,
        /**
        Additional params for submit. If defined as <code>object</code> - it is **appended** to original ajax data (pk, name and value).  
        If defined as <code>function</code> - returned object **overwrites** original ajax data.
        @example
        params: function(params) {
            //originally params contain pk, name and value
            params.a = 1;
            return params;
        }

        @property params 
        @type object|function
        @default null
        **/          
        params:null,
        /**
        Name of field. Will be submitted on server. Can be taken from <code>id</code> attribute

        @property name 
        @type string
        @default null
        **/         
        name: null,
        /**
        Primary key of editable object (e.g. record id in database). For composite keys use object, e.g. <code>{id: 1, lang: 'en'}</code>.
        Can be calculated dynamically via function.

        @property pk 
        @type string|object|function
        @default null
        **/         
        pk: null,
        /**
        Initial value. If not defined - will be taken from element's content.
        For __select__ type should be defined (as it is ID of shown text).

        @property value 
        @type string|object
        @default null
        **/        
        value: null,
        /**
        Strategy for sending data on server. Can be <code>auto|always|never</code>.
        When 'auto' data will be sent on server only if pk defined, otherwise new value will be stored in element.

        @property send 
        @type string
        @default 'auto'
        **/          
        send: 'auto', 
        /**
        Function for client-side validation. If returns string - means validation not passed and string showed as error.

        @property validate 
        @type function
        @default null
        @example
        validate: function(value) {
            if($.trim(value) == '') {
                return 'This field is required';
            }
        }
        **/         
        validate: null,
        /**
        Success callback. Called when value successfully sent on server and **response status = 200**.  
        Useful to work with json response. For example, if your backend response can be <code>{success: true}</code>
        or <code>{success: false, msg: "server error"}</code> you can check it inside this callback.  
        If it returns **string** - means error occured and string is shown as error message.  
        If it returns **object like** <code>{newValue: &lt;something&gt;}</code> - it overwrites value, submitted by user.  
        Otherwise newValue simply rendered into element.
        
        @property success 
        @type function
        @default null
        @example
        success: function(response, newValue) {
            if(!response.success) return response.msg;
        }
        **/          
        success: null,
        /**
        Additional options for ajax request.
        List of values: http://api.jquery.com/jQuery.ajax

        @property ajaxOptions 
        @type object
        @default null
        @since 1.1.1        
        **/        
        ajaxOptions: null,
        /**
        Whether to show buttons or not.  
        Form without buttons can be auto-submitted by input or by onblur = 'submit'.
        @example 
        ajaxOptions: {
            method: 'PUT',
            dataType: 'xml'
        }

        @property showbuttons 
        @type boolean
        @default true
        @since 1.1.1
        **/         
        showbuttons: true,
        /**
        Scope for callback methods (success, validate).  
        If <code>null</code> means editableform instance itself. 

        @property scope 
        @type DOMElement|object
        @default null
        @since 1.2.0
        @private
        **/            
        scope: null,
        /**
        Whether to save or cancel value when it was not changed but form was submitted

        @property savenochange 
        @type boolean
        @default false
        @since 1.2.0
        **/
        savenochange: false         
    };   

    /*
    Note: following params could redefined in engine: bootstrap or jqueryui:
    Classes 'control-group' and 'editable-error-block' must always present!
    */      
    $.fn.editableform.template = '<form class="form-inline editableform">'+
    '<div class="control-group">' + 
    '<div><div class="editable-input"></div><div class="editable-buttons"></div></div>'+
    '<div class="editable-error-block"></div>' + 
    '</div>' + 
    '</form>';

    //loading div
    $.fn.editableform.loading = '<div class="editableform-loading"></div>';

    //buttons
    $.fn.editableform.buttons = '<button type="submit" class="editable-submit">ok</button>'+
    '<button type="button" class="editable-cancel">cancel</button>';      

    //error class attached to control-group
    $.fn.editableform.errorGroupClass = null;  

    //error class attached to editable-error-block
    $.fn.editableform.errorBlockClass = 'editable-error';
}(window.jQuery));
/**
* EditableForm utilites
*/
(function ($) {
    //utils
    $.fn.editableutils = {
        /**
        * classic JS inheritance function
        */  
        inherit: function (Child, Parent) {
            var F = function() { };
            F.prototype = Parent.prototype;
            Child.prototype = new F();
            Child.prototype.constructor = Child;
            Child.superclass = Parent.prototype;
        },

        /**
        * set caret position in input
        * see http://stackoverflow.com/questions/499126/jquery-set-cursor-position-in-text-area
        */        
        setCursorPosition: function(elem, pos) {
            if (elem.setSelectionRange) {
                elem.setSelectionRange(pos, pos);
            } else if (elem.createTextRange) {
                var range = elem.createTextRange();
                range.collapse(true);
                range.moveEnd('character', pos);
                range.moveStart('character', pos);
                range.select();
            }
        },

        /**
        * function to parse JSON in *single* quotes. (jquery automatically parse only double quotes)
        * That allows such code as: <a data-source="{'a': 'b', 'c': 'd'}">
        * safe = true --> means no exception will be thrown
        * for details see http://stackoverflow.com/questions/7410348/how-to-set-json-format-to-html5-data-attributes-in-the-jquery
        */
        tryParseJson: function(s, safe) {
            if (typeof s === 'string' && s.length && s.match(/^[\{\[].*[\}\]]$/)) {
                if (safe) {
                    try {
                        /*jslint evil: true*/
                        s = (new Function('return ' + s))();
                        /*jslint evil: false*/
                    } catch (e) {} finally {
                        return s;
                    }
                } else {
                    /*jslint evil: true*/
                    s = (new Function('return ' + s))();
                    /*jslint evil: false*/
                }
            }
            return s;
        },

        /**
        * slice object by specified keys
        */
        sliceObj: function(obj, keys, caseSensitive /* default: false */) {
            var key, keyLower, newObj = {};

            if (!$.isArray(keys) || !keys.length) {
                return newObj;
            }

            for (var i = 0; i < keys.length; i++) {
                key = keys[i];
                if (obj.hasOwnProperty(key)) {
                    newObj[key] = obj[key];
                }

                if(caseSensitive === true) {
                    continue;
                }

                //when getting data-* attributes via $.data() it's converted to lowercase.
                //details: http://stackoverflow.com/questions/7602565/using-data-attributes-with-jquery
                //workaround is code below.
                keyLower = key.toLowerCase();
                if (obj.hasOwnProperty(keyLower)) {
                    newObj[key] = obj[keyLower];
                }
            }

            return newObj;
        },

        /**
        * exclude complex objects from $.data() before pass to config
        */
        getConfigData: function($element) {
            var data = {};
            $.each($element.data(), function(k, v) {
                if(typeof v !== 'object' || (v && typeof v === 'object' && v.constructor === Object)) {
                    data[k] = v;
                }
            });
            return data;
        },

        objectKeys: function(o) {
            if (Object.keys) {
                return Object.keys(o);  
            } else {
                if (o !== Object(o)) {
                    throw new TypeError('Object.keys called on a non-object');
                }
                var k=[], p;
                for (p in o) {
                    if (Object.prototype.hasOwnProperty.call(o,p)) {
                        k.push(p);
                    }
                }
                return k;
            }

        },
        
       /**
        method to escape html.
       **/
       escape: function(str) {
           return $('<div>').text(str).html();
       }           
    };      
}(window.jQuery));
/**
Attaches stand-alone container with editable-form to HTML element. Element is used only for positioning, value is not stored anywhere.<br>
This method applied internally in <code>$().editable()</code>. You should subscribe on it's events (save / cancel) to get profit of it.<br>
Final realization can be different: bootstrap-popover, jqueryui-tooltip, poshytip, inline-div. It depends on which js file you include.<br>
Applied as jQuery method.

@class editableContainer
@uses editableform
**/
(function ($) {

    var EditableContainer = function (element, options) {
        this.init(element, options);
    };

    //methods
    EditableContainer.prototype = {
        containerName: null, //tbd in child class
        innerCss: null, //tbd in child class
        init: function(element, options) {
            this.$element = $(element);
            //todo: what is in priority: data or js?
            this.options = $.extend({}, $.fn.editableContainer.defaults, $.fn.editableutils.getConfigData(this.$element), options);         
            this.splitOptions();
            this.initContainer();

            //bind 'destroyed' listener to destroy container when element is removed from dom
            this.$element.on('destroyed', $.proxy(function(){
                this.destroy();
            }, this)); 
            
            //attach document handlers (once)
            if(!$(document).data('editable-handlers-attached')) {
                //close all on escape
                $(document).on('keyup.editable', function (e) {
                    if (e.which === 27) {
                        $('.editable-open').editableContainer('hide');
                        //todo: return focus on element 
                    }
                });

                //close containers when click outside
                $(document).on('click.editable', function(e) {
                    var $target = $(e.target);
                    
                    //if click inside some editableContainer --> no nothing  
                    if($target.is('.editable-container') || $target.parents('.editable-container').length || $target.parents('.ui-datepicker-header').length) {                
                        return;
                    } else {
                        //close all open containers (except one)
                        EditableContainer.prototype.closeOthers(e.target);
                    }
                });
                
                $(document).data('editable-handlers-attached', true);
            }                        
        },

        //split options on containerOptions and formOptions
        splitOptions: function() {
            this.containerOptions = {};
            this.formOptions = {};
            var cDef = $.fn[this.containerName].defaults;
            for(var k in this.options) {
              if(k in cDef) {
                 this.containerOptions[k] = this.options[k];
              } else {
                 this.formOptions[k] = this.options[k];
              } 
            }
        },
        
        initContainer: function(){
            this.call(this.containerOptions);
        },

        initForm: function() {
            this.formOptions.scope = this.$element[0]; //set scope of form callbacks to element
            this.$form = $('<div>')
            .editableform(this.formOptions)
            .on({
                save: $.proxy(this.save, this),
                cancel: $.proxy(function(){ this.hide('cancel'); }, this),
                nochange: $.proxy(function(){ this.hide('nochange'); }, this),
                show: $.proxy(this.setPosition, this), //re-position container every time form is shown (occurs each time after loading state)
                rendering: $.proxy(this.setPosition, this), //this allows to place container correctly when loading shown
                rendered: $.proxy(function(){
                    /**        
                    Fired when container is shown and form is rendered (for select will wait for loading dropdown options)
                    
                    @event shown 
                    @param {Object} event event object
                    @example
                    $('#username').on('shown', function() {
                         var $tip = $(this).data('editableContainer').tip();
                         $tip.find('input').val('overwriting value of input..');
                    });                     
                    **/                      
                    this.$element.triggerHandler('shown');
                }, this) 
            });
            return this.$form;
        },        

        /*
        Returns jquery object of container
        @method tip()
        */         
        tip: function() {
            return this.container().$tip;
        },

        container: function() {
            return this.$element.data(this.containerName); 
        },

        call: function() {
            this.$element[this.containerName].apply(this.$element, arguments); 
        },

        /**
        Shows container with form
        @method show()
        @param {boolean} closeAll Whether to close all other editable containers when showing this one. Default true.
        **/          
        show: function (closeAll) {
            this.$element.addClass('editable-open');
            if(closeAll !== false) {
                //close all open containers (except this)
                this.closeOthers(this.$element[0]);  
            }
            
            this.innerShow();
        },
        
        /* internal show method. To be overwritten in child classes */
        innerShow: function () {
            this.call('show');                
            this.tip().addClass('editable-container');
            this.initForm();
            this.tip().find(this.innerCss).empty().append(this.$form);     
            this.$form.editableform('render');            
        },

        /**
        Hides container with form
        @method hide()
        @param {string} reason Reason caused hiding. Can be <code>save|cancel|onblur|nochange|undefined (=manual)</code>
        **/         
        hide: function(reason) {
            if(!this.tip() || !this.tip().is(':visible') || !this.$element.hasClass('editable-open')) {
                return;
            }
            this.$element.removeClass('editable-open');   
            this.innerHide();
            /**        
            Fired when container was hidden. It occurs on both save or cancel.

            @event hidden 
            @param {object} event event object
            @param {string} reason Reason caused hiding. Can be <code>save|cancel|onblur|nochange|undefined (=manual)</code>
            @example
            $('#username').on('hidden', function(e, reason) {
                if(reason === 'save' || reason === 'cancel') {
                    //auto-open next editable
                    $(this).closest('tr').next().find('.editable').editable('show');
                } 
            });            
            **/             
            this.$element.triggerHandler('hidden', reason);   
        },
        
        /* internal hide method. To be overwritten in child classes */
        innerHide: function () {
            this.call('hide');       
        },        
        
        /**
        Toggles container visibility (show / hide)
        @method toggle()
        @param {boolean} closeAll Whether to close all other editable containers when showing this one. Default true.
        **/          
        toggle: function(closeAll) {
            if(this.tip && this.tip().is(':visible')) {
                this.hide();
            } else {
                this.show(closeAll);
            } 
        },

        /*
        Updates the position of container when content changed.
        @method setPosition()
        */       
        setPosition: function() {
            //tbd in child class
        },

        save: function(e, params) {
            this.hide('save');
            /**        
            Fired when new value was submitted. You can use <code>$(this).data('editableContainer')</code> inside handler to access to editableContainer instance
            
            @event save 
            @param {Object} event event object
            @param {Object} params additional params
            @param {mixed} params.newValue submitted value
            @param {Object} params.response ajax response
            @example
            $('#username').on('save', function(e, params) {
                //assuming server response: '{success: true}'
                var pk = $(this).data('editableContainer').options.pk;
                if(params.response && params.response.success) {
                    alert('value: ' + params.newValue + ' with pk: ' + pk + ' saved!');
                } else {
                    alert('error!'); 
                } 
            });
            **/             
            this.$element.triggerHandler('save', params);
        },

        /**
        Sets new option
        
        @method option(key, value)
        @param {string} key 
        @param {mixed} value 
        **/         
        option: function(key, value) {
            this.options[key] = value;
            if(key in this.containerOptions) {
                this.containerOptions[key] = value;
                this.setContainerOption(key, value); 
            } else {
                this.formOptions[key] = value;
                if(this.$form) {
                    this.$form.editableform('option', key, value);  
                }
            }
        },
        
        setContainerOption: function(key, value) {
            this.call('option', key, value);
        },

        /**
        Destroys the container instance
        @method destroy()
        **/        
        destroy: function() {
            this.call('destroy');
        },
        
        /*
        Closes other containers except one related to passed element. 
        Other containers can be cancelled or submitted (depends on onblur option)
        */
        closeOthers: function(element) {
            $('.editable-open').each(function(i, el){
                //do nothing with passed element and it's children
                if(el === element || $(el).find(element).length) {
                    return;
                }

                //otherwise cancel or submit all open containers 
                var $el = $(el),
                ec = $el.data('editableContainer');

                if(!ec) {
                    return;  
                }
                
                if(ec.options.onblur === 'cancel') {
                    $el.data('editableContainer').hide('onblur');
                } else if(ec.options.onblur === 'submit') {
                    $el.data('editableContainer').tip().find('form').submit();
                }
            });

        },
        
        /**
        Activates input of visible container (e.g. set focus)
        @method activate()
        **/         
        activate: function() {
            if(this.tip && this.tip().is(':visible') && this.$form) {
               this.$form.data('editableform').input.activate(); 
            }
        } 

    };

    /**
    jQuery method to initialize editableContainer.
    
    @method $().editableContainer(options)
    @params {Object} options
    @example
    $('#edit').editableContainer({
        type: 'text',
        url: '/post',
        pk: 1,
        value: 'hello'
    });
    **/  
    $.fn.editableContainer = function (option) {
        var args = arguments;
        return this.each(function () {
            var $this = $(this),
            dataKey = 'editableContainer', 
            data = $this.data(dataKey), 
            options = typeof option === 'object' && option;

            if (!data) {
                $this.data(dataKey, (data = new EditableContainer(this, options)));
            }

            if (typeof option === 'string') { //call method 
                data[option].apply(data, Array.prototype.slice.call(args, 1));
            }            
        });
    };     

    //store constructor
    $.fn.editableContainer.Constructor = EditableContainer;

    //defaults
    $.fn.editableContainer.defaults = {
        /**
        Initial value of form input

        @property value 
        @type mixed
        @default null
        @private
        **/        
        value: null,
        /**
        Placement of container relative to element. Can be <code>top|right|bottom|left</code>. Not used for inline container.

        @property placement 
        @type string
        @default 'top'
        **/        
        placement: 'top',
        /**
        Whether to hide container on save/cancel.

        @property autohide 
        @type boolean
        @default true
        @private 
        **/        
        autohide: true,
        /**
        Action when user clicks outside the container. Can be <code>cancel|submit|ignore</code>.  
        Setting <code>ignore</code> allows to have several containers open. 

        @property onblur 
        @type string
        @default 'cancel'
        @since 1.1.1
        **/        
        onblur: 'cancel'
    };

    /* 
    * workaround to have 'destroyed' event to destroy popover when element is destroyed
    * see http://stackoverflow.com/questions/2200494/jquery-trigger-event-when-an-element-is-removed-from-the-dom
    */
    jQuery.event.special.destroyed = {
        remove: function(o) {
            if (o.handler) {
                o.handler();
            }
        }
    };    

}(window.jQuery));

/**
Makes editable any HTML element on the page. Applied as jQuery method.

@class editable
@uses editableContainer
**/
(function ($) {

    var Editable = function (element, options) {
        this.$element = $(element);
        this.options = $.extend({}, $.fn.editable.defaults, $.fn.editableutils.getConfigData(this.$element), options);  
        this.init();
    };

    Editable.prototype = {
        constructor: Editable, 
        init: function () {
            var TypeConstructor, 
                isValueByText = false, 
                doAutotext, 
                finalize;

            //editableContainer must be defined
            if(!$.fn.editableContainer) {
                $.error('You must define $.fn.editableContainer via including corresponding file (e.g. editable-popover.js)');
                return;
            }    
                
            //name
            this.options.name = this.options.name || this.$element.attr('id');
             
            //create input of specified type. Input will be used for converting value, not in form
            if(typeof $.fn.editabletypes[this.options.type] === 'function') {
                TypeConstructor = $.fn.editabletypes[this.options.type];
                this.typeOptions = $.fn.editableutils.sliceObj(this.options, $.fn.editableutils.objectKeys(TypeConstructor.defaults));
                this.input = new TypeConstructor(this.typeOptions);
            } else {
                $.error('Unknown type: '+ this.options.type);
                return; 
            }            

            //set value from settings or by element's text
            if (this.options.value === undefined || this.options.value === null) {
                this.value = this.input.html2value($.trim(this.$element.html()));
                isValueByText = true;
            } else {
                /*
                  value can be string when received from 'data-value' attribute
                  for complext objects value can be set as json string in data-value attribute, 
                  e.g. data-value="{city: 'Moscow', street: 'Lenina'}"
                */
                this.options.value = $.fn.editableutils.tryParseJson(this.options.value, true); 
                if(typeof this.options.value === 'string') {
                    this.value = this.input.str2value(this.options.value);
                } else {
                    this.value = this.options.value;
                }
            }
            
            //add 'editable' class to every editable element
            this.$element.addClass('editable');
            
            //attach handler activating editable. In disabled mode it just prevent default action (useful for links)
            if(this.options.toggle !== 'manual') {
                this.$element.addClass('editable-click');
                this.$element.on(this.options.toggle + '.editable', $.proxy(function(e){
                    
                    e.preventDefault();
                    //stop propagation not required anymore because in document click handler it checks event target
                    //e.stopPropagation();
                    
                    if(this.options.toggle === 'mouseenter') {
                        //for hover only show container
                        this.show(); 
                    } else {
                        //when toggle='click' we should not close all other containers as they will be closed automatically in document click listener
                        var closeAll = (this.options.toggle !== 'click');
                        this.toggle(closeAll);
                    }                    
                }, this));
            } else {
                this.$element.attr('tabindex', -1); //do not stop focus on element when toggled manually
            }
            
            //check conditions for autotext:
            //if value was generated by text or value is empty, no sense to run autotext
            doAutotext = !isValueByText && this.value !== null && this.value !== undefined;
            doAutotext &= (this.options.autotext === 'always') || (this.options.autotext === 'auto' && !this.$element.text().length);
            $.when(doAutotext ? this.render() : true).then($.proxy(function() {
                if(this.options.disabled) {
                    this.disable();
                } else {
                    this.enable(); 
                }
               /**        
               Fired when element was initialized by editable method.
                              
               @event init 
               @param {Object} event event object
               @param {Object} editable editable instance
               @since 1.2.0
               **/                  
                this.$element.triggerHandler('init', this);
            }, this));
        },

        /*
        Renders value into element's text.
        Can call custom display method from options.
        Can return deferred object.
        @method render()
        */          
        render: function() {
            //do not display anything
            if(this.options.display === false) {
                return;
            }
            //if it is input with source, we pass callback in third param to be called when source is loaded
            if(this.input.options.hasOwnProperty('source')) {
                return this.input.value2html(this.value, this.$element[0], this.options.display); 
            //if display method defined --> use it    
            } else if(typeof this.options.display === 'function') {
                return this.options.display.call(this.$element[0], this.value);
            //else use input's original value2html() method    
            } else {
                return this.input.value2html(this.value, this.$element[0]); 
            }
        },
        
        /**
        Enables editable
        @method enable()
        **/          
        enable: function() {
            this.options.disabled = false;
            this.$element.removeClass('editable-disabled');
            this.handleEmpty();
            if(this.options.toggle !== 'manual') {
                if(this.$element.attr('tabindex') === '-1') {    
                    this.$element.removeAttr('tabindex');                                
                }
            }
        },
        
        /**
        Disables editable
        @method disable()
        **/         
        disable: function() {
            this.options.disabled = true; 
            this.hide();           
            this.$element.addClass('editable-disabled');
            this.handleEmpty();
            //do not stop focus on this element
            this.$element.attr('tabindex', -1);                
        },
        
        /**
        Toggles enabled / disabled state of editable element
        @method toggleDisabled()
        **/         
        toggleDisabled: function() {
            if(this.options.disabled) {
                this.enable();
            } else { 
                this.disable(); 
            }
        },  
        
        /**
        Sets new option
        
        @method option(key, value)
        @param {string|object} key option name or object with several options
        @param {mixed} value option new value
        @example
        $('.editable').editable('option', 'pk', 2);
        **/          
        option: function(key, value) {
            //set option(s) by object
            if(key && typeof key === 'object') {
               $.each(key, $.proxy(function(k, v){
                  this.option($.trim(k), v); 
               }, this)); 
               return;
            }

            //set option by string             
            this.options[key] = value;                          
            
            //disabled
            if(key === 'disabled') {
                if(value) {
                    this.disable();
                } else {
                    this.enable();
                }
                return;
            } 
            
            //value
            if(key === 'value') {
                this.setValue(value);
            }
            
            //transfer new option to container! 
            if(this.container) {
                this.container.option(key, value);  
            }
        },              
        
        /*
        * set emptytext if element is empty (reverse: remove emptytext if needed)
        */
        handleEmpty: function () {
            //do not handle empty if we do not display anything
            if(this.options.display === false) {
                return;
            }
            
            var emptyClass = 'editable-empty';
            //emptytext shown only for enabled
            if(!this.options.disabled) {
                if ($.trim(this.$element.text()) === '') {
                    this.$element.addClass(emptyClass).text(this.options.emptytext);
                } else {
                    this.$element.removeClass(emptyClass);
                }
            } else {
                //below required if element disable property was changed
                if(this.$element.hasClass(emptyClass)) {
                    this.$element.empty();
                    this.$element.removeClass(emptyClass);
                }
            }
        },        
        
        /**
        Shows container with form
        @method show()
        @param {boolean} closeAll Whether to close all other editable containers when showing this one. Default true.
        **/  
        show: function (closeAll) {
            if(this.options.disabled) {
                return;
            }
            
            //init editableContainer: popover, tooltip, inline, etc..
            if(!this.container) {
                var containerOptions = $.extend({}, this.options, {
                    value: this.value
                });
                this.$element.editableContainer(containerOptions);
                this.$element.on("save.internal", $.proxy(this.save, this));
                this.container = this.$element.data('editableContainer'); 
            } else if(this.container.tip().is(':visible')) {
                return;
            }      
            
            //show container
            this.container.show(closeAll);
        },
        
        /**
        Hides container with form
        @method hide()
        **/       
        hide: function () {   
            if(this.container) {  
                this.container.hide();
            }
        },
        
        /**
        Toggles container visibility (show / hide)
        @method toggle()
        @param {boolean} closeAll Whether to close all other editable containers when showing this one. Default true.
        **/  
        toggle: function(closeAll) {
            if(this.container && this.container.tip().is(':visible')) {
                this.hide();
            } else {
                this.show(closeAll);
            }
        },
        
        /*
        * called when form was submitted
        */          
        save: function(e, params) {
            //if url is not user's function and value was not sent to server and value changed --> mark element with unsaved css. 
            if(typeof this.options.url !== 'function' && this.options.display !== false && params.response === undefined && this.input.value2str(this.value) !== this.input.value2str(params.newValue)) { 
                this.$element.addClass('editable-unsaved');
            } else {
                this.$element.removeClass('editable-unsaved');
            }
            
           // this.hide();
            this.setValue(params.newValue);
            
            /**        
            Fired when new value was submitted. You can use <code>$(this).data('editable')</code> to access to editable instance
            
            @event save 
            @param {Object} event event object
            @param {Object} params additional params
            @param {mixed} params.newValue submitted value
            @param {Object} params.response ajax response
            @example
            $('#username').on('save', function(e, params) {
                //assuming server response: '{success: true}'
                var pk = $(this).data('editable').options.pk;
                if(params.response && params.response.success) {
                    alert('value: ' + params.newValue + ' with pk: ' + pk + ' saved!');
                } else {
                    alert('error!'); 
                } 
            });
            **/
            //event itself is triggered by editableContainer. Description here is only for documentation              
        },

        validate: function () {
            if (typeof this.options.validate === 'function') {
                return this.options.validate.call(this, this.value);
            }
        },
        
        /**
        Sets new value of editable
        @method setValue(value, convertStr)
        @param {mixed} value new value 
        @param {boolean} convertStr whether to convert value from string to internal format
        **/         
        setValue: function(value, convertStr) {
            if(convertStr) {
                this.value = this.input.str2value(value);
            } else {
                this.value = value;
            }
            if(this.container) {
                this.container.option('value', this.value);
            }
            $.when(this.render())
            .then($.proxy(function() {
                this.handleEmpty();
            }, this));
        },
        
        /**
        Activates input of visible container (e.g. set focus)
        @method activate()
        **/         
        activate: function() {
            if(this.container) {
               this.container.activate(); 
            }
        }
    };

    /* EDITABLE PLUGIN DEFINITION
    * ======================= */

    /**
    jQuery method to initialize editable element.
    
    @method $().editable(options)
    @params {Object} options
    @example
    $('#username').editable({
        type: 'text',
        url: '/post',
        pk: 1
    });
    **/    
    $.fn.editable = function (option) {
        //special API methods returning non-jquery object
        var result = {}, args = arguments, datakey = 'editable';
        switch (option) {
            /**
            Runs client-side validation for all matched editables
            
            @method validate()
            @returns {Object} validation errors map
            @example
            $('#username, #fullname').editable('validate');
            // possible result:
            {
              username: "username is required",
              fullname: "fullname should be minimum 3 letters length"
            }
            **/             
            case 'validate':
                this.each(function () {
                    var $this = $(this), data = $this.data(datakey), error;
                    if (data && (error = data.validate())) {
                        result[data.options.name] = error;
                    }
                });
            return result;

            /**
            Returns current values of editable elements. If value is <code>null</code> or <code>undefined</code> it will not be returned
            @method getValue()
            @returns {Object} object of element names and values
            @example
            $('#username, #fullname').editable('validate');
            // possible result:
            {
            username: "superuser",
            fullname: "John"
            }
            **/               
            case 'getValue':
                this.each(function () {
                    var $this = $(this), data = $this.data(datakey);
                    if (data && data.value !== undefined && data.value !== null) {
                        result[data.options.name] = data.input.value2submit(data.value);
                    }
                });
            return result;

            /**  
            This method collects values from several editable elements and submit them all to server.   
            Internally it runs client-side validation for all fields and submits only in case of success.  
            See <a href="#newrecord">creating new records</a> for details.
            
            @method submit(options)
            @param {object} options 
            @param {object} options.url url to submit data 
            @param {object} options.data additional data to submit
            @param {object} options.ajaxOptions additional ajax options            
            @param {function} options.error(obj) error handler 
            @param {function} options.success(obj,config) success handler
            @returns {Object} jQuery object
            **/            
            case 'submit':  //collects value, validate and submit to server for creating new record
                var config = arguments[1] || {},
                $elems = this,
                errors = this.editable('validate'),
                values;

                if($.isEmptyObject(errors)) {
                    values = this.editable('getValue'); 
                    if(config.data) {
                        $.extend(values, config.data);
                    }                    
                    
                    $.ajax($.extend({
                        url: config.url, 
                        data: values, 
                        type: 'POST'                        
                    }, config.ajaxOptions))
                    .success(function(response) {
                        //successful response 200 OK
                        if(typeof config.success === 'function') {
                            config.success.call($elems, response, config);
                        } 
                    })
                    .error(function(){  //ajax error
                        if(typeof config.error === 'function') {
                            config.error.apply($elems, arguments);
                        }
                    });
                } else { //client-side validation error
                    if(typeof config.error === 'function') {
                        config.error.call($elems, errors);
                    }
                }
            return this;
        }

        //return jquery object
        return this.each(function () {
            var $this = $(this), 
                data = $this.data(datakey), 
                options = typeof option === 'object' && option;

            if (!data) {
                $this.data(datakey, (data = new Editable(this, options)));
            }

            if (typeof option === 'string') { //call method 
                data[option].apply(data, Array.prototype.slice.call(args, 1));
            } 
        });
    };    
            

    $.fn.editable.defaults = {
        /**
        Type of input. Can be <code>text|textarea|select|date|checklist</code> and more

        @property type 
        @type string
        @default 'text'
        **/
        type: 'text',        
        /**
        Sets disabled state of editable

        @property disabled 
        @type boolean
        @default false
        **/         
        disabled: false,
        /**
        How to toggle editable. Can be <code>click|dblclick|mouseenter|manual</code>.   
        When set to <code>manual</code> you should manually call <code>show/hide</code> methods of editable.    
        **Note**: if you call <code>show</code> or <code>toggle</code> inside **click** handler of some DOM element, 
        you need to apply <code>e.stopPropagation()</code> because containers are being closed on any click on document.
        
        @example
        $('#edit-button').click(function(e) {
            e.stopPropagation();
            $('#username').editable('toggle');
        });

        @property toggle 
        @type string
        @default 'click'
        **/          
        toggle: 'click',
        /**
        Text shown when element is empty.

        @property emptytext 
        @type string
        @default 'Empty'
        **/         
        emptytext: 'Empty',
        /**
        Allows to automatically set element's text based on it's value. Can be <code>auto|always|never</code>. Useful for select and date.
        For example, if dropdown list is <code>{1: 'a', 2: 'b'}</code> and element's value set to <code>1</code>, it's html will be automatically set to <code>'a'</code>.  
        <code>auto</code> - text will be automatically set only if element is empty.  
        <code>always|never</code> - always(never) try to set element's text.

        @property autotext 
        @type string
        @default 'auto'
        **/          
        autotext: 'auto', 
        /**
        Initial value of input. Taken from <code>data-value</code> or element's text.

        @property value 
        @type mixed
        @default element's text
        **/
        value: null,
        /**
        Callback to perform custom displaying of value in element's text.  
        If <code>null</code>, default input's value2html() will be called.  
        If <code>false</code>, no displaying methods will be called, element's text will no change.  
        Runs under element's scope.  
        Second parameter __sourceData__ is passed for inputs with source (select, checklist).
        
        @property display 
        @type function|boolean
        @default null
        @since 1.2.0
        @example
        display: function(value, sourceData) {
            var escapedValue = $('<div>').text(value).html();
            $(this).html('<b>'+escapedValue+'</b>');
        }
        **/          
        display: null
    };
    
}(window.jQuery));

/**
AbstractInput - base class for all editable inputs.
It defines interface to be implemented by any input type.
To create your own input you can inherit from this class.

@class abstractinput
**/
(function ($) {

    //types
    $.fn.editabletypes = {};
    
    var AbstractInput = function () { };

    AbstractInput.prototype = {
       /**
        Initializes input
        
        @method init() 
        **/
       init: function(type, options, defaults) {
           this.type = type;
           this.options = $.extend({}, defaults, options); 
           this.$input = null;
           this.$clear = null;
           this.error = null;
       },
       
       /**
        Renders input from tpl. Can return jQuery deferred object.
        
        @method render() 
       **/       
       render: function() {
            this.$input = $(this.options.tpl);
            if(this.options.inputclass) {
                this.$input.addClass(this.options.inputclass); 
            }
            
            if (this.options.placeholder) {
                this.$input.attr('placeholder', this.options.placeholder);
            }   
       }, 

       /**
        Sets element's html by value. 
        
        @method value2html(value, element) 
        @param {mixed} value
        @param {DOMElement} element
       **/       
       value2html: function(value, element) {
           $(element).text(value);
       },
        
       /**
        Converts element's html to value
        
        @method html2value(html) 
        @param {string} html
        @returns {mixed}
       **/             
       html2value: function(html) {
           return $('<div>').html(html).text();
       },
        
       /**
        Converts value to string (for internal compare). For submitting to server used value2submit().
        
        @method value2str(value) 
        @param {mixed} value
        @returns {string}
       **/       
       value2str: function(value) {
           return value;
       }, 
       
       /**
        Converts string received from server into value.
        
        @method str2value(str) 
        @param {string} str
        @returns {mixed}
       **/        
       str2value: function(str) {
           return str;
       }, 
       
       /**
        Converts value for submitting to server
        
        @method value2submit(value) 
        @param {mixed} value
        @returns {mixed}
       **/       
       value2submit: function(value) {
           return value;
       },         
       
       /**
        Sets value of input.
        
        @method value2input(value) 
        @param {mixed} value
       **/       
       value2input: function(value) {
           this.$input.val(value);
       },
        
       /**
        Returns value of input. Value can be object (e.g. datepicker)
        
        @method input2value() 
       **/         
       input2value: function() { 
           return this.$input.val();
       }, 

       /**
        Activates input. For text it sets focus.
        
        @method activate() 
       **/        
       activate: function() {
           if(this.$input.is(':visible')) {
               this.$input.focus();
           }
       },
       
       /**
        Creates input.
        
        @method clear() 
       **/        
       clear: function() {
           this.$input.val(null);
       },
       
       /**
        method to escape html.
       **/
       escape: function(str) {
           return $('<div>').text(str).html();
       },
       
       /**
        attach handler to automatically submit form when value changed (useful when buttons not shown)
       **/       
       autosubmit: function() {
        
       }
    };
        
    AbstractInput.defaults = {  
        /**
        HTML template of input. Normally you should not change it.

        @property tpl 
        @type string
        @default ''
        **/   
        tpl: '',
        /**
        CSS class automatically applied to input
        
        @property inputclass 
        @type string
        @default input-medium
        **/         
        inputclass: 'input-medium',
        /**
        Name attribute of input

        @property name 
        @type string
        @default null
        **/         
        name: null
    };
    
    $.extend($.fn.editabletypes, {abstractinput: AbstractInput});
        
}(window.jQuery));

/**
List - abstract class for inputs that have source option loaded from js array or via ajax

@class list
@extends abstractinput
**/
(function ($) {

    var List = function (options) {
       
    };

    $.fn.editableutils.inherit(List, $.fn.editabletypes.abstractinput);

    $.extend(List.prototype, {
        render: function () {
            List.superclass.render.call(this);
            var deferred = $.Deferred();
            this.error = null;
            this.sourceData = null;
            this.prependData = null;
            this.onSourceReady(function () {
                this.renderList();
                deferred.resolve();
            }, function () {
                this.error = this.options.sourceError;
                deferred.resolve();
            });

            return deferred.promise();
        },

        html2value: function (html) {
            return null; //can't set value by text
        },
        
        value2html: function (value, element, display) {
            var deferred = $.Deferred();
            this.onSourceReady(function () {
                if(typeof display === 'function') {
                    //custom display method
                    display.call(element, value, this.sourceData); 
                } else {
                    this.value2htmlFinal(value, element);
                }
                deferred.resolve();
            }, function () {
                //do nothing with element
                deferred.resolve();
            });

            return deferred.promise();
        },  

        // ------------- additional functions ------------

        onSourceReady: function (success, error) {
            //if allready loaded just call success
            if($.isArray(this.sourceData)) {
                success.call(this);
                return; 
            }

            // try parse json in single quotes (for double quotes jquery does automatically)
            try {
                this.options.source = $.fn.editableutils.tryParseJson(this.options.source, false);
            } catch (e) {
                error.call(this);
                return;
            }

            //loading from url
            if (typeof this.options.source === 'string') {
                //try to get from cache
                if(this.options.sourceCache) {
                    var cacheID = this.options.source + (this.options.name ? '-' + this.options.name : ''),
                    cache;

                    if (!$(document).data(cacheID)) {
                        $(document).data(cacheID, {});
                    }
                    cache = $(document).data(cacheID);

                    //check for cached data
                    if (cache.loading === false && cache.sourceData) { //take source from cache
                        this.sourceData = cache.sourceData;
                        success.call(this);
                        return;
                    } else if (cache.loading === true) { //cache is loading, put callback in stack to be called later
                        cache.callbacks.push($.proxy(function () {
                            this.sourceData = cache.sourceData;
                            success.call(this);
                        }, this));

                        //also collecting error callbacks
                        cache.err_callbacks.push($.proxy(error, this));
                        return;
                    } else { //no cache yet, activate it
                        cache.loading = true;
                        cache.callbacks = [];
                        cache.err_callbacks = [];
                    }
                }
                
                //loading sourceData from server
                $.ajax({
                    url: this.options.source,
                    type: 'get',
                    cache: false,
                    data: this.options.name ? {name: this.options.name} : {},
                    dataType: 'json',
                    success: $.proxy(function (data) {
                        if(cache) {
                            cache.loading = false;
                        }
                        this.sourceData = this.makeArray(data);
                        if($.isArray(this.sourceData)) {
                            this.doPrepend();
                            success.call(this);
                            if(cache) {
                                //store result in cache
                                cache.sourceData = this.sourceData;
                                $.each(cache.callbacks, function () { this.call(); }); //run success callbacks for other fields
                            }
                        } else {
                            error.call(this);
                            if(cache) {
                                $.each(cache.err_callbacks, function () { this.call(); }); //run error callbacks for other fields
                            }
                        }
                    }, this),
                    error: $.proxy(function () {
                        error.call(this);
                        if(cache) {
                             cache.loading = false;
                             //run error callbacks for other fields
                             $.each(cache.err_callbacks, function () { this.call(); }); 
                        }
                    }, this)
                });
            } else { //options as json/array
                this.sourceData = this.makeArray(this.options.source);
                if($.isArray(this.sourceData)) {
                    this.doPrepend();
                    success.call(this);   
                } else {
                    error.call(this);
                }
            }
        },

        doPrepend: function () {
            if(this.options.prepend === null || this.options.prepend === undefined) {
                return;  
            }
            
            if(!$.isArray(this.prependData)) {
                //try parse json in single quotes
                this.options.prepend = $.fn.editableutils.tryParseJson(this.options.prepend, true);
                if (typeof this.options.prepend === 'string') {
                    this.options.prepend = {'': this.options.prepend};
                }              
                this.prependData = this.makeArray(this.options.prepend);
            }

            if($.isArray(this.prependData) && $.isArray(this.sourceData)) {
                this.sourceData = this.prependData.concat(this.sourceData);
            }
        },

        /*
         renders input list
        */
        renderList: function() {
            // this method should be overwritten in child class
        },
       
         /*
         set element's html by value
        */
        value2htmlFinal: function(value, element) {
            // this method should be overwritten in child class
        },        

        /**
        * convert data to array suitable for sourceData, e.g. [{value: 1, text: 'abc'}, {...}]
        */
        makeArray: function(data) {
            var count, obj, result = [], iterateEl;
            if(!data || typeof data === 'string') {
                return null; 
            }

            if($.isArray(data)) { //array
                iterateEl = function (k, v) {
                    obj = {value: k, text: v};
                    if(count++ >= 2) {
                        return false;// exit each if object has more than one value
                    }
                };
            
                for(var i = 0; i < data.length; i++) {
                    if(typeof data[i] === 'object') {
                        count = 0;
                        $.each(data[i], iterateEl);
                        if(count === 1) {
                            result.push(obj); 
                        } else if(count > 1 && data[i].hasOwnProperty('value') && data[i].hasOwnProperty('text')) {
                            result.push(data[i]);
                        } else {
                            //data contains incorrect objects
                        }
                    } else {
                        result.push({value: data[i], text: data[i]}); 
                    }
                }
            } else {  //object
                $.each(data, function (k, v) {
                    result.push({value: k, text: v});
                });  
            }
            return result;
        },
        
        //search for item by particular value
        itemByVal: function(val) {
            if($.isArray(this.sourceData)) {
                for(var i=0; i<this.sourceData.length; i++){
                    /*jshint eqeqeq: false*/
                    if(this.sourceData[i].value == val) {
                    /*jshint eqeqeq: true*/                            
                        return this.sourceData[i];
                    }
                }
            }
        }        

    });      

    List.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
        /**
        Source data for list. If string - considered ajax url to load items. Otherwise should be an array.
        Array format is: <code>[{value: 1, text: "text"}, {...}]</code><br>
        For compability it also supports format <code>{value1: "text1", value2: "text2" ...}</code> but it does not guarantee elements order.      
        If source is **string**, results will be cached for fields with the same source and name. See also <code>sourceCache</code> option.
        
        @property source 
        @type string|array|object
        @default null
        **/         
        source:null, 
        /**
        Data automatically prepended to the beginning of dropdown list.
        
        @property prepend 
        @type string|array|object
        @default false
        **/         
        prepend:false,
        /**
        Error message when list cannot be loaded (e.g. ajax error)
        
        @property sourceError 
        @type string
        @default Error when loading list
        **/          
        sourceError: 'Error when loading list',
        /**
        if <code>true</code> and source is **string url** - results will be cached for fields with the same source and name.  
        Usefull for editable grids.
        
        @property sourceCache 
        @type boolean
        @default true
        @since 1.2.0
        **/        
        sourceCache: true
    });

    $.fn.editabletypes.list = List;      

}(window.jQuery));
/**
Text input

@class text
@extends abstractinput
@final
@example
<a href="#" id="username" data-type="text" data-pk="1">awesome</a>
<script>
$(function(){
    $('#username').editable({
        url: '/post',
        title: 'Enter username'
    });
});
</script>
**/
(function ($) {
    var Text = function (options) {
        this.init('text', options, Text.defaults);
    };

    $.fn.editableutils.inherit(Text, $.fn.editabletypes.abstractinput);

    $.extend(Text.prototype, {
        activate: function() {
            if(this.$input.is(':visible')) {
                this.$input.focus();
                $.fn.editableutils.setCursorPosition(this.$input.get(0), this.$input.val().length);
            }
        }  
    });

    Text.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
        /**
        @property tpl 
        @default <input type="text">
        **/         
        tpl: '<input type="text">',
        /**
        Placeholder attribute of input. Shown when input is empty.

        @property placeholder 
        @type string
        @default null
        **/             
        placeholder: null
    });

    $.fn.editabletypes.text = Text;

}(window.jQuery));

/**
Textarea input

@class textarea
@extends abstractinput
@final
@example
<a href="#" id="comments" data-type="textarea" data-pk="1">awesome comment!</a>
<script>
$(function(){
    $('#comments').editable({
        url: '/post',
        title: 'Enter comments'
    });
});
</script>
**/
(function ($) {

    var Textarea = function (options) {
        this.init('textarea', options, Textarea.defaults);
    };

    $.fn.editableutils.inherit(Textarea, $.fn.editabletypes.abstractinput);

    $.extend(Textarea.prototype, {
        render: function () {
            Textarea.superclass.render.call(this);

            //ctrl + enter
            this.$input.keydown(function (e) {
                if (e.ctrlKey && e.which === 13) {
                    $(this).closest('form').submit();
                }
            });
        },

        value2html: function(value, element) {
            var html = '', lines;
            if(value) {
                lines = value.split("\n");
                for (var i = 0; i < lines.length; i++) {
                    lines[i] = $('<div>').text(lines[i]).html();
                }
                html = lines.join('<br>');
            }
            $(element).html(html);
        },

        html2value: function(html) {
            if(!html) {
                return '';
            }
            var lines = html.split(/<br\s*\/?>/i);
            for (var i = 0; i < lines.length; i++) {
                lines[i] = $('<div>').html(lines[i]).text();
            }
            return lines.join("\n"); 
        },        

        activate: function() {
            if(this.$input.is(':visible')) {
                $.fn.editableutils.setCursorPosition(this.$input.get(0), this.$input.val().length);
                this.$input.focus();
            }
        }         
    });

    Textarea.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
        /**
        @property tpl 
        @default <textarea></textarea>
        **/          
        tpl:'<textarea></textarea>',
        /**
        @property inputclass 
        @default input-large
        **/          
        inputclass: 'input-large',
        /**
        Placeholder attribute of input. Shown when input is empty.

        @property placeholder 
        @type string
        @default null
        **/             
        placeholder: null 
    });

    $.fn.editabletypes.textarea = Textarea;    

}(window.jQuery));

/**
Select (dropdown)

@class select
@extends list
@final
@example
<a href="#" id="status" data-type="select" data-pk="1" data-url="/post" data-original-title="Select status"></a>
<script>
$(function(){
    $('#status').editable({
        value: 2,    
        source: [
              {value: 1, text: 'Active'},
              {value: 2, text: 'Blocked'},
              {value: 3, text: 'Deleted'}
           ]
        }
    });
});
</script>
**/
(function ($) {

    var Select = function (options) {
        this.init('select', options, Select.defaults);
    };

    $.fn.editableutils.inherit(Select, $.fn.editabletypes.list);

    $.extend(Select.prototype, {
        renderList: function() {
            if(!$.isArray(this.sourceData)) {
                return;
            }

            for(var i=0; i<this.sourceData.length; i++) {
                this.$input.append($('<option>', {value: this.sourceData[i].value}).text(this.sourceData[i].text)); 
            }
            
            //enter submit
            this.$input.on('keydown.editable', function (e) {
                if (e.which === 13) {
                    $(this).closest('form').submit();
                }
            });            
        },
       
        value2htmlFinal: function(value, element) {
            var text = '', item = this.itemByVal(value);
            if(item) {
                text = item.text;
            }
            Select.superclass.constructor.superclass.value2html(text, element);   
        },
        
        autosubmit: function() {
            this.$input.off('keydown.editable').on('change.editable', function(){
                $(this).closest('form').submit();
            });
        }
    });      

    Select.defaults = $.extend({}, $.fn.editabletypes.list.defaults, {
        /**
        @property tpl 
        @default <select></select>
        **/         
        tpl:'<select></select>'
    });

    $.fn.editabletypes.select = Select;      

}(window.jQuery));
/**
List of checkboxes. 
Internally value stored as javascript array of values.

@class checklist
@extends list
@final
@example
<a href="#" id="options" data-type="checklist" data-pk="1" data-url="/post" data-original-title="Select options"></a>
<script>
$(function(){
    $('#options').editable({
        value: [2, 3],    
        source: [
              {value: 1, text: 'option1'},
              {value: 2, text: 'option2'},
              {value: 3, text: 'option3'}
           ]
        }
    });
});
</script>
**/
(function ($) {

    var Checklist = function (options) {
        this.init('checklist', options, Checklist.defaults);
    };

    $.fn.editableutils.inherit(Checklist, $.fn.editabletypes.list);

    $.extend(Checklist.prototype, {
        renderList: function() {
            var $label, $div;
            if(!$.isArray(this.sourceData)) {
                return;
            }

            for(var i=0; i<this.sourceData.length; i++) {
                $label = $('<label>').append($('<input>', {
                                           type: 'checkbox',
                                           value: this.sourceData[i].value, 
                                           name: this.options.name
                                     }))
                                     .append($('<span>').text(' '+this.sourceData[i].text));
                
                $('<div>').append($label).appendTo(this.$input);
            }
        },
       
       value2str: function(value) {
           return $.isArray(value) ? value.sort().join($.trim(this.options.separator)) : '';
       },  
       
       //parse separated string
        str2value: function(str) {
           var reg, value = null;
           if(typeof str === 'string' && str.length) {
               reg = new RegExp('\\s*'+$.trim(this.options.separator)+'\\s*');
               value = str.split(reg);
           } else if($.isArray(str)) {
               value = str; 
           }
           return value;
        },       
       
       //set checked on required checkboxes
       value2input: function(value) {
            var $checks = this.$input.find('input[type="checkbox"]');
            $checks.removeAttr('checked');
            if($.isArray(value) && value.length) {
               $checks.each(function(i, el) {
                   var $el = $(el);
                   // cannot use $.inArray as it performs strict comparison
                   $.each(value, function(j, val){
                       /*jslint eqeq: true*/
                       if($el.val() == val) {
                       /*jslint eqeq: false*/                           
                           $el.attr('checked', 'checked');
                       }
                   });
               }); 
            }  
        },  
        
       input2value: function() { 
           var checked = [];
           this.$input.find('input:checked').each(function(i, el) {
               checked.push($(el).val());
           });
           return checked;
       },            
          
       //collect text of checked boxes
        value2htmlFinal: function(value, element) {
           var html = [],
               /*jslint eqeq: true*/
               checked = $.grep(this.sourceData, function(o){
                   return $.grep(value, function(v){ return v == o.value; }).length;
               });
               /*jslint eqeq: false*/
           if(checked.length) {
               $.each(checked, function(i, v) { html.push($.fn.editableutils.escape(v.text)); });
               $(element).html(html.join('<br>'));
           } else {
               $(element).empty(); 
           }
        },
        
       activate: function() {
           this.$input.find('input[type="checkbox"]').first().focus();
       },
       
       autosubmit: function() {
           this.$input.find('input[type="checkbox"]').on('keydown', function(e){
               if (e.which === 13) {
                   $(this).closest('form').submit();
               }
           });
       }
    });      

    Checklist.defaults = $.extend({}, $.fn.editabletypes.list.defaults, {
        /**
        @property tpl 
        @default <div></div>
        **/         
        tpl:'<div></div>',
        
        /**
        @property inputclass 
        @type string
        @default editable-checklist
        **/         
        inputclass: 'editable-checklist',        
        
        /**
        Separator of values when reading from 'data-value' string

        @property separator 
        @type string
        @default ', '
        **/         
        separator: ','
    });

    $.fn.editabletypes.checklist = Checklist;      

}(window.jQuery));

/**
HTML5 input types.
Following types are supported:

* password
* email
* url
* tel
* number
* range

Learn more about html5 inputs:  
http://www.w3.org/wiki/HTML5_form_additions  
To check browser compatibility please see:  
https://developer.mozilla.org/en-US/docs/HTML/Element/Input
            
@class html5types 
@extends text
@final
@since 1.3.0
@example
<a href="#" id="email" data-type="email" data-pk="1">admin@example.com</a>
<script>
$(function(){
    $('#email').editable({
        url: '/post',
        title: 'Enter email'
    });
});
</script>
**/

/**
@property tpl 
@default depends on type
**/ 

/*
Password
*/
(function ($) {
    var Password = function (options) {
        this.init('password', options, Password.defaults);
    };
    $.fn.editableutils.inherit(Password, $.fn.editabletypes.text);
    $.extend(Password.prototype, {
       //do not display password, show '[hidden]' instead
       value2html: function(value, element) {
           if(value) {
               $(element).text('[hidden]');
           } else {
               $(element).empty(); 
           }
       },
       //as password not displayed, should not set value by html
       html2value: function(html) {
           return null;
       }       
    });    
    Password.defaults = $.extend({}, $.fn.editabletypes.text.defaults, {
        tpl: '<input type="password">'
    });
    $.fn.editabletypes.password = Password;
}(window.jQuery));


/*
Email
*/
(function ($) {
    var Email = function (options) {
        this.init('email', options, Email.defaults);
    };
    $.fn.editableutils.inherit(Email, $.fn.editabletypes.text);
    Email.defaults = $.extend({}, $.fn.editabletypes.text.defaults, {
        tpl: '<input type="email">'
    });
    $.fn.editabletypes.email = Email;
}(window.jQuery));


/*
Url
*/
(function ($) {
    var Url = function (options) {
        this.init('url', options, Url.defaults);
    };
    $.fn.editableutils.inherit(Url, $.fn.editabletypes.text);
    Url.defaults = $.extend({}, $.fn.editabletypes.text.defaults, {
        tpl: '<input type="url">'
    });
    $.fn.editabletypes.url = Url;
}(window.jQuery));


/*
Tel
*/
(function ($) {
    var Tel = function (options) {
        this.init('tel', options, Tel.defaults);
    };
    $.fn.editableutils.inherit(Tel, $.fn.editabletypes.text);
    Tel.defaults = $.extend({}, $.fn.editabletypes.text.defaults, {
        tpl: '<input type="tel">'
    });
    $.fn.editabletypes.tel = Tel;
}(window.jQuery));


/*
Number
*/
(function ($) {
    var NumberInput = function (options) {
        this.init('number', options, NumberInput.defaults);
    };
    $.fn.editableutils.inherit(NumberInput, $.fn.editabletypes.text);
    $.extend(NumberInput.prototype, {
         render: function () {
            NumberInput.superclass.render.call(this);

            if (this.options.min !== null) {
                this.$input.attr('min', this.options.min);
            } 
            
            if (this.options.max !== null) {
                this.$input.attr('max', this.options.max);
            } 
            
            if (this.options.step !== null) {
                this.$input.attr('step', this.options.step);
            }                         
        }
    });     
    NumberInput.defaults = $.extend({}, $.fn.editabletypes.text.defaults, {
        tpl: '<input type="number">',
        inputclass: 'input-mini',
        min: null,
        max: null,
        step: null
    });
    $.fn.editabletypes.number = NumberInput;
}(window.jQuery));


/*
Range (inherit from number)
*/
(function ($) {
    var Range = function (options) {
        this.init('range', options, Range.defaults);
    };
    $.fn.editableutils.inherit(Range, $.fn.editabletypes.number);
    $.extend(Range.prototype, {
        render: function () {
            this.$input = $(this.options.tpl);
            var $slider = this.$input.filter('input');
            if(this.options.inputclass) {
                $slider.addClass(this.options.inputclass); 
            }
            if (this.options.min !== null) {
                $slider.attr('min', this.options.min);
            } 
            
            if (this.options.max !== null) {
                $slider.attr('max', this.options.max);
            } 
            
            if (this.options.step !== null) {
                $slider.attr('step', this.options.step);
            }             
            
            $slider.on('input', function(){
                $(this).siblings('output').text($(this).val()); 
            });  
        },
        activate: function() {
            this.$input.filter('input').focus();
        }         
    });
    Range.defaults = $.extend({}, $.fn.editabletypes.number.defaults, {
        tpl: '<input type="range"><output style="width: 30px; display: inline-block"></output>',
        inputclass: 'input-medium'
    });
    $.fn.editabletypes.range = Range;
}(window.jQuery));
/*
Editableform based on Twitter Bootstrap
*/
(function ($) {
    
      $.extend($.fn.editableform.Constructor.prototype, {
         initTemplate: function() {
            this.$form = $($.fn.editableform.template); 
            this.$form.find('.editable-error-block').addClass('help-block');
         }
    });    
    
    //buttons
    $.fn.editableform.buttons = '<button type="submit" class="btn btn-primary editable-submit"><i class="icon-ok icon-white"></i></button>'+
                                '<button type="button" class="btn editable-cancel"><i class="icon-remove"></i></button>';         
    
    //error classes
    $.fn.editableform.errorGroupClass = 'error';
    $.fn.editableform.errorBlockClass = null;    
    
}(window.jQuery));
/**
* Editable Popover 
* ---------------------
* requires bootstrap-popover.js
*/
(function ($) {

    //extend methods
    $.extend($.fn.editableContainer.Constructor.prototype, {
        containerName: 'popover',
        //for compatibility with bootstrap <= 2.2.1 (content inserted into <p> instead of directly .popover-content) 
        innerCss: $($.fn.popover.defaults.template).find('p').length ? '.popover-content p' : '.popover-content',

        initContainer: function(){
            $.extend(this.containerOptions, {
                trigger: 'manual',
                selector: false,
                content: ' '
            });
            this.call(this.containerOptions);
        },        
        
        setContainerOption: function(key, value) {
            this.container().options[key] = value; 
        },               

        /**
        * move popover to new position. This function mainly copied from bootstrap-popover.
        */
        /*jshint laxcomma: true*/
        setPosition: function () { 
         
            (function() {    
                var $tip = this.tip()
                , inside
                , pos
                , actualWidth
                , actualHeight
                , placement
                , tp;

                placement = typeof this.options.placement === 'function' ?
                this.options.placement.call(this, $tip[0], this.$element[0]) :
                this.options.placement;

                inside = /in/.test(placement);
               
                $tip
              //  .detach()
              //vitalets: remove any placement class because otherwise they dont influence on re-positioning of visible popover
                .removeClass('top right bottom left')
                .css({ top: 0, left: 0, display: 'block' });
              //  .insertAfter(this.$element);
               
                pos = this.getPosition(inside);

                actualWidth = $tip[0].offsetWidth;
                actualHeight = $tip[0].offsetHeight;

                switch (inside ? placement.split(' ')[1] : placement) {
                    case 'bottom':
                        tp = {top: pos.top + pos.height, left: pos.left + pos.width / 2 - actualWidth / 2};
                        break;
                    case 'top':
                        tp = {top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2};
                        break;
                    case 'left':
                        tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth};
                        break;
                    case 'right':
                        tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width};
                        break;
                }

                $tip
                .offset(tp)
                .addClass(placement)
                .addClass('in');
                
            }).call(this.container());
          /*jshint laxcomma: false*/  
        }            
    });

    //defaults
    /*
    $.fn.editableContainer.defaults = $.extend({}, $.fn.popover.defaults, $.fn.editableContainer.defaults, {
        
    });
    */    

}(window.jQuery));
/**
Bootstrap-datepicker.  
Description and examples: http://vitalets.github.com/bootstrap-datepicker.  
For localization you can include js file from here: https://github.com/eternicode/bootstrap-datepicker/tree/master/js/locales

@class date
@extends abstractinput
@final
@example
<a href="#" id="dob" data-type="date" data-pk="1" data-url="/post" data-original-title="Select date">15/05/1984</a>
<script>
$(function(){
    $('#dob').editable({
        format: 'yyyy-mm-dd',    
        viewformat: 'dd/mm/yyyy',    
        datepicker: {
                weekStart: 1
           }
        }
    });
});
</script>
**/
(function ($) {

    var Date = function (options) {
        this.init('date', options, Date.defaults);
        
        //set popular options directly from settings or data-* attributes
        var directOptions =  $.fn.editableutils.sliceObj(this.options, ['format']);

        //overriding datepicker config (as by default jQuery extend() is not recursive)
        this.options.datepicker = $.extend({}, Date.defaults.datepicker, directOptions, options.datepicker);

        //by default viewformat equals to format
        if(!this.options.viewformat) {
            this.options.viewformat = this.options.datepicker.format;
        }  
        
        //language
        this.options.datepicker.language = this.options.datepicker.language || 'en'; 
        
        //store DPglobal
        this.dpg = $.fn.datepicker.DPGlobal; 
        
        //store parsed formats
        this.parsedFormat = this.dpg.parseFormat(this.options.datepicker.format);
        this.parsedViewFormat = this.dpg.parseFormat(this.options.viewformat);
    };

    $.fn.editableutils.inherit(Date, $.fn.editabletypes.abstractinput);    
    
    $.extend(Date.prototype, {
        render: function () {
            Date.superclass.render.call(this);
            this.$input.datepicker(this.options.datepicker);
                        
            if(this.options.clear) {
                this.$clear = $('<a href="#"></a>').html(this.options.clear).click($.proxy(function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    this.clear();
                }, this));
            }
        },

        value2html: function(value, element) {
            var text = value ? this.dpg.formatDate(value, this.parsedViewFormat, this.options.datepicker.language) : '';
            Date.superclass.value2html(text, element); 
        },

        html2value: function(html) {
            return html ? this.dpg.parseDate(html, this.parsedViewFormat, this.options.datepicker.language) : null;
        },   
        
        value2str: function(value) {
            return value ? this.dpg.formatDate(value, this.parsedFormat, this.options.datepicker.language) : '';
       }, 
       
       str2value: function(str) {
           return str ? this.dpg.parseDate(str, this.parsedFormat, this.options.datepicker.language) : null;
       }, 
       
       value2submit: function(value) {
           return this.value2str(value);
       },                    

       value2input: function(value) {
           this.$input.datepicker('update', value);
       },
        
       input2value: function() { 
           return this.$input.data('datepicker').date;
       },       
       
       activate: function() {
       },
       
       clear:  function() {
          this.$input.data('datepicker').date = null;
          this.$input.find('.active').removeClass('active');
       },
       
       autosubmit: function() {
           this.$input.on('changeDate', function(e){
               var $form = $(this).closest('form');
               setTimeout(function() {
                   $form.submit();
               }, 200);
           });
       }

    });
    
    Date.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
        /**
        @property tpl 
        @default <div></div>
        **/         
        tpl:'<div></div>',
        /**
        @property inputclass 
        @default editable-date well
        **/         
        inputclass: 'editable-date well',
        /**
        Format used for sending value to server. Also applied when converting date from <code>data-value</code> attribute.<br>
        Possible tokens are: <code>d, dd, m, mm, yy, yyyy</code>  
        
        @property format 
        @type string
        @default yyyy-mm-dd
        **/         
        format:'yyyy-mm-dd',
        /**
        Format used for displaying date. Also applied when converting date from element's text on init.   
        If not specified equals to <code>format</code>
        
        @property viewformat 
        @type string
        @default null
        **/          
        viewformat: null,  
        /**
        Configuration of datepicker.
        Full list of options: http://vitalets.github.com/bootstrap-datepicker
        
        @property datepicker 
        @type object
        @default {
            weekStart: 0,
            startView: 0,
            autoclose: false
        }
        **/
        datepicker:{
            weekStart: 0,
            startView: 0,
            autoclose: false
        },
        /**
        Text shown as clear date button. 
        If <code>false</code> clear button will not be rendered.
        
        @property clear 
        @type boolean|string
        @default 'x clear'         
        **/
        clear: '&times; clear'
    });   

    $.fn.editabletypes.date = Date;

}(window.jQuery));

/* =========================================================
 * bootstrap-datepicker.js
 * http://www.eyecon.ro/bootstrap-datepicker
 * =========================================================
 * Copyright 2012 Stefan Petre
 * Improvements by Andrew Rowls
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================= */

!function( $ ) {

	function UTCDate(){
		return new Date(Date.UTC.apply(Date, arguments));
	}
	function UTCToday(){
		var today = new Date();
		return UTCDate(today.getUTCFullYear(), today.getUTCMonth(), today.getUTCDate());
	}

	// Picker object

	var Datepicker = function(element, options) {
		var that = this;

		this.element = $(element);
		this.language = options.language||this.element.data('date-language')||"en";
		this.language = this.language in dates ? this.language : "en";
		this.format = DPGlobal.parseFormat(options.format||this.element.data('date-format')||'mm/dd/yyyy');
                this.isInline = false;
		this.isInput = this.element.is('input');
		this.component = this.element.is('.date') ? this.element.find('.add-on') : false;
		this.hasInput = this.component && this.element.find('input').length;
		if(this.component && this.component.length === 0)
			this.component = false;

       if (this.isInput) {   //single input
            this.element.on({
                focus: $.proxy(this.show, this),
                keyup: $.proxy(this.update, this),
                keydown: $.proxy(this.keydown, this)
            });
        } else if(this.component && this.hasInput) {  //component: input + button
                // For components that are not readonly, allow keyboard nav
                this.element.find('input').on({
                    focus: $.proxy(this.show, this),
                    keyup: $.proxy(this.update, this),
                    keydown: $.proxy(this.keydown, this)
                });

                this.component.on('click', $.proxy(this.show, this));
        } else if(this.element.is('div')) {  //inline datepicker
            this.isInline = true;
        } else {
            this.element.on('click', $.proxy(this.show, this));
        }

        this.picker = $(DPGlobal.template)
                            .appendTo(this.isInline ? this.element : 'body')
                            .on({
                                click: $.proxy(this.click, this),
                                mousedown: $.proxy(this.mousedown, this)
                            });

        if(this.isInline) {
            this.picker.addClass('datepicker-inline');
        } else {
            this.picker.addClass('dropdown-menu');
        }

		$(document).on('mousedown', function (e) {
			// Clicked outside the datepicker, hide it
			if ($(e.target).closest('.datepicker').length == 0) {
				that.hide();
			}
		});

		this.autoclose = false;
		if ('autoclose' in options) {
			this.autoclose = options.autoclose;
		} else if ('dateAutoclose' in this.element.data()) {
			this.autoclose = this.element.data('date-autoclose');
		}

		this.keyboardNavigation = true;
		if ('keyboardNavigation' in options) {
			this.keyboardNavigation = options.keyboardNavigation;
		} else if ('dateKeyboardNavigation' in this.element.data()) {
			this.keyboardNavigation = this.element.data('date-keyboard-navigation');
		}

		switch(options.startView || this.element.data('date-start-view')){
			case 2:
			case 'decade':
				this.viewMode = this.startViewMode = 2;
				break;
			case 1:
			case 'year':
				this.viewMode = this.startViewMode = 1;
				break;
			case 0:
			case 'month':
			default:
				this.viewMode = this.startViewMode = 0;
				break;
		}

		this.todayBtn = (options.todayBtn||this.element.data('date-today-btn')||false);
		this.todayHighlight = (options.todayHighlight||this.element.data('date-today-highlight')||false);

		this.weekStart = ((options.weekStart||this.element.data('date-weekstart')||dates[this.language].weekStart||0) % 7);
		this.weekEnd = ((this.weekStart + 6) % 7);
		this.startDate = -Infinity;
		this.endDate = Infinity;
		this.setStartDate(options.startDate||this.element.data('date-startdate'));
		this.setEndDate(options.endDate||this.element.data('date-enddate'));
		this.fillDow();
		this.fillMonths();
		this.update();
		this.showMode();

        if(this.isInline) {
            this.show();
        }
	};

	Datepicker.prototype = {
		constructor: Datepicker,

		show: function(e) {
			this.picker.show();
			this.height = this.component ? this.component.outerHeight() : this.element.outerHeight();
			this.update();
			this.place();
			$(window).on('resize', $.proxy(this.place, this));
			if (e ) {
				e.stopPropagation();
				e.preventDefault();
			}
			this.element.trigger({
				type: 'show',
				date: this.date
			});
		},

		hide: function(e){
            if(this.isInline) return;
			this.picker.hide();
			$(window).off('resize', this.place);
			this.viewMode = this.startViewMode;
			this.showMode();
			if (!this.isInput) {
				$(document).off('mousedown', this.hide);
			}
			if (e && e.currentTarget.value)
				this.setValue();
			this.element.trigger({
				type: 'hide',
				date: this.date
			});
		},

		getDate: function() {
			var d = this.getUTCDate();
			return new Date(d.getTime() + (d.getTimezoneOffset()*60000))
		},

		getUTCDate: function() {
			return this.date;
		},

		setDate: function(d) {
			this.setUTCDate(new Date(d.getTime() - (d.getTimezoneOffset()*60000)));
		},

		setUTCDate: function(d) {
			this.date = d;
			this.setValue();
		},

		setValue: function() {
			var formatted = this.getFormattedDate();
			if (!this.isInput) {
				if (this.component){
					this.element.find('input').prop('value', formatted);
				}
				this.element.data('date', formatted);
			} else {
				this.element.prop('value', formatted);
			}
		},

        getFormattedDate: function(format) {
            if(format == undefined) format = this.format;
            return DPGlobal.formatDate(this.date, format, this.language);
        },

		setStartDate: function(startDate){
			this.startDate = startDate||-Infinity;
			if (this.startDate !== -Infinity) {
				this.startDate = DPGlobal.parseDate(this.startDate, this.format, this.language);
			}
			this.update();
			this.updateNavArrows();
		},

		setEndDate: function(endDate){
			this.endDate = endDate||Infinity;
			if (this.endDate !== Infinity) {
				this.endDate = DPGlobal.parseDate(this.endDate, this.format, this.language);
			}
			this.update();
			this.updateNavArrows();
		},

		place: function(){
                        if(this.isInline) return;
			var zIndex = parseInt(this.element.parents().filter(function() {
							return $(this).css('z-index') != 'auto';
						}).first().css('z-index'))+10;
			var offset = this.component ? this.component.offset() : this.element.offset();
			this.picker.css({
				top: offset.top + this.height,
				left: offset.left,
				zIndex: zIndex
			});
		},

		update: function(){
            var date, fromArgs = false;
            if(arguments && arguments.length && (typeof arguments[0] === 'string' || arguments[0] instanceof Date)) {
                date = arguments[0];
                fromArgs = true;
            } else {
                date = this.isInput ? this.element.prop('value') : this.element.data('date') || this.element.find('input').prop('value');
            }

			this.date = DPGlobal.parseDate(date, this.format, this.language);

            if(fromArgs) this.setValue();

			if (this.date < this.startDate) {
				this.viewDate = new Date(this.startDate);
			} else if (this.date > this.endDate) {
				this.viewDate = new Date(this.endDate);
			} else {
				this.viewDate = new Date(this.date);
			}
			this.fill();
		},

		fillDow: function(){
			var dowCnt = this.weekStart;
			var html = '<tr>';
			while (dowCnt < this.weekStart + 7) {
				html += '<th class="dow">'+dates[this.language].daysMin[(dowCnt++)%7]+'</th>';
			}
			html += '</tr>';
			this.picker.find('.datepicker-days thead').append(html);
		},

		fillMonths: function(){
			var html = '';
			var i = 0
			while (i < 12) {
				html += '<span class="month">'+dates[this.language].monthsShort[i++]+'</span>';
			}
			this.picker.find('.datepicker-months td').html(html);
		},

		fill: function() {
			var d = new Date(this.viewDate),
				year = d.getUTCFullYear(),
				month = d.getUTCMonth(),
				startYear = this.startDate !== -Infinity ? this.startDate.getUTCFullYear() : -Infinity,
				startMonth = this.startDate !== -Infinity ? this.startDate.getUTCMonth() : -Infinity,
				endYear = this.endDate !== Infinity ? this.endDate.getUTCFullYear() : Infinity,
				endMonth = this.endDate !== Infinity ? this.endDate.getUTCMonth() : Infinity,
				currentDate = this.date && this.date.valueOf(),
				today = new Date();
			this.picker.find('.datepicker-days thead th:eq(1)')
						.text(dates[this.language].months[month]+' '+year);
			this.picker.find('tfoot th.today')
						.text(dates[this.language].today)
						.toggle(this.todayBtn !== false);
			this.updateNavArrows();
			this.fillMonths();
			var prevMonth = UTCDate(year, month-1, 28,0,0,0,0),
				day = DPGlobal.getDaysInMonth(prevMonth.getUTCFullYear(), prevMonth.getUTCMonth());
			prevMonth.setUTCDate(day);
			prevMonth.setUTCDate(day - (prevMonth.getUTCDay() - this.weekStart + 7)%7);
			var nextMonth = new Date(prevMonth);
			nextMonth.setUTCDate(nextMonth.getUTCDate() + 42);
			nextMonth = nextMonth.valueOf();
			var html = [];
			var clsName;
			while(prevMonth.valueOf() < nextMonth) {
				if (prevMonth.getUTCDay() == this.weekStart) {
					html.push('<tr>');
				}
				clsName = '';
				if (prevMonth.getUTCFullYear() < year || (prevMonth.getUTCFullYear() == year && prevMonth.getUTCMonth() < month)) {
					clsName += ' old';
				} else if (prevMonth.getUTCFullYear() > year || (prevMonth.getUTCFullYear() == year && prevMonth.getUTCMonth() > month)) {
					clsName += ' new';
				}
				// Compare internal UTC date with local today, not UTC today
				if (this.todayHighlight &&
					prevMonth.getUTCFullYear() == today.getFullYear() &&
					prevMonth.getUTCMonth() == today.getMonth() &&
					prevMonth.getUTCDate() == today.getDate()) {
					clsName += ' today';
				}
				if (currentDate && prevMonth.valueOf() == currentDate) {
					clsName += ' active';
				}
				if (prevMonth.valueOf() < this.startDate || prevMonth.valueOf() > this.endDate) {
					clsName += ' disabled';
				}
				html.push('<td class="day'+clsName+'">'+prevMonth.getUTCDate() + '</td>');
				if (prevMonth.getUTCDay() == this.weekEnd) {
					html.push('</tr>');
				}
				prevMonth.setUTCDate(prevMonth.getUTCDate()+1);
			}
			this.picker.find('.datepicker-days tbody').empty().append(html.join(''));
			var currentYear = this.date && this.date.getUTCFullYear();

			var months = this.picker.find('.datepicker-months')
						.find('th:eq(1)')
							.text(year)
							.end()
						.find('span').removeClass('active');
			if (currentYear && currentYear == year) {
				months.eq(this.date.getUTCMonth()).addClass('active');
			}
			if (year < startYear || year > endYear) {
				months.addClass('disabled');
			}
			if (year == startYear) {
				months.slice(0, startMonth).addClass('disabled');
			}
			if (year == endYear) {
				months.slice(endMonth+1).addClass('disabled');
			}

			html = '';
			year = parseInt(year/10, 10) * 10;
			var yearCont = this.picker.find('.datepicker-years')
								.find('th:eq(1)')
									.text(year + '-' + (year + 9))
									.end()
								.find('td');
			year -= 1;
			for (var i = -1; i < 11; i++) {
				html += '<span class="year'+(i == -1 || i == 10 ? ' old' : '')+(currentYear == year ? ' active' : '')+(year < startYear || year > endYear ? ' disabled' : '')+'">'+year+'</span>';
				year += 1;
			}
			yearCont.html(html);
		},

		updateNavArrows: function() {
			var d = new Date(this.viewDate),
				year = d.getUTCFullYear(),
				month = d.getUTCMonth();
			switch (this.viewMode) {
				case 0:
					if (this.startDate !== -Infinity && year <= this.startDate.getUTCFullYear() && month <= this.startDate.getUTCMonth()) {
						this.picker.find('.prev').css({visibility: 'hidden'});
					} else {
						this.picker.find('.prev').css({visibility: 'visible'});
					}
					if (this.endDate !== Infinity && year >= this.endDate.getUTCFullYear() && month >= this.endDate.getUTCMonth()) {
						this.picker.find('.next').css({visibility: 'hidden'});
					} else {
						this.picker.find('.next').css({visibility: 'visible'});
					}
					break;
				case 1:
				case 2:
					if (this.startDate !== -Infinity && year <= this.startDate.getUTCFullYear()) {
						this.picker.find('.prev').css({visibility: 'hidden'});
					} else {
						this.picker.find('.prev').css({visibility: 'visible'});
					}
					if (this.endDate !== Infinity && year >= this.endDate.getUTCFullYear()) {
						this.picker.find('.next').css({visibility: 'hidden'});
					} else {
						this.picker.find('.next').css({visibility: 'visible'});
					}
					break;
			}
		},

		click: function(e) {
			e.stopPropagation();
			e.preventDefault();
			var target = $(e.target).closest('span, td, th');
			if (target.length == 1) {
				switch(target[0].nodeName.toLowerCase()) {
					case 'th':
						switch(target[0].className) {
							case 'switch':
								this.showMode(1);
								break;
							case 'prev':
							case 'next':
								var dir = DPGlobal.modes[this.viewMode].navStep * (target[0].className == 'prev' ? -1 : 1);
								switch(this.viewMode){
									case 0:
										this.viewDate = this.moveMonth(this.viewDate, dir);
										break;
									case 1:
									case 2:
										this.viewDate = this.moveYear(this.viewDate, dir);
										break;
								}
								this.fill();
								break;
							case 'today':
								var date = new Date();
								date = UTCDate(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0);

								this.showMode(-2);
								var which = this.todayBtn == 'linked' ? null : 'view';
								this._setDate(date, which);
								break;
						}
						break;
					case 'span':
						if (!target.is('.disabled')) {
							this.viewDate.setUTCDate(1);
							if (target.is('.month')) {
								var month = target.parent().find('span').index(target);
								this.viewDate.setUTCMonth(month);
								this.element.trigger({
									type: 'changeMonth',
									date: this.viewDate
								});
							} else {
								var year = parseInt(target.text(), 10)||0;
								this.viewDate.setUTCFullYear(year);
								this.element.trigger({
									type: 'changeYear',
									date: this.viewDate
								});
							}
							this.showMode(-1);
							this.fill();
						}
						break;
					case 'td':
						if (target.is('.day') && !target.is('.disabled')){
							var day = parseInt(target.text(), 10)||1;
							var year = this.viewDate.getUTCFullYear(),
								month = this.viewDate.getUTCMonth();
							if (target.is('.old')) {
								if (month == 0) {
									month = 11;
									year -= 1;
								} else {
									month -= 1;
								}
							} else if (target.is('.new')) {
								if (month == 11) {
									month = 0;
									year += 1;
								} else {
									month += 1;
								}
							}
							this._setDate(UTCDate(year, month, day,0,0,0,0));
						}
						break;
				}
			}
		},

		_setDate: function(date, which){
			if (!which || which == 'date')
				this.date = date;
			if (!which || which  == 'view')
				this.viewDate = date;
			this.fill();
			this.setValue();
			this.element.trigger({
				type: 'changeDate',
				date: this.date
			});
			var element;
			if (this.isInput) {
				element = this.element;
			} else if (this.component){
				element = this.element.find('input');
			}
			if (element) {
				element.change();
				if (this.autoclose) {
									this.hide();
				}
			}
		},

		moveMonth: function(date, dir){
			if (!dir) return date;
			var new_date = new Date(date.valueOf()),
				day = new_date.getUTCDate(),
				month = new_date.getUTCMonth(),
				mag = Math.abs(dir),
				new_month, test;
			dir = dir > 0 ? 1 : -1;
			if (mag == 1){
				test = dir == -1
					// If going back one month, make sure month is not current month
					// (eg, Mar 31 -> Feb 31 == Feb 28, not Mar 02)
					? function(){ return new_date.getUTCMonth() == month; }
					// If going forward one month, make sure month is as expected
					// (eg, Jan 31 -> Feb 31 == Feb 28, not Mar 02)
					: function(){ return new_date.getUTCMonth() != new_month; };
				new_month = month + dir;
				new_date.setUTCMonth(new_month);
				// Dec -> Jan (12) or Jan -> Dec (-1) -- limit expected date to 0-11
				if (new_month < 0 || new_month > 11)
					new_month = (new_month + 12) % 12;
			} else {
				// For magnitudes >1, move one month at a time...
				for (var i=0; i<mag; i++)
					// ...which might decrease the day (eg, Jan 31 to Feb 28, etc)...
					new_date = this.moveMonth(new_date, dir);
				// ...then reset the day, keeping it in the new month
				new_month = new_date.getUTCMonth();
				new_date.setUTCDate(day);
				test = function(){ return new_month != new_date.getUTCMonth(); };
			}
			// Common date-resetting loop -- if date is beyond end of month, make it
			// end of month
			while (test()){
				new_date.setUTCDate(--day);
				new_date.setUTCMonth(new_month);
			}
			return new_date;
		},

		moveYear: function(date, dir){
			return this.moveMonth(date, dir*12);
		},

		dateWithinRange: function(date){
			return date >= this.startDate && date <= this.endDate;
		},

		keydown: function(e){
			if (this.picker.is(':not(:visible)')){
				if (e.keyCode == 27) // allow escape to hide and re-show picker
					this.show();
				return;
			}
			var dateChanged = false,
				dir, day, month,
				newDate, newViewDate;
			switch(e.keyCode){
				case 27: // escape
					this.hide();
					e.preventDefault();
					break;
				case 37: // left
				case 39: // right
					if (!this.keyboardNavigation) break;
					dir = e.keyCode == 37 ? -1 : 1;
					if (e.ctrlKey){
						newDate = this.moveYear(this.date, dir);
						newViewDate = this.moveYear(this.viewDate, dir);
					} else if (e.shiftKey){
						newDate = this.moveMonth(this.date, dir);
						newViewDate = this.moveMonth(this.viewDate, dir);
					} else {
						newDate = new Date(this.date);
						newDate.setUTCDate(this.date.getUTCDate() + dir);
						newViewDate = new Date(this.viewDate);
						newViewDate.setUTCDate(this.viewDate.getUTCDate() + dir);
					}
					if (this.dateWithinRange(newDate)){
						this.date = newDate;
						this.viewDate = newViewDate;
						this.setValue();
						this.update();
						e.preventDefault();
						dateChanged = true;
					}
					break;
				case 38: // up
				case 40: // down
					if (!this.keyboardNavigation) break;
					dir = e.keyCode == 38 ? -1 : 1;
					if (e.ctrlKey){
						newDate = this.moveYear(this.date, dir);
						newViewDate = this.moveYear(this.viewDate, dir);
					} else if (e.shiftKey){
						newDate = this.moveMonth(this.date, dir);
						newViewDate = this.moveMonth(this.viewDate, dir);
					} else {
						newDate = new Date(this.date);
						newDate.setUTCDate(this.date.getUTCDate() + dir * 7);
						newViewDate = new Date(this.viewDate);
						newViewDate.setUTCDate(this.viewDate.getUTCDate() + dir * 7);
					}
					if (this.dateWithinRange(newDate)){
						this.date = newDate;
						this.viewDate = newViewDate;
						this.setValue();
						this.update();
						e.preventDefault();
						dateChanged = true;
					}
					break;
				case 13: // enter
					this.hide();
					e.preventDefault();
					break;
				case 9: // tab
					this.hide();
					break;
			}
			if (dateChanged){
				this.element.trigger({
					type: 'changeDate',
					date: this.date
				});
				var element;
				if (this.isInput) {
					element = this.element;
				} else if (this.component){
					element = this.element.find('input');
				}
				if (element) {
					element.change();
				}
			}
		},

		showMode: function(dir) {
			if (dir) {
				this.viewMode = Math.max(0, Math.min(2, this.viewMode + dir));
			}
            /*
              vitalets: fixing bug of very special conditions:
              jquery 1.7.1 + webkit + show inline datepicker in bootstrap popover.
              Method show() does not set display css correctly and datepicker is not shown.
              Changed to .css('display', 'block') solve the problem.
              See https://github.com/vitalets/x-editable/issues/37
              
              In jquery 1.7.2+ everything works fine.
            */
            //this.picker.find('>div').hide().filter('.datepicker-'+DPGlobal.modes[this.viewMode].clsName).show();
			this.picker.find('>div').hide().filter('.datepicker-'+DPGlobal.modes[this.viewMode].clsName).css('display', 'block');
			this.updateNavArrows();
		}
	};

	$.fn.datepicker = function ( option ) {
		var args = Array.apply(null, arguments);
		args.shift();
		return this.each(function () {
			var $this = $(this),
				data = $this.data('datepicker'),
				options = typeof option == 'object' && option;
			if (!data) {
				$this.data('datepicker', (data = new Datepicker(this, $.extend({}, $.fn.datepicker.defaults,options))));
			}
			if (typeof option == 'string' && typeof data[option] == 'function') {
				data[option].apply(data, args);
			}
		});
	};

	$.fn.datepicker.defaults = {
	};
	$.fn.datepicker.Constructor = Datepicker;
	var dates = $.fn.datepicker.dates = {
		en: {
			days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
			daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
			daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"],
			months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
			monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
			today: "Today"
		}
	}

	var DPGlobal = {
		modes: [
			{
				clsName: 'days',
				navFnc: 'Month',
				navStep: 1
			},
			{
				clsName: 'months',
				navFnc: 'FullYear',
				navStep: 1
			},
			{
				clsName: 'years',
				navFnc: 'FullYear',
				navStep: 10
		}],
		isLeapYear: function (year) {
			return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0))
		},
		getDaysInMonth: function (year, month) {
			return [31, (DPGlobal.isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month]
		},
		validParts: /dd?|mm?|MM?|yy(?:yy)?/g,
		nonpunctuation: /[^ -\/:-@\[-`{-~\t\n\r]+/g,
		parseFormat: function(format){
			// IE treats \0 as a string end in inputs (truncating the value),
			// so it's a bad format delimiter, anyway
			var separators = format.replace(this.validParts, '\0').split('\0'),
				parts = format.match(this.validParts);
			if (!separators || !separators.length || !parts || parts.length == 0){
				throw new Error("Invalid date format.");
			}
			return {separators: separators, parts: parts};
		},
		parseDate: function(date, format, language) {
			if (date instanceof Date) return date;
			if (/^[-+]\d+[dmwy]([\s,]+[-+]\d+[dmwy])*$/.test(date)) {
				var part_re = /([-+]\d+)([dmwy])/,
					parts = date.match(/([-+]\d+)([dmwy])/g),
					part, dir;
				date = new Date();
				for (var i=0; i<parts.length; i++) {
					part = part_re.exec(parts[i]);
					dir = parseInt(part[1]);
					switch(part[2]){
						case 'd':
							date.setUTCDate(date.getUTCDate() + dir);
							break;
						case 'm':
							date = Datepicker.prototype.moveMonth.call(Datepicker.prototype, date, dir);
							break;
						case 'w':
							date.setUTCDate(date.getUTCDate() + dir * 7);
							break;
						case 'y':
							date = Datepicker.prototype.moveYear.call(Datepicker.prototype, date, dir);
							break;
					}
				}
				return UTCDate(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate(), 0, 0, 0);
			}
			var parts = date && date.match(this.nonpunctuation) || [],
				date = new Date(),
				parsed = {},
				setters_order = ['yyyy', 'yy', 'M', 'MM', 'm', 'mm', 'd', 'dd'],
				setters_map = {
					yyyy: function(d,v){ return d.setUTCFullYear(v); },
					yy: function(d,v){ return d.setUTCFullYear(2000+v); },
					m: function(d,v){
						v -= 1;
						while (v<0) v += 12;
						v %= 12;
						d.setUTCMonth(v);
						while (d.getUTCMonth() != v)
							d.setUTCDate(d.getUTCDate()-1);
						return d;
					},
					d: function(d,v){ return d.setUTCDate(v); }
				},
				val, filtered, part;
			setters_map['M'] = setters_map['MM'] = setters_map['mm'] = setters_map['m'];
			setters_map['dd'] = setters_map['d'];
			date = UTCDate(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0);
			if (parts.length == format.parts.length) {
				for (var i=0, cnt = format.parts.length; i < cnt; i++) {
					val = parseInt(parts[i], 10);
					part = format.parts[i];
					if (isNaN(val)) {
						switch(part) {
							case 'MM':
								filtered = $(dates[language].months).filter(function(){
									var m = this.slice(0, parts[i].length),
										p = parts[i].slice(0, m.length);
									return m == p;
								});
								val = $.inArray(filtered[0], dates[language].months) + 1;
								break;
							case 'M':
								filtered = $(dates[language].monthsShort).filter(function(){
									var m = this.slice(0, parts[i].length),
										p = parts[i].slice(0, m.length);
									return m == p;
								});
								val = $.inArray(filtered[0], dates[language].monthsShort) + 1;
								break;
						}
					}
					parsed[part] = val;
				}
				for (var i=0, s; i<setters_order.length; i++){
					s = setters_order[i];
					if (s in parsed)
						setters_map[s](date, parsed[s])
				}
			}
			return date;
		},
		formatDate: function(date, format, language){
			var val = {
				d: date.getUTCDate(),
				m: date.getUTCMonth() + 1,
				M: dates[language].monthsShort[date.getUTCMonth()],
				MM: dates[language].months[date.getUTCMonth()],
				yy: date.getUTCFullYear().toString().substring(2),
				yyyy: date.getUTCFullYear()
			};
			val.dd = (val.d < 10 ? '0' : '') + val.d;
			val.mm = (val.m < 10 ? '0' : '') + val.m;
			var date = [],
				seps = $.extend([], format.separators);
			for (var i=0, cnt = format.parts.length; i < cnt; i++) {
				if (seps.length)
					date.push(seps.shift())
				date.push(val[format.parts[i]]);
			}
			return date.join('');
		},
		headTemplate: '<thead>'+
							'<tr>'+
								'<th class="prev"><i class="icon-arrow-left"/></th>'+
								'<th colspan="5" class="switch"></th>'+
								'<th class="next"><i class="icon-arrow-right"/></th>'+
							'</tr>'+
						'</thead>',
		contTemplate: '<tbody><tr><td colspan="7"></td></tr></tbody>',
		footTemplate: '<tfoot><tr><th colspan="7" class="today"></th></tr></tfoot>'
	};
	DPGlobal.template = '<div class="datepicker">'+
							'<div class="datepicker-days">'+
								'<table class=" table-condensed">'+
									DPGlobal.headTemplate+
									'<tbody></tbody>'+
									DPGlobal.footTemplate+
								'</table>'+
							'</div>'+
							'<div class="datepicker-months">'+
								'<table class="table-condensed">'+
									DPGlobal.headTemplate+
									DPGlobal.contTemplate+
									DPGlobal.footTemplate+
								'</table>'+
							'</div>'+
							'<div class="datepicker-years">'+
								'<table class="table-condensed">'+
									DPGlobal.headTemplate+
									DPGlobal.contTemplate+
									DPGlobal.footTemplate+
								'</table>'+
							'</div>'+
						'</div>';
                        
    $.fn.datepicker.DPGlobal = DPGlobal;
    
}( window.jQuery );

/* /phpjs.js */
var phpjs = (function() {
  return {
    date: function(format, timestamp) {
      // http://kevin.vanzonneveld.net
      // +   original by: Carlos R. L. Rodrigues (http://www.jsfromhell.com)
      // +      parts by: Peter-Paul Koch (http://www.quirksmode.org/js/beat.html)
      // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // +   improved by: MeEtc (http://yass.meetcweb.com)
      // +   improved by: Brad Touesnard
      // +   improved by: Tim Wiel
      // +   improved by: Bryan Elliott
      //
      // +   improved by: Brett Zamir (http://brett-zamir.me)
      // +   improved by: David Randall
      // +      input by: Brett Zamir (http://brett-zamir.me)
      // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // +   improved by: Brett Zamir (http://brett-zamir.me)
      // +   improved by: Brett Zamir (http://brett-zamir.me)
      // +   improved by: Theriault
      // +  derived from: gettimeofday
      // +      input by: majak
      // +   bugfixed by: majak
      // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // +      input by: Alex
      // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
      // +   improved by: Theriault
      // +   improved by: Brett Zamir (http://brett-zamir.me)
      // +   improved by: Theriault
      // +   improved by: Thomas Beaucourt (http://www.webapp.fr)
      // +   improved by: JT
      // +   improved by: Theriault
      // +   improved by: Rafał Kukawski (http://blog.kukawski.pl)
      // +   bugfixed by: omid (http://phpjs.org/functions/380:380#comment_137122)
      // +      input by: Martin
      // +      input by: Alex Wilson
      // %        note 1: Uses global: php_js to store the default timezone
      // %        note 2: Although the function potentially allows timezone info (see notes), it currently does not set
      // %        note 2: per a timezone specified by date_default_timezone_set(). Implementers might use
      // %        note 2: this.php_js.currentTimezoneOffset and this.php_js.currentTimezoneDST set by that function
      // %        note 2: in order to adjust the dates in this function (or our other date functions!) accordingly
      // *     example 1: date('H:m:s \\m \\i\\s \\m\\o\\n\\t\\h', 1062402400);
      // *     returns 1: '09:09:40 m is month'
      // *     example 2: date('F j, Y, g:i a', 1062462400);
      // *     returns 2: 'September 2, 2003, 2:26 am'
      // *     example 3: date('Y W o', 1062462400);
      // *     returns 3: '2003 36 2003'
      // *     example 4: x = date('Y m d', (new Date()).getTime()/1000); 
      // *     example 4: (x+'').length == 10 // 2009 01 09
      // *     returns 4: true
      // *     example 5: date('W', 1104534000);
      // *     returns 5: '53'
      // *     example 6: date('B t', 1104534000);
      // *     returns 6: '999 31'
      // *     example 7: date('W U', 1293750000.82); // 2010-12-31
      // *     returns 7: '52 1293750000'
      // *     example 8: date('W', 1293836400); // 2011-01-01
      // *     returns 8: '52'
      // *     example 9: date('W Y-m-d', 1293974054); // 2011-01-02
      // *     returns 9: '52 2011-01-02'
      var that = this,
          jsdate, f, formatChr = /\\?([a-z])/gi,
          formatChrCb,
          // Keep this here (works, but for code commented-out
          // below for file size reasons)
          //, tal= [],
          _pad = function (n, c) {
              if ((n = n + '').length < c) {
                  return new Array((++c) - n.length).join('0') + n;
              }
              return n;
          },
          txt_words = ["Sun", "Mon", "Tues", "Wednes", "Thurs", "Fri", "Satur", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
      formatChrCb = function (t, s) {
          return f[t] ? f[t]() : s;
      };
      f = {
          // Day
          d: function () { // Day of month w/leading 0; 01..31
              return _pad(f.j(), 2);
          },
          D: function () { // Shorthand day name; Mon...Sun
              return f.l().slice(0, 3);
          },
          j: function () { // Day of month; 1..31
              return jsdate.getDate();
          },
          l: function () { // Full day name; Monday...Sunday
              return txt_words[f.w()] + 'day';
          },
          N: function () { // ISO-8601 day of week; 1[Mon]..7[Sun]
              return f.w() || 7;
          },
          S: function () { // Ordinal suffix for day of month; st, nd, rd, th
              var j = f.j();
              return j < 4 | j > 20 && ['st', 'nd', 'rd'][j%10 - 1] || 'th'; 
          },
          w: function () { // Day of week; 0[Sun]..6[Sat]
              return jsdate.getDay();
          },
          z: function () { // Day of year; 0..365
              var a = new Date(f.Y(), f.n() - 1, f.j()),
                  b = new Date(f.Y(), 0, 1);
              return Math.round((a - b) / 864e5) + 1;
          },

          // Week
          W: function () { // ISO-8601 week number
              var a = new Date(f.Y(), f.n() - 1, f.j() - f.N() + 3),
                  b = new Date(a.getFullYear(), 0, 4);
              return _pad(1 + Math.round((a - b) / 864e5 / 7), 2);
          },

          // Month
          F: function () { // Full month name; January...December
              return txt_words[6 + f.n()];
          },
          m: function () { // Month w/leading 0; 01...12
              return _pad(f.n(), 2);
          },
          M: function () { // Shorthand month name; Jan...Dec
              return f.F().slice(0, 3);
          },
          n: function () { // Month; 1...12
              return jsdate.getMonth() + 1;
          },
          t: function () { // Days in month; 28...31
              return (new Date(f.Y(), f.n(), 0)).getDate();
          },

          // Year
          L: function () { // Is leap year?; 0 or 1
              var j = f.Y();
              return j%4==0 & j%100!=0 | j%400==0;
          },
          o: function () { // ISO-8601 year
              var n = f.n(),
                  W = f.W(),
                  Y = f.Y();
              return Y + (n === 12 && W < 9 ? -1 : n === 1 && W > 9);
          },
          Y: function () { // Full year; e.g. 1980...2010
              return jsdate.getFullYear();
          },
          y: function () { // Last two digits of year; 00...99
              return (f.Y() + "").slice(-2);
          },

          // Time
          a: function () { // am or pm
              return jsdate.getHours() > 11 ? "pm" : "am";
          },
          A: function () { // AM or PM
              return f.a().toUpperCase();
          },
          B: function () { // Swatch Internet time; 000..999
              var H = jsdate.getUTCHours() * 36e2,
                  // Hours
                  i = jsdate.getUTCMinutes() * 60,
                  // Minutes
                  s = jsdate.getUTCSeconds(); // Seconds
              return _pad(Math.floor((H + i + s + 36e2) / 86.4) % 1e3, 3);
          },
          g: function () { // 12-Hours; 1..12
              return f.G() % 12 || 12;
          },
          G: function () { // 24-Hours; 0..23
              return jsdate.getHours();
          },
          h: function () { // 12-Hours w/leading 0; 01..12
              return _pad(f.g(), 2);
          },
          H: function () { // 24-Hours w/leading 0; 00..23
              return _pad(f.G(), 2);
          },
          i: function () { // Minutes w/leading 0; 00..59
              return _pad(jsdate.getMinutes(), 2);
          },
          s: function () { // Seconds w/leading 0; 00..59
              return _pad(jsdate.getSeconds(), 2);
          },
          u: function () { // Microseconds; 000000-999000
              return _pad(jsdate.getMilliseconds() * 1000, 6);
          },

          // Timezone
          e: function () { // Timezone identifier; e.g. Atlantic/Azores, ...
              // The following works, but requires inclusion of the very large
              // timezone_abbreviations_list() function.
  /*              return this.date_default_timezone_get();
  */
              throw 'Not supported (see source code of date() for timezone on how to add support)';
          },
          I: function () { // DST observed?; 0 or 1
              // Compares Jan 1 minus Jan 1 UTC to Jul 1 minus Jul 1 UTC.
              // If they are not equal, then DST is observed.
              var a = new Date(f.Y(), 0),
                  // Jan 1
                  c = Date.UTC(f.Y(), 0),
                  // Jan 1 UTC
                  b = new Date(f.Y(), 6),
                  // Jul 1
                  d = Date.UTC(f.Y(), 6); // Jul 1 UTC
              return 0 + ((a - c) !== (b - d));
          },
          O: function () { // Difference to GMT in hour format; e.g. +0200
              var tzo = jsdate.getTimezoneOffset(),
                  a = Math.abs(tzo);
              return (tzo > 0 ? "-" : "+") + _pad(Math.floor(a / 60) * 100 + a % 60, 4);
          },
          P: function () { // Difference to GMT w/colon; e.g. +02:00
              var O = f.O();
              return (O.substr(0, 3) + ":" + O.substr(3, 2));
          },
          T: function () { // Timezone abbreviation; e.g. EST, MDT, ...
              // The following works, but requires inclusion of the very
              // large timezone_abbreviations_list() function.
  /*              var abbr = '', i = 0, os = 0, default = 0;
              if (!tal.length) {
                  tal = that.timezone_abbreviations_list();
              }
              if (that.php_js && that.php_js.default_timezone) {
                  default = that.php_js.default_timezone;
                  for (abbr in tal) {
                      for (i=0; i < tal[abbr].length; i++) {
                          if (tal[abbr][i].timezone_id === default) {
                              return abbr.toUpperCase();
                          }
                      }
                  }
              }
              for (abbr in tal) {
                  for (i = 0; i < tal[abbr].length; i++) {
                      os = -jsdate.getTimezoneOffset() * 60;
                      if (tal[abbr][i].offset === os) {
                          return abbr.toUpperCase();
                      }
                  }
              }
  */
              return 'UTC';
          },
          Z: function () { // Timezone offset in seconds (-43200...50400)
              return -jsdate.getTimezoneOffset() * 60;
          },

          // Full Date/Time
          c: function () { // ISO-8601 date.
              return 'Y-m-d\\Th:i:sP'.replace(formatChr, formatChrCb);
          },
          r: function () { // RFC 2822
              return 'D, d M Y H:i:s O'.replace(formatChr, formatChrCb);
          },
          U: function () { // Seconds since UNIX epoch
              return jsdate / 1000 | 0;
          }
      };
      this.date = function (format, timestamp) {
          that = this;
          jsdate = (timestamp == null ? new Date() : // Not provided
          (timestamp instanceof Date) ? new Date(timestamp) : // JS Date()
          new Date(timestamp * 1000) // UNIX timestamp (auto-convert to int)
          );
          return format.replace(formatChr, formatChrCb);
      };
      this.strtotime = function strtotime(text, now) {
          //  discuss at: http://phpjs.org/functions/strtotime/
          //     version: 1109.2016
          // original by: Caio Ariede (http://caioariede.com)
          // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
          // improved by: Caio Ariede (http://caioariede.com)
          // improved by: A. Matías Quezada (http://amatiasq.com)
          // improved by: preuter
          // improved by: Brett Zamir (http://brett-zamir.me)
          // improved by: Mirko Faber
          //    input by: David
          // bugfixed by: Wagner B. Soares
          // bugfixed by: Artur Tchernychev
          //        note: Examples all have a fixed timestamp to prevent tests to fail because of variable time(zones)
          //   example 1: strtotime('+1 day', 1129633200);
          //   returns 1: 1129719600
          //   example 2: strtotime('+1 week 2 days 4 hours 2 seconds', 1129633200);
          //   returns 2: 1130425202
          //   example 3: strtotime('last month', 1129633200);
          //   returns 3: 1127041200
          //   example 4: strtotime('2009-05-04 08:30:00 GMT');
          //   returns 4: 1241425800

          var parsed, match, today, year, date, days, ranges, len, times, regex, i, fail = false;

          if (!text) {
            return fail;
          }

          // Unecessary spaces
          text = text.replace(/^\s+|\s+$/g, '')
            .replace(/\s{2,}/g, ' ')
            .replace(/[\t\r\n]/g, '')
            .toLowerCase();

          // in contrast to php, js Date.parse function interprets:
          // dates given as yyyy-mm-dd as in timezone: UTC,
          // dates with "." or "-" as MDY instead of DMY
          // dates with two-digit years differently
          // etc...etc...
          // ...therefore we manually parse lots of common date formats
          match = text.match(
            /^(\d{1,4})([\-\.\/\:])(\d{1,2})([\-\.\/\:])(\d{1,4})(?:\s(\d{1,2}):(\d{2})?:?(\d{2})?)?(?:\s([A-Z]+)?)?$/);

          if (match && match[2] === match[4]) {
            if (match[1] > 1901) {
              switch (match[2]) {
                case '-':
                  { // YYYY-M-D
                    if (match[3] > 12 || match[5] > 31) {
                      return fail;
                    }

                    return new Date(match[1], parseInt(match[3], 10) - 1, match[5],
                      match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
                  }
                case '.':
                  { // YYYY.M.D is not parsed by strtotime()
                    return fail;
                  }
                case '/':
                  { // YYYY/M/D
                    if (match[3] > 12 || match[5] > 31) {
                      return fail;
                    }

                    return new Date(match[1], parseInt(match[3], 10) - 1, match[5],
                      match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
                  }
              }
            } else if (match[5] > 1901) {
              switch (match[2]) {
                case '-':
                  { // D-M-YYYY
                    if (match[3] > 12 || match[1] > 31) {
                      return fail;
                    }

                    return new Date(match[5], parseInt(match[3], 10) - 1, match[1],
                      match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
                  }
                case '.':
                  { // D.M.YYYY
                    if (match[3] > 12 || match[1] > 31) {
                      return fail;
                    }

                    return new Date(match[5], parseInt(match[3], 10) - 1, match[1],
                      match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
                  }
                case '/':
                  { // M/D/YYYY
                    if (match[1] > 12 || match[3] > 31) {
                      return fail;
                    }

                    return new Date(match[5], parseInt(match[1], 10) - 1, match[3],
                      match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
                  }
              }
            } else {
              switch (match[2]) {
                case '-':
                  { // YY-M-D
                    if (match[3] > 12 || match[5] > 31 || (match[1] < 70 && match[1] > 38)) {
                      return fail;
                    }

                    year = match[1] >= 0 && match[1] <= 38 ? +match[1] + 2000 : match[1];
                    return new Date(year, parseInt(match[3], 10) - 1, match[5],
                      match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
                  }
                case '.':
                  { // D.M.YY or H.MM.SS
                    if (match[5] >= 70) { // D.M.YY
                      if (match[3] > 12 || match[1] > 31) {
                        return fail;
                      }

                      return new Date(match[5], parseInt(match[3], 10) - 1, match[1],
                        match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
                    }
                    if (match[5] < 60 && !match[6]) { // H.MM.SS
                      if (match[1] > 23 || match[3] > 59) {
                        return fail;
                      }

                      today = new Date();
                      return new Date(today.getFullYear(), today.getMonth(), today.getDate(),
                        match[1] || 0, match[3] || 0, match[5] || 0, match[9] || 0) / 1000;
                    }

                    return fail; // invalid format, cannot be parsed
                  }
                case '/':
                  { // M/D/YY
                    if (match[1] > 12 || match[3] > 31 || (match[5] < 70 && match[5] > 38)) {
                      return fail;
                    }

                    year = match[5] >= 0 && match[5] <= 38 ? +match[5] + 2000 : match[5];
                    return new Date(year, parseInt(match[1], 10) - 1, match[3],
                      match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
                  }
                case ':':
                  { // HH:MM:SS
                    if (match[1] > 23 || match[3] > 59 || match[5] > 59) {
                      return fail;
                    }

                    today = new Date();
                    return new Date(today.getFullYear(), today.getMonth(), today.getDate(),
                      match[1] || 0, match[3] || 0, match[5] || 0) / 1000;
                  }
              }
            }
          }

          // other formats and "now" should be parsed by Date.parse()
          if (text === 'now') {
            return now === null || isNaN(now) ? new Date()
              .getTime() / 1000 | 0 : now | 0;
          }
          if (!isNaN(parsed = Date.parse(text))) {
            return parsed / 1000 | 0;
          }

          date = now ? new Date(now * 1000) : new Date();
          days = {
            'sun': 0,
            'mon': 1,
            'tue': 2,
            'wed': 3,
            'thu': 4,
            'fri': 5,
            'sat': 6
          };
          ranges = {
            'yea': 'FullYear',
            'mon': 'Month',
            'day': 'Date',
            'hou': 'Hours',
            'min': 'Minutes',
            'sec': 'Seconds'
          };

          function lastNext(type, range, modifier) {
            var diff, day = days[range];

            if (typeof day !== 'undefined') {
              diff = day - date.getDay();

              if (diff === 0) {
                diff = 7 * modifier;
              } else if (diff > 0 && type === 'last') {
                diff -= 7;
              } else if (diff < 0 && type === 'next') {
                diff += 7;
              }

              date.setDate(date.getDate() + diff);
            }
          }

          function process(val) {
            var splt = val.split(' '), // Todo: Reconcile this with regex using \s, taking into account browser issues with split and regexes
              type = splt[0],
              range = splt[1].substring(0, 3),
              typeIsNumber = /\d+/.test(type),
              ago = splt[2] === 'ago',
              num = (type === 'last' ? -1 : 1) * (ago ? -1 : 1);

            if (typeIsNumber) {
              num *= parseInt(type, 10);
            }

            if (ranges.hasOwnProperty(range) && !splt[1].match(/^mon(day|\.)?$/i)) {
              return date['set' + ranges[range]](date['get' + ranges[range]]() + num);
            }

            if (range === 'wee') {
              return date.setDate(date.getDate() + (num * 7));
            }

            if (type === 'next' || type === 'last') {
              lastNext(type, range, num);
            } else if (!typeIsNumber) {
              return false;
            }

            return true;
          }

          times = '(years?|months?|weeks?|days?|hours?|minutes?|min|seconds?|sec' +
            '|sunday|sun\\.?|monday|mon\\.?|tuesday|tue\\.?|wednesday|wed\\.?' +
            '|thursday|thu\\.?|friday|fri\\.?|saturday|sat\\.?)';
          regex = '([+-]?\\d+\\s' + times + '|' + '(last|next)\\s' + times + ')(\\sago)?';

          match = text.match(new RegExp(regex, 'gi'));
          if (!match) {
            return fail;
          }

          for (i = 0, len = match.length; i < len; i++) {
            if (!process(match[i])) {
              return fail;
            }
          }

          // ECMAScript 5 only
          // if (!match.every(process))
          //    return false;

          return (date.getTime() / 1000);
      };
      return this.date(format, timestamp);
    }
  }
})();


/* /waypoints.min.js */
// Generated by CoffeeScript 1.6.2
/*
jQuery Waypoints - v2.0.4
Copyright (c) 2011-2014 Caleb Troughton
Dual licensed under the MIT license and GPL license.
https://github.com/imakewebthings/jquery-waypoints/blob/master/licenses.txt
*/
(function(){var t=[].indexOf||function(t){for(var e=0,n=this.length;e<n;e++){if(e in this&&this[e]===t)return e}return-1},e=[].slice;(function(t,e){if(typeof define==="function"&&define.amd){return define("waypoints",["jquery"],function(n){return e(n,t)})}else{return e(t.jQuery,t)}})(this,function(n,r){var i,o,l,s,f,u,c,a,h,d,p,y,v,w,g,m;i=n(r);a=t.call(r,"ontouchstart")>=0;s={horizontal:{},vertical:{}};f=1;c={};u="waypoints-context-id";p="resize.waypoints";y="scroll.waypoints";v=1;w="waypoints-waypoint-ids";g="waypoint";m="waypoints";o=function(){function t(t){var e=this;this.$element=t;this.element=t[0];this.didResize=false;this.didScroll=false;this.id="context"+f++;this.oldScroll={x:t.scrollLeft(),y:t.scrollTop()};this.waypoints={horizontal:{},vertical:{}};this.element[u]=this.id;c[this.id]=this;t.bind(y,function(){var t;if(!(e.didScroll||a)){e.didScroll=true;t=function(){e.doScroll();return e.didScroll=false};return r.setTimeout(t,n[m].settings.scrollThrottle)}});t.bind(p,function(){var t;if(!e.didResize){e.didResize=true;t=function(){n[m]("refresh");return e.didResize=false};return r.setTimeout(t,n[m].settings.resizeThrottle)}})}t.prototype.doScroll=function(){var t,e=this;t={horizontal:{newScroll:this.$element.scrollLeft(),oldScroll:this.oldScroll.x,forward:"right",backward:"left"},vertical:{newScroll:this.$element.scrollTop(),oldScroll:this.oldScroll.y,forward:"down",backward:"up"}};if(a&&(!t.vertical.oldScroll||!t.vertical.newScroll)){n[m]("refresh")}n.each(t,function(t,r){var i,o,l;l=[];o=r.newScroll>r.oldScroll;i=o?r.forward:r.backward;n.each(e.waypoints[t],function(t,e){var n,i;if(r.oldScroll<(n=e.offset)&&n<=r.newScroll){return l.push(e)}else if(r.newScroll<(i=e.offset)&&i<=r.oldScroll){return l.push(e)}});l.sort(function(t,e){return t.offset-e.offset});if(!o){l.reverse()}return n.each(l,function(t,e){if(e.options.continuous||t===l.length-1){return e.trigger([i])}})});return this.oldScroll={x:t.horizontal.newScroll,y:t.vertical.newScroll}};t.prototype.refresh=function(){var t,e,r,i=this;r=n.isWindow(this.element);e=this.$element.offset();this.doScroll();t={horizontal:{contextOffset:r?0:e.left,contextScroll:r?0:this.oldScroll.x,contextDimension:this.$element.width(),oldScroll:this.oldScroll.x,forward:"right",backward:"left",offsetProp:"left"},vertical:{contextOffset:r?0:e.top,contextScroll:r?0:this.oldScroll.y,contextDimension:r?n[m]("viewportHeight"):this.$element.height(),oldScroll:this.oldScroll.y,forward:"down",backward:"up",offsetProp:"top"}};return n.each(t,function(t,e){return n.each(i.waypoints[t],function(t,r){var i,o,l,s,f;i=r.options.offset;l=r.offset;o=n.isWindow(r.element)?0:r.$element.offset()[e.offsetProp];if(n.isFunction(i)){i=i.apply(r.element)}else if(typeof i==="string"){i=parseFloat(i);if(r.options.offset.indexOf("%")>-1){i=Math.ceil(e.contextDimension*i/100)}}r.offset=o-e.contextOffset+e.contextScroll-i;if(r.options.onlyOnScroll&&l!=null||!r.enabled){return}if(l!==null&&l<(s=e.oldScroll)&&s<=r.offset){return r.trigger([e.backward])}else if(l!==null&&l>(f=e.oldScroll)&&f>=r.offset){return r.trigger([e.forward])}else if(l===null&&e.oldScroll>=r.offset){return r.trigger([e.forward])}})})};t.prototype.checkEmpty=function(){if(n.isEmptyObject(this.waypoints.horizontal)&&n.isEmptyObject(this.waypoints.vertical)){this.$element.unbind([p,y].join(" "));return delete c[this.id]}};return t}();l=function(){function t(t,e,r){var i,o;r=n.extend({},n.fn[g].defaults,r);if(r.offset==="bottom-in-view"){r.offset=function(){var t;t=n[m]("viewportHeight");if(!n.isWindow(e.element)){t=e.$element.height()}return t-n(this).outerHeight()}}this.$element=t;this.element=t[0];this.axis=r.horizontal?"horizontal":"vertical";this.callback=r.handler;this.context=e;this.enabled=r.enabled;this.id="waypoints"+v++;this.offset=null;this.options=r;e.waypoints[this.axis][this.id]=this;s[this.axis][this.id]=this;i=(o=this.element[w])!=null?o:[];i.push(this.id);this.element[w]=i}t.prototype.trigger=function(t){if(!this.enabled){return}if(this.callback!=null){this.callback.apply(this.element,t)}if(this.options.triggerOnce){return this.destroy()}};t.prototype.disable=function(){return this.enabled=false};t.prototype.enable=function(){this.context.refresh();return this.enabled=true};t.prototype.destroy=function(){delete s[this.axis][this.id];delete this.context.waypoints[this.axis][this.id];return this.context.checkEmpty()};t.getWaypointsByElement=function(t){var e,r;r=t[w];if(!r){return[]}e=n.extend({},s.horizontal,s.vertical);return n.map(r,function(t){return e[t]})};return t}();d={init:function(t,e){var r;if(e==null){e={}}if((r=e.handler)==null){e.handler=t}this.each(function(){var t,r,i,s;t=n(this);i=(s=e.context)!=null?s:n.fn[g].defaults.context;if(!n.isWindow(i)){i=t.closest(i)}i=n(i);r=c[i[0][u]];if(!r){r=new o(i)}return new l(t,r,e)});n[m]("refresh");return this},disable:function(){return d._invoke.call(this,"disable")},enable:function(){return d._invoke.call(this,"enable")},destroy:function(){return d._invoke.call(this,"destroy")},prev:function(t,e){return d._traverse.call(this,t,e,function(t,e,n){if(e>0){return t.push(n[e-1])}})},next:function(t,e){return d._traverse.call(this,t,e,function(t,e,n){if(e<n.length-1){return t.push(n[e+1])}})},_traverse:function(t,e,i){var o,l;if(t==null){t="vertical"}if(e==null){e=r}l=h.aggregate(e);o=[];this.each(function(){var e;e=n.inArray(this,l[t]);return i(o,e,l[t])});return this.pushStack(o)},_invoke:function(t){this.each(function(){var e;e=l.getWaypointsByElement(this);return n.each(e,function(e,n){n[t]();return true})});return this}};n.fn[g]=function(){var t,r;r=arguments[0],t=2<=arguments.length?e.call(arguments,1):[];if(d[r]){return d[r].apply(this,t)}else if(n.isFunction(r)){return d.init.apply(this,arguments)}else if(n.isPlainObject(r)){return d.init.apply(this,[null,r])}else if(!r){return n.error("jQuery Waypoints needs a callback function or handler option.")}else{return n.error("The "+r+" method does not exist in jQuery Waypoints.")}};n.fn[g].defaults={context:r,continuous:true,enabled:true,horizontal:false,offset:0,triggerOnce:false};h={refresh:function(){return n.each(c,function(t,e){return e.refresh()})},viewportHeight:function(){var t;return(t=r.innerHeight)!=null?t:i.height()},aggregate:function(t){var e,r,i;e=s;if(t){e=(i=c[n(t)[0][u]])!=null?i.waypoints:void 0}if(!e){return[]}r={horizontal:[],vertical:[]};n.each(r,function(t,i){n.each(e[t],function(t,e){return i.push(e)});i.sort(function(t,e){return t.offset-e.offset});r[t]=n.map(i,function(t){return t.element});return r[t]=n.unique(r[t])});return r},above:function(t){if(t==null){t=r}return h._filter(t,"vertical",function(t,e){return e.offset<=t.oldScroll.y})},below:function(t){if(t==null){t=r}return h._filter(t,"vertical",function(t,e){return e.offset>t.oldScroll.y})},left:function(t){if(t==null){t=r}return h._filter(t,"horizontal",function(t,e){return e.offset<=t.oldScroll.x})},right:function(t){if(t==null){t=r}return h._filter(t,"horizontal",function(t,e){return e.offset>t.oldScroll.x})},enable:function(){return h._invoke("enable")},disable:function(){return h._invoke("disable")},destroy:function(){return h._invoke("destroy")},extendFn:function(t,e){return d[t]=e},_invoke:function(t){var e;e=n.extend({},s.vertical,s.horizontal);return n.each(e,function(e,n){n[t]();return true})},_filter:function(t,e,r){var i,o;i=c[n(t)[0][u]];if(!i){return[]}o=[];n.each(i.waypoints[e],function(t,e){if(r(i,e)){return o.push(e)}});o.sort(function(t,e){return t.offset-e.offset});return n.map(o,function(t){return t.element})}};n[m]=function(){var t,n;n=arguments[0],t=2<=arguments.length?e.call(arguments,1):[];if(h[n]){return h[n].apply(null,t)}else{return h.aggregate.call(null,n)}};n[m].settings={resizeThrottle:100,scrollThrottle:30};return i.load(function(){return n[m]("refresh")})})}).call(this);
/* /overrides.js */
// set Backbone defaults
Backbone.emulateHTTP = true;
Backbone.emulateJSON = true;

/**
 * Override the setPosition function to correct the x coordinate
 * if it is tip extends beyond the width of the window on either side
 */
$.fn.editableContainer.Constructor.prototype.setPosition = function(){
  (function() {    
      var $tip = this.tip()
      , inside
      , pos
      , actualWidth
      , actualHeight
      , placement
      , tp;

      placement = typeof this.options.placement === 'function' ?
      this.options.placement.call(this, $tip[0], this.$element[0]) :
      this.options.placement;

      inside = /in/.test(placement);
     
      $tip
    //  .detach()
    //vitalets: remove any placement class because otherwise they dont influence on re-positioning of visible popover
      .removeClass('top right bottom left')
      .css({ top: 0, left: 0, display: 'block' });
    //  .insertAfter(this.$element);
     
      pos = this.getPosition(inside);

      actualWidth = $tip[0].offsetWidth;
      actualHeight = $tip[0].offsetHeight;

      switch (inside ? placement.split(' ')[1] : placement) {
          case 'bottom':
              tp = {top: pos.top + pos.height, left: pos.left + pos.width / 2 - actualWidth / 2};
              break;
          case 'top':
              tp = {top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2};
              break;
          case 'left':
              tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth};
              break;
          case 'right':
              tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width};
              break;
      }
      
      // check that the x position is withinin the screen
      if( tp.left < 0 ) {
        var offset = tp.left-15;
        tp.left = 5;
        $tip.find('.arrow').css({'margin-left': offset });
      }
      else if( tp.left + actualWidth > $(window).width() ){
        var offset = (tp.left+actualWidth) - ($(window).scrollLeft() + $(window).width());
        tp.left -= (offset + 5);
        $tip.find('.arrow').css({'margin-left': offset-5 });
      }
      else {
        $tip.find('.arrow').css({'margin-left': -10 });
      }
      
      // check to make sure that the y position is not off the top of the screen
      if( tp.top < $(window).scrollTop() ){
        // change the placement to the bottom
        tp.top = pos.top + pos.height;
        placement = 'bottom';
      }
      

      $tip
      .offset(tp)
      .addClass(placement)
      .addClass('in');
      
  }).call(this.container());
};
 
/* /op/namespace.js */
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
/* /op/data/route/Routes.js */
(function($){
  op.ns('data.route').Routes = Backbone.Router.extend({
    initialize: function(options) {
      this.lightbox = op.Lightbox.getInstance();
      for(i in options) {
        if(options.hasOwnProperty(i))
          this[i] = options[i];
      }
    },
    photoDetail: function(id) {
      var photo = op.data.store.Photos.get(id);
      if(typeof photo === 'object')
        this.render(photo.toJSON());
    },
    photoModal: function(id) {
      this.lightbox.open(id);
    },
    photosList: function(id) {
      this.lightbox.hide();
    }
  });
})(jQuery);

/* /op/data/model/Album.js */
(function($){
  op.ns('data.model').Album = Backbone.Model.extend({
    sync: function(method, model, options) {
      options.data = {};
      options.data.crumb = TBX.crumb();
      options.data.httpCodes='*';
      switch(method) {
        case 'read':
          options.url = '/album/'+model.get('id')+'/view.json';
          break;
        case 'update':
          options.url = '/album/'+model.get('id')+'/update.json';
          var changedParams = model.changedAttributes();
          for(i in changedParams) {
            if(changedParams.hasOwnProperty(i)) {
              options.data[i] = changedParams[i];
            }
          }
          break;
        case 'delete':
          options.url = '/album/'+model.get('id')+'/delete.json';
          break;
      }
      return Backbone.sync(method, model, options);
    },
    parse: function(response) {
      return response.result;
    }
  });
})(jQuery);

/* /op/data/model/Batch.js */
(function($){
  op.ns('data.model').Batch = Backbone.Model.extend({
    initialize: function() {
      this.set('loading', false);
    },
    sync: function(method, model) {},
  });
})(jQuery);

/* /op/data/model/Notification.js */
(function($){
  op.ns('data.model').Notification = Backbone.Model.extend({
    sync: function(method, model) {
      model.trigger('change');
    },
    parse: function(response) {
      return response.result;
    }
  });
})(jQuery);


/* /op/data/model/Profile.js */
(function($){
  /* ------------------------------- Profile ------------------------------- */
  op.ns('data.model').Profile = Backbone.Model.extend({
    sync: function(method, model, options) {
      options.data = {};
      options.data.crumb = TBX.crumb();
      options.data.httpCodes='*';
      switch(method) {
        case 'read':
          options.url = '/user/profile.json';
          break;
        case 'update':
          options.url = '/user/profile.json';
          var changedParams = model.changedAttributes();
          for(i in changedParams) {
            if(changedParams.hasOwnProperty(i)) {
              options.data[i] = changedParams[i];
            }
          }
          break;
      }
      return Backbone.sync(method, model, options);
    },
    parse: function(response) {
      return response.result;
    }
  });
})(jQuery);

/* /op/data/model/Photo.js */
(function($){
  op.ns('data.model').Photo = Backbone.Model.extend({
    sync: function(method, model, options) {
      options.data = {};
      options.data.crumb = TBX.crumb();
      options.data.httpCodes='*';
      switch(method) {
        case 'read':
          options.url = '/photo/'+model.get('id')+'/view.json';
          break;
        case 'update':
          options.url = '/photo/'+model.get('id')+'/update.json';
          var changedParams = model.changedAttributes();
          for(i in changedParams) {
            if(changedParams.hasOwnProperty(i)) {
              if(i == 'active') {
                isRestore = changedParams[i];
              } else if(i == 'dateTaken') {
                if(changedParams[i].search(/^([+-])/) === 0) {
                  // add or subtract units. See gh-321
                  options.data[i] = phpjs.strtotime(changedParams[i], model.previous('dateTaken'));
                } else {
                  options.data[i] = phpjs.strtotime(changedParams[i]);
                }
              } else {
                options.data[i] = changedParams[i];
              }
            }
          }
          break;
        case 'delete':
          options.url = '/photo/'+model.get('id')+'/delete.json';
          break;
      }
      return Backbone.sync(method, model, options);
    },
    parse: function(response) {
      return response.result;
    }
  });
})(jQuery);

/* /op/data/model/ProgressBar.js */
(function($){
  op.ns('data.model').ProgressBar = Backbone.Model.extend({
    defaults: {
      "total": 0,
      "success": 0,
      "warning": 0,
      "danger": 0,
      "striped": "progress-striped"
    },
    completed: function() {
      this.set('striped', '');
    },
    sync: function(method, model) {
      model.trigger('change');
    }
  });
})(jQuery);

/* /op/data/model/Tag.js */
(function($){
  op.ns('data.model').Tag = Backbone.Model.extend({
    /**
     * TODO - implement the sync method for Tags
     */
  });
})(jQuery);

/* /op/data/collection/Album.js */
op.ns('data.collection').Album = Backbone.Collection.extend({
  model         :op.data.model.Album,
  localStorage  :'op-albums'
});
/* /op/data/collection/Profile.js */
op.ns('data.collection').Profile = Backbone.Collection.extend({
  model         :op.data.model.Profile,
  localStorage  :'op-profiles'
});
/* /op/data/collection/Photo.js */
op.ns('data.collection').Photo = Backbone.Collection.extend({
  model         :op.data.model.Photo,
  localStorage  :'op-photos'
});
/* /op/data/collection/Tag.js */
op.ns('data.collection').Tag = Backbone.Collection.extend({
  model         :op.data.model.Tag,
  localStorage  :'op-tags'
});
/* /op/data/store/Albums.js */
/**
 * Global Store
 * - these may be better within an "app.js" file or something,
 * but will leave them here for now.
 */
op.ns('data.store').Albums = new op.data.collection.Album();

/* /op/data/store/Profiles.js */
/**
 * Global Store
 * - these may be better within an "app.js" file or something,
 * but will leave them here for now.
 */
op.ns('data.store').Profiles = new op.data.collection.Profile();
/* /op/data/store/Photos.js */
/**
 * Global Store
 * - these may be better within an "app.js" file or something,
 * but will leave them here for now.
 */
op.ns('data.store').Photos = new op.data.collection.Photo();
/* /op/data/store/Tags.js */
/**
 * Global Store
 * - these may be better within an "app.js" file or something,
 * but will leave them here for now.
 */
op.ns('data.store').Tags = new op.data.collection.Tag();
/* /op/data/view/Editable.js */
(function($){
  
  op.ns('data.view').Editable = Backbone.View.extend({
    
    getViewData : function(){
      return this.model.toJSON();
    },
    
    render : function(){
      
      var self = this;
      
      // whenever this is re-rendered, check for any old editables
      // and hide them in case the tip is open.
      if( this.editable ) for(var i in this.editable){
        var e;
        if( this.editable.hasOwnProperty(i) && this.el && (e = $(this.el).find(i).data('editable')) ){
          e.hide();
        }
      }
      
      this.trigger('beforerender', this);
      
      $(this.el).html(this.template(this.getViewData()));
      
      if( !this.editable ){
        this.trigger('afterrender', this);
        return this;
      }
      
      for( var i in this.editable ){
        if( this.editable.hasOwnProperty(i) ){
          
          var $el = $(this.el).find(i);
          
          if( $el.length === 0 ) continue;
          
          var config = _.extend({
            placement: 'top',
            url : function(params){
              var d = new $.Deferred;
              self.model.set(params.name, params.value, {silent:true});
              self.model.save(null, {
                success : function(){
                  d.done();
                },
                error : function( TODO ){
                  /**
                   * TODO: should report the error, but I'm not sure what the
                   * arguments / return values are.
                   */
                  d.reject();
                }
              });
              return d;
            }
          }, this.editable[i]);

          if(typeof(this.editable[i].shown) !== "undefined") {
            $el.on('shown', this.editable[i].shown);
          }
          
          if( $el.find('.value').length ){
            config.value = $el.find('.value').text();
          }
          
          // grab the "on" property
          var on = config.on;
          
          delete config.on;
          $el.editable(config);
          $el.data('editable').view = self;
          
          if( on ) $el.on( on );
        }
      }
      this.trigger('afterrender', this);
      return this;
    }
  });
  
})(jQuery);
/* /op/data/view/BatchIndicator.js */
(function($){
  op.ns('data.view').BatchIndicator = Backbone.View.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
      OP.Util.on('callback:batch-clear', this.clearCallback);
    },
    model: this.model,
    className: 'batch-meta',
    template    :_.template($('#batch-meta').html()),
    modelChanged: function() {
      this.render();
    },
    events: {
      'click .clear': 'clear',
      'click .showBatchForm': 'showBatchForm'
    },
    clear: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), batch = OP.Batch, ids = batch.ids();
      batch.clear();
      TBX.notification.show(TBX.format.sprintf(TBX.strings.batchConfirm, 0, 's'), 'flash', 'confirm');
    },
    showBatchForm: function(ev) {
      TBX.handlers.click.showBatchForm(ev);
    },
    clearCallback: function() {
      var model = TBX.init.pages.photos.batchModel;
      model.set('count', 0);
    },
    render: function(){
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    }
  });
})(jQuery);

/* /op/data/view/AlbumCover.js */
(function($){
  op.ns('data.view').AlbumCover = op.data.view.Editable.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
    },
    model: this.model,
    className: 'album-meta',
    template    :_.template($('#album-meta').html()),
    editable    : {
      '.name.edit' : {
        name: 'name',
        placement: 'top',
        title: 'Edit Album Name',
        validate : function(value){
          if($.trim(value) == ''){
            return 'Please enter a name';
          }
          return null;
        }
      }
    },
    events: {
      'click .delete': 'delete_',
      'click .share': 'share'
    },
    modelChanged: function() {
      this.render();
    },
    modelDestroyed: function() {
      var $el = $('.album-'+this.get('id'));
      $el.fadeTo('medium', .25);
    },
    delete_: function(ev) {
      ev.preventDefault();
      var $el = $(ev.target), id = $el.attr('data-id'), model = this.model, ask;
      ask = prompt('Type DELETE if you\'d like to delete the album '+model.get('name') + '.');
      if(ask === 'DELETE')
        model.destroy({success: this.modelDestroyed.bind(model), error: TBX.notification.display.generic.error});
      else
        TBX.notification.show('Your request to delete ' + model.get('name') + ' was cancelled.', 'flash', 'error');
    },
    share: function(ev) {
      ev.preventDefault();
      var $el = $(ev.currentTarget), id = $el.attr('data-id');
      OP.Util.makeRequest('/share/album/'+id+'/view.json', {crumb: TBX.crumb()}, TBX.callbacks.share, 'json', 'get');
    }
  });
})(jQuery);

/* /op/data/view/Notification.js */
(function($){
  op.ns('data.view').Notification = Backbone.View.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
    },
    model: this.model,
    className: 'notification-meta',
    template    :_.template($('#notification-meta').html()),
    modelChanged: function() {
      this.render();
    },
    render: function(){
      var that = this, $el = $(this.el), notification = that.model.toJSON(), exists = $('.trovebox-message').length === 1;
      if(exists) {
        $el.html(that.template(notification));
        TBX.highlight.run($('.alert', $el));
      } else {
        $el.css('display', 'none').html(that.template(notification)).slideDown('medium');
      }
      return this;
    }
  });
})(jQuery);


/* /op/data/view/PhotoDetail.js */
(function($){
  var convertDateForInput = function(ev) {
    var $el = $(ev.currentTarget), date = $('.display-for-edit', $el).attr('data-value'), $input = $('.editable-container .date-inline-input');
    $input.val(date);
  };
  var validateDateFromInput = function(value) {
    if(phpjs.strtotime(value) === false) {
      return 'Could not process date.';
    }
  };
  var CommentView = Backbone.View.extend({
    template : _.template($('#photo-comment-tmpl').html()),
    render : function(){
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    }
  });
  
  var CommentsView = Backbone.View.extend({
    template : _.template($('#photo-comments-tmpl').html()),
    render : function(){
      $(this.el).html(this.template(this.model.toJSON()));
      this.updateCommentList();
      return this;
    },
    updateCommentList : function(){
      // how do we know what comments
      $el = $(this.el).find('ul.comment-list');
      $el.empty().hide();
      // get the comments...
      var actions = this.model.get('actions');
      
      /**
       *
       * DEMO to be replaced with the real actions
       *
       */
      actions = [{
        type: 'comment',
        name: 'Mark',
        email: 'fabrizim@owlwatch.com',
        avatar: 'http://openphotofabrizio.s3.amazonaws.com/custom/201212/7d0cae-IMG_4413_100x100xCR.jpg',
        value: 'ZOMG! This is like wicked awesome!',
        date : 'January 11, 2013 12:07am EST'
      },{
        type: 'comment',
        name: 'Mark',
        email: 'fabrizim@owlwatch.com',
        avatar: 'http://openphotofabrizio.s3.amazonaws.com/custom/201212/7d0cae-IMG_4413_100x100xCR.jpg',
        value: 'Meh.',
        date : 'January 11, 2013 12:11am EST'
      }];
      
      $(this.el).find('.comment-count').html( actions.length );
      _.each(actions, function(action){
        var model = new Backbone.Model(action);
        $el.append(new CommentView({model: model}).render().el);
      });
      $el.show();
    }
  });
  
  var TitleView = op.data.view.Editable.extend({
    template : _.template($('#photo-detail-title-tmpl').html()),
    editable    : {
      '.title.edit' : {
        name: 'title',
        title: 'Edit Photo Title',
        placement: 'bottom'
      }
    }
  });
  
  var DateView = op.data.view.Editable.extend({
    template : _.template($('#photo-detail-date-tmpl').html()),
    editable    : {
      '.date.edit' : {
        name: 'dateTaken',
        title: 'Edit Photo Date',
        emptytext: 'Set a date',
        placement: 'top',
        inputclass: 'date-inline-input',
        shown: convertDateForInput,
        validate: validateDateFromInput
      }
    }
  });
  
  var DescriptionView = op.data.view.Editable.extend({
    template : _.template($('#photo-detail-description-tmpl').html()),
    editable    : {
      '.text.edit' : {
        type: 'textarea',
        name: 'description',
        title: 'Edit Photo Description',
        emptytext: 'Add a description',
        placement: 'top'
      }
    }
  });
  
  var PhotoMetaView = Backbone.View.extend({
    template : _.template($('#photo-detail-meta-tmpl').html()),
    render : function(){
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
    events : {
      'click .lightbox': 'lightbox',
      'click .permission.edit': 'permission',
      'click .profile': 'profile',
      'click .rotate': 'rotate',
      'click .share': 'share'
    },
    lightbox: function(ev) {
      ev.preventDefault();
      op.Lightbox.getInstance().open(this.model.get('id'));
      $('.detail-link').hide();
    },
    permission: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), model = this.model;
      model.set('permission', model.get('permission') == 0 ? 1 : 0, {silent:false});
      model.save();
    },
    profile: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), 
          ownerModel = op.data.store.Profiles.get(TBX.profiles.getOwner()),
          viewerModel = op.data.store.Profiles.get(TBX.profiles.getViewer());
      ownerModel.set('photoId', id, {silent:true});
      ownerModel.save(null, {error: TBX.notification.display.generic.error, success: function(){ TBX.notification.show('Your profile photo was successfully updated.', 'flash', 'confirm'); }});
      if(TBX.profiles.getOwner() !== TBX.profiles.getViewer()) {
        viewerModel.set('photoId', id, {silent:true});
        viewerModel.save();
      }
    },
    rotate: function(ev) {
      ev.preventDefault();
      var $el = $(ev.currentTarget), model = this.model, id = model.get('id'), size = '870x870', value='90';
      OP.Util.makeRequest('/photo/'+id+'/transform.json', {crumb: TBX.crumb(),rotate:value,returnSizes:size,generate:'true'}, TBX.callbacks.rotate.bind({model: model, id: id, size: size}), 'json', 'post');
    },
    share: function(ev) {
      ev.preventDefault();
      var $el = $(ev.currentTarget), id = $el.attr('data-id');
      OP.Util.makeRequest('/share/photo/'+id+'/view.json', {crumb: TBX.crumb()}, TBX.callbacks.share, 'json', 'get');
    }
  });
  
  var RightsView = Backbone.View.extend({
    template : _.template($('#photo-detail-rights-tmpl').html()),
    render : function(){
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    }
  });
  
  var CollapsiblesView = Backbone.View.extend({
    open : {},
    template : _.template($('#photo-detail-collapsibles-tmpl').html()),
    render : function(){
      var self = this;
      
      $(this.el).html(this.template(this.model.toJSON()));
      $(this.el).find('.collapse').on({
        'show' : function(e){
          self.open[$(e.target).attr('id')] = true;
          $(e.target).parents('.collapsibles li').addClass('active');
          
          if( e.target.id == 'photo-location' ){
            self.updateMap();
          }
          
        },
        'hide' : function(e){
          self.open[$(e.target).attr('id')] = false;
          $(e.target).parents('.collapsibles li').removeClass('active');
        }
      });
      
      for(var i in this.open ) if( this.open[i] ){
        $('a[href=#'+i+']').parents('.collapsibles li').addClass('active');
        $('#'+i).addClass('in');
        if(i === 'photo-location' ) this.updateMap();
      }
      
      
      return this;
    },
    updateMap : function(){
      var lat,lng,$mapEl;

      
      if((lat=this.model.get('latitude')) && (lng=this.model.get('longitude')) ){
        $mapEl = $($(this.el).find('.map')[0]);
        $mapEl.html('<img src="/map/'+lat+'/'+lng+'/7/275x160/roadmap/map.png">');
      }
    }
  });
  
  op.ns('data.view').PhotoDetail = op.data.view.Editable.extend({
    
    largePath : 'path870x870',
    thumbPath : 'path180x180xCR',
    _filter: null,
    _query: location.search || '',
    
    viewMap : {
      '.comments'     :CommentsView,
      '.photo-title'  :TitleView,
      '.description'  :DescriptionView,
      '.photo-meta'   :PhotoMetaView,
      '.photo-date'   :DateView,
      '.collapsibles' :CollapsiblesView,
      '.rights'       :RightsView
    },
    
    initialize: function() {
      this.model.on('change', this.updateViews, this);
      this.on('afterrender', this.onAfterRender, this);
      this.store = op.data.store.Photos;
      this.initialModel = this.model;
      this.thumbs = {};
      this.views = {};

      if(location.pathname.search('/photo/') > -1)
        this._filter = /\/photo\/([^/]+)(\/.*)?\/view/.exec(location.pathname)[2] || '';
      else
        this._filter = /\/p\/([^/]+)(\/.*)?/.exec(location.pathname)[2] || '';

      var self = this;
      op.Lightbox.getInstance().on('updatemodel', function(model){
        self.go(model.get('id'));
      });
    },
    model: this.model,
    className: 'photo-detail-meta',
    template    :_.template($('#photo-detail-meta').html()),
    modelChanged: function() {
      this.render();
    },
    
    onAfterRender: function(){
      var self = this;
      this.setupPagination();
      this.updateViews();
    },
    
    updateModel : function(model){
      var $title = $('title');
      this.model.off(null, null, this);
      this.model = model;
      this.model.on('change', this.updateViews, this);
      this.updateViews();
      // change the main image
      $(this.el).find('.photo img')
        .attr('src', this.model.get(this.largePath))
      $title.html(TBX.format.sprintf('%s / Photo / %s / Trovebox', TBX.profiles.getOwnerUsername(), this.model.get('title') || this.model.get('filenameOriginal')));

      $(this.el).find('.photo .photo-view-modal-click')
        .attr('data-id', this.model.get('id'))
    },
    
    updateViews : function(){
      this.updateUserBadge();
      for(var i in this.viewMap )
        if( this.viewMap.hasOwnProperty(i) ){
          this.updateView(i);
        }
    },
    
    updateView : function(name){
      if( !this.views[name] ){
        this.views[name] = new this.viewMap[name]({
          el: $(this.el).find(name),
          model: this.model
        }).render();
      }
      else{
        this.views[name].model = this.model;
        this.views[name].render();
      }
    },
    
    updateUserBadge : function(){
      // update the user badge...
      var $el = $(this.el).find('.userbadge')
        , model = op.data.store.Profiles.get(this.model.get('owner')) 
      
      if( model ){
        new op.data.view.UserBadge({el: $el, model: model}).render();
      }
    },
    
    setupPagination : function(){
      var $scroller = $(this.el).find('.pagination .photos .scroller')
        , shimDiv;
      
      $(this.el).find('.pagination .arrow-prev').click(_.bind(this.prev, this));  
      $(this.el).find('.pagination .arrow-next').click(_.bind(this.next, this));
      $(this.el).find('img.photo-large').click(_.bind(this.next, this));
      
      // create the shim...
      shimDiv = $('<div class="thumb thumb-shim"><div class="border"><div class="inner" /></div></div>')
        .appendTo($(this.el).find('.pagination .photos .scroller .thumbs'));
        
      $('<img />')
        .attr('src', this.model.get(this.thumbPath))
        .appendTo(shimDiv.find('.inner'))
      
      this.addModel(this.model);
      this.addPreviousFromModel(this.model);
      this.addNextFromModel(this.model);
    },
    
    addPreviousFromModel : function(model){
      this._addMoreFromModel(model, 'next');
    },
    
    addNextFromModel : function(model){
      this._addMoreFromModel(model, 'previous');
    },
    
    _addMoreFromModel : function(model, dir){
      if( !dir ) dir = 'next';
      if( (ar = model.get(dir)) && ar.length ){
        for(var i=0; i<ar.length; i++ ){
          var m = new op.data.model.Photo(ar[i]);
          if( ar.length === 1 ){
            m[dir==='next'?'_last':'_first'] = true;
          }
          this.addModel(m, dir==='next');
        }
      }
      else {
        model[dir==='next'?'_last':'_first'] = true;
      }
    },
    
    addModel : function(model, pos){
      if( (m = this.store.get(model.get('id'))) ){
        this.addThumbNav(m);
        return m;
      }
      
      this.store[pos===false?'unshift':'push'](model);
      this.addThumbNav(model);
      
      /**
       * TODO - figure out why routing won't work...
       *
      var self = this
        , a = document.createElement('a');
        
      a.href = model.get('url');
      this.router.route( a.pathname, 'photo_'+model.get('id'), function(){
        self.go(model.get('id'));
      });
      this.router.on('route:photo_'+model.get('id'), function(){
        
      });
      */
      return model;
    },
    
    addThumbNav : function(model){
      var t
        , id = model.get('id')
        
      if( (t = this.thumbs[id]) ){
        return t;
      }
      
      // get the difference
      var init = _.indexOf( this.store.models, this.initialModel )
        , diff = _.indexOf( this.store.models, model ) - init
        , x = diff * 33.33333333 + 33.33333333 + .625;
        
      t = this.thumbs[id] = $('<div class="thumb" />')
        .appendTo( $(this.el).find('.pagination .photos .scroller .thumbs') )
        .css({left: x+'%'})
        .click(_.bind(this.thumbClick, this, model.get('id')))
        
      var b = $('<div class="border" />').appendTo(t);
      var b2 = $('<div class="inner" />').appendTo(b);
      
      var i = $('<img />')
        .attr('src', model.get(this.thumbPath))
        .appendTo(b2);
        
      if( this.model == model )
        t.addClass('active');
        
      return t;
    },
    
    thumbClick : function(id){
      /**
       * TODO - this is where you would call the router kmethod
       * if it was working.
       */
      //this.router.navigate('/p/'+id, {trigger: true});
      this.go(id);
    },
    
    next : function(){
      var cur = _.indexOf( this.store.models, this.model );
      if( cur < this.store.models.length-1 ){
        this.go( this.store.at(cur + 1).get('id') );
      }
    },
    
    prev : function(){
      var cur = _.indexOf( this.store.models, this.model );
      if( cur > 0 ){
        this.go( this.store.at(cur - 1).get('id') );
      }
    },
    
    go : function(id){
      if( this.model === this.store.get(id)) return;
      
      // get the difference
      var init = _.indexOf( this.store.models, this.initialModel )
        , diff = _.indexOf( this.store.models, this.store.get(id) ) - init
        , router = op.data.store.Router;
      
      $(this.el).find('.pagination .photos .scroller .thumbs')
        .stop()
        // might be better to use css3 translate3d and transition properties instead
        .animate({'left': (-33.33333333*diff)+'%'}, 200)
      
      $(this.el).find('.pagination .photos .scroller .thumbs .thumb').removeClass('active');
      this.thumbs[id].addClass('active');
      
      // lets also get next/prev
      var x = _.indexOf( this.store.models, this.store.get(id) )
        , c = _.indexOf( this.store.models, this.model )
      
      
      this.loadMore( x > c );
      this.updateModel(this.store.get(id));
      router.navigate('/p/'+id+this._filter+location.search, {trigger: false});
    },
    
    loadMore : function( dir ){
      var model
        , self = this
        , sizes = _.map(
          [this.thumbPath, this.largePath],
          function(str){ return str.replace(/^path/,''); }
        ).join(',') 
        , apiParams = {nextprevious:'1', generate: 'true', returnSizes:sizes, sortBy:TBX.util.getQueryParam('sortBy')}
        , endpoint
        , fn = 'next';
        
      if( dir ){
        // going to add more to the end...
        // first lets get the last model in the store
        // and check if its the last one
        var model = this.store.at( this.store.models.length-1 );
        if( model._last ) return;
      }
      else {
        fn = 'previous';
        var model = this.store.at(0);
        if( model._first ) return;
      }
      
      endpoint = TBX.init.pages.photo.filterOpts ?
        '/photo/'+model.get('id')+'/view.json' :
        '/photo/'+model.get('id')+'/'+TBX.init.pages.photo.filterOpts+'/view.json' ;
      
      OP.Util.makeRequest(endpoint, apiParams, function(response) {
        if( response.result ){
          if( response.result.next ) model.set('next', response.result.next);
          if( response.result.previous ) model.set('previous', response.result.previous);
        }
        self._addMoreFromModel(model, fn);
      }, 'json', 'get');
    }
  });
})(jQuery);


/* /op/data/view/PhotoGallery.js */
(function($){
  op.ns('data.view').PhotoGallery = op.data.view.Editable.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
      OP.Util.on('callback:photo-destroy', this.modelDestroyed);
      OP.Util.on('callback:batch-remove', this.batchRemove);
      OP.Util.on('callback:batch-add', this.batchAdd);
    },
    batchAdd: function(photo) {
      var model = TBX.init.pages.photos.batchModel, batch = OP.Batch;
      $('.photo-id-'+photo.id).addClass('pinned');
      model.set('count', batch.length());
      model.trigger('change');
    },
    batchRemove: function(id) {
      var model = TBX.init.pages.photos.batchModel, batch = OP.Batch;
      $('.photo-id-'+id).removeClass('pinned');
      model.set('count', batch.length());
      model.trigger('change');
    },
    model: this.model,
    className: 'photo-meta',
    template    :_.template($('#photo-meta').html()),
    editable    : {
      '.title.edit a' : {
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
      'click .album.edit': 'album',
      'click .delete.edit': 'delete_',
      'click .permission.edit': 'permission',
      'click .pin.edit': 'pin',
      'click .share': 'share'
    },
    album: function(ev) {
      TBX.handlers.click.setAlbumCover(ev);
    },
    delete_: function(ev) {
      TBX.handlers.click.showBatchForm(ev);
    },
    permission: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), model = this.model, permission = model.get('permission') == 0 ? 1 : 0;
      model.set('permission', permission, {silent: true});
      model.save(null, {error: TBX.notification.display.generic.error});
    },
    pin: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), batch = OP.Batch, count, photo = op.data.store.Photos.get(id).toJSON();
      if(batch.exists(id)) // exists, we need to remove
        batch.remove(id);
      else // let's add it
        batch.add(id, photo);

      count = batch.length();
      TBX.notification.show(TBX.format.sprintf(TBX.strings.batchConfirm, count, count > 1 ? 's' : ''), 'flash', 'confirm');
    },
    share: function(ev) {
      ev.preventDefault();
      var $el = $(ev.currentTarget), id = $el.attr('data-id');
      OP.Util.makeRequest('/share/photo/'+id+'/view.json', {crumb: TBX.crumb()}, TBX.callbacks.share, 'json', 'get');
    },
    modelChanged: function() {
      this.render();
    },
    modelDestroyed: function(model) {
      var id = model.get('id'), $el = $('.imageContainer.photo-id-'+id);
      $el.fadeTo('medium', .25);
    }
  });
})(jQuery);

/* /op/data/view/PhotoGalleryDate.js */
(function($){
  op.ns('data.view').PhotoGalleryDate = Backbone.View.extend({
    model: this.model,
    className: 'photo-meta-date',
    template    :_.template($('#photo-meta-date').html()),

    initialize: function() {
      this.model.on('change', this.modelChanged, this);
    },
    render : function(){
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },

    modelChanged: function() {
      this.render();
    }

  });
})(jQuery);


/* /op/data/view/ProfileName.js */
(function($){
  op.ns('data.view').ProfileName = op.data.view.Editable.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
    },
    model: this.model,
    tagName: 'h4',
    className: 'profile-name-meta',
    template    :_.template($('#profile-name-meta').html()),
    editable    : {
      '.name.edit' : {
        name: 'name',
        placement: 'bottom',
        title: 'Edit Gallery Name',
        validate : function(value){
          if($.trim(value) == ''){
            return 'Please enter a name';
          }
          return null;
        }
      }
    },
    modelChanged: function() {
      this.render();
    }
  });
})(jQuery);
/* /op/data/view/ProfilePhoto.js */
(function($){
  op.ns('data.view').ProfilePhoto = Backbone.View.extend({
    initialize: function() {
      if( this.model )
        this.model.on('change', this.modelChanged, this);
    },
    model: this.model,
    className: 'profile-photo-meta',
    template    :_.template($('#profile-photo-meta').html()),
    render      :function(){
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
    
    modelChanged: function() {
      this.render();
    }
  });
})(jQuery);
/* /op/data/view/ProgressBar.js */
(function($){
  op.ns('data.view').ProgressBar = Backbone.View.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
    },
    model: this.model,
    className: 'progress-meta',
    template    :_.template($('#progress-meta').html()),
    render: function() {
      this.$el.html(this.template(this.model.attributes));
      return this;
    },
    modelChanged: function() {
      this.render();
    }
  });
})(jQuery);


/* /op/data/view/TagSearch.js */
(function($){
  op.ns('data.view').TagSearch = Backbone.View.extend({
    initialize: function() {
      var $el = $(this.el), source = [];
      if($el.hasClass('tags') && $el.hasClass('albums')) {
        source = $.merge(_.pluck(OP.Tag.getTags(), 'id'),  _.pluck(OP.Album.getAlbums(), 'name'));
      } else if($el.hasClass('tags')) {
        source = _.pluck(OP.Tag.getTags(), 'id');
      } else if($el.hasClass('albums')) {
        source = _.pluck(OP.Album.getAlbums(), 'name');
      }

      $el
        .typeahead({
          source : source,
          updater : _.bind(this.updater, this),
          matcher: this.matcher,
          highlighter: this.highlighter
        });
    },
    
    // http://stackoverflow.com/questions/12662824/twitter-bootstrap-typeahead-multiple-values
    updater : function(item){
      /**
       * TODO - this could just fire the search right away...
       */
      var $el = $(this.el);
      return $el.val().replace(/[^,]*$/,'')+item+',';
    },
    matcher: function (item) {
      var tquery = TBX.util.tagExtractor(this.query);
      if(!tquery)
        return false;
      return item.toLowerCase().search(tquery) === 0;
    },
    highlighter: function (item) {
      var query = TBX.util.tagExtractor(this.query).replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&'), icon,
        tag = _.where(OP.Tag.getTags(), {'id': item}), album = _.where(OP.Album.getAlbums(), {'name': item});

      if(tag.length > 0) {
        icon = '<i class="icon-tags"></i> ';
        count = tag[0].count;
      } else if(album.length > 0) {
        icon = '<i class="icon-th-large"></i> ';
        count = album[0].count;
      }

      return  ' <span class="badge badge-inverse pull-right">'+count+'</span>' + icon + item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
        return '<strong>' + match + '</strong>'
      });
    }
  });
})(jQuery);

/* /op/data/view/UserBadge.js */
(function($){
  op.ns('data.view').UserBadge = op.data.view.Editable.extend({
    getViewData : function(){
      return _.extend({}, this.model.toJSON(), {
        showStorage : this.options.showStorage === true
      });
    },
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
      this.on('afterrender', this.onAfterRender, this);
      // pull in the html5 data tags
      this.options = $(this.el).data();
      
    },
    model: this.model,
    className: 'user-badge-meta',
    template    :_.template($('#user-badge-meta').html()),
    editable    : {
      '.name.edit' : {
        name: 'name',
        placement: 'bottom',
        title: 'Edit Display Name',
        validate : function(value){
          if($.trim(value) == ''){
            return 'Please enter a name';
          }
          return null;
        }
      }
    },
    onAfterRender : function(){
      if($(this.el).hasClass('userbadge-light')) $(this.el).find('[rel=tooltip]').tooltip();
    },
    modelChanged: function() {
      this.render();
    }
  });
})(jQuery);


/* /op/Lightbox.js */
(function($){
  var _instance, addTagSearch;

  addTagSearch = function() {
    var $el = $('.editable-container .tags-inline-input')
    new op.data.view.TagSearch({el: $el});
  };
  
  var DetailView = op.data.view.Editable.extend({
    initialize: function(){
      this.boundFunctions = {};
    },
    _bound : function(name){
      if( !this.boundFunctions[name] ){
        if( typeof this[name] !== 'function' ) return null;
        this.boundFunctions[name] = _.bind(this[name], this);
      }
      return this.boundFunctions[name];
    },
    template: _.template($('#op-lightbox-details').html()),
    editable: {
      '.title.edit' : {
        name: 'title',
        title: 'Edit Photo Title',
        placement: 'top',
      },
      '.description.edit' : {
        name: 'description',
        type: 'textarea',
        title: 'Edit Photo Description',
        placement: 'top',
        emptytext: 'Click to add a description'
      },
      '.tags.edit' : {
        name: 'tags',
        title: 'Edit Tags',
        placement: 'top',
        emptytext: '',
        inputclass: 'tags-inline-input tags',
        shown: addTagSearch
      }
    },
    setModel: function(model){
      if( this.model ){
        // stop listening
        this.model.off('change', this._bound('render'));
      }
      this.model = model;
      this.model.on('change', this._bound('render'));
      this.render();
    },
    events : {
      'click .permission.edit': 'permission',
      'click .rotate': 'rotate',
      'click .share': 'share'
    },
    permission: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), model = this.model;
      model.set('permission', model.get('permission') == 0 ? 1 : 0, {silent:false});
      model.save();
    },
    rotate: function(ev) {
      ev.preventDefault();
      var $el = $(ev.currentTarget), model = this.model, id = model.get('id'), size = 'Base', value='90';
      OP.Util.makeRequest('/photo/'+id+'/transform.json', {crumb: TBX.crumb(),rotate:value,generate:'true'}, TBX.callbacks.rotate.bind({model: model, id: id, size: size}), 'json', 'post');
    },
    share: function(ev) {
      ev.preventDefault();
      var $el = $(ev.currentTarget), id = $el.attr('data-id'), router = op.data.store.Router;
      OP.Util.makeRequest('/share/photo/'+id+'/view.json', {crumb: TBX.crumb()}, function(response) {
        router.navigate(op.Lightbox.prototype._path, {trigger: true});
        TBX.callbacks.share(response);
      }, 'json', 'get');
    }
  });
  
  var Lightbox = function(){
    
    // we are going to use the global photo store.
    this.boundFunctions = {};
    this.cache = {};
    this.store = op.data.store.Photos;
    this.template = _.template($('#op-lightbox').html());
    this._initialized = false;
  };
  
  _.extend( Lightbox.prototype, Backbone.Events, {
	
    imagePathKey : 'pathBase',
    
    _initialize : function(){
      if( this._initialized ) return;
      this._initView();
      this._initEvents();
      this._initialized = true;
    },
    
    keys : {
      // we may want to rethink some of these key codes...
      // i think down and up may be good to toggle details / thumbs
      next      	  :[34, 39, 40, 74], // up, down, j
      prev      	  :[33, 37, 38, 75], // left, up, k
      title         :[84],  // t
      tags          :[71],  // g
      description   :[68],  // d
      privacy    	  :[80],  // p
      hide      	  :[27],  // escape
      togglePlay	  :[32]  // spacebar 
      //toggleDetails :[68]   // d
    },
    
    _path: location.pathname,
    _pathWithQuery: location.pathname+location.search, // see Handlers.js (click.photoModal) where this gets updated per gh-1434
    _filter: location.pathname.replace('/p/', '/').replace('/photos/', '/').replace('/list', ''),
    _query: function() { return location.search || ''; }, // gh-1421 we make this a function since location.search changes when viewing a photo 
    _visible: false,

    _indexOf : function(model){
      return _.indexOf(this.store.models, model);
    },
    
    _bound : function(name){
      if( !this.boundFunctions[name] ){
        if( typeof this[name] !== 'function' ) return null;
        this.boundFunctions[name] = _.bind(this[name], this);
      }
      return this.boundFunctions[name];
    },
    
    _initView : function(){
      
      this.$el = $( this.template() )
        .appendTo($(document.body))
        .hide()
        .fadeIn('fast')
        
      this.detailView = new DetailView({el: this.$el.find('.details .container')} );
    },
    
    _initEvents : function(){
      this.$el.click( this._bound('onContainerClick') );
      this.$el.find('.photo').click( this._bound('nextIfImage'));
      this.$el.find('.header .container .close-link').click( this._bound('close'));
      this.$el.find('.photo .nav .prev').click( this._bound('prev'));
      this.$el.find('.photo .nav .next').click( this._bound('next'));
      this.$el.find('.details .toggle').click( this._bound('toggleDetails'));
      this.$el.find('.detail-link').click( this._bound('viewDetailPage'));
    },
    
    _captureDocumentEvents : function(){
      $(document).on({
        'keyup.oplightbox'	:this._bound('keyup')
      });
      $(window).on({
        'resize.oplightbox'		:this._bound('adjustSize')
      });
    },
    
    _releaseDocumentEvents : function(){
      $(document).off('.oplightbox');
      $(window).off('.oplightbox');
    },
    
    onContainerClick : function(e){
      if( e.target === this.$el[0] ) this.hide();
      if( $(e.target).parent()[0] == this.$el[0] ) this.hide();
    },
    
    keyup : function(e){
      var code = e.which || e.keyCode
        , target = e.target || e.srcElement
        , self = this
        
      // Ignore key combinations and key events within form elements
      if (!e.ctrlKey && !e.altKey && !e.shiftKey && !e.metaKey && !(target && (target.type || $(target).is('[contenteditable]')))) {
        $.each(self.keys, function(fn, codes){
          if( ~_.indexOf(codes, code) ){
            self[fn]();
            return false;
          }
          return true;
        });
      }
    },
	
    adjustSize : function(){
      var $photo = this.$el.find('.photo');
      
      // check for the image
      if( (c =this.cache[this.model.get('id')]) && c._loaded ){
        /*
         * iw = image width, ih = image height, ir = image ratio
         * cw = chrome width, ch = chrome height, cr = chrome ratio
         */
        var iw = c.width
          , ih = c.height
          , ir = iw / ih
          , cw = $(window).width()
          , ch = $(window).height() - this.$el.find('.bd').position().top
          , cr = cw / ch;
        
        // if the image is narrower and the height is shorter than the chrome
        //  then set the div's width to the images dimensions
        // else
        //  if the chrome ratio (width) is greater than the image
        //    set the width to the image width * chrome height / image height
        //    set the height to the chrome height
        //  else
        //    set the height to the image height * chrome width / image width
        //    set the width to the chrome width
        if( iw < cw && ih < ch ){
          $photo.width(iw);
          $photo.height(ih);
        } else {
          $photo.css( cr > ir ? {width: iw * ch/ih, height: ch} : {width: cw, height: ih * cw/iw} );
        }
      } else {
        $photo.css({'height': ( $(window).height() - this.$el.find('.bd').position().top )+'px'} );
      }
    },

    isOpen: function() {
      if(this.$el)
        return true;
      return false;
    },
    
    show : function(item){
      this._initialize();
      this._visible = true;
      this._captureDocumentEvents();
      this.$el.fadeIn('fast');
      this.adjustSize();
      return this;
    },

    close : function(ev){
      ev.preventDefault();
      return this.hide();
    },
    
    hide : function(){
      var router = op.data.store.Router, $title = $('title');
      this._releaseDocumentEvents();
      this._visible = false;
      if(this.$el)
        this.$el.fadeOut('fast');
      $title.html($title.attr('data-original'));
      router.navigate(this._pathWithQuery, {silent:true});
      return this;
    },
    
    update : function(model){
      this._initialize();
      this.$el.addClass('loading');
      this.setModel( model );
      this.$el.find('.photo').find('img').remove();
      this.loadImage();
      return this;
    },
	
    setModel : function(model){
      this.model = model;
      this.trigger('updatemodel', model);
      this.detailView.setModel( model );
      this.loadImage();
      //this.$el.find('.header .detail-link').attr('href', model.get('url'));
      this._preloadNextPrevious(model);
    },
	
    _imageLoaded : function(id){
      var c = this.cache[id];
      c._loaded = true;
      if( this.model.get('id') != id ) return;
      this.$el.removeClass('loading');
      this.$el.find('.photo img').remove();
      $('<img />').attr('class', 'photo-img-'+id).attr('src', $(c).attr('src')).hide().appendTo(this.$el.find('.photo')).fadeIn('fast');
      this.adjustSize();
    },

    _preloadNextPrevious : function(model) {
      var next, previous, photos = [];
      previous = this.store.at(this.store.indexOf(model) - 1);
      next = this.store.at(this.store.indexOf(model) + 1);

      if(typeof(previous) !== 'undefined') {
        photos.push(previous.get(this.imagePathKey));
      }
      if(typeof(next) !== 'undefined') {
        photos.push(next.get(this.imagePathKey));
      }

      if(photos.length > 0) {
        OP.Util.fire('preload:photos', photos);
      }
    },
    
    loadImage : function(){
      var c, src = this.model.get(this.imagePathKey), $title = $('title'), next, previous, $photo;
      previous = this.store.at(this.store.indexOf(this.model) - 1),
      next = this.store.at(this.store.indexOf(this.model) + 1);

      this.$el.find('.photo img').remove();
      this.$el.addClass('loading');
      $photo = this.$el.find('.photo');
      $photo
        .width($(window).width())
        .height(($(window).height() - this.$el.find('.bd').position().top )+'px');
      // add/remove class="first" to .photo to hide previous arrow
      if(typeof(previous) === 'undefined')
        $photo.addClass('first');
      else
        $photo.removeClass('first');

      // add/remove class="last" to .photo to hide next arrow
      if(typeof(next) === 'undefined')
        $photo.addClass('last');
      else
        $photo.removeClass('last');
        
      // set the title to include the photo's title
      $title.html(TBX.format.sprintf('%s / Photo / %s / Trovebox', TBX.profiles.getOwnerUsername(), this.model.get('title') || this.model.get('filenameOriginal')));

      if( !(c = this.cache[this.model.get('id')]) ){
        var c = this.cache[this.model.get('id')] = new Image();
        c.onload = _.bind(this._imageLoaded, this, this.model.get('id'));
        c.src = this.model.get(this.imagePathKey);
        c._loaded = true;
      }
      else if( c._loaded ){
        this._imageLoaded(this.model.get('id'));
      }
    },
    
    prev : function(ev){
      if(ev !== undefined && ev.preventDefault) ev.preventDefault();
      var i = _.indexOf( this.store.models, this.model ) - 1, router = op.data.store.Router, id;
      if( i < 0 ) i = this.store.models.length-1;
      id = this.store.models[i].get('id');
      if( !$('body').hasClass('photo-details') ){
        router.navigate('/p/'+id+this._filter+this._query(), {trigger: false});
      }
      this.go(i);
    },
    
    next : function(ev){
      if(ev !== undefined && ev.preventDefault) ev.preventDefault();
      var i = _.indexOf( this.store.models, this.model ) + 1, router = op.data.store.Router, id;
      // at the end, load some more synchronously
      if( i > this.store.models.length-1 ) {
        TBX.init.pages.photos.load(false);
      }

      // we check the length again since we append above
      // once we reach the end the appending stops so this works
      if( i < this.store.models.length ) {
        if( !$('body').hasClass('photo-details') ){
          id = this.store.models[i].get('id');
          router.navigate('/p/'+id+this._filter+this._query(), {trigger: false});
        }
        this.go(i);
      }
    },

    // next if the image was clicked (as opposed to the arrows)
    nextIfImage : function(ev) {
      var el = ev.target;
      if(el.tagName === 'IMG') {
        ev.stopPropagation();
        this.next(ev);
      }
    },

    viewDetailPage : function(ev) {
      ev.preventDefault();
      var id = this.model.get('id');
      location.href = '/p/'+id+this._filter+this._query();
    },

    tags: function(ev) {
      var $tagsEl = $('a.tags.editable-click', this.$el);
      $tagsEl.trigger('click');
      //new op.data.view.TagSearch({el: $inputEl});
    },

    description: function(ev) {
      $('a.description.editable-click', this.$el).trigger('click');
    },

    title: function(ev) {
      $('a.title.editable-click', this.$el).trigger('click');
    },

    privacy: function(ev) {
      $('.permission', this.$el).trigger('click');
    },
    
    go : function( index ){
      this.setModel( this.store.models[index] );
    },
    
    togglePlay : function(){
      // TODO - implement slideshow playing state.
      return this;
    },

    toggleDetails : function(){
      this.$el.toggleClass('details-hidden');
    },

    open: function(arg) {
      var id, model;
      if(typeof(arg) === 'event') {
        arg.preventDefault();
        id = $(arg.currentTarget).attr('data-id');
      } else {
        id = arg.replace(/[^a-z0-9].*/,''); // since adding query strings for sort we need to sanitie
      }
      
      // get the item from the store
      model = op.data.store.Photos.get(id);
        
      if( !model ) return $.error('No image in store with id '+id);
      return this.update(model).show();
    }
  });
  
  Lightbox.getInstance = function(){
    if( _instance === undefined ){
      _instance = new Lightbox();
    }
    return _instance;
  }
  
  op.Lightbox = Lightbox;
})(jQuery);

/* /op/Util.js */
(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};

  function Util() {
    var enableBetaFeatures = false, currentPage = null;

    this.currentPage = function(/*[set]*/) {
      if(arguments.length === 1)
        currentPage = arguments[0];
      return currentPage;
    };

    this.enableBetaFeatures = function(/*[set]*/) {
      if(arguments.length === 1)
        enableBetaFeatures = arguments[0];
      return enableBetaFeatures;
    };

    this.fetchAndCache = function(src) {
      $('<img />').attr('src', src).appendTo('body').css('display', 'none').on('load', function(ev) { $(ev.target).remove(); });
    };

    // http://stackoverflow.com/questions/12662824/twitter-bootstrap-typeahead-multiple-values
    // used by typeahead plugin
    this.tagExtractor = function(query) {
      var result = /([^,]+)$/.exec(query);
      if(result && result[1])
          return result[1].trim();
      return '';
    };

    this.getPathParam = function(name) {
      var re = new RegExp(TBX.format.sprintf('/%s-([^/]+)/', name)), result = re.exec(location.pathname);
      if(result !== null && result.length === 2)
        return result[1];
      return null;
    };

    this.getQueryParam = function(name) {
      var re = new RegExp(TBX.format.sprintf('%s=([^&]+)', name)), result = re.exec(location.search);
      if(result !== null && result && result.length === 2)
        return result[1];
      return null;
    };

    this.getQueryParam = function(name) {
      var re = new RegExp(TBX.format.sprintf('%s=([^&]+)', name)), result = re.exec(location.search);
      if(result && result.length === 2)
        return result[1];
      return null;
    };
  }

  TBX.util = new Util;
})(jQuery);

/* /op/Strings.js */
(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};

  function Strings() {
    this.batchConfirm = 'Your batch queue has been updated and now contains %s photo%s.';
  }
  
  TBX.strings = new Strings;
})(jQuery);

/* /op/Handlers.js */
(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};
  
  function Handlers() {
    var state = {};

    // CLICK
    this.click = {};
    this.click.addSpinner = function(ev) {
      var $el = $(ev.target);
      // remove existing icons if any
      $el.find('i').remove();
      $('<i class="icon-spinner icon-spin"></i>').prependTo($el);
    };
    this.click.batchAlbumMode = function(ev) {
      var $el = $(ev.target), $form = $el.closest('form'), $albums = $('select.albums', $form);
      if($el.val() === 'add')
        $albums.attr('name', 'albumsAdd');
      else
        $albums.attr('name', 'albumsRemove');
    };
    this.click.batchHide = function(ev) {
      ev.preventDefault();
      $('.secondary-flyout').slideUp('fast');
    };
    this.click.batchTagMode = function(ev) {
      var $el = $(ev.target), $form = $el.closest('form'), $tags = $('input.tags', $form);
      if($el.val() === 'add')
        $tags.attr('name', 'tagsAdd');
      else
        $tags.attr('name', 'tagsRemove');
    };
    this.click.credentialDelete = function(ev) {
      ev.preventDefault();
      var el = $(ev.target),
          url = el.attr('href')+'.json',
          params = {crumb: TBX.crumb()};

      OP.Util.makeRequest(url, params, TBX.callbacks.credentialDelete.bind(el));
      return false;
    };
    this.click.credentialView = function(ev) {
      ev.preventDefault();
      var el = $(ev.target),
          url = el.attr('href') + '.json',
          params = {crumb: TBX.crumb()};

      OP.Util.makeRequest(url, params, TBX.callbacks.credentialView, 'json', 'get');
      return false;
    };
    this.click.loadMorePhotos = function(ev) {
      ev.preventDefault();
      TBX.init.pages.photos.load();
    };
    this.click.loadMoreAlbums = function(ev) {
      ev.preventDefault();
      TBX.init.pages.albums.load();
    };
    this.click.loginExternal = function(ev) {
      ev.preventDefault();
      var el = $(ev.target);
      if(el.hasClass('persona')) {
        navigator.id.getVerifiedEmail(function(assertion) {
            if (assertion) {
              TBX.callbacks.personaSuccess(assertion);
            } else {
              TBX.notification.show('Sorry, something went wrong trying to log you in.', 'flash', 'error');
            }
        });
      } else if(el.hasClass('facebook')) {
        FB.login(function(response) {
          if (response.authResponse) {
            log('User logged in, posting to openphoto host.');
            OP.Util.makeRequest('/user/facebook/login.json', opTheme.user.base.loginProcessed);
          } else {
            log('User cancelled login or did not fully authorize.');
          }
        }, {scope: 'email'});
      }
      return false;
    };
    this.click.notificationDelete = function(ev) {
      ev.preventDefault();
      OP.Util.makeRequest('/notification/delete.json', {crumb: TBX.crumb()}, null, 'json');
    };
    this.click.photoModal = function(ev) {
      ev.preventDefault();
      var $el = $(ev.target), id = $el.attr('data-id'), $a = $el.closest('a'), $container = $el.closest('.imageContainer'), $pin = $('.pin.edit', $container), url = $a.attr('href'), router = op.data.store.Router, lightbox;
      
      if(ev.altKey && $pin.length > 0) { // alt+click pins the photo
        $pin.trigger('click');
      } else if (ev.shiftKey && $pin.length > 0){ // shift+click pins a range of photos
        // if state.shiftId is undefined then we start the range
        //  else we complete it
        if(typeof(state.shiftId) === 'undefined') {
          state.shiftId = id;
        } else {
          //  if the start and end are the same we omit
          if(id == state.shiftId)
            return;

          var $start = $('.imageContainer.photo-id-'+state.shiftId), $end = $container, range = $.merge([$start, $end], $start.nextUntil($end));
          for(i in range) {
            if(range.hasOwnProperty(i))
              $('.pin.edit', range[i]).trigger('click');
          }
          delete state.shiftId;
        }
      } else {
        lightbox = op.Lightbox.getInstance();
        lightbox._pathWithQuery = location.pathname+location.search;
        router.navigate(url, {trigger:true});
      }
    };
    this.click.pluginStatusToggle = function(ev) {
      ev.preventDefault();
      var el = $(ev.target),
          url = el.attr('href')+'.json',
          params = {crumb: TBX.crumb()};
      OP.Util.makeRequest(url, params, TBX.callbacks.pluginStatusToggle.bind(el), 'json', 'post');
      return false;
    };
    this.click.pluginView = function(ev) {
      ev.preventDefault();
      var el = $(ev.target),
          url = el.attr('href')+'.json',
          params = {crumb: TBX.crumb()},
          urlParts = url.match(/\/plugin\/(.*)\/view.json$/);
      OP.Util.makeRequest('/plugin/'+urlParts[1]+'/view.json', params, TBX.callbacks.pluginView, 'json', 'get');
    };
    this.click.selectAll = function(ev) {
      ev.preventDefault();
      var $els = $('.photo-grid .imageContainer .pin.edit'), batch = OP.Batch, count;
      $els.each(TBX.callbacks.selectAll);
      count = batch.length();
      TBX.notification.show(TBX.format.sprintf(TBX.strings.batchConfirm, count, count > 1 ? 's' : ''), 'flash', 'confirm');
    };
    this.click.setAlbumCover = function(ev) {
      ev.preventDefault();
      var $el = $(ev.target), photoId = $el.attr('data-id'), albumId = TBX.util.getPathParam('album');
      OP.Util.makeRequest(TBX.format.sprintf('/album/%s/cover/%s/update.json', albumId, photoId), {crumb: TBX.crumb()}, TBX.callbacks.setAlbumCover, 'json', 'post');
    };
    this.click.shareAlbum = function(ev) {
      ev.preventDefault();
      var $el = $(ev.target), id = $el.attr('data-id');
      OP.Util.makeRequest('/share/album/'+id+'/view.json', {crumb: TBX.crumb()}, TBX.callbacks.share, 'json', 'get');
    };
    this.click.sharePopup = function(ev) {
      ev.preventDefault();
      var $el = $(ev.target), url = $el.attr('href'), w=575, h=300, l, t, opts;
      l = parseInt(($(window).width()  - w)  / 2);
      t = parseInt(($(window).height() - h) / 2);
      opts = 'location=0,toolbar=0,status=0,width='+w+',height='+h+',left='+l+',top='+t;
      window.open(url, 'TB', opts);
    };
    this.click.showBatchForm = function(ev) {
      ev.preventDefault();
      var $el = $(ev.target), params = {}, model;
      if($el.hasClass('photo')) {
        model = TBX.init.pages.photos.batchModel;
        model.set('loading', true);
        params.action = $el.attr('data-action');
        // we allow overriding the batch queue by passing in an id
        if($el.attr('data-ids'))
          params.ids = $el.attr('data-ids');

        OP.Util.makeRequest('/photos/update.json', params, function(response) {
          var result = response.result;
          model.set('loading', false);
          $('.secondary-flyout').html(result.markup).slideDown('fast');
          if(typeof(params.action) !== "undefined" && params.action === "tags") {
            new op.data.view.TagSearch({el: $('form.batch input.typeahead')});
          }
        }, 'json', 'get');
      } else if($el.hasClass('album')) {
        model = TBX.init.pages.albums.batchModel;
        model.set('loading', true);
        OP.Util.makeRequest('/album/form.json', params, function(response) {
          var result = response.result;
          model.set('loading', false);
          $('.secondary-flyout').html(result.markup).slideDown('fast');
        }, 'json', 'get');
      }
      return;
    };
    this.click.toggle = function(ev) {
      // use data-type to support other toggles
      ev.preventDefault();
      var $el = $(ev.target), $tgt = $($el.attr('data-target'));
      $tgt.slideToggle();
    };
    this.click.tokenDelete = function(ev) {
      ev.preventDefault();
      var el = $(ev.target),
          url = el.attr('href')+'.json',
          params = {crumb: TBX.crumb()};

      OP.Util.makeRequest(url, params, TBX.callbacks.tokenDelete.bind(el));
      return false;
    };
    this.click.triggerDownload = function(ev) {
      ev.preventDefault();
      var $el = $('.download.trigger'), url = $el.attr('href');
      location.href = url;
    },
    this.click.triggerShare = function(ev) { 
      ev.preventDefault();
      var $el = $('.share.trigger');
      $el.trigger('click');
    };
    this.click.tutorial = function(ev) {
      ev.preventDefault();
      TBX.tutorial.run();
    };
    this.click.uploadBeta = function(ev) {
      ev.preventDefault();
      TBX.upload.start();
    };

    this.keydown = { };
    this.keyup = { };
    this.keyup["27"] = "keyup:escape";
    this.keyup["191"] = "keyup:slash";
    this.keyup["37"] = "keyup:left";
    this.keyup["39"] = "keyup:right";
    this.mouseover = { };
    this.submit = {};
    this.submit.albumCreate = function(ev) {
      ev.preventDefault();
      var $form = $(ev.target), params = {name: $('input[name="name"]', $form).val(), crumb: TBX.crumb()};
      $('button', $form).prepend('<i class="icon-spinner icon-spin"></i> ');
      OP.Util.makeRequest('/album/create.json', params, TBX.callbacks.albumCreate, 'json', 'post');
    };
    this.submit.batch = function(ev) {
      ev.preventDefault();
      var $form = $(ev.target), url = '/photos/update.json', formParams = $form.serializeArray(), batch = OP.Batch, idsArr = batch.ids(), params = {ids: idsArr.join(','), crumb: TBX.crumb()};
      $('button', $form).prepend('<i class="icon-spinner icon-spin"></i> ');
      for(i in formParams) {
        if(formParams.hasOwnProperty(i)) {
          // we allow the batch ids to be overridden by a parameter named ids
          if(formParams[i].name === 'ids')
            params.ids = formParams[i].value;

          if(formParams[i].name === 'albumsAdd') {
            url = '/album/'+formParams[i].value+'/photo/add.json';
          } else if(formParams[i].name === 'albumsRemove') {
            url = '/album/'+formParams[i].value+'/photo/remove.json';
          } else if(formParams[i].name === 'delete') {
            if($('input[name="confirm"]', $form).attr('checked') === 'checked' && $('input[name="confirm2"]', $form).attr('checked') === 'checked') {
              url = '/photos/delete.json';
            } else {
              TBX.notification.show("Check the appropriate checkboxes so we know you're serious.", 'flash', 'error');
              OP.Util.fire('callback:remove-spinners');
              return; // don't continue
            }
          } else if(formParams[i].name === 'dateAdjust') {
            var dateAdjustedValue = formParams[i].value;
            for(var innerI=0; innerI<idsArr.length; innerI++) {
              op.data.store.Photos.get(idsArr[innerI])
              .set({dateTaken: dateAdjustedValue}, {silent: true})
              .save();
            }
            OP.Util.fire('callback:remove-spinners');
            // TODO gh-321 figure out how to handle errors
            return; // don't continue
          }

          params[formParams[i].name] = formParams[i].value;
        }
      }
      OP.Util.makeRequest(url, params, TBX.callbacks.batch.bind(params), 'json', 'post');
    };
    this.submit.login = function(ev) {
      ev.preventDefault();
      var form = $(ev.target),
          params = form.serialize();
      params += '&httpCodes=403';
      $.ajax(
        {
          url: '/user/self/login.json',
          dataType:'json',
          data:params,
          type:'POST',
          success: TBX.callbacks.loginSuccess,
          error: TBX.notification.display.generic.error,
          context: form
        }
      );
    };
    this.submit.pluginUpdate = function(ev) {
      ev.preventDefault();
      var form = $(ev.target),
          url = form.attr('action')+'.json';
      OP.Util.makeRequest(url, form.serializeArray(), TBX.callbacks.pluginUpdate, 'json', 'post');
      return false;
    };
    this.submit.shareEmail = function(ev) {
      ev.preventDefault();
      var $form = $(ev.target),
          params = $form.serialize()
          data = $('input[name="data"]', $form).val(),
          type = $('input[name="type"]', $form).val(),
          url = $('input[name="url"]', $form).val(),
          recipients = $('input[name="recipients"]', $form).val(),
          message = $('textarea[name="message"]', $form).val();

      if(recipients.length == 0) {
        TBX.notification.show('Please specify at least one recipient.', 'flash', 'error');
        return;
      } else if(message.length === 0) {
        TBX.notification.show('Please type in a message.', 'flash', 'error');
        return;
      }

      $('button', $form).prepend('<i class="icon-spinner icon-spin"></i> ');
      $.ajax(
        {
          url: '/share/'+type+'/'+data+'/send.json',
          dataType:'json',
          data:params,
          type:'POST',
          success: TBX.callbacks.shareEmailSuccess,
          error: function() { $('button i', $form).remove(); TBX.notification.display.generic.error(); },
          context: $form
        }
      );
      return false;
    };
    this.submit.search = function(ev) {
      ev.preventDefault();
      var $form = $(ev.target), $button = $('button', $form), query = $('input[type="search"]', $form).val(), albums = OP.Album.getAlbums(), 
          albumSearch = _.where(albums, {name: query.replace(/,+$/, '')}), isAlbum  = albumSearch.length > 0, url;
      if(query === '')
        return;

      $button.html('<i class="icon-spinner icon-spin"></i>');
      // trim leading and trailing commas and spaces in between
      if(isAlbum) {
        url = TBX.format.sprintf('/photos/album-%s/list', albumSearch[0].id);
      } else {
        query = query.replace(/^,\W*|,\W*$/g, '').replace(/\W*,\W*/, ',');
        url = TBX.format.sprintf('/photos/tags-%s/list', query);
      }
      location.href = url;
    };
    this.submit.upload = function(ev) {
      ev.preventDefault();
      var uploader = $("#uploader").pluploadQueue();
      if (typeof(uploader.files) != 'undefined' && uploader.files.length > 0) {
        uploader.start();
      } else {
        TBX.notification.show('Nothing to upload.', 'flash', 'error');
        OP.Util.fire('callback:remove-spinners');
      }
    };

    // custom
    this.custom = {};
    this.custom.preloadPhotos = function(photos) {
      if(photos.length == 0)
        return;

      for(i in photos) {
        if(photos.hasOwnProperty(i)) { 
          TBX.util.fetchAndCache(photos[i]);
        }
      }
    };
    this.custom.tutorialUpdate = function() {
      var params = {section: this.section, key: this.key, crumb: TBX.crumb()};
      OP.Util.makeRequest('/plugin/Tutorial/update.json', params, TBX.callbacks.tutorialUpdate, 'json', 'post');
    };
    this.custom.uploaderBetaReady = function() {
      TBX.upload.init();
    };
  }
  
  TBX.handlers = new Handlers;
})(jQuery);

/* /op/Highlight.js */
(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};

  function Hightlight() {
    var highlightColor = '#fff7dc', startColor;
    reset = function() {
      $(this).animate({'backgroundColor':startColor}, 'slow');
    }

    this.run = function(el) {
      var $el = $(el);

      startColor = $el.css('backgroundColor');
      $el.animate({backgroundColor: highlightColor}, 'slow', 'swing', reset.bind($el));
    }
  }
  
  TBX.highlight = new Hightlight;
})(jQuery);


/* /op/Callbacks.js */
(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};
  
  function Callbacks() {
    this.albumCreate = function(response) {
      var result = response.result;
      if(response.code === 201 || response.code === 409) {
        // if we're on the upload form then we insert into the selection list
        var $sel = $('form.upload select[name="albums"]');
        if($sel.length === 1) {
          $sel.append($('<option>', {value: result.id, text: result.name, selected: 'selected'}));
          if(response.code === 201) {
            TBX.notification.show('Your album was created and we\'ve gone ahead and selected it for you.', 'flash', 'confirm');
          } else {
            TBX.notification.show('We found an album with that name so we\'ve gone ahead and selected it for you.', 'flash', 'confirm');
          }
        } else {
          if(response.code === 201) {
            TBX.notification.show('Your album was created. You can <a href="/photos/upload?album='+result.id+'"><i class="icon-upload"></i> upload photos</a> or <a href="#" class="albumInviteUploaders" data-id="'+result.id+'"><i class="icon-exchange"></i> invite others</a> to upload their photos.', 'flash', 'confirm');
          } else {
            TBX.notification.show('We found an album with that name so we didn\'t create another.  You can <a href="/photos/upload?album='+result.id+'"><i class="icon-upload"></i> upload photos</a> or <a href="#" class="albumInviteUploaders" data-id="'+result.id+'"><i class="icon-exchange"></i> invite others</a> to upload their photos.', 'flash', 'confirm');
          }
        }
      } else {
        TBX.notification.show('Sorry, an error occured when trying to create your album.', 'flash', 'error');
      }
      $('.batchHide').trigger('click');
    };
    this.batch = function(response) { // this is the form params
      var id, model, ids = this.ids.split(','), photoCount = ids.length, store = op.data.store.Photos, action = response.code === 204 ? 'deleted' : 'updated';
      if(response.code === 200 || response.code === 204) {
        for(i in ids) {
          if(ids.hasOwnProperty(i)) {
            id = ids[i];
            model = store.get(id);
            if(model) {
              // on update we fetch, on delete we destroy
              if(response.code === 200) {
                model.fetch();
              } else {
                OP.Util.fire('callback:photo-destroy', model);
              }
            }
          }
        }

        // deleted, let's clear the batch queue
        if(response.code === 204) {
          var delIds = _.intersection(ids, OP.Batch.ids());
          for(i in delIds) {
            OP.Batch.remove(OP.Batch.getKey(delIds[i]));
          }
        }

        TBX.notification.show(photoCount + ' photo ' + (photoCount>1?'s were':'was') + ' ' + action + '.', 'flash', 'confirm');
      } else {
        TBX.notification.show('Sorry, an error occured when trying to update your photos.', 'flash', 'error');
      }
      $('.batchHide').trigger('click');
    };
    this.credentialDelete = function(response) {
      if(response.code === 204) {
        this.closest('tr').slideUp('medium');
        TBX.notification.show('Your app was successfully deleted.', null, 'confirm');
      } else {
        TBX.notification.show('There was a problem deleting your app.', null, 'error');
      }
    };
    this.credentialView = function(response) {
      if(response.code === 200) {
        $('.secondary-flyout').html(response.result.markup).slideDown();
      } else {
        TBX.notification.show('There was a problem retrieving your app.', null, 'error');
      }
    };
    this.loginSuccess = function() {
      var redirect = $('input[name="r"]', this).val();
      window.location.href = redirect;
    };
    this.personaSuccess = function(assertion) {
      var params = {assertion: assertion};
      OP.Util.makeRequest('/user/browserid/login.json', params, TBX.callbacks.loginProcessed);
    };
    this.loginProcessed = function(response) {
      if(response.code != 200) {
        TBX.notification.show('Sorry, we could not log you in.', 'flash', 'error');
        return;
      }
      
      var url = $('input[name="r"]', $('form.login')).val();
      location.href = url;
    };
    this.passwordReset = function(response) {
      var $button = this;
      if(response.code !== 200) {
        TBX.notification.show('We could not update your password.', 'flash', 'error');
        OP.Util.fire('callback:replace-spinner', {button: $button, icon:'icon-warning-sign'});
        return;
      }
      location.href = '/';
    };
    this.personaSuccess = function(assertion) {
      var params = {assertion: assertion};
      OP.Util.makeRequest('/user/browserid/login.json', params, TBX.callbacks.loginProcessed);
    };
    this.photoNext = function(ev) {
      if(!op.Lightbox.getInstance().isOpen())
        $('.pagination .arrow-next').click();
    };
    this.photoPrevious = function(ev) {
      if(!op.Lightbox.getInstance().isOpen())
        $('.pagination .arrow-prev').click();
    };
    this.pluginStatusToggle = function(response) {
      var a = $(this),
          div = a.parent(),
          container = div.parent();
      if(response.code === 200) {
        $('div', container).removeClass('hide');
        div.addClass('hide');
      } else {
        TBX.notification.show('Could not update the status of this plugin.', 'flash', 'error');
      }
    };
    this.pluginUpdate = function(response) {
      if(response.code === 200) {
        TBX.notification.show('Your plugin was successfully updated.', 'flash', 'confirm');
        $('.batchHide').trigger('click');
      } else {
        TBX.notification.show('Could not update the status of this plugin.', 'flash', 'error');
      }
      $("#modal").modal('hide');
    };
    this.pluginView = function(response) {
      if(response.code === 200) {
        $(".secondary-flyout").html(response.result.markup).fadeIn();
      } else {
        opTheme.message.error('Unable to load this plugin for editing.');
      }
    };
    this.profilesSuccess = function(owner, viewer, profiles) {
      var ownerId = owner.id, viewerId = viewer.id;
      profiles.owner = owner;
      if(viewer !== undefined)
        profiles.viewer = viewer;

      // create model(s)
      op.data.store.Profiles.add(profiles.owner);
      // only if the viewer !== owner do we create two models
      if(viewer !== undefined && owner.isOwner === false)
        op.data.store.Profiles.add(profiles.viewer);
        
      $('.user-badge-meta').each(function(i, el) {
        (new op.data.view.UserBadge({model:op.data.store.Profiles.get(ownerId), el: el})).render();
      });
      $('.profile-name-meta.owner').each(function(i, el) {
        (new op.data.view.ProfileName({model:op.data.store.Profiles.get(ownerId), el: el})).render();
      });
      $('.profile-photo-meta').each(function(i, el) {
        (new op.data.view.ProfilePhoto({model:op.data.store.Profiles.get(ownerId), el: el})).render();
      });
      (new op.data.view.ProfilePhoto({model:op.data.store.Profiles.get(viewerId), el: $('.profile-photo-header-meta')})).render();
    };
    this.removeSpinners = function() {
      var $icons = $('button i.icon-spinner');
      $icons.each(function(i, el) { $(el).remove(); });
    };
    this.replaceSpinner = function(args) {
      var $icon = $('i.icon-spinner', args.button), cls = 'icon-ok';
      if(typeof(args['icon']) !== 'undefined')
        cls = args.icon;

      $icon.removeClass('icon-spinner icon-spin');
      $icon.addClass(cls);
    };
    this.rotate = function(response) {
      var model = this.model, id = this.id, size = this.size, code = response.code, src = response.result['path'+size], $img = $('img.photo-img-'+id);
      model.fetch();
      if(response.code === 200) {
        $img.fadeOut('fast', function() { $img.attr('src', src).fadeIn('fast'); });
      }
    };
    this.selectAll = function(i, el) {
      var id = $(el).attr('data-id'), photo = op.data.store.Photos.get(id).toJSON();
      OP.Batch.add(id, photo);
    };
    this.setAlbumCover = function(response) {
      if(response.code === 200) {
        TBX.notification.show('Your album cover was updated successfully.', 'flash', 'confirm');
        return;
      }
      TBX.notification.show('There was a problem updating your album cover.', 'flash', 'error');
    };
    this.share = function(response) {
      var result = response.result;
      if(response.code !== 200) {
        TBX.notification.show('There was a problem generating your sharing token.', 'flash', 'error');
        return;
      }

      $('.secondary-flyout').html(result.markup).slideDown('fast');
    };
    this.shareEmailSuccess = function(response) {
      var result = response.result;
      $('a.batchHide').trigger('click');
      TBX.notification.show('Your photo was successfully emailed.', 'flash', 'confirm');
    };
    this.showKeyboardShortcuts = function(ev) {
      if(!ev.shiftKey)
        return;
      var markup = $('script#keyboard-shortcuts').html();
      $('.secondary-flyout').html(markup).slideDown('fast');
    };
    this.tokenDelete = function(response) {
      if(response.code === 204) {
        this.closest('tr').slideUp('medium');
        TBX.notification.show('Your sharing token was successfully deleted.', null, 'confirm');
      } else {
        TBX.notification.show('There was a problem deleting your sharing token.', null, 'error');
      }
    };
    this.tutorialUpdate = function(response) {
      $('.navbar-inner-secondary ul li.info').fadeOut();
    };
    this.upload = function(ev) {
      ev.preventDefault();
      var uploader = $("#uploader").pluploadQueue();
      if (typeof(uploader.files) != 'undefined' && uploader.files.length > 0) {
        uploader.start();
      } else {
        // TODO something that doesn't suck
        //opTheme.message.error('Please select at least one photo to upload.');
      }
    };
    this.uploadCompleteSuccess = function(photoResponse) {
      photoResponse.crumb = TBX.crumb();
      $("form.upload").fadeOut('fast', function() {
        OP.Util.makeRequest('/photos/upload/confirm.json', photoResponse, TBX.callbacks.uploadConfirm, 'json', 'post');
      });
    };
    this.uploadConfirm = function(response) {
      var $el, container, model, view, item, result = response.result, success = result.data.successPhotos;
      $(".upload-container").fadeOut('fast', function() { $(".upload-confirm").fadeIn('fast'); });
      $(".upload-confirm").html(result.tpl).show('fast', function(){
        if(success.length > 0) {
          var batchModel = TBX.init.pages.photos.batchModel, $batchEl = $('.batch-meta');
          $('body').addClass('upload-confirm');
          (new op.data.view.BatchIndicator({model:batchModel, el: $batchEl})).render();
          op.data.store.Photos.add(success);
          container = $('.upload-preview.success');
          Gallery.showImages(container, success);
        }
      });
    };
    this.uploadSendNotification = function(args) {
      OP.Util.makeRequest(TBX.format.sprintf('/photos/upload/%s/notify.json', args.token), {uploader:args.by, count:(args.photosUploaded.success.length+args.photosUploaded.duplicate.length)}, function(){}, 'json', 'post');
    };
    this.uploaderReady = function() {
      var form = $('form.upload');
      if(typeof OPU === 'object')
        OPU.init();
    };
  }
  
  TBX.callbacks = new Callbacks;
})(jQuery);

/* /op/Tutorial.js */
(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};
  
  var q = [], fired = false, introObj, enabled, enabledState;
  function Tutorial() {
    this.queue = function(step, selector, intro, key, section, width, init) {
      if(!enabled())
        return;

      // step = numeric value always starts at 1
      // selector = css selector for the element
      // intro = text to display to user
      // key = key from the ini file
      // section = section from the ini file
      q.push({step:step, selector: selector, intro:intro, key: key, section: section, width: width});
      if(init && fired === false) {
        OP.Util.fire('tutorial:run');
        fired = true;
      }
    };

    this.run = function() {
      if(!enabled())
        return;

      var qObj, el;
      introObj = introJs();
      for(i=0; i<q.length; i++) {
        qObj = q[i];
        el = $(qObj.selector);
        el.attr('data-intro', qObj.intro).attr('data-step', qObj.step);
        if(typeof(qObj.width) !== "undefined")
          el.attr('data-width', qObj.width);
      }
      introObj.complete(TBX.handlers.custom.tutorialUpdate.bind(qObj));
      introObj.start();
    };
    
    // we only enable this is the info element in the navigation is visible
    //  we hide it using hidden-phone
    enabled = function() {
      if(typeof(enabledState) === "undefined") {
        enabledState = $('.navbar-inner-secondary li.info').is(':visible');
      }
      return enabledState;
    }
  }
  TBX.tutorial = new Tutorial;
})(jQuery);
OP.Util.on('tutorial:run', TBX.tutorial.run);


/* /op/Upload.js */
(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};

  function Upload() {
    var dropzone, total = 0, ids = [], duplicateCache = {}, completeObj = {success: 0, duplicate: 0, failed: 0, completed: 0}, progressModel, $form = $('form.upload');

    var fileFinishedHandler = function(file, response) {
      var message, $previewElement = $(file.previewElement);
      switch(response.code) {
        case 201:
          completeObj.success++;
          $(".dz-details>img", $previewElement).attr('src', response.result.path100x100xCR).css('display', 'block');
          break;
        case 409:
          $(".dz-details>img", $previewElement).attr('src', response.result.path100x100xCR).css('display', 'block');
          completeObj.duplicate++;
          break;
        default:
          completeObj.failed++;
          break;
      }

      completeObj.completed++;
      ids.push(response.result.id);

      if(completeObj.completed === total) {
        progressModel.completed();

        if(completeObj.failed === 0) {
          if(completeObj.duplicate === 0)
            message = 'Your photos have been successfully uploaded. %s';
          else
            message = TBX.format.sprintf('Your photos have been successfully uploaded (%s %s).', completeObj.duplicate, TBX.format.plural('duplicate', completeObj.duplicate)) + ' %s';

          TBX.notification.show(TBX.format.sprintf(message, TBX.format.sprintf('<a href="/photos/ids-%s/list">View or edit your photos</a>.', ids.join(','))));
        } else {
          TBX.notification.show('There was a problem uploading your photos. Please try again.', 'static', 'error');
        }

        $('button i', $form).remove();
      }

      progressModel.set('success', percent(completeObj.success, total));
      progressModel.set('warning', percent(completeObj.duplicate, total));
      progressModel.set('danger', percent(completeObj.failed, total));
    };
    var fileSending = function() {
      if(typeof(progressModel) === "undefined") {
        // insert the progress container
        $el = $('.progress-upload');
        // create the progress model
        progressModel = new op.data.model.ProgressBar();
        // insert the view and render it in place
        (new op.data.view.ProgressBar({model: progressModel, el: $el})).render();
        $('button', $form).prepend('<i class="icon-spinner icon-spin"></i> ');
      }
    };
    var percent = function(numerator, denominator) {
      if(denominator === 0)
        return 0;
      return (numerator / denominator) * 100;
    };

    this.init = function() {
      dropzone = new Dropzone('form.dropzone', {
        url: '/photo/upload.json',
        parallelUploads: 5,
        paramName: 'photo',
        maxThumbnailFilesize: 1,
        //clickable: true,
        acceptedMimeTypes: 'image/jpg,image/jpeg,image/png,image/gif,image/tiff',
        /*accept: function (file, done) {
          if('image/jpg,image/jpeg,image/png,image/gif,image/tiff'.search(file.type) !== -1) {
            done(); 
          } else { 
            done("Invalid file type."); 
          } 
        },*/
        previewsContainer: 'form.dropzone .preview-container',
        enqueueForUpload: false
      });
      
      dropzone.on("addedfile", function(file) {
        // check if the file is already queued
        if(typeof(duplicateCache[file.name]) !== "undefined") {
          dropzone.removeFile(file);
          return;
        }
      
        total++;
        duplicateCache[file.name] = 1;
        dropzone.filesQueue.push(file);
        TBX.notification.show(TBX.format.sprintf('Your upload queue has %s %s pending', dropzone.filesQueue.length, TBX.format.plural('photo', dropzone.filesQueue.length)));
      });
      dropzone.on("success", function(file, response) { fileFinishedHandler(file, response); });
      dropzone.on("error", function(file) { fileFinishedHandler(file, {"code":500}); });
      dropzone.on("sending", fileSending);
    };
    this.start = function() {
      dropzone.processQueue();
    }
  }
 
  TBX.upload = new Upload;
})(jQuery);

/* /op/Format.js */
(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};
  
  function Format() {
    var _date = function(format, timestamp) {
      var that = this,
          jsdate, f, formatChr = /\\?([a-z])/gi,
          formatChrCb,
          // Keep this here (works, but for code commented-out
          // below for file size reasons)
          //, tal= [],
          _pad = function (n, c) {
              if ((n = n + '').length < c) {
                  return new Array((++c) - n.length).join('0') + n;
              }
              return n;
          },
          txt_words = ["Sun", "Mon", "Tues", "Wednes", "Thurs", "Fri", "Satur", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
      formatChrCb = function (t, s) {
          return f[t] ? f[t]() : s;
      };
      f = {
          // Day
          d: function () { // Day of month w/leading 0; 01..31
              return _pad(f.j(), 2);
          },
          D: function () { // Shorthand day name; Mon...Sun
              return f.l().slice(0, 3);
          },
          j: function () { // Day of month; 1..31
              return jsdate.getDate();
          },
          l: function () { // Full day name; Monday...Sunday
              return txt_words[f.w()] + 'day';
          },
          N: function () { // ISO-8601 day of week; 1[Mon]..7[Sun]
              return f.w() || 7;
          },
          S: function () { // Ordinal suffix for day of month; st, nd, rd, th
              var j = f.j();
              return j < 4 | j > 20 && ['st', 'nd', 'rd'][j%10 - 1] || 'th'; 
          },
          w: function () { // Day of week; 0[Sun]..6[Sat]
              return jsdate.getDay();
          },
          z: function () { // Day of year; 0..365
              var a = new Date(f.Y(), f.n() - 1, f.j()),
                  b = new Date(f.Y(), 0, 1);
              return Math.round((a - b) / 864e5) + 1;
          },

          // Week
          W: function () { // ISO-8601 week number
              var a = new Date(f.Y(), f.n() - 1, f.j() - f.N() + 3),
                  b = new Date(a.getFullYear(), 0, 4);
              return _pad(1 + Math.round((a - b) / 864e5 / 7), 2);
          },

          // Month
          F: function () { // Full month name; January...December
              return txt_words[6 + f.n()];
          },
          m: function () { // Month w/leading 0; 01...12
              return _pad(f.n(), 2);
          },
          M: function () { // Shorthand month name; Jan...Dec
              return f.F().slice(0, 3);
          },
          n: function () { // Month; 1...12
              return jsdate.getMonth() + 1;
          },
          t: function () { // Days in month; 28...31
              return (new Date(f.Y(), f.n(), 0)).getDate();
          },

          // Year
          L: function () { // Is leap year?; 0 or 1
              var j = f.Y();
              return j%4==0 & j%100!=0 | j%400==0;
          },
          o: function () { // ISO-8601 year
              var n = f.n(),
                  W = f.W(),
                  Y = f.Y();
              return Y + (n === 12 && W < 9 ? -1 : n === 1 && W > 9);
          },
          Y: function () { // Full year; e.g. 1980...2010
              return jsdate.getFullYear();
          },
          y: function () { // Last two digits of year; 00...99
              return (f.Y() + "").slice(-2);
          },

          // Time
          a: function () { // am or pm
              return jsdate.getHours() > 11 ? "pm" : "am";
          },
          A: function () { // AM or PM
              return f.a().toUpperCase();
          },
          B: function () { // Swatch Internet time; 000..999
              var H = jsdate.getUTCHours() * 36e2,
                  // Hours
                  i = jsdate.getUTCMinutes() * 60,
                  // Minutes
                  s = jsdate.getUTCSeconds(); // Seconds
              return _pad(Math.floor((H + i + s + 36e2) / 86.4) % 1e3, 3);
          },
          g: function () { // 12-Hours; 1..12
              return f.G() % 12 || 12;
          },
          G: function () { // 24-Hours; 0..23
              return jsdate.getHours();
          },
          h: function () { // 12-Hours w/leading 0; 01..12
              return _pad(f.g(), 2);
          },
          H: function () { // 24-Hours w/leading 0; 00..23
              return _pad(f.G(), 2);
          },
          i: function () { // Minutes w/leading 0; 00..59
              return _pad(jsdate.getMinutes(), 2);
          },
          s: function () { // Seconds w/leading 0; 00..59
              return _pad(jsdate.getSeconds(), 2);
          },
          u: function () { // Microseconds; 000000-999000
              return _pad(jsdate.getMilliseconds() * 1000, 6);
          },

          // Timezone
          e: function () { // Timezone identifier; e.g. Atlantic/Azores, ...
              // The following works, but requires inclusion of the very large
              // timezone_abbreviations_list() function.
  /*              return this.date_default_timezone_get();
  */
              throw 'Not supported (see source code of date() for timezone on how to add support)';
          },
          I: function () { // DST observed?; 0 or 1
              // Compares Jan 1 minus Jan 1 UTC to Jul 1 minus Jul 1 UTC.
              // If they are not equal, then DST is observed.
              var a = new Date(f.Y(), 0),
                  // Jan 1
                  c = Date.UTC(f.Y(), 0),
                  // Jan 1 UTC
                  b = new Date(f.Y(), 6),
                  // Jul 1
                  d = Date.UTC(f.Y(), 6); // Jul 1 UTC
              return 0 + ((a - c) !== (b - d));
          },
          O: function () { // Difference to GMT in hour format; e.g. +0200
              var tzo = jsdate.getTimezoneOffset(),
                  a = Math.abs(tzo);
              return (tzo > 0 ? "-" : "+") + _pad(Math.floor(a / 60) * 100 + a % 60, 4);
          },
          P: function () { // Difference to GMT w/colon; e.g. +02:00
              var O = f.O();
              return (O.substr(0, 3) + ":" + O.substr(3, 2));
          },
          T: function () { // Timezone abbreviation; e.g. EST, MDT, ...
              // The following works, but requires inclusion of the very
              // large timezone_abbreviations_list() function.
              /*
              var abbr = '', i = 0, os = 0, default = 0;
              if (!tal.length) {
                  tal = that.timezone_abbreviations_list();
              }
              if (that.php_js && that.php_js.default_timezone) {
                  default = that.php_js.default_timezone;
                  for (abbr in tal) {
                      for (i=0; i < tal[abbr].length; i++) {
                          if (tal[abbr][i].timezone_id === default) {
                              return abbr.toUpperCase();
                          }
                      }
                  }
              }
              for (abbr in tal) {
                  for (i = 0; i < tal[abbr].length; i++) {
                      os = -jsdate.getTimezoneOffset() * 60;
                      if (tal[abbr][i].offset === os) {
                          return abbr.toUpperCase();
                      }
                  }
              }
              */
              return 'UTC';
          },
          Z: function () { // Timezone offset in seconds (-43200...50400)
              return -jsdate.getTimezoneOffset() * 60;
          },

          // Full Date/Time
          c: function () { // ISO-8601 date.
              return 'Y-m-d\\Th:i:sP'.replace(formatChr, formatChrCb);
          },
          r: function () { // RFC 2822
              return 'D, d M Y H:i:s O'.replace(formatChr, formatChrCb);
          },
          U: function () { // Seconds since UNIX epoch
              return jsdate / 1000 | 0;
          }
      };
    }

    this.date = function (format, timestamp) {
        that = this;
        jsdate = (timestamp == null ? new Date() : // Not provided
        (timestamp instanceof Date) ? new Date(timestamp) : // JS Date()
        new Date(timestamp * 1000) // UNIX timestamp (auto-convert to int)
        );
        return format.replace(formatChr, formatChrCb);
    };
    
    this.number_format = function(number, decimals, dec_point, thousands_sep) {
      number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
      var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
          var k = Math.pow(10, prec);
          return '' + Math.round(n * k) / k;
        };
      // Fix for IE parseFloat(0.55).toFixed(0) = 0;
      s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }
      if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
      }
      return s.join(dec);
    };

    this.bytes_to = function(size/*, tgtUnit*/) {
      var kb = 1024;

      // less than 1mb
      if(size < 1048576)
        return {size: Math.ceil(size / kb), unit: 'KB'}

      // less than 1gb
      if(size < 1073741824)
        return {size: Math.ceil(size / Math.pow(kb,2)), unit: 'MB'}

      return {size: Math.ceil(size / Math.pow(kb,3)), unit: 'GB'}
    };
    this.plural = function(string, count) {
      if(count < 2)
        return string;

      var lastLetter = string.charAt(string.length-1);
      if(lastLetter === 'y' || lastLetter === 'Y')
        return string.substr(0, string.length-1) + 'ies';
      else
        return string.substr(0, string.length) + 's';
    };

    this.sprintf = function() {
      // http://kevin.vanzonneveld.net
      // +   original by: Ash Searle (http://hexmen.com/blog/)
      // + namespaced by: Michael White (http://getsprink.com)
      // +    tweaked by: Jack
      // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // +      input by: Paulo Freitas
      // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // +      input by: Brett Zamir (http://brett-zamir.me)
      // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // +   improved by: Dj
      // +   improved by: Allidylls
      // *     example 1: sprintf("%01.2f", 123.1);
      // *     returns 1: 123.10
      // *     example 2: sprintf("[%10s]", 'monkey');
      // *     returns 2: '[    monkey]'
      // *     example 3: sprintf("[%'#10s]", 'monkey');
      // *     returns 3: '[####monkey]'
      // *     example 4: sprintf("%d", 123456789012345);
      // *     returns 4: '123456789012345'
      var regex = /%%|%(\d+\$)?([-+\'#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuideEfFgG])/g;
      var a = arguments,
        i = 0,
        format = a[i++];

      // pad()
      var pad = function (str, len, chr, leftJustify) {
        if (!chr) {
          chr = ' ';
        }
        var padding = (str.length >= len) ? '' : Array(1 + len - str.length >>> 0).join(chr);
        return leftJustify ? str + padding : padding + str;
      };

      // justify()
      var justify = function (value, prefix, leftJustify, minWidth, zeroPad, customPadChar) {
        var diff = minWidth - value.length;
        if (diff > 0) {
          if (leftJustify || !zeroPad) {
            value = pad(value, minWidth, customPadChar, leftJustify);
          } else {
            value = value.slice(0, prefix.length) + pad('', diff, '0', true) + value.slice(prefix.length);
          }
        }
        return value;
      };

      // formatBaseX()
      var formatBaseX = function (value, base, prefix, leftJustify, minWidth, precision, zeroPad) {
        // Note: casts negative numbers to positive ones
        var number = value >>> 0;
        prefix = prefix && number && {
          '2': '0b',
          '8': '0',
          '16': '0x'
        }[base] || '';
        value = prefix + pad(number.toString(base), precision || 0, '0', false);
        return justify(value, prefix, leftJustify, minWidth, zeroPad);
      };

      // formatString()
      var formatString = function (value, leftJustify, minWidth, precision, zeroPad, customPadChar) {
        if (precision != null) {
          value = value.slice(0, precision);
        }
        return justify(value, '', leftJustify, minWidth, zeroPad, customPadChar);
      };

      // doFormat()
      var doFormat = function (substring, valueIndex, flags, minWidth, _, precision, type) {
        var number;
        var prefix;
        var method;
        var textTransform;
        var value;

        if (substring == '%%') {
          return '%';
        }

        // parse flags
        var leftJustify = false,
          positivePrefix = '',
          zeroPad = false,
          prefixBaseX = false,
          customPadChar = ' ';
        var flagsl = flags.length;
        for (var j = 0; flags && j < flagsl; j++) {
          switch (flags.charAt(j)) {
          case ' ':
            positivePrefix = ' ';
            break;
          case '+':
            positivePrefix = '+';
            break;
          case '-':
            leftJustify = true;
            break;
          case "'":
            customPadChar = flags.charAt(j + 1);
            break;
          case '0':
            zeroPad = true;
            break;
          case '#':
            prefixBaseX = true;
            break;
          }
        }

        // parameters may be null, undefined, empty-string or real valued
        // we want to ignore null, undefined and empty-string values
        if (!minWidth) {
          minWidth = 0;
        } else if (minWidth == '*') {
          minWidth = +a[i++];
        } else if (minWidth.charAt(0) == '*') {
          minWidth = +a[minWidth.slice(1, -1)];
        } else {
          minWidth = +minWidth;
        }

        // Note: undocumented perl feature:
        if (minWidth < 0) {
          minWidth = -minWidth;
          leftJustify = true;
        }

        if (!isFinite(minWidth)) {
          throw new Error('sprintf: (minimum-)width must be finite');
        }

        if (!precision) {
          precision = 'fFeE'.indexOf(type) > -1 ? 6 : (type == 'd') ? 0 : undefined;
        } else if (precision == '*') {
          precision = +a[i++];
        } else if (precision.charAt(0) == '*') {
          precision = +a[precision.slice(1, -1)];
        } else {
          precision = +precision;
        }

        // grab value using valueIndex if required?
        value = valueIndex ? a[valueIndex.slice(0, -1)] : a[i++];

        switch (type) {
        case 's':
          return formatString(String(value), leftJustify, minWidth, precision, zeroPad, customPadChar);
        case 'c':
          return formatString(String.fromCharCode(+value), leftJustify, minWidth, precision, zeroPad);
        case 'b':
          return formatBaseX(value, 2, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
        case 'o':
          return formatBaseX(value, 8, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
        case 'x':
          return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
        case 'X':
          return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad).toUpperCase();
        case 'u':
          return formatBaseX(value, 10, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
        case 'i':
        case 'd':
          number = +value || 0;
          number = Math.round(number - number % 1); // Plain Math.round doesn't just truncate
          prefix = number < 0 ? '-' : positivePrefix;
          value = prefix + pad(String(Math.abs(number)), precision, '0', false);
          return justify(value, prefix, leftJustify, minWidth, zeroPad);
        case 'e':
        case 'E':
        case 'f': // Should handle locales (as per setlocale)
        case 'F':
        case 'g':
        case 'G':
          number = +value;
          prefix = number < 0 ? '-' : positivePrefix;
          method = ['toExponential', 'toFixed', 'toPrecision']['efg'.indexOf(type.toLowerCase())];
          textTransform = ['toString', 'toUpperCase']['eEfFgG'.indexOf(type) % 2];
          value = prefix + Math.abs(number)[method](precision);
          return justify(value, prefix, leftJustify, minWidth, zeroPad)[textTransform]();
        default:
          return substring;
        }
      };

      return format.replace(regex, doFormat);
    }

  }
  
  TBX.format = new Format;
})(jQuery);

/* /op/Waypoints.js */
(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};

  function Waypoints() {
    this.add = function($el, func) {
      $el.waypoint(func.bind($el)).addClass('waypoint-added');
      console.log('waypoint added to ' + $el.attr('class'));
    };
  }

  TBX.waypoints = new Waypoints;
})(jQuery);


/* /gallery.js */
/* Author: Florian Maul */
/* http://www.techbits.de/2011/10/25/building-a-google-plus-inspired-image-gallery/ */
var Gallery = (function($) {
	/* ------------ PRIVATE variables ------------ */
  // defaults
  var configuration = {
  	'thumbnailSize':'960x180',
  	'marginsOfImage': 10,
  	'defaultWidthValue':120,
  	'defaultHeightValue':120
  };

  // Keep track of remaining width on last row and date
  var lastRowWidthRemaining = 0;
  var lastDate = null;
  var videoQueue = {};
  var inited = false;
  var batchEmpty;
  var currentPage = 1;

	/* ------------ PRIVATE functions ------------ */

	/** Utility function that returns a value or the defaultvalue if the value is null */
	var $nz = function(value, defaultvalue) {
		if( typeof (value) === undefined || value == null) {
			return defaultvalue;
		}
		return value;
	};

  var init = function() {
    if(inited)
      return;

    setStartPage();
    inited = true;
  };

  var trackPages = function(direction) {
    var $el = this, thisPage = $el.attr('data-waypoint-page'), pathname = location.pathname, router = op.data.store.Router;

    // check if page parameter exists and remove it
    if(TBX.util.getPathParam('page') !== null)
      pathname = pathname.replace(/\/page-[0-9]+/, '');

    pathname = pathname.replace(/\/photos/, '/photos/page-'+thisPage);
    router.navigate(pathname, {replace: true});
  };

  var setStartPage = function() {
    var page;

    page = TBX.util.getQueryParam('page');
    if(page !== null) {
      currentPage = page;
      return;
    } 

    page = TBX.util.getPathParam('page');
    if(page !== null) {
      currentPage = page;
      return;
    } 
  };

  var dateSeparator = function(model, ts) {
    var calendarContainer, view;
    calendarContainer= $('<div/>');
    calendarContainer.attr('class', 'date-placeholder');
    view = new op.data.view.PhotoGalleryDate({model: model, el: calendarContainer});
    view.render();
    return calendarContainer;
  };

  var parseURL = function(url) {
      //save the unmodified url to href property
      //so that the object we get back contains
      //all the same properties as the built-in location object
      var loc = { 'href' : url };

      //split the URL by single-slashes to get the component parts
      var parts = url.replace('//', '/').split('/');

      //store the protocol and host
      loc.protocol = parts[0];
      loc.host = parts[1];

      //extract any port number from the host
      //from which we derive the port and hostname
      parts[1] = parts[1].split(':');
      loc.hostname = parts[1][0];
      loc.port = parts[1].length > 1 ? parts[1][1] : '';

      //splice and join the remainder to get the pathname
      parts.splice(0, 2);
      loc.pathname = '/' + parts.join('/');

      //extract any hash and remove from the pathname
      loc.pathname = loc.pathname.split('#');
      loc.hash = loc.pathname.length > 1 ? '#' + loc.pathname[1] : '';
      loc.pathname = loc.pathname[0];

      //extract any search query and remove from the pathname
      loc.pathname = loc.pathname.split('?');
      loc.search = loc.pathname.length > 1 ? '?' + loc.pathname[1] : '';
      loc.pathname = loc.pathname[0];

      //return the final object
      return loc;
  }
	
	/**
	 * Distribute a delta (integer value) to n items based on
	 * the size (width) of the items thumbnails.
	 * 
	 * @method calculateCutOff
	 * @property len the sum of the width of all thumbnails
	 * @property delta the delta (integer number) to be distributed
	 * @property items an array with items of one row
	 */
	var calculateCutOff = function(len, delta, items) {
		// resulting distribution
		var cutoff = [];
		var cutsum = 0;
        var photoKey = 'photo' + configuration['thumbnailSize'];

		// distribute the delta based on the proportion of
		// thumbnail size to length of all thumbnails.
		for(var i in items) {
			var item = items[i];
			var fractOfLen = item[photoKey][1] / len;
			cutoff[i] = Math.floor(fractOfLen * delta);
			cutsum += cutoff[i];
		}

		// still more pixel to distribute because of decimal
		// fractions that were omitted.
		var stillToCutOff = delta - cutsum;
		while(stillToCutOff > 0) {
			for(i in cutoff) {
				// distribute pixels evenly until done
				cutoff[i]++;
				stillToCutOff--;
				if (stillToCutOff == 0) break;
			}
		}
		return cutoff;
	};
	
	/**
	 * Takes images from the items array (removes them) as 
	 * long as they fit into a width of maxwidth pixels.
	 *
	 * @method buildImageRow
	 */
	var buildImageRow = function(maxwidth, items) {
		var row = [], len = 0, currentDate, d, lastDate;
		var photoKey = 'photo' + configuration['thumbnailSize'];


    // if the last row has pixels left just fill them
    if(lastRowWidthRemaining > 0)
      maxwidth = lastRowWidthRemaining;

    // once adjusted the last row always has maxwidth left
    lastRowWidthRemaining = maxwidth;
		
		// each image a has a 3px margin, i.e. it takes 6px additional space
		var marginsOfImage = configuration['marginsOfImage'];

		// Build a row of images until longer than maxwidth
		while(items.length > 0 && len < maxwidth) {
			var item = items[0];
			row.push(item);
			len += (item[photoKey][1] + marginsOfImage);
      items.shift();
		}

		// calculate by how many pixels too long?
		var delta = len - maxwidth;

		// if the line is too long, make images smaller
		if(row.length > 0 && delta > 0) {

			// calculate the distribution to each image in the row
			var cutoff = calculateCutOff(len, delta, row);
			for(var i in row) {
				var pixelsToRemove = cutoff[i];
				item = row[i];

				// move the left border inwards by half the pixels
				item.vx = Math.floor(pixelsToRemove / 2);

				// shrink the width of the image by pixelsToRemove
				item.vwidth = item[photoKey][1] - pixelsToRemove;
        lastRowWidthRemaining -= (item.vwidth+marginsOfImage);
			}
		} else {
			// all images fit in the row, set vx and vwidth
			for(var i in row) {
				item = row[i];
				item.vx = 0;
				item.vwidth = item[photoKey][1];
        lastRowWidthRemaining -= (item.vwidth+marginsOfImage);
			}
		}
		return row;
	};
	
	/**
	 * Creates a new thumbail in the image area. An attaches a fade in animation
	 * to the image. 
	 */
	var createImageElement = function(parent, item) {
    var d = new Date(item.dateTaken*1000);
    var pageObject = TBX.init.pages.photos;
    var qsRe = /(page|returnSizes)=[^&?]+\&?/g;
    var qs = pageObject.pageLocation.search.replace(qsRe, '');
    var pinnedClass = !batchEmpty && OP.Batch.exists(item.id) ? 'pinned' : '';
    var imageContainer = $('<div class="imageContainer photo-id-'+item.id+' '+pinnedClass+'"/>');

    // we need to "mark" the first element for each api response
    //  See gh-1434
    var isFirstItemInResponse = typeof(item.totalRows) === 'number';
    imageContainer.attr("data-waypoint-page", currentPage);

    if(isFirstItemInResponse)
      imageContainer.addClass('first-in-response');

		var pathKey = 'path' + configuration['thumbnailSize'];
		var defaultWidthValue = configuration['defaultWidthValue'];
		var defaultHeightValue = configuration['defaultHeightValue'];

		var overflow = $("<div/>");
		overflow.css("width", ""+$nz(item.vwidth, defaultWidthValue)+"px");
		overflow.css("height", ""+$nz(item[pathKey][1], defaultHeightValue)+"px");
		overflow.css("overflow", "hidden");

    var urlParts = parseURL(item.url);
    if(pageObject.filterOpts !== undefined && pageObject.filterOpts !== null && pageObject.filterOpts.length > 0) {
      if(qs.length === 0)
        urlParts.pathname = urlParts.pathname+'/'+pageObject.filterOpts;
      else
        urlParts.pathname = urlParts.pathname.replace('?', '/'+pageObject.filterOpts+'?');
    }
		var link = $('<a/>');
    link.attr('href', urlParts.pathname+qs);
		link.attr("data-id", item.id);
		
		var img = $("<img/>");
		img.attr("data-id", item.id);
		img.attr("src", item[pathKey]);
    //img.attr('class', 'photo-view-modal-click');
    img.attr('class', 'photoModal');
		img.attr("title", item.title);
		img.css("width", "" + $nz(item[pathKey][1], defaultWidthValue) + "px");
		img.css("height", "" + $nz(item[pathKey][2], defaultHeightValue) + "px");
		img.css("margin-left", "" + (item.vx ? (-item.vx) : 0) + "px");
		img.css("margin-top", "" + 0 + "px");
		img.hide();

		var overflow = $("<div/>");
		overflow.css("width", ""+$nz(item.vwidth, defaultWidthValue)+"px");
		overflow.css("height", ""+$nz(item[pathKey][1], defaultHeightValue)+"px");
		overflow.css("overflow", "hidden");
    if(typeof(item.video) !== 'undefined' && typeof(item.videoSource) !== 'undefined') {
      overflow.addClass("video");
      overflow.append('<div class="video-element video-element-'+item.id+' is-splash" style="height:'+configuration.thumbnailHeight+'px; background:url(\''+item[pathKey]+'\') 100%;"/>');
      videoQueue[item.id] = {
        id: item.id,
        //file:'http://content.bitsontherun.com/videos/lWMJeVvV-364767.mp4',
        file: item.videoSource,
        image: item[pathKey],
        width: item.vwidth,
        title: item.name,
        height: configuration.thumbnailHeight
      };
      imageContainer.append(overflow);
    } else {
      var link = $('<a/>');
      link.attr('href', urlParts.pathname+qs);
      link.attr('title', _.escape(item.title));
      link.attr("data-id", item.id);
      
      var img = $("<img/>");
      img.attr("data-id", item.id);
      img.attr("src", item[pathKey]);
      //img.attr('class', 'photo-view-modal-click');
      img.attr('class', 'photoModal');
      img.attr("alt", _.escape(item.title));
      img.css("width", "" + $nz(item[pathKey][1], defaultWidthValue) + "px");
      img.css("height", "" + $nz(item[pathKey][2], defaultHeightValue) + "px");
      img.css("margin-left", "" + (item.vx ? (-item.vx) : 0) + "px");
      img.css("margin-top", "" + 0 + "px");
      img.hide();

      link.append(img);
      overflow.append(link);
      imageContainer.append(overflow);

      // fade in the image after load
      img.bind("load", function () { 
        $(this).fadeIn(400); 
      });
    }

    /**
     * Add meta information to bottom
     *
     * @date 2012-12-11
     * @author fabrizim
     */
    var meta = $('<div class="meta" />').appendTo(imageContainer)
    // while we could grab this directly from the item,
    // this should all be derived from the Backbone Store
    // for the page
      , model = op.data.store.Photos.get(item.id)
      , view = new op.data.view.PhotoGallery({model: model, el: meta});
    
    view.render();
    
    // End meta section

		// fade in the image after load
		img.bind("load", function () { 
			$(this).fadeIn(400); 
		});

    // insert calendar icon
    var d = new Date(item.dateTakenYear, item.dateTakenMonth - 1, item.dateTakenDay);
    currentDate = d.getYear()+'-'+d.getMonth()+'-'+d.getDay();
    imageContainer.append(dateSeparator(model, d));
    lastDate = currentDate;

		parent.append(imageContainer);
    return parseInt(imageContainer.width());
	};
	
	/**
	 * Updates an exisiting tthumbnail in the image area. 
	 */
	var updateImageElement = function(item) {
		var overflow = item.el.find("div:first");
		var img = overflow.find("img:first");

		var defaultWidthValue = configuration['defaultWidthValue'];
		var defaultHeightValue = configuration['defaultHeightValue'];

		overflow.css("width", "" + $nz(item.vwidth, defaultWidthValue) + "px");
		overflow.css("height", "" + $nz(item.theight, defaultHeightValue) + "px");

		img.css("margin-left", "" + (item.vx ? (-item.vx) : 0) + "px");
		img.css("margin-top", "" + 0 + "px");
	};	
		
	/* ------------ PUBLIC functions ------------ */
	return {
		setConfig : function(key, value) {
			configuration[key] = value;
		},

		showImages : function(photosContainer, realItems) {
      // initialize
      init();

      // check if the batch queue is empty
      // we do this here to keep from having to call length for each photo, just for each page
      batchEmpty = OP.Batch.length() === 0;

			// reduce width by 1px due to layout problem in IE
      var containerWidth = photosContainer.width() - 1;
			
			// Make a copy of the array
			var items = realItems.slice();

		
			// calculate rows of images which each row fitting into
			// the specified windowWidth.
			var rows = [];
			while(items.length > 0) {
				rows.push(buildImageRow(containerWidth, items));
			}  

			for(var r in rows) {
				for(var i in rows[r]) {
					var item = rows[r][i];
          createImageElement(photosContainer, item);
				}
			}

      // add waypoint for new page (:not() selector skips previously added elements)
      TBX.waypoints.add($('.imageContainer.first-in-response:not(.waypoint-added)'), trackPages);
      currentPage++;
		}
	}
})(jQuery);

/* /intro.js */
/**
 * Intro.js v0.1.0
 * https://github.com/usablica/intro.js
 * MIT licensed
 *
 * Copyright (C) 2013 usabli.ca - A weekend project by Afshin Mehrabani (@afshinmeh)
 */ 

(function () {

  //Default config/variables
  var VERSION = "0.1.1";

  /**
   * IntroJs main class
   *
   * @class IntroJs
   */
  function IntroJs(obj) {
    this._targetElement = obj;
  }

  /**
   * Initiate a new introduction/guide from an element in the page
   *
   * @api private
   * @method _introForElement
   * @param {Object} targetElm
   * @returns {Boolean} Success or not?
   */
  function _introForElement(targetElm) {
    var allIntroSteps = targetElm.querySelectorAll("*[data-intro]"),
        introItems = [],
        self = this;

    //if there's no element to intro
    if(allIntroSteps.length < 1) {
      return;
    }

    for (var i = 0, elmsLength = allIntroSteps.length; i < elmsLength; i++) {
      var currentElement = allIntroSteps[i];
      introItems.push({
        element: currentElement,
        intro: currentElement.getAttribute("data-intro"),
        step: parseInt(currentElement.getAttribute("data-step"))
      });
    }

    //Ok, sort all items with given steps
    introItems.sort(function (a, b) {
      return a.step - b.step;
    });

    //set it to the introJs object
    self._introItems = introItems;

    //add overlay layer to the page
    if(_addOverlayLayer(targetElm)) {
      //then, start the show
      _nextStep.call(self);

      var skipButton = targetElm.querySelector(".introjs-skipbutton"),
          nextStepButton = targetElm.querySelector(".introjs-nextbutton");

      targetElm.onkeydown = function(e) {
        if(e.keyCode == 27) {
          //escape key pressed, exit the intro
          _exitIntro(targetElm);
        }
        if([37, 39].indexOf(e.keyCode) >= 0) {
          if(e.keyCode == 37) {
            //left arrow
            _previousStep.call(self);
          } else if (e.keyCode == 39) {
            //right arrow
            _nextStep.call(self);
          }
        };
      }
    }
    return false;
  }

  /**
   * Go to next step on intro
   *
   * @api private
   * @method _nextStep
   */
  function _nextStep() {
    if(this._currentStep == undefined) {
      this._currentStep = 0;
    } else {
      ++this._currentStep;
    }
    if((this._introItems.length) <= this._currentStep) {
      //end of the intro
      //check if any callback is defined
      if (this._introCompleteCallback != undefined){
        this._introCompleteCallback();
      }
      _exitIntro(this._targetElement);
      return;
    }
    _showElement.call(this, this._introItems[this._currentStep].element);

  }

  /**
   * Go to previous step on intro
   *
   * @api private
   * @method _nextStep
   */
  function _previousStep() {
    if(this._currentStep == 0)
      return;

    _showElement.call(this, this._introItems[--this._currentStep].element);
  }

  /**
   * Exit from intro
   *
   * @api private
   * @method _exitIntro
   * @param {Object} targetElement
   */
  function _exitIntro(targetElement) {
    //remove overlay layer from the page
    var overlayLayer = targetElement.querySelector(".introjs-overlay");
    //for fade-out animation
    overlayLayer.style.opacity = 0;
    setTimeout(function () {
      overlayLayer.parentNode.removeChild(overlayLayer);
    }, 500);
    //remove all helper layers
    var helperLayer = targetElement.querySelector(".introjs-helperLayer");
    helperLayer.parentNode.removeChild(helperLayer);
    //remove `introjs-showElement` class from the element
    var showElement = document.querySelector(".introjs-showElement");
    showElement.className = showElement.className.replace(/introjs-showElement/,'').trim();
    //clean listeners
    targetElement.onkeydown = null;
  }

  /**
   * Show an element on the page
   *
   * @api private
   * @method _showElement
   * @param {Object} targetElement
   */
  function _showElement(targetElement) {
  
    var self = this,
        oldHelperLayer = document.querySelector(".introjs-helperLayer"),
        elementPosition = _getOffset(targetElement);

    //targetElement.scrollIntoView();
    if(oldHelperLayer != null) {
      var oldHelperNumberLayer = oldHelperLayer.querySelector(".introjs-helperNumberLayer"),
          oldtooltipLayer = oldHelperLayer.querySelector(".introjs-tooltiptext"),
          oldtooltipContainer = oldHelperLayer.querySelector(".introjs-tooltip")

      //set new position to helper layer
      oldHelperLayer.setAttribute("style", "width: " + (elementPosition.width + 10) + "px; " +
                                  "height:" + (elementPosition.height + 10) + "px; " +
                                  "top:" + (elementPosition.top - 5) + "px;" +
                                  "left: " + (elementPosition.left - 5) + "px;");
      //set current step to the label
      oldHelperNumberLayer.innerHTML = targetElement.getAttribute("data-step");
      //set current tooltip text
      oldtooltipLayer.innerHTML = targetElement.getAttribute("data-intro");
      var oldShowElement = document.querySelector(".introjs-showElement");
      oldShowElement.className = oldShowElement.className.replace(/introjs-showElement/,'').trim();
      //change to new intro item
      targetElement.className += " introjs-showElement";

      //wait until the animation is completed
      setTimeout(function() {
        oldtooltipContainer.style.bottom = "-" + (_getOffset(oldtooltipContainer).height + 10) + "px";
      }, 300);

    } else {
      targetElement.className += " introjs-showElement";

      var helperLayer = document.createElement("div"),
          helperNumberLayer = document.createElement("span"),
          tooltipLayer = document.createElement("div");

      helperLayer.className = "introjs-helperLayer";
      helperLayer.setAttribute("style", "width: " + (elementPosition.width + 10) + "px; " +
                                        "height:" + (elementPosition.height + 10) + "px; " +
                                        "top:" + (elementPosition.top - 5) + "px;" +
                                        "left: " + (elementPosition.left - 5) + "px;");

      document.body.appendChild(helperLayer);
      
      helperNumberLayer.className = "introjs-helperNumberLayer";
      tooltipLayer.className = "introjs-tooltip";
      if(targetElement.getAttribute('data-width'))
        tooltipLayer.setAttribute("style", "width:" + targetElement.getAttribute("data-width") + "px;");

      helperNumberLayer.innerHTML = targetElement.getAttribute("data-step");
      tooltipLayer.innerHTML = "<div class='introjs-tooltiptext'>" + targetElement.getAttribute("data-intro") + "</div><div class='introjs-tooltipbuttons'></div>";
      helperLayer.appendChild(helperNumberLayer);
      helperLayer.appendChild(tooltipLayer);

      var skipTooltipButton = document.createElement("a");
      skipTooltipButton.className = "introjs-skipbutton";
      skipTooltipButton.href = "javascript:void(0);";
      skipTooltipButton.innerHTML = "Skip";

      var nextTooltipButton = document.createElement("a");

      nextTooltipButton.onclick = function() {
        _nextStep.call(self);
      };

      nextTooltipButton.className = "introjs-nextbutton";
      nextTooltipButton.href = "javascript:void(0);";
      nextTooltipButton.innerHTML = "Next →";

      skipTooltipButton.onclick = function() {
        _exitIntro(self._targetElement);
      };

      var tooltipButtonsLayer = tooltipLayer.querySelector('.introjs-tooltipbuttons');
      tooltipButtonsLayer.appendChild(skipTooltipButton);
      tooltipButtonsLayer.appendChild(nextTooltipButton);
      
      
      //set proper position
      tooltipLayer.style.bottom = "-" + (_getOffset(tooltipLayer).height + 10) + "px";
    }

    //scroll the page to the element position
    if(typeof(targetElement.scrollIntoViewIfNeeded) === "function") {
      //awesome method guys: https://bugzilla.mozilla.org/show_bug.cgi?id=403510
      //but I think this method has some problems with IE < 7.0, I should find a proper failover way
      targetElement.scrollIntoViewIfNeeded();
    }
  }

  /**
   * Add overlay layer to the page
   *
   * @api private
   * @method _addOverlayLayer
   * @param {Object} targetElm
   */
  function _addOverlayLayer(targetElm) {
    var overlayLayer = document.createElement("div"),
        styleText = "";
    //set css class name
    overlayLayer.className = "introjs-overlay";
    
    //set overlay layer position
    var elementPosition = _getOffset(targetElm);
    if(elementPosition) {
      styleText += "width: " + elementPosition.width + "px; height:" + elementPosition.height + "px; top:" + elementPosition.top + "px;left: " + elementPosition.left + "px;";
      overlayLayer.setAttribute("style", styleText);
    }

    targetElm.appendChild(overlayLayer);

    overlayLayer.onclick = function() {
      _exitIntro(targetElm);
    };
    
    setTimeout(function() {
      styleText += "opacity: .5;";
      overlayLayer.setAttribute("style", styleText);
    }, 10);
    return true;
  }

  /**
   * Get an element position on the page
   * Thanks to `meouw`: http://stackoverflow.com/a/442474/375966
   *
   * @api private
   * @method _getOffset
   * @param {Object} element
   * @returns Element's position info
   */
  function _getOffset(element) {
    var elementPosition = {};

    //set width
    elementPosition.width = element.offsetWidth;

    //set height
    elementPosition.height = element.offsetHeight;

    //calculate element top and left
    var _x = 0;
    var _y = 0;
    while(element && !isNaN(element.offsetLeft) && !isNaN(element.offsetTop)) {
        _x += element.offsetLeft;
        _y += element.offsetTop;
        element = element.offsetParent;
    }
    //set top
    elementPosition.top = _y;
    //set left
    elementPosition.left = _x;

    return elementPosition;
  }

  var introJs = function (targetElm) {
    if (typeof (targetElm) === "object") {
      //Ok, create a new instance
      return new IntroJs(targetElm);

    } else if (typeof (targetElm) === "string") {
      //select the target element with query selector
      var targetElement = document.querySelector(targetElm);

      if(targetElement) {
        return new IntroJs(targetElement);
      } else {
        throw new Error("There's no element with given selector.");
      }
    } else {
      return new IntroJs(document.body);
    }
  };

  /**
   * Current IntroJs version
   *
   * @property version
   * @type String
   */
  introJs.version = VERSION;

  //Prototype
  introJs.fn = IntroJs.prototype = {
    clone: function () {
      return IntroJs(this);
    },
    start: function () {
      return _introForElement.call(this, this._targetElement);
    },
    complete: function( providedCallback ) {
      if (typeof (providedCallback) === "function") {
        this._introCompleteCallback = providedCallback;
      } else {
        throw new Error("Provided callback was not a function");
      }
    }
  };

  this['introJs'] = introJs;
})();

/* /fabrizio.js */
(function($) {

  function Fabrizio() {
    var crumb, markup, profiles, pushstate, tags, pathname, util;

    crumb = (function() {
      var value = null;
      return {
        get: function() {
          return value;
        },
        set: function(crumb) {
          value = crumb;
        }
      };
    })(); // crumb
    markup = {
      message: function(message) { // messageMarkup
        var cls = '';
        if(arguments.length > 1) {
          if(arguments[1] == 'error')
            cls = 'error';
          else if(arguments[1] == 'confirm')
            cls = 'success';
        }
        return '<div class="alert-message block-message '+cls+'"><a class="modal-close-click close" href="#">x</a>' + message + '</div>'
      },
      modal: function(header, body, footer) { // modalMarkup
        return '<div class="modal-header">' +
               '  <a href="#" class="close" data-dismiss="modal">&times;</a>' +
               '  <h3>'+header+'</h3>' +
               '</div>' +
               '<div class="modal-body">' +
               '  <p>'+body+'</p>' +
               '</div>' +
               (footer ? '<div class="modal-footer">' + footer + '</div>' : '');
      }
    }; // markup
    profiles = {
      owner: {},
      viewer: {},
      load: function() {
        // TODO cache this somehow
        $.get('/user/profile.json', {includeViewer: '1'}, function(response) {
          if(response.code !== 200)
            return;

          var result = response.result, id = result.id, owner = result, viewer = result.viewer || null;
          if(owner.viewer !== undefined)
            delete owner.viewer;
          TBX.callbacks.profilesSuccess(owner, viewer, profiles);
        }, 'json');
      }
    }; // profiles
    util = (function() {
      return {
        getDeviceWidth: function() {
          return $(window).width();
        },
        fetchAndCache: function(src) {
          $('<img />').attr('src', src).appendTo('body').css('display', 'none').on('load', function(ev) { $(ev.target).remove(); });
        },
        load: function(context) {
          var async = typeof(arguments[1]) === 'undefined' ? true : arguments[1], $button = $('button.loadMore');
          $('i', $button).show().addClass('icon-spinner icon-spin');
          // we define initData at runtime to avoid having to make an HTTP call on load
          // all subsequent calls run through the http API
          if(typeof(context.initData) === "undefined") {
            if(context.end || context.running)
              return;

            context.running = true;

            if(context.page === null) {
              var qsMatch = loc.href.match('page=([0-9]+)');
              if(qsMatch !== null) {
                context.page = qsMatch[1];
              } else {
                var uriMatch = loc.pathname.match(/\/page-([0-9]+)/);
                if(uriMatch !== null) {
                  context.page = uriMatch[1];
                }
              }

              if(context.page === null)
                context.page = 1;
            }

            var api = context.pageLocation.pathname+'.json';
                params = {}, qs = context.pageLocation.search.replace('?', '');
            
            if(qs.length > 0) {
              var qsKeyValueStrings = qs.split('&'), qsKeyAndValue;
              for(i in qsKeyValueStrings) {
                if(qsKeyValueStrings.hasOwnProperty(i)) {
                  qsKeyAndValue = qsKeyValueStrings[i].split('=');
                  if(qsKeyAndValue.length === 2) {
                    params[qsKeyAndValue[0]] = qsKeyAndValue[1];
                  }
                }
              }
            }

            params.returnSizes = '960x180,870x870,180x180xCR';
            params.page = context.page;
            // for mobile devices limit the number pages before a full page refresh. See #778
            if(context.pageCount > context.maxMobilePageCount && util.getDeviceWidth() < 900) {
              location.href = context.pageLocation.pathname + '?' + decodeURIComponent($.param(params));
            } else {
              $.ajax({
                async: async,
                dataType: 'json',
                url: api,
                data: params,
                success: util.loadCb.bind(context)
              });
            }
          } else {
            delete context.initData;
            context.page = 1;
            var response = {code:200, result:initData};
            util.loadCb.call(context, response);
          }
        },
        loadCb: function(response) {
          // this is the context, bound in the callback
          // at the last page
          var context = this, r = response.result, $button = $('button.loadMore');

          // check if there are more pages
          if(r.length > 0 && r[0].totalPages > r[0].currentPage) {
            $button.fadeIn();
            $('i', $button).fadeOut();
            context.page++;
            context.pageCount++;
            context.running = false;
          } else {
            $button.fadeOut();
            context.end = true;
          }
          
          // proceed to call the context's loadCb
          this.loadCb(response);
        },
        scrollCb: function(context) {
          // don't autoload if the width is narrow
          //  crude way to check if we're on a mobile device
          //  See #778
          if(util.getDeviceWidth() < 900)
            return;

          if($(window).scrollTop() > $(document).height() - $(window).height() - 200){
            context.load();
          }
        },
      };
    })(); // util

    this.crumb = function() { return crumb.get(); };
    this.init = {
      load: function(_crumb) {
        // http://stackoverflow.com/a/6974186
        // http://stackoverflow.com/questions/6421769/popstate-on-pages-load-in-chrome/10651028#10651028
        var popped = ('state' in window.history && window.history.state !== null);

        crumb.set(_crumb);
        OP.Tag.init();
        OP.Album.init();
        pathname = location.pathname;

        // TODO cache in local storage
        profiles.load();
        
        /**
         * Initialize tags typeahead
         */
        $('input.typeahead.tags').each(function(i, el) {
          new op.data.view.TagSearch({el: el});
        });
      },
      run: function() {
        if(location.pathname === '/') {
          TBX.init.pages.front.init();
        } else if(location.pathname.search(/^\/albums(.*)\/list/) === 0) {
          TBX.init.pages.albums.init();
          TBX.util.currentPage('albums');
        } else if(location.pathname.search(/^\/photos(.*)\/list/) === 0) {
          if(location.pathname.search(/album-/) !== -1)
            TBX.util.currentPage('album');
          else
            TBX.util.currentPage('photos');

          TBX.init.pages.photos.init();
        } else if(location.pathname.search(/^\/p\/[a-z0-9]+/) === 0 || location.pathname.search(/^\/photo\/[a-z0-9]+\/?(.*)\/view/) === 0) {
          TBX.init.pages.photo.init();
        } else if(location.pathname === '/photos/upload') {
          TBX.init.pages.upload();
        } else if(location.pathname === '/photos/upload/beta') {
          TBX.init.pages.uploadBeta();
        }
      },
      attachEvents: function() {
        OP.Util.on('keyup:escape', TBX.handlers.click.batchHide);
        OP.Util.on('keyup:slash', TBX.callbacks.showKeyboardShortcuts);
        OP.Util.on('callback:remove-spinners', TBX.callbacks.removeSpinners);
        OP.Util.on('callback:replace-spinner', TBX.callbacks.replaceSpinner);
        OP.Util.on('preload:photos', TBX.handlers.custom.preloadPhotos);
      },
      pages: {
        albums: {
          initData: typeof(initData) === "undefined" ? undefined : initData,
          filterOpts: typeof(filterOpts) === "undefined" ? undefined : filterOpts,
          batchModel: new op.data.model.Batch(),
          page: null,
          pageCount: 0,
          pageLocation: {
            pathname: window.location.pathname,
            search: window.location.search
          },
          maxMobilePageCount: 5,
          end: false,
          running: false,
          addAlbums: function(albums) {
            var album, model, view, $el;
            for(i in albums) {
              if(albums.hasOwnProperty(i)) {
                $el = $('<li class="album" />').appendTo($('ul.albums'))
                album = albums[i];
                op.data.store.Albums.add( album );
                model = op.data.store.Albums.get(album.id);
                view = new op.data.view.AlbumCover({model: model, el: $el});
                view.render();
              }
            }
          },
          init: function() {
            var _pages = TBX.init.pages, _this = _pages.albums, batchModel = _pages.albums.batchModel, $batchEl = $('.batch-meta');
            (new op.data.view.BatchIndicator({model:batchModel, el: $batchEl})).render();
            $(window).scroll(function() { util.scrollCb(_this); });
            _this.load();
          },
          load: function() {
            var _this = TBX.init.pages.albums; loc = location, async = typeof(arguments[0]) === 'undefined' ? true : arguments[0];
            util.load(_this, async);
          },
          loadCb: function(response) {
            var items = response.result, _this = TBX.init.pages.albums;

            for(i in items) {
              if(items.hasOwnProperty(i))
                op.data.store.Albums.add( items[i] );
            }

            if(items.length > 0)
              _this.addAlbums(items);
          }
        },
        front: {
          init: function() {}
        },
        photo: {
          initData: typeof(initData) === "undefined" ? undefined : initData,
          filterOpts: typeof(filterOpts) === "undefined" ? undefined : filterOpts,
          photo: null,
          el: $('.photo-detail'),
          init: function() {
            var options, _this = TBX.init.pages.photo;
            if(_this.initData === undefined) {
              return;
            }

            options = {
              routes: {
                "p/:id": "photoDetail" 
              },
              render: _this.render
            };
            op.data.store.Router = new op.data.route.Routes(options);
            // Start Backbone history a necessary step for bookmarkable URL's
            Backbone.history.start({pushState: true, silent: true});

            _this.photo = initData;
            delete _this.photo.actions;
            _this.render(_this.photo);
            delete _this.initData;
            OP.Util.on('keyup:left', TBX.callbacks.photoPrevious);
            OP.Util.on('keyup:right', TBX.callbacks.photoNext);
          },
          load: function(id) {
            // TODO don't hard code the returnSizes
            var _this = TBX.init.pages.photo, endpoint, apiParams = {nextprevious:'1', returnSizes:'90x90xCR,870x550'};
            
            if(_this.filterOpts === undefined || _this.filterOpts === null)
              endpoint = '/photo/'+id+'/view.json';
            else
              endpoint = '/photo/'+id+'/'+filterOpts+'/view.json';

            OP.Util.makeRequest(endpoint, apiParams, function(response) {
              _this.render(response.result);
            }, 'json', 'get');
          },
          render: function(photo) {
            var _this = TBX.init.pages.photo, $el = _this.el;
            op.data.store.Photos.add(photo);
            if( !_this.photoDetailView ){
              _this.photoDetailView = (new op.data.view.PhotoDetail({model: op.data.store.Photos.get(photo.id), el: $el})).render();
            }
            else {
              // instead of rerendering, lets just go to the specific photo
              // since it assumes it is already part of the store.
              _this.photoDetailView.go( photo.id );
            }
          }
        },
        photos: {
          // TODO have a better way of sending data into the JS framework. See #780
          initData: typeof(initData) === "undefined" ? undefined : initData,
          filterOpts: typeof(filterOpts) === "undefined" ? undefined : filterOpts,
          batchModel: new op.data.model.Batch({count: OP.Batch.length()}),
          page: null,
          pageCount: 0,
          pageLocation: {
            pathname: window.location.pathname,
            search: window.location.search
          },
          maxMobilePageCount: 5,
          end: false,
          running: false,
          init: function() {
            var options, _pages = TBX.init.pages, _this = _pages.photos, batchModel = _pages.photos.batchModel, $batchEl = $('.batch-meta');
            $(window).scroll(function() { util.scrollCb(_this); });
            _this.load();
            (new op.data.view.BatchIndicator({model:batchModel, el: $batchEl})).render();

            options = {
              routes: {
                "p/:id": "photoModal",
                "p/:id/*path": "photoModal",
                "p/:id?*path": "photoModal",
                "photos/list": "photosList",
                "photos/*path/list": "photosList"
              },
            };
            op.data.store.Router = new op.data.route.Routes(options);
            // Start Backbone history a necessary step for bookmarkable URL's
            Backbone.history.start({pushState: Modernizr.history, silent: true});
            Backbone.history.loadUrl(Backbone.history.getFragment());
          },
          load: function() {
            var _this = TBX.init.pages.photos, async = typeof(arguments[0]) === 'undefined' ? true : arguments[0], concat = _this.pageLocation.search.length == 0 ? '?' : '&';
            // since sort order for the API is static and onload changes the sort order for albums and gallery we need to mimic the controller behavior in JS
            if(_this.pageLocation.search.search('sortBy') === -1) {
              if(_this.pageLocation.pathname.search('/album-') === -1) // gallery
                _this.pageLocation.search += concat + 'sortBy=dateUploaded,desc';
              else // album
                _this.pageLocation.search += concat + 'sortBy=dateTaken,asc';
            }

            util.load(_this, async);
          },
          loadCb: function(response) {
            var items = response.result, _this = TBX.init.pages.photos,
                ui = TBX.ui, i, $button = $('button.loadMorePhotos');

            op.data.store.Photos.add( items );

            if(items.length > 0)
              Gallery.showImages($(".photo-grid"), items);
          }
        },
        upload: function() {
          OP.Util.on('upload:complete-success', TBX.callbacks.uploadCompleteSuccess);
          OP.Util.on('upload:complete-failure', TBX.callbacks.uploadCompleteFailure);
          OP.Util.on('upload:uploader-ready', TBX.callbacks.uploaderReady);
          OP.Util.on('submit:photo-upload', TBX.callbacks.upload);
          OP.Util.fire('upload:uploader-ready');
        },
        uploadBeta: function() {
          window.Dropzone.autoDiscover = false;
          OP.Util.on('upload:uploader-beta-ready', TBX.handlers.custom.uploaderBetaReady);
//        OP.Util.on('click:upload-dialog', TBX.handlers.uploadDialogBeta);
//        OP.Util.on('click:photo-upload', TBX.handlers.click.uploadBeta);
          OP.Util.fire('upload:uploader-beta-ready');
        }
      }
    }; // init
    this.notification = {
      model: new op.data.model.Notification,
      errorIcon: '<i class="icon-warning-sign"></i>',
      successIcon: '<i class="icon-ok"></i>',
      init: function() {
        var $el = $('.notification-meta'), view = new op.data.view.Notification({model: TBX.notification.model, el: $el});
      },
      show: function(message, type, mode) {
        var model = TBX.notification.model;
        if(mode === 'confirm' || typeof mode === 'undefined')
          message = TBX.notification.successIcon + ' ' + message;
        else
          message = TBX.notification.errorIcon + ' ' + message;

        type = type || 'flash';

        model.set('msg', message, {silent:true});
        model.set('mode', mode, {silent:true});
        model.set('type', type, {silent:true});
        model.save();
      },
      display: {
        generic: {
          error: function() {
            TBX.notification.show('Sorry, an unknown error occurred.', 'flash', 'error');
          }
        }
      }
    }; // notification
    this.profiles = {
      getOwner: function() {
        return profiles.owner.id;
      },
      getViewer: function() {
        return profiles.viewer.id;
      },
      getOwnerUsername: function() {
        return profiles.owner.name;
      }
    }; // profiles
  }

  var _TBX = new Fabrizio;
  TBX.profiles = _TBX.profiles;
  TBX.notification = _TBX.notification;
  TBX.init = _TBX.init;
  TBX.crumb = _TBX.crumb;
  TBX.log = OP.Log; // for consistency
})(jQuery);
