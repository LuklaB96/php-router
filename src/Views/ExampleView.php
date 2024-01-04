<!DOCTYPE html>
<html lang="en">

<head>
    <base href="/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?php asset('app.css') ?>">
    <script type="text/javascript" src="<?php asset('app.js') ?>"></script>
    <title>Document</title>
</head>

<body>
    <?php get($helloWorld); ?>
    <form action="/test" method="post">
        <?php
        //this will create hidden input field inside form, and will provide valid csrf token as a value, field name will be set as 'token'
        HiddenCSRF();
        ?>
        <input type="text" name="imie" placeholder="imie" />
        <input type="text" name="nazwisko" placeholder="nazwisko" />
        <input type="submit" />
    </form>
</body>

</html>