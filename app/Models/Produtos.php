<?php
// Arquivo: Models/Produtos.php

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


    // --- Métodos de Banco de Dados (Estáticos) ---

    public static function listarTodos($pdo)
    {
        $sql = "SELECT * FROM produtos ORDER BY nome ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById($pdo, $id)
    {
        $sql = "SELECT * FROM produtos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function salvar($pdo)
    {
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
                ':imagem3' => $this->getImagem3(),
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

    public function atualizar($pdo)
    {
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
                ':id' => $this->getId(),
                ':nome' => $this->getNome(),
                ':descricao' => $this->getDescricao(),
                ':preco_custo' => $this->getPrecoCusto(),
                ':preco_venda' => $this->getPrecoVenda(),
                ':quantidade_estoque' => $this->getQuantidadeEstoque(),
                ':id_categoria' => $this->getIdCategoria(),
                ':imagem1' => $this->getImagem1(),
                ':imagem2' => $this->getImagem2(),
                ':imagem3' => $this->getImagem3(),
            ]);
            
            return true;

        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar o produto: " . $e->getMessage());
        }
    }

    public function excluir($pdo)
    {
        try {
            $pdo->beginTransaction();

            // 1. Buscar os nomes dos arquivos de imagem antes de excluir o produto
            $produtoParaExcluir = self::findById($pdo, $this->getId());
            $imagensParaApagar = [
                $produtoParaExcluir['imagem1'],
                $produtoParaExcluir['imagem2'],
                $produtoParaExcluir['imagem3']
            ];

            // 2. Excluir o registro da tabela de produtos
            // O ON DELETE CASCADE cuidará das movimentações
            $sql = "DELETE FROM produtos WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $this->getId()]);

            // 3. Se a exclusão do banco de dados foi bem-sucedida, apagar os arquivos
            $caminhoUpload = __DIR__ . '/../../public/uploads/produtos/';
            foreach ($imagensParaApagar as $imagem) {
                if (!empty($imagem) && file_exists($caminhoUpload . $imagem)) {
                    unlink($caminhoUpload . $imagem);
                }
            }

            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            if ($e->getCode() == '23000') {
                throw new Exception("Não é possível excluir este produto, pois ele está associado a outros registos (ex: vendas).");
            }
            throw new Exception("Erro ao excluir o produto: " . $e->getMessage());
        }
    }

    /**
     * Adiciona uma quantidade ao estoque de um produto e registra a movimentação.
     */
    public static function adicionarEstoque($pdo, $id_produto, $quantidade, $observacao)
    {
        // Validação básica
        if (!is_numeric($quantidade) || $quantidade <= 0) {
            throw new Exception("A quantidade deve ser um número positivo.");
        }
        if (empty($observacao)) {
            $observacao = 'Entrada manual de estoque';
        }

        try {
            $pdo->beginTransaction();

            // 1. Atualiza a quantidade na tabela de produtos
            $sql_update = "UPDATE produtos SET quantidade_estoque = quantidade_estoque + :quantidade WHERE id = :id_produto";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([
                ':quantidade' => $quantidade,
                ':id_produto' => $id_produto
            ]);

            // 2. Insere o registro na tabela de movimentação
            $sql_mov = "INSERT INTO movimentacao_estoque (id_produto, tipo_movimentacao, quantidade, observacao) 
                        VALUES (:id_produto, 'ENTRADA', :quantidade, :observacao)";
            $stmt_mov = $pdo->prepare($sql_mov);
            $stmt_mov->execute([
                ':id_produto' => $id_produto,
                ':quantidade' => $quantidade,
                ':observacao' => $observacao
            ]);

            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            throw new Exception("Erro ao adicionar estoque: " . $e->getMessage());
        }
    }
}

