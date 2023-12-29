<?php
namespace App\Lib\Security\CSRF;

class TokenGenerator
{
    private int $entropy;
    public function __construct(int $entropy = 256)
    {
        if ($entropy <= 7) {
            throw new \InvalidArgumentException('Entropy should be greater than 7');
        }

        $this->entropy = $entropy;
    }
    /**
     * Generate random uri safe csrf token
     * @return string
     */
    public function generate(): string
    {
        $bytes = random_bytes(intdiv($this->entropy, 8));
        $token = rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
        return $token;
    }
}
?>