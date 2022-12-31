# Laravel Telescope

- [Introducción](#introduction)
- [Instalación](#installation)
  - [Instalación sólo local](#local-only-installation)
  - [Configuración](#configuration)
  - [Poda de datos](#data-pruning)
  - [Autorización del panel de control](#dashboard-authorization)
- [Actualización de Telescope](#upgrading-telescope)
- [Filtrado](#filtering)
  - [Entradas](#filtering-entries)
  - [Lotes](#filtering-batches)
- [Etiquetado](#tagging)
- [Observadores disponibles](#available-watchers)
  - [Vigilancia por lotes](#batch-watcher)
  - [cache-watcher">Vigilancia decache](<#\<glossary variable=>)
  - [Vigilancia de comandos](#command-watcher)
  - [Vigilancia de volcados](#dump-watcher)
  - [Vigilancia de eventos](#event-watcher)
  - [Vigilancia de excepciones](#exception-watcher)
  - [gate-watcher">Vigilancia degate](<#\<glossary variable=>)
  - [Vigilancia de clientes HTTP](#http-client-watcher)
  - [Vigilancia de trabajos](#job-watcher)
  - [Vigilancia de registros](#log-watcher)
  - [Vigilancia de correo](#mail-watcher)
  - [Vigilancia de modelos](#model-watcher)
  - [Vigilancia de notificaciones](#notification-watcher)
  - [Vigilancia de consultas](#query-watcher)
  - [Vigilancia de Redis](#redis-watcher)
  - [Vigilancia de solicitudes](#request-watcher)
  - [Vigilancia de horarios](#schedule-watcher)
  - [Observación de vistas](#view-watcher)
- [Visualización de avatares de usuario](#displaying-user-avatars)

[]()

## Introducción

[Laravel Telescope](https://github.com/laravel/telescope) es un maravilloso compañero para tu entorno de desarrollo local de Laravel. Telescope proporciona información sobre las peticiones que llegan a tu aplicación, excepciones, entradas de registro, consultas a bases de datos, trabajos en cola, correo, notificaciones, operaciones de cache, tareas programadas, volcados de variables y mucho más.

<img src="https://laravel.com/img/docs/telescope-example.png"/>

[]()

## Instalación

Puedes utilizar el gestor de paquetes Composer para instalar Telescope en tu proyecto Laravel:

```shell
composer require laravel/telescope
```

Después de instalar Telescope, publica sus activos utilizando el comando `telescope:install` Artisan. Después de instalar Telescope, también debes ejecutar el comando `migrate` para crear las tablas necesarias para almacenar los datos de Telescope:

```shell
php artisan telescope:install

php artisan migrate
```

[]()

#### Personalización de la migración

Si no vas a utilizar las migraciones por defecto de Telescope, debes llamar al método `Telescope::ignoreMigrations` en el método `register` de la clase `App\Providers\AppServiceProvider` de tu aplicación. Puede exportar las migraciones por defecto utilizando el siguiente comando: `php artisan vendor:publish --tag=telescope-migrations`

[]()

### Instalación sólo local

Si planea utilizar Telescope únicamente para asistir su desarrollo local, puede instalar Telescope utilizando la bandera `--dev`:

```shell
composer require laravel/telescope --dev

php artisan telescope:install

php artisan migrate
```

Después de ejecutar `telescope:install`, debe eliminar el registro del proveedor de servicios `TelescopeServiceProvider` del archivo de configuración `config/app.` php de su aplicación. En su lugar, registra manualmente los proveedores de servicios de Telescope en el método `register` de tu clase `App\Providers\AppServiceProvider`. Nos aseguraremos de que el entorno actual es `local` antes de registrar los proveedores:

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

Por último, también debe evitar que el paquete Telescope sea [auto-descubierto](/docs/%7B%7Bversion%7D%7D/packages#package-discovery) añadiendo lo siguiente a su archivo `composer.json`:

```json
"extra": {
    "laravel": {
        "dont-discover": [
            "laravel/telescope"
        ]
    }
},
```

[]()

### Configuración

Después de publicar los activos de Telescope, su archivo de configuración principal se encontrará en `config/telescope.php`. Este archivo de configuración permite configurar las [opciones del observador](#available-watchers). Cada opción de configuración incluye una descripción de su propósito, así que asegúrese de explorar a fondo este archivo.

Si lo desea, puede desactivar por completo la recopilación de datos de Telescope utilizando la opción de configuración `enabled`:

    'enabled' => env('TELESCOPE_ENABLED', true),

[]()

### Poda de datos

Sin poda, la tabla `telescope_entries` puede acumular registros muy rápidamente. Para mitigar esto, debe [programar](/docs/%7B%7Bversion%7D%7D/scheduling) el comando `telescope:prune` Artisan para que se ejecute diariamente:

    $schedule->command('telescope:prune')->daily();

Por defecto, todas las entradas de más de 24 horas serán eliminadas. Puede utilizar la opción `horas` al llamar al comando para determinar cuánto tiempo se conservarán los datos de Telescope. Por ejemplo, el siguiente comando eliminará todos los registros creados hace más de 48 horas:

    $schedule->command('telescope:prune --hours=48')->daily();

[]()

### Autorización del panel de control

Puede acceder al panel de control de Telescope en la ruta `/telescope`. Por defecto, sólo podrás acceder a este tablero en el entorno `local`. En el archivo `app/Providers/TelescopeServiceProvider.` php hay una definición de [gate](/docs/%7B%7Bversion%7D%7D/authorization#gates) [gate](/docs/%7B%7Bversion%7D%7D/authorization#gates) de [autorización](/docs/%7B%7Bversion%7D%7D/authorization#gates). Esta gate de autorización controla el acceso a Telescope en entornos **no locales**. Usted es libre de modificar esta gate según sea necesario para restringir el acceso a su instalación de Telescope:

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewTelescope', function ($user) {
            return in_array($user->email, [
                'taylor@laravel.com',
            ]);
        });
    }

> **Advertencia**  
> Debe asegurarse de cambiar su variable de entorno `APP_ENV` a `producción` en su entorno de producción. De lo contrario, su instalación de Telescope estará a disposición del público.

[]()

## Actualización de Telescope

Al actualizar a una nueva versión principal de Telescope, es importante que revise cuidadosamente [la guía de actualización](https://github.com/laravel/telescope/blob/master/UPGRADE.md).

Además, al actualizar a cualquier nueva versión de Telescope, debe volver a publicar los activos de Telescope:

```shell
php artisan telescope:publish
```

Para mantener los activos actualizados y evitar problemas en futuras actualizaciones, puede añadir el comando `telescope:publish` a los scripts `post-update-cmd` en el archivo `composer.json` de su aplicación:

```json
{
    "scripts": {
        "post-update-cmd": [
            "@php artisan telescope:publish --ansi"
        ]
    }
}
```

[]()

## Filtrado

[]()

### Entradas

Usted puede filtrar los datos que se registran por Telescope a través del closure de `filtro` que se define en su `App\Providers\TelescopeServiceProvider` clase. Por defecto, este closure registra todos los datos en el entorno `local` y las excepciones, los trabajos fallidos, las tareas programadas y los datos con etiquetas supervisadas en todos los demás entornos:

    use Laravel\Telescope\IncomingEntry;
    use Laravel\Telescope\Telescope;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->hideSensitiveRequestDetails();

        Telescope::filter(function (IncomingEntry $entry) {
            if ($this->app->environment('local')) {
                return true;
            }

            return $entry->isReportableException() ||
                $entry->isFailedJob() ||
                $entry->isScheduledTask() ||
                $entry->isSlowQuery() ||
                $entry->hasMonitoredTag();
        });
    }

[]()

### Lotes

Mientras que el closure `filter` filtra los datos de entradas individuales, puede utilizar el método `filterBatch` para registrar un closure que filtre todos los datos de una solicitud o comando de consola determinados. Si el closure devuelve `true`, todas las entradas son registradas por Telescope:

    use Illuminate\Support\Collection;
    use Laravel\Telescope\Telescope;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->hideSensitiveRequestDetails();

        Telescope::filterBatch(function (Collection $entries) {
            if ($this->app->environment('local')) {
                return true;
            }

            return $entries->contains(function ($entry) {
                return $entry->isReportableException() ||
                    $entry->isFailedJob() ||
                    $entry->isScheduledTask() ||
                    $entry->isSlowQuery() ||
                    $entry->hasMonitoredTag();
                });
        });
    }

[]()

## Etiquetado

Telescope permite buscar entradas por "etiqueta". A menudo, las etiquetas son nombres de clases modelo de Eloquent o ID de usuarios autenticados que Telescope añade automáticamente a las entradas. Ocasionalmente, es posible que desee adjuntar sus propias etiquetas personalizadas a las entradas. Para ello, puede utilizar el método `Telescope::tag`. El método `tag` acepta un closure que debe devolver una array de etiquetas. Las etiquetas devueltas por el closure se combinarán con cualquier etiqueta que Telescope adjunte automáticamente a la entrada. Por lo general, usted debe llamar al método de `la etiqueta` en el método de `registro` de su clase `AppProviders\TelescopeServiceProvider`:

    use Laravel\Telescope\IncomingEntry;
    use Laravel\Telescope\Telescope;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->hideSensitiveRequestDetails();

        Telescope::tag(function (IncomingEntry $entry) {
            return $entry->type === 'request'
                        ? ['status:'.$entry->content['response_status']]
                        : [];
        });
     }

[]()

## Observadores disponibles

Los "observadores" de Telescope recopilan datos de la aplicación cuando se ejecuta una solicitud o un comando de consola. Puede personalizar la lista de observadores que le gustaría habilitar dentro de su archivo de configuración `config/telescope.php`:

    'watchers' => [
        Watchers\CacheWatcher::class => true,
        Watchers\CommandWatcher::class => true,
        ...
    ],

Algunos observadores también permiten proporcionar opciones de personalización adicionales:

    'watchers' => [
        Watchers\QueryWatcher::class => [
            'enabled' => env('TELESCOPE_QUERY_WATCHER', true),
            'slow' => 100,
        ],
        ...
    ],

[]()

### Observador de lotes

El observador de lotes registra información sobre [los lotes](/docs/%7B%7Bversion%7D%7D/queues#job-batching) en cola, incluyendo el trabajo y la información de conexión.

[]()

### Vigilancia decache

El observador de cache registra los datos cuando una clave de cache es golpeada, perdida, actualizada y olvidada.

[]()

### Vigilancia de comandos

El observador de comandos registra los argumentos, las opciones, el código de salida y la salida cada vez que se ejecuta un comando de Artisan. Si deseas excluir ciertos comandos de ser registrados por el observador, puedes especificar el comando en la opción `ignorar` dentro de tu archivo `config/telescope.php`:

    'watchers' => [
        Watchers\CommandWatcher::class => [
            'enabled' => env('TELESCOPE_COMMAND_WATCHER', true),
            'ignore' => ['key:generate'],
        ],
        ...
    ],

[]()

### Vigilancia de volcados

El observador de volcado registra y muestra tus volcados de variables en Telescope. Cuando se utiliza Laravel, las variables pueden ser volcados utilizando la función de `volcado` global. La pestaña del observador de volcado debe estar abierta en un navegador para que el volcado se registre, de lo contrario, los volcados serán ignorados por el observador.

[]()

### Vigilancia de eventos

El observador de eventos registra la carga útil, los oyentes y los datos de difusión de los [eventos](/docs/%7B%7Bversion%7D%7D/events) enviados por la aplicación. El observador de eventos ignora los eventos internos del framework Laravel.

[]()

### Vigilancia de excepciones

El observador de excepciones registra los datos y el seguimiento de pila de cualquier excepción notificable que sea lanzada por su aplicación.

[]()

### Vigilancia degate

El observador de gate registra los datos y el resultado de las comprobaciones de [gate y policy](/docs/%7B%7Bversion%7D%7D/authorization) realizadas por tu aplicación. Si deseas excluir ciertas habilidades de ser registradas por el observador, puedes especificarlas en la opción `ignore_abilities` en tu archivo `config/telescope.php`:

    'watchers' => [
        Watchers\GateWatcher::class => [
            'enabled' => env('TELESCOPE_GATE_WATCHER', true),
            'ignore_abilities' => ['viewNova'],
        ],
        ...
    ],

[]()

### Vigilancia de clientes HTTP

El observador de clientes HTTP registra [las peticiones](/docs/%7B%7Bversion%7D%7D/http-client) salientes [de clientes HTTP](/docs/%7B%7Bversion%7D%7D/http-client) realizadas por su aplicación.

[]()

### Vigilancia de trabajos

El observador de trabajos registra los datos y el estado de los [trabajos](/docs/%7B%7Bversion%7D%7D/queues) enviados por la aplicación.

[]()

### Vigilancia de registros

El observador de registros registra los [datos](/docs/%7B%7Bversion%7D%7D/logging) de cualquier registro escrito por su aplicación.

[]()

### Vigilancia de correo

El observador de correo le permite ver una vista previa en el navegador de los [correos electrónicos](/docs/%7B%7Bversion%7D%7D/mail) enviados por su aplicación junto con sus datos asociados. También puede descargar el correo electrónico como un archivo `.eml`.

[]()

### Vigilancia de modelos

El observador del modelo registra los cambios del modelo cada vez que se envía un evento de [modelo](/docs/%7B%7Bversion%7D%7D/eloquent#events) de Eloquent. Puedes especificar qué eventos de modelo deben registrarse a través de la opción de `eventos` del observador:

    'watchers' => [
        Watchers\ModelWatcher::class => [
            'enabled' => env('TELESCOPE_MODEL_WATCHER', true),
            'events' => ['eloquent.created*', 'eloquent.updated*'],
        ],
        ...
    ],

Si desea registrar el número de modelos hidratados durante una petición determinada, active la opción de `hidratación`:

    'watchers' => [
        Watchers\ModelWatcher::class => [
            'enabled' => env('TELESCOPE_MODEL_WATCHER', true),
            'events' => ['eloquent.created*', 'eloquent.updated*'],
            'hydrations' => true,
        ],
        ...
    ],

[]()

### Vigilancia de notificaciones

El observador de notificaciones registra todas las [notificaciones](/docs/%7B%7Bversion%7D%7D/notifications) enviadas por su aplicación. Si la notificación desencadena un correo electrónico y tienes habilitado el observador de correo, el correo electrónico también estará disponible para su previsualización en la pantalla del observador de correo.

[]()

### Vigilancia de consultas

El observador de consultas registra el SQL sin procesar, los enlaces y el tiempo de ejecución de todas las consultas ejecutadas por la aplicación. El observador también etiqueta como `lenta` cualquier consulta que sea más lenta de 100 milisegundos. Puede personalizar el umbral de consultas lentas utilizando la opción `slow` del observador:

    'watchers' => [
        Watchers\QueryWatcher::class => [
            'enabled' => env('TELESCOPE_QUERY_WATCHER', true),
            'slow' => 50,
        ],
        ...
    ],

[]()

### Vigilancia de Redis

El observador de Redis registra todos los comandos [de Redis](/docs/%7B%7Bversion%7D%7D/redis) ejecutados por su aplicación. Si está utilizando Redis para el almacenamiento en caché, los comandos de cache también serán registrados por el observador de Redis.

[]()

### Vigilancia de solicitudes

El observador de peticiones registra la petición, las cabeceras, la sesión y los datos de respuesta asociados a cualquier petición gestionada por la aplicación. Puede limitar los datos de respuesta registrados mediante la opción `size_limit` (en kilobytes):

    'watchers' => [
        Watchers\RequestWatcher::class => [
            'enabled' => env('TELESCOPE_REQUEST_WATCHER', true),
            'size_limit' => env('TELESCOPE_RESPONSE_SIZE_LIMIT', 64),
        ],
        ...
    ],

[]()

### Vigilancia de horarios

El observador de programación registra el comando y la salida de cualquier [tarea programada](/docs/%7B%7Bversion%7D%7D/scheduling) ejecutada por su aplicación.

[]()

### Observación de vistas

El observador de vistas registra el nombre de [la vista](/docs/%7B%7Bversion%7D%7D/views), la ruta, los datos y los "compositores" utilizados al renderizar las vistas.

[]()

## Visualización de avatares de usuario

El panel de control de Telescope muestra el avatar del usuario que se autenticó cuando se guardó una entrada determinada. Por defecto, Telescope recuperará los avatares utilizando el servicio web Gravatar. Sin embargo, puede personalizar la URL del avatar mediante el registro de una devolución de llamada en su clase `AppProviders\TelescopeServiceProvider`. El callback recibirá el ID del usuario y la dirección de correo electrónico y debe devolver la URL de la imagen de avatar del usuario:

    use App\Models\User;
    use Laravel\Telescope\Telescope;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // ...

        Telescope::avatar(function ($id, $email) {
            return '/avatars/'.User::find($id)->avatar_path;
        });
    }
