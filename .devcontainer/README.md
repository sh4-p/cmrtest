# GitHub Codespaces / Devcontainer Setup

This project is configured to run seamlessly in GitHub Codespaces.

## Quick Start in Codespaces

1. **Open in Codespaces** - Click the green "Code" button on GitHub and select "Create codespace on main"

2. **Wait for setup** - The devcontainer will automatically configure the environment

3. **Install the application**:
   ```bash
   make install
   ```

4. **Access the application** - Visit the forwarded port 8080

## What Happens Automatically

- ✅ Docker Compose services start automatically
- ✅ PHP 8.4, MySQL, Redis, Nginx containers are created
- ✅ VS Code extensions for Laravel and Vue.js are installed
- ✅ Ports 8080, 3306, 6379 are forwarded

## Important Notes for Codespaces

### Frontend Assets

**Codespaces uses production build** instead of Vite dev server to avoid mixed content errors (HTTPS vs HTTP).

The `make install` command automatically runs `npm run build` to compile assets.

If you make frontend changes:
```bash
make build-assets
```

### Why No HMR (Hot Module Replacement)?

Codespaces runs on HTTPS, but Vite dev server uses HTTP, causing browsers to block "mixed content". The production build is more reliable in cloud environments.

For local development with HMR, use:
```bash
make dev-hmr  # Only works on localhost
```

### Database Access

MySQL runs inside Docker and is accessible:
- **From app**: `mysql:3306`
- **From your machine** (port forwarded): `localhost:3306`

Access MySQL CLI:
```bash
make db-shell
```

### Useful Commands

```bash
make help          # Show all available commands
make shell         # Access app container
make logs          # View all logs
make fresh         # Reset database with fresh data
make test          # Run tests
```

## Troubleshooting

### Blank Page

If you see a blank page:
1. Check if assets are built: `ls -la public/build`
2. Rebuild assets: `make build-assets`
3. Clear Laravel cache: `docker-compose exec app php artisan optimize:clear`

### Database Connection Errors

Wait for MySQL to be healthy:
```bash
docker-compose ps
```

If crm-mysql is not healthy, wait a few seconds or restart:
```bash
make restart
```

### Port Already in Use

Stop all containers and restart:
```bash
make down
make up
```

## Environment Variables

The `.env` file is automatically created from `.env.docker` template.

To customize, edit `.env` directly in the Codespace.

## Debugging

Xdebug is installed and configured in the dev container. Set breakpoints in VS Code and they will work automatically.

## More Information

- Full Docker guide: [docker/DOCKER_GUIDE.md](../docker/DOCKER_GUIDE.md)
- Main README: [README.md](../README.md)
