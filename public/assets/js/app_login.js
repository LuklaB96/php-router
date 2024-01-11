import LoginUser from './Login/login.js';
const login = new LoginUser('successModal', 'errorContainer');
window.handleLogin = () => {
    login.handleLogin();
}