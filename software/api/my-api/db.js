// db.js
const { Pool } = require('pg');

const pool = new Pool({
    user: 'postgres',
    host: 'localhost',
    database: 'sensor_db',
    password: 'r@kusen5',
    port: 5432,
});

module.exports = pool;
