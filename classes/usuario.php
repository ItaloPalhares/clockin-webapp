<?php

class Usuario {

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function cadastrarColaborador($nome, $email, $senha, $perfil = "colaborador"){

        $hashSenha = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users
                (nome, email, senha, perfil, ativo)
                VALUES
                (:nome, :email, :senha, :perfil, 1)";

        $cmd = $this->pdo->prepare($sql);

         $cmd->execute([
            ":nome" => $nome,
            ":email" => $email,
            ":senha" => $hashSenha,
            ":perfil" => $perfil
        ]);

        return $this->pdo->lastInsertId();

    }

    public function buscarEmail($email){

        $sql = "SELECT * FROM users
            WHERE email = :email
            LIMIT 1";

        $cmd = $this->pdo->prepare($sql);

        $cmd->execute([
            ":email" => $email
        ]);

    return $cmd->fetch();
}

    public function validarSenha($senha, $hashSenha){
        return password_verify($senha, $hashSenha);
         

    }
}