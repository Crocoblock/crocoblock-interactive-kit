<?php
namespace Croco_IK;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Dashboard manager class
 */
class Dashboard {

	private $subpage = null;
	public $page_slug = 'croco-ik';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'admin_init', array( $this, 'maybe_redirect_after_activation' ) );
		add_filter( 'plugin_action_links_' . CROCO_IK_PLUGIN_BASE, array( $this, 'plugin_action_links' ) );
	}

	/**
	 * Plugin action links.
	 * Adds ink to wizard strat page to the plugin list table
	 * Fired by `plugin_action_links` filter.
	 *
	 * @param array $links An array of plugin action links.
	 * @return array An array of plugin action links.
	 */
	public function plugin_action_links( $links ) {

		$start_page = sprintf(
			'<a href="%1$s">%2$s</a>',
			$this->page_url( $this->get_initial_page() ),
			__( 'Start Page', 'croco-ik' )
		);

		array_unshift( $links, $start_page );

		return $links;
	}

	/**
	 * Maybe redirect to plugin start page after activation
	 *
	 * @return [type] [description]
	 */
	public function maybe_redirect_after_activation() {

		if ( ! get_transient( 'croco_ik_redirect' ) ) {
			return;
		}

		if ( wp_doing_ajax() ) {
			return;
		}

		delete_transient( 'croco_ik_redirect' );

		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}

		if ( $this->is_dashboard_page() ) {
			return;
		}

		wp_safe_redirect( $this->page_url( $this->get_initial_page() ) );
		die();

	}

	/**
	 * Register menu page
	 *
	 * @return void
	 */
	public function register_menu_page() {

		add_management_page(
			__( 'Crocoblock Interactive Kit', 'croco-ik' ),
			__( 'Crocoblock Interactive Kit', 'croco-ik' ),
			'manage_options',
			$this->page_slug,
			array( $this, 'render_wizard' )
		);

	}

	/**
	 * Wizard assets
	 *
	 * @return void
	 */
	public function assets( $hook ) {

		if ( 'tools_page_' . $this->page_slug !== $hook ) {
			return;
		}

		require_once CROCO_IK_PATH . 'framework/vue-ui/cherry-x-vue-ui.php';

		$ui = new \CX_Vue_UI( array(
			'url'  => CROCO_IK_URL . 'framework/vue-ui/',
			'path' => CROCO_IK_PATH . 'framework/vue-ui/',
		) );

		$ui->enqueue_assets();

		wp_register_script(
			'croco-ik-mixins',
			CROCO_IK_URL . 'assets/js/mixins.js',
			array( 'cx-vue-ui' ),
			CROCO_IK_VERSION,
			true
		);

		/**
		 * Fires before enqueue page assets
		 */
		do_action( 'croco-ik/dashboard/before-enqueue-assets', $this );

		/**
		 * Fires before enqueue page assets with dynamic subpage name
		 */
		do_action( 'croco-ik/dashboard/before-enqueue-assets/' . $this->get_subpage(), $this );

		wp_enqueue_script(
			'croco-ik',
			CROCO_IK_URL . 'assets/js/wizard.js',
			array( 'cx-vue-ui' ),
			CROCO_IK_VERSION,
			true
		);

		wp_localize_script(
			'croco-ik',
			'CBWPageConfig',
			apply_filters( 'croco-ik/dashboard/js-page-config', array(
				'title'        => __( 'Installation Wizard', 'croco-ik' ),
				'main_page'    => $this->page_url( $this->get_initial_page() ),
				'has_header'   => true,
				'wrapper_css'  => false,
				'body'         => false,
				'prev'         => array( 'to' => false ),
				'next'         => array( 'to' => false ),
				'skip'         => array( 'to' => false ),
				'action_mask'  => $this->page_slug . '/%module%',
				'module'       => $this->get_subpage(),
				'nonce'        => wp_create_nonce( $this->page_slug ),
			), $this->get_subpage() )
		);

		add_action( 'admin_footer', array( $this, 'print_js_templates' ) );

		wp_enqueue_style(
			'croco-ik',
			CROCO_IK_URL . 'assets/css/wizard.css',
			array(),
			CROCO_IK_VERSION
		);

	}

	/**
	 * Print JS templates for current page
	 *
	 * @return [type] [description]
	 */
	public function print_js_templates() {

		$templates = apply_filters(
			'croco-ik/dashboard/js-page-templates',
			array(
				'main'         => 'common/main',
				'header'       => 'common/header',
				'logger'       => 'common/logger',
				'video'        => 'common/video',
				'choices'      => 'common/choices',
				'progress'     => 'common/progress',
				'progress_alt' => 'common/progress-alt',
			),
			$this->get_subpage()
		);

		foreach ( $templates as $name => $path ) {

			ob_start();
			include Plugin::instance()->get_view( $path );
			$content = ob_get_clean();

			printf(
				'<script type="text/x-template" id="cbw_%1$s">%2$s</script>',
				$name,
				$content
			);
		}

	}

	/**
	 * Returns url to dashboard page
	 *
	 * @param  [type] $subpage [description]
	 * @return [type]          [description]
	 */
	public function page_url( $subpage = null, $args = array() ) {

		$page_args = array(
			'page' => $this->page_slug,
			'sub'  => $subpage,
		);

		if ( ! empty( $args ) ) {
			$page_args = array_merge( $page_args, $args );
		}

		return add_query_arg( $page_args, admin_url( 'tools.php' ) );
	}

	/**
	 * Returns current subpage slug
	 *
	 * @return string
	 */
	public function get_subpage() {

		if ( null === $this->subpage ) {
			$this->subpage = isset( $_GET['sub'] ) ? esc_attr( $_GET['sub'] ) : $this->get_initial_page();
		}

		return $this->subpage;
	}

	/**
	 * Check if dashboard page is currently displayiing
	 *
	 * @return boolean [description]
	 */
	public function is_dashboard_page() {
		return ( ! empty( $_GET['page'] ) && $this->page_slug === $_GET['page'] );
	}

	/**
	 * Returns wizard initial subpage
	 *
	 * @return string
	 */
	public function get_initial_page() {
		return 'license';
	}

	/**
	 * Render installation wizard page
	 *
	 * @return void
	 */
	public function render_wizard() {
		include Plugin::instance()->get_view( 'common/page' );
	}

}
