<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
</head>

<body>
    <h1>Welcome, {{ $user->name }}!</h1>
    <p>Thanks for registering at {{ config('app.name') }}.</p>
</body>

</html>
