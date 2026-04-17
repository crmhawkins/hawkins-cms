# Desarrollo local

## Requisitos

- Docker + Docker Compose
- Node 20+ (solo si quieres ejecutar el frontend fuera del contenedor)

## Arranque rápido

```bash
bash scripts/setup.sh
docker compose up -d
```

Ver logs en vivo:
```bash
docker compose logs -f
```

Parar:
```bash
docker compose down
```

Parar y borrar datos (⚠️ destruye DB, MinIO y uploads):
```bash
docker compose down -v
```

## Puertos expuestos

| Servicio | Puerto local |
|---|---|
| Next.js frontend | 3000 |
| Directus admin/API | 8055 |
| phpMyAdmin | 8081 |
| MinIO API | 9000 |
| MinIO console | 9001 |
| LibreTranslate API | 5000 |
| MariaDB | 3306 |

Puedes ajustarlos en `docker-compose.override.yml` si chocan con otros
servicios en tu máquina.

## Desarrollo del frontend con hot-reload

El contenedor `frontend` corre en modo build. Para desarrollo con
`next dev` (hot-reload) → levanta solo los servicios de backend y ejecuta
Next.js localmente:

```bash
# Levanta solo backend
docker compose up -d mariadb directus minio libretranslate phpmyadmin

# En otra terminal:
cd frontend
npm install
npm run dev
```

Ahora `http://localhost:3000` recarga solo al guardar ficheros.

## Inspeccionar la DB

- phpMyAdmin: http://localhost:8081 (usuario/pass en .env)
- O con cualquier cliente MySQL:
  ```
  host:     localhost
  port:     3306
  user:     hawkins
  password: (ver .env)
  database: hawkins_cms
  ```
