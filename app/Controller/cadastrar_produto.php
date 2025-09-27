<?php
// Arquivo: Controller/cadastrar_produto.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    require_once __DIR__ . '/../Config/conexao.php';
    require_once __DIR__ . '/../Models/Produtos.php'; 

    $conexao = new Conexao();
    $pdo = $conexao->getConn();

    $produto = new Produto();
    // Preenche com dados do POST
    $produto->setNome($_POST['nome'] ?? '');
    $produto->setPrecoCusto(!empty($_POST['preco_custo']) ? $_POST['preco_custo'] : null);
    $produto->setPrecoVenda($_POST['preco_venda'] ?? 0);
    $produto->setQuantidadeEstoque($_POST['quantidade_estoque'] ?? 0);
    // $produto->setIdCategoria(!empty($_POST['id_categoria']) ? $_POST['id_categoria'] : null);
    $produto->setDescricao(!empty($_POST['descricao']) ? $_POST['descricao'] : null);
    
    // --- LÓGICA DE UPLOAD DE IMAGENS ---

    // Define o diretório de destino para as imagens.
    // O ideal é que esta pasta esteja dentro de 'public' para ser acessível pela web.
    $uploadDir = __DIR__ . '/../../public/uploads/produtos/';

    // Garante que o diretório de uploads exista.
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $nomesImagens = [];
    $camposImagem = ['imagem1', 'imagem2', 'imagem3'];

    foreach ($camposImagem as $campo) {
        // Verifica se o arquivo foi enviado e não houve erros
        if (isset($_FILES[$campo]) && $_FILES[$campo]['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES[$campo]['tmp_name'];
            $fileName = $_FILES[$campo]['name'];
            $fileSize = $_FILES[$campo]['size'];
            $fileType = $_FILES[$campo]['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Gera um novo nome de arquivo único para evitar sobreposições
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            
            // Define o caminho completo de destino do arquivo
            $dest_path = $uploadDir . $newFileName;

            // Move o arquivo do diretório temporário para o destino final
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $nomesImagens[$campo] = $newFileName;
            } else {
                // Se falhar ao mover o arquivo, redireciona com erro
                $mensagemErro = "Erro ao mover o arquivo de imagem '$fileName'.";
                header("Location: ../../public/Produtos/CadastrarProdutos.php?status=erro&msg=" . urlencode($mensagemErro));
                exit();
            }
        }
    }

    // Define os nomes das imagens no objeto produto, usando null se a imagem não foi enviada
    $produto->setImagem1($nomesImagens['imagem1'] ?? null);
    $produto->setImagem2($nomesImagens['imagem2'] ?? null);
    $produto->setImagem3($nomesImagens['imagem3'] ?? null);

    // --- FIM DA LÓGICA DE UPLOAD ---

    // Validação básica dos campos de texto
    if (empty($produto->getNome()) || empty($produto->getPrecoVenda()) || $produto->getQuantidadeEstoque() === null) {
        $mensagemErro = "Erro: Campos obrigatórios (Nome, Preço de Venda, Estoque) não foram preenchidos.";
        header("Location: ../../public/Produtos/CadastrarProdutos.php?status=erro&msg=" . urlencode($mensagemErro));
        exit();
    }

    try {
        // Tenta salvar o produto (incluindo os nomes das imagens)
        $produto->salvar($pdo);

        $mensagemSucesso = "Produto '" . htmlspecialchars($produto->getNome()) . "' cadastrado com sucesso!";
        header("Location: ../../public/Produtos/CadastrarProdutos.php?status=sucesso&msg=" . urlencode($mensagemSucesso));
        exit();

    } catch (Exception $e) {
        $mensagemErro = "Erro ao cadastrar o produto: " . $e->getMessage();
        header("Location: ../../public/Produtos/CadastrarProdutos.php?status=erro&msg=" . urlencode($mensagemErro));
        exit();
    }
} else {
    header("Location: ../../public/Produtos/CadastrarProdutos.php");
    exit();
}

