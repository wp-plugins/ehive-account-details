<?php
/*
	Plugin Name: eHive Account Details
	Plugin URI: http://developers.ehive.com/wordpress-plugins/
	Author: Vernon Systems limited
	Description: Displays the public profile page for an eHive account. The <a href="http://developers.ehive.com/wordpress-plugins#ehiveaccess" target="_blank">eHiveAccess plugin</a> must be installed.
	Version: 2.1.2
	Author URI: http://vernonsystems.com
	License: GPL2+
*/
/*
	Copyright (C) 2012 Vernon Systems Limited

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/	
if (in_array('ehive-access/EHiveAccess.php', (array) get_option('active_plugins', array()))) {
	
	define('EHIVE_ACCOUNT_DETAILS_PLUGIN_DIR', plugin_dir_url( __FILE__ ));

    class EHiveAccountDetails {
    	
    	const CURRENT_VERSION = 1; // Increment each time an upgrade is required. (Options added or deleted.)
    	const EHIVE_ACCOUNT_DETAILS_OPTIONS = "ehive_account_details_options";
    	   
        function __construct() {
                        
        	add_action("admin_init", array(&$this, "ehive_account_details_admin_options_init"));        	
        	add_action("admin_menu", array(&$this, "ehive_account_details_admin_menu"));
        	         	
            add_action('get_ehive_account_details', array(&$this, 'get_ehive_account_details'));
                        
			add_action( 'wp_print_styles', array(&$this,'enqueue_styles')); 
			add_action( 'wp_print_scripts', array(&$this,'enqueue_scripts'));

			add_shortcode('ehive_account_details', array(&$this, 'ehive_account_details_shortcode'));				
        }
                
        function ehive_account_details_admin_options_init() {
        	
        	$this->ehive_plugin_update();
        	
        	wp_enqueue_script( 'jquery' );
        	 
        	wp_enqueue_style( 'farbtastic' );
        	wp_enqueue_script( 'farbtastic' );
        	
        
        	register_setting('ehive_account_details_options', 'ehive_account_details_options', array(&$this, 'plugin_options_validate') );
        	 
        	add_settings_section('comment_section', '', array(&$this, 'comment_section_fn'), __FILE__);
        	 
        	add_settings_section('style_section', 'CSS', array(&$this, 'style_section_fn'), __FILE__);
        	 
        	add_settings_section('css_inline_section', 'CSS - inline', array(&$this, 'css_inline_section_fn'), __FILE__);
        	
        }
        
        function plugin_options_validate($input) {
        	add_settings_error('ehive_account_details_options', 'updated', 'eHive Account Details settings saved.', 'updated');
        	return $input;
        }
        
        function  comment_section_fn() {
        	echo "<p><em>An overview of the plugin and shortcode documentation is available in the help.</em></p>";
        }
        
        function style_section_fn() {
        	add_settings_field('plugin_css_enabled', 'Enable plugin stylesheet', array(&$this, 'plugin_css_enabled_fn'), __FILE__, 'style_section');
        	add_settings_field('css_class', 'Custom class selector', array(&$this, 'css_class_fn'), __FILE__, 'style_section');
        }
        
        function css_inline_section_fn() {
        	add_settings_field('gallery_background_colour', 'Gallery background colour', array(&$this, 'gallery_background_colour_fn'), __FILE__, 'css_inline_section');
        	add_settings_field('gallery_border_colour', 'Gallery border colour', array(&$this, 'gallery_border_colour_fn'), __FILE__, 'css_inline_section');
        	add_settings_field('gallery_border_width', 'Gallery border width', array(&$this, 'gallery_border_width_fn'), __FILE__, 'css_inline_section');
        	add_settings_field('image_background_colour', 'Image background colour', array(&$this, 'image_background_colour_fn'), __FILE__, 'css_inline_section');
        	add_settings_field('image_padding', 'Image padding', array(&$this, 'image_padding_fn'), __FILE__, 'css_inline_section');
        	add_settings_field('image_border_colour', 'Image border colour', array(&$this, 'image_border_colour_fn'), __FILE__, 'css_inline_section');
        	add_settings_field('image_border_width', 'Image border width', array(&$this, 'image_border_width_fn'), __FILE__, 'css_inline_section');
        	echo '<div class="ehive-options-demo-image account-detail-item"><img src="'.EHIVE_ACCOUNT_DETAILS_PLUGIN_DIR.'images/account_details_item.png" /></div>';
        }
        
        function plugin_css_enabled_fn() {
        	$options = get_option('ehive_account_details_options');
        	if($options['plugin_css_enabled']) {
        		$checked = ' checked="checked" ';
        	}
        	echo "<input ".$checked." id='plugin_css_enabled' name='ehive_account_details_options[plugin_css_enabled]' type='checkbox' />";
        }
        
        function css_class_fn() {
        	$options = get_option('ehive_account_details_options');
        	echo "<input id='css_class' name='ehive_account_details_options[css_class]' class='regular-text' type='text' value='{$options['css_class']}' />";
        }
        	
        function gallery_background_colour_fn() {
        	$options = get_option('ehive_account_details_options');
        	if(isset($options['gallery_background_colour_enabled']) && $options['gallery_background_colour_enabled'] == 'on') {
				$checked = ' checked="checked" ';
        	}
			echo "<input class='medium-text' id='gallery_background_colour' name='ehive_account_details_options[gallery_background_colour]' type='text' value='{$options['gallery_background_colour']}' />";
			echo '<div id="gallery_background_colourpicker"></div>';
			echo "<td><input ".$checked." id='gallery_background_colour_enabled' name='ehive_account_details_options[gallery_background_colour_enabled]' type='checkbox' /></td>";
		}
        
        function gallery_border_colour_fn() {
        	$options = get_option('ehive_account_details_options');
        	if(isset($options['gallery_border_colour_enabled']) && $options['gallery_border_colour_enabled'] == 'on') {
        		$checked = ' checked="checked" ';
			}
			echo "<input class='medium-text' id='gallery_border_colour' name='ehive_account_details_options[gallery_border_colour]' type='text' value='{$options['gallery_border_colour']}' />";
			echo '<div id="gallery_border_colourpicker"></div>';
			echo "<td><input ".$checked." id='gallery_border_colour_enabled' name='ehive_account_details_options[gallery_border_colour_enabled]' type='checkbox' /></td>";
		}
        
		function gallery_border_width_fn() {
        	$options = get_option('ehive_account_details_options');
			if(isset($options['gallery_border_width_enabled']) && $options['gallery_border_width_enabled'] == 'on') {
				$checked = ' checked="checked" ';
			}
			echo "<input class='small-text' id='gallery_border_width' name='ehive_account_details_options[gallery_border_width]' type='number' value='{$options['gallery_border_width']}' />";
		}
        
		function image_background_colour_fn() {
			$options = get_option('ehive_account_details_options');
			if(isset($options['image_background_colour_enabled']) && $options['image_background_colour_enabled'] == 'on') {
				$checked = ' checked="checked" ';
			}
			echo "<input class='medium-text' id='image_background_colour' name='ehive_account_details_options[image_background_colour]' type='text' value='{$options['image_background_colour']}' />";
			echo '<div id="image_background_colourpicker"></div>';
			echo "<td><input ".$checked." id='image_background_colour_enabled' name='ehive_account_details_options[image_background_colour_enabled]' type='checkbox' /></td>";
		}
        
		function image_padding_fn() {
			$options = get_option('ehive_account_details_options');
			if(isset($options['image_padding_enabled']) && $options['image_padding_enabled'] == 'on') {
        		$checked = ' checked="checked" ';
        	}
			echo "<input class='small-text' id='image_padding' name='ehive_account_details_options[image_padding]' type='number' value='{$options['image_padding']}' />";
			echo "<td><input ".$checked." id='image_padding_enabled' name='ehive_account_details_options[image_padding_enabled]' type='checkbox' /></td>";
		}
        
		function image_border_colour_fn() {
        	$options = get_option('ehive_account_details_options');
        	if(isset($options['image_border_colour_enabled']) && $options['image_border_colour_enabled'] == 'on') {
        		$checked = ' checked="checked" ';
			}
			echo "<input class='medium-text' id='image_border_colour' name='ehive_account_details_options[image_border_colour]' type='text' value='{$options['image_border_colour']}' />";
			echo '<div id="image_border_colourpicker"></div>';
			echo "<td rowspan='2'><input ".$checked." id='image_border_colour_enabled' name='ehive_account_details_options[image_border_colour_enabled]' type='checkbox' /></td>";
		}
        
		function image_border_width_fn() {
        	$options = get_option('ehive_account_details_options');
        	if(isset($options['image_border_width_enabled']) && $options['image_border_width_enabled'] == 'on') {
        		$checked = ' checked="checked" ';
        	}
        	echo "<input class='medium-text' id='image_border_width' name='ehive_account_details_options[image_border_width]' type='number' value='{$options['image_border_width']}' />";
        }

        
        function ehive_account_details_admin_menu() {
        
        	global $ehive_account_details_options_page;
        
        	$ehive_account_details_options_page = add_submenu_page('ehive_access', 'eHive Account', 'Account Details', 'manage_options', 'ehive_account', array(&$this, 'ehive_account_details_options_page'));
        
        	add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'ehive_account_details_plugin_action_links'), 10, 2);
        
        	add_action("load-$ehive_account_details_options_page",array(&$this, "ehive_account_details_options_help"));
        	 
        	add_action("admin_print_styles-" . $ehive_account_details_options_page, array(&$this, "ehive_account_details_admin_enqueue_styles") );
        }				

        function ehive_account_details_plugin_action_links($links, $file) {
        	$settings_link = '<a href="admin.php?page=ehive_account">' . __('Settings') . '</a>';
        	array_unshift($links, $settings_link); // before other links
        	return $links;
        }
        
        function ehive_account_details_options_help() {
        	global $ehive_account_details_options_page;
        
        	$screen = get_current_screen();
        	if ($screen->id != $ehive_account_details_options_page)
        	return;
        
        	$screen->add_help_tab(array('id'      => 'ehive-account-details-overview',
        								'title'   => 'Overview',
        								'content' => "<p>Displays the details for an eHive account",
        	));
        	 
        	$htmlShortcode = "<p><strong>Shortcode</strong> [ehive_account_details]</p>";
        	$htmlShortcode.= "<p><strong>Attributes:</strong></p>";
        	$htmlShortcode.= "<ul>";
        	 
        	$htmlShortcode.= '<li><strong>css_class</strong> - Adds a custom class selector to the plugin markup.</li>';
        	$htmlShortcode.= '<li><strong>account_id</strong> - Display the eHive account details for the account id. Attribute, a valid account id.</li>';
        	
        	$htmlShortcode.= '<p><strong>Examples:</strong></p>';
        	$htmlShortcode.= '<p>[ehive_account_details]<br/>Shortcode with no attributes. Attributes default to the options settings.</p>';
        	$htmlShortcode.= '<p>[ehive_account_details css_class="myClass" account_id="3406"]<br/>Displays the details for the Pohutukawa Collection account in eHive with a custom class selector "myClass".</p>';
        	$htmlShortcode.= "</ul>";
        	
        	$screen->add_help_tab(array('id'	  => 'ehive-account-details-shortcode',
        								'title'	  => 'Shortcode',
        								'content' => $htmlShortcode
        	));
        	
        	$screen->set_help_sidebar('<p><strong>For more information:</strong></p><p><a href="http://developers.ehive.com/wordpress-plugins#ehiveaccountdetails" target="_blank">Documentation for eHive plugins</a></p>');
        }

        function ehive_account_details_admin_enqueue_styles() {
        	wp_enqueue_style('eHiveAdminCSS');
        }
        
		function ehive_account_details_options_page() {
			?>
		    <div class="wrap">
				<div class="icon32" id="icon-options-ehive"><br></div>
					<h2>eHive Account Details Settings</h2>    
					<?php settings_errors();?>    		
					<form action="options.php" method="post">
						<?php settings_fields('ehive_account_details_options'); ?>
						<?php do_settings_sections(__FILE__); ?>
						<p class="submit">
							<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
						</p>
					</form>
				</div>
			<?php
		}            
        
        public function enqueue_styles() {        
			global $eHiveAccess; 
        	
        	$accountDetailsPageId = $eHiveAccess->getAccountDetailsPageId();
        	
			if (is_page( $accountDetailsPageId )) {        	        	

				$options = get_option('ehive_account_details_options');
				
				if ($options[plugin_css_enabled] == 'on') {
	        		wp_register_style($handle = 'eHiveAccountDetailsCSS', $src = plugins_url('eHiveAccountDetails.css', '/ehive-account-details/css/eHiveAccountDetails.css'), $deps = array(), $ver = '0.0.1', $media = 'all');
	        		wp_enqueue_style( 'eHiveAccountDetailsCSS');
				}
	
	        	wp_register_style($handle = 'prettyPhoto', $src = plugins_url('prettyPhoto.css', '/ehive-account-details/js/prettyphoto/css/prettyPhoto'), $deps = array(), $ver = '1.0.0', $media = 'all');
	        	wp_enqueue_style( 'prettyPhoto');
        	}        	
        }
        
        public function enqueue_scripts() {
        	global $eHiveAccess;
        	
			$accountDetailsPageId = $eHiveAccess->getAccountDetailsPageId();
			        	        	
        	if (is_page( $accountDetailsPageId )){        	        	

	        	wp_enqueue_script( 'jquery' );        	
	        		        		        	
	        	wp_register_script($handle = 'jcarousellite', $src= plugins_url('jcarousellite_1.0.1.min.js', '/ehive-account-details/js/jcarousellite_1.0.1.min.js'), $deps = array('jquery'), $ver = '1.0.0', false);          	
	        	wp_enqueue_script( 'jcarousellite' );        	
	
	        	wp_register_script($handle = 'prettyPhoto', $src= plugins_url('jquery.prettyPhoto.js', '/ehive-account-details/js/prettyphoto/js/jquery.prettyPhoto.js'), $deps = array('jquery'), $ver = '1.0.0', false);          	
	        	wp_enqueue_script( 'prettyPhoto' );

	        	wp_register_script($handle = 'googleMapAPI', $src='http://maps.google.com/maps/api/js?sensor=false', $deps = array('jquery'), '', false);
	        	wp_enqueue_script( 'googleMapAPI' );
	        	
	        	wp_register_script($handle = 'eHiveAccountDetails', $src= plugins_url('eHiveAccountDetails.js', '/ehive-account-details/js/eHiveAccountDetails.js'), $deps = array('jquery','jcarousellite','prettyPhoto','googleMapAPI'), $ver = '1.0.0', false);          	
	        	wp_enqueue_script( 'eHiveAccountDetails' );	        	
        	}   
        }        	        
        
        public function ehive_account_details_shortcode($atts, $content) {
        	$options = get_option('ehive_account_details_options');
        	
        	extract(shortcode_atts(array('css_class'  => array_key_exists('css_class', $options) ? $options['css_class'] : '',
        								 'account_id' => 0,
        								 'gallery_background_colour'				=> array_key_exists('gallery_background_colour', $options) ? $options['gallery_background_colour'] : '#f3f3f3',
										 'gallery_background_colour_enabled'		=> array_key_exists('gallery_background_colour_enabled', $options) ? $options['gallery_background_colour_enabled'] : 'on',
										 'gallery_border_colour'					=> array_key_exists('gallery_border_colour', $options) ? $options['gallery_border_colour'] : '#666666',
										 'gallery_border_colour_enabled'			=> array_key_exists('gallery_border_colour_enabled', $options) ? $options['gallery_border_colour_enabled'] : '',
										 'gallery_border_width' 					=> array_key_exists('gallery_border_width', $options) ? $options['gallery_border_width'] : '2',
										 'image_background_colour'					=> array_key_exists('image_background_colour', $options) ? $options['image_background_colour'] : '#ffffff',
										 'image_background_colour_enabled'			=> array_key_exists('image_background_colour_enabled', $options) ? $options['image_background_colour_enabled'] : 'on',
										 'image_padding' 							=> array_key_exists('image_padding', $options) ? $options['image_padding'] : '1',
										 'image_padding_enabled' 					=> array_key_exists('image_padding_enabled', $options) ? $options['image_padding_enabled'] : 'on',
										 'image_border_colour'						=> array_key_exists('image_border_colour', $options) ? $options['image_border_colour'] : '#666666',
										 'image_border_colour_enabled'				=> array_key_exists('image_border_colour_enabled', $options) ? $options['image_border_colour_enabled'] : 'on',
										 'image_border_width' 						=> array_key_exists('image_border_width', $options) ? $options['image_border_width'] : '2'), $atts));
        	
        	if ($account_id == 0) {
        		$account_id = ehive_get_var('ehive_account_id');
        	}
        	                        
            global $eHiveAccess;
            $siteType = $eHiveAccess->getSiteType();
            $communityId = $eHiveAccess->getCommunityId();

            $eHiveApi = $eHiveAccess->eHiveApi();
            
            try {
	            switch($siteType) {
	            	case 'Account':
	            		$account = $eHiveApi->getAccount($account_id);
	            		break;
	            	case 'Community':
	            		$account = $eHiveApi->getAccountInCommunity($communityId, $account_id);
	            		break;
	            	default:
	            		$account = $eHiveApi->getAccount($account_id);
	            }
            } catch (Exception $exception) {
				error_log('EHive Account Details plugin returned and error while accessing the eHive API: ' . $exception->getMessage());
				$eHiveApiErrorMessage = " ";
				if ($eHiveAccess->getIsErrorNotificationEnabled()) {
					$eHiveApiErrorMessage = $eHiveAccess->getErrorMessage();
				}
			}
            $template = locate_template(array('eHiveAccountDetails.php'));
            if ('' == $template) {
            	$template = "templates/eHiveAccountDetails.php";
            }
            ob_start();
            require($template);
            $content = ob_get_clean();
            return apply_filters('ehive_account_details', $content);
        }
                
        function query_vars($vars) {
            $vars[] = 'ehive_account_id';
            return $vars;
        }
        
        function add_rewrite_rules($rules) {
            global $eHiveAccess, $wp_rewrite;
            
            $pageId = $eHiveAccess->getAccountDetailsPageId();
            
            if ($pageId != 0) {
	            $page = get_post($pageId);	            
    	        $accountIdToken = '%eHiveAccountId%';
        	    $wp_rewrite->add_rewrite_tag($accountIdToken, '([0-9]+)', "pagename={$page->post_name}&ehive_account_id=");
            	$rules = $wp_rewrite->generate_rewrite_rules($wp_rewrite->root . "/{$page->post_name}/$accountIdToken") + $rules;
            }
            return $rules;
        }
        
		function ehive_plugin_update() {     
				 
			// Add the default options.
			if ( get_option(self::EHIVE_ACCOUNT_DETAILS_OPTIONS) === false ) {

        		$arr = array("update_version"=>self::CURRENT_VERSION,
							 "plugin_css_enabled"=>"on",
							 "css_class"=>"",
							 "gallery_background_colour"=>"#f3f3f3",
							 "gallery_background_colour_enabled"=>'on',
							 "gallery_border_colour"=>"#666666",
							 "gallery_border_colour_enabled"=>'',
							 "gallery_border_width"=>"2",
							 "image_background_colour"=>"#ffffff",
							 "image_background_colour_enabled"=>'on',
							 "image_padding"=>"1",
							 "image_padding_enabled"=>"on",
							 "image_border_colour"=>"#666666",
							 "image_border_colour_enabled"=>'on',
							 "image_border_width"=>"2"
							);        
        		update_option(self::EHIVE_ACCOUNT_DETAILS_OPTIONS, $arr);  

        	} else {

				$options = get_option(self::EHIVE_ACCOUNT_DETAILS_OPTIONS);

				if ( array_key_exists("update_version", $options)) {
					$updateVersion = $options["update_version"];
				} else {
					$updateVersion = 0;
				}

				if ( $updateVersion == self::CURRENT_VERSION ) {
					// Nothing to do.
				}  else {

					if ( $updateVersion == 0 ) {
						$updateVersion = 1;
					}

					// End of the update chain, save the options to the database.
					$options["update_version"] = self::CURRENT_VERSION;
					update_option(self::EHIVE_ACCOUNT_DETAILS_OPTIONS, $options);
				}
			}
        }
                
        public function activate() {
        }
        
        public function deactivate() {
        }        
    }
    
    $eHiveAccountDetails = new EHiveAccountDetails();

    add_filter('query_vars', array(&$eHiveAccountDetails, 'query_vars'));
    add_filter('rewrite_rules_array', array(&$eHiveAccountDetails, 'add_rewrite_rules'));
    
    add_action('activate_ehive-account-details/EHiveAccountDetails.php', array(&$eHiveAccountDetails, 'activate'));
    add_action('deactivate_ehive-account-details/EHiveAccountDetails.php', array(&$eHiveAccountDetails, 'deactivate'));    
}
?>