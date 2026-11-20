
<?php
$usuario = $_SESSION['usuario'] ?? [];

$nome = $usuario['nome'] ?? '';
$username = $usuario['username'] ?? '';
$email = $usuario['email'] ?? '';
$foto = $usuario['foto_perfil'] ?? '';
?>

<div class="pagina-perfil">

    <div class="informacoes-perfil">

        <h2>Editar perfil</h2>

        <?php if (!empty($mensagem)): ?>
            <div class="mensagem-autenticacao">
                <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form
            action="index.php?pagina=perfil&acao=atualizar"
            method="POST"
            enctype="multipart/form-data"
        >

            <div class="campo-autenticacao">
                <label for="foto_perfil">Foto de perfil</label>

                <img
                    src="<?= htmlspecialchars($foto !== '' ? $foto : 'img/perfil-padrao.png', ENT_QUOTES, 'UTF-8') ?>"
                    id="previewFotoPerfil"
                    alt="Pré-visualização da foto de perfil"
                    style="
                        width:100px;
                        height:100px;
                        object-fit:cover;
                        border-radius:50%;
                        margin-bottom:12px;
                    "
                >

                <input
                    type="file"
                    id="foto_perfil"
                    name="foto_perfil"
                    accept="image/jpeg,image/png,image/webp"
                >
            </div>

            <div class="campo-autenticacao">
                <label for="nome">Nome completo</label>
                <input
                    type="text"
                    id="nome"
                    name="nome"
                    maxlength="100"
                    value="<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>"
                >
            </div>

            <div class="campo-autenticacao">
                <label for="username">Nome de usuário</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    maxlength="50"
                    value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
                >
            </div>

            <div class="campo-autenticacao">
                <label for="email">E-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    maxlength="150"
                    value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                >
            </div>

            <button type="submit" class="botao-autenticacao">
                Salvar alterações
            </button>

        </form>

        <div class="link-autenticacao">
            <a href="index.php?pagina=perfil">Voltar ao perfil</a>
        </div>

    </div>

    <script>
document.addEventListener('DOMContentLoaded', function () {

    const campoFoto = document.getElementById('foto_perfil');
    const previewFoto = document.getElementById('previewFotoPerfil');

    campoFoto.addEventListener('change', function () {

        const arquivo = this.files[0];

        if (!arquivo) return;

        if (!arquivo.type.startsWith('image/')) {
            alert('Selecione um arquivo de imagem válido.');
            this.value = '';
            return;
        }

        const leitor = new FileReader();

        leitor.onload = function (e) {
            previewFoto.src = e.target.result;
        };

        leitor.readAsDataURL(arquivo);

    });

});
</script>
</div>
