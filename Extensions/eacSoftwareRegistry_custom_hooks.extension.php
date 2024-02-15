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
 */

/*
 * To customize, copy the 'eacSoftwareRegistry/CustomHooks' folder to your theme folder.
 *
 *	/wp-content/themes/my-awesome-theme
 *		/eacSoftwareRegistry
 *			/CustomHooks
 *				custom_hooks_*.extension.php
 *
 * The only code changes needed are in the appropriate method(s), within the try...catch block.
 *
 * API Function Arguments
 *
 *		$newRegistrationKey		The key value (uuid) assigned to a new registration
 *		$requestParams			The parameter array passed through the API. May include:
 *		- registry_product		Registered product
 *		- registry_title		Registered product title
 *		- registry_description	Registered product description
 *		- registry_version		Registered product version
 *		- registry_license		'developer', 'basic', 'pro', 'unlimited'
 *		- registry_count		Number of licenses (users/seats/devices) included
 *		- registry_status		'pending', 'trial', 'active', 'inactive', 'expired', 'terminated'
 *		- registry_effective	Effective date (Y-m-d)
 *		- registry_expires		Expiration date (Y-m-d) or term (30 days, 1 year,...)
 *		- registry_name			Full name
 *		- registry_email		Valid email address
 *		- registry_company		Company name
 *		- registry_address		Full postal address (textarea)
 *		- registry_variations	Registrar variations (key=>value associative array)
 *		- registry_options		Registrar options (indexed array)
 *		- registry_domains		Registered domain name(s) (indexed array)
 *		- registry_sites		Registered site URL(s) (indexed array)
 *		$apiAction				One of 'create', 'activate', 'revise', 'deactivate', 'verify' or 'update' (non-api)
 *		$registration			The registration data array with above registry values
 *		$wpPost					WP_Post object
 *		$postValues				Array of values passed to wp_insert_post(), including 'meta_input' array with above registry values
 */

class eacSoftwareRegistry_custom_hooks extends \EarthAsylumConsulting\abstract_extension
{
	/**
	 * @var string extension version
	 */
	const VERSION	= '22.1115.1';


	/**
	 * constructor method
	 *
	 * @param	object	$plugin main plugin object
	 * @return	void
	 */
	public function __construct($plugin)
	{
		$this->enable_option = false;
		parent::__construct($plugin, self::ALLOW_ADMIN|self::ONLY_ADMIN);

		if ($this->is_admin())
		{
			$this->registerExtension( ['registry_hooks' , 'Hooks'] );
			// Register plugin options when needed
			$this->add_action( "options_settings_page", array($this, 'admin_options_settings') );
			// Add contextual help
			$this->add_action( 'options_settings_help', array($this, 'admin_options_help') );
		}
	}

	/**
	 * register options on options_settings_page
	 *
	 * @access public
	 * @return void
	 */
	public function admin_options_settings(): void
	{
		/* Register this extension with [group name, tab name] and settings array */
		$this->registerExtension( 'registry_hooks',
			[
				'_instructions'		=> array(
										'type'		=> 'display',
										'label'		=>	'<span class="dashicons dashicons-info-outline"></span>',
										'default'	=> 	"Registry hooks allow you to write PHP code for each of the available filters. ".
														"Through these hooks, you can customize your registration server, validate and/or modify API request/response data, ".
														"or trigger other actions or functions.</p>",
									),
			]
		);
	}


	/**
	 * Add help tab on admin page
	 *
	 * @return	void
	 */
	public function admin_options_help()
	{
		if (!$this->plugin->isSettingsPage('Hooks')) return;

		ob_start();
		?>
            Using the many hooks available in the Software Registration Server,
            you can customize the registration server options,
            incoming API requests, outgoing API responses, and client emails and notifications.

            Although this extension includes some pre-built customizations,
            the main purpose is to allow you (or your programmer) to further customize the Software Registration Server.

            <blockquote>
                This custom hooks extension is a developer-level extension which uses <em>your</em> PHP code.
                To add customizations beyond what is provided requires PHP programming knowledge and
                basic understanding of WordPress actions and filters.
            </blockquote>

			<details><summary>To implement your customizations</summary>
				<ol>
					<li>Copy the /eacSoftwareRegistry/Customhooks folder from this plugin folder to your development environment.
					<li>Modify the appropriate extension(s) [custom_hooks_*.extension.php] found in the Customhooks folder.
					<li>Upload the /eacSoftwareRegistry/Customhooks folder from your development environment to your WordPress theme folder (preferably a child theme).
				</ol>
			</details>

            <blockquote><em>
				In some cases, default code is provided as both example and preference.
				If not needed, defaults should be removed or disabled.
            </em></blockquote>
		<?php
		$content = ob_get_clean();

		$this->addPluginHelpTab('Registry Hooks',$content,['{eac}SoftwareRegistry Custom Hooks','open']);

		$this->addPluginSidebarLink(
			"<span class='dashicons dashicons-image-filter'></span>Custom Hooks",
			$this->plugin->getDocumentationURL(true,'/software-registry-hooks'),
			"{eac}SoftwareRegistry Custom Hooks"
		);

		// filter to override field-level help (tab.label,content)
		$this->add_filter("plugin_help_field", function($help)
			{
				$help['tab'] 	= 'Registry Hooks';
				$help['content']= preg_replace("/\[(.*)\]/",'<code>$1</code>',$help['content']);
				return $help;
			}
		);
	}
}

/**
 * return a new instance of this class
 */
return new eacSoftwareRegistry_custom_hooks($this);
?>
