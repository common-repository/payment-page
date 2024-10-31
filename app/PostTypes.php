<?php

namespace PaymentPage;

use WP_Query;
use PaymentPage\PostTypes\Form as FormPostType;

class PostTypes {

  /**
   * @var null|PostTypes;
   */
  protected static $_instance = null;

  /**
   * @return PostTypes
   */
  public static function instance(): PostTypes {
    if (self::$_instance === null) {
      self::$_instance = new self();
    }

    return self::$_instance;
  }

  public ?FormPostType $form = null;

  public function setup() {
    $this->form = new FormPostType();

    add_action( 'init', array( $this, '_register_post_types' ), 10 );
  }

  public function _register_post_types() {
    $this->form->register_post_type();
  }

}