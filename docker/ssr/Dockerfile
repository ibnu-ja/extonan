FROM docker.io/library/composer:latest AS wayfinder
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
COPY --from=wayfinder /app /app
RUN WAYFINDER_COMMAND=echo bun run build:ssr

FROM docker.io/oven/bun:1-alpine
WORKDIR /app
ENV NODE_ENV=production
COPY --from=bun_builder /app/node_modules /app/node_modules
COPY --from=bun_builder /app/bootstrap/ssr /app/bootstrap/ssr
COPY --from=bun_builder /app/public /app/public
EXPOSE 13714
CMD ["bun", "bootstrap/ssr/ssr.js"]
