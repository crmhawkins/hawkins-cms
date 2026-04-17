# Directus

Carpeta para extensiones y snapshots de esquema de Directus.

## Estructura

```
directus/
├── extensions/      # Extensiones custom (hooks, endpoints, etc.)
│   └── .gitkeep
└── snapshots/       # Snapshots del schema para versionar colecciones
    └── .gitkeep
```

## Snapshots de schema

Directus permite exportar/importar el schema (colecciones, campos, permisos)
a un fichero YAML. Esto nos permite versionar el esquema como código.

### Exportar schema actual
```bash
docker compose exec directus npx directus schema snapshot ./snapshots/schema.yaml
```

### Aplicar schema al arrancar
```bash
docker compose exec directus npx directus schema apply ./snapshots/schema.yaml
```

El schema base con las colecciones iniciales (Pages, Posts, Projects, etc.)
se añadirá en la Fase 3 del plan.
