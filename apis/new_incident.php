<?php
require_once("tapi_core.php");
require_once("database-account.php");
require_once("sso.php");
$conn = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_db);
$sso = new sso($mysql_host, $mysql_user, $mysql_password, $mysql_db);
$api = new TAPI();

$api->test_permission($sso, "case_management");

$query = $api->query_params(["caseId", "text"]);
$caseId = $query["caseId"];
$text = $query["text"];
$text_e1 = htmlspecialchars($text);
$caseId_escaped = $conn->real_escape_string($caseId);
$text_escapted = $conn->real_escape_string($text_e1);

$sql = "SELECT * FROM `casesManifest` WHERE `id`='$caseId_escaped'";
$result = $conn->query($sql);
if (!$result) {
    $api->respond_error("Failed to query casesManifest: " . $conn->error);
}
$created = 0;
if ($result->num_rows == 1) {
    $created = $result->fetch_assoc()["createdTime"];
} else {
    $api->respond_error("Case not found");
}

$time = time();
$sql = "INSERT INTO `casesData` (`caseId`, `text`, `time`) VALUES ('$caseId_escaped', '$text_escapted', $time)";
$result = $conn->query($sql);
if (!$result) {
    $api->respond_error("Failed to insert casesData: " . $conn->error);
}
$sql = "SELECT * FROM `casesData` WHERE `caseId`='$caseId_escaped' ORDER BY `dataId` DESC";
$result = $conn->query($sql);
if (!$result) {
    $api->respond_error("Failed to query casesData: " . $conn->error);
}

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = array(
        "dataId" => $row["dataid"],
        "text" => $row["text"],
        "timestamp" => $row["time"]
    );
}
$data[] = array(
    "dataId" => -1,
    "text" => "Created",
    "timestamp" => $created
);
$api->respond_success(array(
    "data" => $data,
    "created" => $created
));
