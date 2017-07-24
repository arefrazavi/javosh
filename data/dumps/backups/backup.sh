#!/bin/bash
# Database credentials
user="root"
password="05614313347"
host="localhost"
db_name="javosh_db"

# Other options
backup_path="/home/backups/mysql"
date=$(date +"%d-%b-%Y")

# Set default file permissions
umask 177

# Dump database into SQL file
mysqldump --user=$user --password=$password --host=$host $db_name | gzip  > $backup_path/$db_name-$date.sql.gz

# Delete files older than 30 days
 find $backup_path/* -mtime +30 -exec rm {} \;
