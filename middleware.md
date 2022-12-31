# middleware

- [Introducción](#introduction)
- [Definición de middleware](#defining-middleware)
- [Registro de middleware](#registering-middleware)
  - [middleware global](#global-middleware)
  - [middleware-to-routes">Asignación de middleware a rutas](<#assigning-\<glossary variable=>)
  - [middleware-groups">Grupos demiddleware](<#\<glossary variable=>)
  - [Ordenación de middleware](#sorting-middleware)
- [middleware-parameters">Parámetros demiddleware](<#\<glossary variable=>)
- [middleware terminable](#terminable-middleware)

[]()

## Introducción

middleware middleware proporcionan un mecanismo conveniente para inspeccionar y filtrar las peticiones HTTP que entran en tu aplicación. Por ejemplo, Laravel incluye un middleware que verifica que el usuario de tu aplicación está autenticado. Si el usuario no está autenticado, el middleware redirigirá al usuario a la pantalla de login de tu aplicación. Sin embargo, si el usuario está autenticado, el middleware permitirá que la solicitud continúe en la aplicación.

Se puede escribir middleware adicional para realizar una variedad de tareas además de la autenticación. Por ejemplo, un middleware de registro podría registrar todas las peticiones entrantes a tu aplicación. Hay varios middleware incluidos en el framework Laravel, incluyendo middleware para autenticación y protección CSRF. Todos estos middleware se encuentran en el directorio `app/Http/middleware`.

[]()

## Definir middleware

Para crear un nuevo middleware, utiliza el comando `make:middleware` Artisan:

```shell
php artisan make:middleware EnsureTokenIsValid
```

Este comando colocará una nueva clase `EnsureTokenIsValid` dentro de tu directorio `app/Http/middleware`. En este middleware, sólo permitiremos el acceso a la ruta si el `token` suministrado coincide con un valor especificado. En caso contrario, redirigiremos a los usuarios de vuelta a la URI de `inicio`:

    <?php

    namespace App\Http\Middleware;

    use Closure;

    class EnsureTokenIsValid
    {
        /**
         * Handle an incoming request.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \Closure  $next
         * @return mixed
         */
        public function handle($request, Closure $next)
        {
            if ($request->input('token') !== 'my-secret-token') {
                return redirect('home');
            }

            return $next($request);
        }
    }

Como puedes ver, si el `token` dado no coincide con nuestro token secreto, el middleware devolverá una redirección HTTP al cliente; en caso contrario, la petición se pasará más adentro de la aplicación. Para pasar la petición más adentro de la aplicación (permitiendo al middleware "pasar"), deberías llamar al callback `$next` con el `$request`.

Es mejor imaginar middleware como una serie de "capas" por las que deben pasar las peticiones HTTP antes de llegar a la aplicación. Cada capa puede examinar la petición e incluso rechazarla por completo.

> **Nota**  
> Todos los middleware se resuelven a través del [contenedor de servicios](/docs/%7B%7Bversion%7D%7D/container), por lo que puedes escribir cualquier dependencia que necesites dentro del constructor de un middleware.

[]()[]()

#### middleware y respuestas

Por supuesto, un middleware puede realizar tareas antes o después de pasar la petición al interior de la aplicación. Por ejemplo, el siguiente middleware realizaría alguna tarea **antes de** que la solicitud sea gestionada por la aplicación:

    <?php

    namespace App\Http\Middleware;

    use Closure;

    class BeforeMiddleware
    {
        public function handle($request, Closure $next)
        {
            // Perform action

            return $next($request);
        }
    }

Sin embargo, este middleware realizaría su tarea después **de** que la solicitud sea manejada por la aplicación:

    <?php

    namespace App\Http\Middleware;

    use Closure;

    class AfterMiddleware
    {
        public function handle($request, Closure $next)
        {
            $response = $next($request);

            // Perform action

            return $response;
        }
    }

[]()

## Registro de middleware

[]()

### middleware global

Si quieres que un middleware se ejecute durante cada petición HTTP a tu aplicación, lista la clase de middleware en la propiedad `$middleware` de tu clase `app/Http/Kernel.php`.

[middleware-to-routes">]()

### Asignación de middleware a rutas

Si quieres asignar middleware a rutas específicas, primero debes asignar al middleware una clave en el archivo `app/Http/Kernel.` php de tu aplicación. Por defecto, la propiedad `$routeMiddleware` de esta clase contiene entradas para el middleware incluido con Laravel. Puedes añadir tu propio middleware a esta lista y asignarle una clave de tu elección:

    // Within App\Http\Kernel class...

    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];

Una vez que el middleware se ha definido en el núcleo HTTP, puede utilizar el método `middleware` para asignar middleware a una ruta:

    Route::get('/profile', function () {
        //
    })->middleware('auth');

Puedes asignar múltiples middleware a la ruta pasando un array de nombres de middleware al método `middleware`:

    Route::get('/', function () {
        //
    })->middleware(['first', 'second']);

Al asignar middleware, también puedes pasar el nombre completo de la clase:

    use App\Http\Middleware\EnsureTokenIsValid;

    Route::get('/profile', function () {
        //
    })->middleware(EnsureTokenIsValid::class);

[]()

#### Excluir middleware

Cuando se asignan middleware a un grupo de rutas, puede ser necesario evitar que el middleware se aplique a una ruta individual dentro del grupo. Puedes conseguirlo utilizando el método `withoutMiddleware`:

    use App\Http\Middleware\EnsureTokenIsValid;

    Route::middleware([EnsureTokenIsValid::class])->group(function () {
        Route::get('/', function () {
            //
        });

        Route::get('/profile', function () {
            //
        })->withoutMiddleware([EnsureTokenIsValid::class]);
    });

También puedes excluir un determinado conjunto de middleware de todo un [grupo](/docs/%7B%7Bversion%7D%7D/routing#route-groups) de definiciones de ruta:

    use App\Http\Middleware\EnsureTokenIsValid;

    Route::withoutMiddleware([EnsureTokenIsValid::class])->group(function () {
        Route::get('/profile', function () {
            //
        });
    });

El método `withoutMiddleware` sólo puede eliminar middleware de ruta y no se aplica a [middleware global](#global-middleware).

[]()

### Grupos demiddleware

A veces es posible que desee agrupar varios middleware bajo una sola clave para que sean más fáciles de asignar a las rutas. Puedes conseguirlo usando la propiedad `$middlewareGroups` de tu kernel HTTP.

Laravel incluye grupos predefinidos de middleware `web` y `api` que contienen middleware común que puedes querer aplicar a tus rutas web y API. Recuerde, estos grupos de middleware se aplican automáticamente por su aplicación `App\Providers\RouteServiceProvider` proveedor de servicios a las rutas dentro de su correspondiente `web` y `api` archivos de ruta:

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

Los grupos demiddleware pueden ser asignados a rutas y acciones de controlador usando la misma sintaxis que middleware individuales. De nuevo, los grupos de middleware hacen más conveniente asignar muchos middleware a una ruta a la vez:

    Route::get('/', function () {
        //
    })->middleware('web');

    Route::middleware(['web'])->group(function () {
        //
    });

> **Nota**  
> Fuera de la caja, los grupos middleware middleware `web` y `api` se aplican automáticamente a los correspondientes archivos `routes/web.` php y `routes/api.` php de su aplicación por el `App\Providers\RouteServiceProvider`.

[]()

### Ordenación de middleware

En raras ocasiones, es posible que necesite que su middleware se ejecute en un orden específico, pero no tiene control sobre su orden cuando se asignan a la ruta. En este caso, puedes especificar tu prioridad de middleware usando la propiedad `$middlewarePriority` de tu archivo `app/Http/Kernel.` php. Esta propiedad puede no existir en su kernel HTTP por defecto. Si no existe, puede copiar su definición por defecto a continuación:

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var string[]
     */
    protected $middlewarePriority = [
        \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class,
        \Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class,
        \Illuminate\Contracts\Session\Middleware\AuthenticatesSessions::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];

[]()

## Parámetros demiddleware

middleware también puede recibir parámetros adicionales. Por ejemplo, si tu aplicación necesita verificar que el usuario autenticado tiene un "rol" dado antes de realizar una acción dada, podrías crear un middleware `EnsureUserHasRole` que reciba un nombre de rol como argumento adicional.

Los parámetros adicionales middleware middleware se pasarán al middleware después del argumento `$next`:

    <?php

    namespace App\Http\Middleware;

    use Closure;

    class EnsureUserHasRole
    {
        /**
         * Handle the incoming request.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \Closure  $next
         * @param  string  $role
         * @return mixed
         */
        public function handle($request, Closure $next, $role)
        {
            if (! $request->user()->hasRole($role)) {
                // Redirect...
            }

            return $next($request);
        }

    }

Los parámetros delmiddleware pueden especificarse al definir la ruta separando el nombre del middleware y los parámetros con un `:`. Los parámetros múltiples deben estar delimitados por comas:

    Route::put('/post/{id}', function ($id) {
        //
    })->middleware('role:editor');

[]()

## middleware terminable

A veces un middleware puede necesitar hacer algún trabajo después de que la respuesta HTTP haya sido enviada al navegador. Si defines un método `terminate` en tu middleware y tu servidor web está usando FastCGI, el método `terminate` será llamado automáticamente después de que la respuesta sea enviada al navegador:

    <?php

    namespace Illuminate\Session\Middleware;

    use Closure;

    class TerminatingMiddleware
    {
        /**
         * Handle an incoming request.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \Closure  $next
         * @return mixed
         */
        public function handle($request, Closure $next)
        {
            return $next($request);
        }

        /**
         * Handle tasks after the response has been sent to the browser.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \Illuminate\Http\Response  $response
         * @return void
         */
        public function terminate($request, $response)
        {
            // ...
        }
    }

El método `terminate` debe recibir tanto la petición como la respuesta. Una vez que hayas definido un middleware terminable, debes añadirlo a la lista de rutas o middleware global en el archivo `app/Http/Kernel.` php.

Al llamar al método `terminate` en tu middleware, Laravel resolverá una instancia fresca del middleware desde el [contenedor de servicios](/docs/%7B%7Bversion%7D%7D/container). Si deseas utilizar la misma instancia de middleware cuando los métodos `handle` y `terminate` son llamados, registra el middleware con el contenedor utilizando el método `singleton` del contenedor. Típicamente esto debería hacerse en el método `register` de tu `AppServiceProvider`:

    use App\Http\Middleware\TerminatingMiddleware;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TerminatingMiddleware::class);
    }
