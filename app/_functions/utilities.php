<?php

if( !function_exists( 'payment_page_format_content' ) ) {

  /**
   * @param $content
   * @return string
   */
  function payment_page_format_content( $content ) {
    return do_shortcode( wpautop( $content ) );
  }

}

if( !function_exists( 'payment_page_remove_url_protocol' ) ) {

  /**
   * @param $url
   * @return string
   */
  function payment_page_remove_url_protocol( $url ): string {
    return str_replace([ 'http://', 'https://' ], '', $url);
  }

}

if( !function_exists( 'payment_page_username_from_details' ) ) {

  function payment_page_username_from_details( $email_address, $first_name = '', $last_name = '' ) :string {
    if( $first_name === '' && $last_name !== '' ) {
      $first_name = $last_name;
      $last_name = '';
    }

    if( $first_name !== '' ) {
      $username = payment_page_label_to_alias( $first_name );

      if( validate_username( $username ) && !username_exists( $username ) )
        return $username;

      for( $i = 1; $i <= 5; $i++ ) {
        $current_username_test = $username . '_' . rand( 1, 1000 );

        if( validate_username( $current_username_test ) && !username_exists( $current_username_test ) )
          return $current_username_test;
      }

      $username = payment_page_label_to_alias( $first_name . ' ' . $last_name );

      if( validate_username( $username ) && !username_exists( $username ) )
        return $username;

      for( $i = 1; $i <= 5; $i++ ) {
        $current_username_test = $username . '_' . rand( 1, 1000 );

        if( validate_username( $current_username_test ) && !username_exists( $current_username_test ) )
          return $current_username_test;
      }
    }

    $username = substr( $email_address, 0, strpos( $email_address, '@' ) );

    if( validate_username( $username ) && !username_exists( $username ) )
      return $username;

    for( $i = 1; $i <= 5; $i++ ) {
      $current_username_test = $username . '_' . rand( 1, 1000 );

      if( validate_username( $current_username_test ) && !username_exists( $current_username_test ) )
        return $current_username_test;
    }

    return $email_address;
  }

}

if( !function_exists( 'payment_page_generate_random_token' ) ) {

  function payment_page_generate_random_token( int $length = 13 ) {
    if ( function_exists("random_bytes" ) ) {
      $bytes = random_bytes(ceil($length / 2));
    } elseif (function_exists("openssl_random_pseudo_bytes")) {
      $bytes = openssl_random_pseudo_bytes(ceil($length / 2));
    } else {
      exit( "no cryptographically secure random function available" );
    }

    return substr( bin2hex($bytes), 0, $length );
  }

}

if( !function_exists( 'payment_page_http_user_agent' ) ) {

  /**
   * @return string
   */
  function payment_page_http_user_agent() :string {
    return $_SERVER['HTTP_USER_AGENT'];
  }

}

if( !function_exists( 'payment_page_http_ip_address' ) ) {

  function payment_page_http_ip_address() :string {
    return $_SERVER['REMOTE_ADDR'];
  }

}

if( !function_exists( 'payment_page_debug_dump' ) ) {

  /**
   * Debug helper function.  This is a wrapper for var_dump() that adds
   * the <pre /> tags, cleans up newlines and indents, and runs
   * htmlentities() before output.
   *
   * @param  mixed  $var   The variable to dump.
   * @param  string $label OPTIONAL Label to prepend to output.
   * @param  bool   $echo  OPTIONAL Echo output if true.
   * @return string
   */
  function payment_page_debug_dump($var, $label = null, $echo = true) {
    $label = ($label===null) ? '' : rtrim($label) . ' ';

    ob_start();

    var_dump($var);

    $output = ob_get_clean();
    // neaten the newlines and indents
    $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);

    if(!extension_loaded('xdebug')) {
      $output = htmlspecialchars($output, ENT_QUOTES);
    }

    $output = '<pre>' . $label . $output . '</pre>';

    if ($echo)
      echo $output;

    return $output;
  }

}

if( !function_exists( 'payment_page_format_content_autoembed' ) ) {

  function payment_page_format_content_autoembed( $content ) {
    if( !isset( $GLOBALS['wp_embed'] ) )
      return $content;

    $usecache_status = $GLOBALS['wp_embed']->usecache;

    $GLOBALS['wp_embed']->usecache = false;

    $autoembed = $GLOBALS['wp_embed']->autoembed( $content );

    $GLOBALS['wp_embed']->usecache = $usecache_status;

    return $autoembed;
  }

}

if( !function_exists( 'payment_page_redirect' ) ) {

  function payment_page_redirect( $url ) {
    if( !did_action( 'wp_head' ) ) {
      wp_redirect( $url );

      return;
    }

    echo '<script type="text/javascript">document.location = "' . $url . '"</script>';
  }

}

if( !function_exists( 'payment_page_alias_to_label' ) ) {

  /**
   * @param $alias
   * @return string
   */
  function payment_page_alias_to_label( $alias ) {
    if( $alias === 'api' )
      return 'API';

    $response = str_replace( [ '_', '-', '.' ], ' ', $alias );

    $response = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $response );

    $response = ucwords( $response );

    $response = str_replace( 'Id', 'ID', $response );
    $response = str_replace( 'Url', 'URL', $response );

    return $response;
  }

}

if( !function_exists( 'payment_page_label_to_alias' ) ) {

  /**
   * @param $label
   * @return string
   */
  function payment_page_label_to_alias( $label ) {
    return preg_replace("/\W+/", '', strtolower( str_replace( ' ', '_', $label ) ) );
  }

}

if( !function_exists( 'payment_page_label_to_slug' ) ) {

  /**
   * @param $label
   * @return string
   */
  function payment_page_label_to_slug( $label ) {
    $response = str_replace( '_', '-', preg_replace("/\W+/", '', strtolower( str_replace( ' ', '_', $label ) ) ) );

    $response = str_replace( '--', '-', $response );

    return $response;
  }

}

if( !function_exists( 'payment_page_label_to_prefix' ) ) {

  /**
   * @param $label
   * @return string
   */
  function payment_page_label_to_prefix( $label ) {
    $response = '';
    $tokens   = explode( '_', payment_page_label_to_alias( $label ) );

    if( count( $tokens ) <= 1 )
      return str_replace( '_', '-', payment_page_label_to_alias( $label ) );

    foreach( $tokens as $token )
      if( !empty( $token ) )
        $response .= $token[0];

    return $response;
  }

}

if( !function_exists( 'payment_page_utility_selected' ) ) {

  /**
   * @param $selected
   * @param bool $current
   * @param bool $echo
   * @return string
   */
  function payment_page_utility_selected( $selected, $current = true, $echo = true ) {
    if( is_array( $selected ) )
      return selected( 1, in_array( $current, $selected ), $echo );

    return selected( $selected, $current, $echo );
  }

}

if( !function_exists( 'payment_page_utility_checked' ) ) {

  function payment_page_utility_checked( $checked, $current = true, $echo = true ) {
    if( is_array( $checked ) )
      return checked( 1, in_array( $current, $checked ), $echo );

    return checked( $checked, $current, $echo );
  }

}

if( !function_exists( 'payment_page_utilities_map_object' ) ) {

  function payment_page_utilities_map_object($array, $param = 'ID', $multiple = false ) {
    $ret = [];

    foreach ($array as $a) {
      if( !$multiple ) {
        $ret[ $a->$param ] = $a;
        continue;
      }

      if( !isset( $ret[ $a->$param ] ) )
        $ret[ $a->$param ] = [];

      $ret[ $a->$param ][] = $a;
    }

    return $ret;
  }

}

if( !function_exists( 'payment_page_utilities_map_array' ) ) {

  function payment_page_utilities_map_array($array, $param = 'ID', $multiple = false ) {
    $ret = [];

    foreach ($array as $a) {
      if( $multiple === false ) {
        $ret[ $a[ $param ] ] = $a;
        continue;
      }

      if( !isset( $ret[ $a[ $param ] ] ) )
        $ret[ $a[ $param ] ] = [];

      if( $multiple === true )
        $ret[ $a[ $param ] ][] = $a;
      else
        $ret[ $a[ $param ] ][ $a[ $multiple ] ] = $a;
    }

    return $ret;
  }

}

if( !function_exists( 'payment_page_utility_map_to_array_assoc' ) ) {

  function payment_page_utility_map_to_array_assoc( $array, $key_param = 'key', $value_param = 'value', $merge_existent_indexes = false ) {
    if( !is_array( $array ) )
      return [];

    $ret = [];

    foreach ( $array as $a ) {
      if( is_object( $a ) )
        $a = get_object_vars( $a );

      $current_value_param = $a[$value_param];

      if( is_array( $key_param ) ) {
        $current_key_param   = '';

        foreach( $key_param as $key_param_token )
          $current_key_param .= ( isset( $a[ $key_param_token ] ) ? $a[ $key_param_token ] : $key_param_token );
      } else {
        $current_key_param   = $a[$key_param];
      }

      if( isset( $ret[ $current_key_param ] ) && $merge_existent_indexes ) {
        if( !is_array( $ret[ $current_key_param ] ) )
          $ret[ $current_key_param ] = [ $ret[ $current_key_param ] ];

        $ret[ $current_key_param ][] = $current_value_param;

        continue;
      }

      $ret[ $current_key_param ] = $current_value_param;
    }

    return $ret;
  }

}

if( !function_exists( 'payment_page_utility_array_trim' ) ) {

  function payment_page_utility_array_trim( $array, $charlist = " \t\n\r\0\x0B" ) {
    foreach( $array as $k => $v )
      $array[ $k ] = is_string( $v ) ? trim( $v, $charlist ) : $v;

    return $array;
  }

}

if( !function_exists( 'payment_page_utility_map_to_object_assoc' ) ) {

  function payment_page_utility_map_to_object_assoc( $object, $key_param = 'key', $value_param = 'value' ) {
    if( !is_array( $object ) )
      return new stdClass();

    $ret = new stdClass();

    foreach ($object as $o) {
      $index = $o->$key_param;
      $ret->$index = $o->$value_param;
    }

    return $ret;
  }

}

if( !function_exists( 'payment_page_permalink_extend' ) ) {

  function payment_page_permalink_extend($permalink, $param, $value ) {
    $permalink = remove_query_arg( $param, $permalink );

    $permalink = add_query_arg( $param, $value, $permalink );

    return $permalink;
  }

}

if( !function_exists( 'payment_page_link_is_404' ) ) {

  function payment_page_link_is_404($url) {
    $http_code = payment_page_link_http_code( $url );

    if( $http_code == 0 && strpos( $url, get_site_url() ) === 0 )
      return false;

    if( in_array( $http_code, [ 302, 403 ] ) )
      return false;

    if ($http_code >= 200 && $http_code < 300) {
      return false;
    } else {
      return true;
    }
  }

}

if( !function_exists( 'payment_page_link_http_code' ) ) {

  function payment_page_link_http_code($url) {
    $handle = curl_init($url);
    curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($handle);
    $http_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    curl_close($handle);

    return $http_code;
  }

}

function payment_page_content_allowed_html_tags() {
  return [
    'a'      => [ 'href' => [], 'target' => [], 'alt' => [] ],
    'br'     => [],
    'video'  => [ 'width' => [], 'height' => [] ],
    'source' => [ 'src' => [], 'type' => [] ],
    'strong' => [ 'style' => [] ],
    'sub'    => [ 'style' => [] ],
    'sup'    => [ 'style' => [] ],
    's'      => [ 'style' => [] ],
    'i'      => [ 'style' => [] ],
    'u'      => [ 'style' => [] ],
    'span'   => [ 'align' => [], 'class' => [], 'type' => [], 'id' => [], 'style' => [], 'data' => [] ],
    'h1'     => [ 'align' => [], 'class' => [], 'type' => [], 'id' => [], 'style' => [], 'data' => [] ],
    'h2'     => [ 'align' => [], 'class' => [], 'type' => [], 'id' => [], 'style' => [], 'data' => [] ],
    'h3'     => [ 'align' => [], 'class' => [], 'type' => [], 'id' => [], 'style' => [], 'data' => [] ],
    'ol'     => [ 'align' => [], 'class' => [], 'type' => [], 'id' => [], 'style' => [], 'data' => [] ],
    'ul'     => [ 'align' => [], 'class' => [], 'type' => [], 'id' => [], 'style' => [], 'data' => [] ],
    'li'     => [ 'align' => [], 'class' => [], 'type' => [], 'id' => [], 'style' => [], 'data' => [] ],
    'em'     => [ 'align' => [], 'class' => [], 'type' => [], 'id' => [], 'style' => [], 'data' => [] ],
    'hr'     => [],
    'p'      => [ 'align' => [], 'class' => [], 'type' => [], 'id' => [], 'style' => [], 'data' => [] ],
    'img'    => [ 'align' => [], 'class' => [], 'type' => [], 'id' => [], 'style' => [], 'src' => [], 'alt' => [], 'href' => [], 'rel' => [], 'target' => [], 'value' => [], 'name' => [], 'width' => [], 'height' => [], 'data' => [], 'title' => [] ]
  ];
}

/**
 * For example, turns USD amount to cents
 * @param $price
 * @return int
 */
function payment_page_format_price_as_non_decimal_int( $price, $currency ) {
  $payment_page_currency_code_to_decimal_map = payment_page_currency_code_to_decimal_map();
  $currency = strtoupper( $currency );

  if( isset( $payment_page_currency_code_to_decimal_map[ $currency ] ) )
    return intval( $price * pow( 10, $payment_page_currency_code_to_decimal_map[ $currency ] ) );

  return intval( $price * 100 );
}

function payment_page_format_decimal_int_price_as_readable( $price, $currency ) {
  $payment_page_currency_code_to_decimal_map = payment_page_currency_code_to_decimal_map();
  $currency = strtoupper( $currency );

  if( isset( $payment_page_currency_code_to_decimal_map[ $currency ] ) )
    return number_format_i18n( $price / pow( 10, $payment_page_currency_code_to_decimal_map[ $currency ] ), $payment_page_currency_code_to_decimal_map[ $currency ] );

  return number_format_i18n( $price / 100, 2 );
}

function payment_page_currency_code_to_decimal_map() {
  return [
    'AED' => 2,
    'ALL' => 2,
    'AMD' => 2,
    'ANG' => 2,
    'AOA' => 2,
    'ARS' => 2,
    'AUD' => 2,
    'AWG' => 2,
    'AZN' => 2,
    'BAM' => 2,
    'BBD' => 2,
    'BDT' => 2,
    'BGN' => 2,
    'BHD' => 3,
    'BMD' => 2,
    'BND' => 2,
    'BOB' => 2,
    'BRL' => 2,
    'BSD' => 2,
    'BWP' => 2,
    'BYN' => 2,
    'BZD' => 2,
    'CAD' => 2,
    'CHF' => 2,
    'CLP' => 0,
    'CNY' => 2,
    'COP' => 2,
    'CRC' => 2,
    'CUP' => 2,
    'CVE' => 0,
    'CZK' => 2,
    'DJF' => 0,
    'DKK' => 2,
    'DOP' => 2,
    'DZD' => 2,
    'EGP' => 2,
    'ETB' => 2,
    'EUR' => 2,
    'FJD' => 2,
    'FKP' => 2,
    'GBP' => 2,
    'GEL' => 2,
    'GHS' => 2,
    'GIP' => 2,
    'GMD' => 2,
    'GNF' => 0,
    'GTQ' => 2,
    'GYD' => 2,
    'HKD' => 2,
    'HNL' => 2,
    'HTG' => 2,
    'HUF' => 2,
    'IDR' => 0,
    'ILS' => 2,
    'INR' => 2,
    'IQD' => 3,
    'ISK' => 2,
    'JMD' => 2,
    'JOD' => 3,
    'JPY' => 0,
    'KES' => 2,
    'KGS' => 2,
    'KHR' => 2,
    'KMF' => 0,
    'KRW' => 0,
    'KWD' => 3,
    'KYD' => 2,
    'KZT' => 2,
    'LAK' => 2,
    'LBP' => 2,
    'LKR' => 2,
    'LYD' => 3,
    'MAD' => 2,
    'MDL' => 2,
    'MKD' => 2,
    'MMK' => 2,
    'MNT' => 2,
    'MOP' => 2,
    'MRU' => 2,
    'MUR' => 2,
    'MVR' => 2,
    'MWK' => 2,
    'MXN' => 2,
    'MYR' => 2,
    'MZN' => 2,
    'NAD' => 2,
    'NGN' => 2,
    'NIO' => 2,
    'NOK' => 2,
    'NPR' => 2,
    'NZD' => 2,
    'OMR' => 3,
    'PAB' => 2,
    'PEN' => 2,
    'PGK' => 2,
    'PHP' => 2,
    'PKR' => 2,
    'PLN' => 2,
    'PYG' => 0,
    'QAR' => 2,
    'RON' => 2,
    'RSD' => 2,
    'RUB' => 2,
    'RWF' => 0,
    'SAR' => 2,
    'SBD' => 2,
    'SCR' => 2,
    'SEK' => 2,
    'SGD' => 2,
    'SHP' => 2,
    'SLE' => 2,
    'SOS' => 2,
    'SRD' => 2,
    'STN' => 2,
    'SVC' => 2,
    'SZL' => 2,
    'THB' => 2,
    'TND' => 3,
    'TOP' => 2,
    'TRY' => 2,
    'TTD' => 2,
    'TWD' => 2,
    'TZS' => 2,
    'UAH' => 2,
    'UGX' => 0,
    'USD' => 2,
    'UYU' => 2,
    'UZS' => 2,
    'VEF' => 2,
    'VND' => 0,
    'VUV' => 0,
    'WST' => 2,
    'XAF' => 0,
    'XCD' => 2,
    'XOF' => 0,
    'XPF' => 0,
    'YER' => 2,
    'ZAR' => 2,
    'ZMW' => 2,
  ];
}