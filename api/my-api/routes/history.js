// routes/history.js
const express = require('express');
const pool = require('../db');

const router = express.Router();

// GET Historical Data
router.get('/', async (req, res) => {
    // e.g. /api/history?start=2025-02-01&end=2025-02-10&sensor_id=sensor_2
    const { start, end, sensor_id } = req.query;
    try {
        let query = `SELECT * FROM sensor_data WHERE timestamp BETWEEN $1 AND $2`;
        const values = [start, end];

        if (sensor_id) {
            query += ` AND sensor_id = $3`;
            values.push(sensor_id);
        }

        query += ` ORDER BY timestamp ASC`;

        const result = await pool.query(query, values);
        res.json(result.rows);
    } catch (err) {
        console.error('Historical data error:', err);
        res.status(500).json({ error: 'Error fetching historical data' });
    }
});

module.exports = router;
