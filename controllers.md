# Controladores

- [Introducción](#introduction)
- [Escritura de controladores](#writing-controllers)
  - [Controladores básicos](#basic-controllers)
  - [Controladores de acción única](#single-action-controllers)
- [Controladores middleware](#controller-middleware)
- [Controladores de recursos](#resource-controllers)
  - [Rutas parciales de recursos](#restful-partial-resource-routes)
  - [Recursos anidados](#restful-nested-resources)
  - [Nomenclatura de rutas de recursos](#restful-naming-resource-routes)
  - [Cómo nombrar los parámetros de las rutas de recursos](#restful-naming-resource-route-parameters)
  - [Alcance de las rutas de recursos](#restful-scoping-resource-routes)
  - [Localización de URI de recursos](#restful-localizing-resource-uris)
  - [Complementación de controladores de recursos](#restful-supplementing-resource-controllers)
  - [Controladores de Recursos Singleton](#singleton-resource-controllers)
- [Inyección de dependencia y controladores](#dependency-injection-and-controllers)

[]()

## Introducción

En lugar de definir toda su lógica de gestión de peticiones como closures en sus archivos de ruta, puede que desee organizar este comportamiento utilizando clases "controlador". Los controladores pueden agrupar la lógica de gestión de peticiones relacionadas en una única clase. Por ejemplo, una clase `UserController` podría manejar todas las peticiones entrantes relacionadas con usuarios, incluyendo mostrar, crear, actualizar y borrar usuarios. Por defecto, los controladores se almacenan en el directorio `app/Http/Controllers`.

[]()

## Escribiendo Controladores

[]()

### Controladores básicos

Veamos un ejemplo de controlador básico. Ten en cuenta que el controlador extiende la clase base de controladores incluida con Laravel: `App/Http\Controllers\Controller`:

    <?php

    namespace App\Http\Controllers;

    use App\Models\User;

    class UserController extends Controller
    {
        /**
         * Show the profile for a given user.
         *
         * @param  int  $id
         * @return \Illuminate\View\View
         */
        public function show($id)
        {
            return view('user.profile', [
                'user' => User::findOrFail($id)
            ]);
        }
    }

Puedes definir una ruta a este método controlador de la siguiente manera:

    use App\Http\Controllers\UserController;

    Route::get('/user/{id}', [UserController::class, 'show']);

Cuando una petición entrante coincide con la ruta URI especificada, el método `show` de la clase `App\Http\Controllers\UserController` será invocado y los parámetros de la ruta serán pasados al método.

> **Nota**  
> No **es necesario** que los controladores extiendan una clase base. Sin embargo, no tendrán acceso a características convenientes como los métodos `middleware` y `authorize`.

[]()

### Controladores de acción única

Si una acción de controlador es particularmente compleja, puede que te resulte conveniente dedicar una clase de controlador entera a esa única acción. Para ello, puede definir un único método `__invoke` dentro del controlador:

    <?php

    namespace App\Http\Controllers;

    use App\Models\User;

    class ProvisionServer extends Controller
    {
        /**
         * Provision a new web server.
         *
         * @return \Illuminate\Http\Response
         */
        public function __invoke()
        {
            // ...
        }
    }

Al registrar rutas para controladores de acción única, no es necesario especificar un método de controlador. En su lugar, puede simplemente pasar el nombre del controlador al enrutador:

    use App\Http\Controllers\ProvisionServer;

    Route::post('/server', ProvisionServer::class);

Puedes generar un controlador invocable utilizando la opción `--invokable` del comando `make:controller` de Artisan:

```shell
php artisan make:controller ProvisionServer --invokable
```

> **Nota**  
> stubs controlador pueden personalizarse utilizando [stub-customization">la publicación destub](</docs/%7B%7Bversion%7D%7D/artisan#\<glossary variable=>).

[]()

## Controlador middleware

El[middleware](/docs/%7B%7Bversion%7D%7D/middleware) puede ser asignado a las rutas del controlador en sus archivos de ruta:

    Route::get('profile', [UserController::class, 'show'])->middleware('auth');

O, puedes encontrar conveniente especificar middleware dentro del constructor de tu controlador. Usando el método `middleware` dentro del constructor de tu controlador, puedes asignar middleware a las acciones del controlador:

    class UserController extends Controller
    {
        /**
         * Instantiate a new controller instance.
         *
         * @return void
         */
        public function __construct()
        {
            $this->middleware('auth');
            $this->middleware('log')->only('index');
            $this->middleware('subscribed')->except('store');
        }
    }

Los controladores también permiten registrar middleware utilizando un closure. Esto proporciona una manera conveniente de definir un middleware en línea para un solo controlador sin definir una clase entera de middleware:

    $this->middleware(function ($request, $next) {
        return $next($request);
    });

[]()

## Controladores de recursos

Si piensas en cada modelo Eloquent de tu aplicación como un "recurso", es típico realizar los mismos conjuntos de acciones contra cada recurso de tu aplicación. Por ejemplo, imagine que su aplicación contiene un modelo de `Foto` y un modelo de `Película`. Es probable que los usuarios puedan crear, leer, actualizar o eliminar estos recursos.

Debido a este caso de uso común, el enrutamiento de recursos de Laravel asigna las típicas rutas de creación, lectura, actualización y eliminación ("CRUD") a un controlador con una sola línea de código. Para empezar, podemos utilizar la opción `--resource` del comando `make:` controller de Artisan para crear rápidamente un controlador que gestione estas acciones:

```shell
php artisan make:controller PhotoController --resource
```

Este comando generará un controlador en `app/Http/Controllers/PhotoController.php`. El controlador contendrá un método para cada una de las operaciones de recursos disponibles. A continuación, puede registrar una ruta de recursos que apunte al controlador:

    use App\Http\Controllers\PhotoController;

    Route::resource('photos', PhotoController::class);

Esta única declaración de ruta crea múltiples rutas para manejar una variedad de acciones sobre el recurso. El controlador generado ya tendrá métodos para cada una de estas acciones. Recuerda, siempre puedes obtener una rápida visión general de las rutas de tu aplicación ejecutando el comando `route:list` de Artisan.

Incluso puedes registrar muchos controladores de recursos a la vez pasando un array al método `resources`:

    Route::resources([
        'photos' => PhotoController::class,
        'posts' => PostController::class,
    ]);

[]()

#### Acciones Manejadas por el Controlador de Recursos

|Verbo    |URI                   |Acción    |Nombre de la ruta|
|---------|----------------------|----------|-----------------|
|GET      |`/photos`             |índice    |fotos.index      |
|GET      |`/photos/create`      |crear     |fotos.crear      |
|POST     |`/photos`             |almacenar |fotos.almacenar  |
|GET      |`/photos/{photo}`     |mostrar   |fotos.mostrar    |
|GET      |`/photos/{photo}/edit`|editar    |fotos.editar     |
|PUT/PATCH|`/photos/{photo}`     |actualizar|fotos.actualizar |
|BORRAR   |`/photos/{photo}`     |destruir  |fotos.destruir   |

[]()

#### Personalización del comportamiento del modelo no encontrado

Normalmente, se generará una respuesta HTTP 404 si no se encuentra un modelo de recurso vinculado implícitamente. Sin embargo, puede personalizar este comportamiento llamando al método `missing` cuando defina su ruta de recursos. El método `missing` acepta un closure que se invocará si no se encuentra un modelo vinculado implícitamente para ninguna de las rutas del recurso:

    use App\Http\Controllers\PhotoController;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Redirect;

    Route::resource('photos', PhotoController::class)
            ->missing(function (Request $request) {
                return Redirect::route('photos.index');
            });

[]()

#### Modelos borrados

Típicamente, la vinculación implícita de modelos no recuperará modelos que han sido [borrados suavemente](/docs/%7B%7Bversion%7D%7D/eloquent#soft-deleting), y en su lugar devolverá una respuesta HTTP 404. Sin embargo, puedes indicar al framework que permita modelos borrados soft invocando el método `withTrashed` al definir tu ruta de recursos:

    use App\Http\Controllers\PhotoController;

    Route::resource('photos', PhotoController::class)->withTrashed();

Si se llama a `withTrashed` sin argumentos, se permitirán los modelos borrados por software para las rutas de recursos `show`, `edit` y `update`. Puede especificar un subconjunto de estas rutas pasando un array al método `withTrashed`:

    Route::resource('photos', PhotoController::class)->withTrashed(['show']);

[]()

#### Especificación del Modelo de Recurso

Si utiliza el [enlace de modelo](/docs/%7B%7Bversion%7D%7D/routing#route-model-binding) de ruta y desea que los métodos del controlador de recursos indiquen una instancia de modelo, puede utilizar la opción `--model` al generar el controlador:

```shell
php artisan make:controller PhotoController --model=Photo --resource
```

[]()

#### Generación de peticiones de formulario

Puedes proporcionar la opción `--requests` cuando generes un controlador de recursos para instruir a Artisan a generar [clases de solicitud de formularios](/docs/%7B%7Bversion%7D%7D/validation#form-request-validation) para los métodos de almacenamiento y actualización del controlador:

```shell
php artisan make:controller PhotoController --model=Photo --resource --requests
```

[]()

### Rutas parciales de recursos

Al declarar una ruta de recursos, puede especificar un subconjunto de acciones que el controlador debe manejar en lugar del conjunto completo de acciones por defecto:

    use App\Http\Controllers\PhotoController;

    Route::resource('photos', PhotoController::class)->only([
        'index', 'show'
    ]);

    Route::resource('photos', PhotoController::class)->except([
        'create', 'store', 'update', 'destroy'
    ]);

[]()

#### Rutas de recursos API

Al declarar rutas de recursos que serán consumidas por APIs, comúnmente querrá excluir rutas que presentan plantillas HTML como `crear` y `editar`. Por comodidad, puede utilizar el método `apiResource` para excluir automáticamente estas dos rutas:

    use App\Http\Controllers\PhotoController;

    Route::apiResource('photos', PhotoController::class);

Puede registrar muchos controladores de recursos de API a la vez pasando una array al método `apiResources`:

    use App\Http\Controllers\PhotoController;
    use App\Http\Controllers\PostController;

    Route::apiResources([
        'photos' => PhotoController::class,
        'posts' => PostController::class,
    ]);

Para generar rápidamente un controlador de recursos de API que no incluya los métodos `create` o `edit`, utilice el modificador `--api` al ejecutar el comando `make:controller`:

```shell
php artisan make:controller PhotoController --api
```

[]()

### Recursos anidados

Algunas veces puedes necesitar definir rutas a un recurso anidado. Por ejemplo, un recurso foto puede tener múltiples comentarios que pueden ser adjuntados a la foto. Para anidar los controladores de recursos, puede utilizar la notación "punto" en su declaración de ruta:

    use App\Http\Controllers\PhotoCommentController;

    Route::resource('photos.comments', PhotoCommentController::class);

Esta ruta registrará un recurso anidado al que se podrá acceder con URIs como las siguientes:

    /photos/{photo}/comments/{comment}

[]()

#### Alcance de los recursos anidados

La función de [vinculación implícita de modelos](/docs/%7B%7Bversion%7D%7D/routing#implicit-model-binding-scoping) de Laravel puede delimitar automáticamente las vinculaciones anidadas de forma que se confirme que el modelo hijo resuelto pertenece al modelo padre. Utilizando el método `scoped` al definir tu recurso anidado, puedes habilitar el alcance automático así como indicar a Laravel por qué campo debe recuperarse el recurso hijo. Para obtener más información sobre cómo lograr esto, consulte la documentación sobre [el ámbito de las rutas de recursos](#restful-scoping-resource-routes).

[]()

#### Anidamiento superficial

A menudo, no es del todo necesario tener los IDs padre e hijo dentro de un URI ya que el ID hijo ya es un identificador único. Cuando utilice identificadores únicos como claves primarias autoincrementadas para identificar sus modelos en segmentos URI, puede optar por utilizar un "anidamiento superficial":

    use App\Http\Controllers\CommentController;

    Route::resource('photos.comments', CommentController::class)->shallow();

Esta definición de ruta definirá las siguientes rutas

|Verbo    |URI                              |Acción    |Nombre de la ruta      |
|---------|---------------------------------|----------|-----------------------|
|GET      |`/photos/{photo}/comments`       |índice    |photos.comments.index  |
|GET      |`/photos/{photo}/comments/create`|crear     |fotos.comentarios.crear|
|POST     |`/photos/{photo}/comments`       |almacenar |photos.comments.store  |
|GET      |`/comments/{comment}`            |mostrar   |comentarios.mostrar    |
|GET      |`/comments/{comment}/edit`       |editar    |comentarios.editar     |
|PUT/PATCH|`/comments/{comment}`            |actualizar|comentarios.actualizar |
|BORRAR   |`/comments/{comment}`            |destruir  |comentarios.destruir   |

[]()

### Nomenclatura de rutas de recursos

Por defecto, todas las acciones del controlador de recursos tienen un nombre de ruta; sin embargo, puedes sobrescribir estos nombres pasando un array `names` con los nombres de ruta que desees:

    use App\Http\Controllers\PhotoController;

    Route::resource('photos', PhotoController::class)->names([
        'create' => 'photos.build'
    ]);

[]()

### Cómo nombrar los parámetros de las rutas de recursos

Por defecto, `Route::` resource creará los parámetros de ruta para sus rutas de recursos basándose en la versión "singularizada" del nombre del recurso. Puedes anular esto fácilmente para cada recurso utilizando el método `parameters`. El array pasado al método `parameters` debería ser un array asociativo de nombres de recursos y nombres de parámetros:

    use App\Http\Controllers\AdminUserController;

    Route::resource('users', AdminUserController::class)->parameters([
        'users' => 'admin_user'
    ]);

El ejemplo anterior genera el siguiente URI para la ruta `show` del recurso:

    /users/{admin_user}

[]()

### Alcance de las rutas de recursos

La característica scoped [implicit model binding](/docs/%7B%7Bversion%7D%7D/routing#implicit-model-binding-scoping) de Laravel puede automáticamente hacer scoped bindings anidados de tal forma que se confirme que el modelo hijo resuelto pertenece al modelo padre. Utilizando el método `scoped` al definir tu recurso anidado, puedes habilitar el scoping automático así como indicar a Laravel por qué campo debe recuperarse el recurso hijo:

    use App\Http\Controllers\PhotoCommentController;

    Route::resource('photos.comments', PhotoCommentController::class)->scoped([
        'comment' => 'slug',
    ]);

Esta ruta registrará un recurso anidado de ámbito al que se puede acceder con URI como el siguiente:

    /photos/{photo}/comments/{comment:slug}

Cuando se utiliza un enlace implícito con clave personalizada como parámetro de ruta anidada, Laravel automáticamente delimitará la consulta para recuperar el modelo anidado por su padre utilizando convenciones para adivinar el nombre de la relación en el padre. En este caso, se asumirá que el modelo `Photo` tiene una relación llamada `comments` (el plural del nombre del parámetro de ruta) que puede utilizarse para recuperar el modelo `Comment`.

[]()

### Localización de URI de recursos

Por defecto, `Route::` resource creará URIs de recursos utilizando verbos en inglés y reglas de plural. Si necesita localizar los verbos de las acciones de `creación` y `edición`, puede utilizar el método Route `::resourceVerbs`. Esto puede hacerse al principio del método de `arranque` dentro del `App\Providers\RouteServiceProvider` de tu aplicación:

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        Route::resourceVerbs([
            'create' => 'crear',
            'edit' => 'editar',
        ]);

        // ...
    }

El pluralizador de Laravel soporta [varios idiomas diferentes que puedes configurar en función de tus necesidades](/docs/%7B%7Bversion%7D%7D/localization#pluralization-language). Una vez personalizados los verbos y el idioma de pluralización, un registro de ruta de recursos como `Route::resource('publicacion', PublicacionController::class)` producirá las siguientes URIs:

    /publicacion/crear

    /publicacion/{publicaciones}/editar

[]()

### Controladores de recursos complementarios

Si necesitas añadir rutas adicionales a un controlador de recursos más allá del conjunto predeterminado de rutas de recursos, debes definir esas rutas antes de tu llamada al método `Route::` resource; de lo contrario, las rutas definidas por el método `resource` pueden tener prioridad involuntariamente sobre tus rutas suplementarias:

    use App\Http\Controller\PhotoController;

    Route::get('/photos/popular', [PhotoController::class, 'popular']);
    Route::resource('photos', PhotoController::class);

> **Nota**  
> Recuerda mantener tus controladores enfocados. Si te encuentras necesitando rutinariamente métodos fuera del típico conjunto de acciones resource, considera dividir tu controlador en dos controladores más pequeños.

[]()

### Controladores de Recursos Singleton

A veces, tu aplicación tendrá recursos que pueden tener una única instancia. Por ejemplo, el "perfil" de un usuario puede ser editado o actualizado, pero un usuario no puede tener más de un "perfil". Del mismo modo, una imagen puede tener una única "miniatura". Estos recursos se denominan "recursos singleton", lo que significa que sólo puede existir una instancia del recurso. En estos casos, puede registrar un controlador de recursos "singleton":

```php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::singleton('profile', ProfileController::class);
```

La definición de recurso singleton anterior registrará las siguientes rutas. Como puede ver, las rutas de "creación" no se registran para los recursos singleton, y las rutas registradas no aceptan un identificador ya que sólo puede existir una instancia del recurso:

|Verbo    |URI            |Acción    |Nombre de la ruta|
|---------|---------------|----------|-----------------|
|GET      |`/profile`     |mostrar   |perfil.mostrar   |
|GET      |`/profile/edit`|editar    |perfil.editar    |
|PUT/PATCH|`/profile`     |actualizar|profile.update   |

Los recursos Singleton también pueden anidarse dentro de un recurso estándar:

```php
Route::singleton('photos.thumbnail', ThumbnailController::class);
```

En este ejemplo, el recurso `fotos` recibiría todas las [rutas de recursos estándar](#actions-handled-by-resource-controller); sin embargo, el recurso `miniatura` sería un recurso singleton con las siguientes rutas:

|Verbo    |URI                             |Acción    |Nombre de la ruta      |
|---------|--------------------------------|----------|-----------------------|
|GET      |`/photos/{photo}/thumbnail`     |mostrar   |photos.thumbnail.show  |
|GET      |`/photos/{photo}/thumbnail/edit`|editar    |fotos.miniatura.editar |
|PUT/PATCH|`/photos/{photo}/thumbnail`     |actualizar|photos.thumbnail.update|

[]()

#### Recursos Singleton creables

En ocasiones, es posible que desee definir rutas de creación y almacenamiento para un recurso singleton. Para ello, puede invocar el método `creatable` al registrar la ruta del recurso singleton:

```php
Route::singleton('photos.thumbnail', ThumbnailController::class)->creatable();
```

En este ejemplo, se registrarán las siguientes rutas. Como puede ver, también se registrará una ruta `DELETE` para los recursos singleton creables:

|Verb     |URI                               |Acción    |Nombre de la ruta       |
|---------|----------------------------------|----------|------------------------|
|GET      |`/photos/{photo}/thumbnail/create`|crear     |photos.thumbnail.create |
|POST     |`/photos/{photo}/thumbnail`       |almacenar |photos.thumbnail.store  |
|GET      |`/photos/{photo}/thumbnail`       |mostrar   |photos.thumbnail.show   |
|GET      |`/photos/{photo}/thumbnail/edit`  |editar    |photos.thumbnail.edit   |
|PUT/PATCH|`/photos/{photo}/thumbnail`       |actualizar|photos.thumbnail.update |
|BORRAR   |`/photos/{photo}/thumbnail`       |destruir  |photos.thumbnail.destroy|

[]()

#### Recursos API Singleton

El método `apiSingleton` puede utilizarse para registrar un recurso singleton que se manipulará a través de una API, haciendo innecesarias las rutas `create` y `edit`:

```php
Route::apiSingleton('profile', ProfileController::class);
```

Por supuesto, los recursos API singleton también pueden ser `creables`, lo que registrará las rutas `store` y `destroy` para el recurso:

```php
Route::apiSingleton('photos.thumbnail', ProfileController::class)->creatable();
```

[]()

## Inyección de dependencia y controladores

[]()

#### Inyección de constructor

El [contenedor de servicios](/docs/%7B%7Bversion%7D%7D/container) de Laravel se utiliza para resolver todos los controladores de Laravel. Como resultado, usted es capaz de tipo-indicar cualquier dependencia de su controlador puede necesitar en su constructor. Las dependencias declaradas se resolverán automáticamente y se inyectarán en la instancia del controlador:

    <?php

    namespace App\Http\Controllers;

    use App\Repositories\UserRepository;

    class UserController extends Controller
    {
        /**
         * The user repository instance.
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
    }

[]()

#### Inyección de métodos

Además de la inyección en el constructor, también puedes indicar dependencias en los métodos de tu controlador. Un caso de uso común para la inyección de métodos es inyectar la instancia `Illuminate\Http\Request` en los métodos de su controlador:

    <?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;

    class UserController extends Controller
    {
        /**
         * Store a new user.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
        public function store(Request $request)
        {
            $name = $request->name;

            //
        }
    }

Si su método de controlador también espera la entrada de un parámetro de ruta, liste los argumentos de su ruta después de sus otras dependencias. Por ejemplo, si tu ruta está definida así:

    use App\Http\Controllers\UserController;

    Route::put('/user/{id}', [UserController::class, 'update']);

Usted todavía puede tipo-hint el `Illuminate\Http\Request` y acceder a su parámetro `id` mediante la definición de su método de controlador de la siguiente manera:

    <?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;

    class UserController extends Controller
    {
        /**
         * Update the given user.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  string  $id
         * @return \Illuminate\Http\Response
         */
        public function update(Request $request, $id)
        {
            //
        }
    }
