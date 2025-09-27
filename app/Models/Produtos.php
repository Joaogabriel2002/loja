<?php
// Ficheiro: App/Models/Produtos.php

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
    public function getId(){ return $this->id; }
    public function setId($id){ $this->id = $id; }
    public function getNome(){ return $this->nome; }
    public function setNome($nome){ $this->nome = $nome; }
    public function getDescricao(){ return $this->descricao; }
    public function setDescricao($descricao){ $this->descricao = $descricao; }
    public function getPrecoCusto(){ return $this->preco_custo; }
    public function setPrecoCusto($preco_custo){ $this->preco_custo = $preco_custo; }
    public function getPrecoVenda(){ return $this->preco_venda; }
    public function setPrecoVenda($preco_venda){ $this->preco_venda = $preco_venda; }
    public function getQuantidadeEstoque(){ return $this->quantidade_estoque; }
    public function setQuantidadeEstoque($quantidade_estoque){ $this->quantidade_estoque = $quantidade_estoque; }
    public function getIdCategoria(){ return $this->id_categoria; }
    public function setIdCategoria($id_categoria){ $this->id_categoria = $id_categoria; }
    public function getImagem1(){ return $this->imagem1; }
    public function setImagem1($imagem1){ $this->imagem1 = $imagem1; }
    public function getImagem2(){ return $this->imagem2; }
    public function setImagem2($imagem2){ $this->imagem2 = $imagem2; }
    public function getImagem3(){ return $this->imagem3; }
    public function setImagem3($imagem3){ $this->imagem3 = $imagem3; }

    // --- MÉTODOS DE ACESSO AO BANCO DE DADOS ---

    /**
     * Busca produtos por um termo, apenas com estoque positivo, para a Frente de Caixa.
     */
    public static function buscarPorTermo(PDO $pdo, string $termo)
    {
        $sql = "SELECT id, nome, preco_venda, quantidade_estoque 
                FROM produtos 
                WHERE nome LIKE :termo AND quantidade_estoque > 0
                LIMIT 10";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':termo' => '%' . $termo . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lista todos os produtos cadastrados.
     */
    public static function listarTodos(PDO $pdo) {
        $sql = "SELECT * FROM produtos ORDER BY nome ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Encontra um produto específico pelo seu ID.
     */
    public static function findById(PDO $pdo, int $id) {
        $sql = "SELECT * FROM produtos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dados) {
            $produto = new Produto();
            $produto->setId($dados['id']);
            $produto->setNome($dados['nome']);
            $produto->setDescricao($dados['descricao']);
            $produto->setPrecoCusto($dados['preco_custo']);
            $produto->setPrecoVenda($dados['preco_venda']);
            $produto->setQuantidadeEstoque($dados['quantidade_estoque']);
            $produto->setIdCategoria($dados['id_categoria']);
            $produto->setImagem1($dados['imagem1']);
            $produto->setImagem2($dados['imagem2']);
            $produto->setImagem3($dados['imagem3']);
            return $produto;
        }
        return null;
    }

    /**
     * Salva um novo produto no banco de dados.
     */
    public function salvar(PDO $pdo) {
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

    /**
     * Atualiza os dados de um produto existente.
     */
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
                ':id' => $this->getId(),
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
            return true;
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar o produto: " . $e->getMessage());
        }
    }

    /**
     * Exclui um produto do banco de dados e apaga as suas imagens do servidor.
     */
    public static function excluir(PDO $pdo, int $id) {
        try {
            $pdo->beginTransaction();

            $produto = self::findById($pdo, $id);
            if (!$produto) {
                throw new Exception("Produto não encontrado.");
            }

            // Apaga as imagens do servidor
            $caminhoBaseUploads = __DIR__ . '/../../public/uploads/produtos/';
            if ($produto->getImagem1() && file_exists($caminhoBaseUploads . $produto->getImagem1())) { unlink($caminhoBaseUploads . $produto->getImagem1()); }
            if ($produto->getImagem2() && file_exists($caminhoBaseUploads . $produto->getImagem2())) { unlink($caminhoBaseUploads . $produto->getImagem2()); }
            if ($produto->getImagem3() && file_exists($caminhoBaseUploads . $produto->getImagem3())) { unlink($caminhoBaseUploads . $produto->getImagem3()); }

            // Apaga o registo do banco de dados
            $sql = "DELETE FROM produtos WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            // Verifica se o erro é de violação de chave estrangeira
            if ($e->getCode() == '23000') {
                throw new Exception("Não é possível excluir este produto, pois ele está associado a outros registos (ex: vendas).");
            }
            throw new Exception("Erro ao excluir o produto: " . $e->getMessage());
        }
    }

    /**
     * Adiciona uma quantidade ao estoque do produto e regista a movimentação.
     */
    public function adicionarEstoque(PDO $pdo, int $quantidade, string $motivo) {
        if ($quantidade <= 0) {
            throw new Exception("A quantidade a ser adicionada deve ser positiva.");
        }
        try {
            $pdo->beginTransaction();
            
            // Atualiza a quantidade na tabela de produtos
            $sqlEstoque = "UPDATE produtos SET quantidade_estoque = quantidade_estoque + :quantidade WHERE id = :id";
            $stmtEstoque = $pdo->prepare($sqlEstoque);
            $stmtEstoque->execute([
                ':quantidade' => $quantidade,
                ':id' => $this->getId()
            ]);

            // Regista a entrada na tabela de movimentação
            $sqlMov = "INSERT INTO movimentacao_estoque (id_produto, tipo_movimentacao, quantidade, observacao) 
                       VALUES (:id_produto, 'ENTRADA', :quantidade, :observacao)";
            $stmtMov = $pdo->prepare($sqlMov);
            $stmtMov->execute([
                ':id_produto' => $this->getId(),
                ':quantidade' => $quantidade,
                ':observacao' => $motivo
            ]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw new Exception("Erro ao adicionar estoque: " . $e->getMessage());
        }
    }
}

