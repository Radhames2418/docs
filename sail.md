# Laravel Sail

- [Introducción](#introduction)
- [Instalación y configuración](#installation)
  - [Instalación de Sail en aplicaciones existentes](#installing-sail-into-existing-applications)
  - [Configuración de un alias de shell](#configuring-a-shell-alias)
- [Inicio y parada de Sail](#starting-and-stopping-sail)
- [Ejecución de comandos](#executing-sail-commands)
  - [Ejecución de comandos PHP](#executing-php-commands)
  - [Ejecución de comandos de Composer](#executing-composer-commands)
  - [Ejecución de comandos Artisan](#executing-artisan-commands)
  - [Ejecución de comandos Node / NPM](#executing-node-npm-commands)
- [Interacción con bases de datos](#interacting-with-sail-databases)
  - [MySQL](#mysql)
  - [Redis](#redis)
  - [MeiliSearch](#meilisearch)
- [Almacenamiento de archivos](#file-storage)
- [Ejecución de tests](#running-tests)
  - [Laravel Dusk](#laravel-dusk)
- [Vista previa de correos electrónicos](#previewing-emails)
- [Contenedor CLI](#sail-container-cli)
- [Versiones PHP](#sail-php-versions)
- [Versiones de Node](#sail-node-versions)
- [Compartiendo su Sitio](#sharing-your-site)
- [Depuración con Xdebug](#debugging-with-xdebug)
  - [Uso de Xdebug CLI](#xdebug-cli-usage)
  - [Uso del navegador Xdebug](#xdebug-browser-usage)
- [Personalización](#sail-customization)

[]()

## Introducción

[Laravel Sail](https://github.com/laravel/sail) es una interfaz de línea de comandos ligera para interactuar con el entorno de desarrollo Docker por defecto de Laravel. Sail proporciona un gran punto de partida para la construcción de una aplicación Laravel utilizando PHP, MySQL y Redis sin necesidad de experiencia previa en Docker.

En su corazón, Sail es el archivo `docker-compose.yml` y el script `sail` que se almacena en la raíz de tu proyecto. El script `sail` proporciona una CLI con métodos convenientes para interactuar con los contenedores Docker definidos por el archivo `docker-compose.y` ml.

Laravel Sail es compatible con macOS, Linux y Windows (a través de [WSL2](https://docs.microsoft.com/en-us/windows/wsl/about)).

[]()

## Instalación y configuración

Laravel Sail se instala automáticamente con todas las nuevas aplicaciones Laravel para que pueda empezar a usarlo inmediatamente. Para saber cómo crear una nueva aplicación Laravel, consulta la [documentación de instalación](/docs/%7B%7Bversion%7D%7D/installation) de Laravel para tu sistema operativo. Durante la instalación, se te pedirá que elijas con qué servicios soportados por Sail va a interactuar tu aplicación.

[]()

### Instalación de Sail en aplicaciones existentes

Si estás interesado en usar Sail con una aplicación Laravel existente, puedes simplemente instalar Sail usando el gestor de paquetes Composer. Por supuesto, estos pasos suponen que su entorno de desarrollo local existente le permite instalar las dependencias de Composer:

```shell
composer require laravel/sail --dev
```

Después de instalar Sail, puedes ejecutar el comando `sail:install` Artisan. Este comando publicará el archivo `docker-compose.yml` de Sail en la raíz de tu aplicación:

```shell
php artisan sail:install
```

Finalmente, puedes iniciar Sail. Para continuar aprendiendo a utilizar Sail, por favor continúa leyendo el resto de esta documentación:

```shell
./vendor/bin/sail up
```

[]()

#### Uso de Devcontainers

Si desea desarrollar dentro de un [Devcontainer](https://code.visualstudio.com/docs/remote/containers), puede proporcionar la opción `--devcontainer` al comando `sail:install`. La opción `--devcontainer` indicará al comando sail `:` install que publique un archivo ` .devcontainer/devcontainer.json  `predeterminado en la raíz de su aplicación:

```shell
php artisan sail:install --devcontainer
```

[]()

### Configuración de un alias de shell

Por defecto, los comandos de Sail se invocan utilizando el script `vendor/bin/sail` que se incluye con todas las nuevas aplicaciones Laravel:

```shell
./vendor/bin/sail up
```

Sin embargo, en lugar de escribir repetidamente `vendor/bin/sail` para ejecutar los comandos de Sail, puede que desees configurar un alias de shell que te permita ejecutar los comandos de Sail más fácilmente:

```shell
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

Para asegurarse de que está siempre disponible, puede añadirlo al archivo de configuración de su shell en su directorio personal, como `~/.zshrc` o `~/.bashrc`, y luego reiniciar su shell.

Una vez configurado el alias del intérprete de órdenes, puede ejecutar los comandos de Sail simplemente escribiendo `sail`. El resto de los ejemplos de esta documentación asumirá que has configurado este alias:

```shell
sail up
```

[]()

## Inicio y parada de Sail

El archivo `docker-compose.yml` de Laravel Sail define una variedad de contenedores Docker que trabajan juntos para ayudarte a construir aplicaciones Laravel. Cada uno de estos contenedores es una entrada dentro de la configuración de `servicios` de tu archivo docker-compose `.y` ml. El contenedor `laravel.test` es el contenedor de aplicación principal que servirá tu aplicación.

Antes de iniciar Sail, debes asegurarte de que no hay otros servidores web o bases de datos ejecutándose en tu ordenador local. Para iniciar todos los contenedores Docker definidos en el archivo `docker-compose.yml` de tu aplicación, debes ejecutar el comando `up`:

```shell
sail up
```

Para iniciar todos los contenedores Docker en segundo plano, puedes iniciar Sail en modo "detached":

```shell
sail up -d
```

Una vez iniciados los contenedores de la aplicación, puedes acceder al proyecto desde tu navegador web en: http: [//localhost.](http://localhost)

Para detener todos los contenedores, basta con pulsar Control + C para detener la ejecución del contenedor. O, si los contenedores se están ejecutando en segundo plano, puede utilizar el comando `stop`:

```shell
sail stop
```

[]()

## Ejecución de comandos

Cuando se utiliza Laravel Sail, su aplicación se ejecuta dentro de un contenedor Docker y está aislado de su equipo local. Sin embargo, Sail proporciona una manera conveniente de ejecutar varios comandos contra tu aplicación, tales como comandos arbitrarios de PHP, comandos de Artisan, comandos de Composer y comandos de Node / NPM.

**Al leer la documentación de Laravel, a menudo verás referencias a Composer, Artisan, y comandos Node / NPM que no hacen referencia a Sail.** Esos ejemplos asumen que estas herramientas están instaladas en tu ordenador local. Si estás usando Sail para tu entorno de desarrollo local de Laravel, deberías ejecutar esos comandos usando Sail:

```shell
# Running Artisan commands locally...
php artisan queue:work

# Running Artisan commands within Laravel Sail...
sail artisan queue:work
```

[]()

### Ejecución de comandos PHP

Los comandos PHP pueden ejecutarse utilizando el comando `php`. Por supuesto, estos comandos se ejecutarán utilizando la versión de PHP que esté configurada para su aplicación. Para saber más sobre las versiones de PHP disponibles para Laravel Sail, consulta la [documentación de versiones de PHP](#sail-php-versions):

```shell
sail php --version

sail php script.php
```

[]()

### Ejecución de comandos de Composer

Los comandos de Composer pueden ejecutarse utilizando el comando `composer`. El contenedor de aplicaciones de Laravel Sail incluye una instalación de Composer 2.x:

```nothing
sail composer require laravel/sanctum
```

[]()

#### Instalación de dependencias de Composer para aplicaciones existentes

Si estás desarrollando una aplicación con un equipo, puede que no seas tú quien cree inicialmente la aplicación Laravel. Por lo tanto, ninguna de las dependencias de Composer de la aplicación, incluida Sail, se instalará después de clonar el repositorio de la aplicación en tu equipo local.

Puedes instalar las dependencias de la aplicación navegando hasta el directorio de la aplicación y ejecutando el siguiente comando. Este comando utiliza un pequeño contenedor Docker que contiene PHP y Composer para instalar las dependencias de la aplicación:

```shell
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
```

Cuando uses la imagen `laravelsail/phpXX-composer`, debes usar la misma versión de PHP que planeas usar para tu aplicación`(74`, `80`, o `81`).

[]()

### Ejecución de comandos Artisan

Los comandos de Laravel Artisan pueden ser ejecutados usando el comando `artisan`:

```shell
sail artisan queue:work
```

[]()

### Ejecución de comandos Node / NPM

Los comandos Node pueden ser ejecutados usando el comando `node` mientras que los comandos NPM pueden ser ejecutados usando el comando `npm`:

```shell
sail node --version

sail npm run dev
```

Si lo desea, puede utilizar Yarn en lugar de NPM:

```shell
sail yarn
```

[]()

## Interacción con bases de datos

[]()

### MySQL

Como habrás notado, el archivo `docker-compose.yml` de tu aplicación contiene una entrada para un contenedor MySQL. Este contenedor utiliza un [volumen Docker](https://docs.docker.com/storage/volumes/) para que los datos almacenados en tu base de datos persistan incluso al detener y reiniciar tus contenedores.

Además, la primera vez que se inicie el contenedor MySQL, creará dos bases de datos para usted. La primera base de datos se denomina utilizando el valor de su variable de entorno `DB_DATABASE` y es para su desarrollo local. La segunda es una base de datos dedicada a pruebas llamada `testing` y asegurará que tus tests no interfieran con tus datos de desarrollo.

Una vez que haya iniciado sus contenedores, puede conectarse a la instancia MySQL dentro de su aplicación estableciendo su variable de entorno `DB_HOST` dentro del archivo `.env` de su aplicación a `mysql`.

Para conectarse a la base de datos MySQL de su aplicación desde su máquina local, puede utilizar una aplicación gráfica de gestión de bases de datos como [TablePlus](https://tableplus.com). Por defecto, la base de datos MySQL es accesible en el puerto `localhost` 3306.

[]()

### Redis

El archivo `docker-compose.yml` de tu aplicación también contiene una entrada para un contenedor [Redis](https://redis.io). Este contenedor utiliza un [volumen Docker](https://docs.docker.com/storage/volumes/) para que los datos almacenados en sus datos Redis persistan incluso al detener y reiniciar sus contenedores. Una vez que hayas iniciado tus contenedores, puedes conectarte a la instancia de Redis dentro de tu aplicación estableciendo tu variable de entorno `REDIS_HOST` dentro del archivo `.env` de tu aplicación a `redis`.

Para conectarte a la base de datos Redis de tu aplicación desde tu máquina local, puedes usar una aplicación gráfica de gestión de bases de datos como [TablePlus](https://tableplus.com). Por defecto, la base de datos Redis es accesible en el puerto `localhost` 6379.

[]()

### MeiliSearch

Si eligió instalar el servicio [MeiliSearch](https://www.meilisearch.com) al instalar Sail, el archivo `docker-compose.yml` de su aplicación contendrá una entrada para este potente motor de búsqueda [compatible](https://github.com/meilisearch/meilisearch-laravel-scout) con [Laravel Scout](/docs/%7B%7Bversion%7D%7D/scout). Una vez que haya iniciado sus contenedores, puede conectarse a la instancia MeiliSearch dentro de su aplicación mediante el establecimiento de su variable de entorno `MEILISEARCH_HOST` a `http://meilisearch:7700.`

Desde su máquina local, puede acceder al panel de administración basado en web de MeiliSearch navegando a `http://localhost:7700` en su navegador web.

[]()

## Almacenamiento de archivos

Si planea utilizar Amazon S3 para almacenar archivos mientras ejecuta su aplicación en su entorno de producción, es posible que desee instalar el servicio [MinIO](https://min.io) al instalar Sail. MinIO proporciona una API compatible con S3 que puede utilizar para desarrollar localmente utilizando el controlador de almacenamiento de archivos `s3` de Laravel sin crear buckets de almacenamiento de "prueba" en su entorno S3 de producción. Si eliges instalar MinIO mientras instalas Sail, se añadirá una sección de configuración de MinIO al archivo `docker-compose.yml` de tu aplicación.

Por defecto, el archivo de configuración de `sistemas de archivos` de tu aplicación ya contiene una configuración de disco para el disco `s3`. Además de utilizar este disco para interactuar con Amazon S3, puede utilizarlo para interactuar con cualquier servicio de almacenamiento de archivos compatible con S3, como MinIO, simplemente modificando las variables de entorno asociadas que controlan su configuración. Por ejemplo, cuando se utiliza MinIO, la configuración de su variable de entorno filesystem debe definirse de la siguiente manera:

```ini
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=sail
AWS_SECRET_ACCESS_KEY=password
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=local
AWS_ENDPOINT=http://minio:9000
AWS_USE_PATH_STYLE_ENDPOINT=true
```

Para que la integración Flysystem de Laravel genere URLs adecuadas al utilizar MinIO, debes definir la variable de entorno `AWS_URL` de forma que coincida con la URL local de tu aplicación e incluya el nombre del bucket en la ruta URL:

```ini
AWS_URL=http://localhost:9000/local
```

Puede crear buckets a través de la consola MinIO, disponible en `http://localhost:8900.` El nombre de usuario por defecto para la consola MinIO es `sail` mientras que la contraseña por defecto es `password`.

> **Advertencia**\
> La generación de URLs de almacenamiento temporal a través del método `temporaryUrl` no está soportada cuando se utiliza MinIO.

[]()

## Ejecución de tests

Laravel proporciona un soporte de pruebas increíble fuera de la caja, y usted puede utilizar el comando de `test` de Sail para ejecutar su [característica de](/docs/%7B%7Bversion%7D%7D/testing) aplicaciones [y tests unitarias](/docs/%7B%7Bversion%7D%7D/testing). Cualquier opción CLI que sea aceptada por PHPUnit también puede ser pasada al comando `test`:

```shell
sail test

sail test --group orders
```

El comando `test` `test` Sail es equivalente a ejecutar el comando de `test` Artisan:

```shell
sail artisan test
```

Por defecto, Sail creará una base de datos de `pruebas` dedicada para que tus tests no interfieran con el estado actual de tu base de datos. En una instalación por defecto de Laravel, Sail también configurará tu archivo `phpunit.xml` para usar esta base de datos cuando ejecutes tus tests:

```xml
<env name="DB_DATABASE" value="testing"/>
```

[]()

### Laravel Dusk

[Laravel Dusk](/docs/%7B%7Bversion%7D%7D/dusk) proporciona una API de pruebas y automatización del navegador expresiva y fácil de usar. Gracias a Sail, puedes ejecutar estas tests sin necesidad de instalar Selenium u otras herramientas en tu ordenador local. Para empezar, descomenta el servicio Selenium en el archivo `docker-compose.yml` de tu aplicación:

```yaml
selenium:
    image: 'selenium/standalone-chrome'
    volumes:
        - '/dev/shm:/dev/shm'
    networks:
        - sail
```

A continuación, asegúrese de que el servicio `laravel.test` en el archivo `docker-compose.yml` de su aplicación tiene una entrada `depends_on` para `selenium`:

```yaml
depends_on:
    - mysql
    - redis
    - selenium
```

Por último, puede ejecutar su conjunto de test Dusk iniciando Sail y ejecutando el comando `dusk`:

```shell
sail dusk
```

[]()

#### Selenium en Apple Silicon

Si su máquina local contiene un chip Apple Silicon, su servicio `selenium` debe utilizar la imagen `seleniarm/standalone-chromium`:

```yaml
selenium:
    image: 'seleniarm/standalone-chromium'
    volumes:
        - '/dev/shm:/dev/shm'
    networks:
        - sail
```

[]()

## Vista previa de correos electrónicos

El archivo docker-compose. `yml` por defecto de Laravel Sail contiene una entrada de servicio para [MailHog](https://github.com/mailhog/MailHog). MailHog intercepta los correos electrónicos enviados por tu aplicación durante el desarrollo local y proporciona una cómoda interfaz web para que puedas previsualizar los mensajes de correo electrónico en tu navegador. Cuando se utiliza Sail, el host por defecto de MailHog es `mailhog` y está disponible a través del puerto 1025:

```ini
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_ENCRYPTION=null
```

Cuando Sail se está ejecutando, puedes acceder a la interfaz web de MailHog en: <http://localhost:8025>

[]()

## Contenedor CLI

A veces puede que desee iniciar una sesión Bash dentro del contenedor de su aplicación. Puede utilizar el comando `shell` para conectarse al contenedor de su aplicación, permitiéndole inspeccionar sus archivos y servicios instalados, así como ejecutar comandos shell arbitrarios dentro del contenedor:

```shell
sail shell

sail root-shell
```

Para iniciar una nueva sesión [Laravel Tinker](https://github.com/laravel/tinker), puedes ejecutar el comando `tinker`:

```shell
sail tinker
```

[]()

## Versiones PHP

Sail actualmente soporta servir su aplicación a través de PHP 8.2, 8.1, PHP 8.0, o PHP 7.4. La versión de PHP por defecto utilizada por Sail es actualmente PHP 8.1. Para cambiar la versión de PHP que se utiliza para servir su aplicación, debe actualizar la definición de `construcción` del contenedor `laravel.test` en el archivo `docker-compose.yml` de su aplicación:

```yaml
# PHP 8.2
context: ./vendor/laravel/sail/runtimes/8.2

# PHP 8.1
context: ./vendor/laravel/sail/runtimes/8.1

# PHP 8.0
context: ./vendor/laravel/sail/runtimes/8.0

# PHP 7.4
context: ./vendor/laravel/sail/runtimes/7.4
```

Además, es posible que desee actualizar su nombre de `imagen` para reflejar la versión de PHP que está siendo utilizado por su aplicación. Esta opción también se define en el archivo `docker-compose.y` ml de su aplicación:

```yaml
image: sail-8.1/app
```

Después de actualizar el archivo docker-compose. `yml` de su aplicación, debe reconstruir las imágenes de su contenedor:

```shell
sail build --no-cache

sail up
```

[]()

## Versiones de Node

Sail instala Node 16 por defecto. Para cambiar la versión de Node que se instala al construir tus imágenes, puedes actualizar la definición `build.args` del servicio `laravel.test` en el archivo `docker-compose.` yml de tu aplicación:

```yaml
build:
    args:
        WWWGROUP: '${WWWGROUP}'
        NODE_VERSION: '14'
```

Después de actualizar el archivo docker-compose `.` yml de tu aplicación, debes reconstruir tus imágenes de contenedor:

```shell
sail build --no-cache

sail up
```

[]()

## Compartiendo su Sitio

A veces puede que necesite compartir su sitio públicamente con el fin de obtener una vista previa de su sitio para un colega o para test las integraciones webhook con su aplicación. Para compartir su sitio, puede utilizar el comando `share`. Después de ejecutar este comando, recibirás una URL `laravel-sail.site` aleatoria que podrás utilizar para acceder a tu aplicación:

```shell
sail share
```

Cuando compartas tu sitio a través del comando `share`, debes configurar los proxies de confianza de tu aplicación dentro del middleware `TrustProxies`. De lo contrario, los ayudantes de generación de URL como `url` y `route` no podrán determinar el host HTTP correcto que debe utilizarse durante la generación de URL:

    /**
     * The trusted proxies for this application.
     *
     * @var array|string|null
     */
    protected $proxies = '*';

Si desea elegir el subdominio para su sitio compartido, puede proporcionar la opción de `subdominio` al ejecutar el comando `share`:

```shell
sail share --subdomain=my-sail-site
```

> **Nota**\
> El comando `share` funciona con [Expose](https://github.com/beyondcode/expose), un servicio de túnel de código abierto de [BeyondCode](https://beyondco.de).

[]()

## Depuración con Xdebug

La configuración Docker de Laravel Sail incluye soporte para [Xdebug](https://xdebug.org/), un depurador popular y potente para PHP. Para habilitar Xdebug, necesitarás añadir algunas variables al archivo `.env` de tu aplicación para [configurar Xdebug](https://xdebug.org/docs/step_debug#mode). Para habilitar Xdebug debe configurar el modo(s) apropiado(s) antes de iniciar Sail:

```ini
SAIL_XDEBUG_MODE=develop,debug,coverage
```

#### Configuración IP del host Linux

Internamente, la variable de entorno `XDEBUG_CONFIG` se define como `client_host=host.docker.internal` para que Xdebug se configure correctamente para Mac y Windows (WSL2). Si su máquina local está ejecutando Linux, debe asegurarse de que está ejecutando Docker Engine 17.06.0+ y Compose 1.16.0+. De lo contrario, tendrá que definir manualmente esta variable de entorno como se muestra a continuación.

En primer lugar, debe determinar la dirección IP de host correcta para agregar a la variable de entorno ejecutando el siguiente comando. Típicamente, el `<container-name>` debe ser el nombre del contenedor que sirve a su aplicación y a menudo termina con `_laravel.test_1`:

```shell
docker inspect -f {{range.NetworkSettings.Networks}}{{.Gateway}}{{end}} <container-name>
```

Una vez que hayas obtenido la dirección IP correcta del host, debes definir la variable `SAIL_XDEBUG_CONFIG` dentro del archivo `.env` de tu aplicación:

```ini
SAIL_XDEBUG_CONFIG="client_host=<host-ip-address>"
```

[]()

### Uso de Xdebug CLI

Un comando `sail debug` puede ser utilizado para iniciar una sesión de depuración cuando se ejecuta un comando Artisan:

```shell
# Run an Artisan command without Xdebug...
sail artisan migrate

# Run an Artisan command with Xdebug...
sail debug migrate
```

[]()

### Uso del navegador Xdebug

Para depurar tu aplicación mientras interactúas con ella a través de un navegador web, sigue las [instrucciones proporcionadas por Xdebug](https://xdebug.org/docs/step_debug#web-application) para iniciar una sesión Xdebug desde el navegador web.

Si está utilizando PhpStorm, por favor revise la documentación de JetBrain relativa a la [depuración sin configuración](https://www.jetbrains.com/help/phpstorm/zero-configuration-debugging.html).

> **Advertencia**\
> Laravel Sail depende de artisan `serve` para servir tu aplicación. El comando `artisan` serve sólo acepta las variables `XDEBUG_CONFIG` y `XDEBUG_MODE` a partir de la versión 8.53.0 de Laravel. Las versiones anteriores de Laravel (8.52.0 e inferiores) no soportan estas variables y no aceptarán conexiones de depuración.

[]()

## Personalización

Dado que Sail es sólo Docker, eres libre de personalizar casi todo sobre él. Para publicar los propios Dockerfiles de Sail, puedes ejecutar el comando `sail:publish`:

```shell
sail artisan sail:publish
```

Después de ejecutar este comando, los Dockerfiles y otros archivos de configuración utilizados por Laravel Sail se colocarán dentro de un directorio `docker` en el directorio raíz de tu aplicación. Después de personalizar tu instalación de Sail, es posible que desees cambiar el nombre de la imagen para el contenedor de la aplicación en el archivo `docker-compose.yml` de tu aplicación. Después de hacerlo, reconstruya los contenedores de su aplicación utilizando el comando `build`. Asignar un nombre único a la imagen de la aplicación es particularmente importante si está utilizando Sail para desarrollar múltiples aplicaciones Laravel en una sola máquina:

```shell
sail build --no-cache
```
