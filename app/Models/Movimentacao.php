<?php
// Arquivo: App/Models/Movimentacao.php

class Movimentacao
{
    /**
     * Busca no banco de dados todas as movimentações de estoque do tipo 'SAIDA'.
     *
     * @param PDO $pdo A instância da conexão com o banco de dados.
     * @return array Retorna uma lista de movimentações de venda.
     */
    public static function listarMovimentacoesVenda($pdo)
    {
        $sql = "SELECT 
                    m.data_hora,
                    p.nome AS nome_produto,
                    m.quantidade,
                    m.observacao
                FROM 
                    movimentacao_estoque AS m
                JOIN 
                    produtos AS p ON m.id_produto = p.id
                WHERE 
                    m.tipo_movimentacao = 'SAIDA'
                ORDER BY 
                    m.data_hora DESC";
        
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca no banco de dados todas as movimentações de estoque (entradas e saídas).
     *
     * @param PDO $pdo A instância da conexão com o banco de dados.
     * @return array Retorna uma lista de todas as movimentações.
     */
    public static function listarTodasMovimentacoes($pdo)
    {
        $sql = "SELECT
                    m.data_hora,
                    p.nome AS nome_produto,
                    m.quantidade,
                    m.tipo_movimentacao,
                    m.observacao
                FROM
                    movimentacao_estoque AS m
                JOIN
                    produtos AS p ON m.id_produto = p.id
                ORDER BY
                    m.data_hora DESC";

        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

