-- 1. Criar a Base de Dados
CREATE DATABASE kipu;
USE kipu;

-- --------------------------------------------------------

-- 2. Tabela de Matrículas Autorizadas
-- Serve para controlar quem PODE criar conta de professor.
CREATE TABLE matriculas_permitidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_matricula VARCHAR(20) NOT NULL UNIQUE
);
INSERT INTO matriculas_permitidas (numero_matricula) VALUES ('PROF2024');

-- --------------------------------------------------------

-- 3. Tabela de Professores (Administradores)
CREATE TABLE professores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    matricula VARCHAR(20) NOT NULL UNIQUE, -- Liga-se logicamente à tabela de cima
    senha VARCHAR(255) NOT NULL
);
INSERT INTO professores (nome, email, matricula, senha) VALUES ('Professor Teste', 'prof@simgetec.com', 'PROF2024', '123456');
-- --------------------------------------------------------

-- 4. Tabela de Artigos
CREATE TABLE artigos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    nomes_alunos TEXT NOT NULL, -- Ex: "Ana Silva, Bruno Santos"
    nome_orientador VARCHAR(100) NOT NULL,
    caminho_ficheiro VARCHAR(255) NOT NULL, -- Guarda o caminho (ex: uploads/artigo.pdf)
    data_publicacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    -- Opcional: Para saberes qual o professor que carregou o artigo
    id_professor_postou INT,
    FOREIGN KEY (id_professor_postou) REFERENCES professores(id)
);
INSERT INTO artigos (titulo, nomes_alunos, nome_orientador, caminho_ficheiro, id_professor_postou) VALUES ('Desenvolvimento Web Moderno', 'João e Maria', 'Prof. Teste', 'uploads/teste.pdf', 1);