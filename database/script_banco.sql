-- 1. Criação do Banco de Dados
CREATE DATABASE IF NOT EXISTS simgetec_portal;
USE simgetec_portal;
SELECT * FROM matriculas_permitidas;
SELECT * FROM artigos;
SELECT * FROM professores;
-- --------------------------------------------------------

-- 2. Tabela de Matrículas Autorizadas
-- Lista branca de quem pode se cadastrar como professor
CREATE TABLE matriculas_permitidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_matricula VARCHAR(20) NOT NULL UNIQUE
);

INSERT INTO matriculas_permitidas (numero_matricula) VALUES (1234);
INSERT INTO matriculas_permitidas (numero_matricula) VALUES (5678);
INSERT INTO matriculas_permitidas (numero_matricula) VALUES (9999);

-- --------------------------------------------------------

-- 3. Tabela de Professores (Administradores)
CREATE TABLE professores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    matricula VARCHAR(20) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

-- --------------------------------------------------------

-- 4. Tabela de Artigos (Trabalhos de Graduação)
CREATE TABLE artigos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    nomes_alunos TEXT NOT NULL,
    nome_orientador VARCHAR(100) NOT NULL,
    caminho_ficheiro VARCHAR(255) NOT NULL,
    data_publicacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_professor_postou INT,
    FOREIGN KEY (id_professor_postou) REFERENCES professores(id) ON DELETE SET NULL
);

-- --------------------------------------------------------

-- 5. DADOS INICIAIS (SEED)
-- Para você conseguir logar e testar assim que rodar o script.

-- B. Criando o primeiro Professor (Admin)
-- Login: prof@simgetec.com
-- Senha: 123456
INSERT INTO professores (nome, email, matricula, senha) 
VALUES ('Professor Administrador', 'prof@simgetec.com', '2025', '123456');

-- C. Criando um Artigo de Exemplo
-- Nota: O caminho 'uploads/exemplo.pdf' é fictício, o download dará erro 404 até você subir um real.
INSERT INTO artigos (titulo, nomes_alunos, nome_orientador, caminho_ficheiro, id_professor_postou)
VALUES ('Implementação do Sistema Kipu', 'Turma de ADS', 'Orientador José', 'uploads/exemplo_kipu.pdf', 1);