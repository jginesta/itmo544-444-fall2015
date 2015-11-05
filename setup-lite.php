<?php
// Start the session
require 'vendor/autoload.php';
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);


// Create a table 
$result = $rds->describeDBInstances([
    'DBInstanceIdentifier' => 'mp1-jgl',
]);

$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
print "============\n". $endpoint . "================\n";
$link = mysqli_connect($endpoint,"controller","letmein888","customerrecords") or die("Error " . mysqli_error($link)); 

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

#print "Here is the result: " . $link;
#global $con;
$sql = "CREATE TABLE jgldata
(
ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
uname VARCHAR(20),
email VARCHAR(20),
phone VARCHAR(20),
filename VARCHAR(256),
s3rawurl VARCHAR(256),
s3finishedurl VARCHAR(256),
state TINYINT(3),
date TIMESTAMP 

)";

# DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
#if ($conn->query($sql) === TRUE) {
#    echo "Table MyGuests created successfully";
#} else {
 #   echo "Error creating table: " . $conn->error;
#}
#mysqli_close($conn);
$link->query($sql);
shell-exec("chmod 600 setup.php");
?>