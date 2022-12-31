# Estructura del Directorio

- [Introducción](#introduction)
- [Directorio raíz](#the-root-directory)
  - [El directorio de `aplicaciones`](#the-root-app-directory)
  - [El directorio `bootstrap`](#the-bootstrap-directory)
  - [El directorio `config`](#the-config-directory)
  - [El directorio `database`](#the-database-directory)
  - [El directorio `lang`](#the-lang-directory)
  - [El directorio `public`](#the-public-directory)
  - [Directorio `resources`](#the-resources-directory)
  - [El directorio de `rutas`](#the-routes-directory)
  - [Directorio `storage`](#the-storage-directory)
  - [tests-directory">Directorio de `tests`](<#the-\<glossary variable=>)
  - [Directorio de `proveedores`](#the-vendor-directory)
- [Directorio de aplicaciones](#the-app-directory)
  - [Directorio de `difusión`](#the-broadcasting-directory)
  - [Directorio de `consolas`](#the-console-directory)
  - [El directorio de `eventos`](#the-events-directory)
  - [Directorio de `excepciones`](#the-exceptions-directory)
  - [Directorio `Http`](#the-http-directory)
  - [Directorio `Jobs`](#the-jobs-directory)
  - [Directorio `Listeners`](#the-listeners-directory)
  - [Directorio `Mail`](#the-mail-directory)
  - [Directorio de `modelos`](#the-models-directory)
  - [Directorio de `notificaciones`](#the-notifications-directory)
  - [policies-directory">Directorio de `policies`](<#the-\<glossary variable=>)
  - [Directorio de `proveedores`](#the-providers-directory)
  - [El Directorio de `Reglas`](#the-rules-directory)

[]()

## Introducción

La estructura de aplicaciones por defecto de Laravel está pensada para proporcionar un gran punto de partida tanto para aplicaciones grandes como pequeñas. Pero usted es libre de organizar su aplicación como quiera. Laravel casi no impone restricciones sobre la ubicación de cualquier clase - siempre y cuando Composer pueda cargar automáticamente la clase.

> **Nota¿Es**nuevo en Laravel? Echa un vistazo al [Laravel Bootcamp](https://bootcamp.laravel.com) para un tour práctico del framework mientras te guiamos en la construcción de tu primera aplicación Laravel.

[]()

## El directorio raíz

[]()

#### Directorio de aplicaciones

El directorio `app` contiene el código central de tu aplicación. Exploraremos este directorio en más detalle pronto; sin embargo, casi todas las clases de tu aplicación estarán en este directorio.

[]()

#### El directorio Bootstrap

El directorio `bootstrap` contiene el archivo `app.` php que arranca el framework. Este directorio también contiene un directorio de `cache` que contiene archivos generados por el framework para optimizar el rendimiento, como los archivos de cache rutas y servicios. Normalmente no debería ser necesario modificar ningún archivo de este directorio.

[]()

#### El directorio de configuración

El directorio `config`, como su nombre indica, contiene todos los archivos de configuración de tu aplicación. Es una buena idea leer todos estos archivos y familiarizarse con todas las opciones disponibles.

[]()

#### El directorio de la base de datos

El directorio de la base de `datos` contiene las migraciones de la base de datos, las fábricas de modelos y las semillas. Si lo deseas, también puedes utilizar este directorio para alojar una base de datos SQLite.

[]()

#### Directorio Lang

El directorio `lang` contiene todos los archivos de idioma de su aplicación.

[]()

#### Directorio público

El directorio `público` contiene el archivo `index.` php, que es el punto de entrada para todas las peticiones que entran en su aplicación y configura la carga automática. Este directorio también aloja sus activos, como imágenes, JavaScript y CSS.

[]()

#### Directorio de recursos

El directorio `resources` contiene tus [vistas](/docs/%7B%7Bversion%7D%7D/views) así como tus activos sin compilar como CSS o JavaScript.

[]()

#### El directorio Routes

El directorio de `rutas` contiene todas las definiciones de rutas para su aplicación. Por defecto, varios archivos de ruta se incluyen con Laravel: `web.php`, `api.php`, `console.php`, y `channels.php`.

El archivo web `.` php contiene rutas que el `RouteServiceProvider` coloca en el grupo de middleware `web`, que proporciona estado de sesión, protección CSRF y cifrado de cookies. Si su aplicación no ofrece una API RESTful sin estado, lo más probable es que todas sus rutas estén definidas en el archivo web `.` php.

El archivo `api.php` contiene rutas que el `RouteServiceProvider` coloca en el grupo `api` middleware. Estas rutas están diseñadas para ser sin estado, por lo que las solicitudes que entran en la aplicación a través de estas rutas están diseñadas para ser autenticadas [a través de tokens](/docs/%7B%7Bversion%7D%7D/sanctum) y no tendrán acceso al estado de la sesión.

El archivo `console.` php es donde puede definir todos sus comandos de consola basados en closure. Cada closure está vinculado a una instancia de comando permitiendo un enfoque simple para interactuar con los métodos IO de cada comando. Aunque este archivo no define rutas HTTP, define puntos de entrada basados en consola (rutas) en su aplicación.

El archivo `channels.` php es donde puedes registrar todos los canales de [transmisión de](/docs/%7B%7Bversion%7D%7D/broadcasting) eventos que tu aplicación soporta.

[]()

#### El directorio de almacenamiento

El directorio de `almacenamiento` contiene tus logs, plantillas Blade compiladas, sesiones basadas en ficheros, cachés de ficheros, y otros ficheros generados por el framework. Este directorio está segregado en los directorios `app`, `framework` y `logs`. El directorio `app` se puede utilizar para almacenar cualquier archivo generado por su aplicación. El directorio `framework` se utiliza para almacenar los archivos y cachés generados por el framework. Por último, el directorio `logs` contiene los archivos de registro de tu aplicación.

El directorio `storage/app/public` puede utilizarse para almacenar archivos generados por el usuario, como avatares de perfil, que deben ser accesibles públicamente. Debe crear un enlace simbólico en `public/storage` que apunte a este directorio. Puede crear el enlace usando el comando `php artisan storage:` link Artisan.

[tests-directory">]()

#### El directorio de tests

El directorio `tests` contiene las tests automatizadas. Ejemplos de tests unitarias [PHPUnit](https://phpunit.de/) y tests características se proporcionan fuera de la caja. Cada clase de test debe tener como sufijo la palabra `test`. Puede ejecutar sus tests usando los comandos `phpunit` o `php vendor/bin/phpunit`. O, si desea una representación más detallada y hermosa de los resultados de sus test, puede ejecutar sus tests utilizando el comando php `artisan test` Artisan.

[]()

#### El directorio vendor

El directorio `vendor` contiene tus dependencias de [Composer](https://getcomposer.org).

[]()

## El directorio de aplicaciones

La mayor parte de su aplicación se encuentra en el directorio `app`. Por defecto, este directorio se encuentra bajo `App` y es autocargado por Composer utilizando el [estándar de autocarga PSR-4](https://www.php-fig.org/psr/psr-4/).

El directorio `app` contiene una variedad de directorios adicionales como `Console`, `Http`, y `Providers`. Piense en los directorios `Console` y `Http` como una API dentro del núcleo de su aplicación. Tanto el protocolo HTTP como la CLI son mecanismos para interactuar con tu aplicación, pero en realidad no contienen lógica de aplicación. En otras palabras, son dos formas de emitir comandos a tu aplicación. El directorio `Console` contiene todos sus comandos Artisan, mientras que el directorio `Http` contiene sus controladores, middleware y peticiones.

Una variedad de otros directorios serán generados dentro del directorio `app` cuando utilices los comandos `make` Artisan para generar clases. Así, por ejemplo, el directorio `app/Jobs` no existirá hasta que ejecutes el comando make `:job` Artisan para generar una clase job.

> **Nota**  
> Muchas de las clases en el directorio `app` pueden ser generadas por Artisan a través de comandos. Para revisar los comandos disponibles, ejecute el comando `php artisan list make` en su terminal.

[]()

#### El directorio de difusión

El directorio `Broadcasting` contiene todas las clases de canales de emisión de la aplicación. Estas clases se generan utilizando el comando `make:channel`. Este directorio no existe por defecto, pero se creará para usted cuando cree su primer canal. Para saber más sobre canales, consulta la documentación sobre difusión [de eventos](/docs/%7B%7Bversion%7D%7D/broadcasting).

[]()

#### El Directorio de Consolas

El directorio `Console` contiene todos los comandos personalizados de Artisan para su aplicación. Estos comandos pueden ser generados utilizando el comando `make:command`. Este directorio también contiene el kernel de tu consola, que es donde tus comandos personalizados de Artisan son registrados y tus [tareas programadas](/docs/%7B%7Bversion%7D%7D/scheduling) son definidas.

[]()

#### El Directorio de Eventos

Este directorio no existe por defecto, pero será creado para ti por los comandos `event:generate` y `make:event` de Artisan. El directorio `Events` contiene [clases de eventos](/docs/%7B%7Bversion%7D%7D/events). Los eventos pueden ser utilizados para alertar a otras partes de tu aplicación de que una determinada acción ha ocurrido, proporcionando una gran flexibilidad y desacoplamiento.

[]()

#### El Directorio de Excepciones

El directorio `Exceptions` contiene el manejador de excepciones de su aplicación y es también un buen lugar para colocar cualquier excepción lanzada por su aplicación. Si desea personalizar la forma en que sus excepciones se registran o renderizan, debe modificar la clase `Handler` en este directorio.

[]()

#### El directorio Http

El directorio `Http` contiene sus controladores, middleware y solicitudes de formularios. Casi toda la lógica para manejar las peticiones que entran en su aplicación se colocará en este directorio.

[]()

#### Directorio Jobs

Este directorio no existe por defecto, pero será creado por usted si ejecuta el comando `make:` job Artisan. El directorio `Jobs` alberga los [trabajos en](/docs/%7B%7Bversion%7D%7D/queues) cola para su aplicación. Los trabajos pueden ser puestos en cola por su aplicación o ejecutados sincrónicamente dentro del ciclo de vida de la solicitud actual. Los trabajos que se ejecutan de forma sincrónica durante la solicitud actual se denominan a veces "comandos", ya que son una implementación del [patrón de comandos](https://en.wikipedia.org/wiki/Command_pattern).

[]()

#### El directorio Listeners

Este directorio no existe por defecto, pero será creado por ti si ejecutas los comandos `event:generate` o `make:listener` de Artisan. El directorio `Listeners` contiene las clases que manejan tus [eventos](/docs/%7B%7Bversion%7D%7D/events). Los escuchadores de eventos reciben una instancia de evento y ejecutan la lógica en respuesta al evento disparado. Por ejemplo, un evento `UserRegistered` puede ser manejado por un listener `SendWelcomeEmail`.

[]()

#### Directorio Mail

Este directorio no existe por defecto, pero será creado por ti si ejecutas el comando `make:mail` Artisan. El directorio `Mail` contiene todas las [clases que representan correos electrónicos](/docs/%7B%7Bversion%7D%7D/mail) enviados por su aplicación. Los objetos Mail le permiten encapsular toda la lógica de construcción de un correo electrónico en una única y simple clase que puede ser enviada utilizando el método `Mail::send`.

[]()

#### Directorio Models

El directorio `Models` contiene todas las [clases modelo de Eloquent](/docs/%7B%7Bversion%7D%7D/eloquent). El ORM de Eloquent incluido con Laravel proporciona una hermosa y simple implementación de ActiveRecord para trabajar con tu base de datos. Cada tabla de la base de datos tiene su correspondiente "Modelo" que se utiliza para interactuar con esa tabla. Los modelos te permiten consultar los datos de tus tablas, así como insertar nuevos registros en la tabla.

[]()

#### Directorio de notificaciones

Este directorio no existe por defecto, pero será creado para usted si ejecuta el comando `make:notification` Artisan. El directorio `Notifications` contiene todas las [notificaciones](/docs/%7B%7Bversion%7D%7D/notifications) "transaccionales" que son enviadas por tu aplicación, tales como notificaciones simples sobre eventos que ocurren dentro de tu aplicación. La característica de notificaciones de Laravel abstrae el envío de notificaciones a través de una variedad de controladores como correo electrónico, Slack, SMS, o almacenadas en una base de datos.

[policies-directory">]()

#### Directorio de policies

Este directorio no existe por defecto, pero será creado para ti si ejecutas el comando `make:policy` Artisan. El directorio `policies` contiene las [clases de policy autorización](/docs/%7B%7Bversion%7D%7D/authorization) para tu aplicación. policies se utilizan para determinar si un usuario puede realizar una acción determinada contra un recurso.

[]()

#### El Directorio de Proveedores

El directorio `Providers` contiene todos los [proveedores de servicios](/docs/%7B%7Bversion%7D%7D/providers) para tu aplicación. Los proveedores de servicios arrancan tu aplicación vinculando servicios en el contenedor de servicios, registrando eventos o realizando cualquier otra tarea para preparar tu aplicación para las peticiones entrantes.

En una aplicación Laravel nueva, este directorio ya contendrá varios proveedores. Eres libre de añadir tus propios proveedores a este directorio según sea necesario.

[]()

#### El Directorio de Reglas

Este directorio no existe por defecto, pero será creado por ti si ejecutas el comando `make:rule` Artisan. El directorio `Rules` contiene los objetos de reglas de validación personalizados para su aplicación. Las reglas se utilizan para encapsular lógica de validación complicada en un objeto simple. Para mayor información, revise la [documentación](/docs/%7B%7Bversion%7D%7D/validation) de validación.
