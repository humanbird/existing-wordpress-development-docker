<?php

class DpDFG_DpDiviFilterGrid extends DiviExtension {

	/**
	 * The gettext domain for the extension's translations.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $gettext_domain = 'dpdfg-dp-divi-filtergrid';

	/**
	 * The extension's WP Plugin name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $name = 'dp-divi-filtergrid';

	/**
	 * The extension's version
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $version = DPDFG_VERSION;

	/**
	 * DpDFG_DpCptFilterableModule constructor.
	 *
	 * @param string $name
	 * @param array $args
	 */
	public function __construct( $name = 'dp-divi-filtergrid', $args = array() ) {
		$this->plugin_dir       = plugin_dir_path( __FILE__ );
		$this->plugin_dir_url   = plugin_dir_url( $this->plugin_dir );
		$this->_builder_js_data = array(
			'ajaxurl'       => admin_url( 'admin-ajax.php' ),
			'nonce_layouts' => wp_create_nonce( 'dpdfg_get_layouts_action' ),
		);
		parent::__construct( $name, $args );
	}

	protected function _enqueue_bundles() {
		// Frontend Bundle
		$bundle_url = "{$this->plugin_dir_url}scripts/frontend-bundle.min.js";
		if ( et_core_is_fb_enabled() ) {
			wp_enqueue_script( "{$this->name}-frontend-bundle", $bundle_url, $this->_bundle_dependencies['frontend'], $this->version, true );
			// Builder Bundle
			$bundle_url = "{$this->plugin_dir_url}scripts/builder-bundle.min.js";
			wp_enqueue_script( "{$this->name}-builder-bundle", $bundle_url, $this->_bundle_dependencies['builder'], $this->version, true );
		} else {
			wp_register_script( "{$this->name}-frontend-bundle", $bundle_url, $this->_bundle_dependencies['frontend'], $this->version, true );
		}
	}

}

new DPDFG_DpDiviFilterGrid();
