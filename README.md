# На вырост CRM

мини-CRM для самых маленьких (с уклоном в ремонт некоего оборудования)  
Дока для прогеров живет в `/docs`.

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

#### Built with:
- **Docker Compose** 
- **PHP 8.4** 
- **MySQL 8**
- **Symfony 7.3**
- **Doctrine ORM 3** 
- **FrankenPHP + Caddy**
- **Twig** 
- **Bootstrap 5** 
- **JS/JQ** (клиентская логика)

## Использование
- **войдите**
- **создайте Клиента**
- **создайте его Оборудование**
- **создайте Заявку на ремонт этого Оборудования**
- **внутри заявки добавьте доходы-расходы по ней**
- **снимите предложенные демо-отчеты**

## Структура
- **стр входа**
- **заказчики**
- **оборудование**
- **заявки на ремонт**
- **платежи**
- **заготовка для наиболее востребованных отчетов**

## Примечаение
Разделение по пользователям: созданные пользователем Клиенты, их Оборудование и Заявки видны только ему.

В бесплатной версии нет разделения по ролям пользователей и логирования действий пользователей.

Верстка для мобильных.

# Symfony Docker

A [Docker](https://www.docker.com/)-based installer and runtime for the [Symfony](https://symfony.com) web framework,
with [FrankenPHP](https://frankenphp.dev) and [Caddy](https://caddyserver.com/) inside!

![CI](https://github.com/gluck-59/navyrost/workflows/CI/badge.svg)

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --pull --no-cache` to build fresh images
3. Run `docker compose up --wait` to set up and start a fresh Symfony project
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

## Features

- Production, development and CI ready
- Just 1 service by default
- Blazing-fast performance thanks to [the worker mode of FrankenPHP](https://frankenphp.dev/docs/worker/)
- [Installation of extra Docker Compose services](docs/extra-services.md) with Symfony Flex
- Automatic HTTPS (in dev and prod)
- HTTP/3 and [Early Hints](https://symfony.com/blog/new-in-symfony-6-3-early-hints) support
- Real-time messaging thanks to a built-in [Mercure hub](https://symfony.com/doc/current/mercure.html)
- [Vulcain](https://vulcain.rocks) support
- Native [XDebug](docs/xdebug.md) integration
- Super-readable configuration

**Enjoy!**

## Docs

1. [Options available](docs/options.md)
2. [Using Symfony Docker with an existing project](docs/existing-project.md)
3. [Support for extra services](docs/extra-services.md)
4. [Deploying in production](docs/production.md)
5. [Debugging with Xdebug](docs/xdebug.md)
6. [TLS Certificates](docs/tls.md)
7. [Using MySQL instead of PostgreSQL](docs/mysql.md)
8. [Using Alpine Linux instead of Debian](docs/alpine.md)
9. [Using a Makefile](docs/makefile.md)
10. [Updating the template](docs/updating.md)
11. [Troubleshooting](docs/troubleshooting.md)

## License

Symfony Docker is available under the MIT License.

## Credits

Created by [Kévin Dunglas](https://dunglas.dev), co-maintained by [Maxime Helias](https://twitter.com/maxhelias) and sponsored by [Les-Tilleuls.coop](https://les-tilleuls.coop).
