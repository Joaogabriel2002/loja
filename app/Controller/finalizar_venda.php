<?php
// Ficheiro: App/Controller/finalizar_venda.php

// Estas linhas são importantes para garantir que apenas a nossa resposta JSON seja enviada,
// suprimindo quaisquer avisos ou erros de PHP que possam "sujar" a saída.
error_reporting(0);
ini_set('display_errors', 0);

session_start();
header('Content-Type: application/json');

// Ficheiros necessários
require_once __DIR__ . '/../Config/conexao.php';
require_once __DIR__ . '/../Models/Venda.php';

// Obtém os dados que o JavaScript enviou
$dados = json_decode(file_get_contents('php://input'), true);
$carrinho = $dados['carrinho'] ?? [];
$totalVenda = $dados['total'] ?? 0;

// Validação inicial dos dados recebidos
if (empty($carrinho) || $totalVenda <= 0) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'O carrinho está vazio ou o total é inválido.']);
    exit();
}

try {
    // Inicia a conexão com a base de dados
    $conexao = new Conexao();
    $pdo = $conexao->getConn();

    // Obtém o ID do utilizador da sessão. Se não estiver logado, usa o ID 1 como fallback.
    // Garanta que tem um utilizador com id=1 na sua tabela 'usuarios' para testes.
    $id_usuario = $_SESSION['usuario_id'] ?? 1; 
    if (!$id_usuario) {
        throw new Exception("ID de utilizador inválido. Não foi possível registar a venda.");
    }

    // Cria um novo objeto de Venda
    $venda = new Venda();
    $venda->setIdUsuario($id_usuario);
    $venda->setValorTotal($totalVenda);

    // O método 'criar' fará todo o trabalho pesado:
    // 1. Guarda a venda
    // 2. Guarda os itens
    // 3. Atualiza o estoque
    // 4. Regista a movimentação
    $venda->criar($pdo, $carrinho);

    // Se tudo correu bem, envia uma resposta de sucesso
    echo json_encode(['sucesso' => true, 'mensagem' => 'Venda finalizada com sucesso!']);

} catch (Exception $e) {
    // Se ocorrer um erro durante o processo, regista o erro para depuração
    error_log("Erro em finalizar_venda.php: " . $e->getMessage());
    // E envia uma mensagem de erro para o utilizador
    echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
}

