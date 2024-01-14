<h1>Change password</h1>
<div class="pass-change-container">
    <div id="messageContainer" class="message-container"></div>
    <div id="errorContainer" class="error-container"></div>
    <form id="passChangeForm" method="POST" action="/account/password/change">
        <?php
        HiddenCSRF();
        ?>
        <div>
            <label for="login">Old password: </label>
            <input class="input" type="password" id="oldPassword" name="oldPassword" value="">
            <span id="oldPasswordError" class="input-error"></span>
        </div>
        <div>
            <label for="login">New password: </label>
            <input class="input" type="password" id="password" name="password" value="">
            <span id="passwordError" class="input-error"></span>
        </div>
        <div>
            <label for="login">Repeat new password: </label>
            <input class="input" type="password" id="repeatPassword" name="repeatPassword" value="">
            <span id="passwordRepeatError" class="input-error"></span>
        </div>
        <div>
            <input type="submit" class="btn btn-primary w-100" value="Set new password"></input>
        </div>
    </form>
</div>
<?php
echo '<script>';
echo 'let errors = [];';
echo 'let messages = [];';
if (isset($data['successMessage'])) {
    echo 'messages.push("' . $data['successMessage'] . '");';
}
if (isset($data['errors']) && !empty($data['errors'])) {
    foreach ($data['errors'] as $error) {
        echo 'errors.push("' . $error . '");';
    }
}
echo 'document.addEventListener("DOMContentLoaded", function () {';
echo 'window.displayErrors(errors);';
echo 'window.displayMessages(messages);';
echo '});';
echo '</script>';
?>