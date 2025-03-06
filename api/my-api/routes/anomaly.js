// routes/anomaly.js
const express = require('express');
const pool = require('../db');

const router = express.Router();

// POST Anomaly Detection
router.post('/', async (req, res) => {
    const { sensor_id, temperature } = req.body;
    try {
        // Example threshold logic
        const configResult = await pool.query(
            "SELECT parameter_name, value FROM system_config WHERE parameter_name IN ('lower_threshold', 'upper_threshold', 'critical_threshold')"
        );
        const configMap = {};
        configResult.rows.forEach(row => {
            configMap[row.parameter_name] = parseFloat(row.value);
        });

        let status = 'Green';
        if (temperature > configMap.critical_threshold) {
            status = 'Red';
        } else if (temperature > configMap.upper_threshold) {
            status = 'Amber';
        }

        // Insert alert if needed
        if (status !== 'Green') {
            await pool.query(
                `INSERT INTO alerts (sensor_id, timestamp, alert_level, description)
                 VALUES ($1, NOW(), $2, 'Temperature anomaly detected')`,
                [sensor_id, status]
            );
        }

        res.json({ sensor_id, temperature, status });
    } catch (err) {
        console.error('Anomaly detection error:', err);
        res.status(500).json({ error: 'Error detecting anomaly' });
    }
});

module.exports = router;
