<?php
namespace App\Lib\Security\CSRF;

use App\Lib\Config;

class CsrfToken
{
    protected string $token;
    protected int $tokenCreationTime;
    protected int $tokenLifeTime;
    const SESSION_NAMESPACE_TOKEN = 'csrf_token';
    const SESSION_NAMESPACE_TOKEN_CREATION_TIME = 'csrf_token_creation_time';
    public function __construct(#[\SensitiveParameter] string $token, int $tokenCreationTime = 0)
    {
        $this->token = $token;
        if ($tokenCreationTime == 0) {
            $this->tokenCreationTime = time();
        } else {
            $this->tokenCreationTime = $tokenCreationTime;
        }
        //get token lifetime from config, default is 60 minutes.
        $this->tokenLifeTime = Config::get('CSRF_TOKEN_LIFETIME', 60 * 60);
    }
    /**
     * Get current token
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
    /**
     * Check if token lifetime is greater than current time
     * @return bool
     */
    public function isAlive(): bool
    {
        return $this->tokenCreationTime + $this->tokenLifeTime >= time();
    }
    /**
     * Save current token informations to $_SESSION storage
     * @return void
     */
    public function save()
    {
        $_SESSION[self::SESSION_NAMESPACE_TOKEN] = $this->token;
        $_SESSION[self::SESSION_NAMESPACE_TOKEN_CREATION_TIME] = $this->tokenCreationTime;
    }

}
?>