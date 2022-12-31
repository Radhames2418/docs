# Generación de URL

- [Introducción](#introduction)
- [Conceptos básicos](#the-basics)
  - [Generación de URL](#generating-urls)
  - [Acceso a la URL actual](#accessing-the-current-url)
- [URL para rutas con nombre](#urls-for-named-routes)
  - [URL firmadas](#signed-urls)
- [URL para acciones de controlador](#urls-for-controller-actions)
- [Valores por defecto](#default-values)

[]()

## Introducción

Laravel proporciona varios helpers para ayudarte a generar URLs para tu aplicación. Estos ayudantes son principalmente útiles cuando se construyen enlaces en sus plantillas y respuestas API, o cuando se generan respuestas de redirección a otra parte de su aplicación.

[]()

## Conceptos básicos

[]()

### Generación de URLs

El ayudante `url` puede utilizarse para generar URL arbitrarias para su aplicación. La URL generada utilizará automáticamente el esquema (HTTP o HTTPS) y el host de la petición actual que está siendo gestionada por la aplicación:

    $post = App\Models\Post::find(1);

    echo url("/posts/{$post->id}");

    // http://example.com/posts/1

[]()

### Acceso a la URL actual

Si no se proporciona ninguna ruta al ayudante de `url`, se devuelve una instancia de `Illuminate\Routing\UrlGenerator`, que permite acceder a información sobre la URL actual:

    // Get the current URL without the query string...
    echo url()->current();

    // Get the current URL including the query string...
    echo url()->full();

    // Get the full URL for the previous request...
    echo url()->previous();

También se puede acceder a cada uno de estos métodos a través de la [facade](/docs/%7B%7Bversion%7D%7D/facades) `URL`:

    use Illuminate\Support\Facades\URL;

    echo URL::current();

[]()

## URLs para rutas con nombre

El ayudante de `ruta` puede utilizarse para generar URL a [rutas con nombre](/docs/%7B%7Bversion%7D%7D/routing#named-routes). Las rutas con nombre permiten generar URLs sin estar acopladas a la URL real definida en la ruta. Por lo tanto, si la URL de la ruta cambia, no es necesario realizar cambios en las llamadas a la función de `ruta`. Por ejemplo, imagine que su aplicación contiene una ruta definida como la siguiente:

    Route::get('/post/{post}', function (Post $post) {
        //
    })->name('post.show');

Para generar una URL a esta ruta, puede utilizar el ayudante de `ruta` de esta manera:

    echo route('post.show', ['post' => 1]);

    // http://example.com/post/1

Por supuesto, el ayudante de `ruta` también puede utilizarse para generar URLs para rutas con múltiples parámetros:

    Route::get('/post/{post}/comment/{comment}', function (Post $post, Comment $comment) {
        //
    })->name('comment.show');

    echo route('comment.show', ['post' => 1, 'comment' => 3]);

    // http://example.com/post/1/comment/3

Cualquier elemento adicional array que no se corresponda con los parámetros de definición de la ruta se añadirá a la cadena de consulta de la URL:

    echo route('post.show', ['post' => 1, 'search' => 'rocket']);

    // http://example.com/post/1?search=rocket

[]()

#### Modelos Elocuentes

A menudo generará URLs utilizando la clave de ruta (normalmente la clave primaria) de [los modelos de Eloquent](/docs/%7B%7Bversion%7D%7D/eloquent). Por esta razón, puede pasar modelos Eloquent como valores de parámetro. El ayudante de `ruta` extraerá automáticamente la clave de ruta del modelo:

    echo route('post.show', ['post' => $post]);

[]()

### URL firmadas

Laravel permite crear fácilmente URLs "firmadas" a rutas con nombre. Estas URLs tienen una "firma" hash añadida a la cadena de consulta que permite a Laravel verificar que la URL no ha sido modificada desde que fue creada. Las URLs firmadas son especialmente útiles para rutas que son públicamente accesibles pero que necesitan una capa de protección contra la manipulación de URLs.

Por ejemplo, puede utilizar URLs firmadas para implementar un enlace público de "cancelación de suscripción" que se envía por correo electrónico a sus clientes. Para crear una URL firmada a una ruta con nombre, utilice el método `signedRoute` de la facade `URL`:

    use Illuminate\Support\Facades\URL;

    return URL::signedRoute('unsubscribe', ['user' => 1]);

Si quieres generar una URL de ruta firmada temporal que expire después de un tiempo determinado, puedes utilizar el método `temporarySignedRoute`. Cuando Laravel valide una URL de ruta firmada temporal, se asegurará de que la fecha de caducidad codificada en la URL firmada no haya transcurrido:

    use Illuminate\Support\Facades\URL;

    return URL::temporarySignedRoute(
        'unsubscribe', now()->addMinutes(30), ['user' => 1]
    );

[]()

#### Validación de peticiones de ruta firmadas

Para verificar que una solicitud entrante tiene una firma válida, debe llamar al método `hasValidSignature` en la instancia `Illuminate\Http\Request` entrante:

    use Illuminate\Http\Request;

    Route::get('/unsubscribe/{user}', function (Request $request) {
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        // ...
    })->name('unsubscribe');

A veces, puede que necesite permitir que el frontend de su aplicación añada datos a una URL firmada, como cuando se realiza paginación en el lado del cliente. Por lo tanto, puedes especificar los parámetros de consulta de la petición que deben ser ignorados al validar una URL firmada utilizando el método `hasValidSignatureWhileIgnoring`. Recuerde que ignorar parámetros permite a cualquiera modificar esos parámetros en la petición:

    if (! $request->hasValidSignatureWhileIgnoring(['page', 'order'])) {
        abort(401);
    }

En lugar de validar las URL firmadas utilizando la instancia de solicitud entrante, puede asignar el [middleware](/docs/%7B%7Bversion%7D%7D/middleware) `Illuminate\Routing\middleware\ValidateSignature` a la ruta. Si aún no está presente, debe asignar a este middleware una clave en la array `routeMiddleware` de su núcleo HTTP:

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
    ];

Una vez que hayas registrado el middleware en tu kernel, puedes adjuntarlo a una ruta. Si la petición entrante no tiene una firma válida, el middleware devolverá automáticamente una respuesta HTTP `403`:

    Route::post('/unsubscribe/{user}', function (Request $request) {
        // ...
    })->name('unsubscribe')->middleware('signed');

[]()

#### Respuesta a rutas con firma no válida

Cuando alguien visita una URL firmada que ha caducado, recibirá una página de error genérica para el código de estado HTTP `403`. Sin embargo, puede personalizar este comportamiento definiendo un closure "renderizable" personalizado para la excepción `InvalidSignatureException` en su manejador de excepciones. Este closure debería devolver una respuesta HTTP:

    use Illuminate\Routing\Exceptions\InvalidSignatureException;

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (InvalidSignatureException $e) {
            return response()->view('error.link-expired', [], 403);
        });
    }

[]()

## URLs para acciones de controlador

La función de `acción` genera una URL para la acción del controlador dado:

    use App\Http\Controllers\HomeController;

    $url = action([HomeController::class, 'index']);

Si el método del controlador acepta parámetros de ruta, puede pasar una array ciativa de parámetros de ruta como segundo argumento a la función:

    $url = action([UserController::class, 'profile'], ['id' => 1]);

[]()

## Valores por defecto

Para algunas aplicaciones, es posible que desee especificar valores predeterminados en toda la solicitud para determinados parámetros de URL. Por ejemplo, imagine que muchas de sus rutas definen un parámetro `{locale}`:

    Route::get('/{locale}/posts', function () {
        //
    })->name('post.index');

Es engorroso pasar siempre la `configuración regional` cada vez que se llama al ayudante de `ruta`. Por lo tanto, puede utilizar el método `URL::defaults` para definir un valor por defecto para este parámetro que se aplicará siempre durante la petición actual. Puede llamar a este método desde un [middleware#assigning-middleware-to-routes"> middleware](</docs/%7B%7Bversion%7D%7D/\<glossary variable=>) de ruta para tener acceso a la petición actual:

    <?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Support\Facades\URL;

    class SetDefaultLocaleForUrls
    {
        /**
         * Handle the incoming request.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \Closure  $next
         * @return \Illuminate\Http\Response
         */
        public function handle($request, Closure $next)
        {
            URL::defaults(['locale' => $request->user()->locale]);

            return $next($request);
        }
    }

Una vez establecido el valor por defecto para el parámetro `locale`, ya no es necesario pasar su valor cuando se generan URLs a través del ayudante de `ruta`.

[middleware-priority">]()

#### URL por defecto y middleware Prioridad

Establecer valores por defecto de URL puede interferir con el manejo de Laravel de las vinculaciones implícitas del modelo. Por lo tanto, debes [middleware#sorting-middleware">dar prioridad a tu middleware](</docs/%7B%7Bversion%7D%7D/\<glossary variable=>) [middleware#sorting-middleware"> middleware](</docs/%7B%7Bversion%7D%7D/\<glossary variable=>) [middleware#sorting-middleware"> middleware](</docs/%7B%7Bversion%7D%7D/\<glossary variable=>) que establece valores por defecto de URL para que se ejecute antes que el propio middleware `SubstituteBindings` de Laravel. Puedes conseguirlo asegurándote de que tu middleware se ejecuta antes que el middleware `SubstituteBindings` dentro de la propiedad `$middlewarePriority` del kernel HTTP de tu aplicación.

La propiedad `$middlewarePriority` se define en la clase base `Illuminate\Foundation\Http\Kernel`. Puede copiar su definición de esa clase y sobrescribirla en el núcleo HTTP de su aplicación para modificarla:

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        // ...
         \App\Http\Middleware\SetDefaultLocaleForUrls::class,
         \Illuminate\Routing\Middleware\SubstituteBindings::class,
         // ...
    ];
