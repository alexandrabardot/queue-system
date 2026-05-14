FROM php:8.2-cli

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql

COPY . /app
WORKDIR /app

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080}"]
