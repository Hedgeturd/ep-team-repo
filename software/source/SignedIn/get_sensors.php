<?php
session_start();
require_once('../scripts/dbconnect.php');

// Fetch all sensors ordered by location and sensor_name
$query = "SELECT sensor_name, location, temperature, updated_at FROM sensors ORDER BY location, sensor_name";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['sensor_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['location']) . "</td>";
        echo "<td>" . htmlspecialchars($row['temperature']) . "°C</td>";
        echo "<td>" . (($row['temperature'] > 50) ? "⚠️ Alert" : "✅ OK") . "</td>";
        echo "<td>" . htmlspecialchars($row['updated_at']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>No sensor data available.</td></tr>";
}
?>
