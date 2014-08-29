#!/bin/bash

# This script generates the combined tbx.js JavaScript file for use when `mode="prod"`.
# Running this will save a file at /src/html/assets/javascripts/releases/$VERSION/tbx.js.
# TODO: Add link to documentation that describes how static assets are cached.
#   https://github.com/photo/documentation/issues/40
# These JS assets are what you can see in /src/html/assets/themes/fabrizio1.0/templates/template.php.
#   The order in which these files are combined need to match what's in template.php

VERSION=$(grep 'currentCodeVersion' ../configs/defaults.ini | awk -F '=' '{print $2}')
CONFIGFILE="../configs/js-assets.txt"
JSDIR="../html/assets/themes/fabrizio1.0/javascripts"
OUTFILE="../html/assets/javascripts/releases/$VERSION/tbx.js"

echo -n "Truncating file $OUTFILE..."
cat /dev/null > $OUTFILE
echo "OK"

echo -n "Writing new asset file $OUTFILE..."
while IFS= read -r file; do
  if [ -f "$JSDIR$file" ];
  then
    printf "\n/* $file */\n" >> $OUTFILE
    cat $JSDIR$file >> $OUTFILE
  fi
done < $CONFIGFILE
echo -n "OK"
