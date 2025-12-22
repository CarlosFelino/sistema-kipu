-- 1. Criação do Banco
CREATE DATABASE kipu;
USE kipu;

-- --------------------------------------------------------

-- 2. Tabela de Professores (Administradores)
-- Lógica: Somente quem estiver aqui pode acessar o painel de upload.
CREATE TABLE professores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE, -- O login será feito pelo e-mail
    senha VARCHAR(255) NOT NULL -- Senha criptografada
);

-- --------------------------------------------------------

-- 3. Tabela de Artigos
-- Lógica: Aqui ficam os dados que o público vai ver.
CREATE TABLE artigos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    
    -- Como os alunos não têm cadastro, guardamos os nomes apenas como texto simples.
    -- Ex: "João Silva, Maria Souza e Pedro Santos"
    nomes_alunos TEXT NOT NULL, 
    
    nome_orientador VARCHAR(100) NOT NULL,
    
    -- Onde o arquivo PDF está salvo na pasta do computador/servidor
    arquivo_path VARCHAR(255) NOT NULL, 
    
    data_publicacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    -- (Opcional) Saber QUAL professor postou esse artigo
    id_professor_postou INT,
    FOREIGN KEY (id_professor_postou) REFERENCES professores(id)
);