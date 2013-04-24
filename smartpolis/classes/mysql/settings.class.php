<?php
/**
 * Модуль Умный Полис
 *
 * @file classes/mysql/settings.class.php
 */

class SmartpolisSettings extends GeneralSmartpolisSettings {
    public function get($key) {
        return ($key === 'smartpolis_companies') ? $this->_getCompanies() : parent::get($key);
    }

    public function set($key, $value = null) {
        global $DB;

        if ($key === 'smartpolis_companies') {
            foreach ($value as $k => $v) {
                $DB->Query(
                    " UPDATE" .
                    "   `b_smartpolis_companies`" .
                    " SET" .
                    "   `ACTIVE` = '" . ($v['active'] === 'Y' ? 'Y' : 'N') . "'," .
                    "   `DISCOUNT` = '" . doubleval($v['discount']) . "'" .
                    " WHERE" .
                    "   1" .
                    "   AND `ID` = '" . intval($k) . "'"
                );
            }
        } else {
            parent::set($key, $value);
        }
    }

    public function updateCompanies($provider) {
        global $DB;

        $jCompanies = json_decode($provider->getCompanies());
        $current    = $this->_getCompanies();
        $DB->StartTransaction();
        $DB->Query("TRUNCATE TABLE `b_smartpolis_company_versions`");
        $DB->Query("TRUNCATE TABLE `b_smartpolis_companies`");
        foreach ($jCompanies as $row) {
            $DB->Query(
                "  INSERT INTO" .
                "    `b_smartpolis_companies`" .
                "  VALUES (" .
                "    " . intval($row->id) . "," .
                "    '" . $DB->ForSql($row->code) . "'," .
                "    '" . $DB->ForSql($row->title) . "'," .
                "    '" . $DB->ForSql($row->logo) . "'," .
                "    '" . $DB->ForSql($row->long_title) . "'," .
                "    '" . (@$current[$row->id]['active'] ? 'Y' : 'N') . "'," .
                "    '" . doubleval(@$current[$row->id]['discount']) . "'" .
                "  )"
            );
            foreach ($row->versions as $v) {
                $DB->Query(
                    "  INSERT INTO" .
                    "    `b_smartpolis_company_versions`" .
                    "  VALUES (" .
                    "    " . intval($v->id) . "," .
                    "    " . intval($row->id) . "," .
                    "    '" . $DB->ForSql($v->date) . "'," .
                    "    '" . ($v->is_online ? 'Y' : 'N') . "'" .
                    "  )"
                );
            }
        }
        $DB->Commit();
    }

    protected function _getCompanies() {
        global $DB;

        $result = array();

        $res = $DB->Query(
            " SELECT" .
            "   *" .
            " FROM" .
            "   `b_smartpolis_companies`" .
            " WHERE" .
            "   1" .
            " ORDER BY" .
            "   `TITLE` ASC"
        );

        while ($row = $res->Fetch()) {
            $result[intval($row['ID'])] = array(
                'id'         => intval($row['ID']),
                'code'       => $row['CODE'],
                'title'      => $row['TITLE'],
                'logo'       => $row['LOGO'],
                'long_title' => $row['LONG_TITLE'],
                'active'     => $row['ACTIVE'] === 'Y',
                'discount'   => doubleval($row['DISCOUNT'])
            );
        }

        return $result;
    }
}

?>