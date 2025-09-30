<?php
// Ficheiro: App/Models/Venda.php

class Venda
{
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
     * Cria a venda, os seus itens, atualiza o estoque e regista a movimentação.
     */
    public function criar(PDO $pdo, array $carrinho)
    {
        try {
            $pdo->beginTransaction();

            $sqlVenda = "INSERT INTO vendas (id_usuario, valor_total) VALUES (:id_usuario, :valor_total)";
            $stmtVenda = $pdo->prepare($sqlVenda);
            $stmtVenda->execute([':id_usuario' => $this->getIdUsuario(), ':valor_total' => $this->getValorTotal()]);
            $idVendaInserida = $pdo->lastInsertId();

            $sqlItem = "INSERT INTO itens_venda (id_venda, id_produto, quantidade, preco_unitario_momento) VALUES (:id_venda, :id_produto, :quantidade, :preco)";
            $stmtItem = $pdo->prepare($sqlItem);

            $sqlEstoque = "UPDATE produtos SET quantidade_estoque = quantidade_estoque - :quantidade WHERE id = :id";
            $stmtEstoque = $pdo->prepare($sqlEstoque);

            $sqlMov = "INSERT INTO movimentacao_estoque (id_produto, tipo_movimentacao, quantidade, observacao) VALUES (:id_produto, 'SAIDA', :quantidade, :observacao)";
            $stmtMov = $pdo->prepare($sqlMov);

            foreach ($carrinho as $item) {
                if (!isset($item['id'], $item['quantidade'], $item['preco_venda'])) {
                    throw new Exception('Dados de um item no carrinho estão incompletos.');
                }
                $stmtItem->execute([
                    ':id_venda' => $idVendaInserida,
                    ':id_produto' => $item['id'],
                    ':quantidade' => $item['quantidade'],
                    ':preco' => $item['preco_venda']
                ]);
                $stmtEstoque->execute([':quantidade' => $item['quantidade'], ':id' => $item['id']]);
                $stmtMov->execute([':id_produto' => $item['id'], ':quantidade' => $item['quantidade'], ':observacao' => 'Venda ID: ' . $idVendaInserida]);
            }

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            // Re-lança a exceção para que o controlador a possa apanhar e reportar o erro
            throw new Exception("Erro ao processar a venda: " . $e->getMessage());
        }
    }

    /**
     * Lista todas as vendas realizadas, com filtro de período.
     * @param PDO $pdo A conexão com o banco de dados.
     * @param string $periodo O filtro de período ('hoje', 'semana', 'mes', 'sempre').
     * @return array A lista de todas as vendas.
     */
    public static function listarTodas(PDO $pdo, string $periodo = 'sempre'): array
    {
        $sql = "SELECT v.id, v.data_hora, v.valor_total, u.email AS nome_usuario
                FROM vendas AS v
                JOIN usuarios AS u ON v.id_usuario = u.id";
        
        $whereClause = '';
        switch ($periodo) {
            case 'hoje':
                $whereClause = " WHERE DATE(v.data_hora) = CURDATE()";
                break;
            case 'semana':
                $whereClause = " WHERE YEARWEEK(v.data_hora, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'mes':
                $whereClause = " WHERE MONTH(v.data_hora) = MONTH(CURDATE()) AND YEAR(v.data_hora) = YEAR(CURDATE())";
                break;
        }

        $sql .= $whereClause . " ORDER BY v.data_hora DESC";
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
                JOIN produtos AS p ON iv.id_produto = p.id
                WHERE iv.id_venda = :id_venda";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_venda' => $idVenda]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

