<?php
session_start();
require 'vendor/autoload.php';
use Aws\Sns\SnsClient;

//Create topic and subscribe to it
$email=$_POST['email'];

$sns = new Aws\Sns\SnsClient(array(
'version' => 'latest',
'region' => 'us-east-1'
));

$ArnArray = $sns->createTopic([
'Name' => 'mp2-jgl-pict',
]);

$Arn= $ArnArray['TopicArn'];

$settopicAttributes = $sns->setTopicAttributes(array(
    'TopicArn' => "$Arn",
    'AttributeName'=>'DisplayName',
    'AttributeValue'=>'mp2-jgl-pict',
));

$topicAttributes = $sns->getTopicAttributes(array(
    'TopicArn' => "$Arn",
    'AttributeName'=>'DisplayName',
    'AttributeValue'=>'mp2-jgl-pict',
));

$listSubscriptions = $sns->listSubscriptionsByTopic(array(
    // TopicArn is required
    'TopicArn' => $Arn,
));

$subscribe = $sns->subscribe(array(
    'TopicArn' => $Arn,
    'Protocol' => 'email',
    'Endpoint' => $email,
      ));
?>

<html>
<body style="background-color:LemonChiffon">
<center> 
<h1 style=> Subscription confirmed</h1>
<h2> 
		<?php
		echo $email;
		?>
</h2>
<h3>           <?php echo "\r\n";
                echo "You will receive an email you must confirm";
	  	                
		?>
</h3><a href="/index.php">Index page</a></center>

</body>
</html> 