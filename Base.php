<?php namespace LOE;

require_once __DIR__ . '/Libs/Record/Record.php';


class Base extends \Record{

    const NONASCIIPATT = '/[^\x00-\x7F]/';
    const BADFILEPATT = '/[\:"*?<>|]/';
    const PUNCTPATT = "/['!~`*^%$#@+,]/";
    const TRIDOT = '/[\.]{3}/';

    const DB = 'LOE';
    const PRIMARYKEY = 'UID';
    const FILEPATT = '/^.*(?=(\/LOE))/';
    const WEBROOT = '/var/www/html';
    // const WEBROOT = '/home/ldap/outlaw/LOE';
    const FILEUNSETERR = 'File path must be set.';

    public function __construct($database, $table, $primaryKey, $id)
    {
        parent::__construct($database, $table, $primaryKey, $id);
    }
    protected function _cleanFilePath($path){
        return html_entity_decode(preg_replace(self::FILEPATT,"",$path));
    }
    protected function _cleanProperties(){
        $reflection = new \ReflectionObject($this);
        $data = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach($data as $obj){
            if($obj->name != self::PRIMARYKEY){
              $key = $obj->name;
              if(!is_array($this->$key)){
                $this->$key = html_entity_decode($this->$key);
              }
            }
        }
        return $this;
    }
    public function cleanFilePath($path){
      return $this->_cleanFilePath($path);
    }
    public function verifyLocation(){
        if(!isset($this->file_path)){
            throw new \Exception(self::FILEUNSETERR);
        }
        $path = self::WEBROOT . $this->file_path;
        if(!is_file($path)){
            return false;
        }
        return true;
    }
    public function calculateSize(){
        if(!isset($this->file_path)){
            throw new \Exception(self::FILEUNSETERR);
        }
        if($this->verifyLocation()){
            $path = self::WEBROOT . $this->file_path;
            $fileSize = filesize($path);
            return $fileSize;
        }
        return false;
    }
    public static function buildCleanPath($absolutePath){
        $absolutePath = preg_replace(self::NONASCIIPATT,"",$absolutePath);
        $absolutePath = preg_replace(self::BADFILEPATT,"",$absolutePath);
        $absolutePath = preg_replace(self::PUNCTPATT,"",$absolutePath);
        $absolutePath = preg_replace(self::TRIDOT,"",$absolutePath);
        return $absolutePath;
    }
    public static function isCleanPath($absolutePath){
        if(preg_match(self::NONASCIIPATT,$absolutePath) || preg_match(self::BADFILEPATT,$absolutePath) || preg_match(self::PUNCTPATT,$absolutePath)){
          return false;
        }
        return true;
    }
    public static function recordExists($absolutePath){
      $GLOBALS['db']
        ->database(self::DB)
        ->table(static::TABLE)
        ->select(static::PRIMARYKEY)
        ->where("file_path","=","'" . preg_replace("/'/","",$absolutePath) . "'");
      try{
        $results = $GLOBALS['db']->get();
      }catch(\Exception $e){
        echo $e->getMessage() . "\n";
        echo $absolutePath . "\n";
        return false;
      }
      if(!mysqli_num_rows($results)){
        return false;
      }
      return true;
    }
    public static function search($key,$value){
      $data = array();
      $ids = parent::_search(self::DB,static::TABLE,static::PRIMARYKEY,$key,$value);
      foreach($ids as $id){
          $data[] = new static($id);
      }
      return $data;
    }
    public static function browse($key){
      return parent::_browse(self::DB,static::TABLE,$key);
    }
    public static function count(){
      return parent::_count(self::DB,static::TABLE);
    }
    public static function countOf($key){
      return parent::_countOf(self::DB,static::TABLE,$key);
    }
    public static function getAll(){
      $data = array();
      $ids = parent::_getAll(self::DB,static::TABLE,static::PRIMARYKEY);
      foreach($ids as $id){
          $data[] = new static($id);
      }
      return $data;
    }
    public static function recent($limit){
      $data = array();
      $ids = parent::_getRecent(self::DB,static::TABLE,static::PRIMARYKEY,$limit);
      foreach($ids as $id){
        $data[] = new static($id);
      }
      return $data;
    }
    public function backup(){
        //todo implement backup solution
    }
}
