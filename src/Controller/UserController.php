<?php
namespace App\Controller;

use App\Entity\ActivationCode;
use App\Entity\User;
use App\Lib\Controller\BaseController;
use App\Lib\Database\Entity\Entity;
use App\Lib\Mail\MailSender;
use App\Lib\Security\CSRF\TokenGenerator;
use App\Lib\View\View;

class UserController extends BaseController
{
    /**
     * [GET] /account/activate/{code}
     * No Veryfication needed
     * @return void
     */
    private array $error;
    private MailSender $mailSender;
    public function __construct()
    {
        $this->mailSender = new MailSender();
        parent::__construct();
    }
    public function activateGET($code)
    {
        $message = 'Failed to activate account, contact administrator.';
        $activation = new ActivationCode();
        if ($activation->findOneBy(['activation_code', '=', $code])) {
            $user = $activation->getUser();
            if (!$user->getActivated() && $activation->getUsed() === false) {
                $user->setActivated(true);
                $activated = $user->update();
                if ($activated) {
                    $message = 'Your account is now activacted, <a class="basic-link" href="/login">sign in here</a>';
                    $activation->setUsed(true);
                    $activation->update();
                }
            } else {
                $message = 'Your account is already activated, <a class="basic-link" href="/login">sign in here</a>';
            }

        }
        $view = new View('User/ActivationPartialView', 'Account - Activation info');
        $view->addStyle('error.css');
        $view->renderPartial(['message' => $message]);
    }
    public function showProfile($userLogin)
    {
        $user = new User();
        $user->findOneBy(['login', '=', $userLogin]);
        if ($user->exists()) {
            $data['user'] = $user;
            $view = new View('User/UserProfilePartialView', $user->getLogin() . ' - Profile');
            $view->addStyle('blog/post.css');
            $view->renderPartial($data);
        }
    }
    /**
     * [GET] /user/recovery
     * @return void
     */
    public function forgotPasswordGET()
    {
        if ($this->csrfAuth()) {
            $this->redirectToRoute('/');
            return;
        }
        $view = new View('User/ForgotPasswordPartialView', 'Password Recovery');
        $view->addStyle('account/recovery.css');
        $view->addScript('app_pass_recovery.js', 'module');
        if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
            $data['errors'] = $_SESSION['errors'];
            $view->renderPartial($data);
            unset($_SESSION['errors']);
            return;
        }

        $view->renderPartial();
    }
    /**
     * [POST] /user/recovery
     * @return void
     */
    public function forgotPasswordPOST()
    {
        $data = $this->request->getData();
        $errors = [];
        if (empty($data)) {
            $errors[] = 'Fields are empty';
        } else {
            $login = $data['login'] ?? '';
            $user = $this->findUser($login);
            if (isset($user)) {
                $this->passwordRecoverySend($user);
                return;
            } else {
                $errors[] = 'Invalid Email or Login';
            }
        }
        $_SESSION['errors'] = $errors;
        $this->redirectToRoute('/account/recovery');

    }
    /**
     * [GET] 
     * @param \App\Entity\User $user
     * @return void
     */
    public function passwordRecoverySend(User $user)
    {
        $tokenGen = new TokenGenerator(1024);
        $recoveryToken = $tokenGen->generate();
        $recoveryCode = new ActivationCode();

        $recoveryCode->setActivationCode($recoveryToken);
        $recoveryCode->setUser($user);
        $recoveryCode->setUsed(false);
        $message = 'Password recovery sent to email: ' . $user->getEmail();
        if ($recoveryCode->insert()) {
            $activationUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/account/recovery/' . $recoveryCode->getActivationCode();
            $content = <<<EmailMessage
                Password recovery link: $activationUrl
            EmailMessage;
            $userEmail = $user->getEmail();
            $subject = 'Password recovery for ' . $user->getLogin();
            try {
                $this->mailSender->sendMail($userEmail, $subject, $content);
            } catch (\Exception $e) {
                $message = 'Server could not send email for unknown reason, click <a class="basic-link" href="' . $activationUrl . '">here to activate.</a>';
            }

        }
        $view = new View('User/PasswordRecoveryPartialView', 'Password Recovery');
        $view->addStyle('error.css');
        $view->renderPartial(['message' => $message]);
    }
    public function passwordRecoveryFormGET($code)
    {
        $message = 'Invalid password recovery link';
        $activation = new ActivationCode();
        if ($activation->findOneBy(['activation_code', '=', $code])) {
            if ($activation->getUsed() === false) {
                $data['user'] = $activation->getUser();
                $data['code'] = $code;
                $view = new View('User/PasswordRecoveryFormPartialView', 'Account - Activation info');
                if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
                    $data['errors'] = $_SESSION['errors'];
                    $view->addScript('app_pass_recovery.js', 'module');
                    unset($_SESSION['errors']);
                }
                $view->addStyle('account/recovery.css');
                $view->addStyle('error.css');

                $view->renderPartial($data);
                return;
            }
            $view = new View('User/ActivationPartialView', 'Account - Activation info');
            $view->addStyle('error.css');
            $view->renderPartial(['message' => $message]);
            return;
        } else {
            $view = new View('User/ActivationPartialView', 'Account - Activation info');
            $view->addStyle('error.css');
            $view->renderPartial(['message' => $message]);
            return;
        }
    }
    /**
     * [POST] /account/recovery/submit
     * @return void
     */
    public function passwordRecoveryFormPOST()
    {
        $data = $this->request->getData();
        if (empty($data)) {
            $this->redirectToRoute('/');
            return;
        }
        $code = $data['code'];
        $activation = new ActivationCode();
        if ($activation->findOneBy(['activation_code', '=', $code])) {
            $password = $data['password'];
            $repeatPassword = $data['repeatPassword'];
            $errors = [];
            if ($password !== $repeatPassword) {
                $errors[] = "Passwords don't match";
            }
            $passwordRuleErrors = $this->validatePassword($password);
            if (!empty($passwordRuleErrors)) {
                $errors = array_merge($errors, $passwordRuleErrors);
            }
            $valid = (empty($errors) && !$activation->getUsed());
            if ($valid) {
                $user = $activation->getUser();
                $passwordHash = $this->hashPassword($password);
                $user->setPassword($passwordHash);
                $user->update();
                $activation->setUsed(true);
                $activation->update();
                $message = 'Password changed successfully, you can <a class="basic-link" href="/login">sign in</a> now!';
                $view = new View('User/PasswordRecoveryPartialView', 'Password Recovery - Success');
                $view->addStyle('error.css');
                $view->renderPartial(['message' => $message]);
                return;
            }
            $_SESSION['errors'] = $errors;
            $route = '/account/recovery/' . $code;
            $this->redirectToRoute($route);
            return;
        } else {
            $this->redirectToRoute('/');
            return;
        }
    }
    private function findUser($login): ?User
    {
        $user = new User();
        $found = $user->findOneBy(['login', '=', $login]);
        if ($found) {
            return $user;
        }
        $found = $user->findOneBy(['email', '=', $login]);
        if ($found) {
            return $user;
        }
        return null;

    }
    private function validatePassword(string $password): array
    {
        $rules = [
            [
                'pattern' => '/^.{8,}$/',
                'message' => 'Password should be at least 8 characters'
            ],
            [
                'pattern' => '/[a-z]/',
                'message' => 'Password should contain at least one lowercase letter'
            ],
            [
                'pattern' => '/[A-Z]/',
                'message' => 'Password should contain at least one uppercase letter'
            ],
            [
                'pattern' => '/\d/',
                'message' => 'Password should contain at least one digit'
            ],
            [
                'pattern' => '/[!@#$%^&*()_+\-=[\]{};:\'\\|,.<>\/?]/',
                'message' => 'Password should contain at least one special character'
            ]
        ];
        $errors = [];
        foreach ($rules as $rule) {
            if (!preg_match($rule['pattern'], $password)) {
                $errors[] = $rule['message'];
            }
        }
        return $errors;
    }
    private function hashPassword(string $password): string
    {
        // Generate a secure password hash using the bcrypt algorithm
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if ($hashedPassword === false) {
            throw new \Exception('Password hashing failed.');
        }

        return $hashedPassword;
    }
}