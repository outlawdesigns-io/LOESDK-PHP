<?php namespace LOE;

require_once __DIR__ . '/../../Factory.php';
// require_once __DIR__ . '/../../Libs/WebAccessClient/WebAccessClient.php';
require_once __DIR__ . '/../../../WebAccessClient/WebAccessClient.php';

class PlayCount{

  const SPACEPATT = "/%20/";

  public $searchResultCount;
  public $exceptions = array();
  public $processedCount;
  protected $_webClient;
  protected $_model;
  protected $_modelCounts = array();

  public function __construct($model,$logApiUrl,$accessToken){
    $this->_model = $model;
    $this->processedCount = 0;
    $this->searchResultCount = 0;
    $this->_webClient = new \WebAccessClient($logApiUrl,$accessToken);
    $this->_getModels()->_updateCounts();
  }
  protected function _getModels(){
    foreach($this->_model->fileExtensions as $extension){
      $this->_modelCounts = array_merge($this->_modelCounts,$this->_webClient->getDocTypeCounts($extension));
    }
    $this->searchResultCount = count($this->_modelCounts);
    return $this;
  }
  protected function _updateCounts(){
    foreach($this->_modelCounts as $obj){
      $model = Factory::search($this->_model->label,'file_path',$this->_buildPath($obj->query));
      if(!count($model)){
        $this->exceptions[] = $this->_buildPath($obj->query);
        continue;
      }else{
        $model = $model[0];
      }
      $model->file_path = Base::WEBROOT . $model->file_path;
      $model->cover_path = Base::WEBROOT . $model->cover_path;
      $model->play_count = $obj->downloads;
      $model->update();
      $this->processedCount++;
    }
    return $this;
  }
  protected function _buildPath($query){
    //we've gone back and forth with use of WEBROOT.
    //removed because most records weren't matching.
    return "/LOE" . preg_replace(self::SPACEPATT," ",$query);
    //return Base::WEBROOT . "/LOE" . preg_replace(self::SPACEPATT," ",$query);
  }
}
