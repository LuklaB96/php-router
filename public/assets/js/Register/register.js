// Import the Modal class
import Modal from '../Modal/Modal.js';
import ErrorHandler from '../ErrorHandler/errorHandler.js';

class RegisterUser {
    constructor(modalId, errorContainerId) {
        this.errorContainer = document.getElementById('errorContainer');
        this.successModal = new Modal(modalId);
        this.errorHandler = new ErrorHandler(errorContainerId);

        this.loginError = document.getElementById('loginError');
        this.passwordError = document.getElementById('passwordError');
        this.repeatPasswordError = document.getElementById('repeatPasswordError');
        this.emailError = document.getElementById('emailError');

        this.loginInput = document.getElementById('login');
        this.passwordInput = document.getElementById('password');
        this.repeatPasswordInput = document.getElementById('repeatPassword');
        this.emailInput = document.getElementById('email');

        this.inputErrorMessage = 'Required';

        this.hookInputChecks(this.loginInput, this.loginError, this.inputErrorMessage);
        this.hookInputChecks(this.passwordInput, this.passwordError, this.inputErrorMessage);
        this.hookInputChecks(this.repeatPasswordInput, this.repeatPasswordError, this.inputErrorMessage);
        this.hookInputChecks(this.emailInput, this.emailError, this.inputErrorMessage);

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
    showSuccessModal(userName, data) {
        const successMessage = `
            User "${userName}" successfully registered.</br>
            ${data.data.message}
        `;
        this.successModal.clear();
        this.successModal.setContent(successMessage);

        // Add an "OK" button that redirects to the main page
        this.successModal.addButton('OK', () => {
            //window.location.href = '/'; // Replace with your main page URL
            this.successModal.hide();
        }, ['btn', 'btn-primary']);

        // Show the modal
        this.successModal.show();
    }
    handleRegistration() {
        this.errorHandler.clearErrors();

        const login = this.getInputValue('login');
        const password = this.getInputValue('password');
        const repeatPassword = this.getInputValue('repeatPassword');
        const email = this.getInputValue('email');

        const [validationErrors, valid] = this.validateForm(login, password, repeatPassword, email);
        if (validationErrors.length === 0 && valid) {
            const registrationData = this.buildRegistrationData(login, password, email);

            this.submitRegistration(registrationData);
        } else {
            this.errorHandler.addErrors(validationErrors);
            this.errorHandler.displayErrors();
        }

    }

    getInputValue(inputId) {
        return document.getElementById(inputId).value.trim();
    }

    validateForm(login, password, repeatPassword, email) {
        this.errorHandler.hideErrorContainer();
        const errors = [];


        // check if inputs are empty, and set content for error HTML element
        let validFields = true;
        validFields = !this.checkEmptyInput(login, this.loginError, this.inputErrorMessage) ? false : validFields;
        validFields = !this.checkEmptyInput(password, this.passwordError, this.inputErrorMessage) ? false : validFields;
        validFields = !this.checkEmptyInput(repeatPassword, this.repeatPasswordError, this.inputErrorMessage) ? false : validFields;
        validFields = !this.checkEmptyInput(email, this.emailError, this.inputErrorMessage) ? false : validFields;

        this.validatePassword(password, errors);
        this.validateRepeatPassword(password, repeatPassword, errors);
        this.validateEmail(email, errors);

        return [errors, validFields];
    }

    inputHasValue(value) {
        return value !== '';
    }
    setHTMLInputMessage(HTMLElement, message) {
        HTMLElement.textContent = message;
    }
    checkEmptyInput(input, errorOutputHTMLElement, message) {
        if (!this.inputHasValue(input)) {
            this.setHTMLInputMessage(errorOutputHTMLElement, message);
            return false;
        }
        return true;
    }

    validatePassword(password, errors) {
        // Password validation rules
        let passwordErrors = [];
        if (!password) {
            return;
        }
        if (password.length < 8) {
            errors.push('Password should be at least 8 characters');
        }
        if (!/[a-z]/.test(password)) {
            passwordErrors.push('one lowercase letter');
        }
        if (!/[A-Z]/.test(password)) {
            passwordErrors.push('one uppercase letter');
        }
        if (!/\d/.test(password)) {
            passwordErrors.push('one digit');
        }
        if (!/[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]/.test(password)) {
            passwordErrors.push('one special character');
        }
        if (passwordErrors.length > 0) {
            let errorMessage = 'Password should contain at least ';
            passwordErrors.forEach((error, index) => {
                if (typeof error === 'string') {
                    errorMessage = errorMessage + error;
                    if (index < passwordErrors.length - 1) {
                        if (index === passwordErrors.length - 2) {
                            errorMessage = errorMessage + ' and ';
                        } else {
                            errorMessage = errorMessage + ', ';
                        }
                    }
                }
            });

            errors.push(errorMessage);

        }
    }

    validateRepeatPassword(password, repeatPassword, errors) {
        if (!repeatPassword) {
            return;
        } if (password !== repeatPassword) {
            errors.push('Passwords do not match');
        }

    }

    validateEmail(email, errors) {
        if (!email) {
            return;
        }
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            errors.push('Please enter a valid email address');
        }
    }

    buildRegistrationData(login, password, email) {
        return {
            login,
            password,
            email
        };
    }

    submitRegistration(registrationData) {
        fetch('/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(registrationData)
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        this.handleError(data);
                        this.displayErrors(errorMessages);
                        throw new Error(JSON.stringify(data));
                    });
                }
                return response.json();
            })
            .then(data => {
                this.handleSuccessResponse(data, registrationData.login);
            })
            .catch(error => {
                this.handleError(error);
            });
    }

    handleSuccessResponse(data, user) {
        // Show success modal
        this.showSuccessModal(user, data);
        // Handle success response here if needed
    }

    handleError(error) {
        this.errorHandler.processErrorResponse(error);
        this.errorHandler.displayErrors();
    }
}
export default RegisterUser;