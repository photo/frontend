#!/bin/bash

rm min/*

# copy the already minified bootstrap file
cp bootstrap.min.js min/01-bootstrap.min.js

# minify each js file
minify chosen.jquery.js min/01a-chosen.jquery.min.js --force
minify bootstrap-modal.js min/02-bootstrap-modal.min.js --force
minify gallery.js min/05-gallery.min.js --force
minify jquery.history.js min/06-jquery.history.min.js --force
minify jquery.scrollTo.js min/07-jquery.scrollTo.min.js --force
minify openphoto-theme.js min/08-openphoto-theme.min.js --force
minify phpjs.js min/09-phpjs.min.js --force
minify touchSwipe.js min/10-touchSwipe.min.js --force

##jsmin < chosen.jquery.js > min/01a-chosen.jquery.min.js --force
##jsmin < bootstrap-modal.js > min/02-bootstrap-modal.min.js --force
##jsmin < gallery.js > min/05-gallery.min.js --force
##jsmin < jquery.history.js > min/06-jquery.history.min.js --force
##jsmin < jquery.scrollTo.js > min/07-jquery.scrollTo.min.js --force
##jsmin < openphoto-theme.js > min/08-openphoto-theme.min.js --force
##jsmin < phpjs.js > min/09-phpjs.min.js --force
##jsmin < touchSwipe.js > min/10-touchSwipe.min.js --force

# concatenate all the js files
for i in $(ls min/) ; do 
  echo "/* $i */" >> min/openphoto-theme-full.min.js;
  cat min/$i >> min/openphoto-theme-full.min.js;
done
