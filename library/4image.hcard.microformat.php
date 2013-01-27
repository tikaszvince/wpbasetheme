<?php

/*
Ezekre a CSS szabályokra mindenképp szükséged lesz!
A #hcard-4image background kép pontos elérhetőségéről gondoskodj

#hcard-4image .copy-name,
#hcard-4image .org,
#hcard-4image .photo,
#hcard-4image address { display:none;}
#hcard-4image {
	font-family: 'Trebuchet MS', Helvetica, sans-serif;
}

#hcard-4image .url,
#hcard-4image .url:hover {
	color:#aaa;
	margin:0;
	float:right;
	display:block;
	font-size:11px;
	height:22px;
	line-height:11px;
	padding:0 0 0 25px;
	text-align:left;
	text-transform:uppercase;
	text-decoration:none;
	background:transparent url(img/4image.18.png) no-repeat scroll 0 -20px;
}

#hcard-4image .url .a,
#hcard-4image .url:hover .a {
	display:block;
	float:left;
	white-space:nowrap;
	font-weight:bold;
}

#hcard-4image .url .b,
#hcard-4image .url:hover .b {
	text-transform:lowercase;
	font-size:9px;
	clear:left;
	display:block;
}

#hcard-4image .url {}
#hcard-4image .url .a {}
#hcard-4image .url .b {}
#hcard-4image .url:hover {}
#hcard-4image .url:hover .a {}
#hcard-4image .url:hover .b {}

*/


function fingerprint_4image() {
	$file = dirname(__FILE__).'/4image.fingerprint.ini';
	if (
		!is_file( $file )
		|| filemtime( $file ) + ( 60 * 60 * 24 * 30 ) < time()
	) {
		$ini = _fifingerprint_remote_get('http://data.4image.hu/fingerprint.ini');
		if ( preg_match('/^\[FIFingerprint\]/', $ini ) ) {
			$newFile = dirname(__FILE__).'/_new_4image.fingerprint.ini';
			file_put_contents($newFile, $ini);
			if ( is_file($file) ) {
				unlink( $file );
			}
			rename($newFile, $file);
		}
	}
	$parsedIni = parse_ini_file($file);
	$data = (object)array_merge(
		array(
			'url' => 'http://4image.hu',
			'urlTitle' => '4IMAGE Marketing és Vizuális Kommunikáció',
			'additionalName' => '4Image',
			'givenName' => '4Image',
			'fullName' => 'Marketing és Vizuális Kommunikáció',
			'addr_country' => 'Hungary',
			'addr_postalCode' => 'HU 2051',
			'addr_locality' => 'Biatorbágy',
			'addr_streetAddress' => 'Zsigmond király u. 2.',
		),
		$parsedIni
	);
	echo
	'<div id="hcard-4image" class="vcard">
<span class="full-name copy-name company fn">'.$data->urlTitle.'</span>
<a class="url n" href="',$data->url,'" title="'.$data->urlTitle.'" target="_blank">
	<span class="a">'.$data->givenName.'</span>
	<span class="b">'.$data->fullName.'</span>
</a>
<img src="http://data.4image.hu/4image.png" alt="',$data->urlTitle,'" class="photo"/>
<div class="org">',$data->additionalName,'</div> 
 <address class="adr">
  <div class="street-address">',$data->addr_streetAddress,'</div>
  <span class="locality">',$data->addr_locality,'</span>, 
  <span class="postal-code">',$data->addr_postalCode,'</span>
  <span class="country-name">',$data->addr_country,'</span>
 </address>
</div>'	;
}

function _fifingerprint_remote_get($url) {
	if ( ini_get('allow_url_fopen') ) {
		return _fifingerprint_remote_get_allowed_remote($url);
	}
	elseif ( !preg_match('%\bcurl_init\b%', ini_get('disable_functions')) ) {
		return _fifingerprint_remote_get_curl($url);
	}
	return _fifingerprint_remote_http_get($url);
}

function _fifingerprint_remote_get_allowed_remote($url) {
	return file_get_contents($url);
}

function _fifingerprint_remote_get_curl($url) {
	$ch = curl_init();
	$timeout = 5; // set to zero for no timeout
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$file_contents = curl_exec($ch);
	curl_close($ch);
	return $file_contents;
}


function _fifingerprint_remote_http_get($url) {
  $url_stuff = parse_url($url);
  $port = isset($url_stuff['port']) ? $url_stuff['port'] : 80;
  $fp = fsockopen($url_stuff['host'], $port);

  if (!$fp) {
    return false;
  }
  $query  = 'GET '.$url_stuff['path'].'?'.$url_stuff['query']." HTTP/1.1\r\n";
  $query .= 'Host: '.$url_stuff['host']."\r\n";
  $query .= 'Connection: close'."\r\n";
  $query .= 'Cache-Control: no'."\r\n";
  $query .= 'Accept-Ranges: bytes'."\r\n";
  //$query .= 'Referer: http:/...'."\r\n";
  //$query .= 'User-Agent: myphp'."\r\n";
  $query .= "\r\n";

  fwrite($fp, $query);

  $chunksize = 1*(1024*1024);
  $headersfound = false;
  $buffer = '';

  while (!feof($fp) && !$headersfound) {
    $buffer .= fread($fp, 1);
    if (preg_match('/HTTP\/[0-9]\.[0-9][ ]+([0-9]{3}).*\r\n/', $buffer, $matches)) {
      $headers['HTTP'] = $matches[1];
      $buffer = '';
    }
    elseif (preg_match('/([^:][A-Za-z_-]+):[ ]+(.*)\r\n/', $buffer, $matches)) {
      $headers[$matches[1]] = $matches[2];
      $buffer = '';
    }
    elseif (preg_match('/^\r\n/', $buffer)) {
      $headersfound = true;
      $buffer = '';
    }

    if (strlen($buffer) >= $chunksize) {
      return false;
    }
  }

  if (preg_match('/4[0-9]{2}/', $headers['HTTP'])) {
    return false;
  }
  elseif (preg_match('/3[0-9]{2}/', $headers['HTTP']) && !empty($headers['Location'])) {
    $url = $headers['Location'];
    return _fifingerprint_remote_http_get($url, $range);
  }
  $_content = array();
  while (!feof($fp) && $headersfound) {
    $buffer = fread($fp, $chunksize);
    $_content[] = $buffer;
  }
  $status = fclose($fp);

  return join("\n", $_content);
}

//end
