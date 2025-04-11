<?php
session_start();
require_once('../scripts/dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sensorName = $_POST['sensor'] ?? null;
    $temperature = $_POST['temperature'] ?? null;
    $location = $_POST['location'] ?? null;

    if ($sensorName && $temperature && $location) {
        $sensorName = $conn->real_escape_string($sensorName);
        $temperature = floatval($temperature);
        $location = $conn->real_escape_string($location);

        // Check if sensor already exists
        $check = $conn->prepare("SELECT id FROM sensors WHERE sensor_name = ?");
        $check->bind_param("s", $sensorName);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            // Update temperature and location
            $update = $conn->prepare("UPDATE sensors SET temperature = ?, location = ? WHERE sensor_name = ?");
            $update->bind_param("dss", $temperature, $location, $sensorName);
            $update->execute();
        } else {
            // Insert new sensor
            $insert = $conn->prepare("INSERT INTO sensors (sensor_name, location, temperature, created_at) VALUES (?, ?, ?, NOW())");
            $insert->bind_param("ssd", $sensorName, $location, $temperature);
            $insert->execute();
        }

        http_response_code(200);
        echo "Sensor data saved successfully.";
    } else {
        http_response_code(400);
        echo "Missing data.";
    }
}
?>
