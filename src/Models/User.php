<?php
namespace App\Models;

use PDO;

class User{
    private $db;

    public function __construct(){
        $config = require __DIR__ . '/../config/config.php';
        $dbConfig = $config['db'];

        $this->db = new PDO($dbConfig['driver'] . ':' . $dbConfig['database']);

        $this->criarTabela();
    }

    private function criarTabela(){
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            senha TEXT NOT NULL,
            ativacao TEXT,
            ativo INTEGER DEFAULT 0,
            criado DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);
    }

    public function criarconta($nome, $email, $senha, $ativacao){
        $stmt = $this->db->prepare('INSERT INTO users (nome, email, senha, ativacao) VALUES (:nome, :email, :senha, :ativacao)');
        return $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':senha' => password_hash($senha, PASSWORD_DEFAULT),
            ':ativacao' => $ativacao
        ]);
    }

    public function buscaEmail($email){
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function ativar($ativacao){
        $stmt = $this->db->prepare('UPDATE users SET ativo = 1, ativacao = NULL WHERE ativacao = :ativacao');
        return $stmt->execute([':ativacao' => $ativacao]);
    }
}
