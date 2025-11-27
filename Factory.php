<?php namespace LOE;

require_once __DIR__ . '/Models/Model.php';
require_once __DIR__ . '/Models/Share.php';
require_once __DIR__ . '/Models/DbCheck.php';
require_once __DIR__ . '/Models/FsCheck.php';
require_once __DIR__ . '/Models/PlayCountRun.php';
require_once __DIR__ . '/Models/PlayHistoryRun.php';
require_once __DIR__ . '/Models/ModelStorage.php';
require_once __DIR__ . '/Models/Movie/Movie.php';
require_once __DIR__ . '/Models/Movie/Played.php';
require_once __DIR__ . '/Models/Movie/Rating.php';
require_once __DIR__ . '/Models/Tv/Episode.php';
require_once __DIR__ . '/Models/Tv/Played.php';
require_once __DIR__ . '/Models/Tv/Rating.php';
require_once __DIR__ . '/Models/Music/Song.php';
require_once __DIR__ . '/Models/Music/Played.php';
require_once __DIR__ . '/Models/Music/Rating.php';
require_once __DIR__ . '/Models/Music/PlayList.php';
require_once __DIR__ . '/Models/Music/RandomPlayList.php';
require_once __DIR__ . '/Models/Music/Script.php';
require_once __DIR__ . '/Models/Doc/Doc.php';
require_once __DIR__ . '/Models/Doc/Rating.php';
require_once __DIR__ . '/Models/Doc/Played.php';
require_once __DIR__ . '/Models/Anime/Anime.php';
require_once __DIR__ . '/Models/Anime/Played.php';
require_once __DIR__ . '/Models/Anime/Rating.php';
require_once __DIR__ . '/Models/Comic/Comic.php';
require_once __DIR__ . '/Models/Comic/Rating.php';
require_once __DIR__ . '/Models/Comic/Played.php';
require_once __DIR__ . '/Processes/Scanners/DbHealthScanner.php';
require_once __DIR__ . '/Processes/Scanners/FsHealthScanner.php';
require_once __DIR__ . '/Processes/Scanners/Movie/HoldingBayScanner.php';
require_once __DIR__ . '/Processes/Scanners/Music/HoldingBayScanner.php';
require_once __DIR__ . '/Processes/Scanners/Tv/HoldingBayScanner.php';
require_once __DIR__ . '/Processes/Scanners/Doc/HoldingBayScanner.php';
require_once __DIR__ . '/Processes/Scanners/Comic/HoldingBayScanner.php';
require_once __DIR__ . '/Processes/Processors/PlayCount.php';
require_once __DIR__ . '/Processes/Processors/PlayHistory.php';
require_once __DIR__ . '/Processes/Processors/ModelStorageUpdate.php';
require_once __DIR__ . '/Processes/Processors/Music/HoldingBayProcessor.php';
require_once __DIR__ . '/Processes/Processors/Music/HoldingBayCleaner.php';
require_once __DIR__ . '/Processes/Processors/Movie/HoldingBayProcessor.php';
require_once __DIR__ . '/Processes/Processors/Tv/HoldingBayProcessor.php';
require_once __DIR__ . '/Processes/Processors/Comic/HoldingBayProcessor.php';
require_once __DIR__ . '/Processes/Processors/Doc/AutoInsert.php';
require_once __DIR__ . '/Processes/Processors/HoldingBay/ArchiveExtractor.php';
require_once __DIR__ . '/Processes/Processors/Music/HoldingBayAutoProcessor.php';


class Factory{

  const BADOBJ = 'Invalid Object Type';

  public static function createModel($type,$UID = null){
    switch(ucwords($type)){
      case \LOE\Movie\Movie::TABLE:
        $obj = new \LOE\Movie\Movie($UID);
        break;
      case \LOE\Movie\Played::TABLE:
        $obj = new \LOE\Movie\Played($UID);
        break;
      case \LOE\Movie\Rating::TABLE:
        $obj = new \LOE\Movie\Rating($UID);
        break;
      case \LOE\Music\Song::TABLE:
        $obj = new \LOE\Music\Song($UID);
        break;
      case \LOE\Music\Played::TABLE:
        $obj = new \LOE\Music\Played($UID);
        break;
      case \LOE\Music\Rating::TABLE:
        $obj = new \LOE\Music\Rating($UID);
        break;
      case \LOE\Music\PlayList::TABLE:
        $obj = new \LOE\Music\PlayList($UID);
        break;
      case \LOE\Music\Script::TABLE:
      $obj = new \LOE\Music\Script($UID);
        break;
      case \LOE\Tv\Episode::TABLE:
        $obj = new \LOE\Tv\Episode($UID);
        break;
      case \LOE\Tv\Played::TABLE:
        $obj = new \LOE\Tv\Played($UID);
        break;
      case \LOE\Tv\Rating::TABLE:
        $obj = new \LOE\Tv\Rating($UID);
        break;
      case \LOE\Anime\Anime::TABLE:
        $obj = new \LOE\Anime\Anime($UID);
        break;
      case \LOE\Anime\Played::TABLE:
        $obj = new \LOE\Anime\Played($UID);
        break;
      case \LOE\Anime\Rating::TABLE:
        $obj = new \LOE\Anime\Rating($UID);
        break;
      case \LOE\Doc\Doc::TABLE:
        $obj = new \LOE\Doc\Doc($UID);
        break;
      case \LOE\Doc\Rating::TABLE:
        $obj = new \LOE\Doc\Rating($UID);
        break;
      case \LOE\Comic\Comic::TABLE:
        $obj = new \LOE\Comic\Comic($UID);
        break;
      case \LOE\Comic\Rating::TABLE:
        $obj = new \LOE\Comic\Rating($UID);
        break;
      case \LOE\Model::TABLE:
        $obj = new \LOE\Model($UID);
        break;
      case \LOE\FsCheck::TABLE:
        $obj = new \LOE\FsCheck($UID);
        break;
      case \LOE\DbCheck::TABLE:
        $obj = new \LOE\DbCheck($UID);
        break;
      case \LOE\PlayCountRun::TABLE:
        $obj = new \LOE\PlayCountRun($UID);
        break;
      case \LOE\PlayHistoryRun::TABLE:
        $obj = new \LOE\PlayHistoryRun($UID);
        break;
      case \LOE\ModelStorage::TABLE:
        $obj = new \LOE\ModelStorage($UID);
        break;
      default:
        throw new \Exception(self::BADOBJ);
      }
      return $obj;
    }
    public static function createHoldingBayScanner($model){
      $obj = null;
      $className = $model->namespace . "HoldingBayScanner";
      return new $className($model);
    }
    public static function createFsScanner($model,$msgTo = null,$msgApiUrl = null,$accessToken = null){
      return new \LOE\FsHealthScanner($model, $msgTo, $msgApiUrl, $accessToken);
    }
    public static function createDbScanner($model,$msgTo = null,$msgApiUrl = null,$accessToken = null){
      return new \LOE\DbHealthScanner($model, $msgTo, $msgApiUrl, $accessToken);
    }
    public static function createHoldingBayProcessor($type,$inputObj){
        $obj = null;
        switch(ucwords($type)){
            case \LOE\Movie\Movie::TABLE:
                $obj = new \LOE\Movie\HoldingBayProcessor($inputObj);
                break;
            case \LOE\Tv\Episode::TABLE:
                $obj = new \LOE\Tv\HoldingBayProcessor($inputObj);
                break;
            case \LOE\Music\Song::TABLE:
                $obj = new \LOE\Music\HoldingBayProcessor($inputObj);
                break;
            case \LOE\Comic\Comic::TABLE:
                $obj = new \LOE\Comic\HoldingBayProcessor($inputObj);
                break;
            default:
                throw new \Exception(self::BADOBJ);
        }
        return $obj;
    }
   public static function createHoldingBayCleaner($type){
     $obj = null;
     switch(ucwords($type)){
       case \LOE\Music\Song::TABLE:
         $obj = new \LOE\Music\HoldingBayCleaner();
       break;
       default:
         throw new \Exception(self::BADOBJ);
     }
     return $obj;
   }
   public static function browse($table,$key){
     $model = self::createModel($table);
     return $model::browse($key);
   }
   public static function search($table,$key,$value, $limit = null, $offset = null){
     $model = self::createModel($table);
     return $model::search($key,$value,$limit,$offset);
   }
   public static function updatePlayCounts($model,$logApiUrl,$accessToken){
     return new PlayCount($model,$logApiUrl,$accessToken);
   }
   public static function updatePlayHistory($model,$limitDate,$logApiUrl,$accessToken){
     return new PlayHistory($model,$limitDate,$logApiUrl,$accessToken);
   }
   public static function updateModelStorage($model){
     return new ModelStorageUpdate($model);
   }
   public static function autoInsert($type){
     switch(ucwords($type)){
       case \LOE\Doc\Doc::TABLE:
         $obj = new \LOE\Doc\AutoInsert();
       break;
       default:
         throw new \Exception(self::BADOBJ);
     }
     return $obj;
   }
   public static function authenticate($authApiTokenUrl,$clientId, $secret, $scope = null, $audience = null){
     $postData = array(
       "grant_type" => "client_credentials",
       "client_id" => $clientId,
       "client_secret" => $secret,
     );
     if($scope !== null){
       $postData['scope'] = $scope;
     }
     $postStr = http_build_query($postData);
     if($audience !== null){
       if(is_array($audience)){
         foreach($audience as $aud){
           $postStr .= "&audience=" . urlencode($aud);
         }
       }else{
         $postStr .= "&audience=" . urlencode($audience);
       }
     }
     $ch = curl_init();
     curl_setopt($ch,CURLOPT_URL,$authApiTokenUrl);
     curl_setopt($ch,CURLOPT_POST,1);
     curl_setopt($ch,CURLOPT_POSTFIELDS,$postStr);
     curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
     $output = curl_exec($ch);
     if(curl_errno($ch)){
       throw new \Exception(curl_error($ch));
     }
     $json = json_decode($output);
     curl_close($ch);
     if(isset($json->error)){
       throw new \Exception($json->error);
     }
     return $json;
   }
   public static function extractArchives($rootDir){
     return new \LOE\HoldingBay\ArchiveExtractor($rootDir);
   }
   public static function count($table){
     $model = self::createModel($table);
     return $model::count();
   }
   public static function countOf($table,$key){
     $model = self::createModel($table);
     return $model::countOf($key);
   }
   public static function recent($table,$limit){
     $model = self::createModel($table);
     return $model::recent($limit);
   }
   public static function createRandomPlayList($type,$genre,$limit){
     $obj = null;
     switch(ucwords($type)){
       case \LOE\Music\Song::TABLE:
         $obj = new \LOE\Music\RandomPlayList($genre,$limit);
       break;
       default:
         throw new \Exception(self::BADOBJ);
     }
     return $obj;
   }
   public static function getModel($label){
     return Model::getByLabel($label);
   }
   public static function createShare($userId,$modelId,$objectId){
     return Share::buildShare($userId,$modelId,$objectId);
   }
}
