<?php namespace LOE;

require_once __DIR__ . '/../../Factory.php';
require_once __DIR__ . '/../../Libs/WebAccessClient/WebAccessClient.php';

class PlayHistory{

  const SPACEPATT = "/%20/";
  const REQEND = 'request';
  const REQKEY = 'query';
  const TARGETFIELD = 'file_path';

  protected $_webClient;
  protected $_model;
  protected $_limitDate;
  public $searchResults = array();
  public $exceptions = array();
  public $processedCount;
  public static $responseCodes = array(202,206,304);

  public function __construct($model,$limitDate,$logApiUrl,$accessToken){
    $this->_limitDate = date($limitDate);
    $this->_model = $model;
    $this->processedCount = 0;
    $this->_webClient = new \WebAccessClient($logApiUrl,$accessToken);
    $this->_updatePlayHistory();
  }
  protected function _buildPath($query){
    return Base::WEBROOT . "/LOE" . preg_replace(self::SPACEPATT," ",$query);
  }
  protected function _updatePlayHistory(){
    $playedClass = $this->_model->namespace . 'Played';
    foreach($this->_model->fileExtensions as $extension){
      //We are cheating the _webClient by injecting into search's $value parameter
      $this->searchResults = $this->_webClient->search(self::REQEND,self::REQKEY,'.' . $extension . '/' . $this->_limitDate);
      foreach($this->searchResults as $reqObj){
        $model = Factory::search($this->_model->label,self::TARGETFIELD,$this->_buildPath($reqObj->query));
        if(!count($model)){
          $this->exceptions[] = $this->_buildPath($reqObj->query);
          continue;
        }else{
          $model = $model[0];
        }
        if(!$playedClass::recordExists($model->UID,$reqObj->requestDate) && in_array($reqObj->responseCode,self::$responseCodes)){
          $modelId = strtolower($this->_model->label) . 'Id';
          $played = Factory::createModel($playedClass::TABLE);
          $played->$modelId = $model->UID;
          $played->playDate = $reqObj->requestDate;
          $played->ipAddress = $reqObj->ip_address;
          $played->create();
          $this->processedCount++;
        }
      }
    }
    return $this;
  }
}
