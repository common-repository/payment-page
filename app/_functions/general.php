<?php

function payment_page_setting_get($option_name ) {
  return PaymentPage\Settings::instance()->get( $option_name );
}

function payment_page_template_load( string $template_name, array $args = [], string $template_path = '', string $default_path = '' ) {
  PaymentPage\Template::load_template( $template_name, $args, $template_path, $default_path );
}

function payment_page_encrypt( $string, $secret_key, $secret_iv ): string {
  $key = hash('sha256',$secret_key);
  $iv = substr(hash('sha256',$secret_iv),0,16);

  return base64_encode(openssl_encrypt($string,"AES-256-CBC",$key,0,$iv));;
}

function payment_page_decrypt( $string, $secret_key, $secret_iv ) :string {
  $key = hash('sha256',$secret_key);
  $iv = substr(hash('sha256',$secret_iv),0,16);

  return openssl_decrypt(base64_decode($string),"AES-256-CBC",$key,0,$iv);
}

function payment_page_domain_name() {
  return str_replace( [ 'https://www.', 'https://', 'http://www.', 'http://' ], '', rtrim( get_site_url(), '/' ) );
}

function _payment_page_payment_identification_fields( $post_id, $payment_id ) {
  return [
    'payment_page_payment_id' => intval( $payment_id ),
    'payment_page_url'        => get_the_permalink( intval( $post_id ) ),
    'payment_page_id'         => intval( $post_id ),
    'domain_name'             => payment_page_domain_name()
  ];
}

function _payment_page_rest_api_custom_fields( \WP_REST_Request $request, $key = 'custom_fields' ) {
  $response = [];

  if( $request->has_param( $key ) ) {
    $custom_fields = $request->get_param( $key );

    if( is_array( $custom_fields ) && !empty( $custom_fields ) ) {
      foreach( $custom_fields as $custom_field_key => $custom_field_value )
        $response[ payment_page_label_to_alias( sanitize_text_field( $custom_field_key ) ) ] = sanitize_text_field( $custom_field_value );
    }
  }

  return $response;
}

/**
 * @return wpdb
 */
function payment_page_wpdb(): wpdb {
  global $wpdb;

  return $wpdb;
}

/**
 * @param $query
 * @return array
 */
function payment_page_dbDelta( $query ) {
  if( !function_exists( 'dbDelta' ) )
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

  return dbDelta( $query );
}

/**
 * Can be later abstracted to return a different version number for WP_DEBUG or setting.
 * @return string
 */
function payment_page_frontend_file_version() :string {
  return ( defined( 'WP_DEBUG' ) && WP_DEBUG ? '' . time() : PAYMENT_PAGE_VERSION );
}

function payment_page_frontend_configuration() :array {
  return [
    'user_id'               => get_current_user_id(),
    'is_user_logged_in'     => ( is_user_logged_in() ? 1 : 0 ),
    'is_user_administrator' => ( current_user_can( PAYMENT_PAGE_ADMIN_CAP ) ? 1 : 0 ),
    'is_https'              => ( wp_is_using_https() ? 1 : 0 ),
    'domain_url'            => esc_url( ( isset( $_SERVER['HTTPS'] ) ? "https" : "http" ) . "://" . $_SERVER["HTTP_HOST"] ),
    'site_url'              => get_site_url(),
    'rest_url'              => esc_url_raw( rest_url() ),
    'rest_nonce'            => wp_create_nonce( 'wp_rest' ),
    'file_version'          => payment_page_frontend_file_version(),
    'library_url'           => plugin_dir_url( PAYMENT_PAGE_BASE_FILE_PATH ) . 'interface/app/',
    'component_injection'   => [],
    'template_extra'        => [],
    'svg_icon_map'          => [
      'angle_down'          => '<svg data-payment-page-svg-type="fill" width="36" height="20" viewBox="0 0 36 20" xmlns="http://www.w3.org/2000/svg">
                                  <path d="M16.8049 19.525L0.495604 3.70898C-0.165205 3.07795 -0.165205 2.05756 0.495604 1.42653L1.49385 0.473272C2.15466 -0.157759 3.2232 -0.157759 3.88401 0.473272L18 14.2083L32.116 0.486699C32.7768 -0.144332 33.8453 -0.144332 34.5062 0.486699L35.5044 1.43996C36.1652 2.07099 36.1652 3.09138 35.5044 3.72241L19.1951 19.5385C18.5343 20.1561 17.4657 20.1561 16.8049 19.525Z"/>
                                </svg>',
      'copy'                => '<svg data-payment-page-svg-type="fill" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                  <path d="M433.941 65.941l-51.882-51.882A48 48 0 0 0 348.118 0H176c-26.51 0-48 21.49-48 48v48H48c-26.51 0-48 21.49-48 48v320c0 26.51 21.49 48 48 48h224c26.51 0 48-21.49 48-48v-48h80c26.51 0 48-21.49 48-48V99.882a48 48 0 0 0-14.059-33.941zM352 32.491a15.88 15.88 0 0 1 7.431 4.195l51.882 51.883A15.885 15.885 0 0 1 415.508 96H352V32.491zM288 464c0 8.822-7.178 16-16 16H48c-8.822 0-16-7.178-16-16V144c0-8.822 7.178-16 16-16h80v240c0 26.51 21.49 48 48 48h112v48zm128-96c0 8.822-7.178 16-16 16H176c-8.822 0-16-7.178-16-16V48c0-8.822 7.178-16 16-16h144v72c0 13.2 10.8 24 24 24h72v240z"/>
                                </svg>',
      'circle_checkmark'    => '<svg data-payment-page-svg-type="fill" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                  <path d="M0.15 12C0.15 5.46604 5.46604 0.15 12 0.15C18.534 0.15 23.85 5.46604 23.85 12C23.85 18.534 18.534 23.85 12 23.85C5.46604 23.85 0.15 18.534 0.15 12ZM2.03182 12C2.03182 17.4966 6.50334 21.9682 12 21.9682C17.4966 21.9682 21.9682 17.4966 21.9682 12C21.9682 6.50341 17.4967 2.03182 12 2.03182C6.50334 2.03182 2.03182 6.50341 2.03182 12Z"/>
                                  <path d="M6.06349 11.2059C6.0635 11.2059 6.0635 11.2059 6.0635 11.2059C6.43096 10.8385 7.02674 10.8386 7.39413 11.206L10.2225 14.0344L10.3286 14.1405L10.4346 14.0344L16.6058 7.8633C16.9733 7.49586 17.569 7.4959 17.9365 7.86337C18.3039 8.23083 18.3039 8.82655 17.9365 9.194L10.9939 16.1365C10.8174 16.3129 10.5781 16.412 10.3286 16.412H10.3286H10.3286H10.3285H10.3285H10.3285H10.3285H10.3285H10.3285H10.3285H10.3285C10.079 16.412 9.83968 16.3129 9.66322 16.1364L6.0635 12.5366L5.95744 12.6426M6.06349 11.2059L5.95744 11.0999C5.5314 11.5259 5.5314 12.2166 5.95744 12.6426M6.06349 11.2059C5.69604 11.5734 5.69605 12.1691 6.0635 12.5366L5.95744 12.6426M6.06349 11.2059L5.95744 12.6426"/>
                                </svg>',
      'element_order'           => '<svg data-payment-page-svg-type="fill" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                      <defs>
                                        <clipPath id="clip-Organize">
                                          <rect width="24" height="24"/>
                                        </clipPath>
                                      </defs>
                                      <g id="Organize" clip-path="url(#clip-Organize)">
                                        <path id="arrows-alt-solid" d="M16.509,19.958,12.8,23.67a1.125,1.125,0,0,1-1.591,0L7.493,19.958a1.125,1.125,0,0,1,.8-1.92h2.4V13.312H5.962v2.4a1.125,1.125,0,0,1-1.921.8L.329,12.793a1.125,1.125,0,0,1,0-1.591L4.041,7.49a1.125,1.125,0,0,1,1.921.8v2.4h4.725V5.962h-2.4a1.125,1.125,0,0,1-.8-1.921L11.2.329a1.125,1.125,0,0,1,1.591,0l3.712,3.712a1.125,1.125,0,0,1-.8,1.921h-2.4v4.725h4.725v-2.4a1.125,1.125,0,0,1,1.92-.8l3.712,3.712a1.125,1.125,0,0,1,0,1.591l-3.712,3.712a1.125,1.125,0,0,1-1.92-.8v-2.4H13.312v4.725h2.4a1.125,1.125,0,0,1,.8,1.921Z" transform="translate(0.001 0.001)"/>
                                      </g>
                                    </svg>',
    ],
    'logo'                  => plugins_url( 'interface/img/logo.gif', PAYMENT_PAGE_BASE_FILE_PATH ),
    'loader_icon'           => plugins_url( 'interface/img/loader-icon.gif', PAYMENT_PAGE_BASE_FILE_PATH ),
    'libraries'             => [
      'inputmask' => plugins_url( 'interface/app/third-party/jquery-inputmask/jquery.inputmask.min.js', PAYMENT_PAGE_BASE_FILE_PATH ),
      'clipboard' => plugins_url( 'interface/app/third-party/clipboard/clipboard.min.js', PAYMENT_PAGE_BASE_FILE_PATH ),
      'sortable'  => plugins_url( 'interface/app/third-party/sortable/sortable.min.js', PAYMENT_PAGE_BASE_FILE_PATH ),
      'pickr'               => [
        plugins_url( 'interface/app/third-party/pickr/nano.min.css', PAYMENT_PAGE_BASE_FILE_PATH ),
        plugins_url( 'interface/app/third-party/pickr/pickr.min.js', PAYMENT_PAGE_BASE_FILE_PATH )
      ],
      'select2'               => [
        plugins_url( 'interface/app/third-party/select2/select2.min.css', PAYMENT_PAGE_BASE_FILE_PATH ),
        plugins_url( 'interface/app/third-party/select2/select2.full.min.js', PAYMENT_PAGE_BASE_FILE_PATH )
      ],
    ],
    'currency_code_to_decimal_map' => payment_page_currency_code_to_decimal_map()
  ];
}

function payment_page_frontend_language() :array {
  return [
    'no_results_response'  => __( "No results found.", "payment-page" ),
    'cancelled_request'    => __( "Request cancelled", "payment-page" ),
    'asset_failed_fetch'   => __( "Failed to fetch asset required to display this section, please refresh and try again, if this error persists, contact support for assistance.", "payment-page" ),
    'delete'               => __( "Delete", "payment-page" ),
    'add'                  => __( "Add", "payment-page" ),
  ];
}

function payment_page_register_universal_interface() {
  if( wp_script_is( PAYMENT_PAGE_PREFIX, 'enqueued' ) )
    return;

  $file_version = payment_page_frontend_file_version();

  wp_register_style(PAYMENT_PAGE_PREFIX,plugins_url( 'interface/app/style.css', PAYMENT_PAGE_BASE_FILE_PATH ), [], $file_version );
  wp_enqueue_style(PAYMENT_PAGE_PREFIX );

  wp_register_script( PAYMENT_PAGE_PREFIX, plugins_url( 'interface/app/app.min.js', PAYMENT_PAGE_BASE_FILE_PATH ), [ 'jquery', 'wp-util', 'lodash' ], $file_version, true );

  wp_localize_script( PAYMENT_PAGE_PREFIX, 'payment_page_data', [
    'configuration' => payment_page_frontend_configuration(),
    'lang'          => payment_page_frontend_language()
  ] );

  wp_enqueue_script( PAYMENT_PAGE_PREFIX );
}

function _payment_page_refresh_rewrite_rules_and_capabilities() {
  global $wp_roles;

  $capabilities = [
    PAYMENT_PAGE_ADMIN_CAP
  ];

  foreach( $capabilities as $capability )
    $wp_roles->add_cap( 'administrator', $capability );

  try {
    flush_rewrite_rules();
  } catch ( \Exception $exception ) {

  }
}

function payment_page_form_submit_actions_assoc() {
  $submit_actions = array(
    'email'           => 'Email',
    'redirect_to'     => 'Redirect',
    'dynamic_message' => __( 'Dynamic Message', 'payment-page' ),
    'http_request'    => __( 'HTTP Request', 'payment-page' ),
  );

  return $submit_actions;
}

function payment_page_font_list() {
  return [ 'Arial', 'Verdana', 'Tahoma', 'Trebuchet MS', 'Times New Roman', 'Georgia', 'Garamond', 'Courier New', 'Brush Script MT' ];
}

function payment_page_font_list_assoc() {
  return array_combine( payment_page_font_list(), payment_page_font_list() );
}

/**
 * This is the US List of Stripe supported currencies, which was previously returned through API call.
 * @return string[]
 */
function payment_page_currencies() :array {
  return [
    'usd', 'aed', 'afn', 'all', 'amd', 'ang', 'aoa', 'ars', 'aud', 'awg', 'azn', 'bam', 'bbd', 'bdt', 'bgn', 'bif', 'bmd', 'bnd', 'bob', 'brl', 'bsd', 'bwp',
    'byn', 'bzd', 'cad', 'cdf', 'chf', 'clp', 'cny', 'cop', 'crc', 'cve', 'czk', 'djf', 'dkk', 'dop', 'dzd', 'egp', 'etb', 'eur', 'fjd', 'fkp', 'gbp', 'gel',
    'gip', 'gmd', 'gnf', 'gtq', 'gyd', 'hkd', 'hnl', 'hrk', 'htg', 'huf', 'idr', 'ils', 'inr', 'isk', 'jmd', 'jpy', 'kes', 'kgs', 'khr', 'kmf', 'krw', 'kyd',
    'kzt', 'lak', 'lbp', 'lkr', 'lrd', 'lsl', 'mad', 'mdl', 'mga', 'mkd', 'mmk', 'mnt', 'mop', 'mro', 'mur', 'mvr', 'mwk', 'mxn', 'myr', 'mzn', 'nad', 'ngn',
    'nio', 'nok', 'npr', 'nzd', 'pab', 'pen', 'pgk', 'php', 'pkr', 'pln', 'pyg', 'qar', 'ron', 'rsd', 'rub', 'rwf', 'sar', 'sbd', 'scr', 'sek', 'sgd', 'shp',
    'sll', 'sos', 'srd', 'std', 'szl', 'thb', 'tjs', 'top', 'try', 'ttd', 'twd', 'tzs', 'uah', 'ugx', 'uyu', 'uzs', 'vnd', 'vuv', 'wst', 'xaf', 'xcd', 'xof',
    'xpf', 'yer', 'zar', 'zmw'
  ];
}

function payment_page_form_field_map_core_fields() {
  $fields = [
    "email_address" => [
      'label'       => 'Email Address',
      'placeholder' => 'Email Address',
      'name'        => 'email_address',
      'order'       => 1,
      'type'        => 'core',
      'size'        => 6,
      'size_mobile' => 0,
    ],
    "first_name"    => [
      'label'       => 'First Name',
      'placeholder' => 'First Name',
      'name'        => 'first_name',
      'order'       => 2,
      'type'        => 'core',
      'size'        => 3,
      'size_mobile' => 0,
    ],
    "last_name"     => [
      'label'       => 'Last Name',
      'placeholder' => 'Last Name',
      'name'        => 'last_name',
      'type'        => 'core',
      'size'        => 3,
      'size_mobile' => 0,
      'order'       => 3,
    ],
  ];

  if ( PaymentPage\PaymentGateway::get_integration( 'stripe' )->is_configured()
        && in_array( 'ccard', payment_page_setting_get( 'stripe_payment_methods' ) ) ) {
    $fields += [
      "card_number"          => [
        'label'       => 'Card Number',
        'name'        => 'card_number',
        'type'        => 'payment_method_card',
        'size'        => 6,
        'size_mobile' => 0,
        'order'       => 4,
      ],
      "card_expiration_date" => [
        'label'       => 'Expiration Date',
        'name'        => 'card_expiration_date',
        'type'        => 'payment_method_card',
        'size'        => 2,
        'size_mobile' => 0,
        'order'       => 5,
      ],
      "card_cvc"             => [
        'label'       => 'CVC',
        'name'        => 'card_cvc',
        'type'        => 'payment_method_card',
        'size'        => 2,
        'size_mobile' => 0,
        'order'       => 6,
      ],
      "card_zip_code"        => [
        'label'       => 'ZIP',
        'name'        => 'card_zip_code',
        'type'        => 'payment_method_card',
        'size'        => 2,
        'size_mobile' => 0,
        'is_hidden'   => 0,
        'order'       => 7,
      ],
    ];
  }

  if ( PaymentPage\PaymentGateway::get_integration( 'stripe' )->is_configured()
        && in_array( 'sepa', payment_page_setting_get( 'stripe_payment_methods' ) ) ) {
    $fields += [
      "iban" => [
        'label'       => 'IBAN',
        'name'        => 'iban',
        'type'        => 'payment_method_iban',
        'size'        => 6,
        'size_mobile' => 0,
        'order'       => 7,
      ],
    ];
  }

  return $fields;
}