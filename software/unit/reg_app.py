import re
from flask import Flask, render_template_string, request

app = Flask(__name__)

# A simple route that serves the registration page
@app.route('/register', methods=['GET', 'POST'])
def register():
    email = ''
    username = ''
    message = ''

    if request.method == 'POST':
        email = request.form.get('email')
        username = request.form.get('username')

        # Email validation: must contain '@'
        if '@' not in email:
            message = 'Invalid email address.'
        # Username validation: must be at least 3 characters and only contain letters, numbers, and underscores
        elif len(username) < 3 or not re.match(r'^[a-zA-Z0-9_]+$', username):
            message = 'Username must be at least 3 characters long and contain only letters, numbers, and underscores.'
        else:
            message = f'Registration successful for {username}!'

    return render_template_string('''
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Registration Page</title>
        </head>
        <body>
            <h1>Register</h1>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <button type="submit">Submit</button>
            </form>
            <p>{{ message }}</p>
        </body>
        </html>
    ''', message=message)

if __name__ == '__main__':
    app.run(debug=True)
