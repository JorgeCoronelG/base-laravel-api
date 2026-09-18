# Proyecto base en Laravel para API

Este proyecto es una base para crear un proyecto de API.

- Laravel 13 · PHP 8.5 · MySQL 8.4
- Repositorios y servicios genéricos en `app/Core`

## Requisitos

Solo [Docker](https://www.docker.com/) (con `make`, que ya viene en macOS y Linux). No hace falta instalar PHP ni Composer.

## Ejecución del proyecto

Primera vez:

```
make setup
```

Construye la imagen, instala las dependencias, crea el `.env`, genera la clave y ejecuta las migraciones.

Después, para trabajar:

```
make up      # levanta API, MySQL y phpMyAdmin
make down    # detiene todo
```

| Servicio   | URL                   | Credenciales (del `.env`)          |
|------------|-----------------------|------------------------------------|
| API        | http://localhost:8000 |                                    |
| phpMyAdmin | http://localhost:8080 | `DB_USERNAME` / `DB_PASSWORD`      |
| MySQL      | localhost:3306        | `DB_USERNAME` / `DB_PASSWORD`      |

Los puertos se pueden cambiar con `APP_PORT`, `PMA_PORT` y `DB_FORWARD_PORT` en el `.env`.

## Comandos útiles

```
make test                        # ejecuta los tests (SQLite en memoria)
make test ARGS=tests/Unit/Core   # solo un grupo de tests
make artisan cmd="route:list"    # cualquier comando de artisan
make composer cmd="require x/y"  # cualquier comando de composer
make shell                       # terminal dentro del contenedor
make help                        # lista todos los comandos
```

## Cómo crear un repositorio y un servicio

1. Crear la interfaz en `app/Contracts/Repositories` o `app/Contracts/Services`. Puede extender `BaseRepositoryInterface` / `BaseServiceInterface`, o solo las partes que se necesiten (`ReadableRepositoryInterface`, `WritableRepositoryInterface`, `BulkRepositoryInterface`, `RelationSyncRepositoryInterface`).
2. Crear la implementación en `app/Repositories` o `app/Services`, extendiendo `BaseRepository` o `BaseService` y pasando la dependencia al constructor padre:

```php
class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $entity)
    {
        parent::__construct($entity);
    }
}

class ProductService extends BaseService implements ProductServiceInterface
{
    public function __construct(ProductRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
```

3. Registrar la pareja interfaz => implementación en `RepositoryServiceProvider` o `ServiceLogicServiceProvider`.

Los ids pueden ser `int` o `string` (UUID/ULID).

## Listados en el controller

El servicio no conoce `Request`. El controller arma un `ListQuery` (filtros, orden y tamaño de página) y se lo pasa:

```php
$products = $this->service->findAllPaginated(ListQuery::fromRequest($request));
```

Desde un job o un comando se puede construir directamente: `new ListQuery($filters, '-name', 20)`.

## Filtros y ordenamiento

Cada modelo que use los traits `AdvancedFilter` y `Sortable` declara qué campos se pueden filtrar y ordenar:

```php
public array $allowedFilters = ['id', 'name', 'status'];
public array $allowedSorts = ['id', 'name', 'status'];
```

Un campo fuera de la lista responde `400`. Si el modelo no declara la propiedad y se pide filtrar u ordenar, responde `500` con un mensaje que indica cuál falta.

Los filtros se envían en el parámetro `q` (JSON) y por defecto se combinan con AND. Para usar OR en un filtro se indica `"boolean": "or"`:

```json
{"filters": [
  {"field": "status", "operator": "=", "value": 1},
  {"field": "name", "operator": "%LIKE%", "value": "ana", "boolean": "or"}
]}
```

`IS NULL` e `IS NOT NULL` no necesitan `value`.

## Actualizaciones parciales (PATCH)

`BaseService::update` guarda todo lo que devuelve `Data::toArray()`. Para que un campo no enviado no se sobrescriba, decláralo como `Optional` en el DTO:

```php
public function __construct(
    public string|Optional $name,
    public int|null|Optional $status,
) {}
```
