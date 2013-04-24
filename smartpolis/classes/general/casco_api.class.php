<?php
/**
 * Модуль Умный Полис
 *
 * @file classes/general/casco_api.class.php
 */

class SmartpolisCascoApi {
    private $requestObject = array();
    private $connection = null;
    private $settings = null;

    public function __construct($settings) {
        @session_start();
        if ( isset($_SESSION['cascoRequestObject']) && !empty($_SESSION['cascoRequestObject']) ) {
            $this->requestObject = unserialize($_SESSION['cascoRequestObject']);
        }
        $this->connection = new SmartpolisConnectionPool();
        $this->settings = $settings;
    }

    public function setValue($name, $value) {
        $this->requestObject[$name] = $value;
        $_SESSION['cascoRequestObject'] = serialize($this->requestObject);
    }

    public function getCarMarks() {
        return $this->connection->get('/car_mark/');
    }

    public function getCarModels() {
        return $this->connection->get(
            '/car_mark/' . $this->requestObject['car_mark'] . '/car_model/'
        );
    }

    public function getCarModifications() {
        return $this->connection->get(
            '/car_mark/' . $this->requestObject['car_mark'] . '/car_model/' . $this->requestObject['car_model'] . '/car_modification/'
        );
    }

    public function createResult() {
        $payload = json_encode($this->requestObject);
        $res = $this->connection->post('/calculation/', $payload);
        $object = json_decode($res);
        $_SESSION['cascoResultId'] = $object->id;
        session_commit();
    }

    public function getActiveCompanies() {
        $companies = $this->settings->get('smartpolis_companies');
        $result = array();
        foreach($companies as $company) {
            if ($company['active']) {
                $result[] = $company;
            }
        }
        return json_encode($result);
    }

    public function getCompanies() {
        return $this->connection->get(
            '/insurance_company/active/'
        );
    }

    public function getResult($companyId) {
        $url = '/calculation/' . $_SESSION['cascoResultId'] . '/result/' . $companyId . '/';
        $res = $this->connection->post($url, '{}');
        if (empty($res)) return json_encode(false);
        $res = json_decode($res);
        $result = array();
        $result['company_id'] = $res->insurance_company->id;
        $result['logo'] = $res->insurance_company->logo;
        $result['result_id'] = $res->id;
        $result['sum'] = ceil($res->sum);

        $companies = $this->settings->get('smartpolis_companies');
        $result['our_sum'] = ceil($result['sum'] - $result['sum']*($companies[$companyId]['discount']/100));
        $result['discount'] = $companies[$companyId]['discount'];
        return json_encode($result); 
    }
}

?>