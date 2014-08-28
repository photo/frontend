#!/bin/bash

# This script generates the combined tbx.js JavaScript file for use when `mode="prod"`.
# Running this will save a file at /src/html/assets/javascripts/releases/$VERSION/tbx.js.
# TODO: Add link to documentation that describes how static assets are cached.
#   https://github.com/photo/documentation/issues/40
# These JS assets are what you can see in /src/html/assets/themes/fabrizio1.0/templates/template.php.
#   The order in which these files are combined need to match what's in template.php

VERSION=$(grep 'currentCodeVersion' ../configs/defaults.ini | awk -F '=' '{print $2}')
JSDIR="../html/assets/themes/fabrizio1.0/javascripts/"
OUTFILE="../html/assets/javascripts/releases/$VERSION/tbx.js"

cat $JSDIR/underscore-min.js \
  $JSDIR/underscore-min.js \
  $JSDIR/modernizr.custom.js \
  $JSDIR/backbone.js \
  $JSDIR/bootstrap.min.js \
  $JSDIR/jquery.color.js \
  $JSDIR/x-editable/bootstrap-editable/js/bootstrap-editable.js \
  $JSDIR/phpjs.js \
  $JSDIR/overrides.js \
  $JSDIR/op/namespace.js \
  $JSDIR/op/data/route/Routes.js \
  $JSDIR/op/data/model/Album.js \
  $JSDIR/op/data/model/Batch.js \
  $JSDIR/op/data/model/Notification.js \
  $JSDIR/op/data/model/Profile.js \
  $JSDIR/op/data/model/Photo.js \
  $JSDIR/op/data/model/ProgressBar.js \
  $JSDIR/op/data/model/Tag.js \
  $JSDIR/op/data/collection/Album.js \
  $JSDIR/op/data/collection/Profile.js \
  $JSDIR/op/data/collection/Photo.js \
  $JSDIR/op/data/collection/Tag.js \
  $JSDIR/op/data/store/Albums.js \
  $JSDIR/op/data/store/Profiles.js \
  $JSDIR/op/data/store/Photos.js \
  $JSDIR/op/data/store/Tags.js \
  $JSDIR/op/data/view/Editable.js \
  $JSDIR/op/data/view/BatchIndicator.js \
  $JSDIR/op/data/view/AlbumCover.js \
  $JSDIR/op/data/view/Notification.js \
  $JSDIR/op/data/view/PhotoDetail.js \
  $JSDIR/op/data/view/PhotoGallery.js \
  $JSDIR/op/data/view/PhotoGalleryDate.js \
  $JSDIR/op/data/view/ProfileName.js \
  $JSDIR/op/data/view/ProfilePhoto.js \
  $JSDIR/op/data/view/ProgressBar.js \
  $JSDIR/op/data/view/TagSearch.js \
  $JSDIR/op/data/view/UserBadge.js \
  $JSDIR/op/Lightbox.js \
  $JSDIR/op/Util.js \
  $JSDIR/op/Strings.js \
  $JSDIR/op/Handlers.js \
  $JSDIR/op/Highlight.js \
  $JSDIR/op/Callbacks.js \
  $JSDIR/op/Tutorial.js \
  $JSDIR/op/Upload.js \
  $JSDIR/op/Format.js \
  $JSDIR/gallery.js \
  $JSDIR/intro.js \
  $JSDIR/fabrizio.js > $OUTFILE
