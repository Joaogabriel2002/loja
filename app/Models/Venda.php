<?php
// Arquivo: App/Models/Venda.php

class Venda
{
    /**
     * Processa e guarda uma venda completa no banco de dados, utilizando uma transação.
     *
     * @param PDO $pdo A instância da conexão com o banco de dados.
     * @param array $itensCarrinho Um array de itens do carrinho, cada um contendo 'id' e 'quantidade'.
     * @param float $valorTotal O valor total da venda.
     * @return int O ID da venda que foi criada.
     * @throws Exception Em caso de erro na transação.
     */
    public static function salvarVenda(PDO $pdo, array $itensCarrinho, float $valorTotal)
    {
        try {
            $pdo->beginTransaction();

            // 1. Inserir o registo principal na tabela 'vendas'
            $sqlVenda = "INSERT INTO vendas (valor_total, forma_pagamento) VALUES (:valor_total, :forma_pagamento)";
            $stmtVenda = $pdo->prepare($sqlVenda);
            $stmtVenda->execute([
                ':valor_total' => $valorTotal,
                ':forma_pagamento' => 'Dinheiro' // Pode ser alterado para ser dinâmico
            ]);
            $idVenda = $pdo->lastInsertId();

            // 2. Para cada item no carrinho, fazer as atualizações necessárias
            foreach ($itensCarrinho as $item) {
                // a. Inserir na tabela 'itens_venda'
                $sqlItem = "INSERT INTO itens_venda (id_venda, id_produto, quantidade, preco_unitario) 
                            VALUES (:id_venda, :id_produto, :quantidade, (SELECT preco_venda FROM produtos WHERE id = :id_produto_ref))";
                $stmtItem = $pdo->prepare($sqlItem);
                $stmtItem->execute([
                    ':id_venda' => $idVenda,
                    ':id_produto' => $item['id'],
                    ':quantidade' => $item['quantidade'],
                    ':id_produto_ref' => $item['id']
                ]);

                // b. Atualizar (dar baixa) o estoque na tabela 'produtos'
                $sqlEstoque = "UPDATE produtos SET quantidade_estoque = quantidade_estoque - :quantidade WHERE id = :id";
                $stmtEstoque = $pdo->prepare($sqlEstoque);
                $stmtEstoque->execute([
                    ':quantidade' => $item['quantidade'],
                    ':id' => $item['id']
                ]);

                // c. Registar a saída na tabela 'movimentacao_estoque'
                $sqlMov = "INSERT INTO movimentacao_estoque (id_produto, tipo_movimentacao, quantidade, observacao) 
                           VALUES (:id_produto, 'SAIDA', :quantidade, :observacao)";
                $stmtMov = $pdo->prepare($sqlMov);
                $stmtMov->execute([
                    ':id_produto' => $item['id'],
                    ':quantidade' => $item['quantidade'],
                    ':observacao' => 'Venda ID: ' . $idVenda
                ]);
            }

            $pdo->commit();
            return $idVenda;

        } catch (Exception $e) {
            $pdo->rollBack();
            // Lança a exceção para ser tratada pelo controlador
            throw new Exception("Erro ao finalizar a venda: " . $e->getMessage());
        }
    }
}
