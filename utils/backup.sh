#!/bin/bash

DB_NAME="shuleapp_db"
DB_USER="malonja"
DB_PASSWORD="jbjhhgdtrdtrfeydrtckgcfvhj"
S3_BUCKET="s3://shuleApp/backups"
BACKUP_DIR="/malonja/kisomo/storage/backups"
TIMESTAMP=$(date +"%Y-%m-%d_%H-%M-%S")
BACKUP_FILE="${BACKUP_DIR}/shulemis_backup_${TIMESTAMP}.sql.gz"
RETENTION_DAYS=7  # Number of days to keep local backups

mkdir -p $BACKUP_DIR

# Dump the database
mysqldump -u $DB_USER -p$DB_PASSWORD $DB_NAME | gzip > $BACKUP_FILE

# Check if the dump was successful
if [ $? -eq 0 ]; then
    echo "Database backup created successfully: $BACKUP_FILE"
else
    echo "Database backup failed!"
    exit 1
fi

# Upload to S3
aws s3 cp $BACKUP_FILE $S3_BUCKET

if [ $? -eq 0 ]; then
    echo "Backup uploaded to AWS S3 successfully."
else
    echo "Failed to upload backup to S3!"
    exit 1
fi

# Delete old backups locally
find $BACKUP_DIR -type f -name "shulemis_backup_*.sql.gz" -mtime +$RETENTION_DAYS -exec rm {} \;
echo "Old backups deleted."

echo "Backup process completed successfully!"
