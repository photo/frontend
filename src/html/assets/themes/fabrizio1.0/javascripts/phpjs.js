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

