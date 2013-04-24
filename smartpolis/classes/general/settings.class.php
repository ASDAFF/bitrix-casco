<?php
/**
 * Модуль Умный Полис
 *
 * @file classes/general/settings.class.php
 */

class GeneralSmartpolisSettings {
    protected $_props = array(
        'smartpolis_auth_type',
        'smartpolis_auth_token',
        'smartpolis_show_type',
        'smartpolis_message_before_button',
        'smartpolis_header_before_form',
        'smartpolis_message_before_form'
    );

    public function get($key) {
        return unserialize(COption::GetOptionString('smartpolis', $key));
    }

    public function set($key, $value = null) {
        if (is_array($key)) {
            foreach ($key as $k => $v)
                $this->set($k, $v);
        } elseif (in_array($key, $this->_props)) {
            COption::SetOptionString('smartpolis', $key, serialize($value));
        }
    }

    public function updateCompanies($provider) {
    }
}

?>