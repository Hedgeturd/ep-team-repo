// routes/alerts.js
const express = require('express');
const pool = require('../db');

const router = express.Router();

// GET all alerts
router.get('/', async (req, res) => {
    try {
        const result = await pool.query('SELECT * FROM alerts ORDER BY timestamp DESC');
        res.json(result.rows);
    } catch (err) {
        console.error('Alerts error:', err);
        res.status(500).json({ error: 'Error fetching alerts' });
    }
});

// Acknowledge an alert
router.post('/acknowledge/:id', async (req, res) => {
    const alertId = req.params.id;
    try {
        await pool.query(
            `UPDATE alerts SET acknowledged = TRUE WHERE id = $1`,
            [alertId]
        );
        res.json({ message: 'Alert acknowledged', alertId });
    } catch (err) {
        console.error('Acknowledge alert error:', err);
        res.status(500).json({ error: 'Error acknowledging alert' });
    }
});

module.exports = router;
