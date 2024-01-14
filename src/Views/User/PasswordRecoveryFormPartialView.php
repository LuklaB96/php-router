<h1>Password Recovery</h1>
<div class="pass-recovery-container">
    <div id="errorContainer" class="error-container"></div>
    <form id="passRecoveryForm" method="POST" action="/account/recovery/submit">
        <?php
        use App\Lib\Security\HTML\HiddenFieldGenerator;

        echo HiddenFieldGenerator::generate('code', $data['code']);
        ?>
        <div>
            <label for="login">New password: </label>
            <input class="input" type="password" id="password" name="password" value="">
            <span id="passwordError" class="input-error"></span>
        </div>
        <div>
            <label for="login">Repeat New password: </label>
            <input class="input" type="password" id="repeatPassword" name="repeatPassword" value="">
            <span id="passwordRepeatError" class="input-error"></span>
        </div>
        <div>
            <input type="submit" class="btn btn-primary w-100" value="Set new password"></input>
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
    echo 'window.display(errors);';
    echo '});';
    echo '</script>';
}
?>