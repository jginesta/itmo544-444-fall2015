#!bin/bash

#Creating a read replica
aws rds create-db-instance-read-replica --db-instance-identifier mp1-jgl-read --source-db-instance-identifier mp1-jgl --publicly-accessible
sudo aws rds wait db-instance-available --db-instance-identifier mp1-jgl-read

echo "read replica created"