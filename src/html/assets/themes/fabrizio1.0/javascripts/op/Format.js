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
