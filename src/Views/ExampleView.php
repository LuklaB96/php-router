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
        <input type="text" name="data1" value="mydata" />
        <input type="text" name="data2" value="mydata" />
        <input type="text" name="data3" value="mydata" />
        <input type="text" name="data4" value="mydata" />
        <input type="submit" />
    </form>
</body>

</html>