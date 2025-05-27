
<?php

require_once __DIR__ . '/../service/ProdutoService.php';

class ProdutoController
{
    private $service;

    public function __construct()
    {
        $this->service = new ProdutoService();
    }

    public function listar()
    {
        return $this->service->listarProdutos();
    }

    public function salvar($nome, $preco, $categoria_id)
    {
        return $this->service->salvarProduto($nome, $preco, $categoria_id);
    }

    public function excluir($id)
    {
        return $this->service->excluirProduto($id);
    }

    public function buscarPorId($id)
    {
        return $this->service->buscarProdutoPorId($id);
    }
}