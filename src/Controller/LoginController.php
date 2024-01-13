<?php
namespace App\Controller;

use App\Entity\User;
use App\Lib\Controller\BaseController;
use App\Lib\View\View;

class LoginController extends BaseController
{
    private User $user;
    private array $expectedArrayKeys;
    private array $error = [];

    public function __construct()
    {
        $this->user = new User();
        parent::__construct();
        $this->expectedArrayKeys = array('login', 'password');
    }

    /**
     * [POST] Receive and validate login data sent by user
     * @return void
     */
    public function loginPOST()
    {
        // validate user login data

        // csrf validation
        // if (!$this->csrfAuth()) {
        //     $this->postResponse(401, 'Unauthorized');
        //     return;
        // }

        // validate data
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

        $loginData = [
            'login' => $data['login'],
            'password' => $data['password']
        ];

        $authenticated = $this->authenticateUser($loginData);
        if (!$authenticated) {
            $this->postResponse(401, $this->error);
            return;
        }

        // send response
        $this->postResponse(200, 'ok');
    }

    /**
     * [GET] Return login view page
     * @return void
     */
    public function loginGET()
    {
        if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
            $this->redirectToRoute('/');
            return;
        }

        //create partial view
        $view = new View('Login/LoginViewPartial', 'Sign In');
        //add styles and scripts
        $view->addScript('app_login.js', 'module');
        $view->addStyle('login/login.css');

        $view->renderPartial();
    }

    private function validate(array $data)
    {
        // validate if required data is present
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

            if ($key === 'login') {
                $valid = $this->validateLoginString($data[$key]) ? $valid : false;
                continue;
            }
        }

        return $valid;
    }

    private function validateLoginString(string $login): bool
    {
        if (!preg_match('/^[A-Za-z][A-Za-z0-9]{5,31}$/', $login)) {
            $this->error['login']['reasons'][] = 'Login should start with a letter, contain only letters and numbers, and be between 6 and 32 characters';
            return false;
        }

        return true;
    }

    private function authenticateUser(array $loginData): bool
    {
        $login = $loginData['login'];
        $this->user->findOneBy(['login', '=', $login]);
        if ($this->user->exists()) {
            //user exists, check password
            $password = $loginData['password'];
            $hashedPassword = $this->user->getPassword();
            $verified = password_verify($password, $hashedPassword);
            if ($verified) {
                if ($this->user->getActivated()) {
                    $_SESSION['user'] = $this->user->getId();
                    return true;
                }
                $this->error['authentication']['reasons'][] = 'Account is not activated yet, check email for activation link';
                return false;
            }
        }
        $this->error['authentication']['reasons'][] = 'Invalid login or password';
        return false;
    }
}
?>