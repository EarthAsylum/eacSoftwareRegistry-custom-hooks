<?php
namespace EarthAsylumConsulting\Extensions;

/**
 * EarthAsylum Consulting {eac} Software Registration Server
 *
 * Extension to allow custom code for eacSoftwareRegistry filters
 *
 * @category	WordPress Plugin
 * @package		{eac}SoftwareRegistry
 * @author		Kevin Burkholder <KBurkholder@EarthAsylum.com>
 * @copyright	Copyright (c) 2023 EarthAsylum Consulting <www.earthasylum.com>
 * @version		2.x
 * @see 		eacSoftwareRegistry_custom_hooks.extension.php
 */

/*
 * The only code changes needed are in the appropriate handler method(s), within the try...catch block.
 */

class custom_hooks_api_response extends \EarthAsylumConsulting\abstract_extension
{
	/**
	 * @var string extension version
	 */
	const VERSION	= '23.0501.1';


	/**
	 * constructor method
	 *
	 * @param object $plugin main plugin (eacSoftwareRegistry) object
	 * @return void
	 */
	public function __construct($plugin)
	{
		parent::__construct($plugin, self::ALLOW_ALL|self::DEFAULT_DISABLED);

		/*
		 * Register this extension with [group name, tab name] and settings array
		 * Options here allow enabling/disabling each filter independently
		 */
		$this->registerExtension( ['api_response_hooks' , 'Hooks' ],
			[
				'tag_api_registration_values' 	=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('api_registration_values'),
													'options'	=>	['Enabled'],
													'default'	=> 'Enabled',
													'label'		=> "API Registration Values",
													'info'		=> "For all API Requests, filter the registration array returned to the client API.",
												),
				'tag_validate_registration' 	=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('validate_registration'),
													'options'	=>	['Enabled'],
													'label'		=> "Validate Registration Values",
													'info'		=> "Whenever a registration is created, activated, or updated (by admin or API request), filter the registration values."
												),
				'tag_is_valid_registration' 	=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('is_valid_registration'),
													'options'	=>	['Enabled'],
													'label'		=> "Is Valid Registration",
													'info'		=> "To set the validity of a registration when returned to the client API.",
												),
				'tag_update_registration' 		=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('update_registration_post'),
													'options'	=>	['Enabled'],
													'label'		=> "Update Registration Post",
													'info'		=> "Whenever a registration is updated (by admin or API request), filter the updated WP_Post values.",
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
		if ($this->is_option('tag_api_registration_values'))
			$this->add_filter('api_registration_values',	array($this, 'api_registration_values'), 20, 3);

		if ($this->is_option('tag_validate_registration'))
			$this->add_filter('validate_registration',		array($this, 'validate_registration'), 20, 3);

		if ($this->is_option('tag_is_valid_registration'))
			$this->add_filter('is_valid_registration',		array($this, 'is_valid_registration'), 20, 3);

		if ($this->is_option('tag_update_registration'))
			$this->add_filter('update_registration_post',	array($this, 'update_registration_post'), 20, 3);
	}


	/**
	 * api_registration_values handler
	 *
	 * @param array $registration The registration data array with registry values
	 * @param object $wpPost WP_Post object
	 * @param string $apiAction One of 'create', 'activate', 'revise', 'deactivate', 'verify' or 'update' (non-api)
	 * @return array | WP_Error
	 */
	public function api_registration_values(array $registration, object $wpPost, string $apiAction)
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			$this->plugin->logData($registration,'api_registration_values');
			return $registration;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $registration;}
	}


	/**
	 * validate_registration handler
	 *
	 * @param array $registration The registration data array with registry values
	 * @param object $wpPost WP_Post object
	 * @param string $apiAction One of 'create', 'activate', 'revise', 'deactivate', 'verify' or 'update' (non-api)
	 * @return array | WP_Error
	 */
	public function validate_registration(array $registration, object $wpPost=null, string $apiAction)
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			return $registration;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $registration;}
	}


	/**
	 * is_valid_registration handler
	 *
	 * @param bool $valid
	 * @param array $registration The registration data array with registry values
	 * @param object $wpPost WP_Post object
	 * @return bool
	 */
	public function is_valid_registration(bool $valid, array $registration, object $wpPost): bool
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			return $valid;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $valid;}
	}


	/**
	 * update_registration_post handler
	 *
	 * @param array	$postValues	Array of values passed to wp_insert_post(), including 'meta_input' array with registry values
	 * @param array	$requestParams The parameter array passed through the API.
	 * @param string $apiAction One of 'create', 'activate', 'revise', 'deactivate', 'verify' or 'update' (non-api)
	 * @return array | WP_Error
	 */
	public function update_registration_post(array $postValues, array $requestParams, string $apiAction)
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			return $postValues;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $postValues;}
	}
}

/**
 * return a new instance of this class
 */
return new custom_hooks_api_response($this);
?>
