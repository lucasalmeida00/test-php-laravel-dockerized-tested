# Projeto Laravel Dockerizado

Este projeto é dockerizado e utiliza Laravel Sail para gerenciamento do ambiente de desenvolvimento.

## Como executar

### Primeira execução

Na primeira vez que você for executar o projeto, será necessário fazer o download do Dockerfile do PHP 8.4 e instalar as dependências do Composer. Execute o seguinte comando:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

Após isso, execute o Laravel Sail:

```bash
./vendor/bin/sail up
```

Ou, se preferir usar docker-compose diretamente:

```bash
docker-compose up -d
```

### Configuração do banco de dados

Após subir os containers, execute as migrations:

```bash
./vendor/bin/sail php artisan migrate
```

E então execute os seeders para popular o banco de dados:

```bash
./vendor/bin/sail php artisan db:seed
```

## Usuários de teste

O seeder cria dois usuários para testes:

1. **John Doe** (Usuário comum - pode transferir)
   - Email: `john@example.com`
   - CPF: `12345678900`
   - Senha: `password`
   - Role: `default`
   - Saldo inicial: R$ 1.000,00

2. **Jane Doe** (Lojista - não pode transferir, apenas receber)
   - Email: `jane@example.com`
   - CPF: `12345678901`
   - Senha: `password`
   - Role: `shopmanager`
   - Saldo inicial: R$ 1.000,00

## Sistema de Permissões

O projeto possui um sistema de permissões baseado em **roles** (papéis). Cada usuário pode ter uma ou mais roles atribuídas, e cada role pode ter uma ou mais permissões associadas.

### Permissões disponíveis

Atualmente, existe apenas uma permissão específica no sistema:

- **Transferir dinheiro**: Permite que o usuário realize transferências de dinheiro

### Roles e suas permissões

- **Default (Usuário comum)**: Possui a permissão de transferir dinheiro
- **Shop Manager (Lojista)**: Não possui a permissão de transferir dinheiro. Lojistas apenas recebem dinheiro, não podem realizar transferências

A lógica de negócio garante que apenas usuários com a role `default` podem transferir dinheiro, enquanto lojistas estão limitados a receber valores.

## Endpoints da API

### Login

Autentica um usuário e retorna um token de acesso.

**Endpoint:** `POST /api/authenticate`

**Body:**
```json
{
    "email": "john@example.com",
    "password": "password"
}
```

**Resposta:**
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        ...
    },
    "token": "1|..."
}
```

**Exemplo com cURL:**
```bash
curl -X POST http://localhost/api/authenticate \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password"}'
```

### Transferência

Realiza uma transferência de dinheiro entre usuários. Requer autenticação.

**Endpoint:** `POST /api/transfer`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
    "payer": 1,
    "payee": 2,
    "value": 100.50
}
```

**Parâmetros:**
- `payer`: ID do usuário que está realizando a transferência (deve ter permissão para transferir)
- `payee`: ID do usuário que receberá a transferência
- `value`: Valor a ser transferido (número decimal)

**Resposta:**
```json
{
    "transfer": {
        "id": 1,
        "user_id": 1,
        "recipient_id": 2,
        "amount": 100.50,
        ...
    }
}
```

**Exemplo com cURL:**
```bash
curl -X POST http://localhost/api/transfer \
  -H "Authorization: Bearer {seu_token}" \
  -H "Content-Type: application/json" \
  -d '{"payer":1,"payee":2,"value":100.50}'
```

**Nota:** Apenas usuários com a role `default` podem realizar transferências. Usuários com a role `shopmanager` não possuem permissão para transferir, apenas para receber.
