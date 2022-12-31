# Proveedores de servicios

- [Introducción](#introduction)
- [Proveedores de servicios de escritura](#writing-service-providers)
  - [El método de registro](#the-register-method)
  - [El método Boot](#the-boot-method)
- [Registro de proveedores](#registering-providers)
- [Proveedores diferidos](#deferred-providers)

[]()

## Introducción

Los proveedores de servicios son el lugar central de todo el bootstrapping de aplicaciones Laravel. Tu propia aplicación, así como todos los servicios centrales de Laravel, se arrancan a través de proveedores de servicio.

Pero, ¿qué queremos decir con "bootstrapped"? En general, nos referimos a **registrar** cosas, incluyendo el registro de enlaces de contenedores de servicios, escuchadores de eventos, middleware, e incluso rutas. Los proveedores de servicios son el lugar central para configurar tu aplicación.

Si abres el archivo `config/app.php` incluido con Laravel, verás una array de `proveedores`. Estas son todas las clases de proveedores de servicios que se cargarán para su aplicación. Por defecto, un conjunto de proveedores de servicios del núcleo de Laravel se enumeran en esta array. Estos proveedores arrancan los componentes principales de Laravel, como el mailer, la cola, cache y otros. Muchos de estos proveedores son proveedores "diferidos", lo que significa que no se cargarán en cada petición, sino sólo cuando los servicios que proporcionan sean realmente necesarios.

En esta visión general, aprenderás cómo escribir tus propios proveedores de servicios y registrarlos con tu aplicación Laravel.

> **Nota**  
> Si quieres saber más sobre cómo Laravel gestiona las peticiones y funciona internamente, consulta nuestra documentación sobre el [ciclo de vida de las peticiones](/docs/%7B%7Bversion%7D%7D/lifecycle) de Laravel.

[]()

## Escribir proveedores de servicios

Todos los proveedores de servicios extienden la clase `Illuminate\Support\ServiceProvider`. La mayoría de los proveedores de servicios contienen un método `register` y un método `boot`. Dentro del método de `registro`, **sólo** debe **enlazar cosas en el [contenedor de servicios](/docs/%7B%7Bversion%7D%7D/container)**. Nunca debe intentar registrar escuchadores de eventos, rutas o cualquier otra pieza de funcionalidad dentro del método `register`.

El CLI Artisan puede generar un nuevo proveedor a través del comando `make:provider`:

```shell
php artisan make:provider RiakServiceProvider
```

[]()

### El método Register

Como se mencionó anteriormente, dentro del método de `registro`, sólo debe enlazar cosas en el contenedor de [servicios](/docs/%7B%7Bversion%7D%7D/container). Nunca debes intentar registrar escuchadores de eventos, rutas o cualquier otra funcionalidad dentro del método `register`. De lo contrario, podrías utilizar accidentalmente un servicio que es proporcionado por un proveedor de servicios que aún no se ha cargado.

Echemos un vistazo a un proveedor de servicio básico. Dentro de cualquiera de los métodos de tu proveedor de servicios, siempre tienes acceso a la propiedad `$app` que provee acceso al contenedor del servicio:

    <?php

    namespace App\Providers;

    use App\Services\Riak\Connection;
    use Illuminate\Support\ServiceProvider;

    class RiakServiceProvider extends ServiceProvider
    {
        /**
         * Register any application services.
         *
         * @return void
         */
        public function register()
        {
            $this->app->singleton(Connection::class, function ($app) {
                return new Connection(config('riak'));
            });
        }
    }

Este proveedor de servicios sólo define un método de `registro`, y utiliza ese método para definir una implementación de `AppServicesRiak\Connection` en el contenedor de servicios. Si aún no estás familiarizado con el contenedor de servicios de Laravel, consulta [su documentación](/docs/%7B%7Bversion%7D%7D/container).

[]()

#### Las propiedades `bindings` y `singletons`

Si tu proveedor de servicios registra muchos enlaces simples, puede que quieras utilizar las propiedades `bindings` y `singletons` en lugar de registrar manualmente cada enlace del contenedor. Cuando el framework cargue el proveedor de servicios, buscará automáticamente estas propiedades y registrará sus enlaces:

    <?php

    namespace App\Providers;

    use App\Contracts\DowntimeNotifier;
    use App\Contracts\ServerProvider;
    use App\Services\DigitalOceanServerProvider;
    use App\Services\PingdomDowntimeNotifier;
    use App\Services\ServerToolsProvider;
    use Illuminate\Support\ServiceProvider;

    class AppServiceProvider extends ServiceProvider
    {
        /**
         * All of the container bindings that should be registered.
         *
         * @var array
         */
        public $bindings = [
            ServerProvider::class => DigitalOceanServerProvider::class,
        ];

        /**
         * All of the container singletons that should be registered.
         *
         * @var array
         */
        public $singletons = [
            DowntimeNotifier::class => PingdomDowntimeNotifier::class,
            ServerProvider::class => ServerToolsProvider::class,
        ];
    }

[]()

### El método Boot

Entonces, ¿qué pasa si necesitamos registrar un [compositor de vistas](/docs/%7B%7Bversion%7D%7D/views#view-composers) dentro de nuestro proveedor de servicios? Esto debe hacerse dentro del método `boot`. **Este método es llamado después de que todos los demás proveedores de servicios han sido registrados**, lo que significa que tienes acceso a todos los demás servicios que han sido registrados por el framework:

    <?php

    namespace App\Providers;

    use Illuminate\Support\Facades\View;
    use Illuminate\Support\ServiceProvider;

    class ComposerServiceProvider extends ServiceProvider
    {
        /**
         * Bootstrap any application services.
         *
         * @return void
         */
        public function boot()
        {
            View::composer('view', function () {
                //
            });
        }
    }

[]()

#### Inyección de dependencia de métodos de arranque

Puedes introducir dependencias para el método de `arranque` de tu proveedor de servicios. El [contenedor de servicios](/docs/%7B%7Bversion%7D%7D/container) inyectará automáticamente cualquier dependencia que necesites:

    use Illuminate\Contracts\Routing\ResponseFactory;

    /**
     * Bootstrap any application services.
     *
     * @param  \Illuminate\Contracts\Routing\ResponseFactory  $response
     * @return void
     */
    public function boot(ResponseFactory $response)
    {
        $response->macro('serialized', function ($value) {
            //
        });
    }

[]()

## Registro de proveedores

Todos los proveedores de servicios se registran en el archivo de configuración `config/app.php`. Este archivo contiene un array de `proveedores` donde puedes listar los nombres de las clases de tus proveedores de servicios. Por defecto, un conjunto de proveedores de servicios del núcleo de Laravel se enumeran en esta array. Estos proveedores arrancan los componentes centrales de Laravel, como el mailer, la cola, cache y otros.

Para registrar tu proveedor, añádelo al array:

    'providers' => [
        // Other Service Providers

        App\Providers\ComposerServiceProvider::class,
    ],

[]()

## Proveedores diferidos

Si tu proveedor **sólo** registra dependencias en el contenedor de [servicios](/docs/%7B%7Bversion%7D%7D/container), puedes optar por aplazar su registro hasta que una de las dependencias registradas sea realmente necesaria. Aplazar la carga de un proveedor de este tipo mejorará el rendimiento de tu aplicación, ya que no se carga desde el sistema de archivos en cada petición.

Laravel compila y almacena una lista de todos los servicios suministrados por los proveedores de servicios diferidos, junto con el nombre de su clase de proveedor de servicios. Entonces, sólo cuando intentas resolver uno de estos servicios Laravel carga el proveedor de servicios.

Para aplazar la carga de un proveedor, implemente la interfaz `\Illuminate\Contracts\Support\DeferrableProvider` y defina un método `provides`. El método `provides` debe devolver los enlaces del contenedor de servicios registrados por el proveedor:

    <?php

    namespace App\Providers;

    use App\Services\Riak\Connection;
    use Illuminate\Contracts\Support\DeferrableProvider;
    use Illuminate\Support\ServiceProvider;

    class RiakServiceProvider extends ServiceProvider implements DeferrableProvider
    {
        /**
         * Register any application services.
         *
         * @return void
         */
        public function register()
        {
            $this->app->singleton(Connection::class, function ($app) {
                return new Connection($app['config']['riak']);
            });
        }

        /**
         * Get the services provided by the provider.
         *
         * @return array
         */
        public function provides()
        {
            return [Connection::class];
        }
    }
