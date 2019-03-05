<?php

  echo "FFF";

  $data_dir = '/data';
  echo "<ul>";
  echo scan_dir(dirname(__FILE__).$data_dir);
  echo "</ul>";
  //scan_dir function based on https://stackoverflow.com/questions/11923235/scandir-to-sort-by-date-modified
  //modified to filter out non surfacewater files and also only to return first file in array, i.e. last cached file
  function scan_dir($dir) {
      $ignored = array('.', '..', '.svn', '.htaccess');

      $files = array();
      foreach (scandir($dir) as $file) {
          if (in_array($file, $ignored)) continue;
          if (strpos($file, 'surfacewater') !== false) {
              $files[$file] = filemtime($dir . '/' . $file);

          }


      }

      arsort($files);
      $files = array_keys($files);

      return ($files[0]) ? $files[0] : false;
  }

?>
