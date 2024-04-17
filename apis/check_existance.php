<?php
require_once("tapi_core.php");
require_once("database-account.php");
$conn = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_db);
$api = new TAPI();
$query = $api->query_params(["caseId"]);
$caseId = $query["caseId"];

$caseId_escaped = $conn->real_escape_string($caseId);
$sql = "SELECT * FROM `casesManifest` WHERE `id`='$caseId_escaped'";
$result = $conn->query($sql);
if (!$result) {
    $api->respond_error("Failed to query casesManifest: " . $conn->error);
}
if ($result->num_rows == 1) {
    $api->respond_success(array(
        "exists" => true,
        "type" => "case"
    ));
}

if (preg_match("/^PROP-[a-zA-Z0-9]+$/", $caseId)) {
    //search in `propertyCodes` `code`
    $propId = substr($caseId, 5);
    $sql = "SELECT * FROM `propertyCodes` WHERE `code`='$propId'";
    $result = $conn->query($sql);
    if (!$result) {
        $api->respond_error("Failed to query propertyCodes: " . $conn->error);
    }
    if ($result->num_rows == 0) {
        $api->respond_success(array(
            "exists" => false,
            "type" => "prop?"
        ));
    }
    $createdTime = $result->fetch_assoc()["createdTime"];
    $sql = "INSERT INTO `casesManifest` (`id`,`createdTime`) VALUES ('$caseId','$createdTime')";
    $result = $conn->query($sql);
    if (!$result) {
        $api->respond_error("Failed to create case manifest entry: " . $conn->error);
    }
    $api->respond_success(array(
        "exists" => true,
        "type" => "case"
    ), "Property exists but case does not, created case manifest entry");
}


$api->respond_success(array(
    "exists" => false,
    "type" => "prop?"
));
