
<?php
// Start the session
session_start();
// In PHP versions earlier than 4.1.0, $HTTP_POST_FILES should be used instead
// of $_FILES.

require 'vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Sns\SnsClient;

#$email = 'jginesta@hawk.iit.edu';
$match= 0;

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


$bucket = uniqid("php-jgl-",false);

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
$s3finishedurl = "none";
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


$sns = SnsClient::factory(array(
'version' => 'latest',
'region' => 'us-east-1'
));

$ArnArray = $sns->createTopic([
'Name' => 'mp2-jgl',
]);

$Arn= $ArnArray['TopicArn'];
echo "\r\n";
echo "This is the Arn for the picture upload topic: $Arn";

$settopicAttributes = $sns->setTopicAttributes(array(
    'TopicArn' => "$Arn",
    'AttributeName'=>'DisplayName',
    'AttributeValue'=>'mp2-jgl',
));
echo "\r\n";
##echo "## $settopicAttributes";

$topicAttributes = $sns->getTopicAttributes(array(
    'TopicArn' => "$Arn",
    'AttributeName'=>'DisplayName',
    'AttributeValue'=>'mp2-jgl',
));
echo "\r\n";
##echo "########$topicAttributes";

echo "\r\n";
echo "Subscriptions pending: {$topicAttributes['Attributes']['SubscriptionsPending']}";
echo "\r\n";
echo "Subscriptions confirmed: {$topicAttributes['Attributes']['SubscriptionsConfirmed']} ";

$listSubscriptions = $sns->listSubscriptionsByTopic(array(
    // TopicArn is required
    'TopicArn' => "$Arn",
));


for ($i=0; $i<sizeOf($listSubscriptions['Subscriptions']); $i++) {
    $endpointsubscriptions=$listSubscriptions['Subscriptions'][$i]['Endpoint'];
   # $allendpoint=array($endpointsubscriptions);
    $allendpoint[] = $endpointsubscriptions;
    #echo "\r\n";
    ##echo "The users subscribed to this topic are $allendpoint[0]";
    echo "\r\n";
    #echo "The users subscribed to this topic are $allendpoint[1]";
    #echo "\r\n";
    echo "This is the user email $email";
    if(sizeOf($endpointsubscriptions)==null){
	$subscribe = $sns->subscribe(array(
          'TopicArn' => "$Arn",
          'Protocol' => 'email',
          'Endpoint' => "$email",
           ));
    
            echo "\r\n";
            echo "First user subscribed correctly {$subscribe['SubscriptionArn']}";
	    $match=2;
	}
    else{
	for($i=0;$i<sizeOf($allendpoint);$i++)
        {
   		if($email==$allendpoint[$i]){
   			$match=1;  
   		}
    
   
 	}

	#	echo "\r\n";
	#echo "User exists $match";
     
   		if($match==1)
       		{
           		#echo "\r\n";
           		#echo "User already subscribed to topic";
	   		$match=2;
	   
       		}
   		if($match==0){
	   		$subscribe = $sns->subscribe(array(
          		'TopicArn' => "$Arn",
          		'Protocol' => 'email',
         		 'Endpoint' => "$email",
           	));
    
            	echo "\r\n";
            	echo "User subscribed correctly with status {$subscribe['SubscriptionArn']}";
	    	$match=2;
	    	echo "\r\n";
	   # break;
       		}
    }
}


$link->close();
header ('Location: gallery.php');
//add code to detect if subscribed to SNS topic 
//if not subscribed then subscribe the user and UPDATE the column in the database with a new value 0 to 1 so that then each time you don't have to resubscribe them

// add code to generate SQS Message with a value of the ID returned from the most recent inserted piece of work
//  Add code to update database to UPDATE status column to 1 (in progress)
?>