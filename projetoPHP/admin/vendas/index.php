<?php
session_start();
require_once __DIR__ . '/../../database/dbconnect.php';
require_once __DIR__ . '/../../service/VendaService.php';

$vendaService = new VendaService();

// Exportar histórico de vendas em CSV, se solicitado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['exportar_historico'])) {
    $vendas = $vendaService->listarVendas();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="historico_vendas.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Data', 'Total']);
    foreach ($vendas as $venda) {
        fputcsv($output, [$venda->id, $venda->data, number_format($venda->total, 2, ',', '.')]);
    }
    fclose($output);
    exit;
}

// Limpar histórico de vendas, se solicitado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['limpar_historico'])) {
    $vendaService->limparHistoricoVendas();
    header('Location: index.php');
    exit;
}

// Buscar vendas
$vendas = $vendaService->listarVendas();

// Buscar itens de uma venda específica, se solicitado
$itens = [];
if (isset($_GET['visualizar'])) {
    $itens = $vendaService->listarItensVenda($_GET['visualizar']);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Vendas Realizadas</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background: #f7f7f7; padding: 30px; }
        h1 { color: #222; }
        table { width: 100%; border-collapse: collapse; background: #fff; margin-bottom: 30px; }
        th, td { padding: 10px; border: 1px solid #ddd; }
        th { background: #007bff; color: #fff; }
        tr:nth-child(even) { background: #f2f6fa; }
        tr:hover { background: #e9f2ff; }
        .actions a { color: #007bff; text-decoration: none; font-weight: bold; }
        .actions a:hover { text-decoration: underline; }
        .itens { margin-top: 30px; }
        .limpar-btn { margin-bottom: 20px; }
        .exportar-btn { margin-bottom: 20px; }
        /* Botões customizados */
        .btn-custom {
            background: linear-gradient(90deg, #007bff 60%, #0056b3 100%);
            color: #fff;
            border: none;
            padding: 10px 22px;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: background 0.2s, transform 0.2s;
            margin-bottom: 10px;
        }
        .btn-custom:hover {
            background: linear-gradient(90deg, #0056b3 60%, #007bff 100%);
            transform: translateY(-2px) scale(1.04);
        }
    </style>
</head>
<body>
<h1>Vendas Realizadas</h1>

<form method="post" class="exportar-btn">
    <button type="submit" name="exportar_historico" class="btn-custom">
        Salvar Histórico de Vendas (CSV)
    </button>
</form>

<form method="post" class="limpar-btn">
    <button type="submit" name="limpar_historico" class="btn-custom">
        Limpar Histórico de Vendas
    </button>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>Data</th>
        <th>Total</th>
        <th>Ações</th>
    </tr>
    <?php foreach ($vendas as $venda): ?>
    <tr>
        <td><?= $venda->id ?></td>
        <td><?= date('d/m/Y H:i', strtotime($venda->data)) ?></td>
        <td>R$ <?= number_format($venda->total, 2, ',', '.') ?></td>
        <td class="actions">
            <a href="index.php?visualizar=<?= $venda->id ?>">Visualizar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php if ($itens): ?>
<div class="itens">
    <h2>Itens da Venda #<?= htmlspecialchars($_GET['visualizar']) ?></h2>
    <table>
        <tr>
            <th>Produto</th>
            <th>Quantidade</th>
            <th>Preço Unitário</th>
            <th>Subtotal</th>
        </tr>
        <?php foreach ($itens as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['produto_nome']) ?></td>
            <td><?= $item['quantidade'] ?></td>
            <td>R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
            <td>R$ <?= number_format($item['subtotal'], 2, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>
</body>
</html>