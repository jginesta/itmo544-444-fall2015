
<?php
// Start the session
session_start();
// In PHP versions earlier than 4.1.0, $HTTP_POST_FILES should be used instead
// of $_FILES.


require 'vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Sns\SnsClient;

$_SESSION['upload'] = 1;
echo $_POST['email'];

$uploaddir = '/tmp/';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

echo '<pre>';
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.\n";
} else {
    echo "Possible file upload attack!\n";
}

echo 'Here is some more debugging info:';
print_r($_FILES);
print "</pre>";


$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);


$bucket = uniqid("php-jgl-mpfinal",false);

# AWS PHP SDK version 3 create bucket
$result = $s3->createBucket([
    'ACL' => 'public-read',
    'Bucket' => $bucket
]);

$s3->waitUntil('BucketExists', array( 'Bucket'=> $bucket));

# PHP version 3
$result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
    'Key' => "Hello".$uploadfile,
    'ContentType' => $_FILES['userfile']['tmp_name'],
    'Body'   => fopen($uploadfile, 'r+')
]);  


$url = $result['ObjectURL'];
echo $url;


$result = $s3->getObject(array(
    'Bucket' => $bucket,
    'Key' => "Hello".$uploadfile,
    'ContentType' => $_FILES['userfile']['tmp_name'],
    'SaveAs' => 'result/s3result.jpg'
));

//shell_exec("chmod 777 s3result.jpg");
//shell_exec("chmod 777 imagick.sh");
chmod("result/s3result.jpg",0777);
chmod("imagick.sh",0777);
$install=shell_exec('./imagick.sh');
print_r($install);
$image= new Imagick(glob('result/s3result.jpg'));
$image-> thumbnailImage(100,0);
$image->setImageFormat ("jpg");
$image-> writeImages('imagesResult/s3rendered.jpg',true);

# Upload rendered image
$resultrendered = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
    'Key' => "Hello".$uploadfile." rendered",
    'SourceFile' => "imagesResult/s3rendered.jpg",
    'ContentType' => $_FILES['userfile']['tmp_name'],
    'Body'   => fopen("imagesResult/s3rendered.jpg", 'r+')
]);  

unlink('imagesResult/s3rendered.jpg');
$urlren = $resultrendered['ObjectURL'];
echo $urlren;

$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);

$result = $rds->describeDBInstances(['DBInstanceIdentifier' => 'mp1-jgl',]);

$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
 print "============\n". $endpoint . "================";

# Database connection
$link = mysqli_connect($endpoint,"controller","letmein888","customerrecords",3306) or die("Error " . mysqli_error($link));

# Check database connection 
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
echo "Connection to database correct ";

# Inserting data int the database
/* Prepared statement, stage 1: prepare */
if (!($stmt = $link->prepare("INSERT INTO jgldata (ID, email,phone,filename,s3rawurl,s3finishedurl,state,date) VALUES (NULL,?,?,?,?,?,?,?)"))) {
    echo "Prepare failed: (" . $link->errno . ") " . $link->error;
}

$email = $_POST['email'];
$phone = $_POST['phone'];
$s3rawurl = $url; 
$filename = basename($_FILES['userfile']['name']);
$s3finishedurl = $urlren;
$status =0;
$date='2015-05-30 10:09:00';


$stmt->bind_param("sssssii",$email,$phone,$filename,$s3rawurl,$s3finishedurl,$state,$date);

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

printf("%d Row inserted.\n", $stmt->affected_rows);

/* explicit close recommended */
$stmt->close();

$link->real_query("SELECT * FROM jgldata");
$res = $link->use_result();

echo "Result set order...\n";
while ($row = $res->fetch_assoc()) {
    echo $row['ID'] . " " . $row['email']. " " . $row['phone'];
}

$sns = new Aws\Sns\SnsClient(array(
'version' => 'latest',
'region' => 'us-east-1'
));

$ArnArray = $sns->createTopic([
'Name' => 'mp2-jgl-pict',
]);
$Arn= $ArnArray['TopicArn'];

$result = $sns->publish(array(
    'TopicArn' => $Arn,
   
    // Message is required
    'Message' => 'Image uploaded successfully',
    'Subject' => 'Image upload',

        // ... repeated
    
));
echo "\r\n";	
echo "Successfull publish to user";

$link->close();
header ('Location: gallery.php');
//add code to detect if subscribed to SNS topic 
//if not subscribed then subscribe the user and UPDATE the column in the database with a new value 0 to 1 so that then each time you don't have to resubscribe them

// add code to generate SQS Message with a value of the ID returned from the most recent inserted piece of work
//  Add code to update database to UPDATE status column to 1 (in progress)
?>
