
<?php
require_once __DIR__ . '/../../database/dbconnect.php';

$stmt = $pdo->query("SELECT * FROM categorias ORDER BY id DESC");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Categorias</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 40px;
        }
        h1 {
            color: #333;
            margin-bottom: 24px;
        }
        table {
            width: 400px;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 8px #0001;
            margin-bottom: 30px;
        }
        th, td {
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        th {
            background: #007bff;
            color: #fff;
            font-weight: bold;
        }
        tr:last-child td {
            border-bottom: none;
        }
        tr:nth-child(even) {
            background: #f2f6fa;
        }
        tr:hover {
            background: #e9f2ff;
        }
    </style>
</head>
<body>
    <h1>Gerenciamento de Categorias</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
        </tr>
        <?php foreach ($categorias as $categoria): ?>
        <tr>
            <td><?= $categoria['id'] ?></td>
            <td><?= htmlspecialchars($categoria['nome']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>