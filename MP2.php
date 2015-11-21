<?php

require 'vendor/autoload.php';

use Aws\Sns\SnsClient;

$sns = SnsClient::factory(array(
'version' => 'latest',
'region' => 'us-east-1'
));
$ArnArray = $sns->createTopic([
'Name' => 'mp2-jgl',
]);

$Arn= $ArnArray['TopicArn'];
echo "\r\n";
echo "This is the Arn: $Arn";


$topicAttributes = $sns->getTopicAttributes(array(
    'TopicArn' => "$Arn",
    'AttributeName'=>'DisplayName',
    'AttributeValue'=>'mp2-jgl',
));
echo "\r\n";
echo "Subscriptions pending: {$topicAttributes['Attributes']['SubscriptionsPending']}";
echo "\r\n";
echo "Subscriptions confirmed: {$topicAttributes['Attributes']['SubscriptionsConfirmed']} ";

$listSubscriptions = $sns->listSubscriptionsByTopic(array(
    // TopicArn is required
    'TopicArn' => "$Arn",
));

$endpointsubscriptions=$listSubscriptions['Subscriptions'][0]['Endpoint'];

echo "\r\n";
echo "The users subscribed to this topic are $endpointsubscriptions";

if( in_array('jginesta@hawk.iit.edu', $endpointsubscriptions) ) {
    echo "User already subscribe to topic";
    }
    else{
    $subscribe = $sns->subscribe(array(
    'TopicArn' => "$Arn",
    'Protocol' => 'email',
    'Endpoint' => 'jginesta@hawk.iit.edu',
    ));
    echo "\r\n";
    echo "User subscribed with status {$subscribe['SubscriptionArn']}";

    }




?>
