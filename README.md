## Configuration example:

#### Plugins:

```PHP

add_action( 'init', 'my_plugins_wizard_config', 0 );

function my_plugins_wizard_config() {

	if ( ! function_exists( 'croco_ik' ) ) {
		return;
	}

	croco_ik()->settings->register_external_config( array(
		'plugins' => array(
			'plugin-1' => array(
				'name'   => esc_html__( 'Plugin 1', 'croco-ik' ),
				'sourse' => 'wordpress', // 'git', 'local', 'remote', 'wordpress' (default).
				'path'   => false, // git repository, remote URL or local path.
			),
			'plugin-2' => array(
				'name'   => esc_html__( 'Plugin 2', 'croco-ik' ),
				'sourse' => 'git', // 'git', 'local', 'remote', 'wordpress' (default).
				'path'   => false, // git repository, remote URL or local path.
			),
		)
	) );

	// Or from remote url
	croco_ik()->settings->register_external_config( array(
		'plugins' => array(
			'get_from' => URL which is returns JSON with plugins configuration,
		)
	) );

}
```

#### Skins:

```PHP

add_action( 'init', 'my_plugins_wizard_config', 0 );

function my_plugins_wizard_config() {

	if ( ! function_exists( 'croco_ik' ) ) {
		return;
	}

	croco_ik()->settings->register_external_config( array(
		'skins' => array(
			'skin-name' => array(
				'full'  => array(
					'plugin-1',
					'plugin-2',
				),
				'lite'  => array(
					'plugin-1',
				),
				'demo'  => false,
				'thumb' => false,
				'name'  => esc_html__( 'Skin Name', 'croco-ik' ),
				'type'  => 'skin', // skin or model
			),
		),
	) );

	// Or from remote url
	croco_ik()->settings->register_external_config( array(
		'skins' => array(
			'get_from' => URL which is returns JSON with skins configuration,
		)
	) );

}
```
