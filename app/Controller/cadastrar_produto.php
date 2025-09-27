<?php
// Arquivo: Controller/cadastrar_produto.php

// 1. Verificamos se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Incluímos os arquivos necessários
    require_once __DIR__ . '/../Config/conexao.php';
    require_once __DIR__ . '/../Models/Produtos.php'; 

    // 3. Criamos a conexão com o banco de dados
    $conexao = new Conexao();
    $pdo = $conexao->getConn();

    // 4. Criamos um objeto Produto e o populamos com os dados do formulário
    $produto = new Produto();
    $produto->setNome($_POST['nome'] ?? '');
    $produto->setPrecoCusto(!empty($_POST['preco_custo']) ? $_POST['preco_custo'] : null);
    $produto->setPrecoVenda($_POST['preco_venda'] ?? 0);
    $produto->setQuantidadeEstoque($_POST['quantidade_estoque'] ?? 0);
    // $produto->setIdCategoria(!empty($_POST['id_categoria']) ? $_POST['id_categoria'] : null);
    $produto->setDescricao(!empty($_POST['descricao']) ? $_POST['descricao'] : null);

    // 5. Validação básica
    if (empty($produto->getNome()) || empty($produto->getPrecoVenda()) || $produto->getQuantidadeEstoque() === null) {
        $mensagemErro = "Erro: Campos obrigatórios (Nome, Preço de Venda, Estoque) não foram preenchidos.";
        // Redireciona de volta com a mensagem de erro
        header("Location: ../../public/Produtos/CadastrarProdutos.php?status=erro&msg=" . urlencode($mensagemErro));
        exit();
    }

    try {
        // 6. Tentamos salvar o produto
        $produto->salvar($pdo);

        // 7. Se deu certo, preparamos a mensagem de sucesso e redirecionamos
        $mensagemSucesso = "Produto '" . htmlspecialchars($produto->getNome()) . "' cadastrado com sucesso!";
        header("Location: ../../public/Produtos/CadastrarProdutos.php?status=sucesso&msg=" . urlencode($mensagemSucesso));
        exit();

    } catch (Exception $e) {
        // 8. Se deu erro, pegamos a mensagem da exceção e redirecionamos
        $mensagemErro = "Erro ao cadastrar o produto: " . $e->getMessage();
        header("Location: ../../public/Produtos/CadastrarProdutos.php?status=erro&msg=" . urlencode($mensagemErro));
        exit();
    }
} else {
    // Se o acesso não for via POST, redireciona para o formulário
    header("Location: ../../public/Produtos/CadastrarProdutos.php");
    exit();
}

