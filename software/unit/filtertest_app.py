import pytest
from filter_app import app


@pytest.fixture
def client():
    with app.test_client() as client:
        yield client


def test_filter_by_sensor(client):
    """Test filter by sensor ID"""
    response = client.post('/history', data={'sensor': '1'})
    assert response.status_code == 200
    assert b'Location 1' in response.data  # Sensor 1 should be listed


def test_filter_by_date_range(client):
    """Test filter by start and end date"""
    response = client.post('/history', data={
        'start': '2025-04-01T00:00',
        'end': '2025-04-02T23:59',
    })
    assert response.status_code == 200
    assert b'Location 1' in response.data  # Should include data for "2025-04-01" and "2025-04-02"
    assert b'Location 2' not in response.data  # Should exclude data for "2025-04-03"


def test_filter_by_sensor_and_date(client):
    """Test filter by both sensor and date range"""
    response = client.post('/history', data={
        'sensor': '1',
        'start': '2025-04-01T00:00',
        'end': '2025-04-02T23:59',
    })
    assert response.status_code == 200
    assert b'Location 1' in response.data  # Should include data for sensor 1
    assert b'Location 2' not in response.data  # Should exclude data for sensor 2
    assert b'2025-04-03' not in response.data  # Should exclude date "2025-04-03"


def test_no_filters(client):
    """Test when no filters are applied"""
    response = client.post('/history', data={})
    assert response.status_code == 200
    assert response.data.count(b'<tr>') == 4
