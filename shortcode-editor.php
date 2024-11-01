<?php
/**
Plugin Name: TinyMCE Generic WP Shortcode Editor
Plugin URI: 
Description: Create or edit shortcodes through TinyMCE editor.
Version: 1.0
Author: Cau Guanabara
Author URI: http://www.caugb.com.br/
*/

  if(preg_match("/(post-new|post)\.php/", basename(getenv('SCRIPT_FILENAME')))) {
		add_action('init', 'gse_init');
		add_action('admin_print_scripts', 'gse_admin_js');
	}

function gse_init() {
  if(get_user_option('rich_editing') == 'true' && current_user_can('edit_posts')) {
    add_filter("mce_external_plugins", "gse_mce_plugin", 80);
    add_filter('mce_buttons', 'gse_mce_button', 80);
  }
}

function gse_mce_button($buttons) {
  array_push($buttons, 'separator', 'shortcode_editor');
  return $buttons;
}

function gse_mce_plugin($plugin_array) {
  $plugin_array['shortcode_editor'] = plugins_url(basename(dirname(__FILE__)).'/tinymce/plugins/shortcode-editor/shortcode_editor.js');
  return $plugin_array;
}

function gse_admin_js($buttons) {
  global $shortcode_tags;
  ?>
  <script type="text/javascript">
    var gse_shortcode_er = /\[(<?php print join('|', array_keys($shortcode_tags)); ?>)\s?([^\]]*)(?:\s*\/)?\](([^\[\]]+)\[\/\1\])?/g;
		addLoadEvent(function() { 
		  jQuery('form#post').submit(function() {
				var c = this.content; 
				c.value = c.value.replace(/(\[[^\]]+\S)(\s+sc_id="sc\d+")([^\]]*\])/g, '$1$3');
			});
		});
  </script>
  <?php
}
?>