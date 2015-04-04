<?php

namespace SiteBundle;

class DocShort {
    public $name = "";
    public $url = "";
    public $description = "";
    public $tags = "";
}

class DocFull {
    public $name = "";
    public $description = "";
    public $tags = "";
    public $tagsString = "";
    public $files = array();
}

class DocFile {
    public $name = "";
    public $url = "";
}


function scandir2($dir)
{
    $array = scandir($dir);
    $array2 = array();
    foreach ($array as $a)
    {
        if ($a != "." && $a != "..")
        {
            array_push($array2, $a);
        }
    }
    return $array2;
}


class DocUtils {

    
public static function getDirFromUrl($rootDir, $url)
{
    $array = scandir($rootDir);
    foreach ($array as $a)
    {
        if (DocUtils::slugify($a) === $url)
        {
            return $a;
        }
    }
    return ;
}


public static function getFullDossier($rootDir, $url)
{
    $docFull  = new DocFull();
    $dirPath = $rootDir.'/'.DocUtils::getDirFromUrl($rootDir, $url);
    $docFull->name = basename($dirPath);
    $docFull->description = DocUtils::getDescription($dirPath);
    $docFull->tags = DocUtils::getTags($dirPath);
    $docFull->tagsString = DocUtils::getTagsString($dirPath);
    $docFull->files = DocUtils::getFiles($rootDir, $docFull->name);
    
    return $docFull;
}

public static function getFiles($rootDir, $dirName)
{
    $files = scandir2($rootDir.'/'.$dirName);
    $result = array();
    foreach($files as $f)
    {
        if ($f != "__tags.txt" && $f != "__description.txt")
        {
            $newDocFile = new DocFile();
            $newDocFile->name = $f;
            $newDocFile->url = DocUtils::slugify($dirName).'/'.DocUtils::slugify($f);
            array_push($result, $newDocFile);
        }
    }
    return $result;
}


public static function getFilename($rootDir, $dirUrl, $fileUrl)
{
    $dirPath = DocUtils::getDirFromUrl($rootDir, $dirUrl);
    $filePath = DocUtils::getDirFromUrl($rootDir.'/'.$dirPath, $fileUrl);
    return $rootDir.'/'.$dirPath.'/'.$filePath;
}



// description and tags
public static function getDescription($path)
{
    $description = "";
    $descriptionFilename = $path . "/__description.txt";
    if (file_exists($descriptionFilename)) {
        $description = file_get_contents($descriptionFilename);
    }
    
    return $description;
}

public static function setDescription($path, $description)
{
    error_log("set description : ".$path. " : ".$description);
    file_put_contents($path."/"."__description.txt", $description);
}

public static function getTags($path)
{
    $tags = "";
    $tagsFilename = $path . "/__tags.txt";
    if (file_exists($tagsFilename)) {
        $tags = file_get_contents($tagsFilename);
    }
    
    return split(" ", $tags);
}

public static function getTagsString($path)
{
    $tags = "";
    $tagsFilename = $path . "/__tags.txt";
    if (file_exists($tagsFilename)) {
        $tags = file_get_contents($tagsFilename);
    }
    
    return $tags;
}

public static function setTags($path, $tags)
{
    error_log("set tags : ".$path. " : ".$tags);
    file_put_contents($path."/"."__tags.txt", $tags);
}

/*
public static function setName($path, $name)
{
    error_log("set name : ".$path. " : ".$name);
    
    //$dirLinkPath = readlink($path."/__link");
    //file_put_contents($dirLinkPath."/"."__tags.txt", $tags);
        
    $dirLinkPath = readlink($path."/__link");
    rename($dirLinkPath, $dirLinkPath."/../".$name);
    Mirror::getInstance()->createLinksByName($name, 0);
    deleteDirectoryRec($path);
    
    echo '{"url": "'.filenameToUrl($name).'"}';
}
*/
    
    static public function slugify($text)
  {
        /*
    // replace all non letters or digits by -
    $text = preg_replace('/\W+/', '-', $text);
 
    // trim and lowercase
    $text = strtolower(trim($text, '-'));
 
    return $text;
         * 
         */
        
    return str_replace("'", "-", str_replace(" ", "-", $text));
  }
  
  
public static function getAllDocShort($path) 
{
    $array = array();
    $files = scandir2($path);
    foreach($files as $f)
    {
        array_push($array, DocUtils::getDocShort($path."/".$f));
    }
    return $array;
}

public static function getDocShort($path)
{
    $filename = basename($path);
    $dirLinkPath = $path;
    $name = basename($dirLinkPath);
    
    $short = new DocShort();
    $short->name = $name;
    $short->url = DocUtils::slugify($filename);
    $short->description = DocUtils::getDescription($path);
    $short->tags = DocUtils::getTags($path);
    
    return $short;
}




public static function getSearchDocShort($path, $search) 
{
    $response = [];
    $words = explode(" ",$search);
    
    $dirs = scandir2($path);
    foreach($dirs as $d)
    {
        // read tags
        $tags = DocUtils::getTagsString($path."/".$d);
        if (strlen($tags) == 0)
        {
            continue;
        }
        
        //error_log("tags ".$d.": ".$tags);
        $allWordsFound = true;
        foreach($words as $word)
        {
            $pos = strpos($tags, $word);
            $found = $pos;
            if ($found === 0)
            {
                $found = TRUE;
            }
            if (!$found)
            {
                //error_log("pos : ".$pos);
                //error_log("not found : ".$word);
                $allWordsFound = false;
                break;
            }
        }
        
        if ($allWordsFound)
        {
            array_push($response, DocUtils::getDocShort($path."/".$d));
        }
    }
    return $response;
}

}

