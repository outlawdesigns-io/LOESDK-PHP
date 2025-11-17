<?php

require_once __DIR__ . '/../../Factory.php';

$models = \LOE\Model::getAll();
$authApi = getenv('AUTH_TOKEN_URI');
$webAccessApi = getenv('WEBACCESS_API_URI');
$clientId = getenv('AUTH_CLIENT_ID');
$clientSecret = getenv('AUTH_CLIENT_SECRET');
$scope = getenv('AUTH_SCOPE');
$audience = $webAccessApi;

try{
  $tokenResponse = \LOE\Factory::authenticate($authApi,$clientId, $clientSecret, $scope, $audience);
  $accessToken = $tokenResponse->access_token;
}catch(Exception $ex){
  echo $ex->getMessage() . "\n";
}

foreach($models as $model){
  $startTime = microtime(true);
  $run = \LOE\Factory::createModel('PlayHistoryRun');
  $run->modelId = $model->UID;
  $run->startTime = date("Y-m-d H:i:s");
  try{
    $processor = \LOE\Factory::updatePlayHistory($model,'Y-m-01',$webAccessApi,$accessToken);
    $run->searchResultCount = count($processor->searchResults);
    $run->exceptionCount = count($processor->exceptions);
    $run->processedCount = $processor->processedCount;
    if(count($processor->exceptions)){
      print_r($processor->exceptions);
    }
  }catch(\Exception $e){
    $run->exceptionCaught = 1;
    $run->exceptionMessage = $e->getMessage();
  }
  $endTime = microtime(true);
  $executionSeconds = $endTime - $startTime;
  $run->endTime = date("Y-m-d H:i:s");
  $run->runTime = $executionSeconds;
  $run->create();
}
