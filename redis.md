# Redis

- [Introducción](#introduction)
- [Configuración](#configuration)
  - [Agrupaciones](#clusters)
  - [Predis](#predis)
  - [phpredis](#phpredis)
- [Interacción con Redis](#interacting-with-redis)
  - [Transacciones](#transactions)
  - [Comandos Pipelining](#pipelining-commands)
- [Pub / Sub](#pubsub)

[]()

## Introducción

[Redis](https://redis.io) es un almacén clave-valor avanzado de código abierto. A menudo se le conoce como un servidor de estructuras de datos ya que las claves pueden contener [cadenas](https://redis.io/topics/data-types#strings), [hashes](https://redis.io/topics/data-types#hashes), [listas](https://redis.io/topics/data-types#lists), [conjuntos](https://redis.io/topics/data-types#sets) y [conjuntos ordenados](https://redis.io/topics/data-types#sorted-sets).

Antes de utilizar Redis con Laravel, le recomendamos que instale y utilice la extensión PHP [phpredis](https://github.com/phpredis/phpredis) a través de PECL. La extensión es más compleja de instalar en comparación con los paquetes PHP "de usuario", pero puede ofrecer un mejor rendimiento para las aplicaciones que hacen un uso intensivo de Redis. Si estás usando [Laravel Sail](/docs/%7B%7Bversion%7D%7D/sail), esta extensión ya está instalada en el contenedor Docker de tu aplicación.

Si no puede instalar la extensión phpredis, puede instalar el paquete `predis/predis` a través de Composer. Predis es un cliente Redis escrito completamente en PHP y no requiere ninguna extensión adicional:

```shell
composer require predis/predis
```

[]()

## Configuración

Puede configurar los ajustes de Redis de su aplicación a través del archivo de configuración `config/database.php`. Dentro de este archivo, verá un array `redis` que contiene los servidores Redis utilizados por su aplicación:

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DB', 0),
        ],

        'cache' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_CACHE_DB', 1),
        ],

    ],

Cada servidor Redis definido en su archivo de configuración debe tener un nombre, un host y un puerto, a menos que defina una única URL para representar la conexión Redis:

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'default' => [
            'url' => 'tcp://127.0.0.1:6379?database=0',
        ],

        'cache' => [
            'url' => 'tls://user:password@127.0.0.1:6380?database=1',
        ],

    ],

[]()

#### Configuración del esquema de conexión

Por defecto, los clientes Redis utilizarán el esquema `tcp` cuando se conecten a tus servidores Redis; sin embargo, puedes utilizar el cifrado TLS / SSL especificando una opción de configuración de `esquema` en la array configuración de tu servidor Redis:

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'default' => [
            'scheme' => 'tls',
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DB', 0),
        ],

    ],

[]()

### Clusters

Si tu aplicación está utilizando un cluster de servidores Redis, debes definir estos clusters dentro de una clave de `clusters` de tu configuración Redis. Esta clave de configuración no existe por defecto por lo que deberás crearla dentro del archivo de configuración `config/database.php` de tu aplicación:

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'clusters' => [
            'default' => [
                [
                    'host' => env('REDIS_HOST', 'localhost'),
                    'password' => env('REDIS_PASSWORD'),
                    'port' => env('REDIS_PORT', 6379),
                    'database' => 0,
                ],
            ],
        ],

    ],

Por defecto, los clusters realizarán la fragmentación del lado del cliente a través de tus nodos, permitiéndote agrupar nodos y crear una gran cantidad de RAM disponible. Sin embargo, la fragmentación del lado del cliente no gestiona la conmutación por error; por lo tanto, es adecuada principalmente para datos en caché transitorios que están disponibles desde otro almacén de datos primario.

Si desea utilizar la agrupación nativa de Redis en lugar de la fragmentación del lado del cliente, puede especificarlo estableciendo el valor de configuración `options.cluster` en `redis` dentro del archivo de configuración `config/database.` php de su aplicación:

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
        ],

        'clusters' => [
            // ...
        ],

    ],

[]()

### Predis

Si desea que su aplicación interactúe con Redis a través del paquete Predis, debe asegurarse de que el valor de la variable de entorno `REDIS_CLIENT` sea `predis`:

    'redis' => [

        'client' => env('REDIS_CLIENT', 'predis'),

        // ...
    ],

Además de las opciones de configuración predeterminadas de `host`, `puerto`, `base de datos` y servidor de `contraseñas`, Predis admite [parámetros de conexión](https://github.com/nrk/predis/wiki/Connection-Parameters) adicionales que se pueden definir para cada uno de sus servidores Redis. Para utilizar estas opciones de configuración adicionales, añádalas a la configuración de su servidor Redis en el archivo de configuración `config/database.php` de su aplicación:

    'default' => [
        'host' => env('REDIS_HOST', 'localhost'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', 6379),
        'database' => 0,
        'read_write_timeout' => 60,
    ],

[facade-alias">]()

#### El alias de facade Redis

El fichero de configuración `config/app.php` de Laravel contiene un array `aliases` que define todos los alias de clase que serán registrados por el framework. Por defecto, no se incluye ningún alias de `Redis` porque entraría en conflicto con el nombre de la clase `Redis` proporcionado por la extensión phpredis. Si está utilizando el cliente Predis y desea añadir un alias de `Redis`, puede añadirlo al array `aliases` en el fichero de configuración `config/app.php` de su aplicación:

    'aliases' => Facade::defaultAliases()->merge([
        'Redis' => Illuminate\Support\Facades\Redis::class,
    ])->toArray(),

[]()

### phpredis

Por defecto, Laravel utilizará la extensión phpredis para comunicarse con Redis. El cliente que Laravel utilizará para comunicarse con Redis está dictado por el valor de la opción de configuración `redis.client`, que normalmente refleja el valor de la variable de entorno `REDIS_CLIENT`:

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        // Rest of Redis configuration...
    ],

Además de las opciones predeterminadas de configuración de `esquema`, `host`, `puerto`, `base de datos` y servidor de `contraseñas`, phpredis soporta los siguientes parámetros adicionales de conexión: `name`, `persistent`, `persistent_id`, `prefix`, `read_timeout`, `retry_interval`, `timeout` y `context`. Puede agregar cualquiera de estas opciones a la configuración de su servidor Redis en el archivo de configuración `config/database.php`:

    'default' => [
        'host' => env('REDIS_HOST', 'localhost'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', 6379),
        'database' => 0,
        'read_timeout' => 60,
        'context' => [
            // 'auth' => ['username', 'secret'],
            // 'stream' => ['verify_peer' => false],
        ],
    ],

[]()

#### Serialización y compresión phpredis

La extensión phpredis también puede ser configurada para usar una variedad de algoritmos de serialización y compresión. Estos algoritmos pueden ser configurados a través del array `opciones` de su configuración de Redis:

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'serializer' => Redis::SERIALIZER_MSGPACK,
            'compression' => Redis::COMPRESSION_LZ4,
        ],

        // Rest of Redis configuration...
    ],

Los algoritmos de serialización actualmente soportados incluyen: Redis:: `SERIALIZER_NONE` (por defecto), `Redis::SERIALIZER_PHP`, `Redis::SERIALIZER_JSON`, `Redis::SERIALIZER_IGBINARY`, y `Redis::SERIALIZER_MSGPACK`.

Los algoritmos de compresión soportados son: Redis:: `COMPRESSION_NONE` (por defecto), `Redis::COMPRESSION_LZF`, `Redis::COMPRESSION_ZSTD`, y `Redis::COMPRESSION_LZ4`.

[]()

## Interactuando con Redis

Puede interactuar con Redis llamando a varios métodos de la [facade](/docs/%7B%7Bversion%7D%7D/facades) `Redis`. La facade de `Redis` soporta métodos dinámicos, lo que significa que puedes llamar a cualquier [comando de Redis](https://redis.io/commands) en la facade y el comando será pasado directamente a Redis. En este ejemplo, llamaremos al comando `GET` de Redis llamando al método `get` de la fachada facade `Redis`:

    <?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;
    use Illuminate\Support\Facades\Redis;

    class UserController extends Controller
    {
        /**
         * Show the profile for the given user.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
        public function show($id)
        {
            return view('user.profile', [
                'user' => Redis::get('user:profile:'.$id)
            ]);
        }
    }

Como se mencionó anteriormente, puedes llamar a cualquiera de los comandos de Redis en la facade de `Redis`. Laravel utiliza métodos mágicos para pasar los comandos al servidor Redis. Si un comando Redis espera argumentos, debes pasarlos al método correspondiente de facade fachada:

    use Illuminate\Support\Facades\Redis;

    Redis::set('name', 'Taylor');

    $values = Redis::lrange('names', 5, 10);

Alternativamente, puedes pasar comandos al servidor usando el método `command` de la facade `Redis`, que acepta el nombre del comando como primer argumento y un array de valores como segundo argumento:

    $values = Redis::command('lrange', ['name', 5, 10]);

[]()

#### Uso de múltiples conexiones Redis

El archivo de configuración `config/database.php` de tu aplicación te permite definir múltiples conexiones / servidores Redis. Puedes obtener una conexión a una conexión Redis específica usando el método `connection` de la facade `Redis`:

    $redis = Redis::connection('connection-name');

Para obtener una instancia de la conexión Redis por defecto, puede llamar al método `connection` sin argumentos adicionales:

    $redis = Redis::connection();

[]()

### Transacciones

El método `transaction` de facade fachada de `Redis` proporciona una envoltura conveniente alrededor de los comandos nativos `MULTI` y `EXEC` de Redis. El método `transaction` acepta un closure como único argumento. Este closure recibirá una instancia de conexión Redis y podrá emitir cualquier comando que desee a esta instancia. Todos los comandos de Redis emitidos dentro del closure se ejecutarán en una única transacción atómica:

    use Illuminate\Support\Facades\Redis;

    Redis::transaction(function ($redis) {
        $redis->incr('user_visits', 1);
        $redis->incr('total_visits', 1);
    });

> **Advertencia**  
> Cuando definas una transacción Redis, no puedes recuperar ningún valor de la conexión Redis. Recuerda, tu transacción se ejecuta como una única operación atómica y esa operación no se ejecuta hasta que todo tu closure haya terminado de ejecutar sus comandos.

#### Scripts Lua

El método `eval` proporciona otro método para ejecutar múltiples comandos Redis en una única operación atómica. Sin embargo, el método `eval` tiene el beneficio de poder interactuar e inspeccionar los valores de las claves de Redis durante esa operación. Los scripts de Redis están escritos en el [lenguaje de programación Lua](https://www.lua.org).

El método `eval` puede asustar un poco al principio, pero exploraremos un ejemplo básico para romper el hielo. El método `eval` espera varios argumentos. En primer lugar, debe pasar el script Lua (como una cadena) al método. En segundo lugar, debe pasar el número de claves (como un entero) con las que interactúa el script. En tercer lugar, debe pasar los nombres de esas claves. Por último, puedes pasar cualquier otro argumento adicional al que necesites acceder dentro de tu script.

En este ejemplo, incrementaremos un contador, inspeccionaremos su nuevo valor, e incrementaremos un segundo contador si el valor del primer contador es mayor que cinco. Finalmente, devolveremos el valor del primer contador:

    $value = Redis::eval(<<<'LUA'
        local counter = redis.call("incr", KEYS[1])

        if counter > 5 then
            redis.call("incr", KEYS[2])
        end

        return counter
    LUA, 2, 'first-counter', 'second-counter');

> **Advertencia**  
> Por favor consulta la [documentación de Redis](https://redis.io/commands/eval) para más información sobre scripts Redis.

[]()

### Comandos Pipelining

A veces puede que necesites ejecutar docenas de comandos Redis. En lugar de hacer un viaje de red a tu servidor Redis para cada comando, puedes utilizar el método `pipeline`. El método `pipeline` acepta un argumento: un closure que recibe una instancia de Redis. Puedes enviar todos tus comandos a esta instancia de Redis y todos serán enviados al servidor Redis al mismo tiempo para reducir los viajes de red al servidor. Los comandos serán ejecutados en el orden en que fueron emitidos:

    use Illuminate\Support\Facades\Redis;

    Redis::pipeline(function ($pipe) {
        for ($i = 0; $i < 1000; $i++) {
            $pipe->set("key:$i", $i);
        }
    });

[]()

## Pub / Sub

Laravel proporciona una cómoda interfaz para los comandos `publish` y `subscribe` de Redis. Estos comandos de Redis te permiten escuchar mensajes en un "canal" determinado. Puedes publicar mensajes en el canal desde otra aplicación, o incluso utilizando otro lenguaje de programación, permitiendo una fácil comunicación entre aplicaciones y procesos.

En primer lugar, vamos a configurar un canal de escucha utilizando el método `subscribe`. Colocaremos esta llamada al método dentro de un [comando Artisan](/docs/%7B%7Bversion%7D%7D/artisan) ya que al llamar al método `subscribe` se inicia un proceso de larga duración:

    <?php

    namespace App\Console\Commands;

    use Illuminate\Console\Command;
    use Illuminate\Support\Facades\Redis;

    class RedisSubscribe extends Command
    {
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'redis:subscribe';

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Subscribe to a Redis channel';

        /**
         * Execute the console command.
         *
         * @return mixed
         */
        public function handle()
        {
            Redis::subscribe(['test-channel'], function ($message) {
                echo $message;
            });
        }
    }

Ahora podemos publicar mensajes al canal usando el método `publish`:

    use Illuminate\Support\Facades\Redis;

    Route::get('/publish', function () {
        // ...

        Redis::publish('test-channel', json_encode([
            'name' => 'Adam Wathan'
        ]));
    });

[]()

#### Suscripciones comodín

Usando el método `psubscribe`, puede suscribirse a un canal comodín, que puede ser útil para capturar todos los mensajes en todos los canales. El nombre del canal se pasará como segundo argumento al closure proporcionado:

    Redis::psubscribe(['*'], function ($message, $channel) {
        echo $message;
    });

    Redis::psubscribe(['users.*'], function ($message, $channel) {
        echo $message;
    });
