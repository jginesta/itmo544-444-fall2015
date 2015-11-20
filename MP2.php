<?php

require 'vendor/autoload.php';

use Aws\Rds\RdsClient;

$sns = SnsClient::factory(array(
'version' => 'latest',
'region' => 'us-east-1'
));
$Arn = $sns->createTopic([
'Name' => 'MP2-SNS-test',
]);


echo $Arn;



?>
