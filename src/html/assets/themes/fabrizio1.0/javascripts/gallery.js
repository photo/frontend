/* Author: Florian Maul */
/* http://www.techbits.de/2011/10/25/building-a-google-plus-inspired-image-gallery/ */
var Gallery = (function($) {
	/* ------------ PRIVATE variables ------------ */
	// Keep track of remaining width on last row and date
	var lastRowLen = 0;
	var lastRow = [];
	var lastDate = null;

	// defaults
	var configuration = {
		'thumbnailSize' : '960x180',
		'marginsOfImage' : 10,
		'defaultWidthValue' : 120,
		'defaultHeightValue' : 120
	};

	var batchEmpty;
	var defaultImageHeight = 135;
	var maxImageHeight = Math.floor(2 * defaultImageHeight);

	/* ------------ PRIVATE functions ------------ */

	/**
	 * Utility function that returns a value or the defaultvalue if the value is
	 * null
	 */
	var $nz = function(value, defaultvalue) {
		if (typeof (value) === undefined || value == null) {
			return defaultvalue;
		}
		return value;
	};

	var dateSeparator = function(ts) {
		var calendarContainer = $('<div/>'), a = $('<a/>');
		calendarContainer.attr('class', 'date-placeholder');
		a.html('<i class="tb-icon-small-calendar tb-icon-light""></i> '
				+ phpjs.date('M jS, Y', ts));
		calendarContainer.append(a);
		return calendarContainer;
	};

	var parseURL = function(url) {
		// save the unmodified url to href property
		// so that the object we get back contains
		// all the same properties as the built-in location object
		var loc = {
			'href' : url
		};

		// split the URL by single-slashes to get the component parts
		var parts = url.replace('//', '/').split('/');

		// store the protocol and host
		loc.protocol = parts[0];
		loc.host = parts[1];

		// extract any port number from the host
		// from which we derive the port and hostname
		parts[1] = parts[1].split(':');
		loc.hostname = parts[1][0];
		loc.port = parts[1].length > 1 ? parts[1][1] : '';

		// splice and join the remainder to get the pathname
		parts.splice(0, 2);
		loc.pathname = '/' + parts.join('/');

		// extract any hash and remove from the pathname
		loc.pathname = loc.pathname.split('#');
		loc.hash = loc.pathname.length > 1 ? '#' + loc.pathname[1] : '';
		loc.pathname = loc.pathname[0];

		// extract any search query and remove from the pathname
		loc.pathname = loc.pathname.split('?');
		loc.search = loc.pathname.length > 1 ? '?' + loc.pathname[1] : '';
		loc.pathname = loc.pathname[0];

		// return the final object
		return loc;
	}
	var calculateImageHeightResult = function(values, imageHeight, totalWidth,
			imageMargins) {
		var usedWidth = 0;
		var nonRedistributedWidth = 0;
		var totalWidthWithoutBorders = totalWidth - imageMargins
				* values.length;
		var ratios = [];
		var totalRatio = 0.0;
		for ( var i in values) {
			var value = values[i];
			var ratio = getRatio(value);
			ratios.push(ratio);
			totalRatio += ratio;
			var width = Math.floor(ratio * imageHeight);
			usedWidth += width;
		}
		var rest = totalWidthWithoutBorders - usedWidth;

		{
			imageHeight = Math.floor(totalWidthWithoutBorders / totalRatio);
			var limitReached = maxImageHeight > 0
					&& imageHeight >= maxImageHeight;
			if (limitReached) {
				imageHeight = maxImageHeight;
			} else {
				nonRedistributedWidth = totalWidthWithoutBorders;
				for ( var i in ratios) {
					var r = ratios[i]
					nonRedistributedWidth -= Math.floor(r * imageHeight);
				}
			}
		}
		return new ImageHeightResult(imageHeight, Math.max(0,
				nonRedistributedWidth));
	}

	function ImageHeightResult(itemHeight, nonRedistributedWidth) {
		this.imageHeight = itemHeight;
		this.nonRedistributedWidth = nonRedistributedWidth;
	}

	var photoKey = 'photo' + configuration['thumbnailSize'];
	var getWidth = function(item) {
		return item[photoKey][1];
	}
	var getHeight = function(item) {
		return item[photoKey][2];
	}
	var getRatio = function(item) {
		return getHeight(item) == 0 ? 1 : getWidth(item) / getHeight(item);
	}
	var getWidthForDefaultHeight = function(item)
	{
		return Math.floor(getRatio(item) * defaultImageHeight);
	}
	/**
	 * Takes images from the items array (removes them) as long as they fit into
	 * a width of maxwidth pixels.
	 * 
	 * @method buildImageRow
	 */
	var buildImageRow = function(maxwidth, items) {
		var row = [], len = 0;

		// each image a has a 3px margin, i.e. it takes 6px additional space
		var marginsOfImage = configuration['marginsOfImage'];

		if (lastRowLen > 0 && lastRow.length > 0) {
			var item = items[0]
			var imageWidthWithMargins = getWidthForDefaultHeight(item) + marginsOfImage;
			if ((lastRowLen + imageWidthWithMargins) < maxwidth) {
				row = lastRow;
				len = lastRowLen;
			}
		}

		// Build a row of images until longer than maxwidth
		while (items.length > 0 && len < maxwidth) {
			var item = items[0]
			var imageWidthWithMargins = getWidthForDefaultHeight(item) + marginsOfImage;
			if (row.length == 0 || (len + imageWidthWithMargins) < maxwidth) {
				items.shift();
				row.push(item);
				len += imageWidthWithMargins;
			} else {
				break;
			}
		}
		lastRowLen = len;

		var imageHeightResult = calculateImageHeightResult(row,
				defaultImageHeight, maxwidth, marginsOfImage);

		var perStepExtraWidth = Math
				.ceil(imageHeightResult.nonRedistributedWidth / row.length);
		var usedExtraWidth = 0;
		for ( var i in row) {
			var value = row[i];
			var extraWidth = 0;
			if (usedExtraWidth < imageHeightResult.nonRedistributedWidth) {
				extraWidth = Math.min(imageHeightResult.nonRedistributedWidth
						- usedExtraWidth, perStepExtraWidth);
				usedExtraWidth += extraWidth;
			}

			var ratio = getRatio(value);
			var height = imageHeightResult.imageHeight;
			var width = Math.floor(ratio * height) + extraWidth;
			value.vx = 0;
			value.vwidth = width;
			value.vheight = height;
		}
		lastRow = row;
		return row;
	};

	/**
	 * Creates a new thumbail in the image area. An attaches a fade in animation
	 * to the image.
	 */
	var createImageElement = function(parent, item) {
		var pageObject = TBX.init.pages.photos;
		var qsRe = /(page|returnSizes)=[^&?]+\&?/g;
		var qs = pageObject.pageLocation.search.replace(qsRe, '');
		var pinnedClass = !batchEmpty && OP.Batch.exists(item.id) ? 'pinned'
				: '';
		var imageContainer = $('<div class="imageContainer photo-id-' + item.id
				+ ' ' + pinnedClass + '"/>');

		var d = new Date(item.dateTaken * 1000);

		var pathKey = 'path' + configuration['thumbnailSize'];
		var defaultWidthValue = configuration['defaultWidthValue'];
		var defaultHeightValue = configuration['defaultHeightValue'];

		var overflow = $("<div/>");
		overflow.css("width", "" + $nz(item.vwidth, defaultWidthValue) + "px");
		overflow.css("height", "" + $nz(item.vheight, defaultHeightValue)
				+ "px");
		overflow.css("overflow", "hidden");

		var urlParts = parseURL(item.url);
		if (pageObject.filterOpts !== undefined
				&& pageObject.filterOpts !== null
				&& pageObject.filterOpts.length > 0) {
			if (qs.length === 0)
				urlParts.pathname = urlParts.pathname + '/'
						+ pageObject.filterOpts;
			else
				urlParts.pathname = urlParts.pathname.replace('?', '/'
						+ pageObject.filterOpts + '?');
		}
		var link = $('<a/>');
		link.attr('href', urlParts.pathname + qs);
		link.attr("data-id", item.id);

		var img = $("<img/>");
		img.attr("data-id", item.id);
		img.attr("src", item[pathKey]);
		// img.attr('class', 'photo-view-modal-click');
		img.attr('class', 'photoModal');
		img.attr("title", item.title);
		img.css("width", "" + $nz(item.vwidth, defaultWidthValue) + "px");
		img.css("height", "" + $nz(item.vheight, defaultHeightValue) + "px");
		img.css("margin-left", "" + (item.vx ? (-item.vx) : 0) + "px");
		img.css("margin-top", "" + 0 + "px");
		img.hide();

		link.append(img);
		overflow.append(link);
		imageContainer.append(overflow);

		// insert calendar icon
		currentDate = d.getYear() + '-' + d.getMonth() + '-' + d.getDay();
		if (currentDate !== lastDate)
			imageContainer.prepend(dateSeparator(item.dateTaken));
		lastDate = currentDate;

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
		, model = op.data.store.Photos.get(item.id), view = new op.data.view.PhotoGallery(
				{
					model : model,
					el : meta
				});

		view.render();

		// End meta section

		// fade in the image after load
		img.bind("load", function() {
			$(this).fadeIn(400);
		});
		item.el = imageContainer;
		parent.append(imageContainer);
		return parseInt(imageContainer.width());
	};

	/**
	 * Updates an exisiting tthumbnail in the image area.
	 */
	var updateImageElement = function(item) {
		var overflow = item.el.find('div:not([class]),div[class=""]').first();
		
			
		var img = overflow.find("img:first");

		var defaultWidthValue = configuration['defaultWidthValue'];
		var defaultHeightValue = configuration['defaultHeightValue'];

		overflow.css("width", "" + $nz(item.vwidth, defaultWidthValue) + "px");
		overflow.css("height", "" + $nz(item.vheight, defaultHeightValue)
				+ "px");

		img.css("margin-left", "" + (item.vx ? (-item.vx) : 0) + "px");
		img.css("margin-top", "" + 0 + "px");

		img.css("width", "" + $nz(item.vwidth, defaultWidthValue) + "px");
		img.css("height", "" + $nz(item.vheight, defaultHeightValue) + "px");
	};

	/* ------------ PUBLIC functions ------------ */
	return {
		setConfig : function(key, value) {
			configuration[key] = value;
		},

		showImages : function(photosContainer, realItems) {
			// check if the batch queue is empty
			// we do this here to keep from having to call length for each
			// photo, just for each page
			batchEmpty = OP.Batch.length() === 0;
			
			/*if(lastRow.length == 0 && realItems.length > 0)
			{
				var item = realItems[0];
				item.vwidth = 120;
				item.vheight = 120;
				createImageElement(photosContainer, item);
			}*/

			// reduce width by 1px due to layout problem in IE
			var containerWidth = photosContainer.width() - 1;

			// Make a copy of the array
			var items = realItems.slice();

			// calculate rows of images which each row fitting into
			// the specified windowWidth.
			var rows = [];
			while (items.length > 0) {
				rows.push(buildImageRow(containerWidth, items));
			}

			for ( var r in rows) {
				for ( var i in rows[r]) {
					var item = rows[r][i];
					if (typeof (item.el) !== 'undefined') {
						updateImageElement(item);
					} else {
						createImageElement(photosContainer, item);
					}
				}
			}
		}
	}
})(jQuery);
