<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $mailer;

    public function __construct()
    {
        $config = require __DIR__ . '/../config/config.php';
        $mailConfig = $config['mail'];

        $this->mailer = new PHPMailer(true);

        $this->mailer->isSMTP();
        $this->mailer->Host = $mailConfig['host'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $mailConfig['username'];
        $this->mailer->Password = $mailConfig['password'];
        $this->mailer->SMTPSecure = $mailConfig['encryption'];
        $this->mailer->Port = $mailConfig['port'];

        $this->mailer->setFrom($mailConfig['username'], 'rodrigo');
    }

    public function sendEmail($to, $subject, $body)
    {
        try {
            $this->mailer->addAddress($to);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            echo "Erro ao enviar e-mail: {$this->mailer->ErrorInfo}";
            return false;
        }
    }
}
