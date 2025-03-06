const fs = require('fs');
const csv = require('csv-parser');
const pool = require('../db'); // Ensure your database connection is correct

async function importCsv(filePath, lineNumber) {
    const results = [];

    return new Promise((resolve, reject) => {
        fs.createReadStream(filePath)
            .pipe(csv())
            .on('data', (row) => {
                try {
                    const timestamp = row.timestamp ? new Date(row.timestamp) : null;
                    if (!timestamp) {
                        console.warn(`⚠️ Skipping row with missing timestamp:`, row);
                        return;
                    }

                    // Iterate through sensor readings (r01 - r08)
                    Object.keys(row).forEach((key) => {
                        if (key.startsWith('r')) {
                            const sensorId = key; // r01, r02, etc.
                            const value = parseFloat(row[key]);

                            if (!isNaN(value)) {
                                results.push({ line: lineNumber, sensor_id: sensorId, value, timestamp });
                            } else {
                                console.warn(`⚠️ Skipping invalid sensor value in ${filePath}:`, { sensorId, row });
                            }
                        }
                    });
                } catch (error) {
                    console.error(`⚠️ Error processing row in ${filePath}:`, row, error);
                }
            })
            .on('end', async () => {
                if (results.length === 0) {
                    console.log(`⚠️ No valid data found in ${filePath}`);
                    return resolve();
                }

                const client = await pool.connect();
                try {
                    const queryText = `
                        INSERT INTO sensor_data (line, sensor_id, value, timestamp)
                        VALUES ($1, $2, $3, $4)
                    `;

                    for (const data of results) {
                        await client.query(queryText, [
                            data.line,
                            data.sensor_id,
                            data.value,
                            data.timestamp,
                        ]);
                    }

                    console.log(`✅ Imported ${results.length} records from ${filePath}`);
                    resolve();
                } catch (err) {
                    console.error(`❌ Error inserting data from ${filePath}:`, err);
                    reject(err);
                } finally {
                    client.release();
                }
            })
            .on('error', (error) => {
                console.error(`❌ Error reading file ${filePath}:`, error);
                reject(error);
            });
    });
}

// Process uploaded CSV files
(async () => {
    try {
        await importCsv('./line4.csv', 4);
        await importCsv('./line5.csv', 5);
    } catch (err) {
        console.error('❌ Import process failed:', err);
    }
})();
