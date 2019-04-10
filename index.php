<html>
<head>
  <title>USGS Data Cacher</title>
  <link rel="stylesheet" type="text/css" href="app/css/global.css">
</head>
<body>
  <?php
  //url query string parameter GET-style API options to possibly pass to the visualization
  $sidebar = filter_input(INPUT_GET, 'sidebar', FILTER_SANITIZE_URL);
  $sorting = filter_input(INPUT_GET, 'sorting', FILTER_SANITIZE_URL);
  $zoom = filter_input(INPUT_GET, 'zoom', FILTER_SANITIZE_URL);
  $map = filter_input(INPUT_GET, 'map', FILTER_SANITIZE_URL);

  echo '<h1>USGS Streamflow Data Cacher</h1>';
  echo "\n";
  if( ! ini_get('date.timezone') ) {
    date_default_timezone_set('America/New_York');
    echo '<h2><span class="bad">Server timezone not set </span>, using: '.date_default_timezone_get().'</h2>';
  } else {
    echo '<h2>Server timezone set to '.ini_get('date.timezone').'</h2>';
  }
  echo "\n";
  $data_dir = '/app/data';
  $relative_data_dir = 'data'; //the fact that we need to also have this path is not ideal. If I use the data_dir path it all works locally. But on dreamhost, when this php script goes to finally load viz.html, viz.html can't find the file. If viz.html is handed a more relative path (no 'app' in relative_data_dir because viz.html is in app together with the data folder), it can indeed find it. I tried changing data_dir to just be relative and use that in javascript and below where we check for locally cached files. That broke checking for cached files. So now I guess we need two hardcoded directory path variables, one more relative for JavaScript viz.html and one less relative for index.php. Maybe index.php and app should be in the same folder.

  $data_file_path = '/realtime-streamflow-'.date("Y-m-d").'.csv';//"Y-m-d-H-i-s" for real real time

  $filepath = $data_dir.$data_file_path;
  $more_relative_filepath = $relative_data_dir.$data_file_path;


  $tempdata = $data_dir.'/temp.csv';
  echo '<h2>Scanning for:</h2>';echo "\n";
  echo '<h3>'.$filepath.'</h3>';echo "\n";

  //does the file for this day exist?
  //then don't redownload
  //otherwise download it but if it fails use a previous one

  //PHP requires the clearing of a cache just to see if a file exists http://php.net/manual/en/function.clearstatcache.php
  clearstatcache();

  if (file_exists('.'.$filepath)) {
    echo "<h3><span class='good'>Cached data for this day was found.</span></h3>";echo "\n";
    //load visualization and pass it this file's name as a parameter;
    //*******************************************************************************************************************************************************************************
    load_viz($more_relative_filepath, $sorting, $sidebar, $zoom, $map);
  } else {
    echo "<h3>No cached data for this day.</h3>";echo "\n";
    //load the file from usgs.
    //if response code is 200, cache this file and load vis with this file as parameter
    //if response is not 200, don't cache the file and load vis with a previously cached file

    $url = "https://waterwatch.usgs.gov/webservices/realtime?format=csv";
    // This is where the file will be saved
    $fp = fopen(dirname(__FILE__).$tempdata, 'w+');
    // Replace spaces with %20 on the URL string
    $ch = curl_init(str_replace(" ","%20", $url));
    // We allow cURL to run for max 600 seconds, more info https://curl.haxx.se/libcurl/c/CURLOPT_TIMEOUT.html
    //In theory leaving it 0 can allow it to not time out, but in practice I found that this breaks the process and a file of 0 bytes is written for some php reason
    curl_setopt($ch, CURLOPT_TIMEOUT, 600);
    // Write curl response to file
    curl_setopt($ch, CURLOPT_FILE, $fp);
    // Follow locations
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

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
    //show only if there is something to show e.g. 404 response
    if ($http_respond != "") {
      echo '<p><b>http_respond:</b> '.$http_respond.'</p>';echo "\n";
    }

    if ($http_code != "200") echo "<span class='bad'>";
    echo '<p><b>http_code:</b> '.$http_code.'</p>';
    if ($http_code != "200") echo "</span>";

    if ($http_code == "200") {
      if (!copy(dirname(__FILE__).$tempdata, dirname(__FILE__).$filepath)){
        echo "<p><span class='bad'>ERROR copying temp file into new cached file!</span></p>";
      } else {
        //*******************************************************************************************************************************************************************************
        //copying worked. Load viz with new cached file
        load_viz($more_relative_filepath, $sorting, $sidebar, $zoom, $map);
      }
    } else {
      //Can't load data, use previously cached file
      echo '<p>Find Last Cached File</p>';
      echo "<ul>";
      $last_cached_file = scan_dir(dirname(__FILE__).$data_dir);
      echo $last_cached_file;
      echo "</ul>";
      //*******************************************************************************************************************************************************************************
      //Load viz with previously cached file
      load_viz($more_relative_filepath.'/'.$last_cached_file, $sorting, $sidebar, $zoom, $map);
    }
  }
  //scan_dir function based on https://stackoverflow.com/questions/11923235/scandir-to-sort-by-date-modified
  //modified to filter out non streamflow files and also only to return first file in array, i.e. last cached file
  function scan_dir($dir) {
    $ignored = array('.', '..', '.svn', '.htaccess');

    $files = array();
    foreach (scandir($dir) as $file) {
      if (in_array($file, $ignored)) continue;
      if (strpos($file, 'streamflow') !== false) {
        $files[$file] = filemtime($dir . '/' . $file);
      }
    }

    arsort($files);
    $files = array_keys($files);

    return ($files[0]) ? $files[0] : false;
  }
  function load_viz($pathy, $sorting, $sidebar, $zoom, $map){
    echo "<a href='".$pathy."'>".$pathy."</a>";
    echo "\n\n</body>";
    echo "\n<script>setTimeout(function(){
      window.location.href = 'app/viz.html?data=".$pathy."&sorting=".$sorting."&sidebar=".$sidebar."&zoom=".$zoom."&map=".$map."';
    }, 3000)</script>";
  }
  ?>
</body>
</html>
