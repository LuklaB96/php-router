<?php
namespace App\Controller;

use App\Entity\ActivationCode;
use App\Entity\User;
use App\Lib\Controller\BaseController;
use App\Lib\Database\Entity\Entity;
use App\Lib\Mail\MailSender;
use App\Lib\Security\CSRF\TokenGenerator;
use App\Lib\View\View;

class RegisterController extends BaseController
{
    private User $user;
    private array $expectedArrayKeys;
    private array $error = [];
    private MailSender $mailSender;
    public function __construct()
    {
        $this->user = new User();
        parent::__construct();
        $this->expectedArrayKeys = array('login', 'password', 'email');
        $this->mailSender = new MailSender();
    }
    /**
     * [POST] Receive and validate register data sent by user
     * @return void
     */
    public function registerPOST()
    {
        //validate user register data

        //csrf validation
        // if (!$this->csrfAuth()) {
        //     $this->postResponse(401, 'Unauthorized');
        //     return;
        // }

        //validate data
        $data = $this->request->getData();
        if (empty($data)) {
            $this->error['message'] = 'empty data';
            $this->postResponse(400, $this->error);
            return;
        }
        $valid = $this->validate($data);

        if (!$valid) {
            $this->postResponse(400, $this->error);
            return;
        }
        $user = new User();
        $user->setLogin($data['login']);
        $user->setEmail($data['email']);
        $hashPassword = $this->hashPassword($data['password']);
        $user->setPassword($hashPassword);
        $user->setActivated(false);

        $created = $this->registerUser($user);

        if (!$created) {
            $this->postResponse(400, $this->error);
            return;
        }

        //handle sending activation token to user email
        $tokenGen = new TokenGenerator(1024);
        $activationToken = $tokenGen->generate();
        $emailActivation = new ActivationCode();

        $emailActivation->setActivationCode($activationToken);
        $emailActivation->setUser($user);
        $emailActivation->setUsed(false);
        if ($emailActivation->insert()) {
            $activationUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/account/activate/' . $emailActivation->getActivationCode();
            $content = <<<EmailMessage
                Activation link: $activationUrl
            EmailMessage;
            $userEmail = $user->getEmail();
            $subject = 'Activation code for ' . $user->getLogin();
            $message = 'Activation link sent to: ' . $user->getEmail();
            try {
                $this->mailSender->sendMail($userEmail, $subject, $content);
            } catch (\Exception $e) {
                $message = 'Server could not send email for unknown reason, click <a class="basic-link" href="' . $activationUrl . '">here to activate.</a>';
            }
        }


        //send response
        $this->postResponse(200, ['message' => $message]);
    }
    /**
     * [GET] Return register view page
     * @return void
     */
    public function registerGET()
    {
        if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
            $this->redirectToRoute('/');
            return;
        }
        //create partial view
        $view = new View('Register/RegisterViewPartial', 'Sign Up');
        //add styles and scripts
        $view->addScript('app_register.js', 'module');
        $view->addStyle('register/register.css');

        $view->renderPartial();
    }
    private function validate(array $data)
    {
        //validate if required data is present
        $valid = $this->validateData($data);
        return $valid;
    }
    private function validateData(array $data): bool
    {
        $arrayKeys = array_keys($data);
        $expectedKeys = $this->expectedArrayKeys;
        sort($arrayKeys);
        sort($expectedKeys);
        $valid = true;
        foreach ($expectedKeys as $key) {

            if (!in_array($key, $arrayKeys) || !isset($data[$key]) || empty($data[$key])) {
                $this->error[$key]['status'] = 'invalid';
                $this->error[$key]['reasons'][] = 'empty';
                $valid = false;
                continue;
            }
            if ($key === 'password') {
                $valid = $this->validatePassword($data[$key]) ? $valid : false;
                continue;
            }
            if ($key === 'email') {
                $valid = $this->validateEmail($data[$key]) ? $valid : false;
                continue;
            }
            if ($key === 'login') {
                $valid = $this->validateLogin($data[$key]) ? $valid : false;
                continue;
            }

        }
        return $valid;
    }
    private function validatePassword(string $password): bool
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

        $valid = true;

        foreach ($rules as $rule) {
            if (!preg_match($rule['pattern'], $password)) {
                $this->error['password']['reasons'][] = $rule['message'];
                $valid = false;
            }
        }

        return $valid;
    }
    private function validateEmail(string $email): bool
    {
        $valid = true;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error['email']['reasons'][] = 'Please enter a valid email address';
            $valid = false;
        }
        if (!$this->isEmailAvailable($email)) {
            $this->error['email']['reasons'][] = 'Email is already in use';
            $valid = false;
        }
        return $valid;
    }
    private function validateLogin(string $login): bool
    {
        if (!preg_match('/^[A-Za-z][A-Za-z0-9]{5,31}$/', $login)) {
            $this->error['login']['reasons'][] = 'Login should start with a letter, contain only letters and numbers, and be between 6 and 32 characters';
            return false;
        }

        return true;
    }
    private function isEmailAvailable(string $email): bool
    {
        $query = 'SELECT COUNT(*) AS count FROM `app_db`.`user` WHERE email = :email';
        $result = $this->db->execute($query, ['email' => $email]);

        // Check if the query was successful and the email is available
        if ($result && isset($result[0]['count']) && $result[0]['count'] === 0) {
            return true; // Email is available
        }

        return false; // Email is not available or an error occurred
    }
    private function registerUser(Entity $user): bool
    {
        $valid = true;
        if (!$user->validate()) {
            $this->error['user']['reasons'][] = 'Invalid user data';
            $valid = false; //User data does not meet model requirements
        } else {
            if (!$user->insert()) {
                $this->error['login']['reasons'][] = 'Login already in use';
                $valid = false; //User with this login already exists
            }
        }
        return $valid;
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
?>