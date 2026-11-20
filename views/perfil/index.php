
<?php
$usuario = $_SESSION['usuario'] ?? [];

$nome = $usuario['nome'] ?? 'Usuário';
$username = $usuario['username'] ?? 'usuario';
$email = $usuario['email'] ?? 'Não informado';
$foto = $usuario['foto_perfil'] ?? '';

$inicial = strtoupper(substr($nome, 0, 1));

$historico = $historico ?? [];

$mensagem = $_SESSION['mensagem_perfil'] ?? '';
unset($_SESSION['mensagem_perfil']);

// Imagens dos eventos
$imagensEventos = [
    1 => 'corposquefalam1.png',
    2 => 'sextaas61.png',
    3 => 'aquelequedizsimaquelequediznao1.png',
    4 => 'bugiganga1.png',
    5 => 'sinfoniadasaguas1.png',
    6 => 'mostradetalentos1.png'
];
?>

<div class="pagina-perfil">

    <?php if ($mensagem !== ''): ?>
        <div class="mensagem-autenticacao">
            <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <!-- DADOS DO USUÁRIO -->
    <div class="cabecalho-perfil">

        <div class="avatar-perfil">

            <?php if ($foto !== ''): ?>

                <img
                    src="<?= htmlspecialchars($foto, ENT_QUOTES, 'UTF-8') ?>"
                    alt="Foto de perfil"
                    style="width:100%;height:100%;object-fit:cover;border-radius:50%;"
                >

            <?php else: ?>

                <?= htmlspecialchars($inicial, ENT_QUOTES, 'UTF-8') ?>

            <?php endif; ?>

        </div>

        <h1>
            <?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>
        </h1>

        <p>
            @<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>
        </p>

        <a href="index.php?pagina=perfil&acao=editar" class="botao-autenticacao">
            Editar perfil
        </a>

    </div>

    <!-- INFORMAÇÕES DA CONTA -->
    <div class="informacoes-perfil">

        <h2>Informações da conta</h2>

        <div class="item-perfil">
            <span>Nome completo</span>
            <strong><?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?></strong>
        </div>

        <div class="item-perfil">
            <span>Nome de usuário</span>
            <strong><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></strong>
        </div>

        <div class="item-perfil">
            <span>E-mail</span>
            <strong><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></strong>
        </div>

        <div class="item-perfil">
            <span>Localização</span>
            <strong>Ponta Grossa - PR</strong>
        </div>

    </div>

        <!-- ÁREA DO ORGANIZADOR -->
    <div class="area-organizador">

        <div class="icone-organizador">
            📣
        </div>

        <div class="conteudo-organizador">

            <h2>Tem um espetáculo para divulgar?</h2>

            <p>
                Faça parte do CulturaPG e divulgue suas apresentações,
                espetáculos e eventos culturais para toda a cidade.
            </p>

            <a
                href="index.php?pagina=organizador"
                class="botao-organizador"
            >
                Quero divulgar um espetáculo
            </a>

        </div>

    </div>
    
    <!-- ÚLTIMOS EVENTOS VISUALIZADOS -->
    <div class="historico-perfil">

        <h2>Últimos eventos visualizados</h2>

        <?php if (empty($historico)): ?>

            <p class="historico-vazio">
                Você ainda não visualizou nenhum evento.
            </p>

        <?php else: ?>

            <div class="lista-historico">

                <?php foreach ($historico as $evento): ?>

                    <?php
                    $idEvento = (int) $evento['id_evento'];

                    $imagem = $imagensEventos[$idEvento] ?? '';

                    $data = !empty($evento['data_visualizacao'])
                        ? date('d/m/Y H:i', strtotime($evento['data_visualizacao']))
                        : '';
                    ?>

                    <a
                        href="index.php?pagina=evento&id=<?= $idEvento ?>"
                        class="item-historico"
                    >

                        <?php if ($imagem !== ''): ?>

                            <img
                                src="img/eventos/<?= htmlspecialchars($imagem, ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8') ?>"
                            >

                        <?php endif; ?>

                        <div class="dados-historico">

                            <strong>
                                <?= htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8') ?>
                            </strong>

                            <span>
                                <?= htmlspecialchars($evento['categoria'], ENT_QUOTES, 'UTF-8') ?>
                            </span>

                            <small>
                                Visualizado em <?= htmlspecialchars($data, ENT_QUOTES, 'UTF-8') ?>
                            </small>

                        </div>

                        <span class="seta-historico">›</span>

                    </a>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</div>
