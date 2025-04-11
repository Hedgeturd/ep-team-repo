import pytest
from reg_app import app  # Import the Flask app

# Use pytest fixture for the test client
@pytest.fixture
def client():
    with app.test_client() as client:
        yield client

# Test valid registration
def test_register_post_valid(client):
    response = client.post('/register', data={
        'username': 'valid_user123',
        'email': 'valid@example.com'
    })
    assert response.status_code == 200
    assert b'Registration successful for valid_user123!' in response.data

# Test invalid email (missing @ symbol)
def test_register_post_invalid_email(client):
    response = client.post('/register', data={
        'username': 'valid_user123',
        'email': 'invalidemail.com'
    })
    assert response.status_code == 200
    assert b'Invalid email address.' in response.data

# Test invalid username (too short)
def test_register_post_invalid_username_short(client):
    response = client.post('/register', data={
        'username': 'ab',  # Too short
        'email': 'valid@example.com'
    })
    assert response.status_code == 200
    assert b'Username must be at least 3 characters long and contain only letters, numbers, and underscores.' in response.data

# Test invalid username (contains special characters)
def test_register_post_invalid_username_special_chars(client):
    response = client.post('/register', data={
        'username': 'invalid@user',  # Invalid username with special character '@'
        'email': 'valid@example.com'
    })
    assert response.status_code == 200
    assert b'Username must be at least 3 characters long and contain only letters, numbers, and underscores.' in response.data

# Test invalid username (contains space)
def test_register_post_invalid_username_space(client):
    response = client.post('/register', data={
        'username': 'invalid user',  # Invalid username with space
        'email': 'valid@example.com'
    })
    assert response.status_code == 200
    assert b'Username must be at least 3 characters long and contain only letters, numbers, and underscores.' in response.data

# Test missing username (empty string)
def test_register_post_missing_username(client):
    response = client.post('/register', data={
        'username': '',
        'email': 'valid@example.com'
    })
    assert response.status_code == 200
    assert b'Username must be at least 3 characters long and contain only letters, numbers, and underscores.' in response.data

# Test missing email (empty string)
def test_register_post_missing_email(client):
    response = client.post('/register', data={
        'username': 'validuser',
        'email': ''
    })
    assert response.status_code == 200
    assert b'Invalid email address.' in response.data
