ARG COMPOSER_VERSION=2.8

FROM docker.io/library/composer:${COMPOSER_VERSION} AS wayfinder
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-autoloader --no-ansi --no-scripts --no-progress
COPY . .
RUN apk add --no-cache zlib-dev libpng-dev libjpeg-turbo-dev freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && mkdir -p bootstrap/cache storage/framework/views \
    && composer dump-autoload --optimize --no-dev \
    && php artisan wayfinder:generate --with-form

FROM docker.io/oven/bun:1-alpine AS bun_builder
WORKDIR /app
COPY bun.lock package.json ./
RUN bun install --frozen-lockfile
COPY --from=wayfinder /app /app
RUN WAYFINDER_COMMAND=echo bun run build:ssr \
    && bun build bootstrap/ssr/ssr.js --outdir /app/dist --target bun

FROM docker.io/oven/bun:1-alpine
WORKDIR /app
ENV NODE_ENV=production
COPY --from=bun_builder /app/dist/ssr.js /app/ssr.js

ARG APP_VERSION=dev
ARG APP_BRANCH=unknown
ARG APP_COMMIT_HASH=unknown
ARG APP_REPO_URL=""

RUN echo "build commit: ${APP_COMMIT_HASH}"

LABEL org.opencontainers.image.title="extonan-ssr" \
    org.opencontainers.image.version="${APP_VERSION}" \
    org.opencontainers.image.revision="${APP_COMMIT_HASH}" \
    org.opencontainers.image.source="${APP_REPO_URL}"

ENV APP_VERSION=${APP_VERSION} \
    APP_BRANCH=${APP_BRANCH} \
    APP_COMMIT_HASH=${APP_COMMIT_HASH} \
    APP_REPO_URL=${APP_REPO_URL}

EXPOSE 13714
CMD ["bun", "ssr.js"]
