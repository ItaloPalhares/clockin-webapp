<?php

class Ponto
{

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function buscarUsuarioPorId($usuarioId)
    {

        $sql = "SELECT *
                FROM pontos
                WHERE users_id = :usuario_id
                ORDER BY data_hora DESC";

        $cmd = $this->pdo->prepare($sql);

        $cmd->execute([":usuario_id" => $usuarioId]);

        return $cmd->fetchAll();
    }


    public function registrar($usuarioId, $latitude, $longitude, $imagem)
    {
        $sql = "INSERT INTO pontos
                (users_id, data_hora, latitude, longitude, imagem)
                VALUES
                (:usuario_id, NOW(), :latitude, :longitude, :imagem)";

        $cmd = $this->pdo->prepare($sql);

        $cmd->execute([
            ":usuario_id" => $usuarioId,
            ":latitude" => $latitude,
            ":longitude" => $longitude,
            ":imagem" => $imagem
        ]);

        return $this->pdo->lastInsertId();
    }

    public function buscarPorId($pontoId)
    {
        $sql = "SELECT *
                FROM pontos
                WHERE id = :ponto_id
                LIMIT 1";
        $cmd = $this->pdo->prepare($sql);

        $cmd->execute([":ponto_id" => $pontoId]);

        return $cmd->fetch();
    }

    public function contarPorUsuario($usuarioId)
    {
        $sql = "SELECT COUNT(*)
            FROM pontos
            WHERE users_id = :usuario_id";

        $cmd = $this->pdo->prepare($sql);

        $cmd->execute([
            ":usuario_id" => $usuarioId
        ]);

        return (int) $cmd->fetchColumn();
    }

    public function buscarUsuarioPorIdPaginado($usuarioId, $limite, $offset)
    {
        $sql = "SELECT *
            FROM pontos
            WHERE users_id = :usuario_id
            ORDER BY data_hora DESC
            LIMIT :limite
            OFFSET :offset";

        $cmd = $this->pdo->prepare($sql);

        $cmd->bindValue(
            ":usuario_id",
            $usuarioId,
            PDO::PARAM_INT
        );

        $cmd->bindValue(
            ":limite",
            $limite,
            PDO::PARAM_INT
        );

        $cmd->bindValue(
            ":offset",
            $offset,
            PDO::PARAM_INT
        );

        $cmd->execute();

        return $cmd->fetchAll();
    }

    public function buscarColaboradoresPaginado($limite, $offset, $busca = "")
    {

        $sql = "SELECT pontos.*,
                    users.nome AS usuario_nome,
                    users.email AS usuario_email
                FROM pontos
                INNER JOIN users
                    ON pontos.users_id = users.id";

        if ($busca !== "") {
            $sql .= " WHERE users.nome LIKE :busca_nome
                     OR users.email LIKE :busca_email";
        }

        $sql .= " ORDER BY pontos.data_hora DESC
                LIMIT :limite
                OFFSET :offset";

        $cmd = $this->pdo->prepare($sql);
        
        if ($busca !== ""){
            $cmd->bindValue(":busca_nome","%".$busca."%");
            $cmd->bindValue(":busca_email","%".$busca."%");
        }

        $cmd->bindValue(":limite", $limite, PDO::PARAM_INT);
        $cmd->bindValue(":offset", $offset, PDO::PARAM_INT);

        $cmd->execute();

        return $cmd->fetchAll();    
    }

    public function contarTodosColaboradores($busca = ""){

        $sql = "SELECT COUNT(*)
                FROM pontos
                INNER JOIN users
                ON pontos.users_id = users.id";
        
        if ($busca !== ""){
            $sql .= " WHERE users.nome LIKE :busca_nome
                     OR users.email LIKE :busca_email";
        }

        $cmd = $this->pdo->prepare($sql);

        if ($busca !== ""){

            $cmd ->execute([":busca_nome" => "%" . $busca . "%", ":busca_email" => "%" . $busca . "%"]);
        } else {
            $cmd->execute();
        }

        return (int) $cmd->fetchColumn();

    }
}
