<h1>Sign In</h1>
<div class="sign-in-container">

    <div id="errorContainer" class="error-container"></div>
    <form id="loginForm">
        <div>
            <label for="login">Login:</label>
            <input class="input" type="text" id="login" name="login" value="">
            <span id="loginError" class="input-error"></span>
        </div>

        <div>
            <label for="password">Password:</label>
            <input class="input" type="password" id="password" name="password" value="">
            <span id="passwordError" class="input-error"></span>
        </div>
        <div>
            <button type="button" class="btn btn-primary w-100" onclick="window.handleLogin()">Login</button>
        </div>
        <div class="redirect-link">
            <p>Need account? <a class="basic-link" href="/register">Sign up here</a></p>
            <p>Forgot password? <a class="basic-link" href="/account/recovery">Recover it.</a></p>
        </div>
    </form>

</div>
<!-- Modal HTML -->
<div id="successModal" class="modal">
    <div class="modal-content">
        <!-- Modal content -->
    </div>
    <div id="modalButtons" class="modal-buttons">
        <!-- The button will be appended here dynamically -->
    </div>
</div>

<!-- Overlay HTML -->
<div id="overlay" class="overlay"></div>