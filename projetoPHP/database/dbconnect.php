
<?php
// Configurações do banco de dados
$host = 'localhost';
$db   = 'loja_virtual'; // Altere para o nome do seu banco
$user = 'root';
$pass = ''; // Altere se sua senha for diferente

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    // Ativa o modo de erros do PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}