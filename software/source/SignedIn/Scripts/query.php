<?php
    function historyquery($stmt, $sensor, $statuses) {
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
        if (!empty($histFilters['line']) && !empty($histFilters['sensor'])) {
            $sensor = "r0" . $histFilters['sensor'];
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
            return historyquery($stmt, $sensor, $statuses);
        }
        catch (Exception $e) {
            $result = "";
        }
    }

    function alertquery($stmt, $sensor, $statuses) {
        //$result = $conn->query($sql);
        $result = $stmt->get_result();

        // Filter Results Based on Selected Statuses
        $filteredRows = [];

        while ($row = $result->fetch_assoc()) {
            $temp = $row['temperature'];

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
        if (!empty($alertform['line']) && !empty($alertform['sensor'])) {
            $sensor = $alertform['sensor'];
            $line = "Line " . $alertform['line'];
        }

        // Process Status Checkboxes (Green, Amber, Red)
        $statuses = [];
        if (isset($alertform['green'])) $statuses[] = 'G';  // Green
        if (isset($alertform['yellow'])) $statuses[] = 'A';  // Amber
        if (isset($alertform['red'])) $statuses[] = 'R';    // Red

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
            return alertquery($stmt, $sensor, $statuses);
        }
        catch (Exception $e) {
            $result = "";
        }
    }
?>