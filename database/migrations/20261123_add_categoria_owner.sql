-- Permite identificar o usuário que criou cada categoria.
-- Categorias existentes permanecem sem proprietário e não podem ser removidas por usuários.
ALTER TABLE CATEGORIA
    ADD COLUMN criado_por_usuario_id int NULL AFTER nome,
    ADD KEY idx_categoria_criado_por_usuario (criado_por_usuario_id),
    ADD CONSTRAINT fk_categoria_criador_usuario
        FOREIGN KEY (criado_por_usuario_id)
        REFERENCES USUARIO (id_usuario)
        ON DELETE SET NULL;
