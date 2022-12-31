# Despliegue

- [Introducción](#introduction)
- [Requisitos del servidor](#server-requirements)
- [Configuración del servidor](#server-configuration)
  - [Nginx](#nginx)
- [Optimización](#optimization)
  - [Optimización del autocargador](#autoloader-optimization)
  - [Optimización de la carga de configuraciones](#optimizing-configuration-loading)
  - [Optimización de la carga de rutas](#optimizing-route-loading)
  - [Optimización de la carga de vistas](#optimizing-view-loading)
- [Modo depuración](#debug-mode)
- [Despliegue con Forge / Vapor](#deploying-with-forge-or-vapor)

[]()

## Introducción

Cuando estés listo para desplegar tu aplicación Laravel en producción, hay algunas cosas importantes que puedes hacer para asegurarte de que tu aplicación se ejecuta de la manera más eficiente posible. En este documento, vamos a cubrir algunos grandes puntos de partida para asegurarse de que su aplicación Laravel se despliega correctamente.

[]()

## Requisitos del servidor

El framework Laravel tiene algunos requisitos de sistema. Debes asegurarte de que tu servidor web tiene la siguiente versión mínima de PHP y extensiones:

<div class="content-list" markdown="1"/>

- PHP >= 8.0
- Extensión PHP BCMath
- Extensión PHP Ctype
- Extensión PHP cURL
- DOM Extensión PHP
- Fileinfo Extensión PHP
- JSON Extensión PHP
- Mbstring Extensión PHP
- OpenSSL Extensión PHP
- PCRE Extensión PHP
- Extensión PHP PDO
- Extensión PHP Tokenizer
- Extensión PHP XML

[object Object]

[]()

## Configuración del servidor

[]()

### Nginx

Si está desplegando su aplicación en un servidor que ejecuta Nginx, puede utilizar el siguiente archivo de configuración como punto de partida para configurar su servidor web. Lo más probable es que este archivo tenga que ser personalizado dependiendo de la configuración de su servidor. **Si desea asistencia en la gestión de su servidor, considere el uso de un servidor de Laravel de primera parte de gestión y despliegue de servicios como [Laravel Forge](https://forge.laravel.com).**

Por favor, asegúrese de que, como en la configuración de abajo, su servidor web dirige todas las peticiones al archivo `public/index.` php de su aplicación. Nunca debes intentar mover el archivo `index.` php a la raíz de tu proyecto, ya que servir la aplicación desde la raíz del proyecto expondrá muchos archivos de configuración sensibles a la Internet pública:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name example.com;
    root /srv/example.com/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

[]()

## Optimización

[]()

### Optimización del Autoloader

Cuando despliegues en producción, asegúrate de que estás optimizando el mapa de autocarga de clases de Composer para que Composer pueda encontrar rápidamente el archivo adecuado para cargar una clase determinada:

```shell
composer install --optimize-autoloader --no-dev
```

> **Nota**  
> Además de optimizar el autocargador, asegúrese siempre de incluir un archivo `composer.lock` en el repositorio de control de código fuente de su proyecto. Las dependencias de tu proyecto pueden ser instaladas mucho más rápido cuando un archivo `composer`.lock está presente.

[]()

### Optimización de la carga de configuraciones

Cuando despliegues tu aplicación en producción, debes asegurarte de ejecutar el comando `config:cache` Artisan durante el proceso de despliegue:

```shell
php artisan config:cache
```

Este comando combinará todos los archivos de configuración de Laravel en un único archivo en caché, lo que reduce en gran medida el número de viajes que el framework debe hacer al sistema de archivos cuando carga tus valores de configuración.

> **Advertencia**  
> Si ejecutas el comando `config:cache` durante tu proceso de despliegue, debes asegurarte de que sólo estás llamando a la función `env` desde dentro de tus archivos de configuración. Una vez que la configuración ha sido cacheada, el archivo `.env` no será cargado y todas las llamadas a la función `env` para variables `.env` devolverán `null`.

[]()

### Optimización de la carga de rutas

Si estás construyendo una aplicación grande con muchas rutas, debes asegurarte de que estás ejecutando el comando `route:cache` Artisan durante tu proceso de despliegue:

```shell
php artisan route:cache
```

Este comando reduce todos tus registros de rutas a una sola llamada de método dentro de un archivo en caché, mejorando el rendimiento del registro de rutas cuando se registran cientos de rutas.

[]()

### Optimización de la carga de vistas

Cuando despliegues tu aplicación a producción, debes asegurarte de ejecutar el comando `view:cache` Artisan durante tu proceso de despliegue:

```shell
php artisan view:cache
```

Este comando precompila todas tus vistas Blade para que no sean compiladas bajo demanda, mejorando el rendimiento de cada petición que devuelve una vista.

[]()

## Modo depuración

La opción debug en tu archivo de configuración config/app.php determina cuánta información sobre un error se muestra realmente al usuario. Por defecto, esta opción está configurada para respetar el valor de la variable de entorno `APP_DEBUG`, que se almacena en el archivo `.env` de su aplicación.

**En su entorno de producción, este valor debe ser siempre `falso`. Si la variable `APP_DEBUG` se establece en `true` en producción, corres el riesgo de exponer valores de configuración sensibles a los usuarios finales de tu aplicación.**

[]()

## Despliegue con Forge / Vapor

[]()

#### Laravel Forge

Si no estás listo para gestionar tu propia configuración del servidor o no te sientes cómodo configurando todos los servicios necesarios para ejecutar una aplicación Laravel robusta, [Laravel Forge](https://forge.laravel.com) es una alternativa maravillosa.

Laravel Forge puede crear servidores en varios proveedores de infraestructura como DigitalOcean, Linode, AWS, y más. Además, Forge instala y gestiona todas las herramientas necesarias para construir aplicaciones Laravel robustas, como Nginx, MySQL, Redis, Memcached, Beanstalk, y más.

> **Nota¿Quieres**una guía completa para desplegar con Laravel Forge? Echa un vistazo a la [serie de vídeos](https://laracasts.com/series/learn-laravel-forge-2022-edition) [Laravel Bootcamp](https://bootcamp.laravel.com/deploying) y Forge [disponible en Laracasts](https://laracasts.com/series/learn-laravel-forge-2022-edition).

[]()

#### Vapor Laravel

Si quieres una plataforma de despliegue totalmente sin servidor y autoescalable adaptada a Laravel, echa un vistazo a Laravel [Vapor](https://vapor.laravel.com). Laravel Vapor es una plataforma de despliegue sin servidor para Laravel, impulsada por AWS. Lanza tu infraestructura Laravel en Vapor y enamórate de la simplicidad escalable de serverless. Laravel Vapor está ajustado por los creadores de Laravel para trabajar sin problemas con el framework para que puedas seguir escribiendo tus aplicaciones Laravel exactamente como estás acostumbrado.
