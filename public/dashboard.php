<?php
session_start();

// Redireciona se não estiver logado
if (!isset($_SESSION['usuario_id'])) {
    // Altere para o caminho correto da sua página de login
    header('Location: login.php'); 
    exit();
}

// Pega o e-mail da sessão para a mensagem de boas-vindas
$email = htmlspecialchars($_SESSION['usuario']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Meu Estoque</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

    <!-- Navbar -->
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <div class="text-xl font-bold text-gray-800">
                <i class="fas fa-cubes mr-2"></i>Meu Estoque
            </div>
            <div class="flex items-center">
                 <span class="text-gray-600 text-sm mr-4 hidden md:block">Logado como: <strong><?php echo $email; ?></strong></span>
                 <a href="logout.php" class="text-gray-600 hover:text-red-600 px-3 py-2 rounded transition-colors duration-300" title="Sair">
                    <i class="fas fa-sign-out-alt fa-lg"></i>
                 </a>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <main class="container mx-auto p-4 md:p-8">
        
        <!-- Cabeçalho de Boas-vindas -->
        <header class="mb-10">
            <h1 class="text-4xl font-bold text-gray-800">Bem-vindo(a) de volta!</h1>
            <p class="text-lg text-gray-500 mt-2">Selecione uma das opções abaixo para começar a gerenciar seu negócio.</p>
        </header>

        <!-- Grid de Cards de Ação -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            <!-- Card de Produtos -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-2xl transition-shadow duration-300">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 text-blue-600 p-3 rounded-full mr-4">
                        <i class="fas fa-box-open fa-2x"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Produtos</h2>
                </div>
                <p class="text-gray-600 mb-6">Gerencie seu inventário, adicione novos itens e veja o que está em estoque.</p>
                <div class="flex flex-col gap-3">
                    <a href="Produtos/CadastrarProdutos.php" class="w-full text-center bg-blue-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-2"></i>Cadastrar Novo
                    </a>
                    <a href="Produtos/ListarProdutos.php" class="w-full text-center bg-gray-200 text-gray-800 font-semibold py-3 px-4 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-list mr-2"></i>Visualizar Estoque
                    </a>
                </div>
            </div>

            <!-- Card de Vendas (Exemplo para o futuro) -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-2xl transition-shadow duration-300 opacity-50 cursor-not-allowed">
                 <div class="flex items-center mb-4">
                    <div class="bg-green-100 text-green-600 p-3 rounded-full mr-4">
                        <i class="fas fa-cash-register fa-2x"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Vendas</h2>
                </div>
                <p class="text-gray-600 mb-6">Registre novas vendas e consulte o histórico de transações. (Em breve)</p>
                 <div class="flex flex-col gap-3">
                    <span class="w-full text-center bg-gray-400 text-white font-semibold py-3 px-4 rounded-lg">
                        <i class="fas fa-dollar-sign mr-2"></i>Nova Venda
                    </span>
                 </div>
            </div>

            <!-- Card de Relatórios (Exemplo para o futuro) -->
             <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-2xl transition-shadow duration-300 opacity-50 cursor-not-allowed">
                 <div class="flex items-center mb-4">
                    <div class="bg-purple-100 text-purple-600 p-3 rounded-full mr-4">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Relatórios</h2>
                </div>
                <p class="text-gray-600 mb-6">Analise o desempenho do seu negócio com gráficos e dados. (Em breve)</p>
                 <div class="flex flex-col gap-3">
                    <span class="w-full text-center bg-gray-400 text-white font-semibold py-3 px-4 rounded-lg">
                        <i class="fas fa-file-alt mr-2"></i>Ver Relatórios
                    </span>
                 </div>
            </div>

        </div>
    </main>

</body>
</html>
