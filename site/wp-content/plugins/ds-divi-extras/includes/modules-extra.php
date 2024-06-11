<?php
/**
 * Contains code copied from and/or based on Extra Theme by Elegant Themes.
 * See the license.txt file in the root directory for more information and licenses
 *
 */

if ( class_exists( 'ET_Builder_Module_Posts' ) && ! class_exists( 'ET_Builder_Module_Posts_AGSDCM' ) ) {
	class ET_Builder_Module_Posts_AGSDCM extends ET_Builder_Module_Posts {
		function init() {
			parent::init();
			$this->fb_support = false;
			$this->vb_support = 'partial';
			$this->post_types = array();
			$this->icon_path        =  plugin_dir_path( __FILE__ ) . 'icons/posts_module.svg';
			$this->slug       .= '_agsdcm';
			if ( $this->child_slug ) {
				$this->child_slug .= '_agsdcm';
			}
			if ( ! empty( $this->advanced_fields['fonts']['header']['css'] ) ) {
				foreach ( $this->advanced_fields['fonts']['header']['css'] as &$selector ) {
					if ( substr( $selector, 0, 16 ) == '#page-container ' ) {
						$selector = substr( $selector, 16 );
					}
				}
			}
			if ( isset( $this->advanced_fields['border']['css']['main'] ) ) {
				$this->advanced_fields['border']['css']['main'] = array(
					'border_styles' => $this->advanced_fields['border']['css']['main'],
					'border_radii'  => $this->advanced_fields['border']['css']['main'],
				);
				$this->advanced_fields['borders']               = array( 'default' => $this->advanced_fields['border'], );
			}
			self::createAndSet( $this->advanced_fields, array(
				'borders',
				'default',
				'defaults',
				'border_radii',
				'on|3px|3px|3px|3px'
			) );
		}

		static function createAndSet( &$target, $set ) {
			if ( count( $set ) < 2 ) {
				return;
			}
			if ( count( $set ) == 2 ) {
				$target[ $set[0] ] = $set[1];
			} else {
				$newKey            = array_shift( $set );
				$target[ $newKey ] = array();
				self::createAndSet( $target[ $newKey ], $set );
			}
		}

		function shortcode_atts() {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( $propName == 'remove_drop_shadow' && isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			} else {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			}
		}

		function _add_link_options_fields() {
		}

		function _add_text_fields() {
		}

		function process_advanced_text_options( $function_name ) {
		}

		function get_fields() {
			$fields = parent::get_fields();

			return agsdcm_process_module_fields( $fields, $this );
		}

		function _pre_wp_query( $args ) {
			return agsdcm_process_module_query( parent::_pre_wp_query( $args ), $this );
		}

		function render( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$return = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
			} else {
				$templateName = $this->template_name;
				unset( $this->template_name );
				$return              = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
				$this->template_name = $templateName;
			}

			return $return;
		}

		function _render( $attrs, $content = null, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
			add_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			add_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );
			$return = call_user_func_array( array( __CLASS__, 'parent::_render' ), func_get_args() );
			remove_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			remove_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );

			return $return;
		}

		function fix_range_value( $value ) {
			if ( $value == '0none' ) {
				return 'none';
			}

			return $value;
		}

		function before_render() {
			$this->props['border_radius'] = '';

			return call_user_func_array( array( __CLASS__, 'parent::before_render' ), func_get_args() );
		}

		function filter_selector( $selector ) {
			$selector = explode( ',', $selector );
			foreach ( $selector as &$selectorPart ) {
				$selectorPart = trim( $selectorPart );
				if ( strpos( $selectorPart, '.ags-divi-extras-module ' ) === false ) {
					$moduleClassPos = strpos( $selectorPart, '_agsdcm_' );
					if ( $moduleClassPos ) {
						$moduleClassPos = strrpos( $selectorPart, ' ', $moduleClassPos - strlen( $selectorPart ) );
						$selectorPart   = $moduleClassPos ? substr( $selectorPart, 0, $moduleClassPos ) . ' .ags-divi-extras-module' . substr( $selectorPart, $moduleClassPos ) : '.ags-divi-extras-module ' . $selectorPart;
					}
				}
			}

			return implode( ',', $selector );
		}

		function _set_fields_unprocessed( $fields ) {
			$unsupportedFields = array(
				'module_alignment'             => true,
				'module_alignment_tablet'      => true,
				'module_alignment_phone'       => true,
				'module_alignment_last_edited' => true,
				'border_radius'                => true
			);

			return parent::_set_fields_unprocessed( array_diff_key( $fields, $unsupportedFields ) );
		}

		function output() {
			return '<div class="ags-divi-extras-module">' . parent::output() . '</div>';
		}
	}

	new ET_Builder_Module_Posts_AGSDCM;
}
if ( class_exists( 'ET_Builder_Module_Tabbed_Posts' ) && ! class_exists( 'ET_Builder_Module_Tabbed_Posts_AGSDCM' ) ) {
	class ET_Builder_Module_Tabbed_Posts_AGSDCM extends ET_Builder_Module_Tabbed_Posts {
		function init() {
			parent::init();
			$this->fb_support = false;
			$this->vb_support = 'partial';
			$this->post_types = array();
			$this->slug       .= '_agsdcm';
			$this->icon_path        =  plugin_dir_path( __FILE__ ) . 'icons/tabbed_posts_module.svg';
			if ( $this->child_slug ) {
				$this->child_slug .= '_agsdcm';
			}
			if ( ! empty( $this->advanced_fields['fonts']['header']['css'] ) ) {
				foreach ( $this->advanced_fields['fonts']['header']['css'] as &$selector ) {
					if ( substr( $selector, 0, 16 ) == '#page-container ' ) {
						$selector = substr( $selector, 16 );
					}
				}
			}
			if ( isset( $this->advanced_fields['border']['css']['main'] ) ) {
				$this->advanced_fields['border']['css']['main'] = array(
					'border_styles' => $this->advanced_fields['border']['css']['main'],
					'border_radii'  => $this->advanced_fields['border']['css']['main'],
				);
				$this->advanced_fields['borders']               = array( 'default' => $this->advanced_fields['border'], );
			}
			self::createAndSet( $this->advanced_fields, array(
				'borders',
				'default',
				'defaults',
				'border_radii',
				'on|3px|3px|3px|3px'
			) );
		}

		static function createAndSet( &$target, $set ) {
			if ( count( $set ) < 2 ) {
				return;
			}
			if ( count( $set ) == 2 ) {
				$target[ $set[0] ] = $set[1];
			} else {
				$newKey            = array_shift( $set );
				$target[ $newKey ] = array();
				self::createAndSet( $target[ $newKey ], $set );
			}
		}

		function shortcode_atts() {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( $propName == 'remove_drop_shadow' && isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			} else {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			}
		}

		function _add_link_options_fields() {
		}

		function _add_text_fields() {
		}

		function process_advanced_text_options( $function_name ) {
		}

		function _pre_wp_query( $args ) {
			return agsdcm_process_module_query( parent::_pre_wp_query( $args ), $this );
		}

		function render( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$return = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
			} else {
				$templateName = $this->template_name;
				unset( $this->template_name );
				$return              = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
				$this->template_name = $templateName;
			}

			return $return;
		}

		function _render( $attrs, $content = null, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
			add_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			add_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );
			$return = call_user_func_array( array( __CLASS__, 'parent::_render' ), func_get_args() );
			remove_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			remove_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );

			return $return;
		}

		function fix_range_value( $value ) {
			if ( $value == '0none' ) {
				return 'none';
			}

			return $value;
		}

		function before_render() {
			$this->props['border_radius'] = '';

			return call_user_func_array( array( __CLASS__, 'parent::before_render' ), func_get_args() );
		}

		function filter_selector( $selector ) {
			$selector = explode( ',', $selector );
			foreach ( $selector as &$selectorPart ) {
				$selectorPart = trim( $selectorPart );
				if ( strpos( $selectorPart, '.ags-divi-extras-module ' ) === false ) {
					$moduleClassPos = strpos( $selectorPart, '_agsdcm_' );
					if ( $moduleClassPos ) {
						$moduleClassPos = strrpos( $selectorPart, ' ', $moduleClassPos - strlen( $selectorPart ) );
						$selectorPart   = $moduleClassPos ? substr( $selectorPart, 0, $moduleClassPos ) . ' .ags-divi-extras-module' . substr( $selectorPart, $moduleClassPos ) : '.ags-divi-extras-module ' . $selectorPart;
					}
				}
			}

			return implode( ',', $selector );
		}

		function _set_fields_unprocessed( $fields ) {
			$unsupportedFields = array(
				'module_alignment'             => true,
				'module_alignment_tablet'      => true,
				'module_alignment_phone'       => true,
				'module_alignment_last_edited' => true,
				'border_radius'                => true
			);

			return parent::_set_fields_unprocessed( array_diff_key( $fields, $unsupportedFields ) );
		}

		function output() {
			return '<div class="ags-divi-extras-module">' . parent::output() . '</div>';
		}
	}

	new ET_Builder_Module_Tabbed_Posts_AGSDCM;
}
if ( class_exists( 'ET_Builder_Module_Tabbed_Posts_Tab' ) && ! class_exists( 'ET_Builder_Module_Tabbed_Posts_Tab_AGSDCM' ) ) {
	class ET_Builder_Module_Tabbed_Posts_Tab_AGSDCM extends ET_Builder_Module_Tabbed_Posts_Tab {
		function init() {
			parent::init();
			$this->fb_support = false;
			$this->vb_support = 'partial';
			$this->post_types = array();
			$this->slug       .= '_agsdcm';
			if ( $this->child_slug ) {
				$this->child_slug .= '_agsdcm';
			}
			if ( ! empty( $this->advanced_fields['fonts']['header']['css'] ) ) {
				foreach ( $this->advanced_fields['fonts']['header']['css'] as &$selector ) {
					if ( substr( $selector, 0, 16 ) == '#page-container ' ) {
						$selector = substr( $selector, 16 );
					}
				}
			}
			if ( isset( $this->advanced_fields['border']['css']['main'] ) ) {
				$this->advanced_fields['border']['css']['main'] = array(
					'border_styles' => $this->advanced_fields['border']['css']['main'],
					'border_radii'  => $this->advanced_fields['border']['css']['main'],
				);
				$this->advanced_fields['borders']               = array( 'default' => $this->advanced_fields['border'], );
			}
		}

		static function createAndSet( &$target, $set ) {
			if ( count( $set ) < 2 ) {
				return;
			}
			if ( count( $set ) == 2 ) {
				$target[ $set[0] ] = $set[1];
			} else {
				$newKey            = array_shift( $set );
				$target[ $newKey ] = array();
				self::createAndSet( $target[ $newKey ], $set );
			}
		}

		function shortcode_atts() {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( $propName == 'remove_drop_shadow' && isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			} else {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			}
		}

		function _add_link_options_fields() {
		}

		function _add_text_fields() {
		}

		function process_advanced_text_options( $function_name ) {
		}

		function get_fields() {
			$fields = parent::get_fields();

			return agsdcm_process_module_fields( $fields, $this, false );
		}

		function _pre_wp_query( $args ) {
			return agsdcm_process_module_query( parent::_pre_wp_query( $args ), $this );
		}

		function render( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$return = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
			} else {
				$templateName = $this->template_name;
				unset( $this->template_name );
				$return              = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
				$this->template_name = $templateName;
			}

			return $return;
		}

		function _render( $attrs, $content = null, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
			add_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			add_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );
			$return = call_user_func_array( array( __CLASS__, 'parent::_render' ), func_get_args() );
			remove_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			remove_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );

			return $return;
		}

		function fix_range_value( $value ) {
			if ( $value == '0none' ) {
				return 'none';
			}

			return $value;
		}

		function before_render() {
			$this->props['border_radius'] = '';

			return call_user_func_array( array( __CLASS__, 'parent::before_render' ), func_get_args() );
		}

		function filter_selector( $selector ) {
			$selector = explode( ',', $selector );
			foreach ( $selector as &$selectorPart ) {
				$selectorPart = trim( $selectorPart );
				if ( strpos( $selectorPart, '.ags-divi-extras-module ' ) === false ) {
					$moduleClassPos = strpos( $selectorPart, '_agsdcm_' );
					if ( $moduleClassPos ) {
						$moduleClassPos = strrpos( $selectorPart, ' ', $moduleClassPos - strlen( $selectorPart ) );
						$selectorPart   = $moduleClassPos ? substr( $selectorPart, 0, $moduleClassPos ) . ' .ags-divi-extras-module' . substr( $selectorPart, $moduleClassPos ) : '.ags-divi-extras-module ' . $selectorPart;
					}
				}
			}

			return implode( ',', $selector );
		}

		function _set_fields_unprocessed( $fields ) {
			$unsupportedFields = array(
				'module_alignment'             => true,
				'module_alignment_tablet'      => true,
				'module_alignment_phone'       => true,
				'module_alignment_last_edited' => true,
				'border_radius'                => true
			);

			return parent::_set_fields_unprocessed( array_diff_key( $fields, $unsupportedFields ) );
		}
	}

	new ET_Builder_Module_Tabbed_Posts_Tab_AGSDCM;
}
if ( class_exists( 'ET_Builder_Module_Posts_Carousel' ) && ! class_exists( 'ET_Builder_Module_Posts_Carousel_AGSDCM' ) ) {
	class ET_Builder_Module_Posts_Carousel_AGSDCM extends ET_Builder_Module_Posts_Carousel {
		function init() {
			parent::init();
			$this->fb_support = false;
			$this->vb_support = 'partial';
			$this->post_types = array();
			$this->icon_path        =  plugin_dir_path( __FILE__ ) . 'icons/posts_carousel_module.svg';
			$this->slug       .= '_agsdcm';
			if ( $this->child_slug ) {
				$this->child_slug .= '_agsdcm';
			}
			if ( ! empty( $this->advanced_fields['fonts']['header']['css'] ) ) {
				foreach ( $this->advanced_fields['fonts']['header']['css'] as &$selector ) {
					if ( substr( $selector, 0, 16 ) == '#page-container ' ) {
						$selector = substr( $selector, 16 );
					}
				}
			}
			if ( isset( $this->advanced_fields['border']['css']['main'] ) ) {
				$this->advanced_fields['border']['css']['main'] = array(
					'border_styles' => $this->advanced_fields['border']['css']['main'],
					'border_radii'  => $this->advanced_fields['border']['css']['main'],
				);
				$this->advanced_fields['borders']               = array( 'default' => $this->advanced_fields['border'], );
			}
			self::createAndSet( $this->advanced_fields, array(
				'borders',
				'default',
				'defaults',
				'border_radii',
				'on|3px|3px|3px|3px'
			) );
		}

		static function createAndSet( &$target, $set ) {
			if ( count( $set ) < 2 ) {
				return;
			}
			if ( count( $set ) == 2 ) {
				$target[ $set[0] ] = $set[1];
			} else {
				$newKey            = array_shift( $set );
				$target[ $newKey ] = array();
				self::createAndSet( $target[ $newKey ], $set );
			}
		}

		function shortcode_atts() {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( $propName == 'remove_drop_shadow' && isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			} else {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			}
		}

		function _add_link_options_fields() {
		}

		function _add_text_fields() {
		}

		function process_advanced_text_options( $function_name ) {
		}

		function get_fields() {
			$fields = parent::get_fields();

			return agsdcm_process_module_fields( $fields, $this );
		}

		function _pre_wp_query( $args ) {
			return agsdcm_process_module_query( parent::_pre_wp_query( $args ), $this );
		}

		function render( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$return = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
			} else {
				$templateName = $this->template_name;
				unset( $this->template_name );
				$return              = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
				$this->template_name = $templateName;
			}

			return $return;
		}

		function _render( $attrs, $content = null, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
			add_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			add_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );
			$return = call_user_func_array( array( __CLASS__, 'parent::_render' ), func_get_args() );
			remove_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			remove_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );

			return $return;
		}

		function fix_range_value( $value ) {
			if ( $value == '0none' ) {
				return 'none';
			}

			return $value;
		}

		function before_render() {
			$this->props['border_radius'] = '';

			return call_user_func_array( array( __CLASS__, 'parent::before_render' ), func_get_args() );
		}

		function filter_selector( $selector ) {
			$selector = explode( ',', $selector );
			foreach ( $selector as &$selectorPart ) {
				$selectorPart = trim( $selectorPart );
				if ( strpos( $selectorPart, '.ags-divi-extras-module ' ) === false ) {
					$moduleClassPos = strpos( $selectorPart, '_agsdcm_' );
					if ( $moduleClassPos ) {
						$moduleClassPos = strrpos( $selectorPart, ' ', $moduleClassPos - strlen( $selectorPart ) );
						$selectorPart   = $moduleClassPos ? substr( $selectorPart, 0, $moduleClassPos ) . ' .ags-divi-extras-module' . substr( $selectorPart, $moduleClassPos ) : '.ags-divi-extras-module ' . $selectorPart;
					}
				}
			}

			return implode( ',', $selector );
		}

		function _set_fields_unprocessed( $fields ) {
			$unsupportedFields = array(
				'module_alignment'             => true,
				'module_alignment_tablet'      => true,
				'module_alignment_phone'       => true,
				'module_alignment_last_edited' => true,
				'border_radius'                => true
			);

			return parent::_set_fields_unprocessed( array_diff_key( $fields, $unsupportedFields ) );
		}

		function output() {
			return '<div class="ags-divi-extras-module">' . parent::output() . '</div>';
		}
	}

	new ET_Builder_Module_Posts_Carousel_AGSDCM;
}
if ( class_exists( 'ET_Builder_Module_Featured_Posts_Slider' ) && ! class_exists( 'ET_Builder_Module_Featured_Posts_Slider_AGSDCM' ) ) {
	class ET_Builder_Module_Featured_Posts_Slider_AGSDCM extends ET_Builder_Module_Featured_Posts_Slider {
		function init() {
			parent::init();
			$this->fb_support = false;
			$this->vb_support = 'partial';
			$this->post_types = array();
			$this->icon_path        =  plugin_dir_path( __FILE__ ) . 'icons/posts_slider_module.svg';
			$this->slug       .= '_agsdcm';
			if ( $this->child_slug ) {
				$this->child_slug .= '_agsdcm';
			}
			if ( ! empty( $this->advanced_fields['fonts']['header']['css'] ) ) {
				foreach ( $this->advanced_fields['fonts']['header']['css'] as &$selector ) {
					if ( substr( $selector, 0, 16 ) == '#page-container ' ) {
						$selector = substr( $selector, 16 );
					}
				}
			}
			if ( isset( $this->advanced_fields['border']['css']['main'] ) ) {
				$this->advanced_fields['border']['css']['main'] = array(
					'border_styles' => $this->advanced_fields['border']['css']['main'],
					'border_radii'  => $this->advanced_fields['border']['css']['main'],
				);
				$this->advanced_fields['borders']               = array( 'default' => $this->advanced_fields['border'], );
			}
			self::createAndSet( $this->advanced_fields, array(
				'borders',
				'default',
				'defaults',
				'border_radii',
				'on|3px|3px|3px|3px'
			) );
		}

		static function createAndSet( &$target, $set ) {
			if ( count( $set ) < 2 ) {
				return;
			}
			if ( count( $set ) == 2 ) {
				$target[ $set[0] ] = $set[1];
			} else {
				$newKey            = array_shift( $set );
				$target[ $newKey ] = array();
				self::createAndSet( $target[ $newKey ], $set );
			}
		}

		function shortcode_atts() {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( $propName == 'remove_drop_shadow' && isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			} else {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			}
		}

		function _add_link_options_fields() {
		}

		function _add_text_fields() {
		}

		function process_advanced_text_options( $function_name ) {
		}

		function get_fields() {
			$fields = parent::get_fields();
			unset( $fields['heading_primary'] );
			unset( $fields['heading_sub'] );

			return agsdcm_process_module_fields( $fields, $this );
		}

		function _pre_wp_query( $args ) {
			return agsdcm_process_module_query( parent::_pre_wp_query( $args ), $this );
		}

		function render( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$return = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
			} else {
				$templateName = $this->template_name;
				unset( $this->template_name );
				$return              = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
				$this->template_name = $templateName;
			}

			return $return;
		}

		function _render( $attrs, $content = null, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
			add_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			add_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );
			$return = call_user_func_array( array( __CLASS__, 'parent::_render' ), func_get_args() );
			remove_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			remove_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );

			return $return;
		}

		function fix_range_value( $value ) {
			if ( $value == '0none' ) {
				return 'none';
			}

			return $value;
		}

		function before_render() {
			$this->props['border_radius'] = '';

			return call_user_func_array( array( __CLASS__, 'parent::before_render' ), func_get_args() );
		}

		function filter_selector( $selector ) {
			$selector = explode( ',', $selector );
			foreach ( $selector as &$selectorPart ) {
				$selectorPart = trim( $selectorPart );
				if ( strpos( $selectorPart, '.ags-divi-extras-module ' ) === false ) {
					$moduleClassPos = strpos( $selectorPart, '_agsdcm_' );
					if ( $moduleClassPos ) {
						$moduleClassPos = strrpos( $selectorPart, ' ', $moduleClassPos - strlen( $selectorPart ) );
						$selectorPart   = $moduleClassPos ? substr( $selectorPart, 0, $moduleClassPos ) . ' .ags-divi-extras-module' . substr( $selectorPart, $moduleClassPos ) : '.ags-divi-extras-module ' . $selectorPart;
					}
				}
			}

			return implode( ',', $selector );
		}

		function _set_fields_unprocessed( $fields ) {
			$unsupportedFields = array(
				'module_alignment'             => true,
				'module_alignment_tablet'      => true,
				'module_alignment_phone'       => true,
				'module_alignment_last_edited' => true,
				'border_radius'                => true
			);

			return parent::_set_fields_unprocessed( array_diff_key( $fields, $unsupportedFields ) );
		}

		function output() {
			return '<div class="ags-divi-extras-module">' . parent::output() . '</div>';
		}
	}

	new ET_Builder_Module_Featured_Posts_Slider_AGSDCM;
}
if ( class_exists( 'ET_Builder_Module_Posts_Blog_Feed' ) && ! class_exists( 'ET_Builder_Module_Posts_Blog_Feed_AGSDCM' ) ) {
	class ET_Builder_Module_Posts_Blog_Feed_AGSDCM extends ET_Builder_Module_Posts_Blog_Feed {
		function init() {
			parent::init();
			$this->fb_support = false;
			$this->vb_support = 'partial';
			$this->post_types = array();
			$this->icon_path        =  plugin_dir_path( __FILE__ ) . 'icons/blogfeed_standard_module.svg';
			$this->slug       .= '_agsdcm';
			if ( $this->child_slug ) {
				$this->child_slug .= '_agsdcm';
			}
			if ( ! empty( $this->advanced_fields['fonts']['header']['css'] ) ) {
				foreach ( $this->advanced_fields['fonts']['header']['css'] as &$selector ) {
					if ( substr( $selector, 0, 16 ) == '#page-container ' ) {
						$selector = substr( $selector, 16 );
					}
				}
			}
			$this->advanced_fields['borders'] = array(
				'default' => array(
					'css' => array(
						'main'      => $this->main_css_element,
						'important' => 'all'
					)
				)
			);
			self::createAndSet( $this->advanced_fields, array(
				'borders',
				'default',
				'defaults',
				'border_radii',
				'on|3px|3px|3px|3px'
			) );
		}

		static function createAndSet( &$target, $set ) {
			if ( count( $set ) < 2 ) {
				return;
			}
			if ( count( $set ) == 2 ) {
				$target[ $set[0] ] = $set[1];
			} else {
				$newKey            = array_shift( $set );
				$target[ $newKey ] = array();
				self::createAndSet( $target[ $newKey ], $set );
			}
		}

		function shortcode_atts() {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( $propName == 'remove_drop_shadow' && isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			} else {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			}
		}

		function _add_link_options_fields() {
		}

		function _add_text_fields() {
		}

		function process_advanced_text_options( $function_name ) {
		}

		function get_fields() {
			$fields = parent::get_fields();

			return agsdcm_process_module_fields( $fields, $this );
		}

		function _pre_wp_query( $args ) {
			return agsdcm_process_module_query( parent::_pre_wp_query( $args ), $this );
		}

		function render( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$return = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
			} else {
				$templateName = $this->template_name;
				unset( $this->template_name );
				$return              = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
				$this->template_name = $templateName;
			}

			return $return;
		}

		function _render( $attrs, $content = null, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
			add_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			add_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );
			$return = call_user_func_array( array( __CLASS__, 'parent::_render' ), func_get_args() );
			remove_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			remove_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );

			return $return;
		}

		function fix_range_value( $value ) {
			if ( $value == '0none' ) {
				return 'none';
			}

			return $value;
		}

		function before_render() {
			$this->props['border_radius'] = '';

			return call_user_func_array( array( __CLASS__, 'parent::before_render' ), func_get_args() );
		}

		function filter_selector( $selector ) {
			$selector = explode( ',', $selector );
			foreach ( $selector as &$selectorPart ) {
				$selectorPart = trim( $selectorPart );
				if ( strpos( $selectorPart, '.ags-divi-extras-module ' ) === false ) {
					$moduleClassPos = strpos( $selectorPart, '_agsdcm_' );
					if ( $moduleClassPos ) {
						$moduleClassPos = strrpos( $selectorPart, ' ', $moduleClassPos - strlen( $selectorPart ) );
						$selectorPart   = $moduleClassPos ? substr( $selectorPart, 0, $moduleClassPos ) . ' .ags-divi-extras-module' . substr( $selectorPart, $moduleClassPos ) : '.ags-divi-extras-module ' . $selectorPart;
					}
				}
			}

			return implode( ',', $selector );
		}

		function _set_fields_unprocessed( $fields ) {
			$unsupportedFields = array(
				'module_alignment'             => true,
				'module_alignment_tablet'      => true,
				'module_alignment_phone'       => true,
				'module_alignment_last_edited' => true,
				'border_radius'                => true
			);

			return parent::_set_fields_unprocessed( array_diff_key( $fields, $unsupportedFields ) );
		}

		function output() {
			return '<div class="ags-divi-extras-module">' . parent::output() . '</div>';
		}
	}

	new ET_Builder_Module_Posts_Blog_Feed_AGSDCM;
}
if ( class_exists( 'ET_Builder_Module_Posts_Blog_Feed_Masonry' ) && ! class_exists( 'ET_Builder_Module_Posts_Blog_Feed_Masonry_AGSDCM' ) ) {
	class ET_Builder_Module_Posts_Blog_Feed_Masonry_AGSDCM extends ET_Builder_Module_Posts_Blog_Feed_Masonry {
		function init() {
			parent::init();
			$this->fb_support = false;
			$this->vb_support = 'partial';
			$this->post_types = array();
			$this->icon_path        =  plugin_dir_path( __FILE__ ) . 'icons/blogfeed_masonry_module.svg';
			$this->slug       .= '_agsdcm';
			if ( $this->child_slug ) {
				$this->child_slug .= '_agsdcm';
			}
			if ( ! empty( $this->advanced_fields['fonts']['header']['css'] ) ) {
				foreach ( $this->advanced_fields['fonts']['header']['css'] as &$selector ) {
					if ( substr( $selector, 0, 16 ) == '#page-container ' ) {
						$selector = substr( $selector, 16 );
					}
				}
			}
			if ( isset( $this->advanced_fields['border']['css']['main'] ) ) {
				$this->advanced_fields['border']['css']['main'] = array(
					'border_styles' => $this->advanced_fields['border']['css']['main'],
					'border_radii'  => $this->advanced_fields['border']['css']['main'],
				);
				$this->advanced_fields['borders']               = array( 'default' => $this->advanced_fields['border'], );
			}
			$this->advanced_fields['box_shadow'] = array( 'default' => array( 'css' => array( 'main' => ".posts-blog-feed-module.masonry{$this->main_css_element} .hentry" ) ) );
		}

		static function createAndSet( &$target, $set ) {
			if ( count( $set ) < 2 ) {
				return;
			}
			if ( count( $set ) == 2 ) {
				$target[ $set[0] ] = $set[1];
			} else {
				$newKey            = array_shift( $set );
				$target[ $newKey ] = array();
				self::createAndSet( $target[ $newKey ], $set );
			}
		}

		function shortcode_atts() {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( $propName == 'remove_drop_shadow' && isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			} else {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			}
		}

		function _add_link_options_fields() {
		}

		function _add_text_fields() {
		}

		function _add_background_fields() {
		}

		function process_advanced_text_options( $function_name ) {
		}

		function get_fields() {
			$fields = parent::get_fields();

			return agsdcm_process_module_fields( $fields, $this );
		}

		function _pre_wp_query( $args ) {
			return agsdcm_process_module_query( parent::_pre_wp_query( $args ), $this );
		}

		function render( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$return = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
			} else {
				$templateName = $this->template_name;
				unset( $this->template_name );
				$return              = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
				$this->template_name = $templateName;
			}

			return $return;
		}

		function _render( $attrs, $content = null, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
			add_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			add_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );
			$return = call_user_func_array( array( __CLASS__, 'parent::_render' ), func_get_args() );
			remove_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			remove_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );

			return $return;
		}

		function fix_range_value( $value ) {
			if ( $value == '0none' ) {
				return 'none';
			}

			return $value;
		}

		function before_render() {
			$this->props['border_radius'] = '';

			return call_user_func_array( array( __CLASS__, 'parent::before_render' ), func_get_args() );
		}

		function filter_selector( $selector ) {
			$selector = explode( ',', $selector );
			foreach ( $selector as &$selectorPart ) {
				$selectorPart = trim( $selectorPart );
				if ( strpos( $selectorPart, '.ags-divi-extras-module ' ) === false ) {
					$moduleClassPos = strpos( $selectorPart, '_agsdcm_' );
					if ( $moduleClassPos ) {
						$moduleClassPos = strrpos( $selectorPart, ' ', $moduleClassPos - strlen( $selectorPart ) );
						$selectorPart   = $moduleClassPos ? substr( $selectorPart, 0, $moduleClassPos ) . ' .ags-divi-extras-module' . substr( $selectorPart, $moduleClassPos ) : '.ags-divi-extras-module ' . $selectorPart;
					}
				}
			}

			return implode( ',', $selector );
		}

		function _set_fields_unprocessed( $fields ) {
			$unsupportedFields = array(
				'module_alignment'             => true,
				'module_alignment_tablet'      => true,
				'module_alignment_phone'       => true,
				'module_alignment_last_edited' => true,
				'border_radius'                => true
			);

			return parent::_set_fields_unprocessed( array_diff_key( $fields, $unsupportedFields ) );
		}

		function output() {
			return '<div class="ags-divi-extras-module">' . parent::output() . '</div>';
		}
	}

	new ET_Builder_Module_Posts_Blog_Feed_Masonry_AGSDCM;
}
if ( class_exists( 'ET_Builder_Module_Ads' ) && ! class_exists( 'ET_Builder_Module_Ads_AGSDCM' ) ) {
	class ET_Builder_Module_Ads_AGSDCM extends ET_Builder_Module_Ads {
		function init() {
			parent::init();
			$this->fb_support = false;
			$this->vb_support = 'partial';
			$this->post_types = array();
			$this->icon_path        =  plugin_dir_path( __FILE__ ) . 'icons/ad.svg';
			$this->slug       .= '_agsdcm';
			if ( $this->child_slug ) {
				$this->child_slug .= '_agsdcm';
			}
			if ( ! empty( $this->advanced_fields['fonts']['header']['css'] ) ) {
				foreach ( $this->advanced_fields['fonts']['header']['css'] as &$selector ) {
					if ( substr( $selector, 0, 16 ) == '#page-container ' ) {
						$selector = substr( $selector, 16 );
					}
				}
			}
			if ( isset( $this->advanced_fields['border']['css']['main'] ) ) {
				$this->advanced_fields['border']['css']['main'] = array(
					'border_styles' => $this->advanced_fields['border']['css']['main'],
					'border_radii'  => $this->advanced_fields['border']['css']['main'],
				);
				$this->advanced_fields['borders']               = array( 'default' => $this->advanced_fields['border'], );
			}
			self::createAndSet( $this->advanced_fields, array(
				'borders',
				'default',
				'defaults',
				'border_radii',
				'on|3px|3px|3px|3px'
			) );
		}

		static function createAndSet( &$target, $set ) {
			if ( count( $set ) < 2 ) {
				return;
			}
			if ( count( $set ) == 2 ) {
				$target[ $set[0] ] = $set[1];
			} else {
				$newKey            = array_shift( $set );
				$target[ $newKey ] = array();
				self::createAndSet( $target[ $newKey ], $set );
			}
		}

		function shortcode_atts() {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( $propName == 'remove_drop_shadow' && isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			} else {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			}
		}

		function _add_link_options_fields() {
		}

		function _add_text_fields() {
		}

		function process_advanced_text_options( $function_name ) {
		}

		function _pre_wp_query( $args ) {
			return agsdcm_process_module_query( parent::_pre_wp_query( $args ), $this );
		}

		function render( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$return = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
			} else {
				$templateName = $this->template_name;
				unset( $this->template_name );
				$return              = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
				$this->template_name = $templateName;
			}

			return $return;
		}

		function _render( $attrs, $content = null, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
			add_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			add_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );
			$return = call_user_func_array( array( __CLASS__, 'parent::_render' ), func_get_args() );
			remove_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			remove_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );

			return $return;
		}

		function fix_range_value( $value ) {
			if ( $value == '0none' ) {
				return 'none';
			}

			return $value;
		}

		function before_render() {
			$this->props['border_radius'] = '';

			return call_user_func_array( array( __CLASS__, 'parent::before_render' ), func_get_args() );
		}

		function filter_selector( $selector ) {
			$selector = explode( ',', $selector );
			foreach ( $selector as &$selectorPart ) {
				$selectorPart = trim( $selectorPart );
				if ( strpos( $selectorPart, '.ags-divi-extras-module ' ) === false ) {
					$moduleClassPos = strpos( $selectorPart, '_agsdcm_' );
					if ( $moduleClassPos ) {
						$moduleClassPos = strrpos( $selectorPart, ' ', $moduleClassPos - strlen( $selectorPart ) );
						$selectorPart   = $moduleClassPos ? substr( $selectorPart, 0, $moduleClassPos ) . ' .ags-divi-extras-module' . substr( $selectorPart, $moduleClassPos ) : '.ags-divi-extras-module ' . $selectorPart;
					}
				}
			}

			return implode( ',', $selector );
		}

		function _set_fields_unprocessed( $fields ) {
			$unsupportedFields = array(
				'module_alignment'             => true,
				'module_alignment_tablet'      => true,
				'module_alignment_phone'       => true,
				'module_alignment_last_edited' => true,
				'border_radius'                => true
			);

			return parent::_set_fields_unprocessed( array_diff_key( $fields, $unsupportedFields ) );
		}

		function output() {
			return '<div class="ags-divi-extras-module">' . parent::output() . '</div>';
		}
	}

	new ET_Builder_Module_Ads_AGSDCM;
}
if ( class_exists( 'ET_Builder_Module_Ads_Ad' ) && ! class_exists( 'ET_Builder_Module_Ads_Ad_AGSDCM' ) ) {
	class ET_Builder_Module_Ads_Ad_AGSDCM extends ET_Builder_Module_Ads_Ad {
		function init() {
			parent::init();
			$this->fb_support = false;
			$this->vb_support = 'partial';
			$this->post_types = array();
			$this->slug       .= '_agsdcm';
			if ( $this->child_slug ) {
				$this->child_slug .= '_agsdcm';
			}
			if ( ! empty( $this->advanced_fields['fonts']['header']['css'] ) ) {
				foreach ( $this->advanced_fields['fonts']['header']['css'] as &$selector ) {
					if ( substr( $selector, 0, 16 ) == '#page-container ' ) {
						$selector = substr( $selector, 16 );
					}
				}
			}
			if ( isset( $this->advanced_fields['border']['css']['main'] ) ) {
				$this->advanced_fields['border']['css']['main'] = array(
					'border_styles' => $this->advanced_fields['border']['css']['main'],
					'border_radii'  => $this->advanced_fields['border']['css']['main'],
				);
				$this->advanced_fields['borders']               = array( 'default' => $this->advanced_fields['border'], );
			}
		}

		static function createAndSet( &$target, $set ) {
			if ( count( $set ) < 2 ) {
				return;
			}
			if ( count( $set ) == 2 ) {
				$target[ $set[0] ] = $set[1];
			} else {
				$newKey            = array_shift( $set );
				$target[ $newKey ] = array();
				self::createAndSet( $target[ $newKey ], $set );
			}
		}

		function shortcode_atts() {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( $propName == 'remove_drop_shadow' && isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			} else {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			}
		}

		function _add_link_options_fields() {
		}

		function _add_text_fields() {
		}

		function process_advanced_text_options( $function_name ) {
		}

		function _pre_wp_query( $args ) {
			return agsdcm_process_module_query( parent::_pre_wp_query( $args ), $this );
		}

		function render( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$return = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
			} else {
				$templateName = $this->template_name;
				unset( $this->template_name );
				$return              = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
				$this->template_name = $templateName;
			}

			return $return;
		}

		function _render( $attrs, $content = null, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
			add_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			add_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );
			$return = call_user_func_array( array( __CLASS__, 'parent::_render' ), func_get_args() );
			remove_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			remove_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );

			return $return;
		}

		function fix_range_value( $value ) {
			if ( $value == '0none' ) {
				return 'none';
			}

			return $value;
		}

		function before_render() {
			$this->props['border_radius'] = '';

			return call_user_func_array( array( __CLASS__, 'parent::before_render' ), func_get_args() );
		}

		function filter_selector( $selector ) {
			$selector = explode( ',', $selector );
			foreach ( $selector as &$selectorPart ) {
				$selectorPart = trim( $selectorPart );
				if ( strpos( $selectorPart, '.ags-divi-extras-module ' ) === false ) {
					$moduleClassPos = strpos( $selectorPart, '_agsdcm_' );
					if ( $moduleClassPos ) {
						$moduleClassPos = strrpos( $selectorPart, ' ', $moduleClassPos - strlen( $selectorPart ) );
						$selectorPart   = $moduleClassPos ? substr( $selectorPart, 0, $moduleClassPos ) . ' .ags-divi-extras-module' . substr( $selectorPart, $moduleClassPos ) : '.ags-divi-extras-module ' . $selectorPart;
					}
				}
			}

			return implode( ',', $selector );
		}

		function _set_fields_unprocessed( $fields ) {
			$unsupportedFields = array(
				'module_alignment'             => true,
				'module_alignment_tablet'      => true,
				'module_alignment_phone'       => true,
				'module_alignment_last_edited' => true,
				'border_radius'                => true
			);

			return parent::_set_fields_unprocessed( array_diff_key( $fields, $unsupportedFields ) );
		}
	}

	new ET_Builder_Module_Ads_Ad_AGSDCM;
}
// End code including copied code from Extra