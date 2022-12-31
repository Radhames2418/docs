# Enrutamiento

- [Enrutamiento básico](#basic-routing)
  - [Redirigir rutas](#redirect-routes)
  - [Ver rutas](#view-routes)
  - [Lista de rutas](#the-route-list)
- [Parámetros de ruta](#route-parameters)
  - [Parámetros obligatorios](#required-parameters)
  - [Parámetros opcionales](#parameters-optional-parameters)
  - [Restricciones de expresiones regulares](#parameters-regular-expression-constraints)
- [Rutas con nombre](#named-routes)
- [Grupos de rutas](#route-groups)
  - [middleware](#route-group-middleware)
  - [Controladores](#route-group-controllers)
  - [Enrutamiento de subdominios](#route-group-subdomain-routing)
  - [Prefijos de ruta](#route-group-prefixes)
  - [Prefijos de nombre de ruta](#route-group-name-prefixes)
- [Enlace del modelo de ruta](#route-model-binding)
  - [Enlace implícito](#implicit-binding)
  - [Enlace Enum implícito](#implicit-enum-binding)
  - [Enlace explícito](#explicit-binding)
- [Rutas Fallback](#fallback-routes)
- [Limitación de velocidad](#rate-limiting)
  - [Definición de limitadores de velocidad](#defining-rate-limiters)
  - [Asignación de limitadores de velocidad a rutas](#attaching-rate-limiters-to-routes)
- [Suplantación del método de formulario](#form-method-spoofing)
- [Acceso a la ruta actual](#accessing-the-current-route)
- [Compartición de recursos entre orígenes (CORS)](#cors)
- [Almacenamiento en caché de rutas](#route-caching)

[]()

## Enrutamiento básico

Las rutas más básicas de Laravel aceptan un URI y un closure, proporcionando un método muy simple y expresivo de definir rutas y comportamientos sin complicados ficheros de configuración de rutas:

    use Illuminate\Support\Facades\Route;

    Route::get('/greeting', function () {
        return 'Hello World';
    });

[]()

#### Los ficheros de rutas por defecto

Todas las rutas Laravel se definen en sus archivos de ruta, que se encuentran en el directorio `routes`. Estos archivos son cargados automáticamente por el `App\Providers\RouteServiceProvider` de su aplicación. El archivo `routes/web.` php define las rutas que son para su interfaz web. A estas rutas se les asigna el grupo `web` middleware, que proporciona características como el estado de sesión y la protección CSRF. Las rutas en `routes/api.` php no tienen estado y se les asigna el grupo `api` middleware.

Para la mayoría de las aplicaciones, comenzarás definiendo rutas en tu fichero `routes/web.` php. Puede acceder a las rutas definidas en `routes/web`.php introduciendo la URL de la ruta definida en su navegador. Por ejemplo, puede acceder a la siguiente ruta navegando a `http://example.com/user` en su navegador:

    use App\Http\Controllers\UserController;

    Route::get('/user', [UserController::class, 'index']);

Las rutas definidas en el archivo `routes/api.` php están anidadas dentro de un grupo de rutas por el `RouteServiceProvider`. Dentro de este grupo, el prefijo `/api` URI se aplica automáticamente, por lo que no es necesario aplicarlo manualmente a cada ruta del archivo. Puede modificar el prefijo y otras opciones del grupo de rutas modificando su clase `RouteServiceProvider`.

[]()

#### Métodos disponibles del enrutador

El enrutador le permite registrar rutas que respondan a cualquier verbo HTTP:

    Route::get($uri, $callback);
    Route::post($uri, $callback);
    Route::put($uri, $callback);
    Route::patch($uri, $callback);
    Route::delete($uri, $callback);
    Route::options($uri, $callback);

A veces puede necesitar registrar una ruta que responda a múltiples verbos HTTP. Puede hacerlo utilizando el método `match`. O incluso puede registrar una ruta que responda a todos los verbos HTTP utilizando el método `any`:

    Route::match(['get', 'post'], '/', function () {
        //
    });

    Route::any('/', function () {
        //
    });

> **Nota**  
> Cuando se definen múltiples rutas que comparten la misma URI, las rutas que utilizan los métodos `get`, `post`, `put`, `patch`, `delete` y `options` deben definirse antes que las rutas que utilizan los métodos `any`, `match` y `redirect`. Esto asegura que la petición entrante se corresponde con la ruta correcta.

[]()

#### Inyección de dependencia

Puede escribir cualquier dependencia requerida por su ruta en la firma de callback de su ruta. Las dependencias declaradas serán automáticamente resueltas e inyectadas en el callback por el [contenedor de servicios de](/docs/%7B%7Bversion%7D%7D/container) Laravel. Por ejemplo, puede escribir la clase `Illuminate\Http\Request` para que la solicitud HTTP actual se inyecte automáticamente en la llamada de retorno de la ruta:

    use Illuminate\Http\Request;

    Route::get('/users', function (Request $request) {
        // ...
    });

[]()

#### Protección CSRF

Recuerde que cualquier formulario HTML que apunte a rutas `POST`, `PUT`, `PATCH` o `DELETE` definidas en el archivo de rutas `web` debe incluir un campo de token CSRF. De lo contrario, la solicitud será rechazada. Puede leer más sobre la protección CSRF en la [documentación CSRF](/docs/%7B%7Bversion%7D%7D/csrf):

    <form method="POST" action="/profile">
        @csrf
        ...
    </form>

[]()

### Redirigir rutas

Si está definiendo una ruta que redirige a otro URI, puede utilizar el método `Route::redirect`. Este método proporciona un atajo conveniente para que no tenga que definir una ruta completa o un controlador para realizar una simple redirección:

    Route::redirect('/here', '/there');

Por defecto, `Route`::redirect devuelve un código de estado `302`. Puede personalizar el código de estado utilizando el tercer parámetro opcional:

    Route::redirect('/here', '/there', 301);

O puede utilizar el método Route:: `permanentRedirect` para devolver un código de estado `301`:

    Route::permanentRedirect('/here', '/there');

> **Advertencia**  
> Cuando se usan parámetros de ruta en rutas de redirección, los siguientes parámetros están reservados por Laravel y no pueden ser usados: `destination` y `status`.

[]()

### Ver Rutas

Si su ruta sólo necesita devolver una [vista](/docs/%7B%7Bversion%7D%7D/views), puede utilizar el método `Route::view`. Al igual que el método `redirect`, este método proporciona un atajo simple para que no tenga que definir una ruta o controlador completo. El método `view` acepta un URI como primer argumento y un nombre de vista como segundo argumento. Además, puede proporcionar una array de datos para pasar a la vista como tercer argumento opcional:

    Route::view('/welcome', 'welcome');

    Route::view('/welcome', 'welcome', ['name' => 'Taylor']);

> **Advertencia**  
> Cuando se utilizan parámetros de ruta en rutas de vista, los siguientes parámetros están reservados por Laravel y no pueden ser utilizados: `vista`, `datos`, `estado` y `cabeceras`.

[]()

### Lista de rutas

El comando `route:list` de Artisan puede proporcionar fácilmente una visión general de todas las rutas definidas por tu aplicación:

```shell
php artisan route:list
```

Por defecto, el middleware ruta que se asigna a cada ruta no se mostrará en la salida de route: `list`; sin embargo, puedes instruir a Laravel para que muestre el middleware ruta añadiendo la opción `-v` al comando:

```shell
php artisan route:list -v
```

También puedes ordenar a Laravel que sólo muestre las rutas que comienzan con un URI determinado:

```shell
php artisan route:list --path=api
```

Además, puedes indicar a Laravel que oculte las rutas definidas por paquetes de terceros proporcionando la opción `--except-vendor` al ejecutar el comando `route:list`:

```shell
php artisan route:list --except-vendor
```

Del mismo modo, también puedes indicar a Laravel que sólo muestre las rutas definidas por paquetes de terceros proporcionando la opción `--only-vendor` al ejecutar el comando `route`:list:

```shell
php artisan route:list --only-vendor
```

[]()

## Parámetros de ruta

[]()

### Parámetros obligatorios

A veces necesitarás capturar segmentos del URI dentro de tu ruta. Por ejemplo, puede que necesite capturar el ID de un usuario de la URL. Puede hacerlo definiendo parámetros de ruta:

    Route::get('/user/{id}', function ($id) {
        return 'User '.$id;
    });

Puede definir tantos parámetros de ruta como requiera su ruta:

    Route::get('/posts/{post}/comments/{comment}', function ($postId, $commentId) {
        //
    });

Los parámetros de ruta van siempre entre llaves `{}` y deben estar formados por caracteres alfabéticos. Los guiones bajos`(_`) también son aceptables en los nombres de parámetros de ruta. Los parámetros de ruta se inyectan en las retrollamadas / controladores de ruta en función de su orden - los nombres de los argumentos de retrollamada / controlador de ruta no importan.

[]()

#### Parámetros e inyección de dependencias

Si tu ruta tiene dependencias que quieres que el contenedor de servicios de Laravel inyecte automáticamente en el callback de tu ruta, debes listar los parámetros de tu ruta después de tus dependencias:

    use Illuminate\Http\Request;

    Route::get('/user/{id}', function (Request $request, $id) {
        return 'User '.$id;
    });

[]()

### Parámetros opcionales

En ocasiones puede ser necesario especificar un parámetro de ruta que no siempre está presente en el URI. Puede hacerlo colocando una marca `?` después del nombre del parámetro. Asegúrese de dar a la variable correspondiente de la ruta un valor por defecto:

    Route::get('/user/{name?}', function ($name = null) {
        return $name;
    });

    Route::get('/user/{name?}', function ($name = 'John') {
        return $name;
    });

[]()

### Restricciones de expresiones regulares

Puede restringir el formato de sus parámetros de ruta utilizando el método `where` en una instancia de ruta. El método `where` acepta el nombre del parámetro y una expresión regular que define cómo debe limitarse el parámetro:

    Route::get('/user/{name}', function ($name) {
        //
    })->where('name', '[A-Za-z]+');

    Route::get('/user/{id}', function ($id) {
        //
    })->where('id', '[0-9]+');

    Route::get('/user/{id}/{name}', function ($id, $name) {
        //
    })->where(['id' => '[0-9]+', 'name' => '[a-z]+']);

Para mayor comodidad, algunos patrones de expresiones regulares de uso común tienen métodos de ayuda que le permiten añadir rápidamente restricciones de patrones a sus rutas:

    Route::get('/user/{id}/{name}', function ($id, $name) {
        //
    })->whereNumber('id')->whereAlpha('name');

    Route::get('/user/{name}', function ($name) {
        //
    })->whereAlphaNumeric('name');

    Route::get('/user/{id}', function ($id) {
        //
    })->whereUuid('id');

    Route::get('/user/{id}', function ($id) {
        //
    })->whereUlid('id');

    Route::get('/category/{category}', function ($category) {
        //
    })->whereIn('category', ['movie', 'song', 'painting']);

Si la petición entrante no coincide con las restricciones del patrón de ruta, se devolverá una respuesta HTTP 404.

[]()

#### Restricciones globales

Si desea que un parámetro de ruta esté siempre limitado por una expresión regular determinada, puede utilizar el método `pattern`. Debe definir estos patrones en el método `boot` de su clase `AppProviders\RouteServiceProvider`:

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        Route::pattern('id', '[0-9]+');
    }

Una vez definido el patrón, se aplica automáticamente a todas las rutas que utilicen ese nombre de parámetro:

    Route::get('/user/{id}', function ($id) {
        // Only executed if {id} is numeric...
    });

[]()

#### Barras diagonales codificadas

El componente de enrutamiento de Laravel permite que todos los caracteres excepto `/` estén presentes en los valores de los parámetros de ruta. Debe permitir explícitamente que `/` forme parte de su marcador de posición utilizando una expresión regular de condición `where`:

    Route::get('/search/{search}', function ($search) {
        return $search;
    })->where('search', '.*');

> **Advertencia**  
> Las barras diagonales codificadas sólo se admiten en el último segmento de la ruta.

[]()

## Rutas con nombre

Las rutas con nombre permiten generar cómodamente URL o redirecciones para rutas específicas. Puede especificar un nombre para una ruta encadenando el método `name` en la definición de ruta:

    Route::get('/user/profile', function () {
        //
    })->name('profile');

También puede especificar nombres de ruta para acciones de controlador:

    Route::get(
        '/user/profile',
        [UserProfileController::class, 'show']
    )->name('profile');

> **Advertencia**  
> Los nombres de ruta deben ser siempre únicos.

[]()

#### Generación de URL para rutas con nombre

Una vez que hayas asignado un nombre a una ruta dada, puedes usar el nombre de la ruta cuando generes URLs o `redirecciones` a través de las funciones `de` ayuda de Laravel:

    // Generating URLs...
    $url = route('profile');

    // Generating Redirects...
    return redirect()->route('profile');

    return to_route('profile');

Si la ruta con nombre define parámetros, puede pasar los parámetros como segundo argumento a la función de `ruta`. Los parámetros dados se insertarán automáticamente en la URL generada en sus posiciones correctas:

    Route::get('/user/{id}/profile', function ($id) {
        //
    })->name('profile');

    $url = route('profile', ['id' => 1]);

Si pasa parámetros adicionales en el array, esos pares clave / valor se añadirán automáticamente a la cadena de consulta de la URL generada:

    Route::get('/user/{id}/profile', function ($id) {
        //
    })->name('profile');

    $url = route('profile', ['id' => 1, 'photos' => 'yes']);

    // /user/1/profile?photos=yes

> **Nota**  
> A veces, puede que desee especificar valores por defecto para parámetros URL, como la configuración regional actual. Para ello, puede utilizar el [método`URL::defaults`](/docs/%7B%7Bversion%7D%7D/urls#default-values).

[]()

#### Inspección de la ruta actual

Si desea determinar si la petición actual ha sido enviada a una ruta determinada, puede utilizar el método `named` en una instancia de Route. Por ejemplo, puede comprobar el nombre de la ruta actual desde un middleware ruta:

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->route()->named('profile')) {
            //
        }

        return $next($request);
    }

[]()

## Grupos de rutas

Los grupos de rutas permiten compartir atributos de ruta, como el middleware, entre un gran número de rutas sin necesidad de definir esos atributos en cada ruta individual.

Los grupos anidados intentan "fusionar" de forma inteligente los atributos con su grupo padre. middleware y las condiciones `where` se fusionan mientras que los nombres y prefijos se añaden. Los delimitadores de espacios de nombres y las barras oblicuas en los prefijos URI se añaden automáticamente cuando procede.

[]()

### middleware

Para asignar [middleware](/docs/%7B%7Bversion%7D%7D/middleware) a todas las rutas de un grupo, puede utilizar el método `middleware` antes de definir el grupo. los middleware se ejecutan en el orden en que aparecen en el array:

    Route::middleware(['first', 'second'])->group(function () {
        Route::get('/', function () {
            // Uses first & second middleware...
        });

        Route::get('/user/profile', function () {
            // Uses first & second middleware...
        });
    });

[]()

### Controladores

Si un grupo de rutas utiliza el mismo [controlador](/docs/%7B%7Bversion%7D%7D/controllers), puede utilizar el método `controlador` para definir el controlador común para todas las rutas del grupo. Entonces, cuando defina las rutas, sólo necesitará proporcionar el método controlador que invocan:

    use App\Http\Controllers\OrderController;

    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders/{id}', 'show');
        Route::post('/orders', 'store');
    });

[]()

### Enrutamiento de subdominios

Los grupos de rutas también pueden utilizarse para gestionar el enrutamiento de subdominios. A los subdominios se les pueden asignar parámetros de ruta igual que a los URI de ruta, lo que le permite capturar una parte del subdominio para utilizarla en su ruta o controlador. El subdominio puede especificarse llamando al método `domain` antes de definir el grupo:

    Route::domain('{account}.example.com')->group(function () {
        Route::get('user/{id}', function ($account, $id) {
            //
        });
    });

> **Advertencia**  
> Para asegurarse de que sus rutas de subdominio son accesibles, debe registrar las rutas de subdominio antes de registrar las rutas de dominio raíz. Esto evitará que las rutas de dominio raíz sobrescriban las rutas de subdominio que tengan la misma ruta URI.

[]()

### Prefijos de ruta

El método de `prefijo` puede utilizarse para prefijar cada ruta del grupo con un URI determinado. Por ejemplo, puede prefijar todas las rutas URI del grupo con `admin`:

    Route::prefix('admin')->group(function () {
        Route::get('/users', function () {
            // Matches The "/admin/users" URL
        });
    });

[]()

### Prefijos de nombres de ruta

El método `name` puede utilizarse para asignar a cada nombre de ruta del grupo una cadena determinada. Por ejemplo, puede prefijar todos los nombres de las rutas agrupadas con `admin`. La cadena dada se antepone al nombre de la ruta exactamente como se especifica, por lo que nos aseguraremos de proporcionar el carácter `.` al final del prefijo:

    Route::name('admin.')->group(function () {
        Route::get('/users', function () {
            // Route assigned name "admin.users"...
        })->name('users');
    });

[]()

## Enlace del modelo de ruta

Cuando se inyecta un ID de modelo a una ruta o acción de controlador, a menudo se consulta la base de datos para recuperar el modelo que corresponde a ese ID. Laravel route model binding proporciona una manera conveniente de inyectar automáticamente las instancias del modelo directamente en sus rutas. Por ejemplo, en lugar de inyectar el ID de un usuario, puedes inyectar la instancia completa del modelo `User` que coincida con el ID dado.

[]()

### Enlace implícito

Laravel resuelve automáticamente los modelos Eloquent definidos en rutas o acciones de controlador cuyos nombres de variables de tipo coinciden con un nombre de segmento de ruta. Por ejemplo:

    use App\Models\User;

    Route::get('/users/{user}', function (User $user) {
        return $user->email;
    });

Dado que la variable `$user` es un type-hinted como el modelo `App\Models\User` Eloquent y el nombre de la variable coincide con el segmento URI `{user}` Laravel inyectará automáticamente la instancia del modelo que tenga un ID que coincida con el valor correspondiente del URI de la petición. Si no se encuentra una instancia de modelo coincidente en la base de datos, se generará automáticamente una respuesta HTTP 404.

Por supuesto, la vinculación implícita también es posible cuando se utilizan métodos de controlador. De nuevo, observe que el segmento `{user}` URI coincide con la variable `$user` en el controlador que contiene una sugerencia de tipo `App\Models\User`:

    use App\Http\Controllers\UserController;
    use App\Models\User;

    // Route definition...
    Route::get('/users/{user}', [UserController::class, 'show']);

    // Controller method definition...
    public function show(User $user)
    {
        return view('user.profile', ['user' => $user]);
    }

[]()

#### Modelos borrados

Típicamente, la vinculación implícita de modelos no recuperará modelos que hayan sido [borrados suavemente](/docs/%7B%7Bversion%7D%7D/eloquent#soft-deleting). Sin embargo, puede ordenar a la vinculación implícita que recupere estos modelos encadenando el método `withTrashed` en la definición de su ruta:

    use App\Models\User;

    Route::get('/users/{user}', function (User $user) {
        return $user->email;
    })->withTrashed();

[]()[]()

#### Personalizando La Clave

A veces puede que desee resolver modelos Eloquent utilizando una columna distinta de `id`. Para ello, puede especificar la columna en la definición del parámetro de ruta:

    use App\Models\Post;

    Route::get('/posts/{post:slug}', function (Post $post) {
        return $post;
    });

Si desea que la vinculación del modelo utilice siempre una columna de la base de datos distinta de `id` al recuperar una clase de modelo determinada, puede anular el método `getRouteKeyName` en el modelo Eloquent:

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

[]()

#### Claves personalizadas y alcance

Cuando se vinculan implícitamente varios modelos Eloquent en una única definición de ruta, es posible que se desee delimitar el segundo modelo Eloquent de forma que sea hijo del modelo Eloquent anterior. Por ejemplo, considere esta definición de ruta que recupera una entrada de blog por slug para un usuario específico:

    use App\Models\Post;
    use App\Models\User;

    Route::get('/users/{user}/posts/{post:slug}', function (User $user, Post $post) {
        return $post;
    });

Cuando se utiliza un enlace implícito con clave personalizada como parámetro de ruta anidada, Laravel delimitará automáticamente la consulta para recuperar el modelo anidado por su padre utilizando convenciones para adivinar el nombre de la relación en el padre. En este caso, se asumirá que el modelo `User` tiene una relación llamada `posts` (la forma plural del nombre del parámetro de ruta) que puede utilizarse para recuperar el modelo `Post`.

Si lo desea, puede instruir a Laravel para que alcance los enlaces "hijo" incluso cuando no se proporcione una clave personalizada. Para ello, puede invocar el método `scopeBindings` al definir su ruta:

    use App\Models\Post;
    use App\Models\User;

    Route::get('/users/{user}/posts/{post}', function (User $user, Post $post) {
        return $post;
    })->scopeBindings();

También puede indicar a todo un grupo de definiciones de ruta que utilicen enlaces de ámbito:

    Route::scopeBindings()->group(function () {
        Route::get('/users/{user}/posts/{post}', function (User $user, Post $post) {
            return $post;
        });
    });

Del mismo modo, puede instruir explícitamente a Laravel para que no use bindings scoped invocando el método `withoutScopedBindings`:

    Route::get('/users/{user}/posts/{post:slug}', function (User $user, Post $post) {
        return $post;
    })->withoutScopedBindings();

[]()

#### Personalización del comportamiento del modelo no encontrado

Normalmente, se generará una respuesta HTTP 404 si no se encuentra un modelo vinculado implícitamente. Sin embargo, puede personalizar este comportamiento llamando al método `missing` cuando defina su ruta. El método `missing` acepta un closure que se invocará si no se encuentra un modelo vinculado implícitamente:

    use App\Http\Controllers\LocationsController;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Redirect;

    Route::get('/locations/{location:slug}', [LocationsController::class, 'show'])
            ->name('locations.view')
            ->missing(function (Request $request) {
                return Redirect::route('locations.index');
            });

[]()

### Enlace Enum implícito

PHP 8.1 introdujo soporte para [Enums](https://www.php.net/manual/en/language.enumerations.backed.php). Para complementar esta característica, Laravel le permite escribir un [Enum respaldado por una cadena](https://www.php.net/manual/en/language.enumerations.backed.php) en su definición de ruta y Laravel sólo invocará la ruta si ese segmento de ruta corresponde a un valor Enum válido. En caso contrario, se devolverá automáticamente una respuesta HTTP 404. Por ejemplo, dado el siguiente Enum:

```php
<?php

namespace App\Enums;

enum Category: string
{
    case Fruits = 'fruits';
    case People = 'people';
}
```

Puede definir una ruta que sólo será invocada si el segmento de ruta `{category}` es `fruits` o `people`. En caso contrario, Laravel devolverá una respuesta HTTP 404:

```php
use App\Enums\Category;
use Illuminate\Support\Facades\Route;

Route::get('/categories/{category}', function (Category $category) {
    return $category->value;
});
```

[]()

### Enlace explícito

No es necesario utilizar la resolución de modelos implícita y basada en convenciones de Laravel para utilizar la vinculación de modelos. También puede definir explícitamente cómo los parámetros de ruta se corresponden con los modelos. Para registrar una vinculación explícita, utilice el método `model` del enrutador para especificar la clase de un parámetro determinado. Debes definir tus enlaces explícitos a modelos al principio del método `boot` de tu clase `RouteServiceProvider`:

    use App\Models\User;
    use Illuminate\Support\Facades\Route;

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        Route::model('user', User::class);

        // ...
    }

A continuación, define una ruta que contenga un parámetro `{user}`:

    use App\Models\User;

    Route::get('/users/{user}', function (User $user) {
        //
    });

Dado que hemos vinculado todos los parámetros `{user}` al modelo `App\Models\User`, una instancia de esa clase será inyectada en la ruta. Así, por ejemplo, una petición a `users/1` inyectará la instancia `User` de la base de datos que tiene un ID de `1`.

Si no se encuentra una instancia de modelo coincidente en la base de datos, se generará automáticamente una respuesta HTTP 404.

[]()

#### Personalización de la lógica de resolución

Si desea definir su propia lógica de resolución de enlace de modelo, puede utilizar el método `Route::bind`. El closure que pase al método `bind` recibirá el valor del segmento URI y deberá devolver la instancia de la clase que debe ser inyectada en la ruta. De nuevo, esta personalización debería tener lugar en el método `boot` del `RouteServiceProvider` de tu aplicación:

    use App\Models\User;
    use Illuminate\Support\Facades\Route;

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        Route::bind('user', function ($value) {
            return User::where('name', $value)->firstOrFail();
        });

        // ...
    }

Alternativamente, puede anular el método `resolveRouteBinding` en su modelo Eloquent. Este método recibirá el valor del segmento URI y debería devolver la instancia de la clase que debería ser inyectada en la ruta:

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('name', $value)->firstOrFail();
    }

Si una ruta utiliza [un ámbito de enlace implícito](#implicit-model-binding-scoping), se utilizará el método `resolveChildRouteBinding` para resolver el enlace hijo del modelo padre:

    /**
     * Retrieve the child model for a bound value.
     *
     * @param  string  $childType
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveChildRouteBinding($childType, $value, $field)
    {
        return parent::resolveChildRouteBinding($childType, $value, $field);
    }

[]()

## Rutas Fallback

Utilizando el método `Route::fallback`, puede definir una ruta que se ejecutará cuando ninguna otra ruta coincida con la petición entrante. Normalmente, las peticiones no gestionadas mostrarán automáticamente una página "404" a través del gestor de excepciones de la aplicación. Sin embargo, dado que típicamente definirías la ruta `fallback` dentro de tu archivo `routes/web.php`, todo el middleware en el grupo de middleware `web` se aplicará a la ruta. Usted es libre de añadir middleware adicional a esta ruta según sea necesario:

    Route::fallback(function () {
        //
    });

> **Advertencia**  
> La ruta fallback debe ser siempre la última ruta registrada por su aplicación.

[]()

## Limitación de velocidad

[]()

### Definición de limitadores de velocidad

Laravel incluye potentes y personalizables servicios de limitación de velocidad que puede utilizar para restringir la cantidad de tráfico para una ruta o grupo de rutas. Para empezar, debes definir las configuraciones del limitador de tasa que satisfagan las necesidades de tu aplicación. Típicamente, esto debería hacerse dentro del método `configureRateLimiting` de la clase `App\Providers\RouteServiceProvider` de su aplicación.

Los limitadores de velocidad se definen utilizando el método `for` de la facade `RateLimiter`. El método `for` acepta un nombre de limitador de tasa y un closure que devuelve la configuración de límite que debería aplicarse a las rutas que están asignadas al limitador de tasa. La configuración de límites son instancias de la clase `Illuminate\cache\RateLimiting\Limit`. Esta clase contiene útiles métodos "constructores" para que pueda definir rápidamente su límite. El nombre del limitador de velocidad puede ser cualquier cadena que desee:

    use Illuminate\Cache\RateLimiting\Limit;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\RateLimiter;

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(1000);
        });
    }

Si la petición entrante excede el límite de velocidad especificado, Laravel devolverá automáticamente una respuesta con un código de estado HTTP 429. Si deseas definir tu propia respuesta que debe ser devuelta por un limitador de tasa, puedes utilizar el método `response`:

    RateLimiter::for('global', function (Request $request) {
        return Limit::perMinute(1000)->response(function (Request $request, array $headers) {
            return response('Custom response...', 429, $headers);
        });
    });

Dado que los callbacks del limitador de tasa reciben la instancia de la petición HTTP entrante, puedes construir el límite de tasa apropiado dinámicamente basándote en la petición entrante o en el usuario autenticado:

    RateLimiter::for('uploads', function (Request $request) {
        return $request->user()->vipCustomer()
                    ? Limit::none()
                    : Limit::perMinute(100);
    });

[]()

#### Segmentación de límites de velocidad

A veces puede que desee segmentar los límites de velocidad por algún valor arbitrario. Por ejemplo, puede que desee permitir a los usuarios acceder a una ruta dada 100 veces por minuto por dirección IP. Para lograr esto, puede usar el método `by` cuando construya su límite de tarifa:

    RateLimiter::for('uploads', function (Request $request) {
        return $request->user()->vipCustomer()
                    ? Limit::none()
                    : Limit::perMinute(100)->by($request->ip());
    });

Para ilustrar esta característica usando otro ejemplo, podemos limitar el acceso a la ruta a 100 veces por minuto por ID de usuario autenticado o 10 veces por minuto por dirección IP para invitados:

    RateLimiter::for('uploads', function (Request $request) {
        return $request->user()
                    ? Limit::perMinute(100)->by($request->user()->id)
                    : Limit::perMinute(10)->by($request->ip());
    });

[]()

#### Límites de tarifa múltiples

Si es necesario, puede devolver una array de límites de velocidad para una configuración de limitador de velocidad determinada. Cada límite de velocidad se evaluará para la ruta en función del orden en el que estén colocados dentro de la array:

    RateLimiter::for('login', function (Request $request) {
        return [
            Limit::perMinute(500),
            Limit::perMinute(3)->by($request->input('email')),
        ];
    });

[]()

### Asignación de limitadores de velocidad a rutas

Los limitadores de velocidad pueden adjuntarse a rutas o grupos de rutas utilizando [middleware](/docs/%7B%7Bversion%7D%7D/middleware) middleware de `aceleración`. El middleware de aceleración acepta el nombre del limitador de velocidad que desea asignar a la ruta:

    Route::middleware(['throttle:uploads'])->group(function () {
        Route::post('/audio', function () {
            //
        });

        Route::post('/video', function () {
            //
        });
    });

[]()

#### Throttling con Redis

Normalmente, middleware middleware de `aceleración` se asigna a la clase `Illuminate\Routing\middleware\ThrottleRequests`. Esta asignación se define en el núcleo HTTP de su aplicación`(App\Http\Kernel`). Sin embargo, si está utilizando Redis como controlador de cache de su aplicación, es posible que desee cambiar esta asignación para utilizar la clase `Illuminate\Routing\middleware\ThrottleRequestsWithRedis`. Esta clase es más eficiente en la gestión de la limitación de velocidad utilizando Redis:

    'throttle' => \Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class,

[]()

## Suplantación del método de formulario

Los formularios HTML no soportan acciones `PUT`, `PATCH` o `DELETE`. Por tanto, cuando defina rutas `PUT`, `PATCH` o `DELETE` que sean llamadas desde un formulario HTML, necesitará añadir un campo `_method` oculto al formulario. El valor enviado con el campo `_method` se utilizará como método de petición HTTP:

    <form action="/example" method="POST">
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    </form>

Para mayor comodidad, puede utilizar la [directiva](/docs/%7B%7Bversion%7D%7D/blade) `@method` [Blade](/docs/%7B%7Bversion%7D%7D/blade) para generar el campo de entrada `_method`:

    <form action="/example" method="POST">
        @method('PUT')
        @csrf
    </form>

[]()

## Acceso a la ruta actual

Puede utilizar los métodos `current`, `currentRouteName` y `currentRouteAction` en la facade `Route` para acceder a la información sobre la ruta que gestiona la petición entrante:

    use Illuminate\Support\Facades\Route;

    $route = Route::current(); // Illuminate\Routing\Route
    $name = Route::currentRouteName(); // string
    $action = Route::currentRouteAction(); // string

Puedes consultar la documentación de la API tanto de la [clase subyacente de](https://laravel.com/api/%7B%7Bversion%7D%7D/Illuminate/Routing/Router.html) la [facade Route](https://laravel.com/api/%7B%7Bversion%7D%7D/Illuminate/Routing/Router.html) como de la [instancia Route](https://laravel.com/api/%7B%7Bversion%7D%7D/Illuminate/Routing/Route.html) para revisar todos los métodos que están disponibles en las clases router y route.

[]()

## Compartición de recursos entre orígenes (CORS)

Laravel puede responder automáticamente a las peticiones HTTP CORS `OPTIONS` con los valores que configures. Todos los ajustes CORS pueden ser configurados en el archivo de configuración `config/cors.php` de tu aplicación. Las peticiones `OPTIONS` serán gestionadas automáticamente por el [middleware](/docs/%7B%7Bversion%7D%7D/middleware) `HandleCors` que está incluido por defecto en tu pila global de middleware. Su middleware global está localizado en el núcleo HTTP de su aplicación`(App\Http\Kernel`).

> **Nota**  
> Para más información sobre CORS y las cabeceras CORS, consulte la [documentación web MDN sobre CORS](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS#The_HTTP_response_headers).

[]()

## Caché de rutas

Cuando despliegues tu aplicación en producción, deberías aprovechar la caché cache rutas de Laravel. El uso de cache caché de rutas reducirá drásticamente la cantidad de tiempo que se tarda en registrar todas las rutas de tu aplicación. Para generar una caché cache rutas, ejecuta el comando `route:cache` Artisan:

```shell
php artisan route:cache
```

Después de ejecutar este comando, tu archivo de rutas en caché será cargado en cada petición. Recuerda, si añades nuevas rutas necesitarás generar un nuevo caché cache rutas. Debido a esto, sólo debes ejecutar el comando route: `cache` durante el despliegue de tu proyecto.

Puede utilizar el comando route: `clear` para borrar cache caché de rutas:

```shell
php artisan route:clear
```
