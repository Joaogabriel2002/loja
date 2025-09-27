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
     * NOVO MÉTODO: Lista os itens vendidos com detalhes de custo e preço para cálculo de lucro.
     * @param PDO $pdo A conexão com o banco de dados.
     * @return array A lista de itens vendidos com detalhes financeiros.
     */
    public static function listarVendasComLucro(PDO $pdo): array
    {
        $sql = "SELECT
                    v.data_hora,
                    p.nome AS nome_produto,
                    iv.quantidade,
                    p.preco_custo,
                    iv.preco_unitario_momento AS preco_venda
                FROM itens_venda AS iv
                JOIN vendas AS v ON iv.id_venda = v.id
                JOIN produtos AS p ON iv.id_produto = p.id
                ORDER BY v.data_hora DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

