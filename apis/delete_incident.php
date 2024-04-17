<?php
require_once("tapi_core.php");
require_once("database-account.php");
require_once("sso.php");
$conn = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_db);
$sso = new sso($mysql_host, $mysql_user, $mysql_password, $mysql_db);
$api = new TAPI();

$api->test_permission($sso, "case_management");

$query = $api->query_params(["dataId"]);
$dataId = $query["dataId"];
$dataId_escaped = $conn->real_escape_string($dataId);

$sql = "DELETE FROM `casesData` WHERE `dataId`='$dataId_escaped'";
$result = $conn->query($sql);
if (!$result) {
    $api->respond_error("Failed to delete casesData: " . $conn->error);
}
$api->respond_success(array());
