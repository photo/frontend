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

  var dateSeparator = function(ts) {
    var calendarContainer = $('<div/>');
    calendarContainer.attr('class', 'date-placeholder');
    calendarContainer.html('<i class="tb-icon-small-calendar tb-icon-light""></i> '+phpjs.date('M jS, Y', ts));
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
    imageContainer.append(dateSeparator(d));
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
