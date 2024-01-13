<h1>Sign Up</h1>
<div class="sign-up-container">
    <div id="errorContainer" class="error-container"></div>
    <form id="registrationForm">
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
            <label for="repeatPassword">Repeat Password:</label>
            <input class="input" type="password" id="repeatPassword" name="repeatPassword" value="">
            <span id="repeatPasswordError" class="input-error"></span>
        </div>

        <div>
            <label for="email">Email:</label>
            <input class="input" type="email" id="email" name="email" value="">
            <span id="emailError" class="input-error"></span>
        </div>

        <div>
            <button type="button" class="btn btn-primary w-100" onclick="window.handleRegistration()">Register</button>
        </div>
        <div class="redirect-link">
            <p>Already have an account? <a class="basic-link" href="/login">Sign in here</a></p>
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