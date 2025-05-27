<?php


require_once __DIR__ . '/../database/dbconnect.php';
require_once __DIR__ . '/../model/Produto.php';

class ProdutoService
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function listarProdutos()
    {
        $stmt = $this->pdo->query("SELECT * FROM produtos");
        $produtos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $produtos[] = new Produto($row['id'], $row['nome'], $row['preco'], $row['categoria_id']);
        }
        return $produtos;
    }

    public function listarPorCategoria($categoria_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM produtos WHERE categoria_id = :categoria_id");
        $stmt->execute(['categoria_id' => $categoria_id]);
        $produtos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $produtos[] = new Produto($row['id'], $row['nome'], $row['preco'], $row['categoria_id']);
        }
        return $produtos;
    }

    public function salvarProduto($nome, $preco, $categoria_id)
    {
        $stmt = $this->pdo->prepare("INSERT INTO produtos (nome, preco, categoria_id) VALUES (:nome, :preco, :categoria_id)");
        return $stmt->execute(['nome' => $nome, 'preco' => $preco, 'categoria_id' => $categoria_id]);
    }

    public function excluirProduto($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM produtos WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    
public function editarProduto($id, $nome, $preco, $categoria_id) {
    $stmt = $this->pdo->prepare("UPDATE produtos SET nome = :nome, preco = :preco, categoria_id = :categoria_id WHERE id = :id");
    return $stmt->execute([
        'nome' => $nome,
        'preco' => $preco,
        'categoria_id' => $categoria_id,
        'id' => $id
    ]);
}
    

    public function buscarProdutoPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM produtos WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Produto($row['id'], $row['nome'], $row['preco'], $row['categoria_id']);
        }
        return null;
    }
}