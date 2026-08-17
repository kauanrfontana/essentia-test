# Modelo Relacional 
## Tabela `customers`

| Coluna | Tipo | Regras |
|---|---|---|
| `id` | `bigint` | **PK**, auto increment |
| `str_name` | `varchar(255)` | not null, indexado |
| `str_email` | `varchar(255)` | **UK**, not null, indexado |
| `str_phone` | `varchar(20)` | nullable, somente dígitos |
| `str_profile_picture_path` | `varchar(255)` | nullable, caminho em `storage/app/public` |
| `created_at` | `timestamp` | nullable |
| `updated_at` | `timestamp` | nullable |

**Legenda:** PK = chave primária · UK = chave única

## Notação relacional

```
customers(id, str_name, str_email, str_phone, str_profile_picture_path, created_at, updated_at)
```