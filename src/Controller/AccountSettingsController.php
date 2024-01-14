<?php
namespace App\Controller;

use App\Entity\User;
use App\Lib\Controller\BaseController;
use App\Lib\View\View;

class AccountSettingsController extends BaseController
{
    /**
     * [GET] /account/settings
     * @return void
     */
    public function profileSettingsGET()
    {
        if (!$this->authUser()) {
            $this->redirectToRoute('/');
            return;
        }
        $data = [];
        if (isset($_SESSION['successMessage']) && !empty($_SESSION['successMessage'])) {
            $data['successMessage'] = $_SESSION['successMessage'];
            unset($_SESSION['successMessage']);
        }
        if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
            $data['errors'] = $_SESSION['errors'];
            unset($_SESSION['errors']);
        }
        $view = new View('User/Settings/ProfileSettingsPartialView', 'Profile Settings');
        $view->addStyle('account/settings.css');
        $view->addScript('app_pass_recovery.js', 'module');
        $view->renderPartial($data);
    }
    public function changePasswordPOST()
    {
        if (!$this->authUser()) {
            $this->redirectToRoute('/');
            return;
        }
        $data = $this->request->getData();
        $errors = [];
        if (empty($data)) {
            $errors[] = 'Fields are empty';
        } else {
            $user = new User();
            $userId = $_SESSION['user'];
            if (!$user->find($userId)) {
                $errors[] = 'User data corrupted, contant administrator!';
                $_SESSION['errors'] = $errors;
                $this->redirectToRoute('/account/settings');
                return;
            }
            $oldPassword = $data['oldPassword'];
            if (!password_verify($oldPassword, $user->getPassword())) {
                $errors[] = 'Old password is wrong';
                $_SESSION['errors'] = $errors;
                $this->redirectToRoute('/account/settings');
                return;
            }
            $password = $data['password'];
            $repeatPassword = $data['repeatPassword'];
            if ($password !== $repeatPassword) {
                $errors[] = 'Passwords do not match';
                $_SESSION['errors'] = $errors;
                $this->redirectToRoute('/account/settings');
                return;
            }
            $passwordValidationErrors = $this->validatePassword($password);
            if (!empty($passwordValidationErrors)) {
                $errors = array_merge($errors, $passwordValidationErrors);
                $_SESSION['errors'] = $errors;
                $this->redirectToRoute('/account/settings');
                return;
            }
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $user->setPassword($passwordHash);
            $user->update();
            $_SESSION['successMessage'] = 'Password has been changed.';
        }
        $_SESSION['errors'] = $errors;
        $this->redirectToRoute('/account/settings');
        return;

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

}
?>