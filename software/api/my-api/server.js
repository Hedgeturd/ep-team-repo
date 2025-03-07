// server.js
const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');

// Import route files
const sensorRoutes = require('./routes/sensor');
const historyRoutes = require('./routes/history');
const anomalyRoutes = require('./routes/anomaly');
const alertsRoutes = require('./routes/alerts');

const app = express();
const PORT = 5000; // Hardcode the port or use process.env.PORT if you like

// Middleware
app.use(cors());
app.use(bodyParser.json());

// Routes
app.use('/api/sensor', sensorRoutes);
app.use('/api/history', historyRoutes);
app.use('/api/anomaly', anomalyRoutes);
app.use('/api/alerts', alertsRoutes);

// Start Server
app.listen(PORT, () => {
    console.log(` Server running on port ${PORT}`);
});
