<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<h2>Login</h2>

<form method="POST" action="{{ route('login.submit') }}">
    @csrf

    <div>
        <label>Username</label>
        <input type="email" name="username" required>
    </div>

    <div>
        <label>Password</label>
        <input type="password" name="password" required>
    </div>

    <button type="submit">Login</button>
</form>

</body>
</html>