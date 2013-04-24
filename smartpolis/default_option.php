<?php
/**
 * Модуль Умный Полис
 *
 * @file default_option.php
 */

IncludeModuleLangFile(__FILE__);

$smartpolis_default_option = array(
    'smartpolis_auth_type'             => serialize('by_ip'),
    'smartpolis_auth_token'            => serialize(''),
    'smartpolis_show_type'             => serialize('show_after_form'),
    'smartpolis_message_before_form'   => serialize(GetMessage('SPM_MESSAGE_BEFORE_FORM')),
    'smartpolis_message_before_button' => serialize(GetMessage('SPM_MESSAGE_BEFORE_BUTTON')),
    'smartpolis_header_before_form'    => serialize(GetMessage('SPM_HEADER_BEFORE_FORM'))
);

?>