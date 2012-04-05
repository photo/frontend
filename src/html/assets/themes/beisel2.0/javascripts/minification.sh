#!/bin/bash

rm min/*

# copy the already minified bootstrap file
cp bootstrap.min.js min/bootstrap.min.js

# minify each js file
minify bootstrap-modal.js min/bootstrap-modal.min.js --force
minify bootstrap-typeahead.js min/bootstrap-typeahead.min.js --force
minify browserupdate.js min/browserupdate.min.js --force
minify gallery.js min/gallery.min.js --force
minify jquery.history.js min/jquery.history.min.js --force
minify jquery.scrollTo.js min/jquery.scrollTo.min.js --force
minify openphoto-theme.js min/openphoto-theme.min.js --force
minify phpjs.js min/phpjs.min.js --force
minify touchSwipe.js min/touchSwipe.min.js --force

# concatenate all the js files
for i in $(ls min/) ; do 
  echo "/* $i */" >> min/openphoto-theme-full.min.js;
  cat min/$i >> min/openphoto-theme-full.min.js;
done
