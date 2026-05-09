<!DOCTYPE html>
<html>
<body>
    <h2>Hello {{ $name }},</h2>
    <p>You have been invited. Here are your login credentials:</p>
    <p><strong>Email:</strong> {{ $email }}</p>
    <p><strong>Password:</strong> {{ $password }}</p>
    <a href="{{ url('/') }}">Click here to login</a>
</body>
</html>