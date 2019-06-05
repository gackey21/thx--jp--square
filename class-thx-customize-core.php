<?php
/*
Plugin Name: thx.jp²
Plugin URI:
Description: thx.jp² means 'Typesetting with Half-width-space eXtra in Japanese' ; made by thx.jp/
Version: 0.4.2
Author:Gackey.21
Author URI: https://thx.jp
License: GPL2
*/
?>
<?php
/*  Copyright 2019 Gackey.21 (email : gackey.21@gmail.com)

		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License, version 2, as
		published by the Free Software Foundation.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php
if ( ! class_exists( 'Thx_Customize_Core' ) ) {
	class Thx_Customize_Core {
		//読み込むurl
		static $push_js_url  = array();
		static $push_css_url = array();

		public function __construct() {
			$thx_cc_option = get_option( 'thx_cc_option' );
			$src_css_url   = plugins_url( 'src/css/', __FILE__ );
			$src_js_url    = plugins_url( 'src/js/', __FILE__ );

			//管理画面の設定
			add_action( '_admin_menu', 'thx_admin_menu' );
			add_action( 'admin_init', 'thx_settings_init' );

			//プラグインメニューの設定
			add_filter(
				'plugin_action_links_' . plugin_basename( __FILE__ ),
				array( $this, 'add_action_links' )
			);

			//自動アップデート
			require plugin_dir_path( __FILE__ ) . 'plugin-update-checker/plugin-update-checker.php';
			$my_update_checker = Puc_v4_Factory::buildUpdateChecker(
				'https://github.com/gackey21/thx--jp--square/',
				__FILE__,
				'thx-customize-core'
			);

			//アンインストール
			if ( function_exists( 'register_uninstall_hook' ) ) {
				register_uninstall_hook( __FILE__, 'Thx_Customize_Core::thx_cc_uninstall' );
			}

			//簡易的な日本語組版処理
			if ( '1' === $thx_cc_option['typesetting'] ) {
				self::$push_css_url[] = $src_css_url . 'thx-typesetting.css';
				add_filter( 'the_content', 'thx_typesetting', 21000 );
				add_filter( 'the_category_content', 'thx_typesetting', 21000 );
			}

			//行間の崩れないルビ
			if ( '1' === $thx_cc_option['ruby'] ) {
				self::$push_css_url[] = $src_css_url . 'thx-ruby.css';
				self::$push_js_url[]  = $src_js_url . 'thx-ruby.js';
			}

			//アンチエイリアス
			if ( '1' === $thx_cc_option['antialiase'] ) {
				self::$push_css_url[] = $src_css_url . 'thx-antialiase.css';
			}

			//テキストの自動拡大
			if ( '1' === $thx_cc_option['text_size_adjust'] ) {
				self::$push_css_url[] = $src_css_url . 'thx-text-size-adjust.css';
			}

			//引用符の解除
			if ( '1' === $thx_cc_option['remove_texturize'] ) {
				remove_filter( 'the_content', 'wptexturize' );
				remove_filter( 'the_excerpt', 'wptexturize' );
				remove_filter( 'the_title', 'wptexturize' );
			}

			//管理画面にCSSを追加
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_style_on_admin_page' ) );

			//キュー実行
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			//ブラウザ判別
			add_filter( 'body_class', array( $this, 'browser_body_class' ) );
		}//__construct()__construct()__construct()__construct()__construct()__construct()

		//アインインストール時にオプション削除
		static function thx_cc_uninstall() {
			$thx_cc_option = get_option( 'thx_cc_option' );
			if ( '1' !== $thx_cc_option['keep_option'] ) {
				delete_option( 'thx_cc_option' );
			}
		}

		//設定リンク追加
		public static function add_action_links( $links ) {
			$add_link = '<a href="admin.php?page=thx-jp-customize-core">設定</a>';
			array_unshift( $links, $add_link );
			return $links;
		}

		//ファイル読み込み
		public static function file_to_str( $path ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			if ( WP_Filesystem() ) {
				global $wp_filesystem;
				$str = $wp_filesystem->get_contents( $path );
				return $str;
			}
		}

		//ファイル書き出し
		public static function str_to_file( $path, $str ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			if ( WP_Filesystem() ) {
				global $wp_filesystem;
				$wp_filesystem->put_contents( $path, $str );
			}
		}

		//キューイング
		public static function enqueue_scripts() {
			foreach ( self::$push_css_url as $url ) {
				self::enqueue_file_style( $url );
			}
			foreach ( self::$push_js_url as $url ) {
				self::enqueue_file_script( $url );
			}
		}//enqueue_scripts()

		//cssファイルをキューイング
		public static function enqueue_file_style( $css_url ) {
			// cssのurlからファイル名のみ取り出す
			$css_name = preg_replace( '{.*\/}uis', '', $css_url );
			// cssのファイル名から拡張子を除去
			$css_name = preg_replace( '{\..*}uis', '', $css_name );
			//キュー
			wp_enqueue_style( $css_name, $css_url );
		}//enqueue_file_style()

		//jsファイルをキューイング
		public static function enqueue_file_script( $js_url ) {
			// jsのurlからファイル名のみ取り出す
			$js_name = preg_replace( '{.*\/}uis', '', $js_url );
			// jsのファイル名から拡張子を除去
			$js_name = preg_replace( '{\..*}uis', '', $js_name );
			//キュー
			wp_enqueue_script( $js_name, $js_url, array( 'jquery' ), false, true );
		}//enqueue_file_script()

		//管理画面にCSSを追加
		public static function enqueue_style_on_admin_page() {
			wp_enqueue_style( 'thx_admin', plugins_url( 'src/css/thx_admin.css', __FILE__ ) );
		}

		//文字列を正規表現で置換
		public static function str_preg_replace( $str, $preg_array ) {
			//正規表現式の数だけループ
			foreach ( $preg_array as $preg_match => $replace ) {
				$str = preg_replace( $preg_match, $replace, $str );

				// // $str内でマッチするものを$matchへ配列化
				// preg_match_all($preg_match, $str, $match);
				// // マッチした配列をループで置換
				// foreach ($match[0] as $value) {
				// 	$str = str_replace($value, $replace, $str);
				// }
			}
			return $str;
		}//str_preg_replace()

		//urlが存在するか確認
		public static function check_url_exist( $url ) {
			$response = @file_get_contents( $url, null, null, 0, 1 );
			if ( false !== $response ) {
				return true;
			} else {
				return false;
			}
		}//check_url_exist( $url )

		//ブラウザ判別
		function browser_body_class( $classes ) {
			global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

			if ( $is_lynx ) {
				$classes[] = 'lynx';
			} elseif ( $is_gecko ) {
				$classes[] = 'gecko';
			} elseif ( $is_opera ) {
				$classes[] = 'opera';
			} elseif ( $is_NS4 ) {
				$classes[] = 'ns4';
			} elseif ( $is_safari ) {
				$classes[] = 'safari';
			} elseif ( $is_chrome ) {
				$classes[] = 'chrome';
			} elseif ( $is_IE ) {
				$classes[] = 'ie';
			} else {
				$classes[] = 'unknown';
			}

			if ( $is_iphone ) {
				$classes[] = 'iphone';
			}
			return $classes;
		}//browser_body_class( $classes )

		//htmlをテキストとタグに分解
		public static function html_split_text_tag( $html ) {
			//alt内の「>」を文字参照に
			if ( preg_match_all( '{alt="[^\"]*>}uis', $html, $match ) ) {
				foreach ( $match as $value ) {
					$alt_amp = preg_replace( '{(>)}is', '&gt;', $value );
					$html    = str_replace( $value, $alt_amp, $html );
				}
			}

			//htmlをテキストとタグに分解・ペアリング
			$tag_match = '{(<.*?>)}uis';
			$pairing   = array_chunk(
				preg_split(
					$tag_match,
					$html,
					-1,
					PREG_SPLIT_DELIM_CAPTURE
				),
				2
			);

			//ペア補充（notice対策）
			$count                 = count( $pairing );
			$pairing[ $count - 1 ] = array( ' ', ' ' );

			return $pairing;
		}//html_split_text_tag( $html )
	}//class
}//! class_exists

require_once( 'src/php/menu.php' );
require_once( 'src/php/typesetting.php' );

new Thx_Customize_Core;
