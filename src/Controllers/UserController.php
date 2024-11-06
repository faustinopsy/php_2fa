<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\TwoFactorToken;
use App\Services\EmailService;

class UserController{

    public function registrar(){
        $data = json_decode(file_get_contents('php://input'), true);

        $nome = $data['nome'];
        $email = $data['email'];
        $senha = $data['senha'];

        $ativacao = rand(100000, 999999);

        $userModel = new User();
        $userModel->criarconta($nome, $email, $senha, $ativacao);

        
        $subject = "Seu código de ativação";
        $body = "Use este código para ativar sua conta: $ativacao";

        $emailService = new EmailService();
        $emailService->sendEmail($email, $subject, $body);

        echo json_encode(['status'=> true, 'message' => 'Registro bem-sucedido! Verifique seu e-mail para ativar a conta.']);
    }

    public function ativarconta(){
        $data = json_decode(file_get_contents('php://input'), true);

        $email = $data['email'];
        $ativacao = $data['ativacao'];

        $userModel = new User();
        $user = $userModel->buscaEmail($email);

        if ($user && $user['ativacao'] == $ativacao) {
            $userModel->ativar($ativacao);
            echo json_encode(['status'=> true, 'message' => 'Conta ativada com sucesso!']);
        } else {
            echo json_encode(['status'=> false, 'message' => 'Código de ativação inválido.']);
        }
    }
    public function login(){
        $ip = $_SERVER['REMOTE_ADDR'];
        $rateLimiter = new \App\Services\RateLimiter($ip);

        if (!$rateLimiter->permitido()) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $details = json_decode(file_get_contents("http://ip-api.com/json/{$ip}"));
            $message = "Você excedeu o limite de tentativas de login. Por favor, tente novamente mais tarde.\n";
            $message .= "IP: {$ip}\nNavegador: {$userAgent}\n";
            $message .= "Cidade: {$details->city}, Latitude: {$details->lat}, Longitude: {$details->lon}";

            echo json_encode(['status' => false, 'message' => $message]);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['email']) || !is_string($data['senha'] )) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'E-mail ou senha precisam ser informados']);
            return;
        }

        $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'E-mail inválido']);
            return;
        }

        $senha = trim($data['senha']);
        if (!is_string($senha) || empty($senha)) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Senha inválida']);
            return;
        }
        
        $userModel = new User();
        $user = $userModel->buscaEmail($email);

        if ($user && password_verify($senha, $user['senha']) && $user['ativo']) {
            $token = rand(100000, 999999);

            $twoFactorModel = new TwoFactorToken();
            $twoFactorModel->limpacodigo($user['id']);
            $twoFactorModel->criacodigo($user['id'], $token);

            $subject = "Seu código de login";
            $body = "Use este código para acessar sua conta: $token";

            $emailService = new EmailService();
            $emailService->sendEmail($user['email'], $subject, $body);

            echo json_encode(['status'=> true, 'message' => 'Código 2FA enviado para o seu e-mail.']);
        } else {
            echo json_encode(['status'=> false, 'message' => 'Credenciais inválidas ou conta não ativada.']);
        }
    }

    public function verificaToken(){
        $data = json_decode(file_get_contents('php://input'), true);

        $email = $data['email'];
        $token = $data['token'];

        $userModel = new User();
        $user = $userModel->buscaEmail($email);

        if ($user) {
            $twoFactorModel = new TwoFactorToken();
            $tokenvalido = $twoFactorModel->verificacodigo($user['id'], $token);

            if ($tokenvalido) {
                $twoFactorModel->limpacodigo($user['id']);
                echo json_encode(['status'=> true, 'message' => 'Login realizado com sucesso!']);
            } else {
                echo json_encode(['status'=> false, 'message' => 'Token inválido ou expirado.']);
            }
        } else {
            echo json_encode(['status'=> false, 'message' => 'Usuário não encontrado.']);
        }
    }
}
