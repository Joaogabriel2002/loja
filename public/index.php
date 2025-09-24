<?php
session_start();
$erro = '';
if (isset($_SESSION['erro_login'])) {
    $erro = $_SESSION['erro_login'];
    unset($_SESSION['erro_login']); 
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">

    <div class="bg-white p-8 rounded shadow-md w-full max-w-sm">
        <h1 class="text-2xl font-bold mb-6 text-center">Login</h1>

        <!-- Mensagem de erro -->
        <?php if ($erro): ?>
            <div class="bg-red-100 text-red-700 p-2 mb-4 rounded text-center">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <form action="../app/Controller/processa_login.php" method="POST" class="flex flex-col gap-4">
            <input type="email" name="email" placeholder="E-mail" required
                   class="border p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="password" name="senha" placeholder="Senha" required
                   class="border p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700 transition">
                Entrar
            </button>
        </form>
    </div>

</body>
</html>
