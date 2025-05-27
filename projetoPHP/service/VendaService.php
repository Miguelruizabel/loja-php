
<?php

require_once __DIR__ . '/../database/dbconnect.php';

class VendaService
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function listarVendas()
    {
        $stmt = $this->pdo->query("SELECT * FROM vendas ORDER BY data_venda DESC");
        $vendas = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Calcula o total somando os itens
            $total = $this->calcularTotalVenda($row['id']);
            $vendas[] = (object)[
                'id' => $row['id'],
                'data' => $row['data_venda'],
                'total' => $total
            ];
        }
        return $vendas;
    }

    private function calcularTotalVenda($vendaId)
    {
        $stmt = $this->pdo->prepare(
            "SELECT vi.quantidade, p.preco 
             FROM vendas_itens vi 
             JOIN produtos p ON vi.produto_id = p.id 
             WHERE vi.venda_id = :venda_id"
        );
        $stmt->execute(['venda_id' => $vendaId]);
        $total = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $total += $row['quantidade'] * $row['preco'];
        }
        return $total;
    }

    public function listarItensVenda($vendaId)
    {
        $stmt = $this->pdo->prepare(
            "SELECT vi.*, p.nome AS produto_nome, p.preco AS preco_unitario
             FROM vendas_itens vi 
             JOIN produtos p ON vi.produto_id = p.id 
             WHERE vi.venda_id = :venda_id"
        );
        $stmt->execute(['venda_id' => $vendaId]);
        $itens = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $itens[] = [
                'produto_nome' => $row['produto_nome'],
                'quantidade' => $row['quantidade'],
                'preco_unitario' => $row['preco_unitario'],
                'subtotal' => $row['quantidade'] * $row['preco_unitario']
            ];
        }
        return $itens;
    }

    public function registrarVenda($carrinho)
    {
        $this->pdo->beginTransaction();
        $stmt = $this->pdo->prepare("INSERT INTO vendas (data_venda) VALUES (NOW())");
        $stmt->execute();
        $venda_id = $this->pdo->lastInsertId();

        $stmtItem = $this->pdo->prepare("INSERT INTO vendas_itens (venda_id, produto_id, quantidade) VALUES (:venda_id, :produto_id, :quantidade)");
        foreach ($carrinho as $produto_id => $quantidade) {
            $stmtItem->execute([
                'venda_id' => $venda_id,
                'produto_id' => $produto_id,
                'quantidade' => $quantidade
            ]);
        }
        $this->pdo->commit();
        return $venda_id;
    }

    // Adicionado: Limpar histórico de vendas
    public function limparHistoricoVendas()
    {
        $this->pdo->beginTransaction();
        $this->pdo->exec("DELETE FROM vendas_itens");
        $this->pdo->exec("DELETE FROM vendas");
        $this->pdo->commit();
    }

    // Novo método: Salvar histórico de vendas em CSV
    public function salvarHistoricoVendasCSV($filePath)
    {
        $vendas = $this->listarVendas();
        $fp = fopen($filePath, 'w');
        // Cabeçalho
        fputcsv($fp, ['ID', 'Data', 'Total']);
        foreach ($vendas as $venda) {
            fputcsv($fp, [$venda->id, $venda->data, number_format($venda->total, 2, ',', '.')]);
        }
        fclose($fp);
        return file_exists($filePath);
    }
}