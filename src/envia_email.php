<?php
require '../vendor/autoload.php';

use App\Services\EmailService;

$emailService = new EmailService();
$emailService->sendEmail('rodrigohipnose@gmail.com', 'Teste de E-mail', 'Este é um e-mail de teste.');
