<?php
namespace App\Lib\Mail;

use App\Lib\Config;

class MailSender
{
    public function sendMail($to, $subject, $message): bool
    {
        $from = Config::get('EMAIL_SENDER');
        // email header
        $header = "From:" . $from;
        // send the email
        $sent = mail($to, $subject, nl2br($message), $header);
        return $sent;
    }
}

?>
