<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frente de Caixa (PDV)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex h-screen">
        <!-- Área Principal da Venda -->
        <main class="w-2/3 p-6 flex flex-col">
            <header class="mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Frente de Caixa</h1>
                    <p class="text-gray-500">Inicie uma nova venda.</p>
                </div>
                <a href="../dashboard.php" class="bg-white text-gray-700 px-4 py-2 rounded-lg shadow hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Voltar ao Dashboard
                </a>
            </header>

            <!-- Pesquisa de Produtos -->
            <div class="relative mb-6">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="searchProduto" placeholder="Digite o nome do produto para adicionar ao carrinho..."
                       class="w-full pl-12 pr-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg">
                <div id="searchResults" class="absolute z-10 w-full bg-white border rounded-lg mt-1 shadow-lg hidden max-h-60 overflow-y-auto"></div>
            </div>

            <!-- Itens do Carrinho -->
            <div class="flex-grow bg-white rounded-lg shadow overflow-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/2">Produto</th>
                            <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qtd.</th>
                            <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Preço Unit.</th>
                            <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ação</th>
                        </tr>
                    </thead>
                    <tbody id="cartItems">
                        <tr id="emptyCartMessage">
                            <td colspan="5" class="text-center py-10 text-gray-500">
                                <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                <p>O carrinho está vazio</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>

        <!-- Barra Lateral do Resumo -->
        <aside class="w-1/3 bg-white p-6 shadow-lg flex flex-col justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 border-b pb-4 mb-6">Resumo da Venda</h2>
                <div class="space-y-4 text-lg">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span id="subtotal" class="font-semibold">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Descontos</span>
                        <span id="descontos" class="font-semibold">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between text-2xl font-bold text-gray-900 border-t pt-4">
                        <span>Total</span>
                        <span id="total">R$ 0,00</span>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button id="finalizeVendaBtn"
                        class="w-full bg-green-600 text-white font-bold py-4 rounded-lg text-lg hover:bg-green-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                    <i class="fas fa-check-circle mr-2"></i> Finalizar Venda
                </button>
            </div>
        </aside>
    </div>

    <!-- Modal de Feedback -->
    <div id="feedbackModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-8 shadow-xl text-center max-w-sm">
            <div id="modalIcon"></div>
            <h3 id="modalTitle" class="text-2xl font-bold mt-4"></h3>
            <p id="modalMessage" class="text-gray-600 mt-2"></p>
            <button onclick="closeModal()" class="mt-6 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">OK</button>
        </div>
    </div>


    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchProduto');
        const searchResults = document.getElementById('searchResults');
        const cartItems = document.getElementById('cartItems');
        const emptyCartMessage = document.getElementById('emptyCartMessage');
        const finalizeVendaBtn = document.getElementById('finalizeVendaBtn');
        let cart = [];

        // BUSCA DE PRODUTOS
        searchInput.addEventListener('keyup', async (e) => {
            const term = e.target.value;

            if (term.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }

            try {
                // O caminho aqui é crucial. Partindo de /public/Vendas/ para /App/Ajax/
                const response = await fetch(`../../App/Ajax/buscar_produtos.php?term=${encodeURIComponent(term)}`);
                if (!response.ok) {
                    throw new Error(`Erro na rede: ${response.statusText}`);
                }
                const products = await response.json();

                searchResults.innerHTML = '';
                if (products.length > 0) {
                    products.forEach(product => {
                        const div = document.createElement('div');
                        div.className = 'p-4 hover:bg-gray-100 cursor-pointer border-b';
                        div.innerHTML = `
                            <p class="font-semibold">${product.nome}</p>
                            <p class="text-sm text-gray-600">Estoque: ${product.quantidade_estoque} | R$ ${parseFloat(product.preco_venda).toFixed(2)}</p>
                        `;
                        div.addEventListener('click', () => addProductToCart(product));
                        searchResults.appendChild(div);
                    });
                    searchResults.classList.remove('hidden');
                } else {
                    searchResults.classList.add('hidden');
                }
            } catch (error) {
                console.error('Erro ao buscar produtos:', error);
                searchResults.classList.add('hidden');
            }
        });

        // Adicionar produto ao carrinho
        function addProductToCart(product) {
            searchInput.value = '';
            searchResults.classList.add('hidden');

            const existingItem = cart.find(item => item.id === product.id);
            if (existingItem) {
                if(existingItem.quantidade < product.quantidade_estoque){
                    existingItem.quantidade++;
                } else {
                    showModal('error', 'Erro de Estoque', 'Quantidade máxima em estoque atingida.');
                }
            } else {
                if (product.quantidade_estoque > 0) {
                    cart.push({ ...product, quantidade: 1 });
                } else {
                    showModal('error', 'Erro de Estoque', 'Este produto está fora de estoque.');
                }
            }
            renderCart();
        }

        // Renderizar o carrinho
        function renderCart() {
            cartItems.innerHTML = '';
            if (cart.length === 0) {
                cartItems.appendChild(emptyCartMessage);
            } else {
                cart.forEach((item, index) => {
                    const subtotal = item.quantidade * item.preco_venda;
                    const tr = document.createElement('tr');
                    tr.className = 'border-b';
                    tr.innerHTML = `
                        <td class="py-4 px-6">${item.nome}</td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center">
                                <button onclick="updateQuantity(${index}, -1)" class="px-2 py-1 bg-gray-200 rounded-l hover:bg-gray-300">-</button>
                                <span class="w-16 text-center font-semibold">${item.quantidade}</span>
                                <button onclick="updateQuantity(${index}, 1)" class="px-2 py-1 bg-gray-200 rounded-r hover:bg-gray-300">+</button>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right">R$ ${parseFloat(item.preco_venda).toFixed(2).replace('.', ',')}</td>
                        <td class="py-4 px-6 text-right font-semibold">R$ ${subtotal.toFixed(2).replace('.', ',')}</td>
                        <td class="py-4 px-6 text-center">
                            <button onclick="removeFromCart(${index})" class="text-red-500 hover:text-red-700"><i class="fas fa-trash-alt"></i></button>
                        </td>
                    `;
                    cartItems.appendChild(tr);
                });
            }
            updateSummary();
        }

        // Atualizar resumo da venda
        function updateSummary() {
            const total = cart.reduce((sum, item) => sum + (item.quantidade * item.preco_venda), 0);
            document.getElementById('subtotal').innerText = `R$ ${total.toFixed(2).replace('.', ',')}`;
            document.getElementById('total').innerText = `R$ ${total.toFixed(2).replace('.', ',')}`;
            finalizeVendaBtn.disabled = cart.length === 0;
        }

        // Funções globais para os botões do carrinho
        window.updateQuantity = (index, change) => {
            const item = cart[index];
            const newQuantity = item.quantidade + change;
            if (newQuantity > 0 && newQuantity <= item.quantidade_estoque) {
                item.quantidade = newQuantity;
                renderCart();
            } else if(newQuantity > item.quantidade_estoque){
                showModal('error', 'Erro de Estoque', 'Quantidade máxima em estoque atingida.');
            } else if (newQuantity <= 0) {
                removeFromCart(index);
            }
        };

        window.removeFromCart = (index) => {
            cart.splice(index, 1);
            renderCart();
        };

        // Finalizar a venda
        finalizeVendaBtn.addEventListener('click', async () => {
            if (cart.length === 0) return;
            
            const total = cart.reduce((sum, item) => sum + (item.quantidade * item.preco_venda), 0);
            
            // --- CORREÇÃO IMPORTANTE AQUI ---
            // Adicionamos 'preco_venda: item.preco_venda' para garantir que o preço de cada item
            // é enviado para o backend, resolvendo o erro.
            const dataToSend = {
                carrinho: cart.map(item => ({ 
                    id: item.id, 
                    quantidade: item.quantidade, 
                    preco_venda: item.preco_venda 
                })),
                total: total
            };

            try {
                const response = await fetch('../../App/Controller/finalizar_venda.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(dataToSend)
                });
                const result = await response.json();
                
                if (result.sucesso) {
                    showModal('success', 'Sucesso!', result.mensagem);
                    cart = [];
                    renderCart();
                } else {
                    showModal('error', 'Erro!', result.mensagem);
                }
            } catch (error) {
                console.error('Erro ao finalizar venda:', error);
                showModal('error', 'Erro!', 'Não foi possível comunicar com o servidor.');
            }
        });

        // Funções do Modal de Feedback
        const feedbackModal = document.getElementById('feedbackModal');
        const modalIcon = document.getElementById('modalIcon');
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');

        function showModal(type, title, message) {
            modalTitle.innerText = title;
            modalMessage.innerText = message;
            if (type === 'success') {
                modalIcon.innerHTML = `<i class="fas fa-check-circle fa-3x text-green-500"></i>`;
            } else {
                modalIcon.innerHTML = `<i class="fas fa-times-circle fa-3x text-red-500"></i>`;
            }
            feedbackModal.classList.remove('hidden');
        }

        window.closeModal = () => {
            feedbackModal.classList.add('hidden');
        }
    });
    </script>
</body>
</html>

