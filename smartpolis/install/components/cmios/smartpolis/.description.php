<?php
/**
 * Компонент Умный Полис
 *
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

IncludeModuleLangFile(__FILE__);

$arComponentDescription = array(
    'NAME'        => GetMessage('SPM_COMPONENT_NAME'),
    'DESCRIPTION' => GetMessage('SPM_COMPONENT_DESCRIPTION'),
    'ICON'        => '/images/icon.png',
    'SORT'        => 100,
    'CACHE_PATH'  => 'Y',
    'PATH'        => array(
        'ID' => 'cmios'
    ),
    'COMPLEX' => 'N'
);

?>