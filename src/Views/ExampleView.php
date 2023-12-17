<!DOCTYPE html>
<html lang="en">

<head>
    <base href="/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?php asset('app.css') ?>">
    <script type="text/javascript" src="/public/assets/js/app.js"></script>
    <title>Document</title>
</head>

<body>
    <?php get($helloWorld) ?>
    <form action="/test" method="post">
        <input type="text" name="title" placeholder="title" />
        <input type="text" name="description" placeholder="description" />
        <input type="submit" />
    </form>
</body>

</html>