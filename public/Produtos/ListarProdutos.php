<?php
session_start();

// Boa prática: descomente as linhas abaixo quando tiver um sistema de login
// if (!isset($_SESSION['usuario_id'])) {
//     header('Location: ../../login.php'); // Altere para sua página de login
//     exit();
// }

// 1. Incluímos os ficheiros essenciais
require_once __DIR__ . '/../../App/Config/conexao.php';
require_once __DIR__ . '/../../App/Models/Produtos.php';

// 2. Criamos a conexão com o banco
$conexao = new Conexao();
$pdo = $conexao->getConn();

// 3. Verificamos o filtro de estoque selecionado
$filtro_estoque = $_GET['filtro_estoque'] ?? 'todos';

// 4. Buscamos a lista de produtos
$produtos = [];
try {
    // A lógica de filtragem será aplicada depois de buscar todos
    $todosOsProdutos = Produto::listarTodos($pdo);

    // Aplicamos o filtro no array de produtos
    if ($filtro_estoque === 'zerado') {
        $produtos = array_filter($todosOsProdutos, function ($produto) {
            return $produto['quantidade_estoque'] <= 0;
        });
    } elseif ($filtro_estoque === 'em_estoque') {
        $produtos = array_filter($todosOsProdutos, function ($produto) {
            return $produto['quantidade_estoque'] > 0;
        });
    } else {
        $produtos = $todosOsProdutos;
    }

} catch (Exception $e) {
    $erro = "Não foi possível carregar os produtos. Tente novamente mais tarde.";
    error_log($e->getMessage()); // É uma boa prática logar o erro real
}

// Lógica de feedback da URL para exibir mensagens de sucesso ou erro
$mensagem = '';
$classeAlerta = '';
if (isset($_GET['status']) && isset($_GET['msg'])) {
    $status = $_GET['status'];
    $msg = urldecode($_GET['msg']);
    if ($status === 'sucesso') {
        $mensagem = $msg;
        $classeAlerta = 'p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg';
    } elseif ($status === 'erro') {
        $mensagem = $msg;
        $classeAlerta = 'p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg';
    }
}

// Define o caminho base para as imagens
$caminhoBaseImagem = '../uploads/produtos/';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque de Produtos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans">

    <!-- Navbar -->
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <div class="text-xl font-bold text-gray-800">Meu Estoque</div>
            <div>
                <a href="../dashboard.php" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded">Dashboard</a>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <div class="container mx-auto p-4 md:p-8">

        <!-- Botão Voltar -->
        <div class="mb-6">
            <a href="../dashboard.php" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-semibold transition-colors duration-300">
                <i class="fas fa-arrow-left mr-2"></i>
                Voltar 
            </a>
        </div>

        <header class="flex flex-col md:flex-row justify-between items-center mb-4">
            <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Produtos Cadastrados</h1>
            <div class="flex gap-4 w-full md:w-auto">
                <div class="relative flex-grow">
                    <input type="text" id="searchInput" placeholder="Buscar produtos..." class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="absolute top-0 left-0 inline-flex items-center p-2 mt-1 ml-1 text-gray-400">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <a href="CadastrarProdutos.php" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700 transition duration-300 flex items-center justify-center shrink-0">
                    <i class="fas fa-plus mr-2"></i> Novo
                </a>
            </div>
        </header>

        <!-- Barra de Filtros de Estoque -->
        <div class="mb-8 flex flex-wrap gap-2">
            <a href="?filtro_estoque=todos" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 <?php echo ($filtro_estoque === 'todos' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 border'); ?>">Todos</a>
            <a href="?filtro_estoque=em_estoque" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 <?php echo ($filtro_estoque === 'em_estoque' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 border'); ?>">Em Estoque</a>
            <a href="?filtro_estoque=zerado" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 <?php echo ($filtro_estoque === 'zerado' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 border'); ?>">Estoque Zerado</a>
        </div>
        
        <!-- Container para mensagens de feedback -->
        <?php if (!empty($mensagem)): ?>
        <div class="<?php echo $classeAlerta; ?>" role="alert">
            <?php echo htmlspecialchars($mensagem); ?>
        </div>
        <?php endif; ?>

        <?php if (isset($erro) && empty($mensagem)): ?>
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>

        <!-- Grid responsivo para os cards de produtos -->
        <div id="productGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

            <?php if (empty($produtos)): ?>
                <div class="col-span-full text-center py-16 bg-white rounded-lg shadow-md border border-gray-200">
                    <i class="fas fa-box-open fa-3x text-gray-400 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700">Nenhum produto encontrado para este filtro</h3>
                    <p class="text-gray-500 mt-2">Tente selecionar outra opção ou cadastre um novo produto.</p>
                </div>
            <?php else: ?>
                <?php foreach ($produtos as $produto): ?>
                    <!-- Card do Produto -->
                    <div class="product-card bg-white rounded-lg shadow-md overflow-hidden transform hover:shadow-xl transition-shadow duration-300 border border-gray-200 flex flex-col">
                        
                        <div class="h-48 bg-gray-200 flex items-center justify-center relative">
                            <?php if (!empty($produto['imagem1'])): ?>
                                <img src="<?php echo $caminhoBaseImagem . htmlspecialchars($produto['imagem1']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <i class="fas fa-image fa-3x text-gray-400"></i>
                            <?php endif; ?>
                            
                            <!-- Indicador de Estoque -->
                            <?php
                                $estoque = $produto['quantidade_estoque'];
                                $corEstoque = 'bg-green-500';
                                if ($estoque <= 0) $corEstoque = 'bg-red-500';
                                elseif ($estoque <= 10) $corEstoque = 'bg-yellow-500';
                            ?>
                            <span class="absolute top-2 right-2 text-xs text-white <?php echo $corEstoque; ?> px-2 py-1 rounded-full font-semibold">
                                Estoque: <?php echo $estoque; ?>
                            </span>
                        </div>

                        <div class="p-4 flex-grow flex flex-col justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 truncate" title="<?php echo htmlspecialchars($produto['nome']); ?>">
                                    <?php echo htmlspecialchars($produto['nome']); ?>
                                </h3>
                                <p class="text-2xl font-bold text-gray-900 mt-2">
                                    R$ <?php echo number_format($produto['preco_venda'], 2, ',', '.'); ?>
                                </p>
                            </div>
                            <div class="mt-4 flex justify-end gap-2">
                                <!-- Botão Adicionar Estoque -->
                                <button type="button" 
                                   class="open-modal-estoque text-gray-500 hover:text-green-600 p-2 rounded-full hover:bg-gray-100 transition-colors" 
                                   title="Adicionar Estoque"
                                   data-id="<?php echo $produto['id']; ?>"
                                   data-nome="<?php echo htmlspecialchars($produto['nome']); ?>">
                                    <i class="fas fa-plus-circle fa-fw"></i>
                                </button>
                                
                                <a href="EditarProdutos.php?id=<?php echo $produto['id']; ?>" class="text-gray-500 hover:text-blue-600 p-2 rounded-full hover:bg-gray-100 transition-colors" title="Editar">
                                    <i class="fas fa-pencil-alt fa-fw"></i>
                                </a>
                                <a href="../../App/Controller/excluir_produto.php?id=<?php echo $produto['id']; ?>" 
                                   class="text-gray-500 hover:text-red-600 p-2 rounded-full hover:bg-gray-100 transition-colors" 
                                   title="Excluir"
                                   onclick="return confirm('Tem a certeza que deseja excluir o produto \'<?php echo htmlspecialchars(addslashes($produto['nome'])); ?>\'? Esta ação não pode ser desfeita.');">
                                    <i class="fas fa-trash-alt fa-fw"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
         <div id="noResultsMessage" class="hidden col-span-full text-center py-16 bg-white rounded-lg shadow-md border border-gray-200">
            <i class="fas fa-search fa-3x text-gray-400 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700">Nenhum resultado encontrado</h3>
            <p class="text-gray-500 mt-2">Tente ajustar os termos da sua busca.</p>
        </div>
    </div>

    <!-- Modal Adicionar Estoque -->
    <div id="modalEstoque" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800" id="modalEstoqueTitle">Adicionar Estoque</h3>
                <button id="closeModalEstoque" class="text-gray-500 hover:text-gray-800">&times;</button>
            </div>
            <form action="../../App/Controller/adicionar_estoque.php" method="POST">
                <input type="hidden" name="id_produto_estoque" id="id_produto_estoque">
                <div class="mb-4">
                    <label for="quantidade_adicionar" class="block text-gray-700 text-sm font-bold mb-2">Quantidade a Adicionar:</label>
                    <input type="number" name="quantidade_adicionar" id="quantidade_adicionar" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" min="1" required>
                </div>
                <div class="mb-6">
                    <label for="observacao_estoque" class="block text-gray-700 text-sm font-bold mb-2">Motivo/Observação:</label>
                    <input type="text" name="observacao_estoque" id="observacao_estoque" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Ex: Compra de fornecedor">
                </div>
                <div class="flex items-center justify-end gap-4">
                    <button type="button" id="cancelModalEstoque" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Lógica da busca
            const searchInput = document.getElementById('searchInput');
            const productGrid = document.getElementById('productGrid');
            const productCards = productGrid.querySelectorAll('.product-card');
            const noResultsMessage = document.getElementById('noResultsMessage');

            searchInput.addEventListener('keyup', function () {
                const searchTerm = searchInput.value.toLowerCase();
                let visibleCards = 0;
                productCards.forEach(card => {
                    const productName = card.querySelector('h3').textContent.toLowerCase();
                    if (productName.includes(searchTerm)) {
                        card.style.display = 'flex';
                        visibleCards++;
                    } else {
                        card.style.display = 'none';
                    }
                });
                noResultsMessage.style.display = visibleCards === 0 ? 'block' : 'none';
            });

            // Lógica do Modal de Estoque
            const modal = document.getElementById('modalEstoque');
            const openModalButtons = document.querySelectorAll('.open-modal-estoque');
            const closeModalButton = document.getElementById('closeModalEstoque');
            const cancelModalButton = document.getElementById('cancelModalEstoque');
            const modalTitle = document.getElementById('modalEstoqueTitle');
            const hiddenProductIdInput = document.getElementById('id_produto_estoque');

            function openModal(id, nome) {
                modalTitle.textContent = `Adicionar Estoque para: ${nome}`;
                hiddenProductIdInput.value = id;
                modal.classList.remove('hidden');
            }

            function closeModal() {
                modal.classList.add('hidden');
            }

            openModalButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nome = this.getAttribute('data-nome');
                    openModal(id, nome);
                });
            });

            closeModalButton.addEventListener('click', closeModal);
            cancelModalButton.addEventListener('click', closeModal);
            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    closeModal();
                }
            });
        });
    </script>

</body>
</html>

