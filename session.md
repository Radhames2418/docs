# Sesión HTTP

- [Introducción](#introduction)
  - [Configuración](#configuration)
  - [Requisitos previos del controlador](#driver-prerequisites)
- [Interacción con la sesión](#interacting-with-the-session)
  - [Recuperación de datos](#retrieving-data)
  - [Almacenamiento de datos](#storing-data)
  - [Flasheo de Datos](#flash-data)
  - [Eliminación de datos](#deleting-data)
  - [Regeneración del ID de sesión](#regenerating-the-session-id)
- [Bloqueo de sesión](#session-blocking)
- [Adición de controladores de sesión personalizados](#adding-custom-session-drivers)
  - [Implementación del controlador](#implementing-the-driver)
  - [Registro del controlador](#registering-the-driver)

[]()

## Introducción

Dado que las aplicaciones HTTP no tienen estado, las sesiones proporcionan una forma de almacenar información sobre el usuario a través de múltiples peticiones. Esa información del usuario se coloca típicamente en un almacén persistente / backend al que se puede acceder desde peticiones posteriores.

Laravel incluye una variedad de backends de sesión a los que se accede a través de una API expresiva y unificada. Se incluye soporte para backends populares como [Memcached](https://memcached.org), [Redis](https://redis.io) y bases de datos.

[]()

### Configuración

El archivo de configuración de sesión de su aplicación se almacena en `config/session.php`. Asegúrese de revisar las opciones disponibles en este archivo. Por defecto, Laravel está configurado para utilizar el controlador de sesión de `archivos`, que funcionará bien para muchas aplicaciones. Si tu aplicación va a tener una carga balanceada a través de múltiples servidores web, deberías elegir un almacén centralizado al que todos los servidores puedan acceder, como Redis o una base de datos.

La opción de configuración `del controlador de` sesión define dónde se almacenarán los datos de sesión para cada petición. Laravel incluye varios controladores de sesión:

<div class="content-list" markdown="1"/>

- `file` - las sesiones se almacenan en `storage/framework/sessions`.
- `cookie` - las sesiones se almacenan en cookies seguras y encriptadas.
- `database` - las sesiones se almacenan en una base de datos relacional.
- `memcached` / `redis` - las sesiones se almacenan en uno de estos almacenes rápidos basados en cache.
- `dynamodb` - las sesiones se almacenan en AWS DynamoDB.
- `array` - las sesiones se almacenan en un array PHP y no se conservan.

[object Object]

> **Nota**  
> El controlador de array se utiliza principalmente durante las [pruebas](/docs/%7B%7Bversion%7D%7D/testing) y evita que los datos almacenados en la sesión sean persistidos.

[]()

### Requisitos previos del controlador

[]()

#### Base de datos

Cuando utilice el controlador de sesión `de base de datos`, necesitará crear una tabla para contener los registros de sesión. A continuación encontrará un ejemplo de declaración de `esquema` para la tabla:

    Schema::create('sessions', function ($table) {
        $table->string('id')->primary();
        $table->foreignId('user_id')->nullable()->index();
        $table->string('ip_address', 45)->nullable();
        $table->text('user_agent')->nullable();
        $table->text('payload');
        $table->integer('last_activity')->index();
    });

Puedes utilizar el comando Artisan `session:table` para generar esta migración. Para aprender más sobre migraciones de bases de datos, puedes consultar la [documentación](/docs/%7B%7Bversion%7D%7D/migrations) completa [de migración](/docs/%7B%7Bversion%7D%7D/migrations):

```shell
php artisan session:table

php artisan migrate
```

[]()

#### Redis

Antes de usar sesiones Redis con Laravel, necesitarás instalar la extensión PHP PhpRedis vía PECL o instalar el paquete `predis/predis` (\~1.0) vía Composer. Para más información sobre la configuración de Redis, consulta la [documentación de Laravel sobre Redis](/docs/%7B%7Bversion%7D%7D/redis#configuration).

> **Nota**  
> En el archivo de configuración de `sesión`, la opción de `conexión` puede ser usada para especificar qué conexión Redis es usada por la sesión.

[]()

## Interacción con la sesión

[]()

### Recuperación de datos

Hay dos formas principales de trabajar con datos de sesión en Laravel: el ayudante de `sesión` global y a través de una instancia `Request`. En primer lugar, vamos a ver cómo acceder a la sesión a través de una instancia `Request`, que puede ser de tipo-hinted en un closure ruta o método de controlador. Recuerda que las dependencias de los métodos del controlador se inyectan automáticamente a través del [contenedor de servicios](/docs/%7B%7Bversion%7D%7D/container) de Laravel:

    <?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    class UserController extends Controller
    {
        /**
         * Show the profile for the given user.
         *
         * @param  Request  $request
         * @param  int  $id
         * @return Response
         */
        public function show(Request $request, $id)
        {
            $value = $request->session()->get('key');

            //
        }
    }

Cuando se recupera un elemento de la sesión, también se puede pasar un valor por defecto como segundo argumento al método `get`. Este valor por defecto será devuelto si la clave especificada no existe en la sesión. Si pasa un closure como valor por defecto al método `get` y la clave solicitada no existe, se ejecutará el cierre y se devolverá su resultado:

    $value = $request->session()->get('key', 'default');

    $value = $request->session()->get('key', function () {
        return 'default';
    });

[]()

#### El Ayudante de Sesión Global

También puede utilizar la función PHP de `sesión` global para recuperar y almacenar datos en la sesión. Cuando el ayudante de `sesión` es llamado con un único argumento de cadena, devolverá el valor de esa clave de sesión. Cuando el ayudante es llamado con un array de pares clave / valor, esos valores serán almacenados en la sesión:

    Route::get('/home', function () {
        // Retrieve a piece of data from the session...
        $value = session('key');

        // Specifying a default value...
        $value = session('key', 'default');

        // Store a piece of data in the session...
        session(['key' => 'value']);
    });

> **Nota**  
> Hay poca diferencia práctica entre usar la sesión a través de una instancia de petición HTTP o usar el ayudante de `sesión` global. Ambos métodos son [comprobables](/docs/%7B%7Bversion%7D%7D/testing) mediante el método `assertSessionHas` que está disponible en todos tus casos de test.

[]()

#### Recuperación de todos los datos de la sesión

Si desea recuperar todos los datos de la sesión, puede utilizar el método `all`:

    $data = $request->session()->all();

[]()

#### Determinar si un elemento existe en la sesión

Para determinar si un elemento está presente en la sesión, puede utilizar el método `has`. El método `has` devuelve `verdadero` si el elemento está presente y no es `nulo`:

    if ($request->session()->has('users')) {
        //
    }

Para determinar si un elemento está presente en la sesión, incluso si su valor es `nulo`, puede utilizar el método `exists`:

    if ($request->session()->exists('users')) {
        //
    }

Para determinar si un elemento no está presente en la sesión, puede utilizar el método `missing`. El método `missing` devuelve `true` si el elemento no está presente:

    if ($request->session()->missing('users')) {
        //
    }

[]()

### Almacenamiento de datos

Para almacenar datos en la sesión, normalmente utilizará el método `put` de la instancia de petición o el ayudante de `sesión` global:

    // Via a request instance...
    $request->session()->put('key', 'value');

    // Via the global "session" helper...
    session(['key' => 'value']);

[array-session-values">]()

#### Empujar a array valores de sesión

El método `push` puede utilizarse para insertar un nuevo valor en un valor de sesión que sea un array. Por ejemplo, si la clave `user.teams` contiene un array de nombres de equipos, puede insertar un nuevo valor en el array de esta forma:

    $request->session()->push('user.teams', 'developers');

[]()

#### Recuperar y borrar un elemento

El método `pull` recuperará y borrará un elemento de la sesión en una única sentencia:

    $value = $request->session()->pull('key', 'default');

[]()

#### Incrementar y decrementar valores de sesión

Si sus datos de sesión contienen un entero que desea incrementar o decrementar, puede utilizar los métodos `increment` y `decrement`:

    $request->session()->increment('count');

    $request->session()->increment('count', $incrementBy = 2);

    $request->session()->decrement('count');

    $request->session()->decrement('count', $decrementBy = 2);

[]()

### Flashear Datos

A veces puede que desee almacenar elementos en la sesión para la siguiente petición. Puede hacerlo utilizando el método `flash`. Los datos almacenados en la sesión utilizando este método estarán disponibles inmediatamente y durante la siguiente petición HTTP. Después de la siguiente petición HTTP, los datos flasheados se borrarán. Los datos flash son útiles principalmente para mensajes de estado de corta duración:

    $request->session()->flash('status', 'Task was successful!');

Si necesita conservar los datos flash durante varias peticiones, puede utilizar el método `reflash`, que conservará todos los datos flash durante una petición adicional. Si sólo necesita conservar datos flash específicos, puede utilizar el método `keep`:

    $request->session()->reflash();

    $request->session()->keep(['username', 'email']);

Para conservar los datos flash sólo para la solicitud actual, puede utilizar el método `now`:

    $request->session()->now('status', 'Task was successful!');

[]()

### Eliminación de datos

El método `forget` eliminará una parte de los datos de la sesión. Si desea eliminar todos los datos de la sesión, puede utilizar el método `flush`:

    // Forget a single key...
    $request->session()->forget('name');

    // Forget multiple keys...
    $request->session()->forget(['name', 'status']);

    $request->session()->flush();

[]()

### Regeneración del ID de sesión

La regeneración del ID de sesión se hace a menudo para evitar que usuarios maliciosos exploten un ataque de [fijación de sesión](https://owasp.org/www-community/attacks/Session_fixation) en su aplicación.

Laravel regenera automáticamente el identificador de sesión durante la autenticación si estás utilizando uno de los [kits de inicio de aplicaciones](/docs/%7B%7Bversion%7D%7D/starter-kits) Laravel o [Laravel Fortify](/docs/%7B%7Bversion%7D%7D/fortify); sin embargo, si necesitas regenerar manualmente el identificador de sesión, puedes utilizar el método `regenerate`:

    $request->session()->regenerate();

Si necesitas regenerar el ID de sesión y eliminar todos los datos de la sesión en una sola sentencia, puedes usar el método `invalidate`:

    $request->session()->invalidate();

[]()

## Bloqueo de sesión

> **Advertencia**  
> Para utilizar el bloqueo de sesión, tu aplicación debe estar utilizando un controlador de cache que soporte [cache#atomic-locks">bloqueos atómicos](</docs/%7B%7Bversion%7D%7D/\<glossary variable=>). Actualmente, estos controladores de cache incluyen `memcached`, `dynamodb`, `redis` y controladores de `bases de datos`. Además, no puede utilizar el controlador de sesión `cookie`.

Por defecto, Laravel permite que las peticiones que utilizan la misma sesión se ejecuten concurrentemente. Así, por ejemplo, si utilizas una librería JavaScript HTTP para realizar dos peticiones HTTP a tu aplicación, ambas se ejecutarán al mismo tiempo. Para muchas aplicaciones, esto no es un problema; sin embargo, la pérdida de datos de sesión puede ocurrir en un pequeño subconjunto de aplicaciones que hacen peticiones concurrentes a dos puntos finales de aplicación diferentes que ambos escriben datos en la sesión.

Para mitigar esto, Laravel proporciona una funcionalidad que permite limitar las peticiones concurrentes para una sesión determinada. Para empezar, puedes simplemente encadenar el método `block` en tu definición de ruta. En este ejemplo, una petición entrante al endpoint `/profile` adquiriría un bloqueo de sesión. Mientras se mantiene este bloqueo, cualquier petición entrante a los puntos finales `/profile` o `/order` que compartan el mismo ID de sesión esperarán a que la primera petición termine de ejecutarse antes de continuar su ejecución:

    Route::post('/profile', function () {
        //
    })->block($lockSeconds = 10, $waitSeconds = 10)

    Route::post('/order', function () {
        //
    })->block($lockSeconds = 10, $waitSeconds = 10)

El método `block` acepta dos argumentos opcionales. El primer argumento aceptado por el método `block` es el número máximo de segundos que debe mantenerse el bloqueo de sesión antes de ser liberado. Por supuesto, si la petición termina de ejecutarse antes de este tiempo, el bloqueo se liberará antes.

El segundo argumento aceptado por el método `block` es el número de segundos que una petición debe esperar mientras intenta obtener un bloqueo de sesión. Se lanzará una `excepción Illuminate\Contracts\cache\LockTimeoutException` si la petición no puede obtener un bloqueo de sesión dentro del número de segundos dado.

Si no se pasa ninguno de estos argumentos, el bloqueo se obtendrá durante un máximo de 10 segundos y las peticiones esperarán un máximo de 10 segundos mientras intentan obtener un bloqueo:

    Route::post('/profile', function () {
        //
    })->block()

[]()

## Adición de controladores de sesión personalizados

[]()

#### Implementación del controlador

Si ninguno de los controladores de sesión existentes se ajusta a las necesidades de su aplicación, Laravel hace posible escribir su propio controlador de sesión. Su controlador de sesión personalizado debe implementar la interfaz integrada `SessionHandlerInterface` de PHP. Esta interfaz contiene sólo unos pocos métodos simples. Una implementación stubbed de MongoDB se parece a la siguiente:

    <?php

    namespace App\Extensions;

    class MongoSessionHandler implements \SessionHandlerInterface
    {
        public function open($savePath, $sessionName) {}
        public function close() {}
        public function read($sessionId) {}
        public function write($sessionId, $data) {}
        public function destroy($sessionId) {}
        public function gc($lifetime) {}
    }

> **Nota**  
> Laravel no viene con un directorio para contener tus extensiones. Eres libre de colocarlas donde quieras. En este ejemplo, hemos creado un directorio `Extensions` para alojar el `MongoSessionHandler`.

Dado que el propósito de estos métodos no es fácilmente comprensible, vamos a cubrir rápidamente lo que hace cada uno de los métodos:

<div class="content-list" markdown="1"/>

- El método `open` se usaría típicamente en sistemas de almacenamiento de sesiones basados en ficheros. Dado que Laravel viene con un controlador de sesión de `archivos`, rara vez necesitarás poner algo en este método. Puedes simplemente dejar este método vacío.
- El método `close`, al igual que el método `open`, también puede ser ignorado. Para la mayoría de los controladores, no es necesario.
- El método `read` debe devolver la versión string de los datos de sesión asociados con el `$sessionId` dado. No hay necesidad de hacer ninguna serialización u otra codificación al recuperar o almacenar datos de sesión en el controlador, ya que Laravel realizará la serialización por ti.
- El método `write` debe escribir la cadena `$data` asociada al `$sessionId` en algún sistema de almacenamiento persistente, como MongoDB u otro sistema de almacenamiento de tu elección. Una vez más, no debes realizar ninguna serialización - Laravel ya habrá manejado eso por ti.
- El método `destroy` debería eliminar los datos asociados con `$sessionId` del almacenamiento persistente.
- El método `gc` debe destruir todos los datos de sesión que sean más antiguos que `$lifetime`, que es una marca de tiempo UNIX. Para sistemas de auto-expiración como Memcached y Redis, este método puede dejarse vacío.

[object Object]

[]()

#### Registro del controlador

Una vez que su controlador ha sido implementado, está listo para registrarlo con Laravel. Para añadir controladores adicionales al backend de sesión de Laravel, puedes utilizar el método `extend` proporcionado por la [facade](/docs/%7B%7Bversion%7D%7D/facades) `Session`. Deberás llamar al método `extend` desde el método `boot` de un [proveedor de servicios](/docs/%7B%7Bversion%7D%7D/providers). Puedes hacerlo desde el `AppProviders\AppServiceProvider` existente o crear un proveedor completamente nuevo:

    <?php

    namespace App\Providers;

    use App\Extensions\MongoSessionHandler;
    use Illuminate\Support\Facades\Session;
    use Illuminate\Support\ServiceProvider;

    class SessionServiceProvider extends ServiceProvider
    {
        /**
         * Register any application services.
         *
         * @return void
         */
        public function register()
        {
            //
        }

        /**
         * Bootstrap any application services.
         *
         * @return void
         */
        public function boot()
        {
            Session::extend('mongo', function ($app) {
                // Return an implementation of SessionHandlerInterface...
                return new MongoSessionHandler;
            });
        }
    }

Una vez que el controlador de sesión ha sido registrado, puede usar el controlador `mongo` en su archivo de configuración `config/session.` php.
