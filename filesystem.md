# Almacenamiento de archivos

- [Introducción](#introduction)
- [Configuración](#configuration)
  - [El controlador local](#the-local-driver)
  - [El disco público](#the-public-disk)
  - [Requisitos previos del controlador](#driver-prerequisites)
  - [Sistemas de archivos de alcance y de sólo lectura](#scoped-and-read-only-filesystems)
  - [Sistemas de archivos compatibles con Amazon S3](#amazon-s3-compatible-filesystems)
- [Obtención de instancias de disco](#obtaining-disk-instances)
  - [Discos bajo demanda](#on-demand-disks)
- [Recuperación de archivos](#retrieving-files)
  - [Descarga de archivos](#downloading-files)
  - [URL de archivos](#file-urls)
  - [Metadatos de archivos](#file-metadata)
- [Almacenamiento de archivos](#storing-files)
  - [Anexar y anexar archivos](#prepending-appending-to-files)
  - [Copiar y mover archivos](#copying-moving-files)
  - [Streaming automático](#automatic-streaming)
  - [Carga de archivos](#file-uploads)
  - [Visibilidad de archivos](#file-visibility)
- [Eliminación de archivos](#deleting-files)
- [Directorios](#directories)
- [Sistemas de archivos personalizados](#custom-filesystems)

[]()

## Introducción

Laravel proporciona una poderosa abstracción del sistema de archivos gracias al maravilloso paquete [Flysystem](https://github.com/thephpleague/flysystem) PHP de Frank de Jonge. La integración de Laravel Flysystem proporciona controladores simples para trabajar con sistemas de archivos locales, SFTP y Amazon S3. Aún mejor, es increíblemente sencillo cambiar entre estas opciones de almacenamiento entre tu máquina de desarrollo local y el servidor de producción, ya que la API sigue siendo la misma para cada sistema.

[]()

## Configuración

El archivo de configuración del sistema de archivos de Laravel se encuentra en `config/filesystems.php`. Dentro de este archivo, puedes configurar todos los "discos" de tu sistema de archivos. Cada disco representa un controlador de almacenamiento particular y una ubicación de almacenamiento. En el archivo de configuración se incluyen ejemplos de configuración para cada controlador soportado, de forma que pueda modificar la configuración para reflejar sus preferencias y credenciales de almacenamiento.

El controlador `local` interactúa con los archivos almacenados localmente en el servidor que ejecuta la aplicación Laravel, mientras que el controlador `s3` se utiliza para escribir en el servicio de almacenamiento en la nube S3 de Amazon.

> **NotaPuede**configurar tantos discos como desee e incluso puede tener varios discos que utilicen el mismo controlador.

[]()

### El Disco Local

Cuando se utiliza el controlador `local`, todas las operaciones de archivo son relativas al directorio `raíz` definido en su archivo de configuración `de sistemas de archivos`. Por defecto, este valor se establece en el directorio `storage/app`. Por lo tanto, el siguiente método escribiría en `storage/app/ejemplo.txt`:

    use Illuminate\Support\Facades\Storage;

    Storage::disk('local')->put('example.txt', 'Contents');

[]()

### El disco público

El disco `público` incluido en el fichero de configuración de sistemas de `ficheros` de tu aplicación está pensado para ficheros que van a ser accesibles públicamente. Por defecto, el disco `público` utiliza el controlador `local` y almacena sus archivos en `storage/app/public`.

Para que estos archivos sean accesibles desde la web, debes crear un enlace simbólico desde `public/storage` a `storage/app/public`. Utilizando esta convención de carpetas mantendrá sus archivos accesibles públicamente en un directorio que puede ser fácilmente compartido a través de despliegues cuando se utilizan sistemas de despliegue sin tiempo de inactividad como [Envoyer](https://envoyer.io).

Para crear el enlace simbólico, puedes utilizar el comando `storage:link` de Artisan:

```shell
php artisan storage:link
```

Una vez que un archivo ha sido almacenado y el enlace simbólico ha sido creado, puede crear una URL a los archivos utilizando el `asset` helper:

    echo asset('storage/file.txt');

Puedes configurar enlaces simbólicos adicionales en tu fichero de configuración de `sistemas de ficheros`. Cada uno de los enlaces configurados se creará cuando ejecute el comando `storage:link`:

    'links' => [
        public_path('storage') => storage_path('app/public'),
        public_path('images') => storage_path('app/images'),
    ],

[]()

### Requisitos previos del controlador

[]()

#### Configuración del controlador S3

Antes de utilizar el controlador S3, deberá instalar el paquete Flysystem S3 a través del gestor de paquetes Composer:

```shell
composer require league/flysystem-aws-s3-v3 "^3.0"
```

La información de configuración del controlador S3 se encuentra en el archivo de configuración `config/filesystems.php`. Este archivo contiene una array de configuración de ejemplo para un controlador S3. Usted es libre de modificar esta array con su propia configuración de S3 y credenciales. Para mayor comodidad, estas variables de entorno coinciden con la convención de nomenclatura utilizada por la CLI de AWS.

[]()

#### Configuración del controlador FTP

Antes de utilizar el controlador FTP, tendrás que instalar el paquete Flysystem FTP a través del gestor de paquetes Composer:

```shell
composer require league/flysystem-ftp "^3.0"
```

Las integraciones Flysystem de Laravel funcionan muy bien con FTP; sin embargo, no se incluye una configuración de ejemplo con el archivo de configuración `filesystems.` php predeterminado del framework. Si necesitas configurar un sistema de archivos FTP, puedes utilizar el ejemplo de configuración que se muestra a continuación:

    'ftp' => [
        'driver' => 'ftp',
        'host' => env('FTP_HOST'),
        'username' => env('FTP_USERNAME'),
        'password' => env('FTP_PASSWORD'),

        // Optional FTP Settings...
        // 'port' => env('FTP_PORT', 21),
        // 'root' => env('FTP_ROOT'),
        // 'passive' => true,
        // 'ssl' => true,
        // 'timeout' => 30,
    ],

[]()

#### Configuración del controlador SFTP

Antes de usar el driver SFTP, necesitarás instalar el paquete Flysystem SFTP a través del gestor de paquetes Composer:

```shell
composer require league/flysystem-sftp-v3 "^3.0"
```

Las integraciones Flysystem de Laravel funcionan muy bien con SFTP; sin embargo, no se incluye un ejemplo de configuración con el archivo de configuración `filesystems.` php por defecto del framework. Si necesitas configurar un sistema de archivos SFTP, puedes utilizar el ejemplo de configuración que se muestra a continuación:

    'sftp' => [
        'driver' => 'sftp',
        'host' => env('SFTP_HOST'),

        // Settings for basic authentication...
        'username' => env('SFTP_USERNAME'),
        'password' => env('SFTP_PASSWORD'),

        // Settings for SSH key based authentication with encryption password...
        'privateKey' => env('SFTP_PRIVATE_KEY'),
        'passphrase' => env('SFTP_PASSPHRASE'),

        // Optional SFTP Settings...
        // 'hostFingerprint' => env('SFTP_HOST_FINGERPRINT'),
        // 'maxTries' => 4,
        // 'passphrase' => env('SFTP_PASSPHRASE'),
        // 'port' => env('SFTP_PORT', 22),
        // 'root' => env('SFTP_ROOT', ''),
        // 'timeout' => 30,
        // 'useAgent' => true,
    ],

[]()

### Sistemas de archivos de alcance y sólo lectura

Los discos de ámbito le permiten definir un sistema de archivos en el que todas las rutas se prefijan automáticamente con un prefijo de ruta determinado. Antes de crear un disco de sistema de archivos de ámbito general, deberá instalar un paquete Flysystem adicional a través del gestor de paquetes Composer:

```shell
composer require league/flysystem-path-prefixing "^3.0"
```

Puede crear una instancia con ámbito de ruta de cualquier disco de sistema de archivos existente definiendo un disco que utilice el controlador `de ámbito`. Por ejemplo, puede crear un disco que asigne su disco `s3` existente a un prefijo de ruta específico, y entonces cada operación de archivo que utilice su disco asignado utilizará el prefijo especificado:

```php
's3-videos' => [
    'driver' => 'scoped',
    'disk' => 's3',
    'prefix' => 'path/to/videos',
],
```

Los discos de "sólo lectura" le permiten crear discos de sistema de archivos que no permiten operaciones de escritura. Antes de utilizar la opción de configuración de sólo `lectura`, deberá instalar un paquete Flysystem adicional a través del gestor de paquetes Composer:

```shell
composer require league/flysystem-read-only "^3.0"
```

A continuación, puede incluir la opción de configuración `de sólo lectura` en una o más de las matrices de configuración de su disco:

```php
's3-videos' => [
    'driver' => 's3',
    // ...
    'read-only' => true,
],
```

[]()

### Sistemas de archivos compatibles con Amazon S3

Por defecto, el archivo de configuración de sistemas de `archivos` de tu aplicación contiene una configuración de disco para el disco `s3`. Además de utilizar este disco para interactuar con Amazon S3, puedes utilizarlo para interactuar con cualquier servicio de almacenamiento de archivos compatible con S3, como [MinIO](https://github.com/minio/minio) o [DigitalOcean Spaces](https://www.digitalocean.com/products/spaces/).

Generalmente, después de actualizar las credenciales del disco para que coincidan con las credenciales del servicio que planea utilizar, sólo necesita actualizar el valor de la opción de configuración `del punto final`. El valor de esta opción se define normalmente a través de la variable de entorno `AWS_ENDPOINT`:

    'endpoint' => env('AWS_ENDPOINT', 'https://minio:9000'),

[]()

## Obtención de instancias de disco

La facade `Storage` puede utilizarse para interactuar con cualquiera de sus discos configurados. Por ejemplo, puede utilizar el método `put` de la facade para almacenar un avatar en el disco predeterminado. Si llamas a métodos de la facade `Storage` sin llamar primero al método `disk`, el método será pasado automáticamente al disco por defecto:

    use Illuminate\Support\Facades\Storage;

    Storage::put('avatars/1', $content);

Si tu aplicación interactúa con múltiples discos, puedes usar el método `disk` en la facade `Storage` para trabajar con archivos en un disco en particular:

    Storage::disk('s3')->put('avatars/1', $content);

[]()

### Discos bajo demanda

A veces puedes querer crear un disco en tiempo de ejecución usando una configuración dada sin que esa configuración esté realmente presente en el fichero de configuración de `sistemas de ficheros` de tu aplicación. Para conseguirlo, puedes pasar un array configuración al método `build` de la facade `Storage`:

```php
use Illuminate\Support\Facades\Storage;

$disk = Storage::build([
    'driver' => 'local',
    'root' => '/path/to/root',
]);

$disk->put('image.jpg', $content);
```

[]()

## Recuperación de archivos

El método `get` se puede utilizar para recuperar el contenido de un archivo. El método devolverá el contenido sin procesar del archivo. Recuerde que todas las rutas de archivos deben especificarse en relación a la ubicación "raíz" del disco:

    $contents = Storage::get('file.jpg');

El método `exists` puede usarse para determinar si un fichero existe en el disco:

    if (Storage::disk('s3')->exists('file.jpg')) {
        // ...
    }

El método `missing` puede utilizarse para determinar si falta un fichero en el disco:

    if (Storage::disk('s3')->missing('file.jpg')) {
        // ...
    }

[]()

### Descarga de archivos

El método `download` puede utilizarse para generar una respuesta que obligue al navegador del usuario a descargar el archivo en la ruta indicada. El método `download` acepta un nombre de fichero como segundo argumento del método, que determinará el nombre de fichero que verá el usuario que descargue el fichero. Finalmente, puedes pasar un array de cabeceras HTTP como tercer argumento al método:

    return Storage::download('file.jpg');

    return Storage::download('file.jpg', $name, $headers);

[]()

### URL de archivos

Puede utilizar el método `url` para obtener la URL de un archivo determinado. Si está utilizando el controlador `local`, esto normalmente sólo añadirá `/storage` a la ruta dada y devolverá una URL relativa al archivo. Si estás utilizando el controlador `s3`, se devolverá la URL remota completa:

    use Illuminate\Support\Facades\Storage;

    $url = Storage::url('file.jpg');

Cuando se utiliza el controlador `local`, todos los archivos que deben ser accesibles al público deben colocarse en el directorio `storage/app/public`. Además, debes [crear un enlace simbólico](#the-public-disk) en `public/storage` que apunte al directorio `storage/app/public`.

> **Advertencia**  
> Cuando se utiliza el controlador `local`, el valor de retorno de `url` no está codificado como URL. Por esta razón, recomendamos almacenar siempre los archivos con nombres que creen URL válidas.

[]()

#### URL temporales

Usando el método `temporaryUrl`, puedes crear URLs temporales a archivos almacenados usando el controlador `s3`. Este método acepta una ruta y una instancia `DateTime` que especifica cuándo debe expirar la URL:

    use Illuminate\Support\Facades\Storage;

    $url = Storage::temporaryUrl(
        'file.jpg', now()->addMinutes(5)
    );

Si necesitas especificar [parámetros de petición S3](https://docs.aws.amazon.com/AmazonS3/latest/API/RESTObjectGET.html#RESTObjectGET-requests) adicionales, puedes pasar el array de parámetros de petición como tercer argumento al método `temporaryUrl`:

    $url = Storage::temporaryUrl(
        'file.jpg',
        now()->addMinutes(5),
        [
            'ResponseContentType' => 'application/octet-stream',
            'ResponseContentDisposition' => 'attachment; filename=file2.jpg',
        ]
    );

Si necesita personalizar cómo se crean las URL temporales para un disco de almacenamiento específico, puede utilizar el método `buildTemporaryUrlsUsing`. Por ejemplo, esto puede ser útil si tiene un controlador que le permite descargar archivos almacenados a través de un disco que normalmente no soporta URLs temporales. Normalmente, este método debe ser llamado desde el método de `arranque` de un proveedor de servicios:

    <?php

    namespace App\Providers;

    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\URL;
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
            Storage::disk('local')->buildTemporaryUrlsUsing(function ($path, $expiration, $options) {
                return URL::temporarySignedRoute(
                    'files.download',
                    $expiration,
                    array_merge($options, ['path' => $path])
                );
            });
        }
    }

[]()

#### Personalización del Host URL

Si desea predefinir el host para las URLs generadas utilizando la facade `Almacenamiento`, puede añadir una opción `url` al array configuración del disco:

    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],

[]()

### Metadatos de archivos

Además de leer y escribir ficheros, Laravel también puede proporcionar información sobre los propios ficheros. Por ejemplo, el método `size` puede utilizarse para obtener el tamaño de un fichero en bytes:

    use Illuminate\Support\Facades\Storage;

    $size = Storage::size('file.jpg');

El método `lastModified` devuelve la marca de tiempo UNIX de la última vez que se modificó el fichero:

    $time = Storage::lastModified('file.jpg');

[]()

#### Rutas de archivos

Puede utilizar el método `path` para obtener la ruta de un archivo determinado. Si utiliza el controlador `local`, obtendrá la ruta absoluta del archivo. Si está utilizando el controlador `s3`, este método devolverá la ruta relativa al archivo en el bucket S3:

    use Illuminate\Support\Facades\Storage;

    $path = Storage::path('file.jpg');

[]()

## Almacenamiento de archivos

El método `put` puede utilizarse para almacenar el contenido de un archivo en un disco. También puede pasar un `recurso` PHP al método `put`, que utilizará el soporte de flujo subyacente de Flysystem. Recuerde que todas las rutas de archivos deben especificarse en relación con la ubicación "raíz" configurada para el disco:

    use Illuminate\Support\Facades\Storage;

    Storage::put('file.jpg', $contents);

    Storage::put('file.jpg', $resource);

[]()

#### Escrituras fallidas

Si el método `put` (u otras operaciones de "escritura") no puede escribir el archivo en el disco, devolverá `false`:

    if (! Storage::put('file.jpg', $contents)) {
        // The file could not be written to disk...
    }

Si lo desea, puede definir la opción `throw` dentro de la array configuración del disco de su sistema de ficheros. Cuando esta opción se define como `true`, los métodos de "escritura" como `put` lanzarán una instancia de `League\Flysystem\UnableToWriteFile` cuando fallen las operaciones de escritura:

    'public' => [
        'driver' => 'local',
        // ...
        'throw' => true,
    ],

[]()

### Anexar y anexar archivos

Los métodos `prepend` y `append` permiten escribir al principio o al final de un fichero:

    Storage::prepend('file.log', 'Prepended Text');

    Storage::append('file.log', 'Appended Text');

[]()

### Copiar y mover archivos

El método `copy` se puede utilizar para copiar un archivo existente a una nueva ubicación en el disco, mientras que el método `move` se puede utilizar para renombrar o mover un archivo existente a una nueva ubicación:

    Storage::copy('old/file.jpg', 'new/file.jpg');

    Storage::move('old/file.jpg', 'new/file.jpg');

[]()

### Streaming automático

El streaming de archivos al almacenamiento ofrece un uso de memoria significativamente reducido. Si desea que Laravel gestione automáticamente la transmisión de un archivo determinado a su ubicación de almacenamiento, puede utilizar el método `putFile` o `putFileAs`. Este método acepta una instancia `Illuminate\Http\File` o `Illuminate\Http\UploadedFile` y automáticamente transmitirá el archivo a la ubicación deseada:

    use Illuminate\Http\File;
    use Illuminate\Support\Facades\Storage;

    // Automatically generate a unique ID for filename...
    $path = Storage::putFile('photos', new File('/path/to/photo'));

    // Manually specify a filename...
    $path = Storage::putFileAs('photos', new File('/path/to/photo'), 'photo.jpg');

Hay algunas cosas importantes a tener en cuenta sobre el método `putFile`. Tenga en cuenta que sólo especificamos un nombre de directorio y no un nombre de archivo. Por defecto, el método `putFile` generará un ID único que servirá como nombre de fichero. La extensión del archivo se determinará examinando el tipo MIME del archivo. La ruta al archivo será devuelta por el método `putFile` para que pueda almacenar la ruta, incluyendo el nombre de archivo generado, en su base de datos.

Los métodos `putFile` y `putFileAs` también aceptan un argumento para especificar la "visibilidad" del archivo almacenado. Esto es particularmente útil si está almacenando el archivo en un disco en la nube como Amazon S3 y desea que el archivo sea accesible públicamente a través de URLs generadas:

    Storage::putFile('photos', new File('/path/to/photo'), 'public');

[]()

### Carga de archivos

En las aplicaciones web, uno de los casos de uso más comunes para el almacenamiento de archivos es el almacenamiento de archivos subidos por el usuario, tales como fotos y documentos. Laravel hace que sea muy fácil almacenar los archivos subidos utilizando el método `store` en una instancia de archivo subido. Llama al método `store` con la ruta en la que deseas almacenar el archivo subido:

    <?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    class UserAvatarController extends Controller
    {
        /**
         * Update the avatar for the user.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
        public function update(Request $request)
        {
            $path = $request->file('avatar')->store('avatars');

            return $path;
        }
    }

Hay algunas cosas importantes a tener en cuenta en este ejemplo. Tenga en cuenta que sólo especificamos un nombre de directorio, no un nombre de archivo. Por defecto, el método `store` generará un ID único que servirá como nombre de fichero. La extensión del archivo se determinará examinando el tipo MIME del archivo. La ruta al archivo será devuelta por el método `store` para que pueda almacenar la ruta, incluyendo el nombre de archivo generado, en su base de datos.

También puede llamar al método `putFile` en la facade `Storage` para realizar la misma operación de almacenamiento de archivos que en el ejemplo anterior:

    $path = Storage::putFile('avatars', $request->file('avatar'));

[]()

#### Especificación de un Nombre de Fichero

Si no desea que se asigne automáticamente un nombre de archivo a su archivo almacenado, puede utilizar el método `storeAs`, que recibe la ruta, el nombre de archivo y el disco (opcional) como argumentos:

    $path = $request->file('avatar')->storeAs(
        'avatars', $request->user()->id
    );

También puede utilizar el método `putFileAs` en la facade `Storage`, que realizará la misma operación de almacenamiento de archivos que el ejemplo anterior:

    $path = Storage::putFileAs(
        'avatars', $request->file('avatar'), $request->user()->id
    );

> **Advertencia**  
> Los caracteres unicode no imprimibles e inválidos se eliminarán automáticamente de las rutas de los archivos. Por lo tanto, es posible que desees desinfectar tus rutas de archivo antes de pasarlas a los métodos de almacenamiento de archivos de Laravel. Las rutas de archivos se normalizan utilizando el método `League\Flysystem\WhitespacePathNormalizer::normalizePath`.

[]()

#### Especificación de un disco

Por defecto, el método `store` de este fichero subido utilizará su disco por defecto. Si desea especificar otro disco, pase el nombre del disco como segundo argumento al método `store`:

    $path = $request->file('avatar')->store(
        'avatars/'.$request->user()->id, 's3'
    );

Si está utilizando el método `storeAs`, puede pasar el nombre del disco como tercer argumento al método:

    $path = $request->file('avatar')->storeAs(
        'avatars',
        $request->user()->id,
        's3'
    );

[]()

#### Otra información del archivo cargado

Si desea obtener el nombre original y la extensión del archivo subido, puede hacerlo utilizando los métodos `getClientOriginalName` y `getClientOriginalExtension`:

    $file = $request->file('avatar');

    $name = $file->getClientOriginalName();
    $extension = $file->getClientOriginalExtension();

Sin embargo, tenga en cuenta que los métodos `getClientOriginalName` y `getClientOriginalExtension` se consideran inseguros, ya que el nombre y la extensión del archivo pueden ser manipulados por un usuario malintencionado. Por esta razón, normalmente deberías preferir los métodos `hashName` y `extension` para obtener un nombre y una extensión para la subida del archivo dado:

    $file = $request->file('avatar');

    $name = $file->hashName(); // Generate a unique, random name...
    $extension = $file->extension(); // Determine the file's extension based on the file's MIME type...

[]()

### Visibilidad de archivos

En la integración Flysystem de Laravel, la "visibilidad" es una abstracción de los permisos de archivos a través de múltiples plataformas. Los archivos pueden ser declarados `públicos` o `privados`. Cuando un archivo se declara `público`, se está indicando que el archivo debe ser generalmente accesible a los demás. Por ejemplo, cuando se utiliza el controlador S3, puede recuperar URLs para archivos `públicos`.

Puede establecer la visibilidad al escribir el archivo mediante el método `put`:

    use Illuminate\Support\Facades\Storage;

    Storage::put('file.jpg', $contents, 'public');

Si el archivo ya ha sido almacenado, su visibilidad puede ser recuperada y establecida mediante los métodos `getVisibility` y `setVisibility`:

    $visibility = Storage::getVisibility('file.jpg');

    Storage::setVisibility('file.jpg', 'public');

Al interactuar con archivos subidos, puede utilizar los métodos `storePublicly` y `storePubliclyAs` para almacenar el archivo subido con visibilidad `pública`:

    $path = $request->file('avatar')->storePublicly('avatars', 's3');

    $path = $request->file('avatar')->storePubliclyAs(
        'avatars',
        $request->user()->id,
        's3'
    );

[]()

#### Archivos locales y visibilidad

Cuando se utiliza el controlador `local`, [la visibilidad](#file-visibility) `pública` se traduce en permisos `0755` para directorios y permisos `0644` para archivos. Puedes modificar las asignaciones de permisos en el archivo de configuración de `sistemas de archivos` de tu aplicación:

    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
        'permissions' => [
            'file' => [
                'public' => 0644,
                'private' => 0600,
            ],
            'dir' => [
                'public' => 0755,
                'private' => 0700,
            ],
        ],
    ],

[]()

## Eliminación de archivos

El método `delete` acepta un único nombre de fichero o un array de ficheros a borrar:

    use Illuminate\Support\Facades\Storage;

    Storage::delete('file.jpg');

    Storage::delete(['file.jpg', 'file2.jpg']);

Si es necesario, puede especificar el disco del que debe borrarse el fichero:

    use Illuminate\Support\Facades\Storage;

    Storage::disk('s3')->delete('path/file.jpg');

[]()

## Directorios

[]()

#### Obtener todos los ficheros de un directorio

El método `files` devuelve una array con todos los ficheros de un directorio determinado. Si desea obtener una lista de todos los ficheros de un directorio determinado, incluidos todos los subdirectorios, puede utilizar el método `allFiles`:

    use Illuminate\Support\Facades\Storage;

    $files = Storage::files($directory);

    $files = Storage::allFiles($directory);

[]()

#### Obtener todos los directorios de un directorio

El método `directories` devuelve un array de todos los directorios de un directorio dado. Además, puedes utilizar el método `allDirectories` para obtener una lista de todos los directorios dentro de un directorio dado y todos sus subdirectorios:

    $directories = Storage::directories($directory);

    $directories = Storage::allDirectories($directory);

[]()

#### Crear un directorio

El método `makeDirectory` creará el directorio dado, incluyendo cualquier subdirectorio necesario:

    Storage::makeDirectory($directory);

[]()

#### Borrar un directorio

Por último, el método `deleteDirectory` se puede utilizar para eliminar un directorio y todos sus archivos:

    Storage::deleteDirectory($directory);

[]()

## Sistemas de archivos personalizados

La integración Flysystem de Laravel proporciona soporte para varios "drivers" de fábrica; sin embargo, Flysystem no se limita a estos y tiene adaptadores para muchos otros sistemas de almacenamiento. Puedes crear un controlador personalizado si deseas utilizar uno de estos adaptadores adicionales en tu aplicación Laravel.

Para definir un sistema de ficheros personalizado necesitará un adaptador Flysystem. Vamos a añadir un adaptador de Dropbox mantenido por la comunidad a nuestro proyecto:

```shell
composer require spatie/flysystem-dropbox
```

A continuación, puedes registrar el controlador en el método de `arranque` de uno de los [proveedores de servicios](/docs/%7B%7Bversion%7D%7D/providers) de tu aplicación. Para ello, debes utilizar el método `extend` de la facade `Storage`:

    <?php

    namespace App\Providers;

    use Illuminate\Filesystem\FilesystemAdapter;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\ServiceProvider;
    use League\Flysystem\Filesystem;
    use Spatie\Dropbox\Client as DropboxClient;
    use Spatie\FlysystemDropbox\DropboxAdapter;

    class AppServiceProvider extends ServiceProvider
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
            Storage::extend('dropbox', function ($app, $config) {
                $adapter = new DropboxAdapter(new DropboxClient(
                    $config['authorization_token']
                ));

                return new FilesystemAdapter(
                    new Filesystem($adapter, $config),
                    $adapter,
                    $config
                );
            });
        }
    }

El primer argumento del método `extend` es el nombre del driver y el segundo es un closure que recibe las variables `$app` y `$config`. El closure debe devolver una instancia de `Illuminate\Filesystem\FilesystemAdapter`. La variable `$config` contiene los valores definidos en `config/filesystems.` php para el disco especificado.

Una vez que hayas creado y registrado el proveedor de servicios de la extensión, puedes usar el controlador `dropbox` en tu archivo de configuración `config/filesystems.` php.
