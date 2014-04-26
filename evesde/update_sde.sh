#!/bin/bash

# 2014 - Leon Jacobs
# SeAT

# This script should pull the latest static data as per $DUMPS from
# https://www.fuzzwork.co.uk/dump/ based in the expansion parameter.
# It will also immediately insert them into the database.

# Ask function borrowed from davejamesmiller, for more information check out
# the following gist: https://gist.github.com/davejamesmiller/1965569
function ask {
	while true; do

		if [ "${2:-}" = "Y" ]; then
			prompt="Y/n"
			default=Y
		elif [ "${2:-}" = "N" ]; then
			prompt="y/N"
			default=N
		else
			prompt="y/n"
			default=
		fi

		read -p "$1 [$prompt] " REPLY

		if [ -z "$REPLY" ]; then
			REPLY=$default
		fi

		case "$REPLY" in
			Y*|y*) echo 0 && break;;
			N*|n*) echo 1 && break;;
		esac

	done
}

URL="https://www.fuzzwork.co.uk/dump/"
EXPANSION="rubicon-1.3-95173"
EXTENTION="sql"
DUMPS="required_sde"
TEMP=/tmp/SeAT-$(date | md5sum | awk '{ print $1 }')
AUTO_IMPORT=$(ask "Automatically import required SQL files?" N)

if [ "$AUTO_IMPORT" -eq 0 ]; then
	read -p "Enter database user: " DB_USR
	read -s -p "Enter database password: " DB_PSS
	echo ""
	read -p "Enter database server (empty for localhost): " DB_SRV

	if [ -z "$DB_SRV" ]; then
			DB_SRV="localhost"
	fi

	read -p "Enter database name: " DB_NAME

	until mysql -e ";" -u $DB_USR --password=$DB_PSS -h $DB_SRV $DB_NAME; do
		echo "Database connection failed. Please try again."
		read -p "Enter database user: " DB_USR
		read -s -p "Enter database password: " DB_PSS
		echo ""
		read -p "Enter database server (empty for localhost): " DB_SRV

		if [ -z "$DB_SRV" ]; then
				DB_SRV="localhost"
		fi

		read -p "Enter database name: " DB_NAME
	done
fi

# Make the working directory in /tmp
echo "Creating temp directory $TEMP..."
mkdir -p $TEMP

# Download each file
while read p; do
	echo "Getting dump $p from $URL$EXPANSION/$p.$EXTENTION.bz2..."
	curl -s "$URL$EXPANSION/$p.$EXTENTION.bz2" > $TEMP/$p.$EXTENTION.bz2

	echo "Extracting $TEMP/$p.$EXTENTION.bz2 ..."
	bzip2 -d $TEMP/$p.$EXTENTION.bz2
done < $DUMPS

if [ "$AUTO_IMPORT" -eq 0 ]; then
	# Import the files into the MySQL server
	echo "Importing the SQL files into MySQL..."
	cat $TEMP/*.sql | mysql -u $DB_USR --password=$DB_PSS -h $DB_SRV $DB_NAME
	# Clean up after ourselves
	rm $TEMP/* && rmdir $TEMP
else
	echo "The extracted files can be found in $TEMP."
fi
