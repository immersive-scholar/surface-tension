<html>
 <head>
  <title>USGS Data Cacher</title>
  <link rel="stylesheet" type="text/css" href="app/css/global.css">
 </head>
 <body>
 <?php
   echo '<h1>USGS Surfacewater Data Cacher</h1>';

   $data_dir = '/app/data';
   $filepath = $data_dir.'/realtime-surfacewater-'.date("Y-m-d").'.csv';

   $tempdata = $data_dir.'/temp.csv';
   echo '<h2>Scanning for:</h2>';
   echo '<h3>'.$filepath.'</h3>';

  //does the file for today exist?

  //then don't redownload interface
  //otherwise download it but if it fails use a previous one?

  //PHP is a pretty terrible language that requires the clearing of a cache just to see if a file exists http://php.net/manual/en/function.clearstatcache.php
  clearstatcache();

  if (file_exists('.'.$filepath)) {
      echo "<h3><span class='good'>Cached data for today was found.</span></h3>";
      //load visualization and pass it this file's name as a parameter;
      //*******************************************************************************************************************************************************************************
      load_viz($filepath);
  } else {
      echo "<h3>No cached data for today.</h3>";
      //load the file from usgs.
      //if response code is 200, cache this file and load vis with this file as parameter
      //if response is not 200, don't cache the file and load vis with a previously cached file as ReflectionParameter

      $url = "https://waterwatch.usgs.gov/webservices/realtime?format=csv";
      //$url = "http://misharabinovich.com/asd";
      // This is where the file will be saved
      $fp = fopen(dirname(__FILE__).$tempdata, 'w+');
      // Replace spaces with %20 on the URL string
      $ch = curl_init(str_replace(" ","%20", $url));
      // We allow cURL to run for max 600 seconds, more info https://curl.haxx.se/libcurl/c/CURLOPT_TIMEOUT.html
      //In theory leaving it 0 can allow it to not time out, but in practice I found that this breaks the process and a file of 0 bytes is written for some dumb php reason
      curl_setopt($ch, CURLOPT_TIMEOUT, 600);
      // Write curl response to file
      curl_setopt($ch, CURLOPT_FILE, $fp);
      // Follow locations
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

      //Trying to reset pho curl option overrides as described in https://stackoverflow.com/questions/27088070/curl-works-from-terminal-but-not-from-php
      //curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 60);
      //curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, 0);
      //curl_setopt($ch, CURLOPT_MAXREDIRS, -1);
      //curl_setopt($ch, CURLOPT_NOSIGNAL, 0);

      // Get curl response
      $output = curl_exec($ch);

      //Some more info about the result for status checking
      $http_respond = trim( strip_tags( $http_respond ) );
      $http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );


      // Close curl handle
      curl_close($ch);
      // Close file handle
      fclose($fp);

      echo $url;
      //show only if there is something to show e.g. 404 barf
      if ($http_respond != "") {
        echo '<p><b>http_respond:</b> '.$http_respond.'</p>';
      }

      if ($http_code != "200") echo "<span class='bad'>";
      echo '<p><b>http_code:</b> '.$http_code.'</p>';
      if ($http_code != "200") echo "</span>";

      if ($http_code == "200") {
        if (!copy(dirname(__FILE__).$tempdata, dirname(__FILE__).$filepath)){
          echo "<p><span class='bad'>ERROR copying temp file into new cached file!</span></p>";
        } else {
          //*******************************************************************************************************************************************************************************
          //copying worked! Load viz with new cache file
          load_viz($filepath);
        }
      } else {
        //Can't load data, use previous cached file
        echo '<p>Find Last Cached File</p>';
        echo "<ul>";
        $last_cached_file = scan_dir(dirname(__FILE__).$data_dir);
        echo $last_cached_file;
        echo "</ul>";
        //*******************************************************************************************************************************************************************************
        //Load viz with previously cached file
        load_viz($data_dir.'/'.$last_cached_file);
      }
  }
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
  function load_viz($pathy){
        echo "<a href='".$pathy."'>".$pathy."</a>";
  }
 ?>
 </body>
</html>
