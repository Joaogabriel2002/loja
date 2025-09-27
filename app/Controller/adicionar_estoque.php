<?php
// Arquivo: Controller/adicionar_estoque.php

// Verificamos se a requisição é do tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Incluímos os ficheiros essenciais
    require_once __DIR__ . '/../Config/conexao.php';
    require_once __DIR__ . '/../Models/Produtos.php';

    // Capturamos os dados do formulário do modal
    $id_produto = $_POST['id_produto_estoque'] ?? null;
    $quantidade = $_POST['quantidade_adicionar'] ?? 0;
    $observacao = $_POST['observacao_estoque'] ?? '';

    // Validação
    if (empty($id_produto) || empty($quantidade)) {
        $mensagemErro = "ID do produto ou quantidade inválida.";
        header("Location: ../../public/Produtos/ListarProdutos.php?status=erro&msg=" . urlencode($mensagemErro));
        exit();
    }

    try {
        // Criamos a conexão com o banco
        $conexao = new Conexao();
        $pdo = $conexao->getConn();

        // Chamamos o método estático para adicionar estoque
        Produto::adicionarEstoque($pdo, $id_produto, $quantidade, $observacao);

        // Preparamos a mensagem de sucesso e redirecionamos
        $mensagemSucesso = "Estoque atualizado com sucesso!";
        header("Location: ../../public/Produtos/ListarProdutos.php?status=sucesso&msg=" . urlencode($mensagemSucesso));
        exit();

    } catch (Exception $e) {
        // Se deu erro, pegamos a mensagem da exceção e redirecionamos
        $mensagemErro = $e->getMessage();
        header("Location: ../../public/Produtos/ListarProdutos.php?status=erro&msg=" . urlencode($mensagemErro));
        exit();
    }

} else {
    // Se o acesso não for via POST, redireciona para a listagem
    header("Location: ../../public/Produtos/ListarProdutos.php");
    exit();
}
