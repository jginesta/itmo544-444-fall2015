<html>
<head><title>Gallery</title>
</head>
<body>

<?php
session_start();
$email = $_POST["email"];
echo $email;
require 'vendor/autoload.php';

use Aws\Rds\RdsClient;
$client = RdsClient::factory(array(
#'profile' => '<profile in your aws credentials file>',
 'version' => 'latest',
 'region'  => 'us-east-1'
));

$result = $client->describeDBInstances([
    'DBInstanceIdentifier' => 'mp1-jgl',
]);

$endpoint = "";
#print_r (getPath('DBInstances'));
#print_r (getPath('DBInstances/*/Endpoint/Address'));
#print_r (getPath('DBInstances/*/Endpoint/Address'));
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];

#foreach ($endpoint as $ep) {
    // Do something with the message
 #   echo "============". $ep . "================";
  #  $endpoint = $ep;
#}   
#foreach ($result->getPath('DBInstances/*/Endpoint/Address') as $ep) {
 #   // Do something with the message
  #  echo "============". $ep . "================";
   # $endpoint = $ep;
#}   
//echo "begin database";
$link = mysqli_connect($endpoint,"controller","letmein888","customerrecords") or die("Error " . mysqli_error($link));

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

//below line is unsafe - $email is not checked for SQL injection -- don't do this in real life or use an ORM instead
#$link->real_query("SELECT * FROM jgldata WHERE email = '$email'");
$link->real_query("SELECT * FROM jgldata");
$res = $link->use_result();

echo "Result set order...\n";
while ($row = $res->fetch_assoc()) {
    echo "<img src =\" " . $row['s3rawurl'] . "\" /><img src =\"" .$row['s3finishedurl'] . "\"/>";
echo $row['ID'] . "Email: " . $row['email'];
}
$link->close();
?>
</body>
</html>
