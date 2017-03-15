<?php

namespace Carbon_Fields\Field;

class Number_Field extends Field {
	/**
	 * template()
	 *
	 * Prints the main Underscore template
	 **/

	protected $default_min = 1;
	protected $default_max = 2147483647;
	protected $default_step = 1;

	protected $min = 1;
	protected $max = 2147483647;
	protected $step = 1;

	function template() {
		?>
		<input id="{{{ id }}}" type="number" name="{{{ name }}}" value="{{ value }}" max="{{ max }}" min="{{ min }}" step="{{ step }}" pattern="[0-9]*" class="regular-text" />
		<?php
	}

	function to_json( $load ) {
		$field_data = parent::to_json( $load );

		$field_data = array_merge( $field_data, array(
			'min' => is_numeric( $this->min ) ? $this->min : $this->default_min,
			'max' => is_numeric( $this->max ) ? $this->max : $this->default_max,
			'step' => is_numeric( $this->step ) ? $this->step : $this->default_step,
		) );

		return $field_data;
	}

	function save() {
		$name = $this->get_name();
		$value = $this->get_value();
		$min = $this->min;
		$max = $this->max;
		$step = $this->step;

		// Set the value for the field
		$this->set_name( $name );

		$field_value = '';
		if ( isset( $value ) && $value !== '' && is_numeric( $value ) ) {
			$value = floatval( $value );

			$is_valid_min = $min <= $value;
			$is_valid_max = $value <= $max;

			// Base Formula "value = min + n * step" where "n" should be integer
			$test_for_step_validation = ( $value - $min ) / $step;
			$is_valid_step = $test_for_step_validation === floor( $test_for_step_validation );

			if ( $value !== '' && $is_valid_min && $is_valid_max && $is_valid_step ) {
				$field_value = $value;
			}
		}

		$this->set_value( $field_value );

		parent::save();
	}

	static function admin_enqueue_scripts() {
		$template_dir = get_template_directory_uri();

		// Get the current url for the carbon-fields-number, regardless of the location
		$template_dir .= str_replace( wp_normalize_path( get_template_directory() ), '', wp_normalize_path( __DIR__ ) );

		# Enqueue JS
		crb_enqueue_script( 'carbon-field-Number', $template_dir . '/js/bundle.js', array( 'carbon-bootstrap' ) );

		# Enqueue CSS
		crb_enqueue_style( 'carbon-field-Number', $template_dir . '/css/field.css' );
	}

	function set_max( $max ) {
		$this->max = $max;
		return $this;
	}

	function set_min( $min ) {
		$this->min = $min;
		return $this;
	}

	function set_step( $step ) {
		$this->step = $step;
		return $this;
	}
}
