<?php
session_start();
require 'vendor/autoload.php';
use Aws\Sns\SnsClient;

$email='jginesta@hawk.iit.edu';
//$email=$_POST['email'];
$match= 0;

$sns = new Aws\Sns\SnsClient(array(
'version' => 'latest',
'region' => 'us-east-1'
));

$ArnArray = $sns->createTopic([
'Name' => 'mp2-jgl-pict',
]);

$Arn= $ArnArray['TopicArn'];
//echo "\r\n";
//echo "This is the Arn for the picture upload topic: $Arn";

$settopicAttributes = $sns->setTopicAttributes(array(
    'TopicArn' => "$Arn",
    'AttributeName'=>'DisplayName',
    'AttributeValue'=>'mp2-jgl-pict',
));
//echo "\r\n";

$topicAttributes = $sns->getTopicAttributes(array(
    'TopicArn' => "$Arn",
    'AttributeName'=>'DisplayName',
    'AttributeValue'=>'mp2-jgl-pict',
));
//echo "\r\n";
//echo "Subscriptions pending: {$topicAttributes['Attributes']['SubscriptionsPending']}";
//echo "\r\n";
//echo "Subscriptions confirmed: {$topicAttributes['Attributes']['SubscriptionsConfirmed']} ";


//$listTopics = $sns->listTopics(array(
//));
//$listTopicsARN=$listTopics['Topics'][0]['TopicArn'];
//echo "\r\n";
//echo "List topics: $listTopicsARN";

$listSubscriptions = $sns->listSubscriptionsByTopic(array(
    // TopicArn is required
    'TopicArn' => $Arn,
));

if(sizeOf($listSubscriptions['Subscriptions'])==0){
   
    
	$subscribe = $sns->subscribe(array(
          'TopicArn' => $Arn,
          'Protocol' => 'email',
          'Endpoint' => $email,
           ));
    
            echo "\r\n";
            echo "First user subscribed correctly {$subscribe['SubscriptionArn']}";
	    $match=2;
	    echo "\r\n";
            echo "You will receive an email you must confirm";
}
	
else{
	 for ($i=0; $i<sizeOf($listSubscriptions['Subscriptions']); $i++) {
         $endpointsubscriptions=$listSubscriptions['Subscriptions'][$i]['Endpoint'];
        $allendpoint[] = $endpointsubscriptions;
	for($i=0;$i<sizeOf($allendpoint);$i++)
        {
   		if($email==$allendpoint[$i]){
   			$match=1;  
   		}
    
   
 	}

		if($match==1)
       		{
           		$match=2;
			echo "User subscribed";
	   
       		}
   		if($match==0){
	   		$subscribe = $sns->subscribe(array(
          		'TopicArn' => $Arn,
          		'Protocol' => 'email',
         		 'Endpoint' => $email,
           	));
    
            	$match=2;
	    	}
 	
    }

}

?>
<html>
<body style="background-color:LemonChiffon">
<center> 
<h1 style="color:LightSkyBlue"> User subscribed correctly</h1>
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