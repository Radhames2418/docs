# Registro

- [Introducción](#introduction)
- [Configuración](#configuration)
  - [Controladores de canal disponibles](#available-channel-drivers)
  - [Requisitos previos del canal](#channel-prerequisites)
  - [Registro de avisos de caducidad](#logging-deprecation-warnings)
- [Creación de pilas de registros](#building-log-stacks)
- [Escritura de mensajes de registro](#writing-log-messages)
  - [Información contextual](#contextual-information)
  - [Escritura en canales específicos](#writing-to-specific-channels)
- [Personalización de canales Monolog](#monolog-channel-customization)
  - [Personalización de canales Monolog](#customizing-monolog-for-channels)
  - [Creación de Canales Monolog Handler](#creating-monolog-handler-channels)
  - [Creación de canales personalizados a través de fábricas](#creating-custom-channels-via-factories)

[]()

## Introducción

Para ayudarle a aprender más acerca de lo que está sucediendo dentro de su aplicación, Laravel proporciona servicios de registro robustos que le permiten registrar mensajes en archivos, el registro de errores del sistema, e incluso a Slack para notificar a todo su equipo.

Laravel logging se basa en "canales". Cada canal representa una forma específica de escribir información de registro. Por ejemplo, el canal `único` escribe archivos de registro en un único archivo de registro, mientras que el canal `slack` envía mensajes de registro a Slack. Los mensajes de registro se pueden escribir en varios canales en función de su gravedad.

Bajo el capó, Laravel utiliza la biblioteca [Monolog](https://github.com/Seldaek/monolog), que proporciona soporte para una variedad de potentes gestores de registro. Laravel hace que sea un juego de niños para configurar estos controladores, lo que le permite mezclar y combinar para personalizar el manejo de registro de su aplicación.

[]()

## Configuración

Todas las opciones de configuración para el comportamiento de registro de su aplicación se encuentra en el archivo de configuración `config/logging.php`. Este archivo le permite configurar los canales de registro de su aplicación, así que asegúrese de revisar cada uno de los canales disponibles y sus opciones. Revisaremos algunas opciones comunes a continuación.

Por defecto, Laravel utilizará el canal de `pila` cuando registre mensajes. El canal de `pila` se utiliza para agregar varios canales de registro en un solo canal. Para obtener más información sobre la construcción de pilas, echa un vistazo a la [documentación a continuación](#building-log-stacks).

[]()

#### Configuración del nombre del canal

Por defecto, Monolog es instanciado con un "nombre de canal" que coincide con el entorno actual, como `producción` o `local`. Para cambiar este valor, añada una opción de `nombre` a la configuración de su canal:

    'stack' => [
        'driver' => 'stack',
        'name' => 'channel-name',
        'channels' => ['single', 'slack'],
    ],

[]()

### Controladores de canal disponibles

Cada canal de registro es alimentado por un "controlador". El controlador determina cómo y dónde se registra realmente el mensaje de registro. Los siguientes controladores de canal de registro están disponibles en cada aplicación Laravel. Una entrada para la mayoría de estos controladores ya está presente en el archivo de configuración `config/logging.php` de su aplicación, así que asegúrese de revisar este archivo para familiarizarse con su contenido:

|Nombre      |Descripción                                                                                  |
|------------|---------------------------------------------------------------------------------------------|
|`custom`    |Un controlador que llama a una fábrica especificada para crear un canal                      |
|`daily`     |A `RotatingFileHandler` basado en Monolog que rota diariamente                               |
|`errorlog`  |Un `ErrorLogHandler` conductor Monolog basado                                                |
|`monolog`   |Un controlador de fábrica Monolog que puede utilizar cualquier controlador Monolog compatible|
|`null`      |Un controlador que descarta todos los mensajes de registro                                   |
|`papertrail`|A `SyslogUdpHandler` conductor Monolog basado                                                |
|`single`    |Un canal de registro basado en un único archivo o ruta (`StreamHandler`)                     |
|`slack`     |A `SlackWebhookHandler` conductor Monolog basado                                             |
|`stack`     |Un wrapper para facilitar la creación de canales "multicanal                                 |
|`syslog`    |A `SyslogHandler` controlador Monolog basado                                                 |

> **Nota**  
> Consulta la documentación sobre [personalización avanzada de canales](#monolog-channel-customization) para saber más sobre el `monolog` y los controladores `personalizados`.

[]()

### Requisitos previos del canal

[]()

#### Configuración de los canales único y diario

Los canales `único` y `diario` tienen tres opciones de configuración opcionales: `burbuja`, `permiso` y `bloqueo`.

|Nombre      |Descripción                                                                          |Por defecto|
|------------|-------------------------------------------------------------------------------------|-----------|
|`bubble`    |Indica si los mensajes deben burbujearse hacia otros canales después de ser manejados|`true`     |
|`locking`   |Intento de bloquear el archivo de registro antes de escribir en él                   |`false`    |
|`permission`|Los permisos del archivo de registro                                                 |`0644`     |

Además, la policy retención del canal `diario` puede configurarse mediante la opción `días`:

|Nombre|Descripción                                                             |Por defecto|
|------|------------------------------------------------------------------------|-----------|
|`days`|El número de días que deben conservarse los archivos de registro diarios|`7`        |

[]()

#### Configuración del canal Papertrail

El canal `papertrail` requiere las opciones de configuración de `host` y `puerto`. Puedes obtener estos valores de [Papertrail](https://help.papertrailapp.com/kb/configuration/configuring-centralized-logging-from-php-apps/#send-events-from-php-app).

[]()

#### Configuración del canal Slack

El canal `slack` requiere una opción de configuración `url`. Esta URL debe coincidir con una URL para un [webhook entrante](https://slack.com/apps/A0F7XDUAZ-incoming-webhooks) que haya configurado para su equipo de Slack.

Por defecto, Slack sólo recibirá registros en el nivel `crítico` y superior; sin embargo, puede ajustar esto en su archivo de configuración `config/logging.` php modificando la opción de configuración de `nivel` dentro de la array configuración de su canal de registro de Slack.

[]()

### Advertencias de obsoleto del registro

PHP, Laravel, y otras librerías a menudo notifican a sus usuarios que algunas de sus características han sido obsoletas y serán eliminadas en una versión futura. Si deseas registrar estas advertencias de desaprobación, puedes especificar tu canal de registro de `desaprobaciones` preferido en el archivo de configuración `config/logging.php` de tu aplicación:

    'deprecations' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),

    'channels' => [
        ...
    ]

O puede definir un canal de registro llamado `"deprecations"`. Si existe un canal de registro con este nombre, siempre se utilizará para registrar las deprecaciones:

    'channels' => [
        'deprecations' => [
            'driver' => 'single',
            'path' => storage_path('logs/php-deprecation-warnings.log'),
        ],
    ],

[]()

## Creación de pilas de registros

Como se mencionó anteriormente, el controlador de `pila` permite combinar varios canales en un único canal de registro para mayor comodidad. Para ilustrar cómo utilizar las pilas de registros, veamos un ejemplo de configuración que podría verse en una aplicación de producción:

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['syslog', 'slack'],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],
    ],

Vamos a diseccionar esta configuración. En primer lugar, observe que nuestro canal `stack` agrega otros dos canales a través de su opción `channels`: `syslog` y `slack`. Por lo tanto, al registrar mensajes, ambos canales tendrán la oportunidad de registrar el mensaje. Sin embargo, como veremos más adelante, si estos canales realmente registran el mensaje puede estar determinado por la gravedad / "nivel" del mensaje.

[]()

#### Niveles de registro

Tenga en cuenta la opción de configuración de `nivel` presente en las configuraciones de los canales `syslog` y `slack` en el ejemplo anterior. Esta opción determina el "nivel" mínimo que debe tener un mensaje para ser registrado por el canal. Monolog, que alimenta los servicios de registro de Laravel, ofrece todos los niveles de registro definidos en la [especificación RFC 5424](https://tools.ietf.org/html/rfc5424). En orden descendente de gravedad, estos niveles de registro son: **emergencia**, **alerta**, **crítico**, **error**, **advertencia**, **aviso**, **información** y **depuración**.

Imaginemos que registramos un mensaje utilizando el método `de depuración`:

    Log::debug('An informational message.');

Dada nuestra configuración, el canal `syslog` escribirá el mensaje en el registro del sistema; sin embargo, dado que el mensaje de error no es `crítico` o superior, no se enviará a Slack. Sin embargo, si registramos un mensaje de `emergencia`, se enviará tanto al registro del sistema como a Slack, ya que el nivel de `emergencia` está por encima de nuestro umbral de nivel mínimo para ambos canales:

    Log::emergency('The system is down!');

[]()

## Escritura de mensajes de registro

Puede escribir información en los registros utilizando la [facade](/docs/%7B%7Bversion%7D%7D/facades) `Log`. Como se mencionó anteriormente, el registrador proporciona los ocho niveles de registro definidos en la [especificación RFC 5424](https://tools.ietf.org/html/rfc5424): **emergencia**, **alerta**, **crítico**, **error**, **advertencia**, **aviso**, **información** y **depuración**:

    use Illuminate\Support\Facades\Log;

    Log::emergency($message);
    Log::alert($message);
    Log::critical($message);
    Log::error($message);
    Log::warning($message);
    Log::notice($message);
    Log::info($message);
    Log::debug($message);

Puede llamar a cualquiera de estos métodos para registrar un mensaje para el nivel correspondiente. Por defecto, el mensaje se escribirá en el canal de registro predeterminado configurado en el archivo de configuración de `registro`:

    <?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;
    use App\Models\User;
    use Illuminate\Support\Facades\Log;

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
            Log::info('Showing the user profile for user: '.$id);

            return view('user.profile', [
                'user' => User::findOrFail($id)
            ]);
        }
    }

[]()

### Información contextual

Se puede pasar un array de datos contextuales a los métodos de registro. Estos datos contextuales serán formateados y mostrados con el mensaje de registro:

    use Illuminate\Support\Facades\Log;

    Log::info('User failed to login.', ['id' => $user->id]);

Ocasionalmente, puede que desee especificar alguna información contextual que debería incluirse con todas las entradas de registro subsiguientes en un canal en particular. Por ejemplo, puede que desee registrar un ID de solicitud que esté asociado con cada solicitud entrante a su aplicación. Para ello, puede llamar al método `withContext` de la facade `Log`:

    <?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Str;

    class AssignRequestId
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
            $requestId = (string) Str::uuid();

            Log::withContext([
                'request-id' => $requestId
            ]);

            return $next($request)->header('Request-Id', $requestId);
        }
    }

Si desea compartir información contextual a través de _todos los_ canales de registro, puede llamar al método `Log::shareContext()`. Este método proporcionará la información contextual a todos los canales creados y a cualquier canal que se cree posteriormente. Normalmente, el método `shareContext` debería llamarse desde el método de `arranque` de un proveedor de servicios de aplicación:

    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Str;

    class AppServiceProvider
    {
        /**
         * Bootstrap any application services.
         *
         * @return void
         */
        public function boot()
        {
            Log::shareContext([
                'invocation-id' => (string) Str::uuid(),
            ]);
        }
    }

[]()

### Escritura en canales específicos

A veces puede que desees registrar un mensaje en un canal distinto al canal por defecto de tu aplicación. Puede utilizar el método `channel` de la facade `Log` para recuperar y registrar cualquier canal definido en su archivo de configuración:

    use Illuminate\Support\Facades\Log;

    Log::channel('slack')->info('Something happened!');

Si desea crear una pila de registro bajo demanda formada por varios canales, puede utilizar el método `stack`:

    Log::stack(['single', 'slack'])->info('Something happened!');

[]()

#### Canales a petición

También es posible crear un canal bajo demanda proporcionando la configuración en tiempo de ejecución sin que dicha configuración esté presente en el fichero de configuración de `registro` de tu aplicación. Para ello, puedes pasar un array configuración al método de `construcción` de la facade `Log`:

    use Illuminate\Support\Facades\Log;

    Log::build([
      'driver' => 'single',
      'path' => storage_path('logs/custom.log'),
    ])->info('Something happened!');

También puedes incluir un canal bajo demanda en una pila de registro bajo demanda. Esto se puede conseguir incluyendo tu instancia de canal bajo demanda en el array pasado al método de `pila`:

    use Illuminate\Support\Facades\Log;

    $channel = Log::build([
      'driver' => 'single',
      'path' => storage_path('logs/custom.log'),
    ]);

    Log::stack(['slack', $channel])->info('Something happened!');

[]()

## Personalización de canales Monolog

[]()

### Personalización de canales Monolog

A veces es posible que necesite un control total sobre cómo Monolog está configurado para un canal existente. Por ejemplo, es posible que desee configurar una implementación personalizada de Monolog `FormatterInterface` para el canal `único` incorporado de Laravel.

Para empezar, define un `tap` array en la configuración del canal. El `tap` array debe contener una lista de clases que deben tener la oportunidad de personalizar (o "aprovechar") la instancia Monolog después de que se crea. No hay una ubicación convencional donde estas clases deben ser colocadas, por lo que es libre de crear un directorio dentro de su aplicación para contener estas clases:

    'single' => [
        'driver' => 'single',
        'tap' => [App\Logging\CustomizeFormatter::class],
        'path' => storage_path('logs/laravel.log'),
        'level' => 'debug',
    ],

Una vez que hayas configurado la opción `tap` en tu canal, estás listo para definir la clase que personalizará tu instancia Monolog. Esta clase sólo necesita un único método: `__invoke`, que recibe una instancia `Illuminate\Log\Logger`. La instancia `Illuminate\Log\Logger` proxy todas las llamadas de método a la instancia Monolog subyacente:

    <?php

    namespace App\Logging;

    use Monolog\Formatter\LineFormatter;

    class CustomizeFormatter
    {
        /**
         * Customize the given logger instance.
         *
         * @param  \Illuminate\Log\Logger  $logger
         * @return void
         */
        public function __invoke($logger)
        {
            foreach ($logger->getHandlers() as $handler) {
                $handler->setFormatter(new LineFormatter(
                    '[%datetime%] %channel%.%level_name%: %message% %context% %extra%'
                ));
            }
        }
    }

> **Nota**  
> Todas sus clases "tap" son resueltos por el [contenedor de servicios](/docs/%7B%7Bversion%7D%7D/container), por lo que cualquier dependencia constructor que requieren se inyecta automáticamente.

[]()

### Creación de Canales Monolog Handler

Monolog tiene una variedad de [manejadores disponibles](https://github.com/Seldaek/monolog/tree/main/src/Monolog/Handler) y Laravel no incluye un canal incorporado para cada uno. En algunos casos, es posible que desee crear un canal personalizado que es simplemente una instancia de un controlador específico Monolog que no tiene un controlador de registro correspondiente Laravel. Estos canales se pueden crear fácilmente utilizando el controlador `monolog`.

Cuando se utiliza el controlador `monolog`, la opción de configuración del `manejador` se utiliza para especificar qué manejador se instanciará. Opcionalmente, se puede especificar cualquier parámetro del constructor que necesite el manejador utilizando la opción de configuración `with`:

    'logentries' => [
        'driver'  => 'monolog',
        'handler' => Monolog\Handler\SyslogUdpHandler::class,
        'with' => [
            'host' => 'my.logentries.internal.datahubhost.company.com',
            'port' => '10000',
        ],
    ],

[]()

#### Formateadores monolog

Cuando se utiliza el controlador `monolog`, el Monolog `LineFormatter` se utilizará como el formateador predeterminado. Sin embargo, puedes personalizar el tipo de formateador que se pasa al manejador utilizando las opciones de configuración `formatter` y `formatter_with`:

    'browser' => [
        'driver' => 'monolog',
        'handler' => Monolog\Handler\BrowserConsoleHandler::class,
        'formatter' => Monolog\Formatter\HtmlFormatter::class,
        'formatter_with' => [
            'dateFormat' => 'Y-m-d',
        ],
    ],

Si utiliza un gestor Monolog capaz de proporcionar su propio formateador, puede establecer el valor de la opción de configuración `formatter` en `default`:

    'newrelic' => [
        'driver' => 'monolog',
        'handler' => Monolog\Handler\NewRelicHandler::class,
        'formatter' => 'default',
    ],

[]()

### Creación de canales personalizados a través de fábricas

Si desea definir un canal totalmente personalizado en el que tenga control total sobre la instanciación y configuración de Monolog, puede especificar un tipo de controlador `personalizado` en su archivo de configuración `config/logging.php`. Su configuración debe incluir una opción `via` que contenga el nombre de la clase de fábrica que será invocada para crear la instancia de Monolog:

    'channels' => [
        'example-custom-channel' => [
            'driver' => 'custom',
            'via' => App\Logging\CreateCustomLogger::class,
        ],
    ],

Una vez que haya configurado el canal de controlador `personalizado`, ya está listo para definir la clase que creará su instancia Monolog. Esta clase sólo necesita un único método `__invoke` que debe devolver la instancia del registrador Monolog. El método recibirá el array configuración de canales como único argumento:

    <?php

    namespace App\Logging;

    use Monolog\Logger;

    class CreateCustomLogger
    {
        /**
         * Create a custom Monolog instance.
         *
         * @param  array  $config
         * @return \Monolog\Logger
         */
        public function __invoke(array $config)
        {
            return new Logger(/* ... */);
        }
    }
