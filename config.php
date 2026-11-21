<?php
// Dados de acesso ao banco de dados.
$host = 'localhost';
$database = 'voltauto';
<<<<<<< HEAD
$user = 'daemon';
$password = 'kesya';
=======
$user = 'root';
$password = '';
>>>>>>> beb99800dacdaea59fc4c9fb2c58b513e19e0a9c

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$database;charset=utf8mb4",
        $user,
        $password
    );

    // Mostra os erros do banco durante o desenvolvimento.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $erro) {
    exit($erro);
}
