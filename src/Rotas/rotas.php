<?php
namespace App\Rotas;

use App\Controllers\UserController;
class Rotas {
    public static function fastRotas(){
        return [
            'GET' => [
            ],
            'POST' => [
               '/registrar' => [UserController::class, 'registrar'],
               '/ativar' => [UserController::class, 'ativarconta'],
               '/login' => [UserController::class, 'login'],
               '/verificaToken' => [UserController::class, 'verificaToken'],
            ],
            'PUT' => [
            ],
            'DELETE' => [
            ],
        ];
    }
}

