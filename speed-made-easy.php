<?php
/*
Plugin Name: Speed Made Easy
Plugin URI: https://veryeasy.io/speed-made-easy
Description: A set and forget solution for WordPress speed.
Version: 0.2
Requires at least: 5.0
Author: VeryEasy
Author URI: https://veryeasy.io/
License: Public Domain
License URI: https://wikipedia.org/wiki/Public_domain
Text Domain: speed-made-easy
*/

// block direct access to this file
if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

// add clear cache button
add_action( 'admin_bar_menu', 'speedmadeeasy_clear_cache', 100 );
function speedmadeeasy_clear_cache( $admin_bar ) {
    $admin_bar->add_menu( array(
        'id'    => 'speedmadeeasy-clear-cache',
        'title' => __( 'Clear Cache', 'speed-made-easy' ),
        'href'  => '?clear-cache',
        'meta'  => array(
            'title' => __( 'Clear Cache', 'speed-made-easy' ),            
        ),
    ));
	if ( isset( $_GET['clear-cache'] ) ) {
		wp_cache_flush();
		add_action( 'admin_notices', 'speedmadeeasy_admin_notice' );
		function speedmadeeasy_admin_notice() {
			echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Cache Cleared', 'speed-made-easy' ) . '</p></div>';
		}
	}
}

// define WP_CACHE
add_action( 'init', 'speedmadeeasy_add_wpconfig' );
function speedmadeeasy_add_wpconfig() {
	if ( !WP_CACHE ) {
		if ( $wp_config = @file_get_contents( ABSPATH . 'wp-config.php' ) ) {
			$wp_config = str_replace( '<?php', "<?php\ndefine( 'WP_CACHE', true ); // Speed Made Easy", $wp_config );
			if ( !@file_put_contents( ABSPATH. 'wp-config.php', $wp_config ) ) {
				return array( 'define( \'WP_CACHE\', true );' . __( 'needs to be added to', 'speed-made-easy' ) . ' wp-config.php', 'error' );
			}
		} else {
			return array( 'define( \'WP_CACHE\', true );' . __( 'needs to be added to', 'speed-made-easy' ) . ' wp-config.php', 'error' );
		}
	}
}

// remove define WP_CACHE
register_deactivation_hook( __FILE__, 'speedmadeeasy_remove_wpconfig' );
function speedmadeeasy_remove_wpconfig() {
	if ( WP_CACHE ) {
		if ( $wp_config = @file_get_contents( ABSPATH . 'wp-config.php' ) ) {
			$wp_config = str_replace( "<?php\ndefine( 'WP_CACHE', true ); // Speed Made Easy", '<?php', $wp_config );
			if ( !@file_put_contents( ABSPATH. 'wp-config.php', $wp_config ) ) {
				return array( 'define( \'WP_CACHE\', true );' . __( 'leftover in', 'speed-made-easy' ) . ' wp-config.php', 'error' );
			}
		} else {
			return array( 'define( \'WP_CACHE\', true );' . __( 'leftover in', 'speed-made-easy' ) . ' wp-config.php', 'error' );
		}
	}
}

// add to .htaccess
add_action( 'init', 'speedmadeeasy_add_htaccess' );
function speedmadeeasy_add_htaccess( $insertion ) {
	$insertion = array(
		'AddDefaultCharset UTF-8',
		'<IfModule mod_mime.c>',
		'AddCharset UTF-8 .atom .css .js .json .rss .vtt .xml',
		'</IfModule>',
		'<IfModule mod_headers.c>',
		'Header unset ETag',
		'</IfModule>',
		'FileETag None',
		'<IfModule mod_alias.c>',
		'<FilesMatch "\.(html|htm|rtf|rtx|txt|xsd|xsl|xml)$">',
		'<IfModule mod_headers.c>',
		'Header unset Pragma',
		'Header append Cache-Control "public"',
		'Header unset Last-Modified',
		'</IfModule>',
		'</FilesMatch>',
		'<FilesMatch "\.(css|htc|js|asf|asx|wax|wmv|wmx|avi|bmp|class|divx|doc|docx|eot|exe|gif|gz|gzip|ico|jpg|jpeg|jpe|json|mdb|mid|midi|mov|qt|mp3|m4a|mp4|m4v|mpeg|mpg|mpe|mpp|otf|odb|odc|odf|odg|odp|ods|odt|ogg|pdf|png|pot|pps|ppt|pptx|ra|ram|svg|svgz|swf|tar|tif|tiff|ttf|ttc|wav|wma|wri|xla|xls|xlsx|xlt|xlw|zip)$">',
		'<IfModule mod_headers.c>',
		'Header unset Pragma',
		'Header append Cache-Control "public"',
		'</IfModule>',
		'</FilesMatch>',
		'</IfModule>',
		'<IfModule mod_expires.c>',
		'ExpiresActive on',
		'ExpiresDefault                              "access plus 1 month"',
		'ExpiresByType text/cache-manifest           "access plus 0 seconds"',
		'ExpiresByType text/html                     "access plus 0 seconds"',
		'ExpiresByType text/xml                      "access plus 0 seconds"',
		'ExpiresByType application/xml               "access plus 0 seconds"',
		'ExpiresByType application/json              "access plus 0 seconds"',
		'ExpiresByType application/rss+xml           "access plus 1 hour"',
		'ExpiresByType application/atom+xml          "access plus 1 hour"',
		'ExpiresByType image/x-icon                  "access plus 1 week"',
		'ExpiresByType image/gif                     "access plus 4 months"',
		'ExpiresByType image/png                     "access plus 4 months"',
		'ExpiresByType image/jpeg                    "access plus 4 months"',
		'ExpiresByType image/webp                    "access plus 4 months"',
		'ExpiresByType video/ogg                     "access plus 1 month"',
		'ExpiresByType audio/ogg                     "access plus 1 month"',
		'ExpiresByType video/mp4                     "access plus 1 month"',
		'ExpiresByType video/webm                    "access plus 1 month"',
		'ExpiresByType text/x-component              "access plus 1 month"',
		'ExpiresByType font/ttf                      "access plus 4 months"',
		'ExpiresByType font/otf                      "access plus 4 months"',
		'ExpiresByType font/woff                     "access plus 4 months"',
		'ExpiresByType font/woff2                    "access plus 4 months"',
		'ExpiresByType image/svg+xml                 "access plus 1 month"',
		'ExpiresByType application/vnd.ms-fontobject "access plus 1 month"',
		'ExpiresByType text/css                      "access plus 1 year"',
		'ExpiresByType application/javascript        "access plus 1 year"',
		'</IfModule>',
		'<IfModule mod_deflate.c>',
		'SetOutputFilter DEFLATE',
		'<IfModule mod_setenvif.c>',
		'<IfModule mod_headers.c>',
		'SetEnvIfNoCase ^(Accept-EncodXng|X-cept-Encoding|X{15}|~{15}|-{15})$ ^((gzip|deflate)\s*,?\s*)+|[X~-]{4,13}$ HAVE_Accept-Encoding',
		'RequestHeader append Accept-Encoding "gzip,deflate" env=HAVE_Accept-Encoding',
		'SetEnvIfNoCase Request_URI \\',
		'\.(?:gif|jpe?g|png|rar|zip|exe|flv|mov|wma|mp3|avi|swf|mp?g|mp4|webm|webp|pdf)$ no-gzip dont-vary',
		'</IfModule>',
		'</IfModule>',
		'<IfModule mod_filter.c>',
		'AddOutputFilterByType DEFLATE application/atom+xml \\',
		'application/javascript \\',
		'application/json \\',
		'application/rss+xml \\',
		'application/vnd.ms-fontobject \\',
		'application/x-font-ttf \\',
		'application/xhtml+xml \\',
		'application/xml \\',
		'font/opentype \\',
		'image/svg+xml \\',
		'image/x-icon \\',
		'text/css \\',
		'text/html \\',
		'text/plain \\',
		'text/x-component \\',
		'text/xml',
		'</IfModule>',
		'<IfModule mod_headers.c>',
		'Header append Vary: Accept-Encoding',
		'</IfModule>',
		'</IfModule>',
	);
	$htaccess = ABSPATH . '.htaccess';
	if ( function_exists( 'insert_with_markers') ) {
		return insert_with_markers( $htaccess, 'Speed Made Easy', ( array ) $insertion );
	}
}

// remove from .htaccess
register_deactivation_hook( __FILE__, 'speedmadeeasy_remove_htaccess' );
function speedmadeeasy_remove_htaccess() {
	$htaccess = ABSPATH . '.htaccess';
	return insert_with_markers( $htaccess, 'Speed Made Easy', '' );
}