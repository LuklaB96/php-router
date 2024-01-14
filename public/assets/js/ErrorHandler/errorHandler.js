class ErrorHandler {
    constructor(containerId) {
        this.errorMessages = [];
        this.errorContainer = document.getElementById(containerId);
    }

    addFieldError(field, reason) {
        this.errorMessages.push(`${field.charAt(0).toUpperCase() + field.slice(1)}: ${reason}`);
    }
    addError(errorMessage) {
        this.errorMessages.push(errorMessage);
    }
    addErrors(errors) {
        console.log('asd');
        errors.forEach(error => {
            if (typeof error === 'string') {
                this.addError(error);
            } else if (typeof error === 'object' && error.field && error.reason) {
                this.addFieldError(error.field, error.reason);
            } else {
                console.error('Invalid error format:', error);
            }
        });
    }
    hideErrorContainer() {
        this.errorContainer.style.display = 'none';
    }
    showErrorContainer() {
        this.errorContainer.style.display = 'flex';
    }
    displayErrors() {
        if (!this.errorContainer) {
            return;
        }

        this.errorContainer.innerHTML = '';


        if (this.errorMessages.length > 0) {
            // Add close button
            const closeButton = document.createElement('span');
            closeButton.className = 'btn-close-container';
            closeButton.innerHTML = '&times;'; // '×' character for close icon
            closeButton.addEventListener('click', () => this.clearErrors());

            const errorList = document.createElement('ul');

            this.errorMessages.forEach(message => {
                const errorItem = document.createElement('li');
                errorItem.textContent = message;
                errorList.appendChild(errorItem);
            });
            this.errorContainer.appendChild(closeButton);
            this.errorContainer.appendChild(errorList);
            this.showErrorContainer();
        }
    }

    clearErrors() {
        this.errorMessages = [];
        this.errorContainer.innerHTML = '';
        this.hideErrorContainer();
    }

    processErrorResponse(data) {
        if (data.code >= 400 && data.data) {
            Object.keys(data.data).forEach(field => {
                data.data[field].reasons.forEach(reason => {
                    this.addFieldError(field, reason);
                });
            });
        }
    }
}
export default ErrorHandler;