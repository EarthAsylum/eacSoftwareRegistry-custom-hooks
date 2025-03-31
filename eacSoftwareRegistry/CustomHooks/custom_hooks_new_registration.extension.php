<?php
namespace EarthAsylumConsulting\Extensions;

/**
 * EarthAsylum Consulting {eac} Software Registration Server
 *
 * Extension to allow custom code for eacSoftwareRegistry filters
 *
 * @category	WordPress Plugin
 * @package		{eac}SoftwareRegistry\Custom Hooks
 * @author		Kevin Burkholder <KBurkholder@EarthAsylum.com>
 * @copyright	Copyright (c) 2025 EarthAsylum Consulting <www.earthasylum.com>
 * @version		2.x
 * @link		https://swregistry.earthasylum.com/
 */

/*
 * The only code changes needed are in the appropriate handler method(s), within the try...catch block.
 */

class custom_hooks_new_registration extends \EarthAsylumConsulting\abstract_extension
{
	/**
	 * @var string extension version
	 */
	const VERSION		= '25.0331.1';

	/**
	 * @var string to set default tab name
	 */
	const TAB_NAME		= 'Hooks';


	/**
	 * constructor method
	 *
	 * @param object $plugin main plugin (eacSoftwareRegistry) object
	 * @return void
	 */
	public function __construct($plugin)
	{
		parent::__construct($plugin, self::ALLOW_ALL|self::DEFAULT_DISABLED);

		if ($this->is_admin())
		{
			$this->registerExtension( 'new_registration_hooks' );
			// Register plugin options when needed
			$this->add_action( "options_settings_page", array($this, 'admin_options_settings') );
		}
	}


	/**
	 * register options on options_settings_page
	 *
	 */
	public function admin_options_settings()
	{
		/*
		 * Register this extension with [group name, tab name] and settings array
		 * Options here allow enabling/disabling each filter independently
		 */
		$this->registerExtensionOptions( 'new_registration_hooks',
			[
				'tag_new_registry_key' 			=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('new_registry_key'),
													'options'	=>	['Enabled'],
													'label'		=> "New Registration Key",
													'info'		=> "Whenever a new key is created (by admin or API request), filter the new key value.",
												),

			]
		);
	}


	/**
	 * Add filters and actions - called from main plugin
	 *
	 * @return	void
	 */
	public function addActionsAndFilters()
	{
		if ($this->is_option('tag_new_registry_key'))
			$this->add_filter('new_registry_key',			array($this, 'new_registry_key'), 20, 1);
	}


	/**
	 * new_registry_key handler
	 *
	 * @param string $newRegistrationKey The key value (uuid) assigned to a new registration
	 * @return string
	 */
	public function new_registry_key(string $newRegistrationKey): string
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			return $newRegistrationKey;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $newRegistrationKey;}
	}
}

/**
 * return a new instance of this class
 */
return new custom_hooks_new_registration($this);
?>
