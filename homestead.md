# Laravel Homestead

- [Introducción](#introduction)
- [Instalación y configuración](#installation-and-setup)
  - [Primeros pasos](#first-steps)
  - [Configuración de Homestead](#configuring-homestead)
  - [Configuración de sitios Nginx](#configuring-nginx-sites)
  - [Configuración de servicios](#configuring-services)
  - [Lanzamiento de Vagrant Box](#launching-the-vagrant-box)
  - [Instalación por Proyecto](#per-project-installation)
  - [Instalación de características opcionales](#installing-optional-features)
  - [Aliases](#aliases)
- [Actualización de Homestead](#updating-homestead)
- [Uso diario](#daily-usage)
  - [Conexión a través de SSH](#connecting-via-ssh)
  - [Adición de sitios adicionales](#adding-additional-sites)
  - [Variables de entorno](#environment-variables)
  - [Puertos](#ports)
  - [Versiones PHP](#php-versions)
  - [Conexión a bases de datos](#connecting-to-databases)
  - [Copias de Seguridad](#database-backups)
  - [Configuración de Cron Schedules](#configuring-cron-schedules)
  - [Configuración de MailHog](#configuring-mailhog)
  - [Configuración de Minio](#configuring-minio)
  - [Laravel Dusk](#laravel-dusk)
  - [Compartiendo tu entorno](#sharing-your-environment)
- [Depuración y perfiles](#debugging-and-profiling)
  - [Depuración de Peticiones Web con Xdebug](#debugging-web-requests)
  - [Depuración de aplicaciones CLI](#debugging-cli-applications)
  - [Perfilado de aplicaciones con Blackfire](#profiling-applications-with-blackfire)
- [Interfaces de red](#network-interfaces)
- [Ampliación de Homestead](#extending-homestead)
- [Configuración específica del proveedor](#provider-specific-settings)
  - [VirtualBox](#provider-specific-virtualbox)

[]()

## Introducción

Laravel se esfuerza por hacer que toda la experiencia de desarrollo de PHP sea agradable, incluyendo su entorno de desarrollo local. Laravel [Homestead](https://github.com/laravel/homestead) es una caja Vagrant oficial y pre-empaquetada que le proporciona un entorno de desarrollo maravilloso sin necesidad de instalar PHP, un servidor web, y cualquier otro software de servidor en su máquina local.

[Vagrant](https://www.vagrantup.com) proporciona una forma sencilla y elegante de gestionar y aprovisionar Máquinas Virtuales. Las cajas Vagrant son completamente desechables. ¡Si algo va mal, puede destruir y volver a crear la caja en cuestión de minutos!

Homestead se ejecuta en cualquier sistema Windows, macOS o Linux e incluye Nginx, PHP, MySQL, PostgreSQL, Redis, Memcached, Node y todo el software que necesitas para desarrollar increíbles aplicaciones Laravel.

> **Advertencia**  
> Si estás usando Windows, puede que necesites habilitar la virtualización de hardware (VT-x). Normalmente se puede activar a través de la BIOS. Si estás usando Hyper-V en un sistema UEFI, puede que además necesites desactivar Hyper-V para poder acceder a VT-x.

[]()

### Software incluido

<style>
    #lista-de-software &gt; ul {
        column-count: 2; -moz-column-count: 2; -webkit-column-count: 2;
        column-gap: 5em; -moz-column-gap: 5em; -webkit-column-gap: 5em;
        altura de línea: 1.9;
    }
</style>

<div id="software-list" markdown="1"/>

- Ubuntu 20.04
- Git
- PHP 8.1
- PHP 8.0
- PHP 7.4
- PHP 7.3
- PHP 7.2
- PHP 7.1
- PHP 7.0
- PHP 5.6
- Nginx
- MySQL 8.0
- lmm
- Sqlite3
- PostgreSQL 13
- Compositor
- Docker
- Node (con Yarn, Bower, Grunt y Gulp)
- Redis
- Memcached
- Beanstalkd
- Mailhog
- avahi
- ngrok
- Xdebug
- XHProf / Tideways / XHGui
- wp-cli

[object Object]

[]()

### Software opcional

<style>
    #software-list &gt; ul {
        column-count: 2; -moz-column-count: 2; -webkit-column-count: 2;
        column-gap: 5em; -moz-column-gap: 5em; -webkit-column-gap: 5em;
        altura de línea: 1.9;
    }
</style>

<div id="software-list" markdown="1"/>

- Apache
- Blackfire
- Cassandra
- Chronograf
- CouchDB
- Crystal y Lucky Framework
- Elasticsearch
- EventStoreDB
- Gearman
- Ir a
- Grafana
- InfluxDB
- MariaDB
- Meilisearch
- MinIO
- MongoDB
- Neo4j
- Oh My Zsh
- Open Resty
- PM2
- Python
- R
- RabbitMQ
- RVM (Gestor de versiones de Ruby)
- Solr
- TimescaleDB
- Trader (extensión PHP)
- Utilidades Webdriver y Laravel Dusk

[object Object]

[]()

## Instalación y configuración

[]()

### Primeros pasos

Antes de lanzar su entorno Homestead, debe instalar [Vagrant](https://www.vagrantup.com/downloads.html), así como uno de los siguientes proveedores compatibles:

- [VirtualBox 6.1.x](https://www.virtualbox.org/wiki/Downloads)
- [Parallels](https://www.parallels.com/products/desktop/)

Todos estos paquetes de software proporcionan instaladores visuales fáciles de usar para todos los sistemas operativos populares.

Para utilizar el proveedor Parallels, deberá instalar [el plug-in Parallels Vagrant](https://github.com/Parallels/vagrant-parallels). Es gratuito.

[]()

#### Instalación de Homestead

Puede instalar Homestead clonando el repositorio de Homestead en su máquina anfitriona. Considera clonar el repositorio en una carpeta `Homestead` dentro de tu directorio "home", ya que la máquina virtual Homestead servirá como host para todas tus aplicaciones Laravel. A lo largo de esta documentación, nos referiremos a este directorio como tu "directorio Homestead":

```shell
git clone https://github.com/laravel/homestead.git ~/Homestead
```

Después de clonar el repositorio de Laravel Homestead, debe comprobar la rama de `lanzamiento`. Esta rama siempre contiene la última versión estable de Homestead:

```shell
cd ~/Homestead

git checkout release
```

A continuación, ejecute el comando `bash init.sh` desde el directorio Homestead para crear el archivo de configuración `Homestead.yam` l. El archivo `Homestead`.yaml es donde configurará todos los ajustes para su instalación de Homestead. Este archivo se colocará en el directorio de Homestead:

```shell
# macOS / Linux...
bash init.sh

# Windows...
init.bat
```

[]()

### Configuración de Homestead

[]()

#### Configuración de su proveedor

La clave de `proveedor` en su archivo Homestead `.` yaml indica qué proveedor Vagrant se debe utilizar: `virtualbox` o `parallels`:

    provider: virtualbox

> **Advertencia**  
> Si está utilizando Apple Silicon, debe añadir `box: laravel/homestead-arm` a su archivo `Homestead`.yaml. Apple Silicon requiere el proveedor Parallels.

[]()

#### Configuración de carpetas compartidas

La propiedad `folders` del archivo `Homestead`.yaml enumera todas las carpetas que desea compartir con su entorno Homestead. A medida que se modifiquen los archivos de estas carpetas, se mantendrán sincronizados entre su máquina local y el entorno virtual de Homestead. Puede configurar tantas carpetas compartidas como sea necesario:

```yaml
folders:
    - map: ~/code/project1
      to: /home/vagrant/project1
```

> **Advertencia**  
> Los usuarios de Windows no deben utilizar la sintaxis de ruta `~/` y en su lugar deben utilizar la ruta completa a su proyecto, como `C:\Users\user\Code\project1`.

Siempre debe asignar aplicaciones individuales a su propia asignación de carpetas en lugar de asignar un único directorio grande que contenga todas sus aplicaciones. Al asignar una carpeta, la máquina virtual debe realizar un seguimiento de todas las IO de disco para *cada* archivo de la carpeta. Puede experimentar un rendimiento reducido si tiene un gran número de archivos en una carpeta:

```yaml
folders:
    - map: ~/code/project1
      to: /home/vagrant/project1
    - map: ~/code/project2
      to: /home/vagrant/project2
```

> **Advertencia**  
> Nunca debe montar `.` (el directorio actual) cuando utilice Homestead. Esto causa que Vagrant no mapee la carpeta actual a `/vagrant` y romperá características opcionales y causará resultados inesperados durante el aprovisionamiento.

Para habilitar [NFS](https://www.vagrantup.com/docs/synced-folders/nfs.html), puede añadir una opción de `tipo` a su asignación de carpetas:

```yaml
folders:
    - map: ~/code/project1
      to: /home/vagrant/project1
      type: "nfs"
```

> **Advertencia**  
> Cuando utilice NFS en Windows, debería considerar instalar el plug-in [vagrant-winnfsd](https://github.com/winnfsd/vagrant-winnfsd). Este plug-in mantendrá los permisos correctos de usuario / grupo para los archivos y directorios dentro de la máquina virtual Homestead.

También puedes pasar cualquier opción soportada por las [Carpetas Sincronizadas](https://www.vagrantup.com/docs/synced-folders/basic_usage.html) de Vagrant listándolas bajo la clave `options`:

```yaml
folders:
    - map: ~/code/project1
      to: /home/vagrant/project1
      type: "rsync"
      options:
          rsync__args: ["--verbose", "--archive", "--delete", "-zz"]
          rsync__exclude: ["node_modules"]
```

[]()

### Configuración de sitios Nginx

¿No está familiarizado con Nginx? No hay problema. La propiedad `sites` de su archivo `Homestead.yaml` le permite asignar fácilmente un "dominio" a una carpeta en su entorno Homestead. Se incluye una configuración de sitio de muestra en el archivo `Homestead`.yaml. De nuevo, puede añadir tantos sitios a su entorno Homestead como sea necesario. Homestead puede servir como un cómodo entorno virtualizado para cada aplicación Laravel en la que estés trabajando:

```yaml
sites:
    - map: homestead.test
      to: /home/vagrant/project1/public
```

Si cambias la propiedad `sites` después de aprovisionar la máquina virtual Homestead, debes ejecutar el comando `vagrant reload --provision` en tu terminal para actualizar la configuración Nginx en la máquina virtual.

> **Advertencia**  
> Los scripts de Homestead están construidos para ser lo más idempotentes posible. Sin embargo, si experimentas problemas durante el aprovisionamiento, debes destruir y reconstruir la máquina ejecutando el comando vagrant `destroy && vagrant up`.

[]()

#### Resolución del nombre de host

Homestead publica nombres de host utilizando `mDNS` para la resolución automática de host. Si estableces `hostname: homestead` en tu archivo `Homestead.yaml`, el host estará disponible en `homestead.local`. Las distribuciones de escritorio de macOS, iOS y Linux incluyen soporte `mDNS` por defecto. Si utiliza Windows, debe instalar [Bonjour Print Services para Windows](https://support.apple.com/kb/DL999?viewlocale=en_US\&locale=en_US).

El uso de nombres de host automáticos funciona mejor para [instalaciones por proyecto](#per-project-installation) de Homestead. Si aloja varios sitios en una sola instancia de Homestead, puede agregar los "dominios" para sus sitios web en el archivo `hosts` en su máquina. El archivo `hosts` redirigirá las solicitudes de sus sitios Homestead en su máquina virtual Homestead. En macOS y Linux, este archivo se encuentra en `/etc/hosts`. En Windows, se encuentra en `C:\Windows\System32\drivers\etc\hosts`. Las líneas que añadas a este archivo tendrán el siguiente aspecto:

    192.168.56.56  homestead.test

Asegúrate de que la dirección IP listada es la establecida en tu archivo `Homestead.yaml`. Una vez que haya añadido el dominio a su archivo de `hosts` y puesto en marcha la caja Vagrant podrá acceder al sitio a través de su navegador web:

```shell
http://homestead.test
```

[]()

### Configurando Servicios

Homestead inicia varios servicios de forma predeterminada, sin embargo, puede personalizar los servicios que están habilitados o deshabilitados durante el aprovisionamiento. Por ejemplo, puede habilitar PostgreSQL y deshabilitar MySQL modificando la opción de `servicios` dentro de su archivo `Homestead.yaml`:

```yaml
services:
    - enabled:
        - "postgresql"
    - disabled:
        - "mysql"
```

Los servicios especificados se iniciarán o detendrán en función de su orden en las directivas `habilitadas` y `deshabilitadas`.

[]()

### Lanzamiento de Vagrant Box

Una vez que haya editado el `Homestead`.yaml a su gusto, ejecute el comando `vagrant up` desde su directorio Homestead. Vagrant arrancará la máquina virtual y configurará automáticamente tus carpetas compartidas y sitios Nginx.

Para destruir la máquina, puede utilizar el comando `vagrant destroy`.

[]()

### Instalación por Proyecto

En lugar de instalar Homestead globalmente y compartir la misma máquina virtual Homestead a través de todos sus proyectos, puede configurar una instancia Homestead para cada proyecto que gestiona. Instalar Homestead por proyecto puede ser beneficioso si desea enviar un `Vagrantfile` con su proyecto, permitiendo a otros que trabajan en el proyecto `vagrant` inmediatamente después de clonar el repositorio del proyecto.

Puede instalar Homestead en su proyecto utilizando el gestor de paquetes Composer:

```shell
composer require laravel/homestead --dev
```

Una vez instalado Homestead, invoca el comando `make` de Homestead para generar el archivo `Vagrantfile` y `Homestead.yaml` para tu proyecto. Estos archivos se colocarán en la raíz del proyecto. El comando `make` configurará automáticamente las directivas de `sitios` y `carpetas` en el archivo `Homestead`.yaml:

```shell
# macOS / Linux...
php vendor/bin/homestead make

# Windows...
vendor\\bin\\homestead make
```

A continuación, ejecute el comando `vagrant up` en su terminal y acceda a su proyecto en `http://homestead.test` en su navegador. Recuerda, todavía tendrás que añadir una entrada en el archivo `/etc/hosts` para `homestead.test` o el dominio de tu elección si no estás utilizando la [resolución](#hostname-resolution) automática [de nombres de host](#hostname-resolution).

[]()

### Instalación de características opcionales

El software opcional se instala utilizando la opción `features` dentro de su archivo `Homestead`.yaml. La mayoría de las características se pueden activar o desactivar con un valor booleano, mientras que algunas características permiten múltiples opciones de configuración:

```yaml
features:
    - blackfire:
        server_id: "server_id"
        server_token: "server_value"
        client_id: "client_id"
        client_token: "client_value"
    - cassandra: true
    - chronograf: true
    - couchdb: true
    - crystal: true
    - elasticsearch:
        version: 7.9.0
    - eventstore: true
        version: 21.2.0
    - gearman: true
    - golang: true
    - grafana: true
    - influxdb: true
    - mariadb: true
    - meilisearch: true
    - minio: true
    - mongodb: true
    - neo4j: true
    - ohmyzsh: true
    - openresty: true
    - pm2: true
    - python: true
    - r-base: true
    - rabbitmq: true
    - rvm: true
    - solr: true
    - timescaledb: true
    - trader: true
    - webdriver: true
```

[]()

#### Elasticsearch

Puede especificar una versión compatible de Elasticsearch, que debe ser un número de versión exacto (major.minor.patch). La instalación por defecto creará un cluster llamado 'homestead'. Nunca debe dar a Elasticsearch más de la mitad de la memoria del sistema operativo, así que asegúrese de que su máquina virtual Homestead tiene al menos el doble de la asignación de Elasticsearch.

> **Nota**  
> Consulta la [documentación de Elasticsearch](https://www.elastic.co/guide/en/elasticsearch/reference/current) para aprender a personalizar tu configuración.

[]()

#### MariaDB

Habilitar MariaDB eliminará MySQL e instalará MariaDB. MariaDB normalmente sirve como un reemplazo de MySQL, por lo que debe seguir utilizando el controlador de base de datos `mysql` en la configuración de base de datos de su aplicación.

[]()

#### MongoDB

La instalación por defecto de MongoDB establecerá el nombre de usuario de la base de datos en `homestead` y la contraseña correspondiente en `secret`.

[]()

#### Neo4j

La instalación por defecto de Neo4j establecerá el nombre de usuario de la base de datos en `homestead` y la contraseña correspondiente en `secret`. Para acceder al navegador Neo4j, visita `http://homestead.test:7474` a través de tu navegador web. Los puertos `7687` (Bolt), `7474` (HTTP) y `7473` (HTTPS) están listos para servir peticiones del cliente Neo4j.

[]()

### Aliases

Puedes añadir alias Bash a tu máquina virtual Homestead modificando el archivo `aliases` dentro de tu directorio Homestead:

```shell
alias c='clear'
alias ..='cd ..'
```

Después de haber actualizado el archivo `aliases`, debes volver a aprovisionar la máquina virtual Homestead usando el comando `vagrant reload --provision`. Esto asegurará que sus nuevos alias estén disponibles en la máquina.

[]()

## Actualización de Homestead

Antes de empezar a actualizar Homestead debe asegurarse de que ha eliminado su máquina virtual actual ejecutando el siguiente comando en su directorio Homestead:

```shell
vagrant destroy
```

A continuación, debe actualizar el código fuente de Homestead. Si clonó el repositorio, puede ejecutar los siguientes comandos en la ubicación en la que clonó originalmente el repositorio:

```shell
git fetch

git pull origin release
```

Estos comandos extraen el último código de Homestead del repositorio de GitHub, obtienen las últimas etiquetas y, a continuación, comprueban la última versión etiquetada. Puede encontrar la última versión estable en la [página de versiones de GitHub](https://github.com/laravel/homestead/releases) de Homestead.

Si ha instalado Homestead a través del archivo `composer.json` de su proyecto, debe asegurarse de que su archivo `composer.json` contiene `"laravel/homestead":` " `^12"` y actualiza tus dependencias:

```shell
composer update
```

A continuación, debe actualizar la caja Vagrant utilizando el comando `vagrant box update`:

```shell
vagrant box update
```

Después de actualizar la caja Vagrant, debe ejecutar el comando `bash init.sh` desde el directorio Homestead con el fin de actualizar los archivos de configuración adicionales de Homestead. Se le preguntará si desea sobrescribir los archivos `Homestead.yaml`, `after.sh` y `alias` existentes:

```shell
# macOS / Linux...
bash init.sh

# Windows...
init.bat
```

Por último, tendrá que regenerar su máquina virtual Homestead para utilizar la última instalación de Vagrant:

```shell
vagrant up
```

[]()

## Uso diario

[]()

### Conexión vía SSH

Puede SSH en su máquina virtual ejecutando el comando de terminal `vagrant ssh` desde su directorio Homestead.

[]()

### Adición de sitios adicionales

Una vez que tu entorno Homestead está aprovisionado y funcionando, es posible que desees añadir sitios Nginx adicionales para tus otros proyectos Laravel. Puedes ejecutar tantos proyectos Laravel como desees en un único entorno Homestead. Para añadir un sitio adicional, añade el sitio a tu archivo `Homestead.yaml`.

```yaml
sites:
    - map: homestead.test
      to: /home/vagrant/project1/public
    - map: another.test
      to: /home/vagrant/project2/public
```

> **Advertencia**  
> Debes asegurarte de haber configurado un [mapeo de carpetas](#configuring-shared-folders) para el directorio del proyecto antes de añadir el sitio.

Si Vagrant no está gestionando automáticamente su archivo "hosts", puede que tenga que añadir el nuevo sitio a ese archivo también. En macOS y Linux, este archivo se encuentra en `/etc/hosts`. En Windows, se encuentra en `C:\Windows\System32\drivers\etc\hosts`:

    192.168.56.56  homestead.test
    192.168.56.56  another.test

Una vez añadido el sitio, ejecuta el comando `vagrant reload --provision` terminal desde tu directorio Homestead.

[]()

#### Tipos de sitio

Homestead soporta varios "tipos" de sitios que le permiten ejecutar fácilmente proyectos que no se basan en Laravel. Por ejemplo, podemos añadir fácilmente una aplicación Statamic a Homestead utilizando el tipo de sitio `Statamic`:

```yaml
sites:
    - map: statamic.test
      to: /home/vagrant/my-symfony-project/web
      type: "statamic"
```

Los tipos de sitio disponibles son: `apache`, `apigility`, `expressive`, `laravel` (el predeterminado), `proxy`, `silverstripe`, `statamic`, `symfony2`, `symfony4` y `zf`.

[]()

#### Parámetros del sitio

Puede añadir valores `fastcgi_param` de Nginx adicionales a su sitio a través de la directiva de sitio `params`:

```yaml
sites:
    - map: homestead.test
      to: /home/vagrant/project1/public
      params:
          - key: FOO
            value: BAR
```

[]()

### Variables de entorno

Puedes definir variables de entorno globales añadiéndolas a tu archivo Homestead `.` yaml:

```yaml
variables:
    - key: APP_ENV
      value: local
    - key: FOO
      value: bar
```

Después de actualizar el archivo `Homestead`.yaml, asegúrate de volver a aprovisionar la máquina ejecutando el comando `vagrant reload --provision`. Esto actualizará la configuración PHP-FPM para todas las versiones PHP instaladas y también actualizará el entorno para el usuario `vagrant`.

[]()

### Puertos

Por defecto, los siguientes puertos son reenviados a tu entorno Homestead:

<div class="content-list" markdown="1"/>

- **HTTP**: 8000 → Reenvía a 80
- **HTTPS**: 44300 → Reenvía a 443

[object Object]

[]()

#### Reenvío de puertos adicionales

Si lo desea, puede reenviar puertos adicionales a la caja Vagrant definiendo una entrada de configuración de `puertos` dentro de su archivo Homestead `.yam` l. Después de actualizar el archivo `Homestead`.yaml, asegúrese de volver a aprovisionar la máquina ejecutando el comando `vagrant reload --provision`:

```yaml
ports:
    - send: 50000
      to: 5000
    - send: 7777
      to: 777
      protocol: udp
```

A continuación se muestra una lista de puertos de servicio Homestead adicionales que es posible que desee asignar desde su máquina host a su caja Vagrant:

<div class="content-list" markdown="1"/>

- **SSH:** 2222 → A 22
- **ngrok UI:** 4040 → A 4040
- **MySQL:** 33060 → A 3306
- **PostgreSQL:** 54320 → Hasta 5432
- **MongoDB**: 27017 → A 27017
- **Mailhog**: 8025 → A 8025
- **Minio**: 9600 → A 9600

[object Object]

[]()

### Versiones PHP

Homestead 6 introdujo soporte para ejecutar múltiples versiones de PHP en la misma máquina virtual. Puede especificar qué versión de PHP a utilizar para un sitio determinado dentro de su archivo `Homestead.yaml`. Las versiones de PHP disponibles son: "5.6", "7.0", "7.1", "7.2", "7.3", "7.4", "8.0" (la predeterminada) y "8.1":

```yaml
sites:
    - map: homestead.test
      to: /home/vagrant/project1/public
      php: "7.1"
```

[Dentro de su máquina virtual](#connecting-via-ssh) Homestead, puede utilizar cualquiera de las versiones de PHP compatibles a través de la CLI:

```shell
php5.6 artisan list
php7.0 artisan list
php7.1 artisan list
php7.2 artisan list
php7.3 artisan list
php7.4 artisan list
php8.0 artisan list
php8.1 artisan list
```

Puede cambiar la versión predeterminada de PHP utilizado por la CLI mediante la emisión de los siguientes comandos desde dentro de su máquina virtual Homestead:

```shell
php56
php70
php71
php72
php73
php74
php80
php81
```

[]()

### Conexión a bases de datos

Una base de datos `Homestead` está configurado tanto para MySQL y PostgreSQL fuera de la caja. Para conectarse a su base de datos MySQL o PostgreSQL desde el cliente de base de datos de su máquina host, debe conectarse a `127.0.0.1` en el puerto `33060` (MySQL) o `54320` (PostgreSQL). El nombre de usuario y contraseña para ambas bases de datos es `homestead` / `secret`.

> **Advertencia**  
> Sólo debe utilizar estos puertos no estándar cuando se conecta a las bases de datos de su máquina host. Utilizarás los puertos por defecto 3306 y 5432 en el archivo de configuración de `base de datos` de tu aplicación Laravel ya que Laravel se ejecuta *dentro* de la máquina virtual.

[]()

### Copias de seguridad de bases de datos

Homestead puede realizar automáticamente una copia de seguridad de su base de datos cuando se destruye su máquina virtual Homestead. Para utilizar esta función, debe utilizar Vagrant 2.1.0 o superior. O, si está utilizando una versión anterior de Vagrant, debe instalar el plug-in `vagrant-triggers`. Para habilitar las copias de seguridad automáticas de la base de datos, añada la siguiente línea a su archivo `Homestead.yaml`:

    backup: true

Una vez configurado, Homestead exportará sus bases de datos a los directorios `.backup/mysql_backup` y `.backup/postgres_backup` cuando se ejecute el comando `vagrant destroy`. Estos directorios se pueden encontrar en la carpeta donde instaló Homestead o en la raíz de su proyecto si está utilizando el método de [instalación por proyecto](#per-project-installation).

[]()

### Configuración de Cron Schedules

Laravel proporciona una manera conveniente de [programar trabajos cron](/docs/%7B%7Bversion%7D%7D/scheduling) mediante la programación de un solo comando schedule: `run` Artisan para que se ejecute cada minuto. El comando `schedule`:run examinará la programación de trabajos definida en tu clase `App\Console\Kernel` para determinar qué tareas programadas ejecutar.

Si desea que el comando `schedule:run` se ejecute para un sitio Homestead, puede establecer la opción `schedule` en `true` al definir el sitio:

```yaml
sites:
    - map: homestead.test
      to: /home/vagrant/project1/public
      schedule: true
```

El cron job para el sitio se definirá en el directorio `/etc/cron.d` de la máquina virtual de Homestead.

[]()

### Configuración de MailHog

[MailHog](https://github.com/mailhog/MailHog) le permite interceptar su correo electrónico saliente y examinarlo sin enviar realmente el correo a sus destinatarios. Para empezar, actualice el archivo `.env` de su aplicación para utilizar la siguiente configuración de correo:

```ini
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

Una vez configurado MailHog, puede acceder al panel de control de MailHog en `http://localhost:8025.`

[]()

### Configuración de Minio

[Minio](https://github.com/minio/minio) es un servidor de almacenamiento de objetos de código abierto con una API compatible con Amazon S3. Para instalar Minio, actualice su archivo `Homestead.yaml` con la siguiente opción de configuración en la sección de [características](#installing-optional-features):

    minio: true

Por defecto, Minio está disponible en el puerto 9600. Puede acceder al panel de control de Minio visitando `http://localhost:9600.` La clave de acceso predeterminada es `homestead`, mientras que la clave secreta predeterminada es `secretkey`. Cuando acceda a Minio, debe utilizar siempre la región `us-east-1`.

Para utilizar Minio, deberá ajustar la configuración del disco S3 en el archivo de configuración `config/filesystems.php` de su aplicación. Deberá añadir la opción `use_path_style_endpoint` a la configuración del disco, así como cambiar la clave `url` a `endpoint`:

    's3' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
        'endpoint' => env('AWS_URL'),
        'use_path_style_endpoint' => true,
    ]

Por último, asegúrese de que su archivo `.env` contiene las siguientes opciones:

```ini
AWS_ACCESS_KEY_ID=homestead
AWS_SECRET_ACCESS_KEY=secretkey
AWS_DEFAULT_REGION=us-east-1
AWS_URL=http://localhost:9600
```

Para aprovisionar buckets "S3" alimentados por Minio, añada una directiva `buckets` a su archivo Homestead. `yaml`. Después de definir tus buckets, debes ejecutar el comando `vagrant reload --provision` en tu terminal:

```yaml
buckets:
    - name: your-bucket
      policy: public
    - name: your-private-bucket
      policy: none
```

Los valores de `policy` admitidos son: `none`, `download`, `upload` y `public`.

[]()

### Laravel Dusk

Para ejecutar tests de [Laravel Dusk](/docs/%7B%7Bversion%7D%7D/dusk) en Homestead, debes habilitar la función [`webdriver`](#installing-optional-features) en la configuración de Homestead:

```yaml
features:
    - webdriver: true
```

Después de habilitar la función `webdriver`, debes ejecutar el comando `vagrant reload --provision` en tu terminal.

[]()

### Compartir su entorno

A veces es posible que desee compartir lo que está trabajando actualmente con compañeros de trabajo o un cliente. Vagrant tiene soporte incorporado para esto a través del comando `vagrant share`; sin embargo, esto no funcionará si tienes múltiples sitios configurados en tu archivo `Homestead.yaml`.

Para resolver este problema, Homestead incluye su propio comando `share`. Para empezar, [SSH en su máquina virtual Homestead](#connecting-via-ssh) a través de `vagrant ssh` y ejecute el comando share `homestead.test`. Este comando compartirá el sitio homestead `.` `test` `test` de su archivo de configuración `Homestead.yaml`. Puede sustituir `homestead.test` por cualquiera de sus otros sitios configurados:

```shell
share homestead.test
```

Después de ejecutar el comando, verás aparecer una pantalla Ngrok que contiene el registro de actividad y las URL de acceso público para el sitio compartido. Si deseas especificar una región personalizada, un subdominio u otra opción de ejecución de Ngrok, puedes añadirlos a tu comando `share`:

```shell
share homestead.test -region=eu -subdomain=laravel
```

> **Advertencia**  
> Recuerda, Vagrant es intrínsecamente inseguro y estás exponiendo tu máquina virtual a Internet cuando ejecutas el comando `share`.

[]()

## Depuración y perfiles

[]()

### Depuración de peticiones web con Xdebug

Homestead incluye soporte para la depuración de pasos utilizando [Xdebug](https://xdebug.org). Por ejemplo, puede acceder a una página en su navegador y PHP se conectará a su IDE para permitir la inspección y modificación del código en ejecución.

Por defecto, Xdebug ya está en ejecución y listo para aceptar conexiones. Si necesita habilitar Xdebug en la CLI, ejecute el comando `sudo phpenmod xdebug` dentro de su máquina virtual Homestead. A continuación, siga las instrucciones de su IDE para habilitar la depuración. Por último, configure su navegador para activar Xdebug con una extensión o [bookmarklet](https://www.jetbrains.com/phpstorm/marklets/).

> **Advertencia**  
> Xdebug causa que PHP corra significativamente más lento. Para deshabilitar Xdebug, ejecute `sudo phpdismod xdebug` dentro de su máquina virtual Homestead y reinicie el servicio FPM.

[]()

#### Autostarting Xdebug

Cuando se depuran tests cionales que realizan peticiones al servidor web, es más fácil autoiniciar la depuración que modificar tests para que pasen a través de una cabecera o cookie personalizada para activar la depuración. Para forzar que Xdebug se inicie automáticamente, modifique el archivo `/etc/php/7.x/fpm/conf.d/20-xdebug.ini` dentro de su máquina virtual Homestead y añada la siguiente configuración:

```ini
; If Homestead.yaml contains a different subnet for the IP address, this address may be different...
xdebug.client_host = 192.168.10.1
xdebug.mode = debug
xdebug.start_with_request = yes
```

[]()

### Depuración de aplicaciones CLI

Para depurar una aplicación PHP CLI, utilice el alias de shell `xphp` dentro de su máquina virtual Homestead:

    xphp /path/to/script

[]()

### Perfilado de aplicaciones con Blackfire

[Blackfire](https://blackfire.io/docs/introduction) es un servicio de perfilado de peticiones web y aplicaciones CLI. Ofrece una interfaz de usuario interactiva que muestra los datos del perfil en gráficos de llamadas y líneas de tiempo. Está diseñado para su uso en desarrollo, puesta en marcha y producción, sin sobrecarga para los usuarios finales. Además, Blackfire ofrece comprobaciones de rendimiento, calidad y seguridad del código y de los parámetros de configuración de `php.ini`.

[Blackfire Player](https://blackfire.io/docs/player/index) es una aplicación de código abierto de rastreo, pruebas y raspado web que puede trabajar conjuntamente con Blackfire para crear guiones de perfiles.

Para habilitar Blackfire, utilice la opción "features" (características) del archivo de configuración de Homestead:

```yaml
features:
    - blackfire:
        server_id: "server_id"
        server_token: "server_value"
        client_id: "client_id"
        client_token: "client_value"
```

Las credenciales del servidor Blackfire y las credenciales del cliente [requieren una cuenta Blackfire](https://blackfire.io/signup). Blackfire ofrece varias opciones para perfilar una aplicación, incluyendo una herramienta CLI y una extensión de navegador. [Consulte la documentación de Blackfire para obtener más información](https://blackfire.io/docs/php/integrations/laravel/index).

[]()

## Interfaces de red

La propiedad `networks` del archivo `Homestead.yaml` configura las interfaces de red para su máquina virtual Homestead. Puede configurar tantas interfaces como sea necesario:

```yaml
networks:
    - type: "private_network"
      ip: "192.168.10.20"
```

Para habilitar una interfaz [en puente](https://www.vagrantup.com/docs/networking/public_network.html), configure un ajuste de `puente` para la red y cambie el tipo de red a `public_network`:

```yaml
networks:
    - type: "public_network"
      ip: "192.168.10.20"
      bridge: "en1: Wi-Fi (AirPort)"
```

Para habilitar [DHCP](https://www.vagrantup.com/docs/networking/public_network.html), simplemente elimine la opción `ip` de su configuración:

```yaml
networks:
    - type: "public_network"
      bridge: "en1: Wi-Fi (AirPort)"
```

[]()

## Ampliación de Homestead

Puede ampliar Homestead utilizando el script `after.sh` en la raíz de su directorio Homestead. Dentro de este archivo, puede añadir cualquier comando shell que sea necesario para configurar y personalizar correctamente su máquina virtual.

Al personalizar Homestead, Ubuntu puede preguntarle si desea mantener la configuración original de un paquete o sobrescribirlo con un nuevo archivo de configuración. Para evitar esto, debe utilizar el siguiente comando al instalar paquetes con el fin de evitar sobrescribir cualquier configuración previamente escrita por Homestead:

```shell
sudo apt-get -y \
    -o Dpkg::Options::="--force-confdef" \
    -o Dpkg::Options::="--force-confold" \
    install package-name
```

[]()

### Personalizaciones de usuario

Al utilizar Homestead con su equipo, es posible que desee ajustar Homestead para adaptarse mejor a su estilo de desarrollo personal. Para ello, puede crear un archivo `user-customizations.sh` en la raíz de su directorio de Homestead (el mismo directorio que contiene su archivo `Homestead.yaml` ). En este archivo, puede realizar cualquier personalización que desee; sin embargo, el `archivo user-customizations.sh` no debe estar controlado por versiones.

[]()

## Configuración específica del proveedor

[]()

### VirtualBox

[]()

#### `natdnshostresolver`

Por defecto, Homestead configura el ajuste `natdnshostresolver` en `on`. Esto permite a Homestead utilizar la configuración DNS de su sistema operativo host. Si desea anular este comportamiento, añada las siguientes opciones de configuración a su archivo `Homestead.yaml`:

```yaml
provider: virtualbox
natdnshostresolver: 'off'
```

[]()

#### Enlaces simbólicos en Windows

Si los enlaces simbólicos no funcionan correctamente en su máquina Windows, puede que tenga que añadir el siguiente bloque a su `archivo Vagrantfile`:

```ruby
config.vm.provider "virtualbox" do |v|
    v.customize ["setextradata", :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate/v-root", "1"]
end
```
