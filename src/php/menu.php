<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
//プラグイン名
// define( 'PLUG_IN_NAME', 'thx.jp/' );
$thx_cc_option = get_option( 'thx_cc_option' );
if ( ! $thx_cc_option ) {
	$thx_cc_option = array(
		'antialiase'       => '1',
		'text_size_adjust' => '1',
		'remove_texturize' => '1',
		'ruby'             => '1',
		'typesetting'      => '1',
		'keep_option'      => '1',
		// 'hook_customize' => 0,
		// 'hook_customize_array' => '',
		// 'content_replace' => 0,
		// 'content_replace_array' => '',
		// 'js_tag' => 0,
		// 'js_async_defer' => 'async',
		// 'js_async_defer_array' => '',
	);
	update_option( 'thx_cc_option', $thx_cc_option );
}//if( !$thx_cc_option )

function thx_admin_menu() {
	//メインメニューに追加する
	add_menu_page(
		'thx.jp/ の設定',
		'thx.jp/',
		'manage_options',
		'thx-jp-customize-core',
		'thx_settings_plugin_options',
		'dashicons-admin-plugins',
		30
	);
	//サブメニュー作成
	add_submenu_page(
		'thx-jp-customize-core',
		'thx.jp² の設定',
		'thx.jp²',
		'manage_options',
		'thx-jp-customize-core',
		'thx_settings_plugin_options'
	);
}//thx_admin_menu

// フォームの枠組を出力する
function thx_settings_plugin_options() {
	// ユーザーが必要な権限を持つか確認する必要がある
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'このページにアクセスする管理者権限がありません。' ) );
	}
	?>
	<div class="wrap">
		<form action="options.php" method="post">
			<?php settings_fields( 'thx_settings-group' ); // グループ名 ?>
			<?php do_settings_sections( 'thx_settings' ); // ページ名 ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php if ( isset( $_GET['settings-updated'] ) ) : ?>
		<?php if ( $_GET['settings-updated'] ) : ?>
			<div class="updated notice is-dismissible">
				<p><strong>設定を保存しました。</strong></p>
			</div>
		<?php endif; ?>
	<?php endif;//isset ?>
	<?php
	// var_dump($thx_cc_option);
}

// 管理画面の作成
function thx_settings_init() {
	register_setting( 'thx_settings-group', 'thx_cc_option' );

	//セクション設定
	add_settings_section(
		'thx_settings_section', // セクション名
		'thx.jp²に関する設定', // タイトル
		'thx_settings_section_callback', // echo '<p>プラグインのON/OFFを切り替えます。</p>';
		'thx_settings' // このセクションを表示するページ名。do_settings_sectionsで設定
	);
	function thx_settings_section_callback() {
		echo '<p>各機能のON/OFFを切り替えます。</p>';
	}

		//簡易的な日本語組版処理
		add_settings_field(
			'thx_typesetting',
			'簡易的な日本語組版処理',
			'thx_single_checkbox_callback',
			'thx_settings',
			'thx_settings_section',
			array(
				'option_name'       => 'thx_cc_option',
				'option_array_name' => 'typesetting',
				'comment'           => '役物ツメ・ぶら下がり・和欧間スペースの付与を行う',
				'add'               => '',
			)
		);

		//ルビ
		add_settings_field(
			'thx_ruby',
			'行間の崩れないルビ',
			// 'thx_checkbox_callback',
			'thx_single_checkbox_callback',
			'thx_settings',
			'thx_settings_section',
			array(
				'option_name'       => 'thx_cc_option',
				'option_array_name' => 'ruby',
				// 'comment' => array(
				// 	'1' => '行間の崩れないルビを使用する',
				// ),
				'comment'           => '行間の崩れないルビを使用する',
				'add'               => '',
			)
		);

	//アンチエイリアス
	add_settings_field(
		'thx_antialiase',
		'アンチエイリアス処理',
		// 'thx_checkbox_callback',
		'thx_single_checkbox_callback',
		'thx_settings',
		'thx_settings_section',
		array(
			'option_name'       => 'thx_cc_option',
			'option_array_name' => 'antialiase',
			// 'comment' => array(
			// 	'1' => '表示される文字にアンチエイリアス処理を施す',
			// ),
			'comment'           => '表示される文字にアンチエイリアス処理を施す',
			'add'               => '',
		)
	);

	//テキストの自動拡大
	add_settings_field(
		'thx_text_size_adjust',
		'テキストの自動拡大',
		// 'thx_checkbox_callback',
		'thx_single_checkbox_callback',
		'thx_settings',
		'thx_settings_section',
		array(
			'option_name'       => 'thx_cc_option',
			'option_array_name' => 'text_size_adjust',
			// 'comment' => array(
			// 	'1' => 'モバイル端末でのテキスト自動拡大を無効化',
			// ),
			'comment'           => 'モバイル端末でのテキスト自動拡大を無効化',
			'add'               => '',
		)
	);

	//引用符の解除
	add_settings_field(
		'thx_remove_texturize',
		'引用符の解除',
		// 'thx_checkbox_callback',
		'thx_single_checkbox_callback',
		'thx_settings',
		'thx_settings_section',
		array(
			'option_name'       => 'thx_cc_option',
			'option_array_name' => 'remove_texturize',
			// 'comment' => array(
			// 	'1' => '引用符などの自動変換機能を解除する',
			// ),
			'comment'           => '引用符などの自動変換機能を解除する',
			'add'               => '',
		)
	);

	//オプションの保持
	add_settings_field(
		'thx_keep_option',
		'設定の保持',
		'thx_single_checkbox_callback',
		'thx_settings',
		'thx_settings_section',
		array(
			'option_name'       => 'thx_cc_option',
			'option_array_name' => 'keep_option',
			'comment'           => 'アンインストール時に設定を残す',
			'add'               => '',
		)
	);
}//function thx_settings_init()

// コールバック
function thx_single_checkbox_callback( $args ) {
	// $thx_cc_option = get_option('thx_cc_option');
	// $option_name = $args['option_array_name'];
	if ( $args['option_array_name'] ) {
		$option_array_name = $args['option_array_name'];
		$option_name       = $args['option_name'];
		$thx_option        = get_option( $option_name )[ $option_array_name ];
		$option_name       = $option_name . '[' . $option_array_name . ']';
		// $option_name = 'thx_cc_option['.$option_array_name.']';
		// $thx_option = get_option('thx_cc_option')[$option_array_name];
	} else {
		$option_name = $args['option_name'];
		$thx_option  = get_option( $option_name );
	}
	$name_id = 'name="' . $option_name . '" id="' . $option_name . '"';
	// $name_id = 'name="thx_cc_option['.$option_name.']" id="thx_'.$option_name.'"';
	?>
	<p>
		<input type="hidden" <?php echo $name_id; ?> value="0" />
		<input type="checkbox" <?php echo $name_id; ?> value="1"
		<?php checked( $thx_option, '1' ); ?>
		/><?php echo $args['comment']; ?>
	</p>
	<?php
	if ( $args['add'] ) {
		call_user_func( $args['add'], $args['arg'] );
	}
}
function thx_checkbox_callback( $args ) {
	if ( $args['option_array_name'] ) {
		$option_array_name = $args['option_array_name'];
		$option_name       = $args['option_name'];
		$thx_option        = get_option( $option_name )[ $option_array_name ];
		$option_name       = $option_name . '[' . $option_array_name . ']';
		// $option_name = 'thx_cc_option['.$option_array_name.']';
		// $thx_option = get_option('thx_cc_option')[$option_array_name];
	} else {
		$option_name = $args['option_name'];
		$thx_option  = get_option( $option_name );
	}
	$name_comment_flag = $args['comment'];
	?>
	<?php foreach ( $name_comment_flag as $name => $comment_flag ) : ?>
		<?php foreach ( $comment_flag as $comment => $flag ) : ?>
			<?php $name_id = 'name="' . $option_name . '[' . $name . ']" id="' . $option_name . '"'; ?>
			<p>
				<input type="hidden" <?php echo $name_id; ?> value="0" />
				<input type="checkbox" <?php echo $name_id; ?> value="1"
				<?php checked( $thx_option[ $name ], '1' ); ?>
				/>
				<?php echo $comment; ?>
			</p>
		<?php endforeach;//$comment => $flag ?>
	<?php endforeach;//$name => $comment_flag ?>
	<?php
	// var_dump($thx_option);
	if ( $args['add'] ) {
		call_user_func( $args['add'], $args['arg'] );
	}
}
function thx_radio_callback( $args ) {
	if ( $args['option_array_name'] ) {
		$option_array_name = $args['option_array_name'];
		$option_name       = $args['option_name'];
		$thx_option        = get_option( $option_name )[ $option_array_name ];
		$option_name       = $option_name . '[' . $option_array_name . ']';
		// $option_name = 'thx_cc_option['.$option_array_name.']';
		// $thx_option = get_option('thx_cc_option')[$option_array_name];
	} else {
		$option_name = $args['option_name'];
		$thx_option  = get_option( $option_name );
	}
	$key_comment = $args['comment'];
	$name_id     = 'name="' . $option_name . '" id="' . $option_name . '"';
	?>
	<?php foreach ( $key_comment as $key => $comment ) : ?>
	<p>
		<input type="radio" <?php echo $name_id; ?> value=<?php echo $key; ?>
		<?php checked( $thx_option, $key ); ?>
		/>
		<?php echo $comment; ?>
	</p>
	<?php endforeach; ?>
	<?php
	if ( $args['add'] ) {
		call_user_func( $args['add'], $args['arg'] );
	}
	// var_dump(get_option('thx_cc_option'));
	// var_dump($thx_option);
}
function thx_textarea_callback( $args ) {
	if ( $args['option_array_name'] ) {
		$option_array_name = $args['option_array_name'];
		$option_name       = $args['option_name'];
		$thx_option        = get_option( $option_name )[ $option_array_name ];
		$option_name       = $option_name . '[' . $option_array_name . ']';
		// $option_name = 'thx_cc_option['.$option_array_name.']';
		// $thx_option = get_option('thx_cc_option')[$option_array_name];
	} else {
		$option_name = $args['option_name'];
		$thx_option  = get_option( $option_name );
	}
	if ( isset( $args['rows'] ) ) {
		$rows = $args['rows'];
	} else {
		$rows = '4';
	}
	if ( isset( $args['comment'] ) ) {
		$comment = $args['comment'];
	} else {
		$comment = '';
	}

	if ( isset( $args['placeholder'] ) ) {
		$placeholder = $args['placeholder'];
	} else {
		$placeholder = '';
	}

	$name_id = 'name="' . $option_name . '" id="' . $option_name . '"';
	// $thx_cc_option = get_option('thx_cc_option');
	// $option_name = $args['option_array_name'];
	// $comment = $args['comment'];
	// $placeholder = $args['placeholder'];
	// $name_id = 'name="thx_cc_option['.$option_name.']" id="thx_'.$option_name.'"';
	?>
	<?php if ( $comment ) : ?>
		<p><?php echo $comment; ?></p>
	<?php endif; ?>
	<textarea <?php echo $name_id; ?> class="thx-monospace" cols="80" rows=<?php echo $rows; ?> placeholder=<?php echo $placeholder; ?>><?php echo $thx_option; ?></textarea>
	<?php
	// var_dump($thx_option);
}
function thx_date_url_callback( $args ) {
	if ( $args['option_array_name'] ) {
		$option_array_name = $args['option_array_name'];
		$option_name       = $args['option_name'];
		$comment           = $args['comment'];
		$thx_option        = get_option( $option_name )[ $option_array_name ];
		$option_name       = $option_name . '[' . $option_array_name . ']';
	} else {
		$option_name = $args['option_name'];
		$thx_option  = get_option( $option_name );
	}
	?>
	<table>
		<tr>
			<td></td>
			<td>確認日</td>
			<td>有効期限</td>
			<td>url</td>
		</tr>

		<?php
		foreach ( $comment as $dis ) {
			if ( ! isset( $thx_option[ $dis ] ) ) {
				$thx_option[ $dis ] = array(
					'checked_date' => '',
					'expired_date' => '',
					'url'          => '',
				);
			}
			// var_dump( $thx_option );
			// echo '<br />';
			$option_checked_date  = $option_name . "[$dis][checked_date]";
			$option_expired_date  = $option_name . "[$dis][expired_date]";
			$option_url           = $option_name . "[$dis][url]";
			$name_id_checked_date = 'name="' . $option_checked_date . '" id="' . $option_checked_date . '" value="' . $thx_option[ $dis ]['checked_date'] . '"';
			$name_id_expired_date = 'name="' . $option_expired_date . '" id="' . $option_expired_date . '" value="' . $thx_option[ $dis ]['expired_date'] . '"';
			$name_id_url          = 'name="' . $option_url . '" id="' . $option_url . '" value="' . $thx_option[ $dis ]['url'] . '"';
			?>
			<tr>
				<td><?php echo $dis; ?></td>
				<td><input type="text" <?php echo $name_id_checked_date; ?>></input></td>
				<td><input type="date" <?php echo $name_id_expired_date; ?>></input></td>
				<td><input type="text" <?php echo $name_id_url; ?>></input></td>
			</tr>
			<?php
		}
		?>
	</table>
	<?php
	// var_dump( $thx_option );
}
