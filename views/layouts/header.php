
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <title>CulturaPG</title>

    <link rel="stylesheet" href="css/style.css">

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

                <a href="index.php?pagina=mapa">Mapa</a>

                <a href="index.php?pagina=catalogo">Eventos</a>

                <?php if (isset($_SESSION['usuario'])): ?>

                    <a href="index.php?pagina=perfil">Perfil</a>

                    <a href="index.php?pagina=notificacoes">Notificações</a>

                    <a href="index.php?pagina=logout">Sair</a>

                <?php else: ?>

                    <a href="index.php?pagina=login">Entrar</a>

                    <a href="index.php?pagina=cadastro">Criar conta</a>

                <?php endif; ?>

            </nav>

        </div>

    </header>

    <main class="conteudo">
