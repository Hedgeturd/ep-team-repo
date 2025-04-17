<?php
    // History Page Table Functions
    function historyquery($stmt, $sensor, $statuses, $line) {
        //$result = $conn->query($sql);
        $result = $stmt->get_result();

        // Filter Results Based on Selected Statuses
        $filteredRows = [];

        while ($row = $result->fetch_assoc()) {
            $value = $row[$sensor];

            // Filter Based on Status Selection (Green, Amber, Red)
            $include = false;

            if (in_array('G', $statuses) && $value <= 250) {
                $include = true; // Green
            }
            if (in_array('A', $statuses) && $value > 250 && $value <= 375) {
                $include = true; // Amber
            }
            if (in_array('R', $statuses) && $value > 375) {
                $include = true; // Red
            }

            // If the value matches any selected filter, add to the filtered result
            if ($include) {
                $row['status'] = ($value <= 250) ? 'Green' : (($value <= 375) ? 'Amber' : 'Red');
                $filteredRows[] = $row;
            }
        }

        return $filteredRows;
    }

    // Pulling the $_POST value from the form before
    function historyfilters($histFilters) {
        $sensor = $line = "";
        if (!empty($histFilters['sensor'])) {
            $sensor = "r0" . $histFilters['sensor'];
        }
        if (!empty($histFilters['line'])) {
            $line = "line" . $histFilters['line'];
        }

        // Process Status Checkboxes (Green, Amber, Red)
        $statuses = [];
        if (isset($histFilters['green'])) $statuses[] = 'G';  // Green
        if (isset($histFilters['amber'])) $statuses[] = 'A';  // Amber
        if (isset($histFilters['red'])) $statuses[] = 'R';    // Red

        require_once('dbconnect.php');

        $sql = "SELECT timestamp, $sensor FROM $line";
        $params = [];
        $types = "";

        if (!empty($histFilters['start']) && !empty($histFilters['end'])) {
            $startDate = date('Y-m-d H:i:s', strtotime($histFilters['start']));
            $endDate = date('Y-m-d H:i:s', strtotime($histFilters['end']));

            $sql .= " WHERE timestamp BETWEEN ? AND ?";
            $params = [$startDate, $endDate];
            $types = "ss";
        }

        $sql .= " ORDER BY timestamp DESC LIMIT 100"; // optional: adjust limit

        // statements pulled and prepped here
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();

        try {
            return historyquery($stmt, $sensor, $statuses, $line);
        }
        catch (Exception $e) {
            $result = "";
        }
    }

    // Alerts Page Table Functions
    function alertquery($stmt, $sensor, $statuses, $line) {
        //$result = $conn->query($sql);
        $result = $stmt->get_result();

        // Filter Results Based on Selected Statuses
        $filteredRows = [];

        while ($row = $result->fetch_assoc()) {
            $temp = $row['temperature'];

            if (!empty($sensor) && $row['sensor_id'] != $sensor) {
                continue; // Skip this row if it doesn't match the sensor
            }

            if (!empty($line) && $row['location'] != $line) {
                continue; // Skip this row if it doesn't match the sensor
            }

            // Filter Based on Status Selection (Green, Amber, Red)
            $include = false;

            if (in_array('G', $statuses) && $row['flag'] == "green") {
                $include = true; // Green
            }
            if (in_array('A', $statuses) && $row['flag'] == "yellow") {
                $include = true; // Amber
            }
            if (in_array('R', $statuses) && $row['flag'] == "red") {
                $include = true; // Red
            }

            // If the value matches any selected filter, add to the filtered result
            if ($include) {
                $row['status'] = ($row['flag'] == "green") ? 'Green' : (($row['flag'] == "yellow") ? 'Yellow' : 'Red');
                $row['location'] = $row['location']; // optional — already included
                $filteredRows[] = $row;
            }
        }

        return $filteredRows;
    }

    function alertfilters($alertform) {
        $sensor = "";
        $line = "";
        if (!empty($alertform['sensor'])) {
            $sensor = $alertform['sensor'];
        }
        if (!empty($alertform['line'])) {
            $line = "Line " . $alertform['line'];
        }

        // Process Severity Checkboxes (Green, Yellow, Red)
        $statuses = [];
        if (isset($alertform['green'])) $statuses[] = 'G';  // Low
        if (isset($alertform['yellow'])) $statuses[] = 'A';  // Medium
        if (isset($alertform['red'])) $statuses[] = 'R';    // High

        require_once('dbconnect.php');

        $sql = "SELECT alerts.*, sensors.location FROM alerts JOIN sensors ON alerts.sensor_id = sensors.id";
        $params = [];
        $types = "";

        if (!empty($alertform['start']) && !empty($alertform['end'])) {
            $startDate = date('Y-m-d H:i:s', strtotime($alertform['start']));
            $endDate = date('Y-m-d H:i:s', strtotime($alertform['end']));

            $sql .= " WHERE recorded_at BETWEEN ? AND ?";
            $params = [$startDate, $endDate];
            $types = "ss";
        }

        $sql .= " ORDER BY recorded_at DESC LIMIT 100"; // optional: adjust limit

        // statements pulled and prepped here
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();

        try {
            return alertquery($stmt, $sensor, $statuses, $line);
        }
        catch (Exception $e) {
            $result = "";
        }
    }

    // Admin Page Table Functions
    function requestfilters($filters) {
        require_once('../scripts/dbconnect.php'); // Assumes $conn is defined in here

        $conditions = [];
        $params = [];
        $types = "";

        // Add conditions only if fields are not empty
        if (!empty($filters['req_start']) && !empty($filters['req_end'])) {
            $conditions[] = "requested_at BETWEEN ? AND ?";
            $params[] = date('Y-m-d H:i:s', strtotime($filters['req_start']));
            $params[] = date('Y-m-d H:i:s', strtotime($filters['req_end']));
            $types .= "ss";
        }

        if (!empty($filters['req_id'])) {
            $conditions[] = "id = ?";
            $params[] = $filters['req_id'];
            $types .= "i";
        }

        if (!empty($filters['req_email'])) {
            $conditions[] = "email LIKE ?";
            $params[] = '%' . $filters['req_email'] . '%';
            $types .= "s";
        }

        if (!empty($filters['req_role'])) {
            $conditions[] = "type = ?";
            $params[] = $filters['req_role'];
            $types .= "s";
        }

        if (!empty($filters['req_type'])) {
            $conditions[] = "role = ?";
            $params[] = $filters['req_type'];
            $types .= "s";
        }

        // Base query
        $sql = "SELECT * FROM requests"; // Replace with your actual table name

        // Add WHERE clause if any filters are active
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY requested_at DESC LIMIT 100";

        // Prepare and bind
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();

        try {
            return requestquery($stmt);
        } catch (Exception $e) {
            return [];
        }
    }

    function requestquery($stmt) {
        $result = $stmt->get_result();
        $rows = [];

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }
?>