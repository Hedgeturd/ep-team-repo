from flask import Flask, request, render_template_string

app = Flask(__name__)

# Sample data for testing
SENSOR_DATA = [
    {"sensor_id": 1, "location": "Location 1", "temp": 240, "timestamp": "2025-04-01 12:00:00"},
    {"sensor_id": 1, "location": "Location 1", "temp": 320, "timestamp": "2025-04-02 13:00:00"},
    {"sensor_id": 2, "location": "Location 2", "temp": 400, "timestamp": "2025-04-03 14:00:00"},
]

@app.route('/history', methods=['GET', 'POST'])
def history():
    filtered_data = SENSOR_DATA
    start_date = request.form.get('start')
    end_date = request.form.get('end')
    sensor = request.form.get('sensor')
    line = request.form.get('line')

    if request.method == 'POST':
        # Filter by sensor
        if sensor:
            filtered_data = [entry for entry in filtered_data if str(entry["sensor_id"]) == sensor]

        # Simulate filter based on date range
        if start_date and end_date:
            filtered_data = [entry for entry in filtered_data if start_date <= entry["timestamp"] <= end_date]

    return render_template_string('''
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Historical Data</title>
    </head>
    <body>
        <form method="POST">
            <label for="start">Start Date:</label>
            <input type="datetime-local" name="start" value="{{ request.form.get('start') }}"><br>
            <label for="end">End Date:</label>
            <input type="datetime-local" name="end" value="{{ request.form.get('end') }}"><br>
            <label for="sensor">Sensor ID:</label>
            <input type="number" name="sensor" value="{{ request.form.get('sensor') }}"><br>
            <label for="line">Line Number:</label>
            <input type="number" name="line" value="{{ request.form.get('line') }}"><br>
            <button type="submit">Apply</button>
            <button type="reset">Reset</button>
        </form>
        
        <h2>Filtered Results</h2>
        <table>
            <thead>
                <tr>
                    <th>Sensor ID</th>
                    <th>Location</th>
                    <th>Temperature</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                {% for entry in filtered_data %}
                <tr>
                    <td>{{ entry.sensor_id }}</td>
                    <td>{{ entry.location }}</td>
                    <td>{{ entry.temp }}</td>
                    <td>{{ entry.timestamp }}</td>
                </tr>
                {% endfor %}
            </tbody>
        </table>
    </body>
    </html>
    ''', filtered_data=filtered_data)


if __name__ == '__main__':
    app.run(debug=True)
