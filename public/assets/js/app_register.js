import RegisterUser from './Register/register.js';
const register = new RegisterUser('successModal', 'errorContainer');
window.handleRegistration = () => {
    register.handleRegistration();
}