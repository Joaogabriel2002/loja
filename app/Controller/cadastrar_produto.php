<?php
// Arquivo: Controller/cadastrar_produto.php

// 1. Verificamos se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Incluímos os arquivos necessários
    require_once __DIR__ . '/../Config/conexao.php';
    require_once __DIR__ . '/../Models/Produtos.php'; 

    // --- CORREÇÃO AQUI ---
    // 3. Criamos uma instância da classe de conexão
    $conexao = new Conexao();
    // 4. Obtém o objeto PDO para ser usado nas operações
    $pdo = $conexao->getConn();
    // ---------------------

    // 5. Criamos um objeto Produto e o populamos com os dados do formulário
    $produto = new Produto();
    $produto->setNome($_POST['nome'] ?? '');
    $produto->setPrecoCusto(!empty($_POST['preco_custo']) ? $_POST['preco_custo'] : null);
    $produto->setPrecoVenda($_POST['preco_venda'] ?? 0);
    $produto->setQuantidadeEstoque($_POST['quantidade_estoque'] ?? 0);
    // $produto->setIdCategoria(!empty($_POST['id_categoria']) ? $_POST['id_categoria'] : null);
    $produto->setDescricao(!empty($_POST['descricao']) ? $_POST['descricao'] : null);

    // 6. Validação básica
    if (empty($produto->getNome()) || empty($produto->getPrecoVenda()) || $produto->getQuantidadeEstoque() === null) {
        die("Erro: Campos obrigatórios não foram preenchidos.");
    }

    try {
        // 7. Chamamos o método salvar, passando a conexão PDO como parâmetro
        $produto->salvar($pdo);

        // 8. Se o método salvar() não lançou exceção, o cadastro foi um sucesso
        header("Location: ../../form_cadastro.html?status=sucesso");
        exit();

    } catch (Exception $e) {
        // 9. Se o método salvar() lançou uma exceção, capturamos e exibimos o erro
        die("Erro ao cadastrar o produto: " . $e->getMessage());
    }
} else {
    // Se o acesso não for via POST, redireciona para o formulário
    header("Location: ../../form_cadastro.html");
    exit();
}

