<?php
// Ficheiro: App/Models/Venda.php

class Venda
{
    // Atributos da Venda
    private $id;
    private $id_usuario;
    private $valor_total;
    private $data_hora;

    // --- Getters e Setters ---
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getIdUsuario() { return $this->id_usuario; }
    public function setIdUsuario($id_usuario) { $this->id_usuario = $id_usuario; }
    public function getValorTotal() { return $this->valor_total; }
    public function setValorTotal($valor_total) { $this->valor_total = $valor_total; }
    public function getDataHora() { return $this->data_hora; }
    public function setDataHora($data_hora) { $this->data_hora = $data_hora; }

    /**
     * Cria um novo registo de venda, os seus itens, e atualiza o estoque.
     * @param PDO $pdo A conexão com o banco de dados.
     * @param array $carrinho O array de produtos no carrinho.
     * @return bool True se a venda for criada com sucesso.
     * @throws Exception Se ocorrer um erro.
     */
    public function criar(PDO $pdo, array $carrinho)
    {
        try {
            $pdo->beginTransaction();

            $sqlVenda = "INSERT INTO vendas (id_usuario, valor_total) VALUES (:id_usuario, :valor_total)";
            $stmtVenda = $pdo->prepare($sqlVenda);
            $stmtVenda->execute([
                ':id_usuario' => $this->getIdUsuario(),
                ':valor_total' => $this->getValorTotal()
            ]);
            $idVendaInserida = $pdo->lastInsertId();

            $sqlItemVenda = "INSERT INTO itens_venda (id_venda, id_variacao, quantidade, preco_unitario_momento) VALUES (:id_venda, :id_variacao, :quantidade, :preco_unitario_momento)";
            $stmtItemVenda = $pdo->prepare($sqlItemVenda);

            $sqlAtualizaEstoque = "UPDATE variacoes_produto SET quantidade_estoque = quantidade_estoque - :quantidade WHERE id = :id_variacao";
            $stmtAtualizaEstoque = $pdo->prepare($sqlAtualizaEstoque);
            
            $sqlMovimentacao = "INSERT INTO movimentacao_estoque (id_produto, tipo_movimentacao, quantidade, observacao) VALUES (:id_produto, 'SAIDA', :quantidade, :observacao)";
            $stmtMovimentacao = $pdo->prepare($sqlMovimentacao);

            foreach ($carrinho as $item) {
                if (!isset($item['id_variacao'], $item['id_produto'], $item['quantidade'], $item['preco_venda'])) {
                    throw new Exception('Dados de um item no carrinho estão incompletos.');
                }
                $stmtItemVenda->execute([
                    ':id_venda' => $idVendaInserida,
                    ':id_variacao' => $item['id_variacao'],
                    ':quantidade' => $item['quantidade'],
                    ':preco_unitario_momento' => $item['preco_venda']
                ]);
                $stmtAtualizaEstoque->execute([
                    ':quantidade' => $item['quantidade'],
                    ':id_variacao' => $item['id_variacao']
                ]);
                $stmtMovimentacao->execute([
                    ':id_produto' => $item['id_produto'],
                    ':quantidade' => $item['quantidade'],
                    ':observacao' => 'Venda ID: ' . $idVendaInserida . ' | Variação ID: ' . $item['id_variacao']
                ]);
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw new Exception("Erro ao processar a venda: " . $e->getMessage());
        }
    }

    /**
     * Lista todas as vendas realizadas, juntando o nome do utilizador.
     * @param PDO $pdo A conexão com o banco de dados.
     * @return array A lista de todas as vendas.
     */
    public static function listarTodas(PDO $pdo): array
    {
        $sql = "SELECT v.id, v.data_hora, v.valor_total, u.email AS nome_usuario
                FROM vendas AS v
                JOIN usuarios AS u ON v.id_usuario = u.id
                ORDER BY v.data_hora DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca os itens detalhados de uma venda específica pelo seu ID.
     * @param PDO $pdo A conexão com o banco de dados.
     * @param int $idVenda O ID da venda a ser detalhada.
     * @return array A lista de itens da venda.
     */
    public static function buscarItensPorVendaId(PDO $pdo, int $idVenda): array
    {
        $sql = "SELECT p.nome, iv.quantidade, iv.preco_unitario_momento
                FROM itens_venda AS iv
                JOIN variacoes_produto AS vp ON iv.id_variacao = vp.id
                JOIN produtos AS p ON vp.id_produto = p.id
                WHERE iv.id_venda = :id_venda";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_venda' => $idVenda]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

