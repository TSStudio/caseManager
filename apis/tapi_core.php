<?php
define("TAPI_CONFIG_SERVER", "TSStudio Main S2");
/**
 * This is the core file for the TAPI API standard.
 * TAPI uses www-form-urlencoded for all requests. and JSON for all responses.
 * JSON response format:
 * {
 *  "status": "success" or "error",
 *  "message": "message", - this is optional when status is "success"
 *  "data": "data" - this is optional when status is "error" 
 *  "server": "server" - this is optional, to show which server the response came from
 * }
 */
class TAPI
{
    function __construct($debug = false)
    {
        if (!$debug) {
            error_reporting(0);
        } else {
            error_reporting(E_ALL);
        }
        header("Content-Type: application/json");
    }
    public function respond_success($data, $message = "")
    {
        $response = array(
            "status" => "success",
            "message" => $message,
            "data" => $data,
            "server" => TAPI_CONFIG_SERVER
        );
        echo json_encode($response);
        exit();
    }
    public function respond_error($message, $data = [])
    {
        $response = array(
            "status" => "error",
            "message" => $message,
            "data" => $data,
            "server" => TAPI_CONFIG_SERVER
        );
        echo json_encode($response);
        exit();
    }
    public function query_params($params)
    {
        $response = array();
        foreach ($params as $param) {
            if (!isset($_REQUEST[$param])) {
                $this->respond_error("Missing parameter: " . $param);
            }
            if (strlen($_REQUEST[$param]) == 0) {
                $this->respond_error("Empty parameter: " . $param);
            }
            $response[$param] = $_REQUEST[$param];
        }
        return $response;
    }
    public function test_permission($sso, $permission)
    {
        $status = $sso->run(2);
        if ($status != 0) {
            $this->respond_error("SSO Error: Not logged in");
        }
        $status = $sso->test_permission($_SESSION["uid"], $permission);
        if ($status != 1) {
            $this->respond_error("SSO Error: No permission");
        }
    }
}
