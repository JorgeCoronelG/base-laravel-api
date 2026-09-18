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

1. Crear la interfaz en `app/Contracts/Repositories` o `app/Contracts/Services`.
2. Crear la implementación en `app/Repositories` o `app/Services`, extendiendo `BaseRepository` o `BaseService`.
3. Registrar la pareja interfaz => implementación en `RepositoryServiceProvider` o `ServiceLogicServiceProvider`.
