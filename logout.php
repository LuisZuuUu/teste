<?php
require 'includes/functions.php';

// Apaga os dados da sessão do usuário.
$_SESSION = [];

// Encerra a sessão.
session_destroy();

// Volta para a tela de login.
header('Location: login.php');
exit;
