# Guía de actualización

- [Actualización a 9.0 desde 8.x](#upgrade-9.0)

[]()

## Cambios de alto impacto

<div class="content-list" markdown="1"/>

- [Actualización de dependencias](#updating-dependencies)
- [Flysystem 3.x](#flysystem-3)
- [Symfony Mailer](#symfony-mailer)

[object Object]

[]()

## Cambios de impacto medio

<div class="content-list" markdown="1"/>

- [Pertenece a muchos métodos `firstOrNew`, `firstOrCreate` y `updateOrCreate`](#belongs-to-many-first-or-new)
- [Casts personalizados y `null`](#custom-casts-and-null)
- [Tiempo de espera del cliente HTTP por defecto](#http-client-default-timeout)
- [Tipos de retorno PHP](#php-return-types)
- [Configuración del "Esquema" Postgres](#postgres-schema-configuration)
- [El método `assertDeleted`](#the-assert-deleted-method)
- [El Directorio `lang`](#the-lang-directory)
- [La regla `password`](#the-password-rule)
- [Los métodos `when` / `unless`](#when-and-unless-methods)
- [array-keys">Claves de array no validadas](<#unvalidated-\<glossary variable=>)

[object Object]

[]()

## Actualización a 9.0 desde 8.x

[]()

#### Tiempo estimado de actualización: 30 minutos

> **Nota**  
> Intentamos documentar todos los cambios de última hora posibles. Dado que algunos de estos cambios están en partes oscuras del framework, sólo una parte de estos cambios pueden afectar realmente a su aplicación. ¿Quieres ahorrar tiempo? Puede utilizar [Laravel Shift](https://laravelshift.com/) para ayudar a automatizar las actualizaciones de su aplicación.

[]()

### Actualización de dependencias

**Probabilidad de impacto: Alta**

#### Se requiere PHP 8.0.2

Laravel ahora requiere PHP 8.0.2 o superior.

#### Dependencias de Composer

Debe actualizar las siguientes dependencias en el archivo `composer.json` de su aplicación:

<div class="content-list" markdown="1"/>

- `laravel/framework` a `^9.`0
- `nunomaduro/collision` a `^6.`1

[object Object]

Además, por favor sustituye `facade` por `"spatie/laravel-ignition":` "^1 `.` 0" y `pusher/pusher-php-server` (si procede) por " `pusher/pusher-php-server":` "^5 `.` 0" en el archivo `composer.json` de tu aplicación.

Además, los siguientes paquetes de terceros han recibido nuevas versiones principales para dar soporte a Laravel 9.x. Si procede, deberías leer sus guías de actualización individuales antes de actualizar:

<div class="content-list" markdown="1"/>

- [Canal de notificaciones de Vonage (v3.0)](https://github.com/laravel/vonage-notification-channel/blob/3.x/UPGRADE.md) (sustituye a Nexmo)

[object Object]

Por último, examine cualquier otro paquete de terceros consumido por su aplicación y verifique que está utilizando la versión adecuada para la compatibilidad con Laravel 9.

[]()

#### Tipos de retorno PHP

PHP está empezando a requerir definiciones de tipo de retorno en métodos PHP como `offsetGet`, `offsetSet`, etc. En vista de ello, Laravel 9 ha implementado estos tipos de retorno en su código base. Normalmente, esto no debería afectar al código escrito por el usuario; sin embargo, si estás sobrescribiendo uno de estos métodos extendiendo las clases del núcleo de Laravel, necesitarás añadir estos tipos de retorno al código de tu propia aplicación o paquete:

<div class="content-list" markdown="1"/>

- `count(): int`
- `getIterator(): Traversable`
- `getSize(): int`
- `jsonSerialize(): array`
- `offsetExists($clave): bool`
- `offsetGet($clave): mixto`
- `offsetSet($clave, $valor): void`
- `offsetUnset($clave): void`

[object Object]

Además, se han añadido tipos de retorno a los métodos que implementan la `SessionHandlerInterface` de PHP. De nuevo, es poco probable que este cambio afecte al código de su propia aplicación o paquete:

<div class="content-list" markdown="1"/>

- `open($rutaGuarda, $nombreSesión): bool`
- `close(): bool`
- `read($id_sesión): string|false`
- `write($id_sesión, $datos): bool`
- `destroy($id_sesión): bool`
- `gc($tiempo de vida): int`

[object Object]

[]()

### Aplicación

[]()

#### Contrato de `aplicación`

**Probabilidad de impacto: Baja**

El método `storagePath` de la interfaz `Illuminate\Contracts\Foundation\Application` se ha actualizado para aceptar un argumento `$path`. Si implementa esta interfaz, debe actualizar su implementación en consecuencia:

    public function storagePath($path = '');

Del mismo modo, el método `langPath` de la clase `Illuminate\Foundation\Application` se ha actualizado para aceptar un argumento `$path`:

    public function langPath($path = '');

#### Método `ignore` del manejador de excepciones

**Probabilidad de impacto: Baja**

El método `ignore` del manejador de excepciones es ahora `público` en lugar de `protegido`. Este método no está incluido en el esqueleto de la aplicación por defecto; sin embargo, si ha definido manualmente este método debe actualizar su visibilidad a `public`:

```php
public function ignore(string $class);
```

#### Exception Handler Contract Binding

**Probabilidad de impacto: Muy baja**

Anteriormente, con el fin de reemplazar el gestor de excepciones predeterminado de Laravel, las implementaciones personalizadas se vinculaban al contenedor de servicios utilizando el tipo `\App\Exceptions\Handler::class`. Sin embargo, ahora debe enlazar implementaciones personalizadas utilizando el tipo `\Illuminate\Contracts\Debug\ExceptionHandler`::class.

### Hoja

#### Colecciones perezosas y la variable `$loop`

**Probabilidad de impacto Baja**

Cuando se itera sobre una instancia de `LazyCollection` dentro de una plantilla Blade, la variable `$loop` ya no está disponible, ya que acceder a esta variable hace que toda la `LazyCollection` se cargue en memoria, lo que hace que el uso de colecciones perezosas no tenga sentido en este escenario.

#### Directivas Checked / Disabled / Selected Blade

**Probabilidad de impacto Baja**

Las nuevas directivas `@checked`, `@disabled` y `@selected` de Blade pueden entrar en conflicto con eventos Vue del mismo nombre. Puedes usar `@@` para escapar las directivas y evitar este conflicto: `@@selected`.

### Colecciones

#### El contrato `Enumerable`

**Probabilidad de impacto Baja**

El contrato `Illuminate\Support\Enumerable` ahora define un `único` método. Si está implementando manualmente esta interfaz, debe actualizar su implementación para reflejar este nuevo método:

```php
public function sole($key = null, $operator = null, $value = null);
```

#### El método `reduceWithKeys`

El método `reduceWithKeys` ha sido eliminado ya que el método `reduce` proporciona la misma funcionalidad. Puede simplemente actualizar su código para llamar a `reduce` en lugar de a `reduceWithKeys`.

#### Método `reduceMany`

El método `reduceMany` ha sido renombrado a `reduceSpread` por coherencia con otros métodos similares.

### Contenedor

#### El contrato `Container`

**Probabilidad de impacto: Muy baja**

El contrato `Illuminate\Contracts\Container\Container` ha recibido dos definiciones de método: `scoped` y `scopedIf`. Si está implementando manualmente este contrato, debe actualizar su implementación para reflejar estos nuevos métodos.

#### El contrato `ContextualBindingBuilder`

**Probabilidad de impacto Muy baja**

El contrato `Illuminate\Contracts\Container\ContextualBindingBuilder` define ahora un método `giveConfig`. Si implementa manualmente esta interfaz, debe actualizar su implementación para reflejar este nuevo método:

```php
public function giveConfig($key, $default = null);
```

### Base de datos

[]()

#### Configuración del "esquema" Postgres

**Probabilidad de impacto: Media**

La opción de configuración de `esquemas` utilizada para configurar las rutas de búsqueda de conexiones Postgres en el archivo de configuración `config/database.` php de su aplicación debe renombrarse a `search_path`.

[]()

#### Método `registerCustomDoctrineType` del generador de esquemas

**Probabilidad de impacto Baja**

Se ha eliminado el método `registerCustomDoctrineType` de la clase `Illuminate\Database\Schema\Builder`. En su lugar, puede utilizar el método `registerDoctrineType` en la facade `DB`, o registrar tipos Doctrine personalizados en el archivo de configuración `config/database.php`.

### Eloquent

[]()

#### Casts personalizados y `null`

**Probabilidad de impacto: Media**

En versiones anteriores de Laravel, el método `set` de las clases cast personalizadas no se invocaba si el atributo cast se establecía en `null`. Sin embargo, este comportamiento era inconsistente con la documentación de Laravel. En Laravel 9.x, el método `set` de la clase cast será invocado con `null` como argumento `$value`. Por tanto, debes asegurarte de que tus cast personalizados son capaces de manejar suficientemente este escenario:

```php
/**
 * Prepare the given value for storage.
 *
 * @param  \Illuminate\Database\Eloquent\Model  $model
 * @param  string  $key
 * @param  AddressModel  $value
 * @param  array  $attributes
 * @return array
 */
public function set($model, $key, $value, $attributes)
{
    if (! $value instanceof AddressModel) {
        throw new InvalidArgumentException('The given value is not an Address instance.');
    }

    return [
        'address_line_one' => $value->lineOne,
        'address_line_two' => $value->lineTwo,
    ];
}
```

[]()

#### Pertenece a muchos métodos `firstOrNew`, `firstOrCreate` y `updateOrCreate`

**Probabilidad de impacto Media**

Los métodos `firstOrNew`, `firstOrCreate` y `updateOrCreate` de la relación `belongsToMany` aceptan un array de atributos como primer argumento. En versiones anteriores de Laravel, esta array de atributos se comparaba con el "pivote" / tabla intermedia para los registros existentes.

Sin embargo, este comportamiento era inesperado y normalmente no deseado. En su lugar, estos métodos comparan ahora la array de atributos con la tabla del modelo relacionado:

```php
$user->roles()->updateOrCreate([
    'name' => 'Administrator',
]);
```

Además, el método `firstOrCreate` acepta ahora un array `$values` como segundo argumento. Esta array se fusionará con el primer argumento del método`($attributes`) al crear el modelo relacionado si no existe ya uno. Este cambio hace que este método sea consistente con los métodos `firstOrCreate` ofrecidos por otros tipos de relación:

```php
$user->roles()->firstOrCreate([
    'name' => 'Administrator',
], [
    'created_by' => $user->id,
]);
```

#### El método `touch`

**Probabilidad de impacto Baja**

El método `touch` ahora acepta un atributo para tocar. Si anteriormente sobrescribía este método, debe actualizar la firma del método para reflejar este nuevo argumento:

```php
public function touch($attribute = null);
```

### Cifrado

#### El contrato Encrypter

**Probabilidad de impacto Baja**

El contrato `Illuminate\Contracts\Encryption\Encrypter` ahora define un método `getKey`. Si está implementando manualmente esta interfaz, debe actualizar su implementación en consecuencia:

```php
public function getKey();
```

### facades

#### El método `getFacadeAccessor`

**Probabilidad de impacto Baja**

El método `getFacadeAccessor` debe devolver siempre una clave de enlace de contenedor. En versiones anteriores de Laravel, este método podía devolver una instancia de objeto; sin embargo, este comportamiento ya no está soportado. Si has escrito tus propias facades, debes asegurarte de que este método devuelve una cadena de enlace de contenedor:

```php
/**
 * Get the registered name of the component.
 *
 * @return string
 */
protected static function getFacadeAccessor()
{
    return Example::class;
}
```

### Sistema de Archivos

#### Variable de entorno `FILESYSTEM_DRIVER`

**Probabilidad de impacto Baja**

La variable de entorno `FILESYSTEM_DRIVER` ha sido renombrada a `FILESYSTEM_DISK` para reflejar mejor su uso. Este cambio sólo afecta al esqueleto de la aplicación; sin embargo, si lo desea, puede actualizar las variables de entorno de su propia aplicación para reflejar este cambio.

#### El Disco "Nube

**Probabilidad de impacto Baja**

La opción de configuración del disco `en la nube` se eliminó del esqueleto de la aplicación por defecto en noviembre de 2020. Este cambio solo afecta al esqueleto de la aplicación. Si está utilizando el disco en `la` nube dentro de su aplicación, debe dejar este valor de configuración en el esqueleto de su propia aplicación.

[]()

### Flysystem 3.x

**Probabilidad de impacto: Alta**

Laravel 9.x ha migrado de [Flysystem](https://flysystem.thephpleague.com/v2/docs/) 1.x a 3.x. Bajo el capó, Flysystem potencia todos los métodos de manipulación de ficheros proporcionados por la facade `Storage`. A la luz de esto, algunos cambios pueden ser necesarios dentro de su aplicación, sin embargo, hemos tratado de hacer esta transición lo más fluida posible.

#### Requisitos previos del controlador

Antes de utilizar los controladores S3, FTP o SFTP, deberá instalar el paquete adecuado a través del gestor de paquetes Composer:

- Amazon S3: composer require `-W league/flysystem-aws-s3-v3 "^3.0"`
- FTP: composer require `league/flysystem-ftp "^3.`0"
- SFTP: composer require `liga/flysystem-sftp-v3 "^3.`0"

#### Sobrescribir archivos existentes

Las operaciones de escritura como `put`, `write` y `writeStream` ahora sobrescriben los archivos existentes por defecto. Si no desea sobrescribir archivos existentes, debe comprobar manualmente la existencia del archivo antes de realizar la operación de escritura.

#### Excepciones de escritura

Las operaciones de escritura como `put`, `write` y `writeStream` ya no lanzan una excepción cuando falla una operación de escritura. En su lugar, se devuelve `false`. Si desea conservar el comportamiento anterior, que lanzaba excepciones, puede definir la opción `throw` en la array configuración de un disco del sistema de archivos:

```php
'public' => [
    'driver' => 'local',
    // ...
    'throw' => true,
],
```

#### Lectura de ficheros perdidos

Al intentar leer de un archivo que no existe ahora devuelve `null`. En versiones anteriores de Laravel, un `Illuminate\Contracts\Filesystem\FileNotFoundException` habría sido lanzado.

#### Borrado de ficheros perdidos

Al intentar `eliminar` un archivo que no existe, ahora se devuelve `true`.

#### Adaptadores en caché

Flysystem ya no soporta "adaptadores en caché". Por lo tanto, se han eliminado de Laravel y cualquier configuración relevante (como la clave de `cache` dentro de las configuraciones de disco) se puede eliminar.

#### Sistemas de archivos personalizados

Se han introducido ligeros cambios en los pasos necesarios para registrar controladores de sistemas de archivos personalizados. Por lo tanto, si estabas definiendo tus propios controladores de sistema de ficheros personalizados, o usando paquetes que definen controladores personalizados, deberías actualizar tu código y dependencias.

Por ejemplo, en Laravel 8.x, un controlador de sistema de archivos personalizado podría registrarse así:

```php
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

Storage::extend('dropbox', function ($app, $config) {
    $client = new DropboxClient(
        $config['authorization_token']
    );

    return new Filesystem(new DropboxAdapter($client));
});
```

Sin embargo, en Laravel 9.x, la devolución de llamada dada al método `Storage::extend` debería devolver directamente una instancia de `Illuminate\Filesystem\FilesystemAdapter`:

```php
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

Storage::extend('dropbox', function ($app, $config) {
    $adapter = new DropboxAdapter(
        new DropboxClient($config['authorization_token'])
    );

    return new FilesystemAdapter(
        new Filesystem($adapter, $config),
        $adapter,
        $config
    );
});
```

### Ayudantes

[]()

#### El ayudante `data_get` y los objetos iterables

**Probabilidad de impacto Muy baja**

Anteriormente, el ayudante `data_get` podía utilizarse para recuperar datos anidados en arrays e instancias de `Collection`; sin embargo, ahora este ayudante puede recuperar datos anidados en todos los objetos iterables.

[]()

#### El ayudante `str`

**Probabilidad de impacto Muy baja**

Laravel 9.x incluye ahora una [función](/docs/%7B%7Bversion%7D%7D/helpers#method-str) `str` helper global. Si está definiendo un helper `str` global en su aplicación, debería renombrarlo o eliminarlo para que no entre en conflicto con el propio helper `str` de Laravel.

[]()

#### Los métodos `when` / `unless`

**Probabilidad de impacto Media**

Como ya sabrás, varias clases del framework ofrecen los métodos `when` y `unless`. Estos métodos se pueden utilizar para realizar condicionalmente una acción si el valor booleano del primer argumento del método se evalúa como `verdadero` o `falso`:

```php
$collection->when(true, function ($collection) {
    $collection->merge([1, 2, 3]);
});
```

Por lo tanto, en versiones anteriores de Laravel, pasar un closure a los métodos `when` o `unless` significaba que la operación condicional siempre se ejecutaría, ya que una comparación suelta contra un objeto de closure (o cualquier otro objeto) siempre se evalúa a `true`. Esto a menudo conducía a resultados inesperados porque los desarrolladores esperaban que el **resultado** del closure se utilizara como el valor booleano que determinaba si la acción condicional se ejecutaba.

Así, en Laravel 9.x, cualquier closures pasado a los métodos `when` o `unless` se ejecutará y el valor devuelto por el closure se considerará el valor booleano utilizado por los métodos `when` y `unless`:

```php
$collection->when(function ($collection) {
    // This closure is executed...
    return false;
}, function ($collection) {
    // Not executed since first closure returned "false"...
    $collection->merge([1, 2, 3]);
});
```

### Cliente HTTP

[]()

#### Tiempo de espera por defecto

**Probabilidad de impacto Media**

El [cliente HTTP](/docs/%7B%7Bversion%7D%7D/http-client) tiene ahora un tiempo de espera por defecto de 30 segundos. En otras palabras, si el servidor no responde en 30 segundos, se lanzará una excepción. Anteriormente, no se había configurado ningún tiempo de espera por defecto en el cliente HTTP, lo que provocaba que las peticiones a veces se "colgaran" indefinidamente.

Si desea especificar un tiempo de espera más largo para una petición determinada, puede hacerlo utilizando el método `timeout`:

    $response = Http::timeout(120)->get(/* ... */);

#### HTTP Fake y middleware

**Probabilidad de impacto Baja**

Anteriormente, Laravel no ejecutaba ningún middleware Guzzle HTTP cuando el cliente [HTTP](/docs/%7B%7Bversion%7D%7D/http-client) era "falso". Sin embargo, en Laravel 9.x, middleware middleware Guzzle HTTP se ejecutará incluso cuando el cliente HTTP esté falseado.

#### HTTP Fake e Inyección de Dependencia

**Probabilidad de impacto Baja**

En versiones anteriores de Laravel, la invocación del método `Http::fake(` ) no afectaba a las instancias de `Illuminate\Http\Client\Factory` que se inyectaban en los constructores de clase. Sin embargo, en Laravel 9.x, Http::fake( `)` garantizará que los clientes HTTP inyectados en otros servicios mediante inyección de dependencias devuelvan respuestas falsas. Este comportamiento es más coherente con el de otras facades y falsificaciones.

[]()

### Symfony Mailer

**Probabilidad de impacto: Alta**

Uno de los mayores cambios en Laravel 9.x es la transición de SwiftMailer, que ya no se mantiene a partir de diciembre de 2021, a Symfony Mailer. Sin embargo, hemos tratado de hacer esta transición lo más fluida posible para tus aplicaciones. Dicho esto, revisa detenidamente la lista de cambios que aparece a continuación para asegurarte de que tu aplicación es totalmente compatible.

#### Requisitos previos del controlador

Para seguir utilizando el transporte Mailgun, su aplicación debe requerir los paquetes `symfony/mailgun-mailer` y `symfony/http-client` Composer:

```shell
composer require symfony/mailgun-mailer symfony/http-client
```

El paquete `wildbit/swiftmailer-postmark` Composer debe eliminarse de la aplicación. En su lugar, su aplicación debe requerir los paquetes `symfony/postmark-mailer` y `symfony/http-client` Composer:

```shell
composer require symfony/postmark-mailer symfony/http-client
```

#### Tipos de retorno actualizados

Los métodos `send`, `html`, `raw` y `plain` de `Illuminate\Mail\Mailer` ya no devuelven `void`. En su lugar, se devuelve una instancia de `Illuminate\Mail\SentMessage`. Este objeto contiene una instancia de `Symfony\Component\Mailer\SentMessage` que es accesible a través del método `getSymfonySentMessage` o invocando dinámicamente métodos en el objeto.

#### Métodos "Swift" renombrados

Varios métodos relacionados con SwiftMailer, algunos de los cuales no estaban documentados, han sido renombrados a sus homólogos de Symfony Mailer. Por ejemplo, el método `withSwiftMessage` ha pasado a llamarse `withSymfonyMessage`:

    // Laravel 8.x...
    $this->withSwiftMessage(function ($message) {
        $message->getHeaders()->addTextHeader(
            'Custom-Header', 'Header Value'
        );
    });

    // Laravel 9.x...
    use Symfony\Component\Mime\Email;

    $this->withSymfonyMessage(function (Email $message) {
        $message->getHeaders()->addTextHeader(
            'Custom-Header', 'Header Value'
        );
    });

> **Advertencia**  
> Revise detenidamente la [documentación de Symfony Mailer](https://symfony.com/doc/6.0/mailer.html#creating-sending-messages) para conocer todas las interacciones posibles con el objeto `Symfony\Component\Mime\Email`.

La siguiente lista contiene una descripción más detallada de los métodos renombrados. Muchos de estos métodos son métodos de bajo nivel utilizados para interactuar con SwiftMailer / Symfony Mailer directamente, por lo que puede no ser de uso común dentro de la mayoría de las aplicaciones Laravel:

    Message::getSwiftMessage();
    Message::getSymfonyMessage();

    Mailable::withSwiftMessage($callback);
    Mailable::withSymfonyMessage($callback);

    MailMessage::withSwiftMessage($callback);
    MailMessage::withSymfonyMessage($callback);

    Mailer::getSwiftMailer();
    Mailer::getSymfonyTransport();

    Mailer::setSwiftMailer($swift);
    Mailer::setSymfonyTransport(TransportInterface $transport);

    MailManager::createTransport($config);
    MailManager::createSymfonyTransport($config);

#### Métodos Proxied `Illuminate\Mail\Message`

El `Illuminate\Mail\Message` normalmente proxy métodos que faltan a la instancia `Swift_Message` subyacente. Sin embargo, los métodos que faltan ahora se proxy a una instancia de `Symfony\Component\Mime\Email` en su lugar. Por lo tanto, cualquier código que anteriormente dependía de los métodos que faltan para ser proxy a SwiftMailer debe actualizarse a sus correspondientes contrapartes Symfony Mailer.

Una vez más, muchas aplicaciones pueden no estar interactuando con estos métodos, ya que no están documentados dentro de la documentación de Laravel:

    // Laravel 8.x...
    $message
        ->setFrom('taylor@laravel.com')
        ->setTo('example@example.org')
        ->setSubject('Order Shipped')
        ->setBody('<h1>HTML</h1>', 'text/html')
        ->addPart('Plain Text', 'text/plain');

    // Laravel 9.x...
    $message
        ->from('taylor@laravel.com')
        ->to('example@example.org')
        ->subject('Order Shipped')
        ->html('<h1>HTML</h1>')
        ->text('Plain Text');

#### IDs de Mensajes Generados

SwiftMailer ofrece la posibilidad de definir un dominio personalizado para incluir en los IDs de mensajes generados a través de la opción de configuración `mime.idgenerator.idright`. Symfony Mailer no admite esta opción. En su lugar, Symfony Mailer generará automáticamente un ID de mensaje basado en el remitente.

#### Cambios en el evento`MessageSent`

La propiedad `message` del evento `Illuminate\Mail\Events\MessageSent` ahora contiene una instancia de `Symfony\Component\Mime\Email` en lugar de una instancia de `Swift_Message`. Este mensaje representa el correo electrónico **antes de** ser enviado.

Además, se ha añadido una nueva propiedad `sent` al evento `MessageSent`. Esta propiedad contiene una instancia de `Illuminate\Mail\SentMessage` y contiene información sobre el correo electrónico enviado, como el ID del mensaje.

#### Reconexiones forzadas

Ya no es posible forzar una reconexión de transporte (por ejemplo, cuando el Mailer se ejecuta a través de un proceso daemon). En su lugar, Symfony Mailer intentará reconectarse al transporte automáticamente y lanzará una excepción si la reconexión falla.

#### Opciones de flujo SMTP

Ya no es posible definir opciones de flujo para el transporte SMTP. En su lugar, debe definir las opciones relevantes directamente dentro de la configuración si son compatibles. Por ejemplo, para desactivar la verificación de pares TLS:

    'smtp' => [
        // Laravel 8.x...
        'stream' => [
            'ssl' => [
                'verify_peer' => false,
            ],
        ],

        // Laravel 9.x...
        'verify_peer' => false,
    ],

Para obtener más información sobre las opciones de configuración disponibles, consulte la [documentación de Symfony Mailer](https://symfony.com/doc/6.0/mailer.html#transport-setup).

> **Advertencia**  
> A pesar del ejemplo anterior, generalmente no se aconseja desactivar la verificación SSL ya que introduce la posibilidad de ataques "man-in-the-middle".

#### SMTP `auth_mode`

Ya no es necesario definir el SMTP `auth_mode` en el archivo de configuración de `correo`. El modo de autenticación se negociará automáticamente entre Symfony Mailer y el servidor SMTP.

#### Destinatarios fallidos

Ya no es posible recuperar una lista de destinatarios fallidos después de enviar un mensaje. En su lugar, se lanzará una excepción `Symfony\Component\Mailer\Exception\TransportExceptionInterface` si falla el envío de un mensaje. En lugar de confiar en la recuperación de direcciones de correo electrónico no válidas después de enviar un mensaje, le recomendamos que valide las direcciones de correo electrónico antes de enviar el mensaje.

### Paquetes

[]()

#### El Directorio `lang`

**Probabilidad de impacto Media**

En las nuevas aplicaciones Laravel, el directorio `resources/lang` ahora se encuentra en el directorio raíz del proyecto`(lang`). Si tu paquete está publicando archivos de idioma a este directorio, debes asegurarte de que tu paquete está publicando a `app()->langPath()` en lugar de una ruta codificada.

[]()

### Cola

[closure-library">]()

#### La librería `opis/closure`

**Probabilidad de impacto Baja**

La dependencia de Laravel de `opis/closure` ha sido sustituida por `laravel/serializable-closure`. Esto no debería causar ningún cambio en su aplicación a menos que esté interactuando con la biblioteca `opis/closure` directamente. Además, las clases `Illuminate\Queue\SerializableClosureFactory` e `Illuminate\Queue\SerializableClosure`, anteriormente obsoletas, han sido eliminadas. Si está interactuando con la biblioteca `opis/closure` directamente o utilizando cualquiera de las clases eliminadas, puede utilizar [Laravel Serializable closure](https://github.com/laravel/serializable-closure) en su lugar.

#### El método `flush` del proveedor de trabajos fallidos

**Probabilidad de impacto Baja**

El método `flush` definido por la interfaz `Illuminate\Queue\Failed\FailedJobProviderInterface` ahora acepta un argumento `$hours` que determina cuántos años debe tener un trabajo fallido (en horas) antes de que sea vaciado por el comando `queue:flush`. Si estás implementando manualmente la `FailedJobProviderInterface` debes asegurarte de que tu implementación se actualiza para reflejar este nuevo argumento:

```php
public function flush($hours = null);
```

### Sesión

#### El método `getSession`

**Probabilidad de impacto Baja**

La clase `Symfony\Component\HttpFoundaton\Request` que es extendida por la propia clase `Illuminate\Http\Request` de Laravel ofrece un método `getSession` para obtener el manejador de almacenamiento de sesión actual. Este método no está documentado por Laravel ya que la mayoría de las aplicaciones Laravel interactúan con la sesión a través del propio método `session` de Laravel.

El método `getSession` anteriormente devolvía una instancia de `Illuminate\Session\Store` o `null`; sin embargo, debido a que la versión Symfony 6.x impone un tipo de retorno de `Symfony\Component\HttpFoundation\Session\SessionInterface`, `getSession` ahora devuelve correctamente una implementación de `SessionInterface` o lanza una excepción `\Symfony\Component\HttpFoundation\Exception\SessionNotFoundException` cuando no hay sesión disponible.

### Pruebas

[]()

#### El método `assertDeleted`

**Probabilidad de impacto Media**

Todas las llamadas al método `assertDeleted` deberían actualizarse a `assertModelMissing`.

### Proxies de confianza

**Probabilidad de impacto Baja**

Si está actualizando su proyecto Laravel 8 a Laravel 9 importando el código de su aplicación existente en un esqueleto de aplicación Laravel 9 totalmente nuevo, puede que necesite actualizar el middleware"proxy de confianza" de su aplicación.

Dentro de tu archivo `app/Http/middleware/TrustProxies.` php, actualiza el `uso` de `Fideloper\Proxy\TrustProxies` `como` `middleware` para `usar Illuminate\Http\middleware\TrustProxies como middleware`.

A continuación, dentro de `app/Http/middleware/TrustProxies.`php, debería actualizar la definición de la propiedad `$headers`:

```php
// Before...
protected $headers = Request::HEADER_X_FORWARDED_ALL;

// After...
protected $headers =
    Request::HEADER_X_FORWARDED_FOR |
    Request::HEADER_X_FORWARDED_HOST |
    Request::HEADER_X_FORWARDED_PORT |
    Request::HEADER_X_FORWARDED_PROTO |
    Request::HEADER_X_FORWARDED_AWS_ELB;
```

Finalmente, puedes eliminar la dependencia `fideloper/proxy` Composer de tu aplicación:

```shell
composer remove fideloper/proxy
```

### Validación

#### Método `validado` de petición de formulario

**Probabilidad de impacto Baja**

El método `validado` que ofrecen las peticiones de formulario acepta ahora los argumentos `$key` y `$default`. Si está sobrescribiendo manualmente la definición de este método, debería actualizar la firma de su método para reflejar estos nuevos argumentos:

```php
public function validated($key = null, $default = null)
```

[]()

#### La regla `password`

**Probabilidad de impacto Media**

La regla `password`, que valida que el valor de entrada dado coincide con la contraseña actual del usuario autenticado, ha sido renombrada a `current_password`.

[array-keys">]()

#### Claves de array no validadas

**Probabilidad de impacto Media**

En versiones anteriores de Laravel, era necesario indicar manualmente al validador de Laravel que excluyera las claves de array no validadas de los datos "validados" que devuelve, especialmente en combinación con una regla de `array` que no especifica una lista de claves permitidas.

Sin embargo, en Laravel 9.x, las claves de array no validadas son siempre excluidas de los datos "validados" incluso cuando no se han especificado claves permitidas mediante la regla de `array`. Normalmente, este comportamiento es el más esperado y el método anterior `excludeUnvalidatedArrayKeys` sólo se añadió a Laravel 8.x como medida temporal para preservar la compatibilidad con versiones anteriores.

Aunque no es recomendable, puedes optar por el comportamiento anterior de Laravel 8.x invocando un nuevo método `includeUnvalidatedArrayKeys` dentro del método `boot` de uno de los proveedores de servicio de tu aplicación:

```php
use Illuminate\Support\Facades\Validator;

/**
 * Register any application services.
 *
 * @return void
 */
public function boot()
{
    Validator::includeUnvalidatedArrayKeys();
}
```

[]()

### Varios

También le animamos a ver los cambios en el [repositorio](https://github.com/laravel/laravel) `laravel/laravel` [GitHub](https://github.com/laravel/laravel). Aunque muchos de estos cambios no son necesarios, es posible que desee mantener estos archivos sincronizados con su aplicación. Algunos de estos cambios serán cubiertos en esta guía de actualización, pero otros, como los cambios en los archivos de configuración o comentarios, no lo serán. Puedes ver fácilmente los cambios con la [herramienta de comparación de GitHub](https://github.com/laravel/laravel/compare/8.x...9.x) y elegir qué actualizaciones son importantes para ti.
