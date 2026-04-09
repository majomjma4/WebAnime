# Estructura MVC

## Arquitectura actual

- `public/index.php` recibe la petición y la delega al controlador frontal.
- `app/Controllers/FrontController.php` resuelve la ruta y ejecuta la acción del controlador correspondiente.
- `app/Routing/Router.php` contiene la lógica de coincidencia de rutas, incluyendo alias estáticos y rutas parametrizadas.
- `app/routes.php` es la fuente central de verdad para las rutas web y API.
- `app/Controllers` contiene los controladores de la aplicación, separados por responsabilidades web y API.
- `app/Models` contiene las clases orientadas a persistencia y acceso a datos.
- `app/Services` contiene servicios reutilizables de negocio o infraestructura.
- `app/Views` contiene únicamente las plantillas de presentación.

## Convenciones aplicadas

- La lógica especial de rutas como `detail/{detail_ref}` vive en el router y no en el front controller.
- Los helpers reutilizables de URL y resolución de detalle están centralizados en el bootstrap para evitar duplicación.
- Los controladores se mantienen enfocados en orquestación, autorización y respuesta de vistas o API.
- La configuración de despliegue y entorno está externalizada mediante variables de entorno.

## Camino recomendado para crecer

- Mantener el SQL complejo y la integración con APIs externas dentro de modelos o servicios, no en vistas ni enrutamiento.
- Agregar nuevas rutas dinámicas mediante `patterns` dentro de `app/routes.php`.
- Si la API sigue creciendo, crear un `ApiController` base compartido para respuestas JSON, validaciones y guards de autorización.
