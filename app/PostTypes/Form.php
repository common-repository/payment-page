<?php

namespace PaymentPage\PostTypes;

use WP_Query;
use PaymentPage\Request as PaymentPage_Request;
use PaymentPage\API\PaymentPage as PaymentPage_API;

class Form {

  public function __construct() {
    add_action( 'load-edit.php', [ $this, '_load_payment_form_list' ], 5 );
    add_filter( 'replace_editor', array( $this, '_replace_editor' ), 10, 2 );
    add_filter( 'manage_' . PAYMENT_PAGE_POST_TYPE_PAYMENT_FORM . '_posts_columns', array( $this, '_manage_payment_page_form_posts_columns' ) );
    add_action( 'manage_' . PAYMENT_PAGE_POST_TYPE_PAYMENT_FORM . '_posts_custom_column', array( $this, '_manage_payment_page_form_posts_custom_column' ), 10, 2 );
    add_action( 'save_post', [ $this, '_save_post' ], 5 );
  }

  public function register_post_type() {
    $args = [
      'label'                 => __( 'Payment Form', 'payment-page' ),
      'description'           => __( 'Payment Forms can be embed anywhere via shortcode.', 'payment-page' ),
      'labels'                => [
        'name'                  => _x( 'Payment Forms', 'Post Type General Name', 'payment-page' ),
        'singular_name'         => __( 'Payment Form', 'payment-page' ),
        'menu_name'             => __( 'Payment Forms', 'payment-page' ),
        'name_admin_bar'        => __( 'Payment Forms', 'payment-page' ),
        'archives'              => __( 'Payment Form Archives', 'payment-page' ),
        'attributes'            => __( 'Payment Form Attributes', 'payment-page' ),
        'parent_item_colon'     => __( 'Parent Payment Form', 'payment-page' ),
        'all_items'             => __( 'Payment Forms', 'payment-page' ),
        'add_new_item'          => __( 'Add New Payment Form', 'payment-page' ),
        'add_new'               => __( 'Add New', 'payment-page' ),
        'new_item'              => __( 'New Payment Form', 'payment-page' ),
        'edit_item'             => __( 'Edit Payment Form', 'payment-page' ),
        'update_item'           => __( 'Update Payment Form', 'payment-page' ),
        'view_item'             => __( 'View Payment Form', 'payment-page' ),
        'view_items'            => __( 'View Payment Forms', 'payment-page' ),
        'search_items'          => __( 'Search Payment Forms', 'payment-page' ),
        'not_found'             => __( 'Not found', 'payment-page' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'payment-page' ),
        'featured_image'        => __( 'Featured Image', 'payment-page' ),
        'set_featured_image'    => __( 'Set featured image', 'payment-page' ),
        'remove_featured_image' => __( 'Remove featured image', 'payment-page' ),
        'use_featured_image'    => __( 'Use as featured image', 'payment-page' ),
        'insert_into_item'      => __( 'Insert into item', 'payment-page' ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', 'payment-page' ),
        'items_list'            => __( 'Payment Form Item List', 'payment-page' ),
        'items_list_navigation' => __( 'Payment Form Item List Navigation', 'payment-page' ),
        'filter_items_list'     => __( 'Filter items list', 'payment-page' ),
      ],
      'supports'              => [ "title", "custom-fields" ],
      'hierarchical'          => false,
      'public'                => false,
      'show_ui'               => true,
      'show_in_menu'          => PAYMENT_PAGE_MENU_SLUG,
      'menu_position'         => 2,
      'show_in_admin_bar'     => true,
      'show_in_nav_menus'     => true,
      'can_export'            => true,
      'has_archive'           => false,
      'exclude_from_search'   => true,
      'publicly_queryable'    => true,
      'capability_type'       => 'post',
      'show_in_rest'          => false,
      'taxonomies'            => array(),
    ];

    $args = apply_filters( "payment_page_register_post_type_payment_form_args", $args );

    register_post_type( PAYMENT_PAGE_POST_TYPE_PAYMENT_FORM, $args );
  }

  public function _load_payment_form_list() {
    if(!isset($_GET['post_type']))
      return;

    if($_GET['post_type'] !== PAYMENT_PAGE_POST_TYPE_PAYMENT_FORM)
      return;

    if(isset($_GET['action']) && $_GET['action'] === 'edit')
      return;

    add_action( 'admin_enqueue_scripts', function() {
      wp_enqueue_script(PAYMENT_PAGE_PREFIX . '-admin-pf',plugins_url( 'interface/app/admin-payment-form.min.js', PAYMENT_PAGE_BASE_FILE_PATH ), [ 'jquery' ], PAYMENT_PAGE_VERSION );
      wp_enqueue_style(PAYMENT_PAGE_PREFIX . '-admin-pf',plugins_url( 'interface/app/admin-payment-form.css', PAYMENT_PAGE_BASE_FILE_PATH ), [], PAYMENT_PAGE_VERSION );
    }, 5 );

    require PAYMENT_PAGE_BASE_PATH . '/templates/admin/payment-form-list.php';

    exit;
  }

  public function _replace_editor( $replace, $post ) {
    if( $post->post_type !== PAYMENT_PAGE_POST_TYPE_PAYMENT_FORM )
      return $replace;

    payment_page_register_universal_interface();

    $current_screen = get_current_screen();

    if( !empty( $current_screen ) ) {
      wp_enqueue_script(PAYMENT_PAGE_PREFIX . '-admin-pf',plugins_url( 'interface/app/admin-payment-form.min.js', PAYMENT_PAGE_BASE_FILE_PATH ), [ 'jquery' ], PAYMENT_PAGE_VERSION );
      wp_enqueue_style(PAYMENT_PAGE_PREFIX . '-admin-pf',plugins_url( 'interface/app/admin-payment-form.css', PAYMENT_PAGE_BASE_FILE_PATH ), [], PAYMENT_PAGE_VERSION );

      if( $current_screen->action === 'add' )
        require PAYMENT_PAGE_BASE_PATH . '/templates/admin/payment-form-add.php';
      else
        require PAYMENT_PAGE_BASE_PATH . '/templates/admin/payment-form-edit.php';
    }

    return true;
  }

  public function _manage_payment_page_form_posts_columns( $columns ) {
    $response = [];

    if(isset($columns['title']))
      $response['title'] = $columns['title'];

    $response['payment_page_shortcode'] = __( 'Shortcode', 'payment-page' );

    if(isset($columns['date']))
      $response['date'] = $columns['date'];

    return $response;
  }

  public function _manage_payment_page_form_posts_custom_column( $column, $post_id ) {
    if( $column === 'payment_page_shortcode' ) {
      echo '<div data-payment-page-library="clipboard">' . '[payment-page-payment-form id="' . $post_id . '"]' . '</div>';

      payment_page_register_universal_interface();
    }
  }

  public function _save_post( $post_id ) {
    if( get_post_type( $post_id ) !== PAYMENT_PAGE_POST_TYPE_PAYMENT_FORM )
      return;

    if(isset($_POST['payment_page_update_post_status'])) {
      $status = $_POST['payment_page_update_post_status'];

      if($status === 'publish' && get_post_status($post_id) !== 'publish')
        wp_update_post( [ 'ID' => $post_id, 'post_status' => 'publish' ] );
      else if($status === 'draft' && get_post_status($post_id) !== 'draft')
        wp_update_post( [ 'ID' => $post_id, 'post_status' => 'draft' ] );
    }

    if(isset($_POST['payment_page_template_id']) && !empty($_POST['payment_page_template_id'])) {
      $payment_page_templates = PaymentPage_API::instance()->get_form_import_templates();

      // Not my nicest code, might need to be cleaned up later.
      if(!empty($payment_page_templates)) {
        foreach( $payment_page_templates as $payment_page_template ) {
          if( intval($payment_page_template[ 'id' ]) === intval($_POST['payment_page_template_id']) ) {
            update_post_meta( $post_id, 'payment_page_settings', $payment_page_template[ 'settings' ] );
          }
        }
      }
    }

    if(!isset($_POST['payment_page_settings']))
      return;

    $settings = $_POST['payment_page_settings'];
    $settings = payment_page_administration_process_request_payment_page_settings( $settings );

    update_post_meta( $post_id, 'payment_page_settings', $settings );
  }

}