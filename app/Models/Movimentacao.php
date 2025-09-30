<?php
// Ficheiro: App/Models/Movimentacao.php
// Versão revertida para o modelo simples, sem variações.

class Movimentacao
{
    /**
     * Lista todas as movimentações de estoque (Entrada e Saída), com filtro de período.
     * @param PDO $pdo A conexão com o banco de dados.
     * @param string $periodo O filtro de período ('hoje', 'semana', 'mes', 'sempre').
     * @return array A lista de todas as movimentações.
     */
    public static function listarTodasMovimentacoes(PDO $pdo, string $periodo = 'sempre'): array
    {
        $sql = "SELECT 
                    m.data_hora,
                    m.tipo_movimentacao,
                    m.quantidade,
                    m.observacao,
                    p.nome as nome_produto
                FROM movimentacao_estoque m
                JOIN produtos p ON m.id_produto = p.id";

        $whereClause = '';
        switch ($periodo) {
            case 'hoje':
                $whereClause = " WHERE DATE(m.data_hora) = CURDATE()";
                break;
            case 'semana':
                $whereClause = " WHERE YEARWEEK(m.data_hora, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'mes':
                $whereClause = " WHERE MONTH(m.data_hora) = MONTH(CURDATE()) AND YEAR(m.data_hora) = YEAR(CURDATE())";
                break;
        }

        $sql .= $whereClause . " ORDER BY m.data_hora DESC";
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
}

