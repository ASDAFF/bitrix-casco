<?php
/**
 * Модуль Умный Полис
 *
 * @file install/index.php
 */

IncludeModuleLangFile(__FILE__);

class smartpolis extends CModule {
    var $MODULE_ID           = __CLASS__;
    var $MODULE_VERSION      = '0.0.2';
    var $MODULE_VERSION_DATE = '2013-03-06 18:00:00';
    var $MODULE_CSS          = '';
    var $MODULE_DESCRIPTION;
    var $MODULE_NAME;

    public function __construct() {
        $this->MODULE_NAME        = GetMessage('SPM_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('SPM_MODULE_DESCRIPTION');
    }

    public function GetModuleTasks() {
        return array();
    }

    public function InstallDB() {
        global $DB, $APPLICATION, $DOCUMENT_ROOT, $DBType;

        $this->errors = $DB->RunSQLBatch("{$DOCUMENT_ROOT}/bitrix/modules/{$this->MODULE_ID}/install/db/{$DBType}/install.sql");
        $this->InstallTasks();
        if($this->errors !== false) {
            $APPLICATION->ThrowException(implode('', $this->errors));
            return false;
        }

        RegisterModule($this->MODULE_ID);
        CModule::IncludeModule($this->MODULE_ID);
        return true;
    }

    public function UnInstallDB($params = array()) {
        global $DB, $APPLICATION, $DOCUMENT_ROOT, $DBType;

        $this->errors = $DB->RunSQLBatch("{$DOCUMENT_ROOT}/bitrix/modules/{$this->MODULE_ID}/install/db/{$DBType}/uninstall.sql");
        if($this->errors !== false) {
            $APPLICATION->ThrowException(implode('', $this->errors));
            return false;
        }

        UnRegisterModule($this->MODULE_ID);
        return true;
    }

    public function InstallEvents() {
        $typeDescription = <<<EOD
#CAR_BRAND# - производитель автомобиля
#CAR_MODEL# - модель автомобиля
#CAR_MODIFICATION# - модификация
#CAR_PRICE# - стоимость автомобиля
#CAR_YEAR# - год выпуска
#DRIVERS# - список водителей
#CLIENT_NAME# - имя клиента
#CLIENT_EMAIL# - e-mail клиента
#CLIENT_PHONE# - контактный телефон клиента
#PREFERRED_DATE# - дата
#DELIVERY# - способ доставки
#COMMENTS# - комментарии
EOD;
        $et = new CEventType();
        $et->Add(array(
            'LID'         => 'ru',
            'EVENT_NAME'  => 'SPM_CMIOS_NEW_ORDER',
            'NAME'        => 'Новый заказ на расчёт полиса',
            'DESCRIPTION' => $typeDescription
        ));

        $sites = array();
        $res = CSite::GetList();
        while ($row = $res->GetNext()) {
            $sites[] = $row['LID'];
        }

        $message = <<<EOM
Вы успешно заказали полис в нашей компании:

Производитель: #CAR_BRAND#
Модель:        #CAR_MODEL#
Модификация:   #CAR_MODIFICATION#
Стоимость:     #CAR_PRICE#
Год выпуска:   #CAR_YEAR#

Водители:
#DRIVERS#

Контактные данные:
E-mail:  #CLIENT_EMAIL#
Телефон: #CLIENT_PHONE#

Данные полиса:
Дата: #PREFERRED_DATE#
Способ доставки:
#DELIVERY#

Комментарии:
#COMMENTS#

--
С уважением,
почтовый робот сайта #SITE_NAME#
EOM;
        $em = new CEventMessage();
        $em->Add(array(
            'ACTIVE'     => 'Y',
            'EVENT_NAME' => 'SPM_CMIOS_NEW_ORDER',
            'LID'        => $sites,
            'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
            'EMAIL_TO'   => '#CLIENT_EMAIL#',
            'SUBJECT'    => 'Благодарим Вас за заказ полиса!',
            'BODY_TYPE'  => 'text',
            'MESSAGE'    => $message
        ));

        $message = <<<EOM
Новый заказ полиса на сайте #SITE_NAME#:

Производитель: #CAR_BRAND#
Модель:        #CAR_MODEL#
Модификация:   #CAR_MODIFICATION#
Стоимость:     #CAR_PRICE#
Год выпуска:   #CAR_YEAR#

Водители:
#DRIVERS#

Контактные данные:
ФИО:     #CLIENT_NAME#
E-mail:  #CLIENT_EMAIL#
Телефон: #CLIENT_PHONE#

Данные полиса:
Дата: #PREFERRED_DATE#
Способ доставки:
#DELIVERY#

Комментарии:
#COMMENTS#

--
С уважением,
почтовый робот сайта #SITE_NAME#
EOM;
        $em = new CEventMessage();
        $em->Add(array(
            'ACTIVE'     => 'Y',
            'EVENT_NAME' => 'SPM_CMIOS_NEW_ORDER',
            'LID'        => $sites,
            'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
            'EMAIL_TO'   => '#DEFAULT_EMAIL_FROM#',
            'SUBJECT'    => 'Новый заказа полиса',
            'BODY_TYPE'  => 'text',
            'MESSAGE'    => $message
        ));

        return true;
    }

    public function UnInstallEvents() {
        $et = new CEventType;
        $et->Delete('SPM_CMIOS_NEW_ORDER');

        $res = CEventMessage::GetList(
            $by = 'id',
            $order = 'asc',
            array(
                'TYPE_ID' => 'SPM_CMIOS_NEW_ORDER'
            )
        );
        while ($row = $res->GetNext()) {
            $em = new CEventMessage();
            $em->Delete($row['ID']);
        }

        return true;
    }

    public function InstallFiles() {
        global $DOCUMENT_ROOT;
        CopyDirFiles(
            "{$DOCUMENT_ROOT}/bitrix/modules/{$this->MODULE_ID}/install/components/",
            "{$DOCUMENT_ROOT}/bitrix/components/",
            true,
            true
        );
        return true;
    }

    public function UnInstallFiles() {
        global $DOCUMENT_ROOT;
        DeleteDirFilesEx("{$DOCUMENT_ROOT}/bitrix/components/cmios");
        @rmdir("{$DOCUMENT_ROOT}/bitrix/components/cmios");
        return true;
    }

    public function DoInstall() {
        global $USER, $APPLICATION, $step, $errors;

        if (!$USER->IsAdmin())
            return;

        $step = intval($step);
        if ($step < 2) {
            $APPLICATION->IncludeAdminFile(
                GetMessage('SPM_INSTALL_TITLE'),
                $this->_getModulePath() . '/install/step1.php'
            );
        } elseif ($step == 2) {
            if ($this->InstallDB()) {
                $this->InstallEvents();
                $this->InstallFiles();
            }
            $errors = $this->errors;
            $APPLICATION->IncludeAdminFile(
                GetMessage('SPM_INSTALL_TITLE'),
                $this->_getModulePath() . '/install/step2.php'
            );
        }
    }

    public function DoUninstall() {
        global $USER, $APPLICATION, $step, $errors;

        if (!$USER->IsAdmin())
            return;

        $step = intval($step);
        if ($step < 2) {
            $APPLICATION->IncludeAdminFile(
                GetMessage('SPM_UNINSTALL_TITLE'),
                $this->_getModulePath() . '/install/unstep1.php'
            );
        } elseif ($step == 2) {
            $this->UnInstallDB();
            $this->UnInstallEvents();
            $this->UnInstallFiles();
            $errors = $this->errors;
            $APPLICATION->IncludeAdminFile(
                GetMessage('SPM_UNINSTALL_TITLE'),
                $this->_getModulePath() . '/install/unstep2.php'
            );
        }
    }

    protected function _getModulePath() {
        global $DOCUMENT_ROOT;
        return  "{$DOCUMENT_ROOT}/bitrix/modules/{$this->MODULE_ID}";
    }
}

?>