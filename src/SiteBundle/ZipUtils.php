<?php

namespace SiteBundle;

class FlxZipArchive extends \ZipArchive {

    /**
     * Add a Dir with Files and Subdirs to the archive
     *
     * @param string $location Real Location
     * @param string $name Name in Archive
     * @author Nicolas Heimann
     * @access private
     * */
    public function addDir($location, $name) {
        $this->addEmptyDir($name);
        $this->addDirDo($location, $name);
    }

// EO addDir;
    /**
     * Add Files & Dirs to archive.
     *
     * @param string $location Real Location
     * @param string $name Name in Archive
     * @author Nicolas Heimann
     * @access private
     * */

    private function addDirDo($location, $name) {
        $name .= '/';
        $location .= '/';
// Read all Files in Dir
        $dir = opendir($location);
        while ($file = readdir($dir)) {
            if ($file == '.' || $file == '..')
            {
                continue;
            }
// Rekursiv, If dir: FlxZipArchive::addDir(), else ::File();
            $do = (filetype($location . $file) == 'dir') ? 'addDir' : 'addFile';
            $this->$do($location . $file, $name . $file);
        }
    }

// EO addDirDo();
}

class ZipUtils {

    
public static function createZipFolder($the_folder, $zip_file_name) {
    $za = new FlxZipArchive;
    $res = $za->open($zip_file_name, \ZipArchive::CREATE);
    if ($res === TRUE) {
        $za->addDir($the_folder, basename($the_folder));
        $za->close();
    } 
    else
    {
        echo 'Could not create a zip archive';
    }
}
}

/*
function sendzip($dirToZip)
{
    $tmpfile = tempnam("tmp", "zip");
    
    createZipFolder($dirToZip, $tmpfile);

    header('Content-Type: application/zip');
    header('Content-Length: ' . filesize($tmpfile));
    header('Content-Disposition: attachment; filename="'.  basename($dirToZip).'.zip"');
    readfile($tmpfile);
    unlink($tmpfile); 
}*/