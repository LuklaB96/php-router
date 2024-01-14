import ErrorHandler from './ErrorHandler/errorHandler.js';
import MessageHandler from './MessageHandler/messageHandler.js';

const messageHandler = new MessageHandler('messageContainer');
const errorHandler = new ErrorHandler('errorContainer');
window.displayErrors = (errors) => {
    errorHandler.addErrors(errors);
    errorHandler.displayErrors();
}
window.displayMessages = (messages) => {
    messageHandler.addMessages(messages);
    messageHandler.displayMessages();
}
