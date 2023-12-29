<?php
namespace App\Lib\Security\CSRF;

/**
 * Use this for csrf token validation
 */

class SessionTokenManager
{
    private static $instance;
    private CsrfToken $csrfToken;
    private TokenGenerator $tokenGenerator;
    public function __construct()
    {
        $this->tokenGenerator = new TokenGenerator();
        $this->csrfToken = $this->startSession();
        $this->csrfToken->save();
    }
    public static function getInstance()
    {
        if (self::$instance !== null) {
            return self::$instance;
        }
        return self::$instance = new SessionTokenManager();
    }
    /**
     * Checks if php session is active
     * 
     * @return bool
     */
    private function isSessionAlive(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }
    /**
     * Start user session and retrive or generate new csrf token
     * 
     * @return \App\Lib\Security\CSRF\CsrfToken
     */
    private function startSession(): CsrfToken
    {
        if ($this->isSessionAlive()) {
            return $this->retriveTokenFromSession();
        }

        session_start();

        if (isset($_SESSION[CsrfToken::SESSION_NAMESPACE_TOKEN], $_SESSION[CsrfToken::SESSION_NAMESPACE_TOKEN_CREATION_TIME])) {
            return $this->retriveTokenFromSession();
        }
        //if token is not present in session storage, create new.
        $token = $this->tokenGenerator->generate();
        return new CsrfToken($token);
    }
    /**
     * Retrive token and its creation time from $_SESSION storage
     * 
     * Validate if it exists in session before usage
     * 
     * @return \App\Lib\Security\CSRF\CsrfToken
     */
    private function retriveTokenFromSession(): CsrfToken
    {
        $token = $_SESSION[CsrfToken::SESSION_NAMESPACE_TOKEN];
        $tokenCreationTime = $_SESSION[CsrfToken::SESSION_NAMESPACE_TOKEN_CREATION_TIME];
        return new CsrfToken($token, $tokenCreationTime);
    }
    /**
     * Get token for current session.
     * @return string
     */
    public function getToken(): string
    {
        return $this->csrfToken->getToken();
    }
    /**
     * Validate csrf token
     * @param string $token
     * @return bool
     */
    public function validateToken(#[\SensitiveParameter] string $token): bool
    {
        $alive = $this->csrfToken->isAlive();
        return ($token === $this->getToken() && $alive);
    }
}

?>