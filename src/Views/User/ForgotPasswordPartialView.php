<h1>Password Recovery</h1>
<div class="pass-recovery-container">
    <div id="errorContainer" class="error-container"></div>
    <form id="passRecoveryForm" method="POST" action="/account/recovery">
        <div>
            <label for="login">Login or Email:</label>
            <input class="input" type="text" id="login" name="login" value="">
            <span id="loginError" class="input-error"></span>
        </div>
        <div>
            <input type="submit" class="btn btn-primary w-100" value="Recover"></input>
        </div>
    </form>

</div>
<?php

if (isset($data['errors']) && !empty($data['errors'])) {
    echo '<script>';
    echo 'let errors = [];';
    foreach ($data['errors'] as $error) {
        echo 'errors.push("' . $error . '");';
    }
    echo 'document.addEventListener("DOMContentLoaded", function () {';
    echo 'window.displayErrors(errors);';
    echo '});';
    echo '</script>';
}
?>