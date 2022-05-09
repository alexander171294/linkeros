<?php

function get_real_ip()
{
 
        if (isset($_SERVER["HTTP_CLIENT_IP"]))
        {
            return $_SERVER["HTTP_CLIENT_IP"];
        }
        elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        elseif (isset($_SERVER["HTTP_X_FORWARDED"]))
        {
            return $_SERVER["HTTP_X_FORWARDED"];
        }
        elseif (isset($_SERVER["HTTP_FORWARDED_FOR"]))
        {
            return $_SERVER["HTTP_FORWARDED_FOR"];
        }
        elseif (isset($_SERVER["HTTP_FORWARDED"]))
        {
            return $_SERVER["HTTP_FORWARDED"];
        }
        else
        {
            return $_SERVER["REMOTE_ADDR"];
        }
 
}

/**
 * Returns the size of a file without downloading it, or -1 if the file
 * size could not be determined.
 *
 * @param $url - The location of the remote file to download. Cannot
 * be null or empty.
 *
 * @return The size of the file referenced by $url, or -1 if the size
 * could not be determined.
 */
function link_file_size( $url ) {
  // Assume failure.
  $result = -1;

  $curl = curl_init( $url );

  // Issue a HEAD request and follow any redirects.
  curl_setopt( $curl, CURLOPT_NOBODY, true );
  curl_setopt( $curl, CURLOPT_HEADER, true );
  curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
  curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
  curl_setopt( $curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );

  $data = curl_exec( $curl );
  curl_close( $curl );

  if( $data ) {
    $content_length = "unknown";
    $status = "unknown";

    if( preg_match( "/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches ) ) {
      $status = (int)$matches[1];
    }

    if( preg_match( "/Content-Length: (\d+)/", $data, $matches ) ) {
      $content_length = (int)$matches[1];
    }

    // http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
    if( $status == 200 || ($status > 300 && $status <= 308) ) {
      $result = $content_length;
    }
  }

  return $result;
}

function getIpAndProxy(&$proxy)
{
    if (isset($_SERVER['HTTP_VIA'])) { $ip = $_SERVER['HTTP_VIA']; $proxy = true; } 
    elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) { $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; $proxy = true; } 
    elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) { $ip = $_SERVER['HTTP_FORWARDED_FOR']; $proxy = true; } 
    elseif (isset($_SERVER['HTTP_X_FORWARDED'])) { $ip = $_SERVER['HTTP_X_FORWARDED']; $proxy = true; } 
    elseif (isset($_SERVER['HTTP_FORWARDED'])) { $ip = $_SERVER['HTTP_FORWARDED']; $proxy = true; } 
    elseif (isset($_SERVER['HTTP_CLIENT_IP'])) { $ip = $_SERVER['HTTP_CLIENT_IP']; $proxy = true; } 
    elseif (isset($_SERVER['HTTP_FORWARDED_FOR_IP'])) { $ip = $_SERVER['HTTP_FORWARDED_FOR_IP']; $proxy = true; } 
    elseif (isset($_SERVER['VIA'])) { $ip = $_SERVER['VIA']; $proxy = true; } 
    elseif (isset($_SERVER['X_FORWARDED_FOR'])) { $ip = $_SERVER['X_FORWARDED_FOR']; $proxy = true; } 
    elseif (isset($_SERVER['FORWARDED_FOR'])) { $ip = $_SERVER['FORWARDED_FOR']; $proxy = true; } 
    elseif (isset($_SERVER['X_FORWARDED'])) { $ip = $_SERVER['X_FORWARDED']; $proxy = true; } 
    elseif (isset($_SERVER['FORWARDED'])) { $ip = $_SERVER['FORWARDED']; $proxy = true; } 
    elseif (isset($_SERVER['CLIENT_IP'])) { $ip = $_SERVER['CLIENT_IP']; $proxy = true; } 
    elseif (isset($_SERVER['FORWARDED_FOR_IP'])) { $ip = $_SERVER['FORWARDED_FOR_IP']; $proxy = true; } 
    elseif (isset($_SERVER['HTTP_PROXY_CONNECTION'])) { $ip = $_SERVER['HTTP_PROXY_CONNECTION']; $proxy = true; }  
    elseif (isset($_SERVER['REMOTE_ADDR'])) { $ip = $_SERVER['REMOTE_ADDR']; $proxy = false; }
    return $ip;
}