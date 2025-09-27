<?php
// Arquivo: Controller/editar_produto.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    require_once __DIR__ . '/../Config/conexao.php';
    require_once __DIR__ . '/../Models/Produtos.php'; 

    $conexao = new Conexao();
    $pdo = $conexao->getConn();

    // 1. Pega o ID do produto do campo oculto do formulário
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        header("Location: ../../public/Produtos/ListarProdutos.php?status=erro&msg=" . urlencode("ID de produto inválido."));
        exit();
    }

    // 2. Cria um objeto Produto e o popula com os dados do formulário
    $produto = new Produto();
    $produto->setId($id);
    $produto->setNome($_POST['nome'] ?? '');
    $produto->setPrecoCusto(!empty($_POST['preco_custo']) ? $_POST['preco_custo'] : null);
    $produto->setPrecoVenda($_POST['preco_venda'] ?? 0);
    $produto->setQuantidadeEstoque($_POST['quantidade_estoque'] ?? 0);
    $produto->setIdCategoria(!empty($_POST['id_categoria']) ? $_POST['id_categoria'] : null);
    $produto->setDescricao(!empty($_POST['descricao']) ? $_POST['descricao'] : null);

    // 3. Processa o upload de imagens (lógica similar à de cadastro)
    $diretorioUpload = __DIR__ . '/../../public/uploads/produtos/';

    for ($i = 1; $i <= 3; $i++) {
        $nomeCampo = 'imagem' . $i;
        if (isset($_FILES[$nomeCampo]) && $_FILES[$nomeCampo]['error'] === UPLOAD_ERR_OK) {
            $nomeArquivo = uniqid('', true) . '_' . basename($_FILES[$nomeCampo]['name']);
            $caminhoCompleto = $diretorioUpload . $nomeArquivo;

            // Move o arquivo para a pasta de uploads
            if (move_uploaded_file($_FILES[$nomeCampo]['tmp_name'], $caminhoCompleto)) {
                // Seta o nome do arquivo no objeto
                $metodoSet = 'setImagem' . $i;
                $produto->$metodoSet($nomeArquivo);
            }
        }
    }
    
    // 4. Tenta atualizar o produto no banco de dados
    try {
        $produto->atualizar($pdo);

        $mensagemSucesso = "Produto '" . htmlspecialchars($produto->getNome()) . "' atualizado com sucesso!";
        header("Location: ../../public/Produtos/ListarProdutos.php?status=sucesso&msg=" . urlencode($mensagemSucesso));
        exit();

    } catch (Exception $e) {
        $mensagemErro = "Erro ao atualizar o produto: " . $e->getMessage();
        header("Location: ../../public/Produtos/EditarProduto.php?id=$id&status=erro&msg=" . urlencode($mensagemErro));
        exit();
    }
} else {
    // Se o acesso não for via POST, redireciona para a listagem
    header("Location: ../../public/Produtos/ListarProdutos.php");
    exit();
}
