<?php
/*
 Plugin Name:  WooCommerce Dynamic Pricing Product Tag Adjustments
*/


class WC_Dynamic_Pricing_Product_Tag_Adjustments {

	private static $instance;

	public static function register( $label ) {
		if ( self::$instance == null ) {
			self::$instance = new WC_Dynamic_Pricing_Product_Tag_Adjustments( $label );
		}

	}

	protected $attribute;
	protected $attribute_label;

	public function __construct( $label ) {
		$this->attribute = 'product_tag';
		$this->attribute_label = $label;

		add_filter( 'wc_dynamic_pricing_get_discount_taxonomies', [
			$this,
			'add_attribute_taxonomies'
		], 10, 1 );

		add_filter( 'woocommerce_dynamic_pricing_tabs', [ $this, 'add_attribute_tabs' ], 10, 1 );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
	}


	public function add_attribute_taxonomies( $additional_taxonomies ) {
		$additional_taxonomies[] = $this->attribute;
		return $additional_taxonomies;
	}

	public function add_attribute_tabs( $tabs ) {
		$tabs[ $this->attribute ] = array(
			'tab_title' => $this->attribute_label,
			'tabs'      => array(
				array(

					'title'       => sprintf( __( 'Basic %s Pricing', 'woocommerce-dynamic-pricing' ), $this->attribute_label ),
					'description' => sprintf( __( 'Use product tag pricing to configure simple bulk price adjustments based on a product\'s %s.', 'woocommerce-dynamic-pricing' ), $this->attribute_label ),
					'function'    => 'taxonomy_basic_tab'
				),
				array(

					'title'       => sprintf( __( 'Advanced %s Pricing', 'woocommerce-dynamic-pricing' ), $this->attribute_label ),
					'description' => sprintf( __( 'Use product tag pricing to configure advanced bulk price adjustments based on a product\'s %s.', 'woocommerce-dynamic-pricing' ), $this->attribute_label ),
					'function'    => 'taxonomy_advanced_tab'
				),
			)
		);

		return $tabs;
	}

	public function register_settings() {
		register_setting( '_a_taxonomy_' . $this->attribute . '_pricing_rules', '_a_taxonomy_' . $this->attribute . '_pricing_rules', array(
			$this,
			'on_advanced_settings_validation'
		) );
	}

	public function on_advanced_settings_validation( $data ) {
		$rules = array();
		if ( isset( $_POST['pricing_rules'] ) ) {
			$pricing_rule_sets = $_POST['pricing_rules'];
			foreach ( $pricing_rule_sets as $key => $rule_set ) {
				$rules[ $key ] = $rule_set;
			}
			$data = $rules;
		} else {
			$data = array();
		}

		return $data;
	}
}

WC_Dynamic_Pricing_Product_Tag_Adjustments::register(  'Product Tags' );




