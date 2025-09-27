<?php
// Ficheiro: App/Models/Produtos.php

class Produto
{
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

    // --- Métodos de Banco de Dados ---

    public function salvar(PDO $pdo) {
        try {
            $sql = "INSERT INTO produtos (nome, descricao, preco_custo, preco_venda, quantidade_estoque, id_categoria, imagem1, imagem2, imagem3) 
                    VALUES (:nome, :descricao, :preco_custo, :preco_venda, :quantidade_estoque, :id_categoria, :imagem1, :imagem2, :imagem3)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
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
            $this->id = $pdo->lastInsertId();
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Erro ao salvar o produto.");
        }
    }

    public function atualizar(PDO $pdo) {
        try {
            $sql = "UPDATE produtos SET 
                        nome = :nome, 
                        descricao = :descricao, 
                        preco_custo = :preco_custo, 
                        preco_venda = :preco_venda, 
                        quantidade_estoque = :quantidade_estoque, 
                        id_categoria = :id_categoria,
                        imagem1 = :imagem1,
                        imagem2 = :imagem2,
                        imagem3 = :imagem3
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $this->getNome(),
                ':descricao' => $this->getDescricao(),
                ':preco_custo' => $this->getPrecoCusto(),
                ':preco_venda' => $this->getPrecoVenda(),
                ':quantidade_estoque' => $this->getQuantidadeEstoque(),
                ':id_categoria' => $this->getIdCategoria(),
                ':imagem1' => $this->getImagem1(),
                ':imagem2' => $this->getImagem2(),
                ':imagem3' => $this->getImagem3(),
                ':id' => $this->getId()
            ]);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Erro ao atualizar o produto.");
        }
    }

    public static function listarTodos(PDO $pdo): array {
        $stmt = $pdo->query("SELECT * FROM produtos ORDER BY nome ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById(PDO $pdo, int $id): ?array {
        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);
        return $produto ?: null;
    }
    
    public static function buscarPorTermo(PDO $pdo, string $termo): array {
        $stmt = $pdo->prepare("SELECT id, nome, preco_venda, quantidade_estoque FROM produtos WHERE nome LIKE ? AND quantidade_estoque > 0 LIMIT 10");
        $stmt->execute(['%' . $termo . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function excluir(PDO $pdo): bool {
        try {
            $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
            $stmt->execute([$this->id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            // Verifica se o erro é de restrição de chave estrangeira
            if ($e->getCode() == '23000') {
                throw new Exception("Não é possível excluir este produto, pois ele está associado a outros registos (ex: vendas).");
            }
            throw new Exception("Erro ao excluir o produto.");
        }
    }

    public static function adicionarEstoque(PDO $pdo, int $idProduto, int $quantidade, string $motivo): bool
    {
        if ($quantidade <= 0) {
            throw new Exception("A quantidade a ser adicionada deve ser positiva.");
        }

        try {
            $pdo->beginTransaction();

            $sql_update = "UPDATE produtos SET quantidade_estoque = quantidade_estoque + :quantidade WHERE id = :id_produto";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([
                ':quantidade' => $quantidade,
                ':id_produto' => $idProduto
            ]);

            $sql_mov = "INSERT INTO movimentacao_estoque (id_produto, tipo_movimentacao, quantidade, observacao) VALUES (:id_produto, 'ENTRADA', :quantidade, :observacao)";
            $stmt_mov = $pdo->prepare($sql_mov);
            $stmt_mov->execute([
                ':id_produto' => $idProduto,
                ':quantidade' => $quantidade,
                ':observacao' => $motivo
            ]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Erro ao adicionar estoque: " . $e->getMessage());
            throw new Exception("Não foi possível adicionar o estoque.");
        }
    }
}

