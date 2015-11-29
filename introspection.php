
<?php
session_start();
require 'vendor/autoload.php';

# Creating a client for the s3 bucket
use Aws\Rds\RdsClient;
$client = new Aws\Rds\RdsClient([
 'version' => 'latest',
 'region'  => 'us-east-1'
]);

$result = $client->describeDBInstances([
    'DBInstanceIdentifier' => 'mp1-jgl',
]);

$endpoint = "";
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];

# Connecting to the database
$link = mysqli_connect($endpoint,"controller","letmein888","customerrecords") or die("Error " . mysqli_error($link));

/* Checking the database connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$uploaddir = '/tmp/';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

$backup = uniqid("dbname",false);

$backuptot=$uploaddir.$backup. '.' . 'sql';

//echo $backuptot;

$dump="mysqldump --user=controller --password=letmein888 --host=$endpoint customerrecords > $backuptot";

exec($dump);

$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);


$bucket = uniqid("backup",false);

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
    'Key' => $backuptot,
    'SourceFile' => $backuptot,
   // 'ContentType' => $_FILES['userfile']['tmp_name'],
    //'Body'   => fopen($uploadfile, 'r+')
]);  

$url = $result['ObjectURL'];
echo $url;

function readOnly(){
shell_exec("read-replica.sh");
$_SESSION['read'] = true;
	
}

?>


<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<title>Gallery</title>
	<link href='http://fonts.googleapis.com/css?family=Slabo+27px' rel='stylesheet' type='text/css'>
	<link href='../dist/simplelightbox.min.css' rel='stylesheet' type='text/css'>
	<link href='demo.css' rel='stylesheet' type='text/css'>
</head>
<body>
	<div class="container">
		<h1 class="align-center">Jessicas Introspection</h1>
		<h2 class="align-center"> 
		<input type='button' name='ReadOnly' onclick=readOnly() value='ReadOnly'>;							
		<?php
		$url = $result['ObjectURL'];
		echo "The url for your backup is: ".$url;
		?>		
		</div>
		<br><br>
			
	
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src="../dist/simple-lightbox.js"></script>
<script>
	$(function(){
		var gallery = $('.gallery a').simpleLightbox();
	});
</script>
</body>
</html>	
