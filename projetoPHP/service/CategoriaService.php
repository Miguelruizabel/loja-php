
<?php

require_once __DIR__ . '/../database/dbconnect.php';
require_once __DIR__ . '/../model/Categoria.php';

class CategoriaService
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function listarCategorias()
    {
        $stmt = $this->pdo->query("SELECT * FROM categorias");
        $categorias = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categorias[] = new Categoria($row['id'], $row['nome']);
        }
        return $categorias;
    }

    public function salvarCategoria($nome)
    {
        $stmt = $this->pdo->prepare("INSERT INTO categorias (nome) VALUES (:nome)");
        return $stmt->execute(['nome' => $nome]);
    }

    public function excluirCategoria($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM categorias WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function buscarCategoriaPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categorias WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Categoria($row['id'], $row['nome']);
        }
        return null;
    }
}