ARG PHP_VERSION=8.5
ARG FRANKENPHP_VERSION=1.12.3
ARG COMPOSER_VERSION=2.8

FROM docker.io/dunglas/frankenphp:${FRANKENPHP_VERSION}-builder-php${PHP_VERSION}-alpine AS upstream

COPY --from=docker.io/caddy:builder /usr/bin/xcaddy /usr/bin/xcaddy

RUN CGO_ENABLED=1 \
    XCADDY_SETCAP=1 \
    XCADDY_GO_BUILD_FLAGS="-ldflags='-w -s' -tags=nobadger,nomysql,nopgx" \
    CGO_CFLAGS=$(php-config --includes) \
    CGO_LDFLAGS="$(php-config --ldflags) $(php-config --libs)" \
    xcaddy build \
        --output /usr/local/bin/frankenphp \
        --with github.com/dunglas/frankenphp=./ \
        --with github.com/dunglas/frankenphp/caddy=./caddy/ \
        --with github.com/dunglas/caddy-cbrotli

FROM docker.io/library/composer:${COMPOSER_VERSION} AS composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-autoloader --no-ansi --no-scripts --no-progress
COPY . .
RUN mkdir -p bootstrap/cache storage/framework/views \
    && composer dump-autoload --optimize --no-dev \
    && php artisan wayfinder:generate --with-form

FROM docker.io/oven/bun:1-alpine AS bun_builder
WORKDIR /app
COPY bun.lock package.json ./
RUN bun install --frozen-lockfile
COPY --from=composer /app /app
RUN WAYFINDER_COMMAND=echo bun run build

FROM docker.io/dunglas/frankenphp:${FRANKENPHP_VERSION}-php${PHP_VERSION}-alpine

COPY --from=upstream /usr/local/bin/frankenphp /usr/local/bin/frankenphp

ARG PUID=0
ARG PGID=0
ARG TZ=UTC
ARG APP_VERSION=dev
ARG APP_BRANCH=unknown
ARG APP_COMMIT_HASH=unknown
ARG APP_REPO_URL=""

ENV TERM=xterm-color \
    OCTANE_SERVER=frankenphp \
    TZ=${TZ} \
    LANG=C.UTF-8 \
    ROOT=/app \
    USER=root \
    SUPERVISOR_PHP_USER=root \
    PUID=${PUID} \
    PGID=${PGID} \
    APP_ENV=production \
    APP_VERSION=${APP_VERSION} \
    APP_BRANCH=${APP_BRANCH} \
    APP_COMMIT_HASH=${APP_COMMIT_HASH} \
    APP_REPO_URL=${APP_REPO_URL} \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_FUND=0 \
    COMPOSER_MAX_PARALLEL_HTTP=48 \
    XDG_CONFIG_HOME=/app/.config \
    XDG_DATA_HOME=/app/.data

WORKDIR /app

SHELL ["/bin/sh", "-eou", "pipefail", "-c"]

RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime \
    && echo ${TZ} > /etc/timezone

RUN if [ "${PUID}" != "0" ] && [ "${PGID}" != "0" ]; then \
        addgroup --system --gid ${PGID} laravel \
        && adduser --system --uid ${PUID} --ingroup laravel laravel; \
    fi

RUN install-php-extensions \
    bcmath pdo_pgsql pgsql zip intl gd pcntl opcache redis apcu \
    && apk add --no-cache supervisor curl unzip tzdata su-exec \
    && rm -rf /tmp/* /var/cache/apk/* \
    && mkdir -p /tmp/opcache-file-cache

RUN cp ${PHP_INI_DIR}/php.ini-production ${PHP_INI_DIR}/php.ini

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=composer /app/bootstrap/cache /app/bootstrap/cache
COPY docker/runtime/php.ini /usr/local/etc/php/conf.d/99-php.ini
COPY docker/runtime/Caddyfile /etc/caddy/Caddyfile
COPY docker/runtime/start-container /usr/local/bin/start-container
COPY docker/runtime/healthcheck /usr/local/bin/healthcheck
COPY docker/runtime/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/runtime/octane.conf /etc/supervisor/conf.d/octane.conf
COPY docker/runtime/supervisord.horizon.conf /etc/supervisor/conf.d/supervisord.horizon.conf
COPY docker/runtime/supervisord.scheduler.conf /etc/supervisor/conf.d/supervisord.scheduler.conf
COPY docker/runtime/supervisord.worker.conf /etc/supervisor/conf.d/supervisord.worker.conf
COPY docker/runtime/supervisord.reverb.conf /etc/supervisor/conf.d/supervisord.reverb.conf
COPY docker/runtime/supervisord.ssr.conf /etc/supervisor/conf.d/supervisord.ssr.conf
COPY --from=composer /app/vendor /app/vendor
COPY --from=bun_builder /app/public/build /app/public/build

COPY composer.json composer.lock ./
COPY artisan ./
COPY app/ app/
COPY bootstrap/ bootstrap/
COPY config/ config/
COPY database/ database/
COPY routes/ routes/
COPY public/ public/
COPY storage/app/public storage/app/public
COPY resources/views/ resources/views/
COPY lang* ./

RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    bootstrap/cache \
    && chmod +x /usr/local/bin/start-container /usr/local/bin/healthcheck \
    && composer dump-autoload --optimize --apcu --no-dev \
    && if [ "${SUPERVISOR_PHP_USER}" != "root" ]; then chown -R laravel:laravel /app; fi \
    && find / -perm /6000 -type f -exec chmod a-s {} + 2>/dev/null || true

USER ${USER}

EXPOSE 80 2019

HEALTHCHECK --start-period=5s --interval=1s --timeout=3s --retries=10 CMD healthcheck || exit 1

ENTRYPOINT ["start-container"]
