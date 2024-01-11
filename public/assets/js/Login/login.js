// Import the Modal class
import Modal from '../Modal/Modal.js';
import ErrorHandler from '../ErrorHandler/ErrorHandler.js';

class LoginUser {
    constructor(modalId, errorContainerId) {
        this.errorContainer = document.getElementById(errorContainerId);
        this.successModal = new Modal(modalId);
        this.errorHandler = new ErrorHandler(errorContainerId);

        this.loginError = document.getElementById('loginError');
        this.passwordError = document.getElementById('passwordError');

        this.loginInput = document.getElementById('login');
        this.passwordInput = document.getElementById('password');

        this.inputErrorMessage = 'Required';

        this.hookInputChecks(this.loginInput, this.loginError, this.inputErrorMessage);
        this.hookInputChecks(this.passwordInput, this.passwordError, this.inputErrorMessage);
    }

    showSuccessModal(userName) {
        const successMessage = `User "${userName}" successfully logged in.`;
        this.successModal.clear();
        this.successModal.setContent(successMessage);

        // Add an "OK" button that closes the modal
        this.successModal.addButton('OK', () => {
            this.successModal.hide();
        }, ['btn', 'btn-primary']);

        // Show the modal
        this.successModal.show();
    }

    handleLogin() {
        this.errorHandler.clearErrors();

        const login = this.getInputValue('login');
        const password = this.getInputValue('password');

        const valid = this.validateLoginForm(login, password);

        if (valid) {
            const loginData = this.buildLoginData(login, password);
            this.submitLogin(loginData);
        }
    }
    hookInputChecks(input, errorOutputHTMLElement, message) {
        input.addEventListener('input', function () {
            if (input.value === '') {
                errorOutputHTMLElement.textContent = message;
            } else {
                errorOutputHTMLElement.textContent = '';
            }
        });
    }

    getInputValue(inputId) {
        return document.getElementById(inputId).value.trim();
    }

    validateLoginForm(login, password) {

        loginError.textContent = '';
        passwordError.textContent = '';

        // check if inputs are empty, and set content for error HTML element
        let validFields = true;
        validFields = !this.checkEmptyInput(login, this.loginError, this.inputErrorMessage) ? false : validFields;
        validFields = !this.checkEmptyInput(password, this.passwordError, this.inputErrorMessage) ? false : validFields;

        return validFields;
    }

    checkEmptyInput(input, errorOutputHTMLElement, message) {
        if (!this.inputHasValue(input)) {
            this.setHTMLInputMessage(errorOutputHTMLElement, message);
            return false;
        }
        return true;
    }


    buildLoginData(login, password) {
        return {
            login,
            password
        };
    }

    submitLogin(loginData) {
        // Replace this with your actual login API endpoint
        fetch('/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(loginData)
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        this.handleError(data);
                        throw new Error(JSON.stringify(data));
                    });
                }
                return response.json();
            })
            .then(data => {
                this.handleSuccessResponse(data, loginData.login);
            })
            .catch(error => {
                this.handleError(error);
            });
    }

    handleSuccessResponse(data, user) {
        // Show success modal
        window.location.href = '/';
        // Handle success response here if needed
    }

    handleError(error) {
        this.errorHandler.processErrorResponse(error);
        this.errorHandler.displayErrors();
    }

    inputHasValue(value) {
        return value !== '';
    }

    setHTMLInputMessage(HTMLElement, message) {
        HTMLElement.textContent = message;
    }
}

export default LoginUser;
