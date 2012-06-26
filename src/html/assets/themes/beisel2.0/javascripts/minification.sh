#!/bin/bash

rm min/*

# copy the already minified bootstrap file
cp bootstrap.min.js min/01-bootstrap.min.js

# minify each js file
minify chosen.jquery.js min/01a-chosen.jquery.min.js --force
minify bootstrap-modal.js min/02-bootstrap-modal.min.js --force
#minify bootstrap-typeahead.js min/03-bootstrap-typeahead.min.js --force
#minify browserupdate.js min/04-browserupdate.min.js --force
minify gallery.js min/05-gallery.min.js --force
minify jquery.history.js min/06-jquery.history.min.js --force
minify jquery.scrollTo.js min/07-jquery.scrollTo.min.js --force
minify openphoto-theme.js min/08-openphoto-theme.min.js --force
minify phpjs.js min/09-phpjs.min.js --force
minify touchSwipe.js min/10-touchSwipe.min.js --force

# concatenate all the js files
for i in $(ls min/) ; do 
  echo "/* $i */" >> min/openphoto-theme-full.min.js;
  cat min/$i >> min/openphoto-theme-full.min.js;
done
