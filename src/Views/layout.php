<!DOCTYPE html>
<html lang="en">

<head>
    <base href="/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Render additional styles -->
    <link rel="stylesheet" type="text/css" href="<?php asset('app.css') ?>">
    <?php renderStyles($data['styles'] ?? []); ?>
    <!-- Render additional scripts -->
    <script type="text/javascript" src="<?php asset('app.js') ?>"></script>
    <?php renderScripts($data['scripts'] ?? []); ?>
    <title>
        <?php echo $data['title'] ?? 'Title'; ?>
    </title>
    <!-- Render additional head data -->
    <?php renderHead($data['headHTML'] ?? '') ?>
</head>

<body>
    <div class="main-container">
        <div class="navbar">
            <a class="navbar-item" href="/">Home</a>
            <?php
            if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
                echo '<a class="navbar-item" href="/blog/page/1">Blog</a>';
            }
            ?>
            <div class="dropdown">
                <button class="navbutton">Dropdown</button>
                <div class="dropdown-content">
                    <a href="#">Link 1</a>
                    <a href="#">Link 2</a>
                    <button>Button</button>
                </div>
            </div>
            <div class="navbar-right">
                <button class="navbutton" onclick="toggleDarkMode()">Toggle Dark Mode</button>
                <?php if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
                    echo '<div class="dropdown">';
                    echo '<button class="navbutton">Profile</button>';
                    echo '<div class="dropdown-content">';
                    echo '<form action="/logout" method="post">';
                    HiddenCSRF();
                    echo '<input type="submit" value="Logout" />';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                } else {
                    echo '<a class="navbar-item" href="/login">Sign in</a>';
                    echo '<a class="navbar-item" href="/register">Sign up</a>';
                } ?>
            </div>
        </div>
        <div class="page-content">
            <?php renderBody($data['view'], $data['additionalViewData'] ?? []); ?>
        </div>
        <div class="footer-content">
            <p>Copyright © 2024 Łukasz Bulicz</p>
        </div>
    </div>
    <script>
        // Apply current dark-mode state after base content is generated
        applyDarkModeState();
    </script>
</body>

</html>