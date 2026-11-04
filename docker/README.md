# Deployment

## Podman Kube

### Build

```bash
# with git metadata
podman build \
  --build-arg APP_VERSION=$(git describe --tags --abbrev=0 2>/dev/null || echo dev) \
  --build-arg APP_BRANCH=$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo unknown) \
  --build-arg APP_COMMIT_HASH=$(git rev-parse --short HEAD 2>/dev/null || echo unknown) \
  --build-arg APP_REPO_URL=$(git remote get-url origin 2>/dev/null || echo "") \
  -f docker/app/Dockerfile -t extonan/app .

# or build with custom PUID/PGID
podman build \
  --build-arg PUID=$(id -u) \
  --build-arg PGID=$(id -g) \
  --build-arg APP_VERSION=$(git describe --tags --abbrev=0 2>/dev/null || echo dev) \
  --build-arg APP_BRANCH=$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo unknown) \
  --build-arg APP_COMMIT_HASH=$(git rev-parse --short HEAD 2>/dev/null || echo unknown) \
  --build-arg APP_REPO_URL=$(git remote get-url origin 2>/dev/null || echo "") \
  -f docker/app/Dockerfile -t extonan/app .

podman build -f docker/ssr/Dockerfile -t extonan/ssr .
```

### Bucket

Default disk: `rustfs`. Configure `extonan.secret.json`

### Secrets

```bash
# generate key with: `podman run --rm extonan/app php artisan key:generate --show`
# after editing extonan.secret.json, re-create secret if necessary
podman secret create extonan extonan.secret.json
```

### Deploy

```bash
podman volume create extonan-pgsql-data
podman volume create extonan-rustfs-data
podman kube play --publish 8000:80 --publish 9000:9000 --publish 9001:9001 extonan.yaml
podman run --rm --pod extonan extonan/app php artisan migrate
```

> Container user defaults to root. Set `SUPERVISOR_PHP_USER=laravel` in `extonan.yaml` to run as non-root.
> Set `PUID=<host-uid>` and `PGID=<host-gid>` to remap the laravel user on rootless docker with non-root user when mounting.

## Podman Quadlet

### Build

```bash
podman quadlet install --replace docker/quadlet-build/
systemctl --user start extonan-app-build.service
systemctl --user start extonan-ssr-build.service
```

### Deploy

```bash
cp docker/quadlet/extonan.env.example docker/quadlet/extonan.env
# adjust value, also edit respective config if needed
podman quadlet install --replace docker/quadlet/
systemctl --user start extonan-pod.service
```

### RustFS Bucket

Same as Podman Kube.

### Scaling

TODO: [read socket](https://github.com/eriksjolund/podman-networking-docs?tab=readme-ov-file#example-use-reuseporttrue-to-share-a-port-between-services) 

## Compose

TODO

## Kubernetes

TODO
