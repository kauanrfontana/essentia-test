## Passo a passo

### 1. Instalar dependências

```bash
composer install
npm install
```

### 2. Subir o banco de dados (Postgres via Docker)

```bash
docker compose up -d
```

Isso sobe um container Postgres 17 na porta `5432` com o banco `customers_registration` (usuário/senha `postgres`/`postgres`), conforme definido em [docker-compose.yml](docker-compose.yml).

### 3. Configurar o `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Depois edite o `.env` e ajuste a seção de banco de dados para apontar para o Postgres do passo 2:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=customers_registration
DB_USERNAME=postgres
DB_PASSWORD=postgres

FILESYSTEM_DISK=public
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

### 4. Rodar as migrations

```bash
php artisan migrate
```

### 5. Criar o link de storage

Necessário para que as fotos enviadas fiquem acessíveis publicamente via HTTP:

```bash
php artisan storage:link
```

### 6. Buildar os assets front-end

```bash
npm run build
```

(Para desenvolvimento com hot-reload, use `npm run dev` em outro terminal.)

### 7. Subir o servidor da aplicação

```bash
php artisan serve
```

Acesse [http://localhost:8000](http://localhost:8000).

## Rodando os testes

```bash
php artisan test
```

Os testes usam SQLite em memória (configurado em [phpunit.xml](phpunit.xml)), então não afetam o banco Postgres de desenvolvimento.

## Apagando os dados do banco

Para resetar completamente o Postgres (apaga todos os registros):

```bash
docker compose down -v
docker compose up -d
php artisan migrate
```
