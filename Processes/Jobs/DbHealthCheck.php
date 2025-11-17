<?php

require_once __DIR__ . '/../../Factory.php';

$authApi = getenv('AUTH_TOKEN_URI');
$clientId = getenv('AUTH_CLIENT_ID');
$clientSecret = getenv('AUTH_CLIENT_SECRET');
$scope = getenv('AUTH_SCOPE');
$msgServiceUrl = getenv('MSGSERVICE_API_URI');
$audience = $msgServiceUrl;


if(!$authApi || !$clientId || !$clientSecret || !$scope || !$msgServiceUrl){
  echo "Missing Authentication Details. Ensure the presence of:\n";
  echo "AUTH_TOKEN_URI\nAUTH_CLIENT_ID\nAUTH_CLIENT_SECRET\nAUTH_SCOPE\nMSGSERVICE_API_URI\n";
  exit;
}
if(!isset($argv[1])){
  echo "Must provide message recipient\n";
  exit;
}else{
  $msgTo = $argv[1];
  $models = \LOE\Model::getAll();
}
try{
  $tokenResponse = \LOE\Factory::authenticate($authApi,$clientId, $clientSecret, $scope, $audience);
  $accessToken = $tokenResponse->access_token;
}catch(\Exception $e){
  echo $e->getMessage() . "\n";
  exit;
}
foreach($models as $model){
  $startTime = microtime(true);
  $run = \LOE\Factory::createModel('DbCheck');
  $run->startTime = date("Y-m-d H:i:s");
  $run->modelId = $model->UID;
  try{
    $scanner = \LOE\Factory::createDbScanner($model,$msgTo,$msgServiceUrl,$accessToken);
  }catch(\Exception $e){
    echo $e->getMessage() . "\n";
  }
  $endTime = microtime(true);
  $executionSeconds = $endTime - $startTime;
  $run->endTime = date("Y-m-d H:i:s");
  $run->runTime = $executionSeconds;
  $run->recordCount = $scanner->recordCount;
  $run->missingCount = count($scanner->missing);
  $run->create();
}
