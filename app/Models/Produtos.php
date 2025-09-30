<?php
// Ficheiro: App/Models/Produtos.php
// Versão simplificada, sem o sistema de variações.

class Produto
{
    // Atributos do produto único
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

    /**
     * Salva o produto no banco de dados e regista a movimentação inicial.
     */
    public function salvar(PDO $pdo)
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
                ':imagem3' => $this->getImagem3()
            ]);
            $idProdutoInserido = $pdo->lastInsertId();

            if ($this->getQuantidadeEstoque() > 0) {
                $sqlMov = "INSERT INTO movimentacao_estoque (id_produto, tipo_movimentacao, quantidade, observacao) 
                           VALUES (:id_produto, 'ENTRADA', :quantidade, 'Cadastro Inicial do Produto')";
                $stmtMov = $pdo->prepare($sqlMov);
                $stmtMov->execute([':id_produto' => $idProdutoInserido, ':quantidade' => $this->getQuantidadeEstoque()]);
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw new Exception("Erro ao salvar o produto: " . $e->getMessage());
        }
    }

    /**
     * Atualiza o produto no banco de dados.
     */
    public function atualizar(PDO $pdo)
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

        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar o produto: " . $e->getMessage());
        }
    }

    // --- MÉTODOS ESTÁTICOS ---

    /**
     * Exclui um produto e as suas imagens, com todas as validações.
     */
    public static function excluir(PDO $pdo, int $id): string
    {
        try {
            $pdo->beginTransaction();

            $produto = self::findById($pdo, $id);
            if (!$produto) {
                throw new Exception("Produto não encontrado.");
            }
            $nomeProduto = $produto['nome'];

            // Verifica se o produto está em alguma venda
            $sqlCheckVendas = "SELECT COUNT(*) FROM itens_venda WHERE id_produto = :id";
            $stmtCheckVendas = $pdo->prepare($sqlCheckVendas);
            $stmtCheckVendas->execute([':id' => $id]);
            if ($stmtCheckVendas->fetchColumn() > 0) {
                throw new Exception("Não é possível excluir este produto, pois ele está associado a vendas existentes.");
            }

            // Apaga o histórico de movimentação deste produto
            $sqlDeleteMov = "DELETE FROM movimentacao_estoque WHERE id_produto = :id";
            $stmtDeleteMov = $pdo->prepare($sqlDeleteMov);
            $stmtDeleteMov->execute([':id' => $id]);

            // Apaga as imagens do servidor
            $pastaUpload = __DIR__ . '/../../../public/uploads/produtos/';
            for ($i = 1; $i <= 3; $i++) {
                $imagem = $produto['imagem' . $i];
                if ($imagem && file_exists($pastaUpload . $imagem)) {
                    unlink($pastaUpload . $imagem);
                }
            }

            // Apaga o produto
            $sqlDeleteProd = "DELETE FROM produtos WHERE id = :id";
            $stmtDeleteProd = $pdo->prepare($sqlDeleteProd);
            $stmtDeleteProd->execute([':id' => $id]);

            $pdo->commit();
            return $nomeProduto;

        } catch (Exception $e) {
            $pdo->rollBack();
            throw new Exception("Erro ao excluir o produto: " . $e->getMessage());
        }
    }

    /**
     * Lista todos os produtos.
     */
    public static function listarTodos(PDO $pdo): array
    {
        $sql = "SELECT p.*, c.nome AS nome_categoria FROM produtos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id 
                ORDER BY p.nome ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um produto pelo ID.
     */
    public static function findById(PDO $pdo, int $id): ?array
    {
        $sql = "SELECT * FROM produtos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Busca produtos por um termo para a Frente de Caixa.
     */
    public static function buscarPorTermo(PDO $pdo, string $termo): array
    {
        $termoBusca = '%' . $termo . '%';
        $sql = "SELECT id, nome, preco_venda, quantidade_estoque FROM produtos 
                WHERE nome LIKE :termo AND quantidade_estoque > 0 LIMIT 10";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':termo' => $termoBusca]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Adiciona estoque a um produto.
     */
    public static function adicionarEstoque(PDO $pdo, int $id_produto, int $quantidade, string $observacao)
    {
        if ($quantidade <= 0) {
            throw new Exception("A quantidade deve ser positiva.");
        }
        try {
            $pdo->beginTransaction();

            $sqlEstoque = "UPDATE produtos SET quantidade_estoque = quantidade_estoque + :quantidade WHERE id = :id_produto";
            $stmtEstoque = $pdo->prepare($sqlEstoque);
            $stmtEstoque->execute([':quantidade' => $quantidade, ':id_produto' => $id_produto]);

            $sqlMov = "INSERT INTO movimentacao_estoque (id_produto, tipo_movimentacao, quantidade, observacao) 
                       VALUES (:id_produto, 'ENTRADA', :quantidade, :observacao)";
            $stmtMov = $pdo->prepare($sqlMov);
            $stmtMov->execute([':id_produto' => $id_produto, ':quantidade' => $quantidade, ':observacao' => $observacao]);

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw new Exception("Erro ao adicionar estoque: " . $e->getMessage());
        }
    }
}

