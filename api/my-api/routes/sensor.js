// routes/sensor.js
const express = require('express');
const pool = require('../db');

const router = express.Router();

// GET Real-Time Data (Simulated)
router.get('/real-time-data', async (req, res) => {
    const { line = 4 } = req.query;
    try {
        // Retrieve last known temperature from sensor_data
        const lastReading = await pool.query(
            'SELECT temperature FROM sensor_data WHERE line = $1 ORDER BY timestamp DESC LIMIT 1',
            [line]
        );
        let lastTemp = 100;
        if (lastReading.rows.length) {
            lastTemp = parseFloat(lastReading.rows[0].temperature);
        }

        // Simulate a new reading
        const newTemp = lastTemp + (Math.random() - 0.5) * 5;
        const response = {
            sensor_id: `sensor_${Math.floor(Math.random() * 8) + 1}`,
            line: parseInt(line),
            temperature: newTemp.toFixed(2),
            timestamp: new Date().toISOString(),
        };

        // Insert the new reading into sensor_data
        await pool.query(
            `INSERT INTO sensor_data (line, sensor_id, temperature, timestamp)
       VALUES ($1, $2, $3, $4)`,
            [response.line, response.sensor_id, response.temperature, response.timestamp]
        );

        res.json(response);
    } catch (err) {
        console.error('Real-time data error:', err);
        res.status(500).json({ error: 'Error generating real-time data' });
    }
});

module.exports = router;
