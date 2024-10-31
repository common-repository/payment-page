<?php

namespace PaymentPage\ThirdPartyIntegration\Elementor\Controls;

use Elementor\Control_Base_Multiple as Elementor_Control_Base_Multiple;
use PaymentPage\PaymentGateway as PaymentGateway;

class FormFields extends Elementor_Control_Base_Multiple {

	public function get_type() {
		return 'payment_page_form_fields';
	}

	/**
	 *
	 * Used to register and enqueue custom scripts and styles used by the emoji one
	 * area control.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function enqueue() {
		wp_enqueue_style( 'payment-page-elementor-form-fields', plugins_url( 'interface/elementor/controls/form-fields/style.css', PAYMENT_PAGE_BASE_FILE_PATH ), [], PAYMENT_PAGE_VERSION );
		wp_register_script( 'payment-page-elementor-form-fields', plugins_url( 'interface/elementor/controls/form-fields/script.js', PAYMENT_PAGE_BASE_FILE_PATH ), [
			'jquery',
			'jquery-ui-sortable',
		], PAYMENT_PAGE_VERSION );
		wp_enqueue_script( 'payment-page-elementor-form-fields' );
	}

	private function get_core_fields() {
		return payment_page_form_field_map_core_fields();
	}

	public function get_default_value() {
		return $this->get_core_fields();
	}

	public function get_value( $control, $settings ) {
		$core_fields = $this->get_core_fields();
		$response    = ( $settings[ $control['name'] ] ?? [] );

		foreach ( $core_fields as $core_field_key => $core_field ) {
			if ( ! isset( $response[ $core_field_key ] ) ) {
				$response[ $core_field_key ] = $core_field;
				continue;
			}

			foreach ( $core_field as $core_field_inner_key => $core_field_inner ) {
				if ( isset( $response[ $core_field_key ][ $core_field_inner_key ] ) ) {
					continue;
				}

				$response[ $core_field_key ][ $core_field_inner_key ] = $core_field_inner;
			}
		}

		return $response;
	}

	public function content_template() {
		$control_uid = $this->get_control_uid();

		?>
        <div class="elementor-control-field payment-page-form-fields-container">
            <label for="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-title">{{{ data.label }}}</label>
            <div class="fields-repeater-container">
                <div class="field-group field-group-template">
                    <div class="payment-page-draggable-container"><i class="payment-page-draggable eicon-handle"></i>
                    </div>
                    <div class="information">
                        <input data-setting="key" type="hidden">
                        <input data-setting="type" type="hidden">
                        <input data-setting="order" type="hidden">
                        <div data-setting-container="label">
                            <label><span><?php echo __( "Label", "payment-page" ) ?></span> <input data-setting="label"
                                                                                                   class="field"
                                                                                                   type="text"></label>
                        </div>
                        <div data-setting-container="placeholder">
                            <label><span><?php echo __( "Placeholder", "payment-page" ) ?></span> <input
                                        data-setting="placeholder" class="field" type="text"></label>
                        </div>
                        <div data-setting-container="is_required">
                            <label><span><?php echo __( "Is Required", "payment-page" ) ?></span> <input
                                        data-setting="is_required" class="field" type="checkbox"></label>
                        </div>
                        <div data-setting-container="is_hidden">
                            <label><span><?php echo __( "Hide on checkout", "payment-page" ) ?></span> <input
                                        data-setting="is_hidden" class="field" type="checkbox"></label>
                        </div>
                        <div data-setting-container="size">
                            <label>
								                <?php echo __( "Width", "payment-page" ) ?>
                                <select data-setting="size">
									                <?php for ( $i = 1; $i <= 6; $i ++ ) : ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i . '/6'; ?></option>
                                  <?php endfor; ?>
                                </select>
                            </label>
                        </div>
                        <div data-setting-container="size_mobile">
                            <label>
								                <?php echo __( "Size Mobile", "payment-page" ) ?>
                                <select data-setting="size_mobile">
                                    <option value="0">Inherit</option>
									                  <?php for ( $i = 1; $i <= 6; $i ++ ) : ?>
                                      <option value="<?php echo $i; ?>"><?php echo $i . '/6'; ?></option>
									                  <?php endfor; ?>
                                </select>
                            </label>
                        </div>
                        <p class="remove-field-container"><i class="eicon-close remove-field" aria-hidden="true"></i>
                        </p>
                    </div>
                </div>
            </div>
            <div class="elementor-button-wrapper">
                <button class="elementor-button elementor-button-default add-more-options"
                        type="button">
                  <i class="eicon-plus" aria-hidden="true"></i>Add Custom Field
                </button>
            </div>
        </div>
        <# if ( data.description ) { #>
        <div class="elementor-control-field-description">{{{ data.description }}}</div>
        <# } #>
		<?php
	}
}