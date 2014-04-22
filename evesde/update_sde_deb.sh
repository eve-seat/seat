#!/bin/bash

# 2013 - Leon Jacobs
# SeAT

# This script should pull the latest static data as per $DUMPS from
# https://www.fuzzwork.co.uk/dump/ based in the expansion parameter.
# It will also immediately insert them into the database.

URL="https://www.fuzzwork.co.uk/dump/"
EXPANSION="rubicon-1.3-95173"
EXTENTION="sql"
DUMPS="required_sde"
TEMPFILE=/tmp/SeAT-$(date | md5sum | awk '{ print $1 }')

read -p "Enter database user: " DB_USR
read -s -p "Enter database password: " DB_PSS
echo ""
read -p "Enter database server: " DB_SRV
read -p "Enter database name: " DB_NAME

# Make the working directory in /tmp
echo "Creating temp directory $TEMPFILE..."
mkdir -p $TEMPFILE

# Download each files
while read p; do
  echo "Getting dump $p from '$URL$EXPANSION/$p.$EXTENTION.bz2'..."
  curl -s "$URL$EXPANSION/$p.$EXTENTION.bz2" > $TEMPFILE/$p.$EXTENTION.bz2

  echo "Extracting $TEMPFILE/$p.$EXTENTION.bz2 ..."
  bzip2 -d $TEMPFILE/$p.$EXTENTION.bz2
done < $DUMPS

# Import the files into the MySQL server
echo "Importing the SQL files into MySQL..."
cat $TEMPFILE/*.sql | mysql -u $DB_USR --password=$DB_PSS -h $DB_SRV $DB_NAME

# Clean up after ourselves
rm $TEMPFILE/* && rmdir $TEMPFILE
