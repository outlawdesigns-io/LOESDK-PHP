<?php namespace LOE;

putenv('MYSQL_HOST=services');
putenv('MYSQL_USER=root');
putenv('MYSQL_PASSWORD=youwonder.');

require_once __DIR__ . '/../../Factory.php';

function generateComicInfo($comic){
  $xml = new \DOMDocument("1.0","UTF-8");
  $xml->formatOutput = true;
  $root = $xml->createElement('ComicInfo');
  $xml->appendChild($root);
  $fields = array(
    'Title' => $comic->issue_title,
    'Series' => $comic->series_title,
    'Number' => $comic->issue_number,
    'Volume' => $comic->series_start_year,
    'Year' => date('y',strtotime($comic->issue_cover_date)),
    'Month' => date('m',strtotime($comic->issue_cover_date)),
    'Publisher' => $comic->publisher,
    'Summary' => $comic->issue_description,
    'Writer' => null,
    'Penciller' => null,
    'Inker' => null,
    'Colorist' => null,
    'Letterer' => null,
  );
  foreach($fields as $name=>$value){
    if($value !== null && $value !== ''){
      $element = $xml->createElement($name);
      $element->appendChild($xml->createTextNode($value));
      $root->appendChild($element);
    }
  }
  return $xml->saveXML();
}

function writeCbzMetaData($path,$xml){
  $zip = new \ZipArchive();
  if($zip->open($path) !== true){
    throw new \RuntimeException("Unable to open: " . $path);
  }
  $zip->deleteName('ComicInfo.xml');
  $result = $zip->addFromString("ComicInfo.xml",$xml);
  $zip->close();
  return $result;
}

function writeCbrMetaData($path,$xml){
  $tmpPath = "/tmp/comicInfo.xml";
  file_put_contents($tmpPath,$xml);
  $cleanPath = escapeshellarg($path);
  $cmdStr = "rar a -ep " . $cleanPath . " " . $tmpPath;
  // $cmdStr = 'rar a "' . $path . ' "/tmp/ComicInfo.xml"';
  // echo $cmdStr . "\n";
  shell_exec($cmdStr);
}

// $comic = new Comic\Comic(1);
// $xml = generateComicInfo($comic);
// $absolutePath = Base::WEBROOT . preg_replace('/LOE\//','',$comic->file_path);
// // writeCbrMetaData($absolutePath,$xml);
// writeCbzMetaData($absolutePath,$xml);
// echo $absolutePath . "\n";
// exit;


$comics = Comic\Comic::getAll();

foreach($comics as $comic){
  //$absolutePath = Base::WEBROOT . preg_replace('/LOE\//','',$comic->file_path);
  $absolutePath = Base::WEBROOT . $comic->file_path;
  if(!file_exists($absolutePath)){
    continue;
  }
  $pathinfo = pathinfo($absolutePath);
  $xml = generateComicInfo($comic);
  if($pathinfo['extension'] == 'cbr'){
    writeCbrMetaData($absolutePath,$xml);
  }elseif($pathinfo['extension'] == 'cbz'){
    try{
      writeCbzMetaData($absolutePath,$xml);
    }catch(\Exception $ex){
      echo $ex->getMessage() . "\n";
      continue;
    }
  }
}
