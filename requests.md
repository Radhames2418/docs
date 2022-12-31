# Peticiones HTTP

- [Introducción](#introduction)
- [Interacción con la petición](#interacting-with-the-request)
  - [Acceso a la solicitud](#accessing-the-request)
  - [Ruta, host y método de la solicitud](#request-path-and-method)
  - [Cabeceras de solicitud](#request-headers)
  - [Dirección IP de la solicitud](#request-ip-address)
  - [Negociación del contenido](#content-negotiation)
  - [Solicitudes PSR-7](#psr7-requests)
- [Entrada](#input)
  - [Recuperación de entradas](#retrieving-input)
  - [Determinación de la presencia de entradas](#determining-if-input-is-present)
  - [Fusión de entradas adicionales](#merging-additional-input)
  - [Entrada antigua](#old-input)
  - [Cookies](#cookies)
  - [Recorte y normalización de entradas](#input-trimming-and-normalization)
- [Archivos](#files)
  - [Recuperación de archivos cargados](#retrieving-uploaded-files)
  - [Almacenamiento de archivos cargados](#storing-uploaded-files)
- [Configuración de proxies de confianza](#configuring-trusted-proxies)
- [Configuración de hosts de confianza](#configuring-trusted-hosts)

[]()

## Introducción

La clase `Illuminate\Http\Request` de Laravel proporciona una forma orientada a objetos de interactuar con la solicitud HTTP actual que está siendo gestionada por su aplicación, así como recuperar la entrada, las cookies y los archivos que se enviaron con la solicitud.

[]()

## Interacción con la petición

[]()

### Acceso a la petición

Para obtener una instancia de la solicitud HTTP actual a través de la inyección de dependencia, debe escribir la clase `Illuminate\Http\Request` en su closure ruta o método de controlador. La instancia de solicitud entrante será inyectada automáticamente por el [contenedor de servicios](/docs/%7B%7Bversion%7D%7D/container) de Laravel:

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
            $name = $request->input('name');

            //
        }
    }

Como se ha mencionado, también puede tipo de sugerencia de la clase `Illuminate\Http\Request` en un cierre closure ruta. El contenedor de servicios inyectará automáticamente la solicitud entrante en el closure cuando se ejecute:

    use Illuminate\Http\Request;

    Route::get('/', function (Request $request) {
        //
    });

[]()

#### Inyección de dependencia y parámetros de ruta

Si el método de su controlador también espera la entrada de un parámetro de ruta, debe listar sus parámetros de ruta después de sus otras dependencias. Por ejemplo, si su ruta está definida así:

    use App\Http\Controllers\UserController;

    Route::put('/user/{id}', [UserController::class, 'update']);

Usted todavía puede tipo-hint el `Illuminate\Http\Request` y acceder a su parámetro de ruta `id` mediante la definición de su método de controlador de la siguiente manera:

    <?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;

    class UserController extends Controller
    {
        /**
         * Update the specified user.
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

[]()

### Ruta, Host y Método de la Petición

La instancia `Illuminate\Http\Request` proporciona una variedad de métodos para examinar la solicitud HTTP entrante y extiende la clase `Symfony\Component\HttpFoundation\Request`. Vamos a discutir algunos de los métodos más importantes a continuación.

[]()

#### Recuperando la ruta de la petición

El método `path` devuelve la información de la ruta de la petición. Así, si la petición entrante está dirigida a `http://example.com/foo/bar,` el método `path` devolverá `foo/bar`:

    $uri = $request->path();

[]()

#### Inspección de la ruta de la petición

El método `is` le permite verificar que la ruta de la petición entrante coincide con un patrón dado. Puede utilizar el carácter `*` como comodín cuando utilice este método:

    if ($request->is('admin/*')) {
        //
    }

Utilizando el método `routeIs`, puede determinar si la solicitud entrante ha coincidido con una [ruta con nombre](/docs/%7B%7Bversion%7D%7D/routing#named-routes):

    if ($request->routeIs('admin.*')) {
        //
    }

[]()

#### Obtención de la URL de la petición

Para recuperar la URL completa de la petición entrante puede utilizar los métodos `url` o `fullUrl`. El método `url` devolverá la URL sin la cadena de consulta, mientras que el método `fullUrl` incluye la cadena de consulta:

    $url = $request->url();

    $urlWithQueryString = $request->fullUrl();

Si desea añadir la cadena de consulta a la URL actual, puede utilizar el método `fullUrlWithQuery`. Este método combina el array de variables de la cadena de consulta con la cadena de consulta actual:

    $request->fullUrlWithQuery(['type' => 'phone']);

[]()

#### Obtención del host de petición

Puede recuperar el "host" de la petición entrante mediante los métodos `host`, `httpHost` y `schemeAndHttpHost`:

    $request->host();
    $request->httpHost();
    $request->schemeAndHttpHost();

[]()

#### Obtención del método de petición

El método `method` devolverá el verbo HTTP de la petición. Puede usar el método `isMethod` para verificar que el verbo HTTP coincide con una cadena dada:

    $method = $request->method();

    if ($request->isMethod('post')) {
        //
    }

[]()

### Cabeceras de la petición

Puede recuperar un encabezado de solicitud de la instancia `Illuminate\Http\Request` mediante el método `header`. Si el encabezado no está presente en la solicitud, se devolverá `null`. Sin embargo, el método `header` acepta un segundo argumento opcional que se devolverá si el encabezado no está presente en la solicitud:

    $value = $request->header('X-Header-Name');

    $value = $request->header('X-Header-Name', 'default');

El método `hasHeader` puede utilizarse para determinar si la petición contiene una cabecera determinada:

    if ($request->hasHeader('X-Header-Name')) {
        //
    }

Por conveniencia, el método `bearerToken` puede usarse para recuperar un token de portador de la cabecera `Authorization`. Si no existe tal cabecera, se devolverá una cadena vacía:

    $token = $request->bearerToken();

[]()

### Dirección IP de la petición

El método `ip` se puede utilizar para recuperar la dirección IP del cliente que hizo la petición a su aplicación:

    $ipAddress = $request->ip();

[]()

### Negociación del contenido

Laravel proporciona varios métodos para inspeccionar los tipos de contenido solicitados a través de la cabecera `Accept`. En primer lugar, el método `getAcceptableContentTypes` devolverá un array con todos los tipos de contenido aceptados por la petición:

    $contentTypes = $request->getAcceptableContentTypes();

El método `accepts` acepta un array de tipos de contenido y devuelve `true` si alguno de los tipos de contenido es aceptado por la petición. En caso contrario, devolverá `false`:

    if ($request->accepts(['text/html', 'application/json'])) {
        // ...
    }

Puede utilizar el método `prefers` para determinar qué tipo de contenido de un array dado es el más preferido por la petición. Si ninguno de los tipos de contenido proporcionados es aceptado por la petición, se devolverá `null`:

    $preferred = $request->prefers(['text/html', 'application/json']);

Dado que muchas aplicaciones sólo sirven HTML o JSON, puede utilizar el método `expectsJson` para determinar rápidamente si la petición entrante espera una respuesta JSON:

    if ($request->expectsJson()) {
        // ...
    }

[]()

### Solicitudes PSR-7

El [estándar PSR-7](https://www.php-fig.org/psr/psr-7/) especifica interfaces para mensajes HTTP, incluyendo peticiones y respuestas. Si deseas obtener una instancia de una solicitud PSR-7 en lugar de una solicitud Laravel, primero tendrás que instalar algunas bibliotecas. Laravel utiliza el componente *Symfony HTTP Message Bridge* para convertir las peticiones y respuestas típicas de Laravel en implementaciones compatibles con PSR-7:

```shell
composer require symfony/psr-http-message-bridge
composer require nyholm/psr7
```

Una vez que haya instalado estas librerías, puede obtener una petición PSR-7 indicando la interfaz de petición en su método de closure ruta o controlador:

    use Psr\Http\Message\ServerRequestInterface;

    Route::get('/', function (ServerRequestInterface $request) {
        //
    });

> **Nota**  
> Si devuelves una instancia de respuesta PSR-7 desde una ruta o controlador, se convertirá automáticamente en una instancia de respuesta Laravel y será mostrada por el framework.

[]()

## Entrada

[]()

### Recuperación de entradas

[]()

#### Recuperación de todos los datos de entrada

Puede recuperar todos los datos de entrada de la petición entrante como un `array` utilizando el método `all`. Este método puede utilizarse independientemente de si la petición entrante procede de un formulario HTML o es una petición XHR:

    $input = $request->all();

Usando el método `collect`, puedes recuperar todos los datos de entrada de la petición entrante como una [colección](/docs/%7B%7Bversion%7D%7D/collections):

    $input = $request->collect();

El método `collect` también le permite recuperar un subconjunto de los datos de entrada de la petición entrante como una colección:

    $request->collect('users')->each(function ($user) {
        // ...
    });

[]()

#### Recuperación de un valor de entrada

Utilizando unos pocos métodos simples, puede acceder a todos los datos de entrada del usuario desde su instancia `Illuminate\Http\Request` sin preocuparse de qué verbo HTTP se utilizó para la solicitud. Independientemente del verbo HTTP, el método `input` puede utilizarse para recuperar la entrada del usuario:

    $name = $request->input('name');

Puede pasar un valor por defecto como segundo argumento al método `input`. Este valor será devuelto si el valor solicitado no está presente en la petición:

    $name = $request->input('name', 'Sally');

Cuando trabaje con formularios que contengan array de entrada, utilice la notación "dot" para acceder a los arrays:

    $name = $request->input('products.0.name');

    $names = $request->input('products.*.name');

Puede llamar al método `input` sin ningún argumento para recuperar todos los valores de entrada como un array asociativo:

    $input = $request->input();

[]()

#### Recuperación de valores de la cadena de consulta

Mientras que el método `input` recupera valores de toda la carga útil de la petición (incluida la cadena de consulta), el método `query` sólo recupera valores de la cadena de consulta:

    $name = $request->query('name');

Si los datos de valor de la cadena de consulta solicitada no están presentes, se devolverá el segundo argumento de este método:

    $name = $request->query('name', 'Helen');

Puede llamar al método `query` sin ningún argumento para recuperar todos los valores de la cadena de consulta como un array asociativo:

    $query = $request->query();

[]()

#### Recuperación de valores de entrada JSON

Cuando envíe peticiones JSON a su aplicación, puede acceder a los datos JSON a través del método `input` siempre que la cabecera `Content-Type` de la petición esté correctamente configurada como `application/json`. Incluso puede utilizar la sintaxis "dot" para recuperar valores que están anidados dentro de matrices / objetos JSON:

    $name = $request->input('user.name');

[]()

#### Recuperación de valores de entrada Stringable

En lugar de recuperar los datos de entrada de la petición como una `cadena` primitiva, puede utilizar el método `string` para recuperar los datos de la petición como una instancia de [`Illuminate\Support\Stringable`](/docs/%7B%7Bversion%7D%7D/helpers#fluent-strings):

    $name = $request->string('name')->trim();

[]()

#### Obtención de valores de entrada booleanos

Al tratar con elementos HTML como casillas de verificación, su aplicación puede recibir valores "verdaderos" que en realidad son cadenas. Por ejemplo, "true" o "on". Para mayor comodidad, puede utilizar el método `boolean` para recuperar estos valores como booleanos. El método `booleano` devuelve `true` para 1, "1", true, "true", "on" y "yes". Todos los demás valores devolverán `false`:

    $archived = $request->boolean('archived');

[]()

#### Recuperación de valores de entrada de fecha

Para mayor comodidad, los valores de entrada que contienen fechas / horas pueden recuperarse como instancias de Carbon utilizando el método `date`. Si la solicitud no contiene un valor de entrada con el nombre indicado, se devolverá `null`:

    $birthday = $request->date('birthday');

El segundo y tercer argumento aceptados por el método `date` pueden utilizarse para especificar el formato de la fecha y la zona horaria, respectivamente:

    $elapsed = $request->date('elapsed', '!H:i', 'Europe/Madrid');

Si el valor de entrada está presente pero tiene un formato inválido, se lanzará una `InvalidArgumentException`; por lo tanto, se recomienda validar la entrada antes de invocar el método `date`.

[]()

#### Recuperación de Valores de Entrada Enum

Los valores de entrada que corresponden a [enums PHP](https://www.php.net/manual/en/language.types.enumerations.php) también pueden ser recuperados de la petición. Si la petición no contiene un valor de entrada con el nombre dado o el enum no tiene un valor de respaldo que coincida con el valor de entrada, se devolverá `null`. El método `enum` acepta el nombre del valor de entrada y la clase enum como primer y segundo argumento:

    use App\Enums\Status;

    $status = $request->enum('status', Status::class);

[]()

#### Recuperación de entradas mediante propiedades dinámicas

También puede acceder a la entrada del usuario utilizando propiedades dinámicas en la instancia `Illuminate\Http\Request`. Por ejemplo, si uno de los formularios de su aplicación contiene un campo de `nombre`, puede acceder al valor del campo así:

    $name = $request->name;

Cuando se utilizan propiedades dinámicas, Laravel buscará primero el valor del parámetro en la carga de la petición. Si no está presente, Laravel buscará el campo en los parámetros de la ruta correspondiente.

[]()

#### Recuperación de una parte de los datos de entrada

Si necesita recuperar un subconjunto de los datos de entrada, puede utilizar los métodos `only` y `except`. Ambos métodos aceptan un único `array` o una lista dinámica de argumentos:

    $input = $request->only(['username', 'password']);

    $input = $request->only('username', 'password');

    $input = $request->except(['credit_card']);

    $input = $request->except('credit_card');

> **Advertencia**  
> El método `only` devuelve todos los pares clave/valor solicitados; sin embargo, no devolverá pares clave/valor que no estén presentes en la solicitud.

[]()

### Determinación de la presencia de entradas

Puede utilizar el método `has` para determinar si un valor está presente en la petición. El método `has` devuelve `true` si el valor está presente en la petición:

    if ($request->has('name')) {
        //
    }

Cuando se le da un array, el método `has` determinará si todos los valores especificados están presentes:

    if ($request->has(['name', 'email'])) {
        //
    }

El método `whenHas` ejecutará el closure dado si un valor está presente en la petición:

    $request->whenHas('name', function ($input) {
        //
    });

Se puede pasar un segundo closure al método `whenHas` que se ejecutará si el valor especificado no está presente en la petición:

    $request->whenHas('name', function ($input) {
        // The "name" value is present...
    }, function () {
        // The "name" value is not present...
    });

El método `hasAny` devuelve `true` si alguno de los valores especificados está presente:

    if ($request->hasAny(['name', 'email'])) {
        //
    }

Si desea determinar si un valor está presente en la petición y no es una cadena vacía, puede utilizar el método `filled`:

    if ($request->filled('name')) {
        //
    }

El método `whenFilled` ejecutará el closure dado si un valor está presente en la petición y no es una cadena vacía:

    $request->whenFilled('name', function ($input) {
        //
    });

Se puede pasar un segundo closure al método `whenFilled` que se ejecutará si el valor especificado no está "lleno":

    $request->whenFilled('name', function ($input) {
        // The "name" value is filled...
    }, function () {
        // The "name" value is not filled...
    });

Para determinar si una clave dada está ausente en la petición, puede utilizar los métodos `missing` y `whenMissing`:

    if ($request->missing('name')) {
        //
    }

    $request->whenMissing('name', function ($input) {
        // The "name" value is missing...
    }, function () {
        // The "name" value is present...
    });

[]()

### Fusión de entrada adicional

A veces puede que necesite combinar manualmente entradas adicionales con los datos de entrada existentes en la petición. Para ello, puede utilizar el método `merge`. Si una determinada clave de entrada ya existe en la solicitud, será sobrescrita por los datos proporcionados al método `merge`:

    $request->merge(['votes' => 0]);

El método `mergeIfMissing` puede utilizarse para combinar entradas en la petición si las claves correspondientes no existen ya en los datos de entrada de la petición:

    $request->mergeIfMissing(['votes' => 0]);

[]()

### Entrada antigua

Laravel le permite mantener la entrada de una solicitud durante la siguiente solicitud. Esta característica es particularmente útil para rellenar formularios después de detectar errores de validación. Sin embargo, si estás utilizando las [características de validación](/docs/%7B%7Bversion%7D%7D/validation) incluidas en Laravel, es posible que no necesites utilizar manualmente estos métodos de destello de entrada de sesión directamente, ya que algunas de las facilidades de validación incorporadas en Laravel los llamarán automáticamente.

[]()

#### Introducir datos en la sesión

El método `flash` en la clase `Illuminate\Http\Request` flasheará la entrada actual a la [sesión](/docs/%7B%7Bversion%7D%7D/session) para que esté disponible durante la siguiente petición del usuario a la aplicación:

    $request->flash();

También puede utilizar los métodos `flashOnly` y `flashExcept` para enviar un subconjunto de los datos de la petición a la sesión. Estos métodos son útiles para mantener información sensible como contraseñas fuera de la sesión:

    $request->flashOnly(['username', 'email']);

    $request->flashExcept('password');

[]()

#### Flashear la entrada y luego redirigirla

Dado que a menudo querrá enviar datos a la sesión y luego redirigir a la página anterior, puede encadenar fácilmente el envío de datos a una redirección utilizando el método `withInput`:

    return redirect('form')->withInput();

    return redirect()->route('user.create')->withInput();

    return redirect('form')->withInput(
        $request->except('password')
    );

[]()

#### Recuperación de entradas antiguas

Para recuperar la entrada flasheada de la solicitud anterior, invoque el método `old` en una instancia de `Illuminate\Http\Request`. El método `old` extraerá los datos de entrada flasheados previamente de la [sesión](/docs/%7B%7Bversion%7D%7D/session):

    $username = $request->old('username');

Laravel también proporciona un ayudante global `old`. Si está mostrando entradas antiguas dentro de una [plantilla Blade](/docs/%7B%7Bversion%7D%7D/blade), es más conveniente utilizar el ayudante `antiguo` para repoblar el formulario. Si no existe ninguna entrada antigua para el campo dado, se devolverá `null`:

    <input type="text" name="username" value="{{ old('username') }}">

[]()

### Cookies

[]()

#### Recuperación de cookies de peticiones

Todas las cookies creadas por el framework Laravel están encriptadas y firmadas con un código de autenticación, lo que significa que se considerarán inválidas si han sido modificadas por el cliente. Para recuperar un valor de cookie de la solicitud, utilice el método `cookie` en una instancia `Illuminate\Http\Request`:

    $value = $request->cookie('name');

[]()

## Recorte y normalización de entradas

Por defecto, Laravel incluye el middleware `App\Http\middleware\TrimStrings` y el `middleware Illuminate\Foundation\Http\middleware\ConvertEmptyStringsToNull` en la pila de middleware global de su aplicación. Estos middleware son listados en la pila global de middleware por la clase `AppHttpKernel`. Estos middleware recortarán automáticamente todos los campos de cadena entrantes en la petición, así como convertirán cualquier campo de cadena vacío a `nulo`. Esto le permite no tener que preocuparse por estas cuestiones de normalización en sus rutas y controladores.

#### Deshabilitar la normalización de entradas

Si desea desactivar este comportamiento para todas las solicitudes, puede eliminar los dos middleware de la pila de middleware de su aplicación eliminándolos de la propiedad `$middleware` de su clase `App\Http\Kernel`.

Si desea desactivar el recorte de cadenas y la conversión de cadenas vacías para un subconjunto de peticiones a su aplicación, puede utilizar el método `skipWhen` ofrecido por ambos middleware. Este método acepta un closure que debe devolver `true` o `false` para indicar si la normalización de entrada debe ser omitida. Típicamente, el método `skipWhen` debería ser invocado en el método `boot` del `AppServiceProvider` de su aplicación.

```php
use App\Http\Middleware\TrimStrings;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;

/**
 * Bootstrap any application services.
 *
 * @return void
 */
public function boot()
{
    TrimStrings::skipWhen(function ($request) {
        return $request->is('admin/*');
    });

    ConvertEmptyStringsToNull::skipWhen(function ($request) {
        // ...
    });
}
```

[]()

## Archivos

[]()

### Recuperación de archivos cargados

Puede recuperar archivos cargados desde una instancia `Illuminate\Http\Request` utilizando el método `file` o utilizando propiedades dinámicas. El método `file` devuelve una instancia de la clase `Illuminate\Http\UploadedFile`, que extiende la clase PHP `SplFileInfo` y proporciona una variedad de métodos para interactuar con el archivo:

    $file = $request->file('photo');

    $file = $request->photo;

Puede determinar si un fichero está presente en la petición usando el método `hasFile`:

    if ($request->hasFile('photo')) {
        //
    }

[]()

#### Validando Subidas Exitosas

Además de comprobar si el archivo está presente, puede verificar que no hubo problemas al subir el archivo mediante el método `isValid`:

    if ($request->file('photo')->isValid()) {
        //
    }

[]()

#### Rutas y extensiones de archivos

La clase `UploadedFile` también contiene métodos para acceder a la ruta completa del archivo y a su extensión. El método de `extensión` intentará adivinar la extensión del archivo basándose en su contenido. Esta extensión puede ser diferente de la extensión proporcionada por el cliente:

    $path = $request->photo->path();

    $extension = $request->photo->extension();

[]()

#### Otros métodos de archivo

Hay una variedad de otros métodos disponibles en las instancias `UploadedFile`. Consulte la [documentación de la API de la clase](https://github.com/symfony/symfony/blob/6.0/src/Symfony/Component/HttpFoundation/File/UploadedFile.php) para obtener más información sobre estos métodos.

[]()

### Almacenamiento de archivos cargados

Para almacenar un archivo subido, normalmente utilizará uno de sus [sistemas de archivos](/docs/%7B%7Bversion%7D%7D/filesystem) configurados. La clase `UploadedFile` tiene un método `store` que moverá un archivo subido a uno de tus discos, que puede ser una ubicación en tu sistema de archivos local o una ubicación de almacenamiento en la nube como Amazon S3.

El método `store` acepta la ruta en la que debe almacenarse el archivo en relación con el directorio raíz configurado del sistema de archivos. Esta ruta no debe contener un nombre de archivo, ya que se generará automáticamente un ID único que servirá como nombre de archivo.

El método `store` también acepta un segundo argumento opcional para el nombre del disco que debe utilizarse para almacenar el archivo. El método devolverá la ruta del archivo relativa a la raíz del disco:

    $path = $request->photo->store('images');

    $path = $request->photo->store('images', 's3');

Si no desea que se genere automáticamente un nombre de archivo, puede utilizar el método `storeAs`, que acepta la ruta, el nombre del archivo y el nombre del disco como argumentos:

    $path = $request->photo->storeAs('images', 'filename.jpg');

    $path = $request->photo->storeAs('images', 'filename.jpg', 's3');

> **Nota**  
> Para más información sobre el almacenamiento de archivos en Laravel, consulta la [documentación](/docs/%7B%7Bversion%7D%7D/filesystem) completa sobre [almacenamiento de](/docs/%7B%7Bversion%7D%7D/filesystem) archivos.

[]()

## Configuración de proxies de confianza

Cuando ejecutas tus aplicaciones detrás de un balanceador de carga que termina certificados TLS / SSL, puedes notar que tu aplicación a veces no genera enlaces HTTPS cuando usas el ayudante `url`. Normalmente esto se debe a que su aplicación está siendo reenviada desde su balanceador de carga en el puerto 80 y no sabe que debe generar enlaces seguros.

Para resolver esto, puedes utilizar el middleware `App\Http\TrustProxies` que se incluye en tu aplicación Laravel, que te permite personalizar rápidamente los balanceadores de carga o proxies en los que debe confiar tu aplicación. Tus proxies de confianza deben ser listados como un array en la propiedad `$proxies` de este middleware. Además de configurar los proxies de confianza, puedes configurar `las $cabeceras de` proxy en las que se debe confiar:

    <?php

    namespace App\Http\Middleware;

    use Illuminate\Http\Middleware\TrustProxies as Middleware;
    use Illuminate\Http\Request;

    class TrustProxies extends Middleware
    {
        /**
         * The trusted proxies for this application.
         *
         * @var string|array
         */
        protected $proxies = [
            '192.168.1.1',
            '192.168.1.2',
        ];

        /**
         * The headers that should be used to detect proxies.
         *
         * @var int
         */
        protected $headers = Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO;
    }

> **Nota**  
> Si está utilizando AWS Elastic Load Balancing, su valor `$headers` debe ser `Request::HEADER_X_FORWARDED_AWS_ELB`. Para más información sobre las constantes que se pueden utilizar en la propiedad `$headers`, consulta la documentación de Symfony sobre [proxies de confianza](https://symfony.com/doc/current/deployment/proxies.html).

[]()

#### Confiar en todos los proxies

Si estás usando Amazon AWS u otro proveedor de balanceadores de carga en la "nube", puede que no conozcas las direcciones IP de tus balanceadores reales. En este caso, puede utilizar `*` para confiar en todos los proxies:

    /**
     * The trusted proxies for this application.
     *
     * @var string|array
     */
    protected $proxies = '*';

[]()

## Configuración de hosts de confianza

Por defecto, Laravel responderá a todas las peticiones que reciba independientemente del contenido de la cabecera `Host` de la petición HTTP. Además, el valor de la cabecera `Host` se utilizará al generar URLs absolutas a su aplicación durante una petición web.

Normalmente, debe configurar su servidor web, como Nginx o Apache, para que solo envíe solicitudes a su aplicación que coincidan con un nombre de host determinado. Sin embargo, si usted no tiene la capacidad de personalizar su servidor web directamente y necesita instruir a Laravel para que sólo responda a ciertos nombres de host, puede hacerlo habilitando el middleware `App\Http\TrustHosts` para su aplicación.

El middleware `TrustHosts` ya está incluido en la pila `$middleware` de tu aplicación; sin embargo, deberías descomentarla para que se active. Dentro del método `hosts` de este middleware, puede especificar los nombres de host a los que su aplicación debe responder. Las peticiones entrantes con otras cabeceras de valor `Host` serán rechazadas:

    /**
     * Get the host patterns that should be trusted.
     *
     * @return array
     */
    public function hosts()
    {
        return [
            'laravel.test',
            $this->allSubdomainsOfApplicationUrl(),
        ];
    }

El método `allSubdomainsOfApplicationUrl` devolverá una expresión regular que coincida con todos los subdominios del valor de configuración `app.url` de tu aplicación. Este método de ayuda proporciona una forma práctica de permitir todos los subdominios de la aplicación cuando se crea una aplicación que utiliza subdominios comodín.
