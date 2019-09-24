<?php
namespace Croco_IK\Base;

use Croco_IK\Plugin as Plugin;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

abstract class Module {

	abstract public function get_slug();

	public function __construct() {

		$this->init();

		add_action(
			'croco-ik/dashboard/before-enqueue-assets/' . $this->get_slug(),
			array( $this, 'assets' )
		);

		add_action( 'wp_ajax_croco-ik/' . $this->get_slug(), array( $this, 'process_ajax' ) );

	}

	/**
	 * Initialize module-specific parts
	 *
	 * @return [type] [description]
	 */
	public function init() {}

	/**
	 * Register module assets
	 *
	 * @return [type] [description]
	 */
	public function assets() {

		$this->enqueue_module_assets();

		add_filter( 'croco-ik/dashboard/js-page-config', array( $this, 'page_config' ), 10, 2 );
		add_filter( 'croco-ik/dashboard/js-page-templates', array( $this, 'page_templates' ), 10, 2 );

	}

	/**
	 * Process ajax
	 *
	 * @return [type] [description]
	 */
	public function process_ajax() {

		$handler = isset( $_REQUEST['handler'] ) ? $_REQUEST['handler'] : false;

		if ( ! $handler || ! is_callable( array( $this, $handler ) ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => __( 'You are not allowed to access this', 'croco-ik' ),
			) );
		}

		$nonce = isset( $_REQUEST['nonce'] ) ? esc_attr( $_REQUEST['nonce'] ) : false;

		if ( ! $nonce || ! wp_verify_nonce( $nonce, Plugin::instance()->dashboard->page_slug ) ) {
			wp_send_json_error( array(
				'message' => __( 'Nonce verfictaion failed', 'croco-ik' ),
			) );
		}

		call_user_func( array( $this, $handler ) );

	}

	/**
	 * Enqueue module-specific assets
	 *
	 * @return void
	 */
	public function enqueue_module_assets() {}

	/**
	 * Modify page config
	 *
	 * @param  [type] $config [description]
	 * @return [type]         [description]
	 */
	public function page_config( $config = array(), $subpage = '' ) {
		return $config;
	}

	/**
	 * Add page templates
	 *
	 * @param  [type] $config [description]
	 * @return [type]         [description]
	 */
	public function page_templates( $templates = array(), $subpage = '' ) {
		return $templates;
	}

	/**
	 * Returns link to current page
	 *
	 * @return [type] [description]
	 */
	public function get_page_link() {
		return Plugin::instance()->dashboard->page_url( $this->get_slug() );
	}

}