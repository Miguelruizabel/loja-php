<?php
require_once '../database/dbconnect.php';
require'verify-log.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Loja Virtual</a>
            <div>
                <a href="logout.php" class="btn btn-outline-light">Sair</a>
            </div>
        </div>
    </nav>
    <div class="container">
        <h1 class="mb-4">Bem-vindo ao Painel Administrativo</h1>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Categorias</h5>
                        <p class="card-text">Gerencie as categorias dos produtos.</p>
                        <a href="categorias/index.php" class="btn btn-primary">Gerenciar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Produtos</h5>
                        <p class="card-text">Gerencie os produtos da loja.</p>
                        <a href="produtos/index.php" class="btn btn-primary">Gerenciar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Vendas</h5>
                        <p class="card-text">Visualize as vendas realizadas.</p>
                        <a href="vendas/index.php" class="btn btn-primary">Visualizar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>