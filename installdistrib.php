<?php


/*
echo "mkdir('../tmpinstall');";
mkdir('../tmpinstall');

echo "copy('distrib.zip', '../tmpinstall/distrib.zip');";
if (!copy('distrib.zip', '../tmpinstall/distrib.zip'))
{
    echo "FAILED";
}
*/
echo "rm -rf ...";
echo exec('rm -rf ../app');
echo exec('rm -rf ../bin');
echo exec('rm -rf ../src');
echo exec('rm -rf ../vendor');
echo exec('rm -rf ../web/bundles');


$zip = new ZipArchive;
$res = $zip->open('./distrib.zip');
if ($res === TRUE) {
  $zip->extractTo('../');
  $zip->close();
  echo 'woot!';
} else {
  echo 'doh!';
}

?>
