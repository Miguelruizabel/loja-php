
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

}
