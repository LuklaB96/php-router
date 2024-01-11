class Modal {
    constructor(modalId) {
        this.modal = document.getElementById(modalId);
        this.overlay = document.getElementById('overlay');
        this.contentElement = this.modal.querySelector('.modal-content');
        this.modalButtons = this.modal.querySelector('.modal-buttons');
    }

    setContent(content) {
        this.contentElement.innerHTML = content;
    }

    addButton(text, callback, styleClasses) {
        const button = document.createElement('button');
        button.textContent = text;
        button.addEventListener('click', callback);

        // Apply button style classes
        if (styleClasses && Array.isArray(styleClasses)) {
            button.classList.add(...styleClasses);
        }
        this.modalButtons.appendChild(button);
    }

    show() {
        this.modal.style.display = 'block';
        this.overlay.style.display = 'block';
    }

    hide() {
        this.modal.style.display = 'none';
        this.overlay.style.display = 'none';
    }
    clear() {
        this.contentElement.innerHTML = '';
        this.modalButtons.innerHTML = '';
    }
}
export default Modal;