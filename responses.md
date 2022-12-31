# Respuestas HTTP

- [Creación de respuestas](#creating-responses)
  - [Cómo adjuntar encabezados a las respuestas](#attaching-headers-to-responses)
  - [Adjuntar cookies a las respuestas](#attaching-cookies-to-responses)
  - [Cookies y cifrado](#cookies-and-encryption)
- [Redirecciones](#redirects)
  - [Redirección a rutas con nombre](#redirecting-named-routes)
  - [Redirección a acciones del controlador](#redirecting-controller-actions)
  - [Redirección a dominios externos](#redirecting-external-domains)
  - [Redireccionamiento con datos de sesión flasheados](#redirecting-with-flashed-session-data)
- [Otros tipos de respuesta](#other-response-types)
  - [Ver respuestas](#view-responses)
  - [Respuestas JSON](#json-responses)
  - [Descarga de archivos](#file-downloads)
  - [Respuestas de archivos](#file-responses)
- [Macros de respuesta](#response-macros)

[]()

## Creación de respuestas

[]()

#### Cadenas y matrices

Todas las rutas y controladores deben devolver una respuesta al navegador del usuario. Laravel proporciona varias formas diferentes de devolver respuestas. La respuesta más básica es devolver una cadena desde una ruta o controlador. El framework convertirá automáticamente la cadena en una respuesta HTTP completa:

    Route::get('/', function () {
        return 'Hello World';
    });

Además de devolver cadenas desde tus rutas y controladores, también puedes devolver arrays. El framework convertirá automáticamente el array en una respuesta JSON:

    Route::get('/', function () {
        return [1, 2, 3];
    });

> **Nota**  
> ¿Sabías que también puedes devolver [colecciones Eloquent](/docs/%7B%7Bversion%7D%7D/eloquent-collections) desde tus rutas o controladores? Se convertirán automáticamente a JSON. ¡Pruébalo!

[]()

#### Objetos de respuesta

Normalmente, no devolverás simples cadenas o matrices desde tus acciones de ruta. En su lugar, devolverá instancias o [vistas](/docs/%7B%7Bversion%7D%7D/views) `Illuminate\Http\Response` completas.

Devolver una instancia de `respuesta` completa le permite personalizar el código de estado HTTP y las cabeceras de la respuesta. Una instancia de `Response` hereda de la clase `Symfony\Component\HttpFoundation\Response`, que proporciona una variedad de métodos para construir respuestas HTTP:

    Route::get('/home', function () {
        return response('Hello World', 200)
                      ->header('Content-Type', 'text/plain');
    });

[]()

#### Modelos y colecciones Eloquent

También puede devolver modelos y colecciones [de Eloquent ORM](/docs/%7B%7Bversion%7D%7D/eloquent) directamente desde sus rutas y controladores. Cuando lo hagas, Laravel convertirá automáticamente los modelos y colecciones en respuestas JSON respetando los [atributos ocultos](/docs/%7B%7Bversion%7D%7D/eloquent-serialization#hiding-attributes-from-json) del modelo:

    use App\Models\User;

    Route::get('/user/{user}', function (User $user) {
        return $user;
    });

[]()

### Adjuntar cabeceras a las respuestas

Tenga en cuenta que la mayoría de los métodos de respuesta son encadenables, lo que permite la construcción fluida de instancias de respuesta. Por ejemplo, puedes utilizar el método `header` para añadir una serie de cabeceras a la respuesta antes de enviarla al usuario:

    return response($content)
                ->header('Content-Type', $type)
                ->header('X-Header-One', 'Header Value')
                ->header('X-Header-Two', 'Header Value');

O puedes utilizar el método `withHeaders` para especificar un array de cabeceras a añadir a la respuesta:

    return response($content)
                ->withHeaders([
                    'Content-Type' => $type,
                    'X-Header-One' => 'Header Value',
                    'X-Header-Two' => 'Header Value',
                ]);

[]()

#### cache Control middleware

Laravel incluye un middleware `cache.headers`, que puede utilizarse para establecer rápidamente la cabecera `cache` para un grupo de rutas. Las directivas deben proporcionarse utilizando el equivalente "snake case" de la directiva cache correspondiente y deben ir separadas por punto y coma. Si se especifica `etag` en la lista de directivas, se establecerá automáticamente un hash MD5 del contenido de la respuesta como identificador ETag:

    Route::middleware('cache.headers:public;max_age=2628000;etag')->group(function () {
        Route::get('/privacy', function () {
            // ...
        });

        Route::get('/terms', function () {
            // ...
        });
    });

[]()

### Adjuntar cookies a las respuestas

Puede adjuntar una cookie a una instancia saliente `Illuminate\Http\Response` utilizando el método `cookie`. Debe pasar a este método el nombre, el valor y el número de minutos que la cookie debe considerarse válida:

    return response('Hello World')->cookie(
        'name', 'value', $minutes
    );

El método `cookie` también acepta algunos argumentos más que se utilizan con menos frecuencia. Generalmente, estos argumentos tienen el mismo propósito y significado que los argumentos que se darían al método nativo [setcookie](https://secure.php.net/manual/en/function.setcookie.php) de PHP:

    return response('Hello World')->cookie(
        'name', 'value', $minutes, $path, $domain, $secure, $httpOnly
    );

Si quiere asegurarse de que se envía una cookie con la respuesta saliente pero todavía no tiene una instancia de esa respuesta, puede usar la facade `Cookie` para "poner en cola" cookies para adjuntarlas a la respuesta cuando se envíe. El método de `cola` acepta los argumentos necesarios para crear una instancia de cookie. Estas cookies se adjuntarán a la respuesta saliente antes de que se envíe al navegador:

    use Illuminate\Support\Facades\Cookie;

    Cookie::queue('name', 'value', $minutes);

[]()

#### Generación de instancias de cookies

Si desea generar una instancia de `Symfony\Component\HttpFoundation\Cookie` que se pueda adjuntar a una instancia de respuesta en un momento posterior, puede utilizar el ayudante de `cookie` global. Esta cookie no se devolverá al cliente a menos que se adjunte a una instancia de respuesta:

    $cookie = cookie('name', 'value', $minutes);

    return response('Hello World')->cookie($cookie);

[]()

#### Expiración anticipada de cookies

Puede eliminar una cookie expirándola mediante el método `withoutCookie` de una respuesta saliente:

    return response('Hello World')->withoutCookie('name');

Si aún no dispone de una instancia de la respuesta saliente, puede utilizar el método `expire` de la facade `Cookie` para expirar una cookie:

    Cookie::expire('name');

[]()

### Cookies y encriptación

Por defecto, todas las cookies generadas por Laravel son encriptadas y firmadas para que no puedan ser modificadas o leídas por el cliente. Si desea desactivar el cifrado para un subconjunto de cookies generadas por su aplicación, puede utilizar la propiedad `$except` del middleware `App\Http\middleware\EncryptCookies`, que se encuentra en el directorio `app/Http/middleware`:

    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [
        'cookie_name',
    ];

[]()

## Redirecciones

Las respuestas de redirección son instancias de la clase `Illuminate\Http\RedirectResponse`, y contienen las cabeceras adecuadas necesarias para redirigir al usuario a otra URL. Hay varias maneras de generar una instancia `RedirectResponse`. El método más sencillo es utilizar el ayudante de `redirección` global:

    Route::get('/dashboard', function () {
        return redirect('home/dashboard');
    });

A veces puede que desee redirigir al usuario a su ubicación anterior, como cuando un formulario enviado no es válido. Puede hacerlo utilizando la función de ayuda global `back`. Dado que esta función utiliza la [sesión](/docs/%7B%7Bversion%7D%7D/session), asegúrese de que la ruta que llama a la función `back` está utilizando el grupo `web` middleware:

    Route::post('/user/profile', function () {
        // Validate the request...

        return back()->withInput();
    });

[]()

### Redirección a rutas con nombre

Cuando se llama al ayudante de `redirección` sin parámetros, se devuelve una instancia de `Illuminate\Routing\Redirector`, lo que permite llamar a cualquier método de la instancia `Redirector`. Por ejemplo, para generar una `RedirectResponse` a una ruta con nombre, puede utilizar el método `route`:

    return redirect()->route('login');

Si su ruta tiene parámetros, puede pasarlos como segundo argumento al método de `ruta`:

    // For a route with the following URI: /profile/{id}

    return redirect()->route('profile', ['id' => 1]);

[]()

#### Rellenando parámetros a través de modelos Eloquent

Si está redirigiendo a una ruta con un parámetro "ID" que está siendo rellenado desde un modelo Eloquent, puede pasar el propio modelo. El ID se extraerá automáticamente:

    // For a route with the following URI: /profile/{id}

    return redirect()->route('profile', [$user]);

Si desea personalizar el valor que se coloca en el parámetro de ruta, puede especificar la columna en la definición del parámetro de ruta`(/profile/{id:slug}`) o puede anular el método `getRouteKey` en su modelo Eloquent:

    /**
     * Get the value of the model's route key.
     *
     * @return mixed
     */
    public function getRouteKey()
    {
        return $this->slug;
    }

[]()

### Redirección a acciones de controlador

También puede generar redirecciones a [acciones del controlador](/docs/%7B%7Bversion%7D%7D/controllers). Para ello, pase el controlador y el nombre de la acción al método de `acción`:

    use App\Http\Controllers\UserController;

    return redirect()->action([UserController::class, 'index']);

Si la ruta de tu controlador requiere parámetros, puedes pasarlos como segundo argumento al método de `acción`:

    return redirect()->action(
        [UserController::class, 'profile'], ['id' => 1]
    );

[]()

### Redireccionamiento a dominios externos

A veces puede que necesite redirigir a un dominio fuera de su aplicación. Puede hacerlo llamando al método `away`, que crea una `RedirectResponse` sin ninguna codificación de URL adicional, validación o verificación:

    return redirect()->away('https://www.google.com');

[]()

### Redirección con datos de sesión flasheados

La redirección a una nueva URL y la [transmisión de datos a la](/docs/%7B%7Bversion%7D%7D/session#flash-data) sesión suelen hacerse al mismo tiempo. Normalmente, esto se hace después de realizar con éxito una acción cuando se muestra un mensaje de éxito a la sesión. Para mayor comodidad, puede crear una instancia de `RedirectResponse` y enviar los datos a la sesión en una única cadena de métodos fluida:

    Route::post('/user/profile', function () {
        // ...

        return redirect('dashboard')->with('status', 'Profile updated!');
    });

Después de redirigir al usuario, puede mostrar el mensaje de la [sesión](/docs/%7B%7Bversion%7D%7D/session). Por ejemplo, utilizando la [sintaxis de Blade](/docs/%7B%7Bversion%7D%7D/blade):

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

[]()

#### Redirección con entrada

Puede utilizar el método `withInput` proporcionado por la instancia `RedirectResponse` para enviar los datos de entrada de la solicitud actual a la sesión antes de redirigir al usuario a una nueva ubicación. Esto se hace normalmente si el usuario ha encontrado un error de validación. Una vez que los datos han sido enviados a la sesión, puede [recuperarlos](/docs/%7B%7Bversion%7D%7D/requests#retrieving-old-input) fácilmente durante la siguiente petición para rellenar el formulario:

    return back()->withInput();

[]()

## Otros tipos de respuestas

El `response` helper puede ser utilizado para generar otros tipos de instancias de respuesta. Cuando se llama al ayudante de `respuesta` sin argumentos, se devuelve una implementación del [contrato](/docs/%7B%7Bversion%7D%7D/contracts) `Illuminate\Contracts\Routing\ResponseFactory`. Este contrato proporciona varios métodos útiles para generar respuestas.

[]()

### Ver respuestas

Si necesita controlar el estado y las cabeceras de la respuesta, pero también necesita devolver una [vista](/docs/%7B%7Bversion%7D%7D/views) como contenido de la respuesta, debe utilizar el método `view`:

    return response()
                ->view('hello', $data, 200)
                ->header('Content-Type', $type);

Por supuesto, si no necesita pasar un código de estado HTTP personalizado o cabeceras personalizadas, puede utilizar la función de ayuda de `la vista` global.

[]()

### Respuestas JSON

El método `json` establecerá automáticamente la cabecera `Content-Type` a `application/json`, así como convertirá el array dado a JSON usando la función PHP `json_encode`:

    return response()->json([
        'name' => 'Abigail',
        'state' => 'CA',
    ]);

Si quieres crear una respuesta JSONP, puedes usar el método `json` en combinación con el método `withCallback`:

    return response()
                ->json(['name' => 'Abigail', 'state' => 'CA'])
                ->withCallback($request->input('callback'));

[]()

### Descarga de archivos

El método `download` puede utilizarse para generar una respuesta que obligue al navegador del usuario a descargar el archivo en la ruta indicada. El método `download` acepta un nombre de fichero como segundo argumento del método, que determinará el nombre de fichero que verá el usuario que descargue el fichero. Finalmente, puedes pasar un array de cabeceras HTTP como tercer argumento al método:

    return response()->download($pathToFile);

    return response()->download($pathToFile, $name, $headers);

> **Advertencia**  
> Symfony HttpFoundation, que gestiona las descargas de archivos, requiere que el archivo que se está descargando tenga un nombre de archivo ASCII.

[]()

#### Descargas en tiempo real

A veces puedes desear convertir la cadena de respuesta de una operación dada en una respuesta descargable sin tener que escribir el contenido de la operación en el disco. En este caso, puede utilizar el método `streamDownload`. Este método acepta como argumentos una llamada de retorno, un nombre de fichero y una array opcional de cabeceras:

    use App\Services\GitHub;

    return response()->streamDownload(function () {
        echo GitHub::api('repo')
                    ->contents()
                    ->readme('laravel', 'laravel')['contents'];
    }, 'laravel-readme.md');

[]()

### Respuestas de archivo

El método `file` puede utilizarse para mostrar un archivo, como una imagen o un PDF, directamente en el navegador del usuario en lugar de iniciar una descarga. Este método acepta la ruta al archivo como primer argumento y una array de encabezados como segundo argumento:

    return response()->file($pathToFile);

    return response()->file($pathToFile, $headers);

[]()

## Macros de respuesta

Si desea definir una respuesta personalizada que pueda reutilizar en varias de sus rutas y controladores, puede utilizar el método `macro` de la facade `Response`. Normalmente, debería llamar a este método desde el método `boot` de uno de los [proveedores de servicio](/docs/%7B%7Bversion%7D%7D/providers) de su aplicación, como el proveedor de servicio `App\Providers\AppServiceProvider`:

    <?php

    namespace App\Providers;

    use Illuminate\Support\Facades\Response;
    use Illuminate\Support\ServiceProvider;

    class AppServiceProvider extends ServiceProvider
    {
        /**
         * Bootstrap any application services.
         *
         * @return void
         */
        public function boot()
        {
            Response::macro('caps', function ($value) {
                return Response::make(strtoupper($value));
            });
        }
    }

La función `macro` acepta un nombre como primer argumento y un closure como segundo argumento. El closure de la macro se ejecutará cuando se llame al nombre de la macro desde una implementación de `ResponseFactory` o el `response` helper:

    return response()->caps('foo');
