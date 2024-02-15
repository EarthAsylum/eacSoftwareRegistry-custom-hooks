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

class custom_hooks_api_request extends \EarthAsylumConsulting\abstract_extension
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
		$this->registerExtension( ['api_request_hooks' , 'Hooks' ],
			[
				'tag_api_request_parameters' 	=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('api_request_parameters'),
													'options'	=>	['Enabled'],
													'default'	=> 'Enabled',
													'label'		=> "API Request Parameters",
													'info'		=> "For all API Requests, filter input registry parameter array.",
												),
				'tag_api_create_registration' 	=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('api_create_registration'),
													'options'	=>	['Enabled'],
													'label'		=> "API Create Parameters",
													'info'		=> "When creating a new registration, filter the WP_Post array used to create the registration.",
												),
				'tag_api_activate_registration' => array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('api_activate_registration'),
													'options'	=>	['Enabled'],
													'label'		=> "API Activate Parameters",
													'info'		=> "When activating a registration, filter the registry parameter array.",
												),
				'tag_api_revise_registration' => array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('api_revise_registration'),
													'options'	=>	['Enabled'],
													'label'		=> "API Revise Parameters",
													'info'		=> "When revising a registration, filter the registry parameter array.",
												),
				'tag_api_deactivate_registration' 	=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('api_deactivate_registration'),
													'options'	=>	['Enabled'],
													'label'		=> "API Deactivate Parameters",
													'info'		=> "When deactivating a registration, filter the registry parameter array.",
												),
				'tag_api_verify_registration' 	=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('api_verify_registration'),
													'options'	=>	['Enabled'],
													'label'		=> "API Verification Parameters",
													'info'		=> "When verifying a registration, filter the registry parameter array.",
												),
				'tag_api_refresh_registration' => array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('api_refresh_registration'),
													'options'	=>	['Enabled'],
													'label'		=> "API Refresh Parameters",
													'info'		=> "When refreshing a registration, filter the registry parameter array.",
												),
				'tag_api_license_limitations' 	=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('api_license_limitations'),
													'options'	=>	['Enabled'],
													'label'		=> "API License Limitations",
													'info'		=> "Apply/override licensing limitations for variations, options, domains, and/or sites.",
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
		if ($this->is_option('tag_api_request_parameters'))
			$this->add_filter('api_request_parameters',		array($this, 'api_request_parameters'), 20, 2);

		if ($this->is_option('tag_api_create_registration'))
			$this->add_filter('api_create_registration',	array($this, 'api_create_registration'), 20, 2);

		if ($this->is_option('tag_api_activate_registration'))
			$this->add_filter('api_activate_registration',	array($this, 'api_activate_registration'), 20, 2);

		if ($this->is_option('tag_api_revise_registration'))
			$this->add_filter('api_revise_registration',	array($this, 'api_revise_registration'), 20, 2);

		if ($this->is_option('tag_api_deactivate_registration'))
			$this->add_filter('api_deactivate_registration',array($this, 'api_deactivate_registration'), 20, 2);

		if ($this->is_option('tag_api_verify_registration'))
			$this->add_filter('api_verify_registration',	array($this, 'api_verify_registration'), 20, 2);

		if ($this->is_option('tag_api_refresh_registration'))
			$this->add_filter('api_refresh_registration',	array($this, 'api_refresh_registration'), 20, 2);

		if ($this->is_option('tag_api_license_limitations'))
			$this->add_filter('api_license_limitations',	array($this, 'api_license_limitations'), 20, 3);
	}


	/**
	 * api_request_parameters handler
	 *
	 * @param array	$requestParams The parameter array passed through the API.
	 * @param string $apiAction One of 'create', 'activate', 'revise', 'deactivate', 'verify' or 'update' (non-api)
	 * @return array | WP_Error
	 */
	public function api_request_parameters(array $requestParams, string $apiAction)
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			$this->plugin->logData($requestParams,'api_request_parameters');
			return $requestParams;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $requestParams;}
	}


	/**
	 * api_create_registration handler
	 *
	 * @param array	$postValues	Array of values passed to wp_insert_post(), including 'meta_input' array with registry values
	 * @param string $apiAction One of 'create', 'activate', 'revise', 'deactivate', 'verify' or 'update' (non-api)
	 * @return array | WP_Error
	 */
	public function api_create_registration(array $postValues, array $requestParams)
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			return $postValues;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $postValues;}
	}


	/**
	 * api_activate_registration handler
	 *
	 * @param array	$requestParams The parameter array passed through the API.
	 * @param object $wpPost WP_Post object
	 * @return array | WP_Error
	 */
	public function api_activate_registration(array $requestParams, object $wpPost)
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			return $requestParams;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $requestParams;}
	}


	/**
	 * api_revise_registration handler
	 *
	 * @param array	$requestParams The parameter array passed through the API.
	 * @param object $wpPost WP_Post object
	 * @return array | WP_Error
	 */
	public function api_revise_registration(array $requestParams, object $wpPost)
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			return $requestParams;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $requestParams;}
	}


	/**
	 * api_deactivate_registration handler
	 *
	 * @param array	$requestParams The parameter array passed through the API.
	 * @param object $wpPost WP_Post object
	 * @return array | WP_Error
	 */
	public function api_deactivate_registration(array $requestParams, object $wpPost)
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			return $requestParams;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $requestParams;}
	}


	/**
	 * api_verify_registration handler
	 *
	 * @param array	$requestParams The parameter array passed through the API.
	 * @param object $wpPost WP_Post object
	 * @return array | WP_Error
	 */
	public function api_verify_registration(array $requestParams, object $wpPost)
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			return $requestParams;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $requestParams;}
	}


	/**
	 * api_refresh_registration handler
	 *
	 * @param array	$requestParams The parameter array passed through the API.
	 * @param object $wpPost WP_Post object
	 * @return array | WP_Error
	 */
	public function api_refresh_registration(array $requestParams, object $wpPost)
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			return $requestParams;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $requestParams;}
	}


	/**
	 * api_license_limitations handler
	 *
	 * @param array	$limitations The limitations array ['count'=> n, 'variations'=>n, 'options'=>n, 'domains'=>n, 'sites'=>n]
	 * @param string $license The current license level (Ln)
	 * @param array	$requestParams The parameter array passed through the API.
	 * @return array
	 */
	public function api_license_limitations(array $limitations, string $license, array $requestParams): array
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			return $limitations;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $limitations;}
	}
}

/**
 * return a new instance of this class
 */
return new custom_hooks_api_request($this);
?>
