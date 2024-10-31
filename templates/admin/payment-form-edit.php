<?php
/**
 * Post advanced form for inclusion in the administration panels.
 *
 * @package WordPress
 * @subpackage Administration
 */

add_filter( 'screen_options_show_screen', '__return_false' );

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
  die( '-1' );
}

/**
 * @global string       $post_type
 * @global WP_Post_Type $post_type_object
 * @global WP_Post      $post             Global post object.
 */
global $post_type, $post_type_object, $post;

// Flag that we're not loading the block editor.
$current_screen = get_current_screen();
$current_screen->is_block_editor( false );

if ( is_multisite() ) {
  add_action( 'admin_footer', '_admin_notice_post_locked' );
} else {
  $check_users = get_users(
    array(
      'fields' => 'ID',
      'number' => 2,
    )
  );

  if ( count( $check_users ) > 1 ) {
    add_action( 'admin_footer', '_admin_notice_post_locked' );
  }

  unset( $check_users );
}

wp_enqueue_script( 'post' );

$_wp_editor_expand   = false;
$_content_editor_dfw = false;

if ( post_type_supports( $post_type, 'editor' )
  && ! wp_is_mobile()
  && ! ( $is_IE && preg_match( '/MSIE [5678]/', $_SERVER['HTTP_USER_AGENT'] ) )
) {
  /**
   * Filters whether to enable the 'expand' functionality in the post editor.
   *
   * @since 4.0.0
   * @since 4.1.0 Added the `$post_type` parameter.
   *
   * @param bool   $expand    Whether to enable the 'expand' functionality. Default true.
   * @param string $post_type Post type.
   */
  if ( apply_filters( 'wp_editor_expand', true, $post_type ) ) {
    wp_enqueue_script( 'editor-expand' );
    $_content_editor_dfw = true;
    $_wp_editor_expand   = ( 'on' === get_user_setting( 'editor_expand', 'on' ) );
  }
}

if ( wp_is_mobile() ) {
  wp_enqueue_script( 'jquery-touch-punch' );
}

/**
 * Post ID global
 *
 * @name $post_ID
 * @var int
 */
$post_ID = isset( $post_ID ) ? (int) $post_ID : 0;
$user_ID = isset( $user_ID ) ? (int) $user_ID : 0;
$action  = isset( $action ) ? $action : '';

if ( (int) get_option( 'page_for_posts' ) === $post->ID && empty( $post->post_content ) ) {
  add_action( 'edit_form_after_title', '_wp_posts_page_notice' );
  remove_post_type_support( $post_type, 'editor' );
}

$thumbnail_support = current_theme_supports( 'post-thumbnails', $post_type ) && post_type_supports( $post_type, 'thumbnail' );
if ( ! $thumbnail_support && 'attachment' === $post_type && $post->post_mime_type ) {
  if ( wp_attachment_is( 'audio', $post ) ) {
    $thumbnail_support = post_type_supports( 'attachment:audio', 'thumbnail' ) || current_theme_supports( 'post-thumbnails', 'attachment:audio' );
  } elseif ( wp_attachment_is( 'video', $post ) ) {
    $thumbnail_support = post_type_supports( 'attachment:video', 'thumbnail' ) || current_theme_supports( 'post-thumbnails', 'attachment:video' );
  }
}

if ( $thumbnail_support ) {
  add_thickbox();
  wp_enqueue_media( array( 'post' => $post->ID ) );
}

// Add the local autosave notice HTML.
add_action( 'admin_footer', '_local_storage_notice' );

/*
 * @todo Document the $messages array(s).
 */
$permalink = get_permalink( $post->ID );
if ( ! $permalink ) {
  $permalink = '';
}

$messages = array();

$preview_post_link_html   = '';
$scheduled_post_link_html = '';
$view_post_link_html      = '';

$preview_page_link_html   = '';
$scheduled_page_link_html = '';
$view_page_link_html      = '';

$preview_url = get_preview_post_link( $post );

$viewable = is_post_type_viewable( $post_type_object );

if ( $viewable ) {

  // Preview post link.
  $preview_post_link_html = sprintf(
    ' <a target="_blank" href="%1$s">%2$s</a>',
    esc_url( $preview_url ),
    __( 'Preview post' )
  );

  // Scheduled post preview link.
  $scheduled_post_link_html = sprintf(
    ' <a target="_blank" href="%1$s">%2$s</a>',
    esc_url( $permalink ),
    __( 'Preview post' )
  );

  // View post link.
  $view_post_link_html = sprintf(
    ' <a href="%1$s">%2$s</a>',
    esc_url( $permalink ),
    __( 'View post' )
  );

  // Preview page link.
  $preview_page_link_html = sprintf(
    ' <a target="_blank" href="%1$s">%2$s</a>',
    esc_url( $preview_url ),
    __( 'Preview page' )
  );

  // Scheduled page preview link.
  $scheduled_page_link_html = sprintf(
    ' <a target="_blank" href="%1$s">%2$s</a>',
    esc_url( $permalink ),
    __( 'Preview page' )
  );

  // View page link.
  $view_page_link_html = sprintf(
    ' <a href="%1$s">%2$s</a>',
    esc_url( $permalink ),
    __( 'View page' )
  );

}

$scheduled_date = sprintf(
/* translators: Publish box date string. 1: Date, 2: Time. */
  __( '%1$s at %2$s' ),
  /* translators: Publish box date format, see https://www.php.net/manual/datetime.format.php */
  date_i18n( _x( 'M j, Y', 'publish box date format' ), strtotime( $post->post_date ) ),
  /* translators: Publish box time format, see https://www.php.net/manual/datetime.format.php */
  date_i18n( _x( 'H:i', 'publish box time format' ), strtotime( $post->post_date ) )
);

$messages['post']       = array(
  0  => '', // Unused. Messages start at index 1.
  1  => __( 'Post updated.' ) . $view_post_link_html,
  2  => __( 'Custom field updated.' ),
  3  => __( 'Custom field deleted.' ),
  4  => __( 'Post updated.' ),
  /* translators: %s: Date and time of the revision. */
  5  => isset( $_GET['revision'] ) ? sprintf( __( 'Post restored to revision from %s.' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
  6  => __( 'Post published.' ) . $view_post_link_html,
  7  => __( 'Post saved.' ),
  8  => __( 'Post submitted.' ) . $preview_post_link_html,
  /* translators: %s: Scheduled date for the post. */
  9  => sprintf( __( 'Post scheduled for: %s.' ), '<strong>' . $scheduled_date . '</strong>' ) . $scheduled_post_link_html,
  10 => __( 'Post draft updated.' ) . $preview_post_link_html,
);
$messages['page']       = array(
  0  => '', // Unused. Messages start at index 1.
  1  => __( 'Page updated.' ) . $view_page_link_html,
  2  => __( 'Custom field updated.' ),
  3  => __( 'Custom field deleted.' ),
  4  => __( 'Page updated.' ),
  /* translators: %s: Date and time of the revision. */
  5  => isset( $_GET['revision'] ) ? sprintf( __( 'Page restored to revision from %s.' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
  6  => __( 'Page published.' ) . $view_page_link_html,
  7  => __( 'Page saved.' ),
  8  => __( 'Page submitted.' ) . $preview_page_link_html,
  /* translators: %s: Scheduled date for the page. */
  9  => sprintf( __( 'Page scheduled for: %s.' ), '<strong>' . $scheduled_date . '</strong>' ) . $scheduled_page_link_html,
  10 => __( 'Page draft updated.' ) . $preview_page_link_html,
);
$messages['attachment'] = array_fill( 1, 10, __( 'Media file updated.' ) ); // Hack, for now.

/**
 * Filters the post updated messages.
 *
 * @since 3.0.0
 *
 * @param array[] $messages Post updated messages. For defaults see `$messages` declarations above.
 */
$messages = apply_filters( 'post_updated_messages', $messages );

$message = false;
if ( isset( $_GET['message'] ) ) {
  $_GET['message'] = absint( $_GET['message'] );
  if ( isset( $messages[ $post_type ][ $_GET['message'] ] ) ) {
    $message = $messages[ $post_type ][ $_GET['message'] ];
  } elseif ( ! isset( $messages[ $post_type ] ) && isset( $messages['post'][ $_GET['message'] ] ) ) {
    $message = $messages['post'][ $_GET['message'] ];
  }
}

$notice     = false;
$form_extra = '';
if ( 'auto-draft' === $post->post_status ) {
  if ( 'edit' === $action ) {
    $post->post_title = '';
  }
  $autosave    = false;
  $form_extra .= "<input type='hidden' id='auto_draft' name='auto_draft' value='1' />";
} else {
  $autosave = wp_get_post_autosave( $post->ID );
}

$form_action  = 'editpost';
$nonce_action = 'update-post_' . $post->ID;
$form_extra  .= "<input type='hidden' id='post_ID' name='post_ID' value='" . esc_attr( $post->ID ) . "' />";

// Detect if there exists an autosave newer than the post and if that autosave is different than the post.
if ( $autosave && mysql2date( 'U', $autosave->post_modified_gmt, false ) > mysql2date( 'U', $post->post_modified_gmt, false ) ) {
  foreach ( _wp_post_revision_fields( $post ) as $autosave_field => $_autosave_field ) {
    if ( normalize_whitespace( $autosave->$autosave_field ) !== normalize_whitespace( $post->$autosave_field ) ) {
      $notice = sprintf(
      /* translators: %s: URL to view the autosave. */
        __( 'There is an autosave of this post that is more recent than the version below. <a href="%s">View the autosave</a>' ),
        get_edit_post_link( $autosave->ID )
      );
      break;
    }
  }
  // If this autosave isn't different from the current post, begone.
  if ( ! $notice ) {
    wp_delete_post_revision( $autosave->ID );
  }
  unset( $autosave_field, $_autosave_field );
}

$post_type_object = get_post_type_object( $post_type );

// All meta boxes should be defined and added before the first do_meta_boxes() call (or potentially during the do_meta_boxes action).
require_once ABSPATH . 'wp-admin/includes/meta-boxes.php';

require_once ABSPATH . 'wp-admin/admin-header.php';

$post_status_list = [
  'draft'     => [
    'title'       => __( 'Draft', 'payment-page' ),
    'post_status' => 'draft',
  ],
  'publish'   => [
    'title'       => __( 'Active', 'payment-page' ),
    'post_status' => 'publish',
  ]
];
?>

  <div class="wrap">
    <?php payment_page_template_load( 'admin/header.php' );?>

    <form name="post" action="post.php" method="post" id="post"
      <?php
      /**
       * Fires inside the post editor form tag.
       *
       * @since 3.0.0
       *
       * @param WP_Post $post Post object.
       */
      do_action( 'post_edit_form_tag', $post );

      $referer = wp_get_referer();
      ?>
    >
    <div class="payment-page-header-container">
      <h1 class="wp-heading-inline">
        <?php echo esc_html( $title ); ?>
      </h1>

      <div class="payment-page-publish-container">
        <select name="payment_page_update_post_status" id="payment_page_update_post_status">
          <?php foreach( $post_status_list as $status ) : ?>
            <option <?php payment_page_utility_selected( $post->post_status, $status[ 'post_status'] ); ?>
                value="<?php echo $status[ 'post_status' ]; ?>"><?php echo $status[ 'title' ]; ?></option>
          <?php endforeach; ?>
        </select>

        <div id="publishing-action">
          <span class="spinner"></span>
          <?php submit_button( __( 'Update' ), 'primary large', 'save', false, array( 'id' => 'publish-visible' ) ); ?>
        </div>
      </div>
    </div>

    <?php
    if ( isset( $post_new_file ) && current_user_can( $post_type_object->cap->create_posts ) ) {
      echo ' <a href="' . esc_url( admin_url( $post_new_file ) ) . '" class="page-title-action">' . esc_html( $post_type_object->labels->add_new ) . '</a>';
    }
    ?>

    <hr class="wp-header-end">

    <?php
    /**
    if ( $notice ) :
      wp_admin_notice(
        '<p id="has-newer-autosave">' . $notice . '</p>',
        array(
          'type'           => 'warning',
          'id'             => 'notice',
          'paragraph_wrap' => false,
        )
      );
    endif;
    if ( $message ) :
      wp_admin_notice(
        $message,
        array(
          'type'               => 'success',
          'dismissible'        => true,
          'id'                 => 'message',
          'additional_classes' => array( 'updated' ),
        )
      );
    endif;
    **/

    $connection_lost_message = sprintf(
      '<span class="spinner"></span> %1$s <span class="hide-if-no-sessionstorage">%2$s</span>',
      __( '<strong>Connection lost.</strong> Saving has been disabled until you are reconnected.' ),
      __( 'This post is being backed up in your browser, just in case.' )
    );

    wp_admin_notice(
      $connection_lost_message,
      array(
        'id'                 => 'lost-connection-notice',
        'additional_classes' => array( 'error', 'hidden' ),
      )
    );
    ?>
      <?php wp_nonce_field( $nonce_action ); ?>
      <input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_ID; ?>" />
      <input type="hidden" id="hiddenaction" name="action" value="<?php echo esc_attr( $form_action ); ?>" />
      <input type="hidden" id="originalaction" name="originalaction" value="<?php echo esc_attr( $form_action ); ?>" />
      <input type="hidden" id="post_author" name="post_author" value="<?php echo esc_attr( $post->post_author ); ?>" />
      <input type="hidden" id="post_type" name="post_type" value="<?php echo esc_attr( $post_type ); ?>" />
      <input type="hidden" id="original_post_status" name="original_post_status" value="<?php echo esc_attr( $post->post_status ); ?>" />
      <input type="hidden" id="referredby" name="referredby" value="<?php echo $referer ? esc_url( $referer ) : ''; ?>" />
      <?php if ( ! empty( $active_post_lock ) ) { ?>
        <input type="hidden" id="active_post_lock" value="<?php echo esc_attr( implode( ':', $active_post_lock ) ); ?>" />
        <?php
      }
      if ( 'draft' !== get_post_status( $post ) ) {
        wp_original_referer_field( true, 'previous' );
      }

      echo $form_extra;

      wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
      wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
      ?>

      <?php
      /**
       * Fires at the beginning of the edit form.
       *
       * At this point, the required hidden fields and nonces have already been output.
       *
       * @since 3.7.0
       *
       * @param WP_Post $post Post object.
       */
      do_action( 'edit_form_top', $post );
      ?>

      <div id="poststuff">
        <div id="post-body" class="columns-1">
          <div id="post-body-content">

            <?php if ( post_type_supports( $post_type, 'title' ) ) { ?>
              <div id="titlediv">
                <div id="titlewrap">
                  <?php
                  /**
                   * Filters the title field placeholder text.
                   *
                   * @since 3.1.0
                   *
                   * @param string  $text Placeholder text. Default 'Add title'.
                   * @param WP_Post $post Post object.
                   */
                  $title_placeholder = apply_filters( 'enter_title_here', __( 'Add title' ), $post );
                  ?>
                  <label class="screen-reader-text" id="title-prompt-text" for="title"><?php echo $title_placeholder; ?></label>
                  <input type="text" name="post_title" size="30" value="<?php echo esc_attr( $post->post_title ); ?>" id="title" spellcheck="true" autocomplete="off" class="edit-page"/>
                </div>
              </div><!-- /titlediv -->
              <?php
            }
            /**
             * Fires after the title field.
             *
             * @since 3.5.0
             *
             * @param WP_Post $post Post object.
             */
            do_action( 'edit_form_after_title', $post );
            ?>
            <div class="payment-page-form-builder-wrapper">
              <?php echo '<div data-payment-page-component="builder" data-payment-page-component-action="' . esc_attr( PAYMENT_PAGE_REST_API_PREFIX . '/v1/administration/payment-forms/' . get_the_ID() . '/builder-data' ) . '"></div>';?>

              <input type="hidden" name="save" id="publish" class="button button-primary button-large" value="Update">
            </div>
          </div><!-- /post-body-content -->
        </div><!-- /post-body -->
      </div><!-- /poststuff -->
    </form>

    <div data-payment-page-component="admin-marketing-notifications"
         data-payment-page-component-args="<?php echo esc_attr( json_encode( [ 'area_slug' => 'form-builder' ] ) ); ?>"></div>
  </div>

  <style>
    /** @todo clean this up and make it work **/
    .elementor-control-pp_upgrade_section {

    }

    .elementor-control-pp_personal_plan .elementor-control-title,
    .elementor-control-pp_pro_plan .elementor-control-title,
    .elementor-control-pp_agency_plan .elementor-control-title {
      font-size : 16px !important;
      margin: 0 0 10px 0;
    }

    .elementor-control-pp_personal_plan .elementor-control-raw-html a.elementor-button {
      padding: 5px 10px;
    }

    .elementor-control-pp_personal_plan .elementor-control-raw-html ul,
    .elementor-control-pp_pro_plan .elementor-control-raw-html ul,
    .elementor-control-pp_agency_plan .elementor-control-raw-html ul {
      padding : 0 0 0 15px !important;
    }

    .elementor-control-pp_personal_plan .elementor-control-raw-html ul > li,
    .elementor-control-pp_pro_plan .elementor-control-raw-html ul > li,
    .elementor-control-pp_agency_plan .elementor-control-raw-html ul > li {
      font-size : 13px !important;
      list-style-type: inherit !important;
      margin : 0 0 10px 0 !important;
    }

    .elementor-control-pp_roadmap {

    }
  </style>

<script>
  jQuery("#publish-visible").off( "click" ).on( "click", function( event ) {
    event.preventDefault();
    event.stopImmediatePropagation();

    jQuery("#publish").parents("form:first").trigger("submit");
  })
</script>

<?php if ( ! wp_is_mobile() && post_type_supports( $post_type, 'title' ) && '' === $post->post_title ) : ?>
  <script type="text/javascript">
    try{document.post.title.focus();}catch(e){}
  </script>
<?php endif; ?>