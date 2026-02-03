# Gestor de Stand Automóvel 🚗

Uma solução web completa e moderna para gestão de concessionários automóveis. Este sistema oferece uma interface pública elegante para clientes e um painel administrativo robusto para gestão total do negócio.

## 🌟 Funcionalidades Principais

### 🏢 Parte Pública (Cliente)
*   **Homepage Dinâmica:**
    *   Slide "Hero" principal totalmente configurável pelo Admin.
    *   Estatísticas em tempo real (veículos em stock, anos de experiência, clientes satisfeitos).
    *   Secção de veículos em destaque (últimas entradas).
*   **Catálogo de Viaturas:**
    *   Listagem completa com paginação.
    *   Filtros avançados (Marca, Preço, Ano, Combustível, Quilómetros).
    *   Identificação visual de carros "Reservados" ou "Vendidos".
*   **Página de Detalhes:**
    *   Galeria de imagens interativa.
    *   Especificações técnicas detalhadas.
    *   Produtos relacionados.
    *   Botões de contacto direto (WhatsApp/Email).

### ⚙️ Painel de Administração
*   **Dashboard Intuitivo:** Visão geral do negócio.
*   **Gestão de Inventário:**
    *   Adicionar/Editar/Remover viaturas.
    *   Controlo de estados: Disponível, Reservado, Vendido.
    *   Upload múltiplo de fotos com ordenação "Drag & Drop".
*   **Gestão de Conteúdos (CMS):**
    *   **Hero Images:** Gestão dos slides da página inicial.
    *   **Configurações Globais:** Edite contactos, horários, localização (Google Maps), links sociais e textos SEO sem tocar em código.
*   **Segurança:** Sistema de login seguro para administradores.

## 🛠️ Tecnologias Utilizadas

*   **Backend:** PHP 8+ (Estrutura MVC simplificada)
*   **Base de Dados:** MySQL
*   **Frontend:**
    *   HTML5 / CSS3 (Design Responsivo/Mobile-first)
    *   JavaScript (Vanilla)
    *   Bootstrap 5 (Base de layout)
    *   Bootstrap Icons

## 🚀 Como Instalar

### 1. Requisitos do Sistema
*   Servidor Web (Apache/Nginx)
*   PHP >= 8.0
*   MySQL/MariaDB

### 2. Configuração
1.  **Base de Dados:**
    *   Crie uma nova base de dados (ex: `stand_automovel`).
    *   Importe o ficheiro `stand_automovel.sql` fornecido na raiz do projeto.

2.  **Ligação:**
    *   Edite o ficheiro `config/database.php`.
    *   Atualize as credenciais:
        ```php
        define('DB_HOST', 'localhost');
        define('DB_USER', 'seu_usuario');
        define('DB_PASS', 'sua_senha');
        define('DB_NAME', 'stand_automovel');
        ```

3.  **Permissões:**
    *   Certifique-se que a pasta `uploads/` tem permissões de escrita (755 ou 777 dependendo do ambiente).

### 3. Acesso Inicial
*   **Admin URL:** `/admin`
*   **Login Padrão:**
    *   User: `admin`
    *   Pass: `admin123` (Recomenda-se alterar imediatamente após o primeiro login)

## 📂 Estrutura do Projeto

```
/
├── admin/          # Painel de Administração
├── config/         # Ficheiros de configuração (DB, Globais)
├── includes/       # Componentes reutilizáveis (Header, Sidebar, etc.)
├── public/         # Páginas públicas do site
├── uploads/        # Armazenamento de imagens (Carros, Hero)
├── logs/           # Logs do sistema
└── index.php       # Redirecionamento inicial
```

## 🛡️ Segurança
*   Passwords encriptadas com `password_hash()` (Bcrypt).
*   Prepared Statements (PDO) para prevenção de SQL Injection.
*   Validação de inputs e sanitize de dados.

---
Desenvolvido com ❤️ por [Seu Nome/Empresa]
