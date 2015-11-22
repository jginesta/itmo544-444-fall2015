<?php
session_start();
require 'vendor/autoload.php';
use Aws\Sns\SnsClient;

$email = 'jginesta@hawk.iit.edu';

$sns = new Aws\Sns\SnsClient(array(
'version' => 'latest',
'region' => 'us-east-1'
));

$ArnArray = $sns->createTopic([
'Name' => 'mp2-jgl-pict',
]);

$Arn= $ArnArray['TopicArn'];
echo "\r\n";
echo "This is the Arn for the picture upload topic: $Arn";

$settopicAttributes = $sns->setTopicAttributes(array(
    'TopicArn' => "$Arn",
    'AttributeName'=>'DisplayName',
    'AttributeValue'=>'mp2-jgl-pict',
));
echo "\r\n";

$topicAttributes = $sns->getTopicAttributes(array(
    'TopicArn' => "$Arn",
    'AttributeName'=>'DisplayName',
    'AttributeValue'=>'mp2-jgl-pict',
));
echo "\r\n";
echo "Subscriptions pending: {$topicAttributes['Attributes']['SubscriptionsPending']}";
echo "\r\n";
echo "Subscriptions confirmed: {$topicAttributes['Attributes']['SubscriptionsConfirmed']} ";

$listSubscriptions = $sns->listSubscriptionsByTopic(array(
    // TopicArn is required
    'TopicArn' => $Arn,
));


$subscribe = $sns->subscribe(array(
    'TopicArn' => $Arn,
    'Protocol' => 'email',
    'Endpoint' => $email,
      ));
    
echo "You will receive an email you must confirm";
	  	


?>
