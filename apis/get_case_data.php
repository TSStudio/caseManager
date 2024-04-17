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
$created = 0;
if ($result->num_rows == 1) {
    $created = $result->fetch_assoc()["createdTime"];
} else {
    $api->respond_error("Case not found");
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
