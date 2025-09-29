<?php
// Ficheiro: App/Controller/finalizar_venda.php

session_start();
header('Content-Type: application/json');

// Ficheiros necessários
require_once __DIR__ . '/../Config/conexao.php';
require_once __DIR__ . '/../Models/Venda.php';
require_once __DIR__ . '/../Models/Produtos.php';

// Obtém os dados enviados via POST (em formato JSON)
$dados = json_decode(file_get_contents('php://input'), true);

$carrinho = $dados['carrinho'] ?? [];
$totalVenda = $dados['total'] ?? 0;

if (empty($carrinho) || $totalVenda <= 0) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Carrinho vazio ou total inválido.']);
    exit();
}

try {
    $conexao = new Conexao();
    $pdo = $conexao->getConn();

    
    $id_usuario = $_SESSION['usuario_id'] ?? 1;

    // Validação do utilizador (mesmo com o fallback)
    if (!$id_usuario) {
        throw new Exception("ID de utilizador inválido. Não foi possível registar a venda.");
    }

    // Cria e configura o objeto Venda
    $venda = new Venda();
    $venda->setIdUsuario($id_usuario);
    $venda->setValorTotal($totalVenda);

    // Chama o método para criar a venda e os seus itens no banco de dados
    $venda->criar($pdo, $carrinho);

    // Se tudo correu bem, envia uma resposta de sucesso
    echo json_encode(['sucesso' => true, 'mensagem' => 'Venda finalizada com sucesso!']);

} catch (Exception $e) {
    // Em caso de erro, regista o erro e envia uma resposta de falha
    error_log("Erro em finalizar_venda.php: " . $e->getMessage());
    echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
}

?>