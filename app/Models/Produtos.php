<?php

class Produto {
    private $id;
    private $nome;
    private $descricao;
    private $preco_custo;
    private $preco_venda;
    private $quantidade_estoque;
    private $id_categoria;
    private $id_fornecedor;

    // --- Setters ---

    public function setId($id){
        $this->id = $id;
    }

    public function setNome($nome){
        $this->nome = $nome;
    }

    public function setDescricao($descricao){
        $this->descricao = $descricao;
    }

    public function setPrecoCusto($preco_custo){
        $this->preco_custo = $preco_custo;
    }

    public function setPrecoVenda($preco_venda){
        $this->preco_venda = $preco_venda;
    }

    public function setQuantidadeEstoque($quantidade_estoque){
        $this->quantidade_estoque = $quantidade_estoque;
    }

    public function setIdCategoria($id_categoria){
        $this->id_categoria = $id_categoria;
    }

    public function setIdFornecedor($id_fornecedor){
        $this->id_fornecedor = $id_fornecedor;
    }

    // --- Getters ---

    public function getId(){
        return $this->id;
    }

    public function getNome(){
        return $this->nome;
    }

    public function getDescricao(){
        return $this->descricao;
    }

    public function getPrecoCusto(){
        return $this->preco_custo;
    }

    public function getPrecoVenda(){
        return $this->preco_venda;
    }

    public function getQuantidadeEstoque(){
        return $this->quantidade_estoque;
    }

    public function getIdCategoria(){
        return $this->id_categoria;
    }

    public function getIdFornecedor(){
        return $this->id_fornecedor;
    }

    
}
