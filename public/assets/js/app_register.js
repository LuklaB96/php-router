import RegisterUser from './Register/register.js';
import Modal from './Modal/Modal.js';
const register = new RegisterUser('successModal', 'errorContainer');
window.handleRegistration = () => {
    register.handleRegistration();
}