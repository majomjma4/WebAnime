# Despliegue futuro: cPanel y VPS

Este proyecto ya puede ejecutarse con un punto de entrada puente en la raiz (`index.php`) que redirige a `public/index.php` sin cambiar las URLs visibles.

## Variables de entorno

Crear un archivo `.env` en la raiz del proyecto usando `.env.example` como base.

Variables soportadas:

- `APP_ENV`: entorno actual (`local`, `staging`, `production`)
- `APP_URL`: URL base completa del proyecto
- `DB_HOST`: host de MySQL
- `DB_NAME`: nombre de la base de datos
- `DB_USER`: usuario de la base de datos
- `DB_PASS`: clave de la base de datos

La prioridad actual es:

1. Variables del sistema del servidor
2. Archivo `.env`
3. Valores locales por defecto

## cPanel

Opción 1:
- Subir todo el proyecto dentro de `public_html/WebAnime` o `public_html/WebAnime-master`
- Mantener el `index.php` de la raiz y el `.htaccess` actual
- Configurar `APP_URL=https://tu-dominio.com/WebAnime` o `APP_URL=https://tu-dominio.com/WebAnime-master` segun el nombre final de la carpeta

Opción 2:
- Apuntar el dominio o subdominio directamente a la carpeta `public/`
- En ese caso `APP_URL` puede ser `https://tu-dominio.com`

## VPS

- Clonar el proyecto en una carpeta como `/var/www/webanime`
- Configurar Apache o Nginx para servir `public/` como document root cuando sea posible
- Si se sirve la raiz del proyecto, el `index.php` puente mantiene compatibilidad
- Definir `APP_URL` con el dominio final
- Cargar credenciales reales de MySQL por variables del sistema o `.env`

## Notas

- La app detecta HTTPS incluso detras de proxy con encabezados reenviados comunes.
- No se modifico el flujo actual de rutas ni la estructura de vistas/controladores.
- Para una migracion futura, lo ideal es que el document root termine apuntando a `public/`.
