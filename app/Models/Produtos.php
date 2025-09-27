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
    private $imagem1;
    private $imagem2;
    private $imagem3;

    // --- Getters e Setters ---
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
    public function getImagem1() { return $this->imagem1; }
    public function setImagem1($imagem1) { $this->imagem1 = $imagem1; }
    public function getImagem2() { return $this->imagem2; }
    public function setImagem2($imagem2) { $this->imagem2 = $imagem2; }
    public function getImagem3() { return $this->imagem3; }
    public function setImagem3($imagem3) { $this->imagem3 = $imagem3; }

    
    public function salvar($pdo) {
        try {
            $pdo->beginTransaction();

            $sqlProduto = "INSERT INTO produtos (nome, descricao, preco_custo, preco_venda, quantidade_estoque, id_categoria, imagem1, imagem2, imagem3) 
                           VALUES (:nome, :descricao, :preco_custo, :preco_venda, :quantidade_estoque, :id_categoria, :imagem1, :imagem2, :imagem3)";
            
            $stmtProduto = $pdo->prepare($sqlProduto);

            $stmtProduto->execute([
                ':nome' => $this->getNome(),
                ':descricao' => $this->getDescricao(),
                ':preco_custo' => $this->getPrecoCusto(),
                ':preco_venda' => $this->getPrecoVenda(),
                ':quantidade_estoque' => $this->getQuantidadeEstoque(),
                ':id_categoria' => $this->getIdCategoria(),
                ':imagem1' => $this->getImagem1(),
                ':imagem2' => $this->getImagem2(),
                ':imagem3' => $this->getImagem3()
            ]);

            $id_produto_inserido = $pdo->lastInsertId();

            if ($this->getQuantidadeEstoque() > 0) {
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

    public function atualizar($pdo) {
        try {
            $pdo->beginTransaction();

            $sql = "UPDATE produtos SET 
                        nome = :nome, 
                        descricao = :descricao, 
                        preco_custo = :preco_custo, 
                        preco_venda = :preco_venda, 
                        quantidade_estoque = :quantidade_estoque, 
                        id_categoria = :id_categoria";

            $params = [
                ':nome' => $this->getNome(),
                ':descricao' => $this->getDescricao(),
                ':preco_custo' => $this->getPrecoCusto(),
                ':preco_venda' => $this->getPrecoVenda(),
                ':quantidade_estoque' => $this->getQuantidadeEstoque(),
                ':id_categoria' => $this->getIdCategoria(),
                ':id' => $this->getId()
            ];

            if ($this->getImagem1() !== null) {
                $sql .= ", imagem1 = :imagem1";
                $params[':imagem1'] = $this->getImagem1();
            }
            if ($this->getImagem2() !== null) {
                $sql .= ", imagem2 = :imagem2";
                $params[':imagem2'] = $this->getImagem2();
            }
            if ($this->getImagem3() !== null) {
                $sql .= ", imagem3 = :imagem3";
                $params[':imagem3'] = $this->getImagem3();
            }

            $sql .= " WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $pdo->commit();
            return true;

        } catch (PDOException $e) {
            $pdo->rollBack();
            throw new Exception("Erro ao atualizar o produto: " . $e->getMessage());
        }
    }

    public static function findById($pdo, $id) {
        try {
            $sql = "SELECT * FROM produtos WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar produto: " . $e->getMessage());
        }
    }

    public static function listarTodos($pdo) {
        try {
            $sql = "SELECT * FROM produtos ORDER BY nome ASC";
            $stmt = $pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar produtos: " . $e->getMessage());
        }
    }
}

