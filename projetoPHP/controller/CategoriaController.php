
<?php

require_once __DIR__ . '/../service/CategoriaService.php';

class CategoriaController
{
    private $service;

    public function __construct()
    {
        $this->service = new CategoriaService();
    }

    public function listar()
    {
        return $this->service->listarCategorias();
    }

    public function salvar($nome)
    {
        return $this->service->salvarCategoria($nome);
    }

    public function excluir($id)
    {
        return $this->service->excluirCategoria($id);
    }

    public function buscarPorId($id)
    {
        return $this->service->buscarCategoriaPorId($id);
    }
}
