class MessageHandler {
    constructor(containerId) {
        this.messagesList = [];
        this.messageContainer = document.getElementById(containerId);
    }
    addMessage(message) {
        this.messagesList.push(message);
    }
    addMessages(messages) {
        messages.forEach(message => {
            if (typeof message === 'string') {
                this.addMessage(message);
            } else {
            }
        });
    }
    hideMessageContainer() {
        this.messageContainer.style.display = 'none';
    }
    showMessageContainer() {
        this.messageContainer.style.display = 'flex';
    }
    displayMessages() {
        if (!this.messageContainer) {
            return;
        }

        this.messageContainer.innerHTML = '';


        if (this.messagesList.length > 0) {
            // Add close button
            const closeButton = document.createElement('span');
            closeButton.className = 'btn-close-container';
            closeButton.innerHTML = '&times;'; // '×' character for close icon
            closeButton.addEventListener('click', () => this.clearMessages());

            const errorList = document.createElement('ul');

            this.messagesList.forEach(message => {
                const errorItem = document.createElement('li');
                errorItem.textContent = message;
                errorList.appendChild(errorItem);
            });
            this.messageContainer.appendChild(closeButton);
            this.messageContainer.appendChild(errorList);
            this.showMessageContainer();
        }
    }

    clearMessages() {
        this.messagesList = [];
        this.messageContainer.innerHTML = '';
        this.hideMessageContainer();
    }
}
export default MessageHandler;