<?php
$urls = array(
   'flickr' => array('url' => 'http://www.flickr.com/atos/pro/'),
   'facebook' => array('url' => 'http://www.facebook.com/terms.php?ref=pf', 'content' => '/\<div id\="terms_of_service"\>(.*?)<\\/div>/is')
);


function disguise_curl($url) { 
   $curl = curl_init(); 

   // Setup headers - I used the same headers from Firefox version 2.0.0.6 
   // below was split up because php.net said the line was too long. :/ 
   $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5"; 
   $header[] = "Cache-Control: max-age=0"; 
   $header[] = "Connection: keep-alive"; 
   $header[] = "Keep-Alive: 300"; 
   $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7"; 
   $header[] = "Accept-Language: en-us,en;q=0.5"; 
   $header[] = "Pragma: "; // browsers keep this blank. 

   curl_setopt($curl, CURLOPT_URL, $url); 
   curl_setopt($curl, CURLOPT_USERAGENT, 'Googlebot/2.1 (+http://www.google.com/bot.html)'); 
   curl_setopt($curl, CURLOPT_HTTPHEADER, $header); 
   curl_setopt($curl, CURLOPT_REFERER, 'http://www.google.com'); 
   curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate'); 
   curl_setopt($curl, CURLOPT_AUTOREFERER, true); 
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
   curl_setopt($curl, CURLOPT_TIMEOUT, 10); 

   $html = curl_exec($curl); // execute the curl command 
   curl_close($curl); // close the connection 

   return $html; // and finally, return $html 
}

$allowed_tags = implode(array('<a>', '<p>', '<h1>', '<h2>', '<h3>', '<h4>', '<h5>', '<h6>', '<h7>', '<div>', '<span>', '<ul>', '<ol>', '<li>', '<table>', '<tbody>', '<thead>', '<tr>',
'<td>', '<th>'));

foreach ($urls as $name => $opt) {
   $content = disguise_curl($opt['url']);
   $filename = $name . '/tos.html';
   
   if (isset($opt['content'])) {
      preg_match($opt['content'], $content, $matches);
      $content = $matches[0];
   }
   
   $content = strip_tags($content, $allowed_tags);
   
   // Make dir
   if(!is_dir($name)) {
      mkdir($name, 0777, TRUE);
   }
   
   // Make folder writable
   if (!is_writable($filename)) {
      chmod($name, 0777);
   }

   // Output file
   $handle = fopen($filename, 'w');
   fwrite($handle, $content);
   fclose($handle);
}