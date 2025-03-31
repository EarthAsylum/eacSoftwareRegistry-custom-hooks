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

class custom_hooks_admin_options extends \EarthAsylumConsulting\abstract_extension
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
			$this->registerExtension( 'administrator_hooks' );
			// Register plugin options when needed
			$this->add_action( "options_settings_page", array($this, 'admin_options_settings') );

			// we need these early (on the settings page)
			if ($this->plugin->isSettingsPage())
			{
				if ($this->is_option('tag_settings_timezones'))
					$this->add_filter('settings_timezones',			array($this, 'settings_timezones'), 20, 1);

				if ($this->is_option('tag_settings_status_codes'))
					$this->add_filter('settings_status_codes',		array($this, 'settings_status_codes'), 20, 1);

				if ($this->is_option('tag_settings_post_status'))
					$this->add_filter('settings_post_status',		array($this, 'settings_post_status'), 20, 1);

				if ($this->is_option('tag_settings_initial_terms'))
					$this->add_filter('settings_initial_terms',		array($this, 'settings_initial_terms'), 20, 1);

				if ($this->is_option('tag_settings_full_terms'))
					$this->add_filter('settings_full_terms',		array($this, 'settings_full_terms'), 20, 1);

				if ($this->is_option('tag_settings_refresh_intervals'))
					$this->add_filter('settings_refresh_intervals',	array($this, 'settings_refresh_intervals'), 20, 1);

				if ($this->is_option('tag_settings_license_levels'))
					$this->add_filter('settings_license_levels',	array($this, 'settings_license_levels'), 20, 1);
			}
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
		$this->registerExtensionOptions( 'administrator_hooks',
			[
				'tag_settings_timezones' 		=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('settings_timezones'),
													'options'	=>	['Enabled'],
													'label'		=> 	"Timezone",
													'info'		=> 	'The available timezones used for registration times.<br/>'.
																	"<small>[ ".implode(",",array_unique(['UTC',wp_timezone_string()]))." ]</small>",
												),
				'tag_settings_status_codes' 	=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('settings_status_codes'),
													'options'	=>	['Enabled'],
													'label'		=> 	"Status Codes",
													'info'		=> 	"Registry status codes (code=description)<br/>".
																	"<small>[ ".$this->plugin->implode_with_keys(',',array_flip($this->plugin->REGISTRY_STATUS_CODES))." ]</small>"
												),
				'tag_settings_post_status' 		=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('settings_post_status'),
													'options'	=>	['Enabled'],
													'label'		=> 	"Post Status",
													'info'		=> 	"Post status codes (registry status=post status)<br/>".
																	"<small>[ ".$this->plugin->implode_with_keys(',',$this->plugin->POST_STATUS_CODES)." ]</small>"
												),
				'tag_settings_initial_terms' 	=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('settings_initial_terms'),
													'options'	=>	['Enabled'],
													'label'		=> 	"Initial Terms",
													'info'		=> 	"The initial terms used when creating a new registration (pending or trial).<br/>".
																	"<small>[ ".implode(",",$this->plugin->REGISTRY_INITIAL_TERMS)." ]</small>",
												),
				'tag_settings_full_terms' 		=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('settings_full_terms'),
													'options'	=>	['Enabled'],
													'label'		=> 	"Full Terms",
													'info'		=> 	"The full terms used when activating a registration.<br/>".
																	"<small>[ ".implode(",",$this->plugin->REGISTRY_FULL_TERMS)." ]</small>",
												),
				'tag_settings_refresh_intervals'=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('settings_refresh_intervals'),
													'options'	=>	['Enabled'],
													'label'		=> 	"Refresh Intervals",
													'info'		=> 	"Intervals used to set the client cache refresh event.<br/>".
																	"<small>[ ".$this->plugin->implode_with_keys(",",$this->plugin->REGISTRY_REFRESH_INTERVALS)." ]</small>",
												),
				'tag_settings_license_levels' 	=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('settings_license_levels'),
													'options'	=>	['Enabled'],
													'label'		=> 	"License Levels",
													'info'		=> 	"Registry license levels<br/>".
																	"<small>[ ".$this->plugin->implode_with_keys(',',array_flip($this->plugin->REGISTRY_LICENSE_LEVEL))." ]</small>"
												),
				'tag_admin_email_headers' 		=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('admin_email_headers'),
													'options'	=>	['Enabled'],
													'label'		=> 	"Notification Email Headers",
													'info'		=> 	"The email headers used for administrator email notifications.",
												),
				'tag_admin_email_style' 		=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('admin_email_style'),
													'options'	=>	['Enabled'],
													'label'		=> 	"Notification Email Style",
													'info'		=> 	"The email CSS styles used for administrator email notifications.",
												),
				'tag_admin_email_message' 		=> array(
													'type'		=>	'checkbox',
													'title'		=> 	$this->plugin->prefixHookName('admin_email_message'),
													'options'	=>	['Enabled'],
													'label'		=> 	"Notification Email Message",
													'info'		=> 	"The email message used for administrator email notifications.",
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
		if ($this->is_option('tag_admin_email_headers'))
			$this->add_filter('admin_email_headers',		array($this, 'admin_email_headers'), 20, 3);

		if ($this->is_option('tag_admin_email_style'))
			$this->add_filter('admin_email_style',			array($this, 'admin_email_style'), 20, 3);

		if ($this->is_option('tag_admin_email_message'))
			$this->add_filter('admin_email_message',		array($this, 'admin_email_message'), 20, 3);
	}


	/**
	 * settings_timezones handler
	 *
	 * @param array $timezones Array of timezones [ UTC,America/New_York ]
	 * @return array
	 */
	public function settings_timezones(array $timezones): array
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			//$timezones[] = 'America/Denver'; // add Denver
			//asort($timezones);
			return $timezones;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $timezones;}
	}


	/**
	 * settings_status_codes handler
	 *
	 * @param array $status_codes Array of status codes [ pending=Pending,trial=Trial,active=Active,inactive=Inactive,expired=Expired,terminated=Terminated ]
	 * @return array
	 */
	public function settings_status_codes(array $status_codes): array
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			//$status_codes['new'] = 'New Registration'; // add 'New' status
			return $status_codes;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $status_codes;}
	}


	/**
	 * settings_post_status handler
	 *
	 * @param array $status_codes Array of post status codes [ future=future,pending=draft,trial=publish,active=publish,inactive=private,expired=private,terminated=trash ]
	 * @return array
	 */
	public function settings_post_status(array $status_codes): array
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			//$status_codes['new'] = 'draft'; // 'New' status is saved as draft
			return $status_codes;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $status_codes;}
	}


	/**
	 * settings_initial_terms handler
	 *
	 * @param array $terms Array of terms [ 7 days,14 days,30 days,60 days,90 days,6 months,1 year ]
	 * @return array
	 */
	public function settings_initial_terms(array $terms): array
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			//$terms = ['7 days','14 days','21 days']; // change initial terms
			return $terms;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $terms;}
	}


	/**
	 * settings_full_terms handler
	 *
	 * @param array $terms Array of terms [ 30 days,60 days,90 days,6 months,1 year,3 years,5 years,10 years,100 years ]
	 * @return array
	 */
	public function settings_full_terms(array $terms): array
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			//$terms = ['1 year','5 years','10 years']; // change full terms
			return $terms;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $terms;}
	}


	/**
	 * settings_refresh_intervals handler
	 *
	 * @param array $intervals Array of intervals [ Hourly=>3600, Daily=>DAY_IN_SECONDS, Weekly=>WEEK_IN_SECONDS,... ]
	 * @return array
	 */
	public function settings_refresh_intervals(array $intervals): array
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			//$intervals['Quarterly'] = MONTH_IN_SECONDS * 3; // (or YEAR_IN_SECONDS / 4)
			return $intervals;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $intervals;}
	}


	/**
	 * settings_license_levels handler
	 *
	 * @param array $license_levels Array of levels [ L1=Lite,L2=Basic,L3=Standard,L4=Professional,L5=Enterprise,LD=Developer ]
	 * @return array
	 */
	public function settings_license_levels(array $license_levels): array
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			//$license_levels['L6'] = 'Unlimited'; // add level 6 unlimited
			return $license_levels;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $license_levels;}
	}


	/**
	 * admin_email_headers handler
	 *
	 * @param array $headers Array of email headers ['from'=>,'to'=>,'subject'=>]
	 * @param array $registration The registration data array with registry values
	 * @param object $wpPost WP_Post object
	 * @return array
	 */
	public function admin_email_headers(array $headers, array $registration, object $wpPost): array
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			//$headers = ['from'=>,'to'=>,'subject'=>]
			return $headers;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $headers;}
	}


	/**
	 * admin_email_style handler
	 *
	 * @param string $style Default CSS for admin email (includes Appearance->Customize->Additional CSS)
	 * @param array $registration The registration data array with registry values
	 * @param object $wpPost WP_Post object
	 * @return string
	 */
	public function admin_email_style(string $style, array $registration, object $wpPost): string
	{
		global $wp, $wpdb;

		try {
			/* custom code here */
			return $style;
		} catch (\Throwable $e) {$this->plugin->logError($e);return $style;}
	}


	/**
	 * admin_email_message handler
	 *
	 * @param string $message Default html for admin email
	 * @param array $registration The registration data array with registry values
	 * @param object $wpPost WP_Post object
	 * @return string
	 */
	public function admin_email_message(string $message, array $registration, object $wpPost): string
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
return new custom_hooks_admin_options($this);
?>
