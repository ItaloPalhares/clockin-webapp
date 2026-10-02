<?php
try{

    $pdo = new PDO("mysql:dbname=ponto_colaboradores;host=localhost;charset=utf8mb4",
    "root",
    "");

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}

catch(PDOException $e){
    echo "Erro com bando de dados: ".$e->getMessage();
}

catch(Exception $e){
     echo "Erro: ".$e->getMessage();
}

?>