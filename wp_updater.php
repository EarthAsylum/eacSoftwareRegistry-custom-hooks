<?php
/**
 * Plugin information file - eacsoftwareregistry-custom-hooks
 *
 * Returns JSON object for WordPress update
 *
 * @category	WordPress Plugin
 * @package		{eac}SoftwareRegistry
 * @author		Kevin Burkholder <KBurkholder@EarthAsylum.com>
 * @copyright	Copyright (c) 2022 EarthAsylum Consulting <www.earthasylum.com>
 * @version		1.x
 */

require '../class.wp_plugin_api.php';

die( wp_plugin_api::plugin_info
    (
        'eacsoftwareregistry-custom-hooks/eacSoftwareRegistry_Custom_Hooks.php',
        __DIR__.'/readme.txt'
    )
);
?>
