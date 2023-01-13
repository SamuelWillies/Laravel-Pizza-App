<?php

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Login</title>
</head>
<body>
    <h1>Login</h1>

    <form method="post" action="/login">
        @csrf
        <fieldset>
            <legend>Login</legend>

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
            <button type="submit" name="submit" formnovalidate>Login</button>
        </div>

    </form>

</body>
</html>
