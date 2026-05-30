FROM docker.io/dunglas/frankenphp:php8.5-trixie

ARG PUID=0
ARG PGID=0
ARG NODE_VERSION=24
ARG MYSQL_CLIENT="default-mysql-client"
ARG POSTGRES_VERSION=18

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC
ENV LANG=C.UTF-8
ENV SUPERVISOR_PHP_COMMAND="/usr/local/bin/php -d variables_order=EGPCS /var/www/html/artisan octane:start --server=frankenphp --host=0.0.0.0 --admin-port=2019 --port=80"
ENV SUPERVISOR_PHP_USER="root"
ENV PLAYWRIGHT_BROWSERS_PATH=0

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN echo "Acquire::http::Pipeline-Depth 0;" > /etc/apt/apt.conf.d/99custom && \
    echo "Acquire::http::No-Cache true;" >> /etc/apt/apt.conf.d/99custom && \
    echo "Acquire::BrokenProxy true;" >> /etc/apt/apt.conf.d/99custom

RUN apt-get update && apt-get upgrade -y && \
    mkdir -p /etc/apt/keyrings && \
    apt-get install -y --no-install-recommends \
        wget ssh gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 \
        libcap2-bin libpng-dev python3 dnsutils librsvg2-bin fswatch ffmpeg nano && \
    install-php-extensions @composer pcntl \
        pgsql pdo_pgsql gd \
        mongodb \
        imap mysqli \
        zip bcmath soap pdo_mysql \
        intl ldap \
        msgpack igbinary redis \
        memcached pcov imagick xdebug && \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg && \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_VERSION.x nodistro main" > /etc/apt/sources.list.d/nodesource.list && \
    curl -fsSL https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor -o /etc/apt/keyrings/pgdg.gpg && \
    echo "deb [signed-by=/etc/apt/keyrings/pgdg.gpg] https://apt.postgresql.org/pub/repos/apt trixie-pgdg main" > /etc/apt/sources.list.d/pgdg.list && \
    apt-get update && \
    apt-get install -y --no-install-recommends \
        nodejs \
        $MYSQL_CLIENT \
        postgresql-client-$POSTGRES_VERSION && \
    npm install -g npm pnpm bun && \
    npx playwright install-deps && \
    corepack enable && \
    corepack prepare yarn@stable --activate && \
    apt-get -y autoremove && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN setcap 'cap_net_bind_service=+ep' /usr/local/bin/frankenphp
RUN if [ "$PUID" != "0" ] && [ "$PGID" != "0" ]; then \
        groupadd --force -g $PGID laravel && \
        useradd -ms /bin/bash --no-user-group -g $PGID -u $PUID laravel; \
    fi
RUN git config --global --add safe.directory /var/www/html

COPY start-container /usr/local/bin/start-container
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /usr/local/etc/php/conf.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

EXPOSE 80/tcp

ENTRYPOINT ["start-container"]
