<?php
/**
 * EarthAsylum Consulting {eac} Software Registration Server - Custom Hooks
 *
 * @category	WordPress Plugin
 * @package		{eac}SoftwareRegistry Custom Hooks
 * @author		Kevin Burkholder <KBurkholder@EarthAsylum.com>
 * @copyright	Copyright (c) 2023 EarthAsylum Consulting <www.earthasylum.com>
 * @version		2.x
 * @uses		{eac}SoftwareRegistry
 *
 * @wordpress-plugin
 * Plugin Name:			{eac}SoftwareRegistry Custom Hooks
 * Description:			Software Registration Server Custom Hooks - allows coding hooks and customization of the Software Registration Server
 * Version:				2.0.10
 * Requires at least:	5.5.0
 * Tested up to:		6.4
 * Requires PHP:		7.2
 * Plugin URI:			https://swregistry.earthasylum.com/software-registry-hooks/
 * Update URI: 			https://swregistry.earthasylum.com/software-updates/eacsoftwareregistry-custom-hooks.json
 * Author:				EarthAsylum Consulting
 * Author URI:			http://www.earthasylum.com
 * License:				GPLv3 or later
 * License URI:			https://www.gnu.org/licenses/gpl.html
 * Text Domain:			eacSoftwareRegistry
 * Domain Path:			/languages
 */

/**
 * This simple plugin file responds to the 'eacSoftwareRegistry_load_extensions' filter to load additional extensions.
 * Using this method prevents overwriting extensions when the plugin is updated or reinstalled.
 */

namespace EarthAsylumConsulting;

class eacSoftwareRegistry_Custom_Hooks
{
	/**
	 * constructor method
	 *
	 * @return	void
	 */
	public function __construct()
	{
		/*
		 * eacSoftwareRegistry_load_extensions - get the extensions directory to load
		 *
		 * @param	array	$extensionDirectories - array of [plugin_slug => plugin_directory]
		 * @return	array	updated $extensionDirectories
		 */

		add_filter( 'eacSoftwareRegistry_load_extensions',	function($extensionDirectories)
			{
				/*
    			 * Enable update notice (self hosted or wp hosted)
    			 */
				eacSoftwareRegistry::loadPluginUpdater(__FILE__,'self');

				/*
    			 * on plugin_action_links_ filter, add 'Settings' link
    			 */
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ),function($pluginLinks, $pluginFile, $pluginData)
					{
						return array_merge(
							[
								'settings'		=> eacSoftwareRegistry::getSettingsLink($pluginData,'hooks'),
								'documentation'	=> eacSoftwareRegistry::getDocumentationLink($pluginData),
							],
							$pluginLinks
						);
					},20,3
				);

				/*
    			 * Add our extension to load (look in theme folder)
    			 */
				foreach ([\get_stylesheet_directory(),\get_template_directory(),\plugin_dir_path( __FILE__ )] as $customHooks)
				{
					$customHooks .= '/eacSoftwareRegistry/CustomHooks';
					if (is_dir($customHooks)) break;
				}

				$extensionDirectories[ plugin_basename( __FILE__ ) ] = [
					plugin_dir_path( __FILE__ ).'/Extensions',
					$customHooks
				];
				return $extensionDirectories;
			}
		);
	}
}
new \EarthAsylumConsulting\eacSoftwareRegistry_Custom_Hooks();
?>
