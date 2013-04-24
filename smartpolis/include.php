<?php
/**
 * Модуль Умный Полис
 *
 * @file include.php
 */

global $DBType;

CModule::AddAutoloadClasses('smartpolis', array(
    'SmartpolisConnectionPool'  => 'classes/general/connection_pool.class.php',
    'SmartpolisCascoApi'        => 'classes/general/casco_api.class.php',
    'GeneralSmartpolisSettings' => 'classes/general/settings.class.php',
    'SmartpolisSettings'        => "classes/{$DBType}/settings.class.php"
));

?>