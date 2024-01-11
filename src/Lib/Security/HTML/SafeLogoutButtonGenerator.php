<?php
namespace App\Lib\Security\HTML;

class SafeLogoutButtonGenerator
{
    public static function generate()
    {
        if ($_SESSION['user']) {
            echo '<form action="/logout" method="post">';
            HiddenCSRF();
            echo '<input type="submit" value="logout" />';
            echo '</form>';
        }
    }
}
?>