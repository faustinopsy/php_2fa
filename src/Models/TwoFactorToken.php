<?php

namespace App\Models;

use PDO;

class TwoFactorToken{
    private $db;

    public function __construct(){
        $config = require __DIR__ . '/../config/config.php';
        $dbConfig = $config['db'];
        $this->db = new PDO($dbConfig['driver'] . ':' . $dbConfig['database']);

        $this->criarTabela();
    }

    private function criarTabela(){
        $sql = "CREATE TABLE IF NOT EXISTS two_factor_tokens (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            token TEXT NOT NULL,
            expiracao DATETIME NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )";
        $this->db->exec($sql);
    }

    public function criacodigo($user_id, $token){
        $stmt = $this->db->prepare('INSERT INTO two_factor_tokens (user_id, token, expiracao) VALUES (:user_id, :token, :expiracao)');
        return $stmt->execute([
            ':user_id' => $user_id,
            ':token' => $token,
            ':expiracao' => date('Y-m-d H:i:s', strtotime('+10 minutes'))
        ]);
    }

    public function verificacodigo($user_id, $token){
        $stmt = $this->db->prepare('SELECT * FROM two_factor_tokens WHERE user_id = :user_id AND token = :token AND expiracao > datetime("now")');
        $stmt->execute([
            ':user_id' => $user_id,
            ':token' => $token
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function limpacodigo($user_id){
        $stmt = $this->db->prepare('DELETE FROM two_factor_tokens WHERE user_id = :user_id');
        $stmt->execute([':user_id' => $user_id]);
    }
}
