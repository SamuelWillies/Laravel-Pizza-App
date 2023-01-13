<?php

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Register</title>
</head>
<body>
    <h1>Register</h1>

    <form method="post" action="/store">
        @csrf
        <fieldset>
            <legend>Enter your registration details</legend>

            <div>
                <label for="username">Username:</label>
                <input type="text" required name="name" id="username" value="{{ old('name') }}">
            </div>

            <div>
                <label for="password">Password:</label>
                <input type="password" required name="password" id="password">
            </div>

        </fieldset>
        <div>
            <button type="submit" name="submit" formnovalidate>Submit Details</button>
        </div>

    </form>
@foreach ($errors->all() as $message)
    <p>{{ $message }}</p>
@endforeach
</body>
</html>
