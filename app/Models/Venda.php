<?php
// Ficheiro: App/Models/Venda.php

class Venda {
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
    public function criar(PDO $pdo, array $carrinho) {
        try {
            $pdo->beginTransaction();

            // 1. Inserir o registo principal na tabela 'vendas'
            $sqlVenda = "INSERT INTO vendas (id_usuario, valor_total) VALUES (:id_usuario, :valor_total)";
            $stmtVenda = $pdo->prepare($sqlVenda);
            $stmtVenda->execute([
                ':id_usuario' => $this->getIdUsuario(),
                ':valor_total' => $this->getValorTotal()
            ]);
            $idVendaInserida = $pdo->lastInsertId();

            // Prepara as queries que serão usadas dentro do loop
            $sqlItemVenda = "INSERT INTO itens_venda (id_venda, id_produto, quantidade, preco_unitario_momento) VALUES (:id_venda, :id_produto, :quantidade, :preco_unitario_momento)";
            $stmtItemVenda = $pdo->prepare($sqlItemVenda);

            $sqlAtualizaEstoque = "UPDATE produtos SET quantidade_estoque = quantidade_estoque - :quantidade WHERE id = :id_produto";
            $stmtAtualizaEstoque = $pdo->prepare($sqlAtualizaEstoque);

            $sqlMovimentacao = "INSERT INTO movimentacao_estoque (id_produto, tipo_movimentacao, quantidade, observacao) VALUES (:id_produto, 'SAIDA', :quantidade, :observacao)";
            $stmtMovimentacao = $pdo->prepare($sqlMovimentacao);

            // 2. Iterar sobre cada item do carrinho para o inserir e atualizar o estoque
            foreach ($carrinho as $item) {
                // --- NOVA VALIDAÇÃO DE SEGURANÇA ---
                // Verifica se os dados essenciais do item existem antes de prosseguir.
                if (!isset($item['id']) || !isset($item['quantidade']) || !isset($item['preco_venda'])) {
                    throw new Exception("Dados de um item no carrinho estão incompletos. Verifique o preço e a quantidade.");
                }

                // Inserir na tabela 'itens_venda'
                $stmtItemVenda->execute([
                    ':id_venda' => $idVendaInserida,
                    ':id_produto' => $item['id'],
                    ':quantidade' => $item['quantidade'],
                    ':preco_unitario_momento' => $item['preco_venda']
                ]);

                // Atualizar o estoque na tabela 'produtos'
                $stmtAtualizaEstoque->execute([
                    ':quantidade' => $item['quantidade'],
                    ':id_produto' => $item['id']
                ]);

                // Registar a saída na tabela 'movimentacao_estoque'
                $stmtMovimentacao->execute([
                    ':id_produto' => $item['id'],
                    ':quantidade' => $item['quantidade'],
                    ':observacao' => 'Venda ID: ' . $idVendaInserida
                ]);
            }

            // 3. Se tudo correu bem, confirma as alterações
            $pdo->commit();
            return true;

        } catch (Exception $e) {
            // 4. Se algo deu errado, desfaz tudo
            $pdo->rollBack();
            throw new Exception("Erro ao processar a venda: " . $e->getMessage());
        }
    }
}

