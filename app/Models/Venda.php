<?php
// Ficheiro: App/Models/Venda.php

class Venda
{
    // ... (atributos e getters/setters existentes) ...

    // --- MÉTODOS PARA O HISTÓRICO DE PEDIDOS ---

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
                JOIN produtos AS p ON iv.id_produto = p.id
                WHERE iv.id_venda = :id_venda";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_venda' => $idVenda]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ... (outros métodos como criar(), etc.) ...
}

