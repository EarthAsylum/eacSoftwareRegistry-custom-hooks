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

class custom_hooks_client_messages extends \EarthAsylumConsulting\abstract_extension
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
			$this->registerExtension( 'client_message_hooks' );
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
		$this->registerExtensionOptions( 'client_message_hooks',
			[
				'tag_registration_notices' 		=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('registration_notices'),
													'options'	=>	['Enabled'],
													'label'		=> "Notice(s) returned with registration",
													'info'		=> "To set the notice(s) returned to the client with the registration values."
												),
				'tag_registration_message' 		=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('registration_message'),
													'options'	=>	['Enabled'],
													'label'		=> "Message returned with registration",
													'info'		=> "To set an html message returned to the client with the registration values."
												),
				'tag_registration_supplemental' => array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('registration_supplemental'),
													'options'	=>	['Enabled'],
													'label'		=> "Supplemental html/data returned with registration",
													'info'		=> "To set additional html or data returned to the client with the registration values."
												),
				'tag_client_registry_translate' => array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('client_registry_translate'),
													'options'	=>	['Enabled'],
													'default'	=> 'Enabled',
													'label'		=> "Registry Translation",
													'info'		=> "Translate registry keys (i.e. 'registry_key') to human-readable titles (i.e. 'Registration Key'). ".
																	"Used to display registration details to the client registrant."
												),
				'tag_client_registry_html' 		=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('client_registry_html'),
													'options'	=>	['Enabled'],
													'label'		=> "Registry Html table",
													'info'		=> "A simple html table of [{translated key} | {registry value}] sent to the client. ".
																	"Used to display registration details to the client registrant."
												),
				'tag_client_email_headers' 	=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('client_email_headers'),
													'options'	=>	['Enabled'],
													'label'		=> 	"Notification Email Headers",
													'info'		=> 	"The email headers used for client email notifications.",
												),
				'tag_client_email_style' 	=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('client_email_style'),
													'options'	=>	['Enabled'],
													'label'		=> 	"Notification Email Style",
													'info'		=> 	"The email CSS styles used for client email notifications.",
												),
				'tag_client_email_message' 	=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('client_email_message'),
													'options'	=>	['Enabled'],
													'label'		=> 	"Notification Email Message",
													'info'		=> 	"The email message used for client email notifications.",
												),
				'tag_client_email_footer' 	=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('client_email_footer'),
													'options'	=>	['Enabled'],
													'label'		=> 	"Notification Email Footer",
													'info'		=> 	"The email footer used for client email notifications.",
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
		if ($this->is_option('tag_registration_notices'))
			$this->add_filter('api_registration_notices',		array($this, 'registration_notices'), 20, 4);

		if ($this->is_option('tag_registration_message'))
			$this->add_filter('api_registration_message',		array($this, 'registration_message'), 20, 4);

		if ($this->is_option('tag_registration_supplemental'))
			$this->add_filter('api_registration_supplemental',	array($this, 'registration_supplemental'), 20, 4);

		if ($this->is_option('tag_client_registry_translate'))
			$this->add_filter('client_registry_translate',		array($this, 'client_registry_translate'), 20, 2);

		if ($this->is_option('tag_client_registry_html'))
			$this->add_filter('client_registry_html',			array($this, 'client_registry_html'), 20, 3);

		if ($this->is_option('tag_client_email_headers'))
			$this->add_filter('client_email_headers',			array($this, 'client_email_headers'), 20, 3);

		if ($this->is_option('tag_client_email_style'))
			$this->add_filter('client_email_style',				array($this, 'client_email_style'), 20, 3);

		if ($this->is_option('tag_client_email_message'))
			$this->add_filter('client_email_message',			array($this, 'client_email_message'), 20, 3);

		if ($this->is_option('tag_client_email_footer'))
			$this->add_filter('client_email_footer',			array($this, 'client_email_footer'), 20, 3);
	}


	/**
	 * registration_notices handler
	 *
	 * @param array $notices Notices array passed to client ['info'=>'...','warning'=>'...','error'=>'...','success'=>'...']
	 * @param array $registration The registration data array with registry values
	 * @param object $wpPost WP_Post object
	 * @param string $apiAction One of 'create', 'activate', 'revise', 'deactivate', 'verify' or 'update' (non-api)
	 * @return array
	 */
	public function registration_notices(array $notices, array $registration, object $wpPost, string $apiAction): array
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			return $notices;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $notices;}
	}


	/**
	 * registration_message handler
	 *
	 * @param string $message HTML message string passed to client
	 * @param array $registration The registration data array with registry values
	 * @param object $wpPost WP_Post object
	 * @param string $apiAction One of 'create', 'activate', 'revise', 'deactivate', 'verify' or 'update' (non-api)
	 * @return string
	 */
	public function registration_message(string $message, array $registration, object $wpPost, string $apiAction): string
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			return $message;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $message;}
	}


	/**
	 * registration_supplemental handler
	 *
	 * @param mixed $supplemental HTML string passed to client
	 * @param array $registration The registration data array with registry values
	 * @param object $wpPost WP_Post object
	 * @param string $apiAction One of 'create', 'activate', 'revise', 'deactivate', 'verify' or 'update' (non-api)
	 * @return mixed
	 */
	public function registration_supplemental($supplemental, array $registration, object $wpPost, string $apiAction)
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			return $supplemental;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $supplemental;}
	}


	/**
	 * client_registry_translate handler
	 *
	 * @param array $translate Translate registry keys (i.e. ['registry_key' => 'Registration Key'])
	 * @param array $registration The registration data array with registry values
	 * @return array
	 */
	public function client_registry_translate(array $translate, array $registration): array
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			// let's not display these values to the client...
			unset(
				$translate['registry_variations'],
				$translate['registry_options'],
				$translate['registry_domains'],
				$translate['registry_sites']
			//	$translate['registry_count']
			);
			return $translate;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $translate;}
	}


	/**
	 * client_registry_html handler
	 *
	 * @params string $html Registry html table sent to client
	 * @param array $translate Translate registry keys (i.e. ['registry_key' => 'Registration Key'])
	 * @param array $registration The registration data array with registry values
	 * @return string
	 */
	public function client_registry_html(string $html, array $translate, array $registration): string
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			return $html;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $html;}
	}


	/**
	 * client_email_headers handler
	 *
	 * @param array $headers Array of email headers ['from'=>,'to'=>,'subject'=>]
	 * @param array $registration The registration data array with registry values
	 * @param object $wpPost WP_Post object
	 * @return array
	 */
	public function client_email_headers(array $headers, array $registration, object $wpPost): array
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			return $headers;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $headers;}
	}


	/**
	 * client_email_style handler
	 *
	 * @param string $style Default CSS for client email (includes Appearance->Customize->Additional CSS)
	 * @param array $registration The registration data array with registry values
	 * @param object $wpPost WP_Post object
	 * @return string
	 */
	public function client_email_style(string $style, array $registration, object $wpPost): string
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			return $style;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $style;}
	}


	/**
	 * client_email_message handler
	 *
	 * @param string $message Default html for client email
	 * @param array $registration The registration data array with registry values
	 * @param object $wpPost WP_Post object
	 * @return string
	 */
	public function client_email_message(string $message, array $registration, object $wpPost): string
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			return $message;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $message;}
	}


	/**
	 * client_email_footer handler
	 *
	 * @param string $message Default html for client email
	 * @param array $registration The registration data array with registry values
	 * @param object $wpPost WP_Post object
	 * @return string
	 */
	public function client_email_footer(string $message, array $registration, object $wpPost): string
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			return $message;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $message;}
	}
}

/**
 * return a new instance of this class
 */
return new custom_hooks_client_messages($this);
?>
