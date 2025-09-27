<?php
// Ficheiro: App/Models/Movimentacao.php

class Movimentacao
{
    /**
     * Lista todas as movimentações de SAÍDA que são vendas.
     * @param PDO $pdo A conexão com o banco de dados.
     * @return array A lista de movimentações de venda.
     */
    public static function listarMovimentacoesVenda(PDO $pdo): array
    {
        $sql = "SELECT m.*, p.nome as nome_produto
                FROM movimentacao_estoque m
                JOIN produtos p ON m.id_produto = p.id
                WHERE m.tipo_movimentacao = 'SAIDA'
                ORDER BY m.data_hora DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista todas as movimentações de estoque (Entrada e Saída).
     * @param PDO $pdo A conexão com o banco de dados.
     * @return array A lista de todas as movimentações.
     */
    public static function listarTodasMovimentacoes(PDO $pdo): array
    {
        $sql = "SELECT m.*, p.nome as nome_produto
                FROM movimentacao_estoque m
                JOIN produtos p ON m.id_produto = p.id
                ORDER BY m.data_hora DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista os itens vendidos com detalhes para cálculo de lucro, com filtro de período.
     * @param PDO $pdo A conexão com o banco de dados.
     * @param string $periodo O filtro de período ('hoje', 'semana', 'mes', 'sempre').
     * @return array A lista de itens vendidos com detalhes financeiros.
     */
    public static function listarVendasComLucro(PDO $pdo, string $periodo = 'sempre'): array
    {
        $sql = "SELECT
                    v.data_hora,
                    p.nome AS nome_produto,
                    iv.quantidade,
                    p.preco_custo,
                    iv.preco_unitario_momento AS preco_venda
                FROM itens_venda AS iv
                JOIN vendas AS v ON iv.id_venda = v.id
                JOIN produtos AS p ON iv.id_produto = p.id";

        // Adiciona a cláusula WHERE com base no período
        $whereClause = '';
        switch ($periodo) {
            case 'hoje':
                // Vendas feitas na data atual
                $whereClause = " WHERE DATE(v.data_hora) = CURDATE()";
                break;
            case 'semana':
                // Vendas feitas na semana atual (considerando a semana a começar na segunda-feira)
                $whereClause = " WHERE YEARWEEK(v.data_hora, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'mes':
                // Vendas feitas no mês e ano atuais
                $whereClause = " WHERE MONTH(v.data_hora) = MONTH(CURDATE()) AND YEAR(v.data_hora) = YEAR(CURDATE())";
                break;
        }

        $sql .= $whereClause . " ORDER BY v.data_hora DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

