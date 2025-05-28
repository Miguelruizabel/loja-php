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
}
