<?php

//assets are accessed through asset function in views eg: asset('app.css') will be checked by Router:isAsset() function and if valid - it will be visible in browser.

$assets = [
    'public_example_file.txt' => '/config/public_example_file.txt',
    'app.css' => '/public/assets/styles/app.css',
    'app.js' => '/public/assets/js/app.js',
];

?>