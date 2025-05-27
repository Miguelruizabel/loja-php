
<?php

class Produto
{
    private $id;
    private $nome;
    private $preco;
    private $categoria_id;

    public function __construct($id = null, $nome = null, $preco = null, $categoria_id = null)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->preco = $preco;
        $this->categoria_id = $categoria_id;
    }

    // Getters e setters...
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getNome() { return $this->nome; }
    public function setNome($nome) { $this->nome = $nome; }
    public function getPreco() { return $this->preco; }
    public function setPreco($preco) { $this->preco = $preco; }
    public function getCategoriaId() { return $this->categoria_id; }
    public function setCategoriaId($categoria_id) { $this->categoria_id = $categoria_id; }
}