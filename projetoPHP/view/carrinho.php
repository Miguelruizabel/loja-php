<?php


session_start();
require_once '../service/ProdutoService.php';
$produtoService = new ProdutoService();
require_once '../service/VendaService.php';
$vendaService = new VendaService();

$pagamento = $_POST['pagamento'] ?? '';
$finalizado = false;
$erro = '';
$etapa = $_POST['etapa'] ?? 'selecionar_pagamento';

// Remover produto do carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remover_id'])) {
        $remover_id = $_POST['remover_id'];
        if (isset($_SESSION['carrinho'][$remover_id])) {
            unset($_SESSION['carrinho'][$remover_id]);
        }
        header('Location: carrinho.php');
        exit;
    }
    if (isset($_POST['finalizar'])) {
        // Salvar venda no banco
        $carrinho = isset($_SESSION['carrinho']) ? $_SESSION['carrinho'] : [];
        $total = 0;
        foreach ($carrinho as $produto_id => $quantidade) {
            $produto = $produtoService->buscarProdutoPorId($produto_id);
            if ($produto) {
                $total += $produto->getPreco() * $quantidade;
            }
        }
        // Verifica se o endereço foi enviado
        $endereco_entrega = $_POST['endereco_entrega'] ?? '';
        if (empty($endereco_entrega)) {
            $finalizado = false;
            $erro = "O endereço de entrega é obrigatório.";
        } elseif ($total > 0) {
            // Aqui você pode passar o endereço para registrarVenda se desejar salvar no banco
            $vendaService->registrarVenda($carrinho /*, $endereco_entrega */);
            $_SESSION['carrinho'] = [];
            $finalizado = true;
        } else {
            $finalizado = false;
            $erro = "Não é possível finalizar uma compra sem itens no carrinho.";
        }
    }
}

$carrinho = isset($_SESSION['carrinho']) ? $_SESSION['carrinho'] : [];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Carrinho - ElectricMonkey</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #031E32;
            margin: 0;
            min-height: 100vh;
            color: #fff;
        }
        .container {
            max-width: 700px;
            margin: 40px auto;
            background: #16263a;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.10);
            padding: 32px 32px 24px 32px;
        }
        h2 {
            color: #ffd02c;
            margin-top: 0;
            text-align: center;
            letter-spacing: 1px;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            background: #22344a;
            border-radius: 8px;
            margin-bottom: 18px;
            padding: 18px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .produto-info {
            display: flex;
            flex-direction: column;
        }
        .produto-nome {
            font-weight: bold;
            color: #ffd02c;
            font-size: 1.1rem;
        }
        .produto-preco {
            color: #2ecc40;
            margin-top: 4px;
            font-size: 1rem;
        }
        .produto-quantidade {
            color: #fff;
            margin-top: 2px;
            font-size: 0.95rem;
        }
        form {
            margin: 0;
            display: inline;
        }
        button, .continuar, .finalizar {
            background: #ff8c1a;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 8px 18px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
        }
        button:hover, .continuar:hover, .finalizar:hover {
            background: #ffd02c;
            color: #0b2236;
        }
        .empty-cart {
            text-align: center;
            color: #fff;
            margin: 40px 0;
        }
        .continuar {
            display: block;
            width: fit-content;
            margin: 32px auto 0 auto;
            padding: 12px 32px;
            font-size: 1.1rem;
        }
        .finalizar {
            display: block;
            width: fit-content;
            margin: 24px auto 0 auto;
            padding: 12px 32px;
            font-size: 1.1rem;
            background: #2ecc40;
        }
        .finalizar:hover {
            background: #27ae3a;
            color: #fff;
        }
        .msg-finalizado {
            text-align: center;
            color: #2ecc40;
            font-size: 1.2rem;
            margin-bottom: 24px;
        }
        .erro-finalizar {
            text-align: center;
            color: #ff4d4d;
            font-size: 1.1rem;
            margin-bottom: 18px;
        }
        .pagamento {
            margin: 24px auto 0 auto;
            text-align: center;
        }
        .pagamento label {
            margin-right: 18px;
            font-size: 1.05rem;
            color: #ffd02c;
        }
        .pagamento input[type="radio"] {
            margin-right: 6px;
        }
        .pagamento-dados {
            margin-top: 18px;
            text-align: left;
            max-width: 350px;
            margin-left: auto;
            margin-right: auto;
        }
        .pagamento-dados label {
            color: #fff;
            font-size: 1rem;
            display: block;
            margin-bottom: 6px;
        }
        .pagamento-dados input {
            width: 100%;
            padding: 7px;
            border-radius: 4px;
            border: 1px solid #ffd02c;
            background: #22344a;
            color: #fff;
            margin-bottom: 14px;
            font-size: 1rem;
        }
        .pagamento-dados input[type="radio"] {
            width: auto;
            margin-right: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Seu Carrinho</h2>
        <?php if (!empty($erro)): ?>
            <div class="erro-finalizar">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>
        <?php if ($finalizado): ?>
            <div class="msg-finalizado">
                Compra finalizada com sucesso!<br>
                <?php if (!empty($pagamento)): ?>
                    Pagamento escolhido: <strong>
                        <?php
                            if ($pagamento === 'pix') echo 'Pix';
                            elseif ($pagamento === 'boleto') echo 'Boleto';
                            elseif ($pagamento === 'cartao_credito') echo 'Cartão de Crédito';
                            elseif ($pagamento === 'cartao_debito') echo 'Cartão de Débito';
                        ?>
                    </strong>
                <?php endif; ?>
                <br>Obrigado por comprar na ElectricMonkey.
            </div>
            <a class="continuar" href="index.php">Voltar para a loja</a>
        <?php elseif (empty($carrinho)): ?>
            <div class="empty-cart">
                <p>O carrinho está vazio.</p>
                <a class="continuar" href="index.php">Voltar para a loja</a>
            </div>
        <?php else: ?>
            <ul>
                <?php foreach ($carrinho as $produto_id => $quantidade): 
                    $produto = $produtoService->buscarProdutoPorId($produto_id);
                    if ($produto):
                ?>
                    <li>
                        <div class="produto-info">
                            <span class="produto-nome"><?= htmlspecialchars($produto->getNome()) ?></span>
                            <span class="produto-quantidade">Quantidade: <?= $quantidade ?></span>
                            <span class="produto-preco">Preço: R$ <?= number_format($produto->getPreco(), 2, ',', '.') ?></span>
                        </div>
                        <form method="post">
                            <input type="hidden" name="remover_id" value="<?= $produto_id ?>">
                            <button type="submit">Remover</button>
                        </form>
                    </li>
                <?php endif; endforeach; ?>
            </ul>
            <?php if ($etapa === 'selecionar_pagamento'): ?>
                <form method="post" class="pagamento" autocomplete="off">
                    <div>
                        <label><input type="radio" name="pagamento" value="pix" required <?= $pagamento=='pix'?'checked':''; ?>> Pix</label>
                        <label><input type="radio" name="pagamento" value="boleto" <?= $pagamento=='boleto'?'checked':''; ?>> Boleto</label>
                        <label><input type="radio" name="pagamento" value="cartao_credito" <?= $pagamento=='cartao_credito'?'checked':''; ?>> Cartão de Crédito</label>
                        <label><input type="radio" name="pagamento" value="cartao_debito" <?= $pagamento=='cartao_debito'?'checked':''; ?>> Cartão de Débito</label>
                    </div>
                    <input type="hidden" name="etapa" value="dados_pagamento">
                    <button type="submit" class="finalizar">Avançar</button>
                </form>
            <?php elseif ($etapa === 'dados_pagamento'): ?>
                <form method="post" class="pagamento-dados" autocomplete="off">
                    <input type="hidden" name="pagamento" value="<?= htmlspecialchars($pagamento) ?>">
                    <input type="hidden" name="etapa" value="finalizar">
                    <!-- Campo de endereço obrigatório para todos os pagamentos -->
                    <label>Endereço de entrega:</label>
                    <input type="text" name="endereco_entrega" maxlength="120" required placeholder="Rua, número, bairro, cidade, UF">
                    <?php if ($pagamento == 'pix'): ?>
                        <label>Nome completo:</label>
                        <input type="text" name="nome_pix" maxlength="60" required>
                        <label>CPF:</label>
                        <input type="text" name="cpf_pix" maxlength="14" required>
                        <label>Chave Pix (CPF, CNPJ, e-mail ou telefone):</label>
                        <input type="text" name="chave_pix" maxlength="60" required>
                    <?php elseif ($pagamento == 'boleto'): ?>
                        <label>Nome completo:</label>
                        <input type="text" name="nome_boleto" maxlength="60" required>
                        <label>CPF:</label>
                        <input type="text" name="cpf_boleto" maxlength="14" required>
                    <?php elseif ($pagamento == 'cartao_credito'): ?>
                        <label>Nome no cartão:</label>
                        <input type="text" name="nome_cartao_credito" maxlength="60" required>
                        <label>Número do cartão:</label>
                        <input type="text" name="numero_cartao_credito" maxlength="19" required>
                        <label>Validade (MM/AA):</label>
                        <input type="text" name="validade_cartao_credito" maxlength="5" required>
                        <label>CVV:</label>
                        <input type="text" name="cvv_cartao_credito" maxlength="4" required>
                    <?php elseif ($pagamento == 'cartao_debito'): ?>
                        <label>Nome no cartão:</label>
                        <input type="text" name="nome_cartao_debito" maxlength="60" required>
                        <label>Número do cartão:</label>
                        <input type="text" name="numero_cartao_debito" maxlength="19" required>
                        <label>Validade (MM/AA):</label>
                        <input type="text" name="validade_cartao_debito" maxlength="5" required>
                        <label>CVV:</label>
                        <input type="text" name="cvv_cartao_debito" maxlength="4" required>
                    <?php endif; ?>
                    <button type="submit" name="finalizar" class="finalizar">Finalizar Compra</button>
                </form>
            <?php endif; ?>
            <a class="continuar" href="index.php">Continuar comprando</a>
        <?php endif; ?>
    </div>
</body>
</html>