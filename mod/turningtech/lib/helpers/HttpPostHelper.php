<?php

/**
 * utility function that handles HTTP POST with AES encrypted data in RW format
 * @param $provider string base URL for HTTP POST
 * @param $username string plaintext user name
 * @param $password string password
 * @param $passismd5ed boolean is the password already MD5ed, defaults to false
 * @return string the HTTP response
 */
function doPostRW( $provider, $username, $password, $passismd5ed = false ) {
  $retval = '';
  if( ini_get( 'allow_url_fopen' ) == '1' ) {
    $url = $provider;
    if( $url[strlen( $url ) - 1] != '/' ) $url .= '/';
    $url .= 'rww.aspx';
    if( ! $passismd5ed ) $password = md5( $password, true );
    $password = base64_encode( $password );
    $data = $username . '|' . $password;
    $data = encryptResponseWareString( $data );
    $data = base64_encode( $data );
    $data = 'LOGIN|' . $data;
    $stream = stream_context_create( array( 'http' => array( 'method' => 'POST', 'content' => $data ) ) );
    $file = @fopen( $url, 'r', false, $stream );
    if( $file !== false ) {
      $response = stream_get_contents( $file );
      if( $response !== false )
      {
        $pieces = explode( '|', $response, 2 );
        if( sizeof( $pieces ) == 2 ) {
          if( ! in_array( $pieces[0], array( 'ERROR', 'LOGOUT' ) ) ) {
            $retval = $pieces[0];
          } else {
            throw new HttpPostHelperException( 'stream_get_contents() returned an error value: ' . $response );
          }
        } else {
          throw new HttpPostHelperException( 'stream_get_contents() returned an unexpected value: ' . $response );
        }
      } else {
        throw new HttpPostHelperIOException( 'stream_get_contents() returned FALSE for ' . $url );
      }
    } else {
      throw new HttpPostHelperIOException( 'fopen() returned FALSE for ' . $url );
    }
  } else {
    throw new HttpPostHelperIOException( "ini_get( 'allow_url_fopen' ) returned " . ini_get( 'allow_url_fopen' ) . ' for ' . $url );
  }
  return $retval;
}


/***
 * utility class that handles HTTP POST with AES encrypted data
 */
class HttpPostHelper {
// um...
}


/**
 * Establish an exception namespace, add output to error_log()
*/
class HttpPostHelperException extends Exception {
  public function __tostring() {
    if( TURNINGTECH_ENABLE_POSTRW_EXCEPTIONS_IN_ERROR_LOG ) error_log( $this->getMessage() );
    return parent::__tostring();
  }
}


/**
 * Establish an exception namespace, add output to error_log()
*/
class HttpPostHelperIOException extends HttpPostHelperException {
}

?>