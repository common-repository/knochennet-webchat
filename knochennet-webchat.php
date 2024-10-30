<?php
/**
 * Plugin Name: {IRC}knochennet Webchat
 * Plugin URI: http://knochennet.de/irc-knochennet-webchat-als-wordpress-plugin/
 * Description: Fügt den Webchat vom {IRC}knochennet in euer Wordpress ein. Dazu muss nur ein Shortcode in die jeweilige Seite/Artikel. <code>&#91;knochennet_webchat&#93;</code> oder <code>&#91;knochennet_webchat channel="channel"&#93;</code> wenn ein bestimmter Channel im {IRC}knochennet betreten werden soll.
 * Version: 0.6
 * Author: H.-Peter Pfeufer
 * Author URI: http://blog.ppfeufer.de
 */

define('KNOCHENNET_WEBCHAT_VERSION', '0.6');

/**
 * Shortcode für Channel des Webclients
 */
if(!function_exists('knochennet_webchat_shortcode')) {
	function knochennet_webchat_shortcode($atts) {
		$server = '';
		$channel = '';
		$layout = '';

		extract(shortcode_atts(array(
			'server' => 'irc.knochennet.de',
			'channel' => 'knochennet-lounge',
			'layout' => '6e6082e50f9fcbac0b58994176061bb6'
		), $atts));

		return '<iframe src="http://wbe04.mibbit.com/?settings=' . $layout . '&amp;server=' . $server . '&amp;channel=%23' . str_replace('#', '', $channel) . '&amp;nick=KN-Webchatter_??????" width="100%" height="550px"></iframe>';
	}

	/**
	 * Shortcode zu Wordpress hinzufügen
	 */
	add_shortcode('knochennet_webchat', 'knochennet_webchat_shortcode');
}

/**
 * Changelog bei Pluginupdate ausgeben.
 *
 * @since 0.1
 */
if(!function_exists('knochennet_webchat_update_notice')) {
	function knochennet_webchat_update_notice() {
		$url = 'http://plugins.trac.wordpress.org/browser/knochennet-webchat/trunk/readme.txt?format=txt';
		$data = '';

		if(ini_get('allow_url_fopen')) {
			$data = file_get_contents($url);
		} else {
			if(function_exists('curl_init')) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$data = curl_exec($ch);
				curl_close($ch);
			} // END if(function_exists('curl_init'))
		} // END if(ini_get('allow_url_fopen'))


		if($data) {
			$matches = null;
			$regexp = '~==\s*Changelog\s*==\s*=\s*[0-9.]+\s*=(.*)(=\s*' . preg_quote(KNOCHENNET_WEBCHAT_VERSION) . '\s*=|$)~Uis';

			if(preg_match($regexp, $data, $matches)) {
				$changelog = (array) preg_split('~[\r\n]+~', trim($matches[1]));

				echo '</div><div class="update-message" style="font-weight: normal;"><strong>What\'s new:</strong>';
				$ul = false;
				$version = 99;

				foreach($changelog as $index => $line) {
					if(version_compare($version, KNOCHENNET_WEBCHAT_VERSION, ">")) {
						if(preg_match('~^\s*\*\s*~', $line)) {
							if(!$ul) {
								echo '<ul style="list-style: disc; margin-left: 20px;">';
								$ul = true;
							} // END if(!$ul)


							$line = preg_replace('~^\s*\*\s*~', '', $line);
							echo '<li>' . $line . '</li>';
						} else {
							if($ul) {
								echo '</ul>';
								$ul = false;
							} // END if($ul)


							$version = trim($line, " =");
							echo '<p style="margin: 5px 0;">' . htmlspecialchars($line) . '</p>';
						} // END if(preg_match('~^\s*\*\s*~', $line))
					} // END if(version_compare($version, TWOCLICK_SOCIALMEDIA_BUTTONS_VERSION,">"))
				} // END foreach($changelog as $index => $line)


				if($ul) {
					echo '</ul><div style="clear: left;"></div>';
				} // END if($ul)


				echo '</div>';
			} // END if(preg_match($regexp, $data, $matches))
		} // END if($data)
	} // END function twoclick_buttons_update_notice()
} // END if(!function_exists('twoclick_buttons_update_notice'))

if(is_admin()) {
// 	Updatemeldung
	if(ini_get('allow_url_fopen') || function_exists('curl_init')) {
		add_action('in_plugin_update_message-' . plugin_basename(__FILE__), 'knochennet_webchat_update_notice');
	}
}