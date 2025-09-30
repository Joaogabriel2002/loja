<?php
// Ficheiro: App/Ajax/buscar_produtos.php
header('Content-Type: application/json');

require_once __DIR__ . '/../Config/Conexao.php';
require_once __DIR__ . '/../Models/Produtos.php';

$termo = $_GET['term'] ?? '';

// Evita buscas desnecessárias no banco de dados com termos muito curtos
if (strlen($termo) < 2) {
    echo json_encode([]);
    exit();
}

try {
    $conexao = new Conexao();
    $pdo = $conexao->getConn();
    
    // Chama o método estático que busca os produtos
    $produtos = Produto::buscarPorTermo($pdo, $termo);
    
    echo json_encode($produtos);

} catch (Exception $e) {
    // Em caso de erro, devolve uma resposta de erro do servidor
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao buscar produtos.']);
    error_log($e->getMessage()); // Regista o erro para depuração
}

