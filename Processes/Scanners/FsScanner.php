<?php namespace LOE;

abstract class FsScanner{

  abstract protected function _interpretFile($absolutePath);

  public static function isDirShortcut($relativePath){
    if($relativePath == '.' || $relativePath == '..'){
      return true;
    }
    return false;
  }
  public static function isDirEmpty($absolutePath){
    if(count(scandir($absolutePath)) == 2){
      return true;
    }
    return false;
  }
  public function cleanUp($absolutePath){
    return $this->_cleanUp($absolutePath);
  }
  protected function _scanForever($dir){
    $results = scandir($dir);
    foreach($results as $result){
      if(self::isDirShortcut($result)){
        continue;
      }else{
        $tester = $dir . "/" . $result;
      }
      if(is_file($tester)){
        $this->_interpretFile($tester);
      }elseif(is_dir($tester)){
        $this->_scanForever($tester);
      }else{
        continue;
      }
    }
    return $this;
  }
  protected function _cleanup($dir){
      if(is_dir($dir)){
          $results = scandir($dir);
          foreach($results as $result){
              if(!self::isDirShortcut($result)){
                  if(is_dir($dir . "/". $result)){
                      $this->_cleanup($dir . "/" . $result);
                  }elseif(!unlink($dir . "/" . $result)){
                      $error = error_get_last()['message'];
                      throw new \Exception($error);
                  }
              }
          }
      }
      if(!rmdir($dir)){
          $error = error_get_last()['message'];
          throw new \Exception($error);
      }
      return $this;
  }
  protected function _unlink($absolutePath){
      if(!unlink($absolutePath)){
          $error = error_get_last()['message'];
          throw new \Exception($error);
      }
      return $this;
  }
  //abstract protected function _recordExists($absolutePath);
  //abstract protected function _verifyDatabase();
}
