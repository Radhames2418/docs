# Contenedor de servicio

- [Introducción](#introduction)
  - [Resolución de configuración cero](#zero-configuration-resolution)
  - [Cuándo utilizar el contenedor](#when-to-use-the-container)
- [Vinculación](#binding)
  - [Conceptos básicos](#binding-basics)
  - [Vinculación de interfaces a implementaciones](#binding-interfaces-to-implementations)
  - [Vinculación contextual](#contextual-binding)
  - [Primitivas de enlace](#binding-primitives)
  - [Variables tipadas de enlace](#binding-typed-variadics)
  - [Etiquetado](#tagging)
  - [Ampliación de enlaces](#extending-bindings)
- [Resolución de](#resolving)
  - [El método Make](#the-make-method)
  - [Inyección automática](#automatic-injection)
- [Invocación e inyección de métodos](#method-invocation-and-injection)
- [Eventos de contenedor](#container-events)
- [PSR-11](#psr-11)

[]()

## Introducción

El contenedor de servicios de Laravel es una potente herramienta para gestionar las dependencias de clases y realizar la inyección de dependencias. La inyección de dependencias es una frase elegante que esencialmente significa esto: las dependencias de clase se "inyectan" en la clase a través del constructor o, en algunos casos, métodos "setter".

Veamos un ejemplo sencillo:

    <?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;
    use App\Repositories\UserRepository;
    use App\Models\User;

    class UserController extends Controller
    {
        /**
         * The user repository implementation.
         *
         * @var UserRepository
         */
        protected $users;

        /**
         * Create a new controller instance.
         *
         * @param  UserRepository  $users
         * @return void
         */
        public function __construct(UserRepository $users)
        {
            $this->users = $users;
        }

        /**
         * Show the profile for the given user.
         *
         * @param  int  $id
         * @return Response
         */
        public function show($id)
        {
            $user = $this->users->find($id);

            return view('user.profile', ['user' => $user]);
        }
    }

En este ejemplo, el `UserController` necesita recuperar usuarios de una fuente de datos. Por lo tanto, **inyectaremos** un servicio capaz de recuperar usuarios. En este contexto, lo más probable es que nuestro `UserRepository` utilice [Eloquent](/docs/%7B%7Bversion%7D%7D/eloquent) para recuperar la información de los usuarios de la base de datos. Sin embargo, dado que el repositorio está inyectado, podemos cambiarlo fácilmente por otra implementación. También podemos fácilmente "simular", o crear una implementación ficticia del `UserRepository` al probar nuestra aplicación.

Un conocimiento profundo del contenedor de servicios de Laravel es esencial para construir una aplicación potente y de gran tamaño, así como para contribuir al propio núcleo de Laravel.

[]()

### Resolución de configuración cero

Si una clase no tiene dependencias o sólo depende de otras clases concretas (no de interfaces), no es necesario indicar al contenedor cómo resolver esa clase. Por ejemplo, puedes colocar el siguiente código en tu archivo `routes/web.php`:

    <?php

    class Service
    {
        //
    }

    Route::get('/', function (Service $service) {
        die(get_class($service));
    });

En este ejemplo, al pulsar la ruta `/` de tu aplicación se resolverá automáticamente la clase `Service` y se inyectará en el manejador de tu ruta. Esto cambia las reglas del juego. Significa que usted puede desarrollar su aplicación y tomar ventaja de la inyección de dependencia sin preocuparse por los archivos de configuración hinchados.

Afortunadamente, muchas de las clases que escribirás cuando construyas una aplicación Laravel reciben automáticamente sus dependencias a través del contenedor, incluyendo [controladores](/docs/%7B%7Bversion%7D%7D/controllers), [escuchadores de eventos](/docs/%7B%7Bversion%7D%7D/events), [middleware](/docs/%7B%7Bversion%7D%7D/middleware), y más. Además, puedes escribir dependencias en el método `handle` de [los trabajos en cola](/docs/%7B%7Bversion%7D%7D/queues). Una vez que pruebes el poder de la inyección de dependencias automática y de configuración cero te parecerá imposible desarrollar sin ella.

[]()

### Cuándo usar el contenedor

Gracias a la resolución de configuración cero, a menudo podrás escribir dependencias en rutas, controladores, escuchadores de eventos, y en otros lugares sin tener que interactuar manualmente con el contenedor. Por ejemplo, es posible que el tipo de sugerencia del objeto `Illuminate\Http\Request` en su definición de ruta para que pueda acceder fácilmente a la solicitud actual. Aunque nunca tengamos que interactuar con el contenedor para escribir este código, está gestionando la inyección de estas dependencias entre bastidores:

    use Illuminate\Http\Request;

    Route::get('/', function (Request $request) {
        // ...
    });

En muchos casos, gracias a la inyección automática de dependencias y las [facades](/docs/%7B%7Bversion%7D%7D/facades), puedes construir aplicaciones Laravel sin **tener** que vincular o resolver nada manualmente desde el contenedor. **Entonces, ¿cuándo interactuarías manualmente con el contenedor?** Examinemos dos situaciones.

En primer lugar, si escribes una clase que implementa una interfaz y deseas escribir una sugerencia de esa interfaz en una ruta o constructor de clase, debes [decirle al contenedor cómo resolver esa interfaz](#binding-interfaces-to-implementations). En segundo lugar, si estás [escribiendo un paquete Laravel](/docs/%7B%7Bversion%7D%7D/packages) que planeas compartir con otros desarrolladores Laravel, puede que necesites enlazar los servicios de tu paquete en el contenedor.

[]()

## Vinculación

[]()

### Conceptos Básicos

[]()

#### Enlaces sencillos

Casi todas las vinculaciones del contenedor de servicios se registrarán dentro de los [proveedores de servicios](/docs/%7B%7Bversion%7D%7D/providers), por lo que la mayoría de estos ejemplos demostrarán el uso del contenedor en ese contexto.

Dentro de un proveedor de servicios, siempre tienes acceso al contenedor a través de la propiedad `$this->app`. Podemos registrar un enlace utilizando el método `bind`, pasando el nombre de la clase o interfaz que deseamos registrar junto con un closure que devuelva una instancia de la clase:

    use App\Services\Transistor;
    use App\Services\PodcastParser;

    $this->app->bind(Transistor::class, function ($app) {
        return new Transistor($app->make(PodcastParser::class));
    });

Nótese que recibimos el propio contenedor como argumento para el resolver. Podemos entonces utilizar el contenedor para resolver subdependencias del objeto que estamos construyendo.

Como se ha mencionado, normalmente se interactuará con el contenedor dentro de los proveedores de servicios; sin embargo, si se desea interactuar con el contenedor fuera de un proveedor de servicios, se puede hacer a través de la [facade](/docs/%7B%7Bversion%7D%7D/facades) `App`:

    use App\Services\Transistor;
    use Illuminate\Support\Facades\App;

    App::bind(Transistor::class, function ($app) {
        // ...
    });

> **Nota**  
> No es necesario vincular clases al contenedor si no dependen de ninguna interfaz. El contenedor no necesita ser instruido sobre cómo construir estos objetos, ya que puede resolverlos automáticamente usando reflection.

[]()

#### Vinculación de un Singleton

El método `singleton` vincula una clase o interfaz al contenedor que sólo debe resolverse una vez. Una vez resuelta la vinculación de un singleton, se devolverá la misma instancia del objeto en las siguientes llamadas al contenedor:

    use App\Services\Transistor;
    use App\Services\PodcastParser;

    $this->app->singleton(Transistor::class, function ($app) {
        return new Transistor($app->make(PodcastParser::class));
    });

[]()

#### Vinculación de Singletons

El método `scoped` vincula una clase o interfaz en el contenedor que sólo debe ser resuelto una vez dentro de una determinada solicitud Laravel / ciclo de vida del trabajo. Si bien este método es similar al método `singleton`, las instancias registradas utilizando el método `scoped` se vaciarán cada vez que la aplicación Laravel inicie un nuevo "ciclo de vida", como cuando un [Laravel Octane](/docs/%7B%7Bversion%7D%7D/octane) worker procesa una nueva solicitud o cuando un Laravel [queue worker](/docs/%7B%7Bversion%7D%7D/queues) procesa un nuevo trabajo:

    use App\Services\Transistor;
    use App\Services\PodcastParser;

    $this->app->scoped(Transistor::class, function ($app) {
        return new Transistor($app->make(PodcastParser::class));
    });

[]()

#### Vinculación de instancias

También puede enlazar una instancia de objeto existente al contenedor utilizando el método `instance`. La instancia dada siempre será devuelta en las siguientes llamadas al contenedor:

    use App\Services\Transistor;
    use App\Services\PodcastParser;

    $service = new Transistor(new PodcastParser);

    $this->app->instance(Transistor::class, $service);

[]()

### Vinculación de interfaces a implementaciones

Una característica muy potente del contenedor de servicios es su capacidad para vincular una interfaz a una implementación determinada. Por ejemplo, supongamos que tenemos una interfaz `EventPusher` y una implementación `RedisEventPusher`. Una vez que hemos codificado nuestra implementación `RedisEventPusher` de esta interfaz, podemos registrarla con el contenedor de servicios de esta manera:

    use App\Contracts\EventPusher;
    use App\Services\RedisEventPusher;

    $this->app->bind(EventPusher::class, RedisEventPusher::class);

Esta sentencia indica al contenedor que debe inyectar el `RedisEventPusher` cuando una clase necesite una implementación de `EventPusher`. Ahora podemos introducir la interfaz `EventPusher` en el constructor de una clase que sea resuelta por el contenedor. Recuerda, controladores, escuchadores de eventos, middleware, y varios otros tipos de clases dentro de las aplicaciones Laravel siempre se resuelven utilizando el contenedor:

    use App\Contracts\EventPusher;

    /**
     * Create a new class instance.
     *
     * @param  \App\Contracts\EventPusher  $pusher
     * @return void
     */
    public function __construct(EventPusher $pusher)
    {
        $this->pusher = $pusher;
    }

[]()

### Vinculación contextual

A veces puedes tener dos clases que utilizan la misma interfaz, pero deseas inyectar diferentes implementaciones en cada clase. Por ejemplo, dos controladores pueden depender de diferentes implementaciones del [contrato](/docs/%7B%7Bversion%7D%7D/contracts) `Illuminate\Contracts\Filesystem\Filesystem`. Laravel proporciona una interfaz sencilla y fluida para definir este comportamiento:

    use App\Http\Controllers\PhotoController;
    use App\Http\Controllers\UploadController;
    use App\Http\Controllers\VideoController;
    use Illuminate\Contracts\Filesystem\Filesystem;
    use Illuminate\Support\Facades\Storage;

    $this->app->when(PhotoController::class)
              ->needs(Filesystem::class)
              ->give(function () {
                  return Storage::disk('local');
              });

    $this->app->when([VideoController::class, UploadController::class])
              ->needs(Filesystem::class)
              ->give(function () {
                  return Storage::disk('s3');
              });

[]()

### Primitivas de enlace

A veces puedes tener una clase que recibe algunas clases inyectadas, pero también necesita un valor primitivo inyectado como un entero. Puedes utilizar fácilmente el enlace contextual para inyectar cualquier valor que tu clase pueda necesitar:

    use App\Http\Controllers\UserController;

    $this->app->when(UserController::class)
              ->needs('$variableName')
              ->give($value);

A veces una clase puede depender de una array de instancias [etiquetadas](#tagging). Usando el método `giveTagged`, puedes inyectar fácilmente todos los enlaces del contenedor con esa etiqueta:

    $this->app->when(ReportAggregator::class)
        ->needs('$reports')
        ->giveTagged('reports');

Si necesitas inyectar un valor de uno de los ficheros de configuración de tu aplicación, puedes utilizar el método `giveConfig`:

    $this->app->when(ReportAggregator::class)
        ->needs('$timezone')
        ->giveConfig('app.timezone');

[]()

### Variables tipadas de enlace

Ocasionalmente, puedes tener una clase que recibe un array de objetos tipados usando un argumento variadic del constructor:

    <?php

    use App\Models\Filter;
    use App\Services\Logger;

    class Firewall
    {
        /**
         * The logger instance.
         *
         * @var \App\Services\Logger
         */
        protected $logger;

        /**
         * The filter instances.
         *
         * @var array
         */
        protected $filters;

        /**
         * Create a new class instance.
         *
         * @param  \App\Services\Logger  $logger
         * @param  array  $filters
         * @return void
         */
        public function __construct(Logger $logger, Filter ...$filters)
        {
            $this->logger = $logger;
            $this->filters = $filters;
        }
    }

Utilizando la vinculación contextual, puede resolver esta dependencia proporcionando al método `give` un closure que devuelva un array de instancias de `Filter` resueltas:

    $this->app->when(Firewall::class)
              ->needs(Filter::class)
              ->give(function ($app) {
                    return [
                        $app->make(NullFilter::class),
                        $app->make(ProfanityFilter::class),
                        $app->make(TooLongFilter::class),
                    ];
              });

Para mayor comodidad, también puede proporcionar simplemente array matriz de nombres de clase que el contenedor resolverá siempre que `Firewall` necesite instancias de `Filter`:

    $this->app->when(Firewall::class)
              ->needs(Filter::class)
              ->give([
                  NullFilter::class,
                  ProfanityFilter::class,
                  TooLongFilter::class,
              ]);

[]()

#### Dependencias de etiquetas variables

A veces una clase puede tener una dependencia variadic que se indica como una clase dada`(Informe ...$informes`). Utilizando los métodos `needs` y `giveTagged`, puede inyectar fácilmente todos los enlaces de contenedor con esa [etiqueta](#tagging) para la dependencia dada:

    $this->app->when(ReportAggregator::class)
        ->needs(Report::class)
        ->giveTagged('reports');

[]()

### Etiquetado

Ocasionalmente, puede que necesite resolver todas las vinculaciones de una determinada "categoría". Por ejemplo, puede que estés construyendo un analizador de informes que recibe una array de diferentes implementaciones de la interfaz `Report`. Después de registrar las implementaciones de `Report`, puedes asignarles una etiqueta utilizando el método `tag`:

    $this->app->bind(CpuReport::class, function () {
        //
    });

    $this->app->bind(MemoryReport::class, function () {
        //
    });

    $this->app->tag([CpuReport::class, MemoryReport::class], 'reports');

Una vez etiquetados los servicios, puede resolverlos fácilmente a través del método `tagged` del contenedor:

    $this->app->bind(ReportAnalyzer::class, function ($app) {
        return new ReportAnalyzer($app->tagged('reports'));
    });

[]()

### Ampliación de enlaces

El método `extender` permite modificar los servicios resueltos. Por ejemplo, cuando se resuelve un servicio, puede ejecutar código adicional para decorar o configurar el servicio. El método `extend` acepta dos argumentos, la clase de servicio que estás extendiendo y un closure que debe devolver el servicio modificado. El closure recibe el servicio que se está resolviendo y la instancia del contenedor:

    $this->app->extend(Service::class, function ($service, $app) {
        return new DecoratedService($service);
    });

[]()

## Resolución de

[]()

### El método `make`

Puedes utilizar el método `make` para resolver una instancia de clase del contenedor. El método `make` acepta el nombre de la clase o interfaz que deseas resolver:

    use App\Services\Transistor;

    $transistor = $this->app->make(Transistor::class);

Si algunas de las dependencias de tu clase no se pueden resolver a través del contenedor, puedes inyectarlas pasándolas como un array asociativo al método `makeWith`. Por ejemplo, podemos pasar manualmente el argumento `$id` del constructor requerido por el servicio `Transistor`:

    use App\Services\Transistor;

    $transistor = $this->app->makeWith(Transistor::class, ['id' => 1]);

Si estás fuera de un proveedor de servicios en una ubicación de tu código que no tiene acceso a la variable `$app`, puedes utilizar la [facade](/docs/%7B%7Bversion%7D%7D/facades) `App` o el `app` [helper](/docs/%7B%7Bversion%7D%7D/helpers#method-app) para resolver una instancia de clase desde el contenedor:

    use App\Services\Transistor;
    use Illuminate\Support\Facades\App;

    $transistor = App::make(Transistor::class);

    $transistor = app(Transistor::class);

Si quieres que la instancia del contenedor Laravel se inyecte en una clase que está siendo resuelta por el contenedor, puedes escribir una sugerencia a la clase `Illuminate\Container\Container` en el constructor de tu clase:

    use Illuminate\Container\Container;

    /**
     * Create a new class instance.
     *
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

[]()

### Inyección automática

Alternativamente, y de manera importante, puedes escribir la dependencia en el constructor de una clase que es resuelta por el contenedor, incluyendo [controladores](/docs/%7B%7Bversion%7D%7D/controllers), [escuchadores de eventos](/docs/%7B%7Bversion%7D%7D/events), [middleware](/docs/%7B%7Bversion%7D%7D/middleware), y más. Además, puedes escribir las dependencias en el método `handle` de [los trabajos en cola](/docs/%7B%7Bversion%7D%7D/queues). En la práctica, así es como la mayoría de tus objetos deberían ser resueltos por el contenedor.

Por ejemplo, puede indicar un repositorio definido por su aplicación en el constructor de un controlador. El repositorio se resolverá automáticamente y se inyectará en la clase:

    <?php

    namespace App\Http\Controllers;

    use App\Repositories\UserRepository;

    class UserController extends Controller
    {
        /**
         * The user repository instance.
         *
         * @var \App\Repositories\UserRepository
         */
        protected $users;

        /**
         * Create a new controller instance.
         *
         * @param  \App\Repositories\UserRepository  $users
         * @return void
         */
        public function __construct(UserRepository $users)
        {
            $this->users = $users;
        }

        /**
         * Show the user with the given ID.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
        public function show($id)
        {
            //
        }
    }

[]()

## Invocación e inyección de métodos

A veces puede que desees invocar un método en una instancia de objeto mientras permites que el contenedor inyecte automáticamente las dependencias de ese método. Por ejemplo, dada la siguiente clase:

    <?php

    namespace App;

    use App\Repositories\UserRepository;

    class UserReport
    {
        /**
         * Generate a new user report.
         *
         * @param  \App\Repositories\UserRepository  $repository
         * @return array
         */
        public function generate(UserRepository $repository)
        {
            // ...
        }
    }

Puede invocar el método `generate` a través del contenedor de la siguiente manera:

    use App\UserReport;
    use Illuminate\Support\Facades\App;

    $report = App::call([new UserReport, 'generate']);

El método `call` acepta cualquier callable de PHP. El método `call` del contenedor puede incluso ser usado para invocar un closure mientras inyecta automáticamente sus dependencias:

    use App\Repositories\UserRepository;
    use Illuminate\Support\Facades\App;

    $result = App::call(function (UserRepository $repository) {
        // ...
    });

[]()

## Eventos de contenedor

El contenedor de servicios lanza un evento cada vez que resuelve un objeto. Puedes escuchar este evento usando el método de `resolución`:

    use App\Services\Transistor;

    $this->app->resolving(Transistor::class, function ($transistor, $app) {
        // Called when container resolves objects of type "Transistor"...
    });

    $this->app->resolving(function ($object, $app) {
        // Called when container resolves object of any type...
    });

Como puedes ver, el objeto que está siendo resuelto será pasado al callback, permitiéndote establecer cualquier propiedad adicional en el objeto antes de que sea entregado a su consumidor.

[]()

## PSR-11

El contenedor de servicios de Laravel implementa la interfaz [PSR-11](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-11-container.md). Por lo tanto, puede escribir la interfaz del contenedor PSR-11 para obtener una instancia del contenedor de Laravel:

    use App\Services\Transistor;
    use Psr\Container\ContainerInterface;

    Route::get('/', function (ContainerInterface $container) {
        $service = $container->get(Transistor::class);

        //
    });

Se lanzará una excepción si el identificador dado no puede ser resuelto. La excepción será una instancia de `Psr\Container\NotFoundExceptionInterface` si el identificador nunca fue vinculado. Si el identificador se vinculó pero no se pudo resolver, se lanzará una instancia de `Psr\Container\ContainerExceptionInterface`.
