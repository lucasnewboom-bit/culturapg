-- Migration incremental e não destrutiva: adiciona uma imagem opcional de capa ao evento.
ALTER TABLE EVENTO ADD COLUMN imagem VARCHAR(255) NULL AFTER classificacao_indicativa;
