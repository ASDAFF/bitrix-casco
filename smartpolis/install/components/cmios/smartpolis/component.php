<?php
/**
 * Компонент Умный Полис
 *
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

IncludeModuleLangFile(__FILE__);

//if ($_REQUEST['ajax'] === 'Y')

$arParams['CACHE_TYPE'] = 0;

if ($this->StartResultCache($arParams['CACHE_TYPE'] === 'N' ? 0 : (is_numeric($arParams['CACHE_TIME']) ? $arParams['CACHE_TIME'] : '3600'))) {
    CModule::IncludeModule('smartpolis');

    $settings = new SmartpolisSettings();
    $api      = new SmartpolisCascoApi($settings);

    $arResult = array(
        'TEXT_BEFORE_BUTTON'  => $settings->get('smartpolis_message_before_button'),
        'HEADER_BEFORE_FORM'  => $settings->get('smartpolis_header_before_form'),
        'MESSAGE_BEFORE_FORM' => $settings->get('smartpolis_message_before_form'),
        'YEARS'               => array(),
        'DRIVERS'             => array(),
        'SHOW_TYPE'           => $settings->get('smartpolis_show_type'),
        'CAR_BRANDS'          => array()
    );

    if (!empty($_POST)) {
        $brands = json_decode($api->getCarMarks());
        $carBrand = 'Производитель не указан';
        foreach ($brands as $brand) {
            if ($brand->id == $_POST['CAR_BRAND']) {
                $carBrand = $brand->title;
                break;
            }
        }

        $api->setValue('car_mark', $_POST['CAR_BRAND']);
        $models = json_decode($api->getCarModels());
        $carModel = 'Модель не указана';
        foreach ($models as $model) {
            if ($model->id == $_POST['CAR_MODEL']) {
                $carModel = $model->title;
                break;
            }
        }

        $api->setValue('car_model', $_POST['CAR_MODEL']);
        $modifications = json_decode($api->getCarModifications());
        $carModification = 'Модификация не указана';
        foreach ($modifications as $modification) {
            if ($modification->id == $_POST['CAR_MODIFICATION']) {
                $carModification = $modification->title;
                break;
            }
        }

        $fields = array(
            'CAR_BRAND'        => $carBrand,
            'CAR_MODEL'        => $carModel,
            'CAR_MODIFICATION' => $carModification,
            'CAR_PRICE'        => doubleval($_POST['CAR_PRICE']),
            'CAR_YEAR'         => intval($_POST['CAR_YEAR']),
            'DRIVERS'          => array(),
            'CLIENT_NAME'      => trim($_POST['YOUR_NAME']),
            'CLIENT_EMAIL'     => trim($_POST['YOUR_EMAIL']),
            'CLIENT_PHONE'     => trim($_POST['CONTACT_PHONE']),
            'PREFERRED_DATE'   => trim($_POST['DATE']),
            'DELIVERY'         => trim($_POST['DELIVERY']) . trim($_POST['ADDRESS']),
            'COMMENTS'         => trim($_POST['COMMENTS'])
        );
        $yrs     = explode(',', $_POST['YEARS']);
        $exps    = explode(',', $_POST['EXPERIENCE']);
        $genders = explode(',', $_POST['GENDER']);

        foreach ($yrs as $k => $v) {
            $age = intval($v);
            $experience = intval($exps[$k]);
            $gender = (trim($genders[$k]) === 'M') ? 'Мужчина' : 'Женщина';
            $fields['DRIVERS'][] = "{$gender}, {$age} лет, стаж {$experience} лет";
        }
        $fields['DRIVERS'] = count($fields['DRIVERS']) ? implode("\n", $fields['DRIVERS']) : 'Мультидрайв';
        
        CEvent::Send(
            'SPM_CMIOS_NEW_ORDER',
            SITE_ID,
            $fields
        );

        $arResult['COMPLETE'] = 'Y';
    }

    if ($_REQUEST['ajax'] === 'Y') {
        $APPLICATION->RestartBuffer();
        header("Content-type: application/json; charset=utf-8");

        switch ($_REQUEST['type']) {
        case 'models':
            $api->setValue('car_mark', $_REQUEST['brand']);
            exit($api->getCarModels());
        case 'modifications':
            $api->setValue('car_mark', $_REQUEST['brand']);
            $api->setValue('car_model', $_REQUEST['model']);
            exit($api->getCarModifications());
        case 'getRequarList':
            $api->setValue('car_modification', $_REQUEST['smartpolis_car_modifications']=='' ? null: $_REQUEST['smartpolis_car_modifications']);
            $api->setValue('car_cost', $_REQUEST['smartpolis_car_cost']);
            $api->setValue('car_manufacturing_year', $_REQUEST['smartpolis_car_manufacturing_year']);
            if ( isset($_REQUEST['smartpolis_drivers_count']) && $_REQUEST['smartpolis_drivers_count']=='multiply') {
                $api->setValue('is_multidrive', true);
                $api->setValue('drivers_minimal_age', 18);
                $api->setValue('drivers_minimal_experience', 0);
                $api->setValue('drivers_count', null);
                $api->setValue('driver_set', array());
            } else {
                $api->setValue('is_multidrive', false);
                $api->setValue('drivers_minimal_age', null);
                $api->setValue('drivers_minimal_experience', null);
                $api->setValue('drivers_count', count($_REQUEST['car_driver_age']));
                $drivers = array();
                foreach($_REQUEST['car_driver_age'] as $key=>$value) {
                    $driver = array();
                    $driver['age'] = $_REQUEST['car_driver_age'][$key];
                    $driver['expirience'] = $_REQUEST['car_driver_prof'][$key];
                    $driver['gender'] = $_REQUEST['car_driver_gender'][$key];
                    $driver['is_married'] = false;
                    $driver['has_children'] = false;
                    $drivers[] = $driver;
                }
                $api->setValue('driver_set', $drivers);
            }
            $api->createResult();
            exit($api->getActiveCompanies());
        case 'getResult':
            exit($api->getResult($_REQUEST['id']));
        default:
            exit();
        }
    }

    for ($i = date('Y'); $i >= 2005; $i--)
        $arResult['YEARS'][$i] = $i;

    $driverValues = array('1', '2', '3', '4', '5', 'MULTI');
    foreach ($driverValues as $i)
        $arResult['DRIVERS'][$i] = GetMessage("SPM_FORM_VALUE_DIIVER_{$i}");

    $brands = json_decode($api->getCarMarks());
    foreach ($brands as $row) {
        $arResult['CAR_BRANDS'][$row->id] = $row->title;
    }

    $this->IncludeComponentTemplate();
}

?>