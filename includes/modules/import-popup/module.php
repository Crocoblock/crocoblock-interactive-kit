<?php
namespace Croco_IK\Modules\Import_Popup;

use Croco_IK\Base\Module as Module_Base;
use Croco_IK\Plugin as Plugin;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Module extends Module_Base {

	private $import_file = null;
	private $importer    = null;
	private $chunk_size  = null;

	/**
	 * Returns module slug
	 *
	 * @return void
	 */
	public function get_slug() {
		return 'import-popup';
	}

	/**
	 * Enqueue module-specific assets
	 *
	 * @return void
	 */
	public function enqueue_module_assets() {

		wp_enqueue_script(
			'croco-ik-popups',
			CROCO_IK_URL . 'assets/js/popups.js',
			array( 'cx-vue-ui' ),
			CROCO_IK_VERSION,
			true
		);

	}

	/**
	 * License page config
	 *
	 * @param  array  $config  [description]
	 * @param  string $subpage [description]
	 * @return [type]          [description]
	 */
	public function page_config( $config = array(), $subpage = '' ) {

		$skin        = isset( $_GET['skin'] ) ? $_GET['skin'] : false;
		$is_uploaded = isset( $_GET['is_uploaded'] ) ? $_GET['is_uploaded'] : false;
		$api         = new API();
		$popups      = $api->get_popups();

		if ( is_array( $popups ) ) {
			$popups = array_values( $popups );
		} else {
			$popups = array();
		}

		$config['body']        = 'cbw-popups';
		$config['page_title']  = __( 'Import popups', 'crocoblock-mki' );
		$config['wrapper_css'] = 'content-page vertical-flex';
		$config['popups']      = $popups;
		$config['filters']     = $api->get_filters();

		return $config;

	}

	/**
	 * Add license component template
	 *
	 * @param  array  $templates [description]
	 * @param  string $subpage   [description]
	 * @return [type]            [description]
	 */
	public function page_templates( $templates = array(), $subpage = '' ) {

		$templates['popups'] = 'import-popup/popups';
		$templates['popup']  = 'import-popup/popup';

		return $templates;

	}

	/**
	 * Process single chunk import
	 *
	 * @return void
	 */
	public function import_content() {

		$slug = isset( $_REQUEST['slug'] ) ? esc_attr( $_REQUEST['slug'] ) : false;

		if ( ! $slug ) {
			wp_send_json_error( __( 'Slug is not defined', 'croco-ik' ) );
		}

		$api    = new API();
		$popups = $api->get_popups();

		if ( ! $popups || ! isset( $popups[ $slug ] ) ) {
			wp_send_json_error( __( 'Requested popup not found', 'croco-ik' ) );
		}

		$popup = $popups[ $slug ];
		$url   = ! empty( $popup['xml'] ) ? $popup['xml'] : false;

		if ( ! $url ) {
			wp_send_json_error( __( 'Can\'t find popup data URL', 'croco-ik' ) );
		}

		$importer = new JSON_Importer( $url );
		$result   = $importer->import();

		if ( ! $result ) {
			wp_send_json_error( __( 'Can\'t import popup data', 'croco-ik' ) );
		}

		$imported_popups = $importer->get_log( 'popups' );

		if ( empty( $imported_popups ) ) {
			wp_send_json_error( __( 'Can\'t import popup data', 'croco-ik' ) );
		}

		$imported_popups = array_values( $imported_popups );
		$popup_id        = $imported_popups[0];

		wp_send_json_success( add_query_arg( array(
			'post'   => $popup_id,
			'action' => 'elementor',
		), esc_url( admin_url( 'post.php' ) ) ) );

	}

}
