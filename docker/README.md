# Deployment

## Podman

### Build

```bash
podman build -f docker/app/Dockerfile -t extonan/app .

# or build with custom PUID/PGID
podman build --build-arg PUID=$(id -u) --build-arg PGID=$(id -g) \
  -f docker/app/Dockerfile -t extonan/app .

podman build -f docker/ssr/Dockerfile -t extonan/ssr .
```

### Secrets

```bash
# generate key with: `podman run --rm extonan/app php artisan key:generate --show`
# after editing extonan.secret.json, re-create secret if necessary
podman secret create extonan extonan.secret.json
```

### Deploy

```bash
podman volume create extonan-pgsql-data
podman kube play --publish 8000:80 extonan.yaml
podman run --rm --pod extonan extonan/app php artisan migrate
```

> Container user defaults to root. Set `SUPERVISOR_PHP_USER=laravel` in `extonan.yaml` to run as non-root.
> Set `PUID=<host-uid>` and `PGID=<host-gid>` to remap the laravel user on rootless docker with non-root user when mounting.

### Update

```bash
podman kube play --replace --publish 8000:80 extonan.yaml
```

## Compose

TODO

## Kubernetes

TODO
