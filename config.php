<?php
// Dados de acesso ao banco de dados.
$host = 'localhost';
$database = 'voltauto';
$user = 'daemon';
$password = 'kesya';

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
