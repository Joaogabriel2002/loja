<?php
// Arquivo: Models/Produtos.php

class Produto {
    // Atributos
    private $id;
    private $nome;
    private $descricao;
    private $preco_custo;
    private $preco_venda;
    private $quantidade_estoque;
    private $id_categoria;

    // --- Getters e Setters (mesmos de antes) ---
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getNome() { return $this->nome; }
    public function setNome($nome) { $this->nome = $nome; }
    public function getDescricao() { return $this->descricao; }
    public function setDescricao($descricao) { $this->descricao = $descricao; }
    public function getPrecoCusto() { return $this->preco_custo; }
    public function setPrecoCusto($preco_custo) { $this->preco_custo = $preco_custo; }
    public function getPrecoVenda() { return $this->preco_venda; }
    public function setPrecoVenda($preco_venda) { $this->preco_venda = $preco_venda; }
    public function getQuantidadeEstoque() { return $this->quantidade_estoque; }
    public function setQuantidadeEstoque($quantidade_estoque) { $this->quantidade_estoque = $quantidade_estoque; }
    public function getIdCategoria() { return $this->id_categoria; }
    public function setIdCategoria($id_categoria) { $this->id_categoria = $id_categoria; }

    /**
     * Salva o produto atual no banco de dados.
     * Inclui a inserção na tabela 'produtos' e o registro inicial na 'movimentacao_estoque'.
     *
     * @param PDO $pdo A instância da conexão com o banco de dados.
     * @return bool Retorna true em caso de sucesso, ou lança uma exceção em caso de erro.
     */
    public function salvar($pdo) {
        try {
            $pdo->beginTransaction();

            $sqlProduto = "INSERT INTO produtos (nome, descricao, preco_custo, preco_venda, quantidade_estoque, id_categoria) 
                           VALUES (:nome, :descricao, :preco_custo, :preco_venda, :quantidade_estoque, :id_categoria)";
            
            $stmtProduto = $pdo->prepare($sqlProduto);

            $stmtProduto->execute([
                ':nome' => $this->getNome(),
                ':descricao' => $this->getDescricao(),
                ':preco_custo' => $this->getPrecoCusto(),
                ':preco_venda' => $this->getPrecoVenda(),
                ':quantidade_estoque' => $this->getQuantidadeEstoque(),
                ':id_categoria' => $this->getIdCategoria()
            ]);

            $id_produto_inserido = $pdo->lastInsertId();

            if ($this->getQuantidadeEstoque() > 0) {
                // --- CORREÇÃO AQUI ---
                // Alterado 'motivo' para 'observacao' para corresponder ao banco de dados
                $sqlMovimentacao = "INSERT INTO movimentacao_estoque (id_produto, tipo_movimentacao, quantidade, observacao) 
                                    VALUES (:id_produto, 'ENTRADA', :quantidade, 'Cadastro Inicial do Produto')";
                
                $stmtMovimentacao = $pdo->prepare($sqlMovimentacao);

                $stmtMovimentacao->execute([
                    ':id_produto' => $id_produto_inserido,
                    ':quantidade' => $this->getQuantidadeEstoque()
                ]);
            }

            $pdo->commit();
            return true;

        } catch (PDOException $e) {
            $pdo->rollBack();
            throw new Exception("Erro ao salvar o produto no banco de dados: " . $e->getMessage());
        }
    }
}

