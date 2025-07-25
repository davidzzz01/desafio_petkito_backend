# Todo API - Backend Laravel

API RESTful em Laravel para gerenciamento de tarefas, usuários e logs de atividades.

## Stack

- **Laravel 10**
- **PHP 8.1+**
- **MySQL**
- **Laravel Sanctum** (Autenticação)
- **DomPDF** (Geração de relatórios)
- **PHPUnit** (Testes)

## Arquitetura

O projeto segue a arquitetura **Repository/UseCase/Controller**:

```
app/
├── Controllers/     # Controladores da API
├── UseCases/        # Lógica de negócio
├── Repositories/    # Acesso a dados
├── Models/          # Modelos Eloquent
├── Observers/       # Observadores para logs
├── Traits/          # Traits reutilizáveis
└── Providers/       # Service Providers
```

## Funcionalidades

### 🔐 Autenticação
- Login/logout via Laravel Sanctum
- Tokens JWT para autenticação
- Middleware de proteção de rotas

### 📋 Gestão de Tarefas
- CRUD completo de tarefas
- Marcar tarefas como concluídas
- Filtros por status (pendente/concluída)
- Busca por título e descrição
- Paginação padronizada

### 👥 Gestão de Usuários
- CRUD completo de usuários
- Diferenciação admin/usuário comum
- Filtros por tipo de usuário

### 📊 Logs de Atividades
- Registro automático de ações
- Observer para tarefas criadas/concluídas
- Relatórios em PDF
- Filtros por usuário

### 📄 Relatórios
- Exportação de tarefas em PDF
- Exportação de logs em PDF
- Diferenciação por permissões (admin vê tudo)

## Instalação

### Pré-requisitos
- PHP 8.1+
- Composer
- MySQL/PostgreSQL
- Extensões PHP: `openssl`, `curl`, `fileinfo`, `mbstring`

### Passos

1. **Clone o repositório:**
```bash
git clone <repository-url>
cd todo-api
```

2. **Instale as dependências:**
```bash
composer install
```

3. **Configure o ambiente:**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure o banco de dados no `.env`:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=todo_db
DB_USERNAME=root
DB_PASSWORD=
```

5. **Execute as migrations:**
```bash
php artisan migrate
```

6. **Popule o banco com dados de exemplo:**
```bash
php artisan db:seed
```

7. **Inicie o servidor:**
```bash
php artisan serve
```

A API estará disponível em `http://localhost:8000`

## Seeders (Dados de Exemplo)

O projeto inclui **seeders completos** para facilitar o desenvolvimento e testes:

### 📊 Seeders Incluídos
- **UserSeeder** - Usuários de exemplo (admin e comuns)
- **TaskSeeder** - Tarefas de exemplo para diferentes usuários

### 🚀 Executar Seeders
```bash
# Executar todos os seeders
php artisan db:seed

# Executar seeder específico
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=TaskSeeder
```

### 👥 Usuários Criados
Após executar os seeders, você terá:

- **Admin:** `admin@admin.com` / `password`
- **Usuário:** `user@user.com` / `password`
- **Gestor:** `gestor@empresa.com` / `password`
- **Colaborador:** `colaborador@empresa.com` / `password`

### 📋 Tarefas Criadas
- Tarefas de exemplo com diferentes status
- Logs de atividades registrados automaticamente
- Dados realistas para testar todas as funcionalidades

## Testes

O projeto inclui **testes unitários completos** para garantir qualidade e confiabilidade:

### 🧪 Testes Incluídos
- **Repository Tests** - Testes para acesso a dados
- **UseCase Tests** - Testes para lógica de negócio
- **Feature Tests** - Testes de integração da API

### 📁 Estrutura de Testes
```
tests/
├── Unit/
│   ├── Repositories/
│   │   ├── TaskRepositoryTest.php
│   │   ├── UserRepositoryTest.php
│   │   └── ActivityLogRepositoryTest.php
│   └── UseCases/
│       └── TaskUseCaseTest.php
└── Feature/
    └── TaskControllerTest.php
```

### 🚀 Executar Testes
```bash
# Executar todos os testes
php artisan test

# Executar testes específicos
php artisan test tests/Unit/Repositories/
php artisan test tests/Unit/UseCases/
php artisan test tests/Feature/

# Executar com detalhes
php artisan test --verbose

# Executar teste específico
php artisan test tests/Unit/Repositories/TaskRepositoryTest.php
```

### ✅ Cobertura de Testes
- **TaskRepository** - CRUD, busca, filtros, contagem
- **UserRepository** - CRUD, busca por email, contagem
- **ActivityLogRepository** - CRUD, filtros por usuário, relacionamentos
- **TaskUseCase** - Lógica de negócio, validações, paginação

### 🎯 Benefícios dos Testes
- **Qualidade garantida** - Código testado e confiável
- **Refatoração segura** - Mudanças não quebram funcionalidades
- **Documentação viva** - Testes mostram como usar as funcionalidades
- **Desenvolvimento rápido** - Feedback imediato sobre mudanças

## Rotas da API

### 🔐 Autenticação
```
POST   /api/login          # Login do usuário
POST   /api/logout         # Logout (autenticado)
GET    /api/user           # Dados do usuário logado
```

### 📋 Tarefas
```
GET    /api/tasks                    # Listar tarefas (paginação)
POST   /api/tasks                    # Criar tarefa
GET    /api/tasks/{id}               # Visualizar tarefa
PUT    /api/tasks/{id}               # Editar tarefa
DELETE /api/tasks/{id}               # Excluir tarefa
PUT    /api/tasks/{id}/complete      # Marcar como concluída
GET    /api/tasks/status             # Filtrar por status
GET    /api/tasks/search             # Buscar por palavra
GET    /api/task/pdf                 # Exportar PDF
```

### 👥 Usuários
```
GET    /api/users                    # Listar usuários (paginação)
POST   /api/user                     # Criar usuário
GET    /api/users/{id}               # Visualizar usuário
PUT    /api/users/{id}               # Editar usuário
DELETE /api/users/{id}               # Excluir usuário
```

### 📊 Logs de Atividades
```
GET    /api/logs                     # Listar logs (paginação)
GET    /api/logs/pdf                 # Exportar logs em PDF
```

## Parâmetros de Paginação

Todas as listagens aceitam:
- `page` (padrão: 1)
- `per_page` (padrão: 10)

**Exemplo:**
```
GET /api/tasks?page=1&per_page=20
```

## Autenticação

### Login
```bash
POST /api/login
Content-Type: application/json

{
  "email": "admin@admin.com",
  "password": "password"
}
```

### Usar Token
```bash
GET /api/tasks
Authorization: Bearer 1|abc123def456...
```

## Permissões

### Admin (`is_admin = true`)
- Acesso total a todas as funcionalidades
- Pode ver todos os usuários e tarefas
- Pode criar, editar e excluir usuários
- Acesso completo aos logs

### Usuário Comum (`is_admin = false`)
- Vê apenas suas próprias tarefas
- Vê apenas seus próprios logs
- Não pode gerenciar usuários

## Estrutura de Resposta

### Paginação
```json
{
  "total": 100,
  "per_page": 10,
  "page": 1,
  "next_page": 2,
  "last_page": 10,
  "previous_page": null,
  "data": [...]
}
```

### Tarefa
```json
{
  "id": 1,
  "title": "Reunião com cliente",
  "description": "Discutir projeto",
  "due_date": "2025-07-26",
  "completed": false,
  "user_id": 1,
  "created_at": "2025-07-25T10:30:00.000000Z",
  "updated_at": "2025-07-25T10:30:00.000000Z"
}
```

### Log de Atividade
```json
{
  "id": 1,
  "user_id": 1,
  "task_id": 5,
  "details": "João criou a tarefa \"Reunião\"",
  "created_at": "2025-07-25T10:30:00.000000Z",
  "user": {
    "id": 1,
    "name": "João"
  },
  "task": {
    "id": 5,
    "title": "Reunião"
  }
}
```

## Observers

O projeto usa observers para registrar automaticamente:
- **Criação de tarefas:** "Usuário criou a tarefa X"
- **Conclusão de tarefas:** "Usuário concluiu a tarefa X"

## Relatórios PDF

### Tarefas
- Lista todas as tarefas com detalhes
- Filtrado por permissões (admin vê tudo, user vê só as próprias)

### Logs
- Lista todas as atividades registradas
- Filtrado por permissões (admin vê tudo, user vê só as próprias)

## Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request
