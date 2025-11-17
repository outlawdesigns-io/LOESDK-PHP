<?php namespace LOE;

require_once __DIR__ . '/FsScanner.php';
require_once __DIR__ . '/../../Libs/MessageClient/MessageClient.php';
require_once __DIR__ . '/../../Factory.php';

class FsHealthScanner extends FsScanner{

  const MSGSUBJ = "Library of Everything File System Check";

  public $missing = array();
  public $files = array();
  public $msgResponse;
  protected $_msgTo;
  protected $_model;

  public function __construct($model,$msgTo = null,$msgApiUrl = null,$accessToken = null){
    $this->_model = $model;
    $this->_scanForever(\LOE\Base::WEBROOT . $this->_model->fsRoot)
         ->_verifyDatabase();
    if(!is_null($msgTo) && !is_null($msgApiUrl) && !is_null($accessToken)){
      $this->_msgTo = $msgTo;
      try{
        $msgClient = new \MessageClient($msgApiUrl,$accessToken);
        $this->msgResponse = $msgClient->send($this->_buildMessage());
      }catch(\Exception $e){
        throw new \Exception($e->getMessage());
      }
    }
  }
  protected function _buildMessageName(){
    return 'LOE_' . strtoupper($this->_model->label) . '_FS_CHECK';
  }
  protected function _interpretFile($absolutePath){
    $fileInfo = pathinfo($absolutePath);
    if(in_array(strtolower($fileInfo['extension']),$this->_model->fileExtensions)){
      $this->files[] = $absolutePath;
    }
    return $this;
  }
  protected function _verifyDatabase(){
    $className = $this->_model->namespace . $this->_model->label;
    foreach($this->files as $file){
      if(!$className::recordExists($file)){
        $this->missing[] = preg_replace("/'/","",$file);
      }
    }
    return $this;
  }
  protected function _calculateHealth(){
    return ((count($this->files) - count($this->missing)) / count($this->files)) * 100;
  }
  protected function _buildMessage(){
    return array(
      "to"=>array($this->_msgTo),
      "subject"=>self::MSGSUBJ . ": " . $this->_model->fsRoot . " " . round($this->_calculateHealth(),2) . "%",
      "msg_name"=>$this->_buildMessageName(),
      "body"=>$this->_fillMessageBody(),
      "flag"=>date('Y-m-d'),
      "sent_by"=>"LOE3:" . __FILE__
    );
  }
  protected function _fillMessageBody(){
    $str = "A file system consitency test has been completed for: ";
    $str .= $this->_model->fsRoot . "<br>";
    if(count($this->missing)){
      sort($this->missing);
      $str .= "The following files cannot be accounted for:<br>";
      $str .= "<pre>" . print_r($this->missing,true) . "</pre>";
    }else{
      $str .= "Congratulations! " . $this->_model->fsRoot . " is at 100% health!";
    }
    return $str;
  }
}
