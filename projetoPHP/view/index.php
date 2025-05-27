<?php

session_start();

require_once '../service/ProdutoService.php';
$produtoService = new ProdutoService();

// Mapeamento de nomes de produtos para imagens
$imagens_produtos = [
    'PlayStation 5' => '../assets/Playstation 5.jpg.jpg',
    'Xbox Series X' => '../assets/Xbox Series X.jpg.webp',
    'Nintendo Switch' => '../assets/Nintendo Switch.jpg.webp',
    'Teclado Gamer' => '../assets/Teclado Gamer.jpg.jpg',
    'Cadeira gamer' => '../assets/Cadeira Gamer.jpg.webp',
    'Headset Gamer' => '../assets/Headset Gamer.jpg.webp',
    'Mouse Gamer' => '../assets/Mouse Gamer.jpg.webp',
    'PlayStation 4' => '../assets/Playstation 4.jpg.webp', // AJUSTADO para o nome correto do arquivo
    'Xbox Series S' => '../assets/Xbox Series S.jpg.webp',
    'Pc Gamer Completo' => '../assets/Gabinete Gamer Completo.jpg.webp',
];


// Adiciona produto ao carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produto_id'])) {
    $produto_id = $_POST['produto_id'];
    $quantidade = max(1, intval($_POST['quantidade']));

    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }
    if (isset($_SESSION['carrinho'][$produto_id])) {
        $_SESSION['carrinho'][$produto_id] += $quantidade;
    } else {
        $_SESSION['carrinho'][$produto_id] = $quantidade;
    }
    header('Location: index.php');
    exit;
}

$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$categoria_id = isset($_GET['categoria_id']) ? $_GET['categoria_id'] : null;

if ($busca !== '') {
    $produtos = [];
    foreach ($produtoService->listarProdutos() as $produto) {
        if (stripos($produto->getNome(), $busca) !== false) {
            $produtos[] = $produto;
        }
    }
} elseif ($categoria_id) {
    $produtos = $produtoService->listarPorCategoria($categoria_id);
} else {
    $produtos = $produtoService->listarProdutos();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>ElectricMonkey</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #031E32;
            margin: 0;
            min-height: 100vh;
        }
        .brand-name {
            color: #ffd02c;
            font-size: 2rem;
            font-family: 'Segoe UI Black', Arial, sans-serif;
            font-weight: bold;
            letter-spacing: 2px;
            margin-left: 10px;
            margin-right: 30px;
            white-space: nowrap;
        }
        .overlay {
            min-height: 100vh;
            background: transparent;
        }
        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            padding: 40px 5vw 0 5vw;
            gap: 18px;
        }
        .logo-img {
            height: 48px;
            margin-right: 8px;
            vertical-align: middle;
        }
        .search-bar {
            flex: 1;
            display: flex;
            justify-content: flex-start;
            margin-left: 0;
        }
        .search-bar input[type="text"] {
            padding: 10px;
            width: 220px;
            border-radius: 6px;
            border: 1px solid #ffd02c;
            background: #0b2236;
            color: #fff;
            font-size: 1rem;
        }
        .search-bar button {
            padding: 10px 18px;
            border: none;
            background: #ff8c1a;
            color: #fff;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 1rem;
            margin-left: 8px;
            transition: background 0.2s;
        }
        .search-bar button:hover {
            background: #ffd02c;
            color: #0b2236;
        }
        .carrinho-link {
            background: #ff8c1a;
            color: #fff;
            padding: 12px 28px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1rem;
            transition: background 0.2s;
            margin-left: 20px;
        }
        .carrinho-link:hover {
            background: #ffd02c;
            color: #0b2236;
        }
        .categorias {
            margin-bottom: 30px;
            padding: 0 5vw;
            text-align: center;
        }
        .categorias h2 {
            color: #ffd02c;
            margin-bottom: 10px;
        }
        .categorias ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            gap: 24px;
        }
        .categorias li {
            margin: 0;
        }
        .categorias a {
            color: #ff8c1a;
            font-weight: bold;
            font-size: 1.1rem;
            text-decoration: underline;
            transition: color 0.2s;
        }
        .categorias a:hover {
            color: #ffd02c;
        }
        h2 {
            color: #ffd02c;
            margin-top: 0;
            padding: 0 5vw;
        }
        .produtos {
            display: flex;
            flex-wrap: wrap;
            gap: 32px;
            padding: 0 5vw 40px 5vw;
        }
        .produto {
            background: #16263a;
            border-radius: 12px;
            padding: 28px 20px 20px 20px;
            width: 240px;
            box-sizing: border-box;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .produto-imagem {
            width: 100%;
            max-width: 180px;
            height: 140px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 12px;
            background: #0b2236;
            display: block;
        }
        .produto h3 {
            margin-top: 0;
            color: #ffd02c;
            font-size: 1.3rem;
            margin-bottom: 10px;
        }
        .produto .preco {
            color: #2ecc40;
            font-weight: bold;
            margin-bottom: 14px;
            font-size: 1.2rem;
        }
        .produto form {
            margin-top: auto;
            width: 100%;
        }
        .produto input[type="number"] {
            width: 60px;
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ff8c1a;
            background: #16263a;
            color: #fff;
            margin-bottom: 8px;
        }
        .produto button {
            width: 100%;
            padding: 10px 0;
            background: #ff8c1a;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .produto button:hover {
            background: #ffd02c;
            color: #0b2236;
        }
        @media (max-width: 900px) {
            .produtos { flex-direction: column; align-items: center; }
            .produto { width: 90%; }
            .top-bar, .categorias, h2, .produtos { padding-left: 2vw; padding-right: 2vw; }
            .brand-name { font-size: 1.3rem; }
            .logo-img { height: 32px; }
        }
    </style>
</head>
<body>
    <div class="overlay">
        <div class="top-bar">
            <img src="../assets/imagem.png" alt="Logo" class="logo-img">
            <span class="brand-name">ElectricMonkey</span>
            <form class="search-bar" method="get" action="index.php">
                <input type="text" name="busca" placeholder="Pesquisar produtos..." value="<?= htmlspecialchars($busca) ?>">
                <button type="submit">Buscar</button>
            </form>
            <a class="carrinho-link" href="carrinho.php">Ver Carrinho</a>
        </div>
        <div class="categorias">
            <h2>Categorias</h2>
            <ul>
                <li><a href="index.php?categoria_id=1">Periféricos</a></li>
                <li><a href="index.php?categoria_id=2">Consoles</a></li>
                <li><a href="index.php">Todas</a></li>
            </ul>
        </div>
        <h2>Produtos</h2>
        <div class="produtos">
            <?php if (count($produtos) > 0): ?>
                <?php foreach ($produtos as $produto): ?>
                    <div class="produto">
                        <?php
                            $nome = $produto->getNome();
                            $img = isset($imagens_produtos[$nome]) ? $imagens_produtos[$nome] : '../assets/imagem.png';
                        ?>
                        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($nome) ?>" class="produto-imagem">
                        <h3><?= htmlspecialchars($nome) ?></h3>
                        <div class="preco">R$ <?= number_format($produto->getPreco(), 2, ',', '.') ?></div>
                        <form action="index.php" method="post">
                            <input type="hidden" name="produto_id" value="<?= $produto->getId() ?>">
                            <input type="number" name="quantidade" value="1" min="1">
                            <button type="submit">Adicionar ao Carrinho</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color:#fff;">Nenhum produto encontrado.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>