<!DOCTYPE html>
<html lang="en">

<head>
    <base href="/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?php asset('app.css') ?>">
    <script type="text/javascript" src="<?php asset('app.js') ?>"></script>
    <title>Main page</title>
</head>

<body>
    <?php
    use App\Lib\Security\HTML\SafeLogoutButtonGenerator;

    SafeLogoutButtonGenerator::generate();
    ?>
</body>

</html>