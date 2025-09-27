<?php
// Ficheiro: Controller/excluir_produto.php

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    require_once __DIR__ . '/../Config/conexao.php';
    require_once __DIR__ . '/../Models/Produtos.php'; 

    $conexao = new Conexao();
    $pdo = $conexao->getConn();

    // 1. Pega o ID do produto da URL e valida
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        header("Location: ../../public/Produtos/ListarProdutos.php?status=erro&msg=" . urlencode("ID de produto inválido."));
        exit();
    }
    
    // 2. Tenta excluir o produto
    try {
        $nomeProduto = Produto::excluir($pdo, $id);

        $mensagemSucesso = "Produto '" . htmlspecialchars($nomeProduto) . "' excluído com sucesso!";
        header("Location: ../../public/Produtos/ListarProdutos.php?status=sucesso&msg=" . urlencode($mensagemSucesso));
        exit();

    } catch (Exception $e) {
        $mensagemErro = $e->getMessage();
        header("Location: ../../public/Produtos/ListarProdutos.php?status=erro&msg=" . urlencode($mensagemErro));
        exit();
    }
} else {
    // Se o acesso não for via GET, redireciona para a listagem
    header("Location: ../../public/Produtos/ListarProdutos.php");
    exit();
}
