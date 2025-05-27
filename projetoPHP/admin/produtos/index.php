
<?php

require_once __DIR__ . '/../../service/ProdutoService.php';
require_once __DIR__ . '/../../service/CategoriaService.php';

$produtoService = new ProdutoService();
$categoriaService = new CategoriaService();

// Excluir produto
if (isset($_GET['excluir'])) {
    $produtoService->excluirProduto($_GET['excluir']);
    header("Location: index.php");
    exit;
}

// Salvar novo produto ou editar existente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $categoria_id = $_POST['categoria_id'];
    $id = $_POST['id'] ?? null;

    if ($id) {
        // Editar produto
        $produtoService->editarProduto($id, $nome, $preco, $categoria_id);
    } else {
        // Novo produto
        $produtoService->salvarProduto($nome, $preco, $categoria_id);
    }
    header("Location: index.php");
    exit;
}

// Buscar produto para edição
$produtoEdit = null;
if (isset($_GET['editar'])) {
    $produtoEdit = $produtoService->buscarProdutoPorId($_GET['editar']);
}

// Listar produtos e categorias
$produtos = $produtoService->listarProdutos();
$categorias = [];
foreach ($categoriaService->listarCategorias() as $cat) {
    $categorias[$cat->getId()] = $cat->getNome();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Produtos</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 30px;
        }
        h1 {
            color: #222;
        }
        form {
            margin-bottom: 30px;
            background: #fff;
            padding: 18px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px #0001;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        form label {
            font-weight: bold;
        }
        form input, form select, form button, form a {
            padding: 6px 10px;
            border-radius: 4px;
            border: 1px solid #bbb;
            font-size: 1em;
        }
        form button {
            background: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.2s;
        }
        form button:hover {
            background: #0056b3;
        }
        form a {
            background: #eee;
            color: #333;
            text-decoration: none;
            border: 1px solid #bbb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px #0001;
        }
        th, td {
            padding: 12px 10px;
            text-align: left;
        }
        th {
            background: #007bff;
            color: #fff;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background: #f2f6fa;
        }
        tr:hover {
            background: #e9f2ff;
        }
        .actions a {
            margin-right: 8px;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.2s;
        }
        .actions a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<h1>Gerenciamento de Produtos</h1>

<!-- Formulário de novo/edição -->
<form method="post">
    <input type="hidden" name="id" value="<?= $produtoEdit ? $produtoEdit->getId() : '' ?>">
    <label>Nome:</label>
    <input type="text" name="nome" required value="<?= $produtoEdit ? htmlspecialchars($produtoEdit->getNome()) : '' ?>">
    <label>Preço:</label>
    <input type="number" step="0.01" name="preco" required value="<?= $produtoEdit ? $produtoEdit->getPreco() : '' ?>">
    <label>Categoria:</label>
    <select name="categoria_id" required>
        <option value="">Selecione</option>
        <?php foreach ($categorias as $id => $nome): ?>
            <option value="<?= $id ?>" <?= $produtoEdit && $produtoEdit->getCategoriaId() == $id ? 'selected' : '' ?>>
                <?= htmlspecialchars($nome) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit"><?= $produtoEdit ? 'Salvar Alterações' : 'Adicionar Produto' ?></button>
    <?php if ($produtoEdit): ?>
        <a href="index.php">Cancelar</a>
    <?php endif; ?>
</form>

<!-- Tabela de produtos -->
<table>
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Preço</th>
        <th>Categoria</th>
        <th>Ações</th>
    </tr>
    <?php foreach ($produtos as $produto): ?>
    <tr>
        <td><?= $produto->getId() ?></td>
        <td><?= htmlspecialchars($produto->getNome()) ?></td>
        <td>R$ <?= number_format($produto->getPreco(), 2, ',', '.') ?></td>
        <td><?= htmlspecialchars($categorias[$produto->getCategoriaId()] ?? 'Sem categoria') ?></td>
        <td class="actions">
            <a href="index.php?editar=<?= $produto->getId() ?>">Editar</a>
            <a href="index.php?excluir=<?= $produto->getId() ?>" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</body>
</html>