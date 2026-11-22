
<?php require_once __DIR__ . '/../../app/Helpers/Icone.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <title>CulturaPG</title>

    <link rel="stylesheet" href="css/style.css?v=20261122-1">

    <!-- Leaflet: biblioteca do mapa -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >
</head>

<body>

    <header class="cabecalho">

        <div class="container">

            <!-- LOGO OFICIAL -->
            <a href="index.php?pagina=mapa" class="logo">
                <img src="img/logo-culturapg.png" alt="CulturaPG">
            </a>

            <!-- MENU DE NAVEGAÇÃO -->
            <nav class="menu-principal">

                <a href="index.php?pagina=mapa" <?= (($_GET['pagina'] ?? 'mapa') === 'mapa' ? 'aria-current="page"' : '') ?>><?= iconeCultura('mapa') ?>Mapa</a>

                <a href="index.php?pagina=catalogo" <?= (in_array($_GET['pagina'] ?? 'mapa', ['catalogo', 'evento'], true) ? 'aria-current="page"' : '') ?>><?= iconeCultura('calendario') ?>Eventos</a>

                <?php if (isset($_SESSION['usuario'])): ?>

                    <a href="index.php?pagina=perfil" <?= (in_array($_GET['pagina'] ?? 'mapa', ['perfil', 'organizador'], true) ? 'aria-current="page"' : '') ?>><?= iconeCultura('usuario') ?>Perfil</a>

                    <a href="index.php?pagina=notificacoes" <?= (($_GET['pagina'] ?? 'mapa') === 'notificacoes' ? 'aria-current="page"' : '') ?>><?= iconeCultura('notificacao') ?>Notificações</a>

                    <a href="index.php?pagina=logout" <?= (($_GET['pagina'] ?? 'mapa') === 'logout' ? 'aria-current="page"' : '') ?>><?= iconeCultura('sair') ?>Sair</a>

                <?php else: ?>

                    <a href="index.php?pagina=login" <?= (($_GET['pagina'] ?? 'mapa') === 'login' ? 'aria-current="page"' : '') ?>><?= iconeCultura('entrar') ?>Entrar</a>

                    <a href="index.php?pagina=cadastro" <?= (($_GET['pagina'] ?? 'mapa') === 'cadastro' ? 'aria-current="page"' : '') ?>><?= iconeCultura('usuario-adicionar') ?>Criar conta</a>

                <?php endif; ?>

            </nav>

        </div>

    </header>

    <main class="conteudo">
