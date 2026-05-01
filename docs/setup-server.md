# Setup Server

## Install dependancies

```bash
composer require guzzlehttp/guzzle
composer require vlucas/phpdotenv
```

## Setup .env

```bash
cp .env.example .env
```

## Run locally

```bash
php -S localhost:8000 index.php
```
