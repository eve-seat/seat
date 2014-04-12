#!/bin/bash

# 2013 - Leon Jacobs
# SeAT

# This script should pull the latest static data as per $DUMPS from
# https://www.fuzzwork.co.uk/dump/ based in the expansion parameter

URL="https://www.fuzzwork.co.uk/dump/"
EXPANSION="rubicon-1.3-95173"
EXTENTION="sql"
DUMPS="required_sde"
TEMPFILE=/tmp/SeAT-$(date | md5sum | awk '{ print $1 }')

# check how many lines in $DUMPS for a form of progress
echo "We have $(wc -l $DUMPS | awk '{ print $1 }') static datadump files to do."
COUNT=$(wc -l $DUMPS | awk '{ print $1 }')
PROGRESS=1

# make the working directory in /tmp
echo "Creating temp directory $TEMPFILE"
mkdir -p $TEMPFILE

# download each files
while read p; do

  echo "$PROGRESS/$COUNT - Getting dump $p from '$URL$EXPANSION/$p.$EXTENTION.bz2' .."
  curl -s "$URL$EXPANSION/$p.$EXTENTION.bz2" > $TEMPFILE/$p.$EXTENTION.bz2

  echo "        Extracting $TEMPFILE/$p.$EXTENTION.bz2 ..."
  bzip2 -d $TEMPFILE/$p.$EXTENTION.bz2

  # increment progress
  PROGRESS=$[PROGRESS + 1]

done < $DUMPS

# list $TEMPFILE
echo "Done, here is the contents of $TEMPFILE"
ls -lah $TEMPFILE

# suggest MySQL import command
echo "Import the .sql thats in $TEMPFILE with something like:"
echo "mysql -u seat -p seat < $TEMPFILE/*.sql"
