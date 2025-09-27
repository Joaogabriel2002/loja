<?php
session_start();

// Redireciona se não estiver logado
// if (!isset($_SESSION['usuario_id'])) {
//     header('Location: index.php');
//     exit();
// }

// $email = htmlspecialchars($_SESSION['usuario'] ?? 'Visitante');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

    <!-- Conteúdo Principal -->
    <div class="min-h-screen flex flex-col">
        <!-- Cabeçalho -->
        <header class="bg-white shadow-md p-4">
            <div class="container mx-auto flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                <div>
                    <button onclick="history.back()" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-lg transition-colors duration-300" title="Voltar">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </button>
                    <!-- <a href="logout.php" class="ml-4 text-red-500 hover:text-red-700">Sair</a> -->
                </div>
            </div>
        </header>

        <!-- Corpo do Dashboard -->
        <main class="flex-grow container mx-auto p-4 md:p-8">
            <div class="text-left mb-10">
                <h2 class="text-3xl font-semibold text-gray-700">Bem-vindo(a) ao seu Painel!</h2>
                <p class="text-gray-500 mt-2">Selecione uma das opções abaixo para começar a gerir o seu negócio.</p>
            </div>

            <!-- Grid de Cards de Ação -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">

                <!-- Card: Realizar Venda (PDV) -->
                <div class="group bg-white rounded-lg shadow-lg hover:shadow-2xl transition-shadow duration-300 transform hover:-translate-y-2 overflow-hidden">
                    <a href="Vendas/FrenteCaixa.php" class="block p-8">
                         <div class="flex items-center justify-center h-16 w-16 rounded-full bg-purple-100 text-purple-600 mb-6 group-hover:bg-purple-600 group-hover:text-white transition-colors duration-300">
                            <i class="fas fa-cash-register fa-2x"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Frente de Caixa (PDV)</h3>
                        <p class="text-gray-600">Inicie uma nova venda, adicione produtos e finalize o pagamento.</p>
                    </a>
                </div>

                <!-- Card: Gerir Produtos -->
                <div class="group bg-white rounded-lg shadow-lg hover:shadow-2xl transition-shadow duration-300 transform hover:-translate-y-2 overflow-hidden">
                    <a href="Produtos/ListarProdutos.php" class="block p-8">
                        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 text-blue-600 mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                            <i class="fas fa-box-open fa-2x"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Gerir Produtos</h3>
                        <p class="text-gray-600">Visualize, adicione, edite e remova produtos do seu inventário.</p>
                    </a>
                </div>
                
                <!-- Card: Movimentação de Estoque -->
                <div class="group bg-white rounded-lg shadow-lg hover:shadow-2xl transition-shadow duration-300 transform hover:-translate-y-2 overflow-hidden">
                    <a href="Relatorios/MovimentacaoEstoque.php" class="block p-8">
                        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 text-yellow-600 mb-6 group-hover:bg-yellow-600 group-hover:text-white transition-colors duration-300">
                            <i class="fas fa-exchange-alt fa-2x"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Movimentação de Estoque</h3>
                        <p class="text-gray-600">Acompanhe o extrato completo de todas as entradas e saídas.</p>
                    </a>
                </div>

                <!-- Card: Histórico de Vendas -->
                <div class="group bg-white rounded-lg shadow-lg hover:shadow-2xl transition-shadow duration-300 transform hover:-translate-y-2 overflow-hidden">
                    <a href="Relatorios/HistoricoVendas.php" class="block p-8">
                        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-green-100 text-green-600 mb-6 group-hover:bg-green-600 group-hover:text-white transition-colors duration-300">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Histórico de Vendas</h3>
                        <p class="text-gray-600">Consulte o relatório detalhado de todas as saídas por venda.</p>
                    </a>
                </div>

            </div>
        </main>
    </div>

</body>
</html>

