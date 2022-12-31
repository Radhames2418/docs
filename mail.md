# Correo

- [Introducción](#introduction)
  - [Configuración](#configuration)
  - [Requisitos previos del controlador](#driver-prerequisites)
  - [Configuración de Failover](#failover-configuration)
- [Generación de Mailables](#generating-mailables)
- [Escritura de Mailables](#writing-mailables)
  - [Configuración del remitente](#configuring-the-sender)
  - [Configuración de la vista](#configuring-the-view)
  - [Datos de la vista](#view-data)
  - [Adjuntos](#attachments)
  - [Anexos en línea](#inline-attachments)
  - [Objetos adjuntos](#attachable-objects)
  - [Encabezados](#headers)
  - [Etiquetas y Metadatos](#tags-and-metadata)
  - [Personalización del mensaje Symfony](#customizing-the-symfony-message)
- [Mailables Markdown](#markdown-mailables)
  - [Generación de mensajes Markdown](#generating-markdown-mailables)
  - [Escribir mensajes Markdown](#writing-markdown-messages)
  - [Personalización de los componentes](#customizing-the-components)
- [Envío de correo](#sending-mail)
  - [Cola de correo](#queueing-mail)
- [Renderización de Mailables](#rendering-mailables)
  - [Vista previa de los mensajes en el navegador](#previewing-mailables-in-the-browser)
- [Localización de Mailables](#localizing-mailables)
- [Pruebas de Mailables](#testing-mailables)
- [Correo y desarrollo local](#mail-and-local-development)
- [Eventos](#events)
- [Transportes personalizados](#custom-transports)
  - [Transportes Symfony adicionales](#additional-symfony-transports)

[]()

## Introducción

Enviar correo electrónico no tiene por qué ser complicado. Laravel proporciona una API de correo electrónico limpia y sencilla impulsada por el popular componente Symfony [Mailer](https://symfony.com/doc/6.0/mailer.html). Laravel y Symfony Mailer proporcionan controladores para el envío de correo electrónico a través de SMTP, Mailgun, Postmark, Amazon SES y `sendmail`, lo que te permite comenzar rápidamente a enviar correo a través de un servicio local o basado en la nube de tu elección.

[]()

### Configuración

Los servicios de correo electrónico de Laravel pueden ser configurados a través del archivo de configuración `config/mail.php` de tu aplicación. Cada mailer configurado dentro de este archivo puede tener su propia configuración única e incluso su propio "transporte" único, permitiendo a tu aplicación utilizar diferentes servicios de correo electrónico para enviar ciertos mensajes de correo electrónico. Por ejemplo, su aplicación puede utilizar Postmark para enviar correos electrónicos transaccionales y Amazon SES para enviar correos masivos.

Dentro de tu archivo de configuración de `correo`, encontrarás un array configuración de `mailers`. Este array contiene una entrada de configuración de ejemplo para cada uno de los principales controladores / transportes de correo soportados por Laravel, mientras que el valor de configuración `por defecto` determina qué mailer se utilizará por defecto cuando tu aplicación necesite enviar un mensaje de correo electrónico.

[]()

### Requisitos previos del controlador / transporte

Los controladores basados en API como Mailgun y Postmark suelen ser más sencillos y rápidos que el envío de correo a través de servidores SMTP. Siempre que sea posible, le recomendamos que utilice uno de estos controladores.

[]()

#### Controlador Mailgun

Para utilizar el controlador Mailgun, instala el transporte Mailgun Mailer de Symfony a través de Composer:

```shell
composer require symfony/mailgun-mailer symfony/http-client
```

Luego, establece la opción `predeterminada` en el archivo de configuración `config/mail.php` de tu aplicación a `mailgun`. Después de configurar el mailer predeterminado de tu aplicación, verifica que tu archivo de configuración `config/services.` php contenga las siguientes opciones:

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

Si no utiliza la [región Mailgun](https://documentation.mailgun.com/en/latest/api-intro.html#mailgun-regions) de Estados Unidos, puede definir el punto de enlace de su región en el archivo de configuración de `servicios`:

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.eu.mailgun.net'),
    ],

[]()

#### Controlador Postmark

Para utilizar el controlador Postmark, instala el transporte Postmark Mailer de Symfony a través de Composer:

```shell
composer require symfony/postmark-mailer symfony/http-client
```

A continuación, establezca la opción `predeterminada` en el archivo de configuración `config/mail.` php de su aplicación en `postmark`. Después de configurar el mailer predeterminado de tu aplicación, verifica que tu archivo de configuración `config/services`.php contenga las siguientes opciones:

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

Si desea especificar el flujo de mensajes de Postmark que debe utilizar un mailer determinado, puede añadir la opción de configuración `message_stream_id` a la array de configuración del mailer. Esta array de configuración se encuentra en el archivo de configuración `config/mail.php` de su aplicación:

    'postmark' => [
        'transport' => 'postmark',
        'message_stream_id' => env('POSTMARK_MESSAGE_STREAM_ID'),
    ],

De este modo, también podrá configurar varios emisores de Postmark con diferentes flujos de mensajes.

[]()

#### Controlador SES

Para utilizar el controlador de Amazon SES, primero debe instalar el SDK de Amazon AWS para PHP. Puede instalar esta biblioteca a través del gestor de paquetes Composer:

```shell
composer require aws/aws-sdk-php
```

A continuación, establezca la opción `predeterminada` de su archivo de configuración `config/mail.` php en `ses` y compruebe que su archivo de configuración `config/services.php` contiene las siguientes opciones:

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

Para utilizar [credenciales temporales](https://docs.aws.amazon.com/IAM/latest/UserGuide/id_credentials_temp_use-resources.html) de AWS mediante un token de sesión, puede añadir una clave de `token` a la configuración de SES de su aplicación:

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
        'token' => env('AWS_SESSION_TOKEN'),
    ],

Si quieres definir [opciones adicionales](https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sesv2-2019-09-27.html#sendemail) que Laravel deba pasar al método `SendEmail` del SDK de AWS cuando envíe un email, puedes definir un array `opciones` dentro de la configuración de `ses`:

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
        'options' => [
            'ConfigurationSetName' => 'MyConfigurationSet',
            'EmailTags' => [
                ['Name' => 'foo', 'Value' => 'bar'],
            ],
        ],
    ],

[]()

### Configuración de Failover

A veces, un servicio externo que hayas configurado para enviar el correo de tu aplicación puede estar caído. En estos casos, puede ser útil definir una o más configuraciones de entrega de correo de respaldo que se utilizarán en caso de que su controlador de entrega principal esté caído.

Para ello, debe definir un mailer dentro del archivo de configuración de `correo` de su aplicación que utilice el transporte de `conmutación por error`. La array configuración para el mailer de conmutación por `error` de su aplicación debe contener una array de `mailers` que hagan referencia al orden en que los controladores de correo deben ser elegidos para la entrega:

    'mailers' => [
        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'postmark',
                'mailgun',
                'sendmail',
            ],
        ],

        // ...
    ],

Una vez definido el emisor de correo de conmutación por error, debe establecerlo como el emisor de correo predeterminado utilizado por su aplicación especificando su nombre como valor de la clave de configuración `predeterminada` dentro del archivo de configuración de `correo` de su aplicación:

    'default' => env('MAIL_MAILER', 'failover'),

[]()

## Generando Mailables

Al construir aplicaciones Laravel, cada tipo de correo electrónico enviado por su aplicación se representa como una clase "mailable". Estas clases se almacenan en el directorio `app/Mail`. No te preocupes si no ves este directorio en tu aplicación, ya que será generado para ti cuando crees tu primera clase mailable usando el comando `make:mail` Artisan:

```shell
php artisan make:mail OrderShipped
```

[]()

## Escribiendo Mailables

Una vez que haya generado una clase mailable, ábrala para que podamos explorar su contenido. La configuración de la clase mailable se realiza en varios métodos, incluyendo los métodos de `sobre`, `contenido` y `adjuntos`.

El método `envelope` devuelve un objeto `Illuminate\Mail\Mailables\Envelope` que define el asunto y, a veces, los destinatarios del mensaje. El método `contenido` devuelve un objeto `Illuminate\Mail\Mailables\Content` que define la [plantilla Blade](/docs/%7B%7Bversion%7D%7D/blade) que se utilizará para generar el contenido del mensaje.

[]()

### Configuración del remitente

[]()

#### Uso del sobre

En primer lugar, vamos a explorar la configuración del remitente del mensaje de correo electrónico. O, en otras palabras, quién va a ser el "remitente" del correo electrónico. Hay dos formas de configurar el remitente. En primer lugar, puede especificar la dirección "de" en el sobre del mensaje:

    use Illuminate\Mail\Mailables\Address;
    use Illuminate\Mail\Mailables\Envelope;

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address('jeffrey@example.com', 'Jeffrey Way'),
            subject: 'Order Shipped',
        );
    }

Si lo desea, también puede especificar una dirección `replyTo`:

    return new Envelope(
        from: new Address('jeffrey@example.com', 'Jeffrey Way'),
        replyTo: [
            new Address('taylor@example.com', 'Taylor Otwell'),
        ],
        subject: 'Order Shipped',
    );

[]()

#### Uso de una dirección global `de`

Sin embargo, si su aplicación utiliza la misma dirección "de" para todos sus correos electrónicos, puede resultar engorroso llamar al método " `de` " en cada clase enviable que genere. En su lugar, puede especificar una dirección "from" global en su archivo de configuración `config/mail.php`. Esta dirección se utilizará si no se especifica ninguna otra dirección "from" dentro de la clase mailable:

    'from' => ['address' => 'example@example.com', 'name' => 'App Name'],

Además, puede definir una dirección global "reply_to" dentro de su archivo de configuración config/mail `.` php:

    'reply_to' => ['address' => 'example@example.com', 'name' => 'App Name'],

[]()

### Configuración de la vista

En el método de `contenido` de una clase mailable, puede definir la `vista` o la plantilla que se utilizará para mostrar el contenido del correo electrónico. Dado que cada correo electrónico suele utilizar una plantilla de [Blade](/docs/%7B%7Bversion%7D%7D/blade) para mostrar su contenido, usted dispone de toda la potencia y comodidad del motor de plantillas de Blade al crear el HTML de su correo electrónico:

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.orders.shipped',
        );
    }

> **Nota**  
> Es posible que desee crear un directorio `resources/views/emails` para alojar todas sus plantillas de correo electrónico; sin embargo, es libre de colocarlas donde desee dentro de su directorio `resources/views`.

[]()

#### Correos electrónicos de texto sin formato

Si desea definir una versión de texto sin formato de su correo electrónico, puede especificar la plantilla de texto sin formato al crear la definición de `contenido` del mensaje. Al igual que el parámetro de `vista`, el parámetro de `texto` debe ser un nombre de plantilla que se utilizará para representar el contenido del correo electrónico. Puede definir tanto una versión HTML como una versión en texto plano de su mensaje:

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.orders.shipped',
            text: 'emails.orders.shipped-text'
        );
    }

Para mayor claridad, el parámetro `html` puede utilizarse como alias del parámetro `view`:

    return new Content(
        html: 'emails.orders.shipped',
        text: 'emails.orders.shipped-text'
    );

[]()

### Datos de la vista

[]()

#### Mediante propiedades públicas

Normalmente, querrá pasar algunos datos a su vista que podrá utilizar cuando renderice el HTML del correo electrónico. Hay dos formas de poner datos a disposición de la vista. En primer lugar, cualquier propiedad pública definida en su clase mailable se pondrá automáticamente a disposición de la vista. Así, por ejemplo, puedes pasar datos al constructor de tu clase mailable y establecer esos datos en las propiedades públicas definidas en la clase:

    <?php

    namespace App\Mail;

    use App\Models\Order;
    use Illuminate\Bus\Queueable;
    use Illuminate\Mail\Mailable;
    use Illuminate\Mail\Mailables\Content;
    use Illuminate\Queue\SerializesModels;

    class OrderShipped extends Mailable
    {
        use Queueable, SerializesModels;

        /**
         * The order instance.
         *
         * @var \App\Models\Order
         */
        public $order;

        /**
         * Create a new message instance.
         *
         * @param  \App\Models\Order  $order
         * @return void
         */
        public function __construct(Order $order)
        {
            $this->order = $order;
        }

        /**
         * Get the message content definition.
         *
         * @return \Illuminate\Mail\Mailables\Content
         */
        public function content()
        {
            return new Content(
                view: 'emails.orders.shipped',
            );
        }
    }

Una vez que los datos se han establecido en una propiedad pública, estarán automáticamente disponibles en la vista, por lo que podrá acceder a ellos como accedería a cualquier otro dato de sus plantillas Blade:

    <div>
        Price: {{ $order->price }}
    </div>

[]()

#### A través del parámetro `con`:

Si desea personalizar el formato de los datos de su correo electrónico antes de que se envíen a la plantilla, puede pasar manualmente sus datos a la vista a través del parámetro `with` de la definición de `contenido`. Típicamente, aún pasará los datos a través del constructor de la clase mailable; sin embargo, debe establecer estos datos como propiedades `protegidas` o `privadas` para que los datos no estén disponibles automáticamente para la plantilla:

    <?php

    namespace App\Mail;

    use App\Models\Order;
    use Illuminate\Bus\Queueable;
    use Illuminate\Mail\Mailable;
    use Illuminate\Mail\Mailables\Content;
    use Illuminate\Queue\SerializesModels;

    class OrderShipped extends Mailable
    {
        use Queueable, SerializesModels;

        /**
         * The order instance.
         *
         * @var \App\Models\Order
         */
        protected $order;

        /**
         * Create a new message instance.
         *
         * @param  \App\Models\Order  $order
         * @return void
         */
        public function __construct(Order $order)
        {
            $this->order = $order;
        }

        /**
         * Get the message content definition.
         *
         * @return \Illuminate\Mail\Mailables\Content
         */
        public function content()
        {
            return new Content(
                view: 'emails.orders.shipped',
                with: [
                    'orderName' => $this->order->name,
                    'orderPrice' => $this->order->price,
                ],
            );
        }
    }

Una vez que los datos han sido pasados al método `with`, estarán automáticamente disponibles en su vista, por lo que podrá acceder a ellos como accedería a cualquier otro dato en sus plantillas Blade:

    <div>
        Price: {{ $orderPrice }}
    </div>

[]()

### Adjuntos

Para añadir archivos adjuntos a un mensaje de correo electrónico, añada los archivos adjuntos a la array devuelta por el método `attachments` del mensaje. En primer lugar, puede añadir un archivo adjunto proporcionando una ruta de archivo al método `fromPath` proporcionado por la clase `Attachment`:

    use Illuminate\Mail\Mailables\Attachment;

    /**
     * Get the attachments for the message.
     *
     * @return \Illuminate\Mail\Mailables\Attachment[]
     */
    public function attachments()
    {
        return [
            Attachment::fromPath('/path/to/file'),
        ];
    }

Al adjuntar archivos a un mensaje, también puede especificar el nombre para mostrar y/o el tipo MIME para el archivo adjunto utilizando los métodos `as` y `withMime`:

    /**
     * Get the attachments for the message.
     *
     * @return \Illuminate\Mail\Mailables\Attachment[]
     */
    public function attachments()
    {
        return [
            Attachment::fromPath('/path/to/file')
                    ->as('name.pdf')
                    ->withMime('application/pdf'),
        ];
    }

[]()

#### Adjuntar archivos desde el disco

Si ha almacenado un archivo en uno de los [discos de](/docs/%7B%7Bversion%7D%7D/filesystem) su [sistema de archivos](/docs/%7B%7Bversion%7D%7D/filesystem), puede adjuntarlo al correo electrónico utilizando el método de adjunto `fromStorage`:

    /**
     * Get the attachments for the message.
     *
     * @return \Illuminate\Mail\Mailables\Attachment[]
     */
    public function attachments()
    {
        return [
            Attachment::fromStorage('/path/to/file'),
        ];
    }

Por supuesto, también puede especificar el nombre y el tipo MIME del archivo adjunto:

    /**
     * Get the attachments for the message.
     *
     * @return \Illuminate\Mail\Mailables\Attachment[]
     */
    public function attachments()
    {
        return [
            Attachment::fromStorage('/path/to/file')
                    ->as('name.pdf')
                    ->withMime('application/pdf'),
        ];
    }

El método `fromStorageDisk` puede utilizarse si necesita especificar un disco de almacenamiento distinto de su disco predeterminado:

    /**
     * Get the attachments for the message.
     *
     * @return \Illuminate\Mail\Mailables\Attachment[]
     */
    public function attachments()
    {
        return [
            Attachment::fromStorageDisk('s3', '/path/to/file')
                    ->as('name.pdf')
                    ->withMime('application/pdf'),
        ];
    }

[]()

#### Archivos adjuntos de datos sin procesar

El método `fromData` puede utilizarse para adjuntar una cadena de bytes sin procesar. Por ejemplo, puede utilizar este método si ha generado un PDF en memoria y desea adjuntarlo al correo electrónico sin escribirlo en el disco. El método `fromData` acepta un closure que resuelve los bytes de datos en bruto, así como el nombre que debe asignarse al archivo adjunto:

    /**
     * Get the attachments for the message.
     *
     * @return \Illuminate\Mail\Mailables\Attachment[]
     */
    public function attachments()
    {
        return [
            Attachment::fromData(fn () => $this->pdf, 'Report.pdf')
                    ->withMime('application/pdf'),
        ];
    }

[]()

### Anexos en línea

Incrustar imágenes en línea en sus correos electrónicos es típicamente engorroso; sin embargo, Laravel proporciona una manera conveniente de adjuntar imágenes a sus correos electrónicos. Para incrustar una imagen en línea, utilice el método `embed` en la variable `$message` dentro de su plantilla de correo electrónico. Laravel automáticamente hace que la variable `$message` esté disponible en todas sus plantillas de correo electrónico, por lo que no necesita preocuparse de pasarla manualmente:

```blade
<body>
    Here is an image:

    <img src="{{ $message->embed($pathToImage) }}">
</body>
```

> **Advertencia**  
> La variable `$message` no está disponible en plantillas de mensajes de texto plano ya que los mensajes de texto plano no utilizan adjuntos en línea.

[]()

#### Cómo incrustar datos adjuntos sin procesar

Si ya tiene una cadena de datos de imagen sin procesar que desea incrustar en una plantilla de correo electrónico, puede llamar al método `embedData` en la variable `$message`. Al llamar al método `embedData`, deberá proporcionar un nombre de archivo que se asignará a la imagen incrustada:

```blade
<body>
    Here is an image from raw data:

    <img src="{{ $message->embedData($data, 'example-image.jpg') }}">
</body>
```

[]()

### Objetos adjuntos

Mientras que adjuntar archivos a los mensajes a través de simples rutas de cadena es a menudo suficiente, en muchos casos las entidades adjuntables dentro de su aplicación están representadas por clases. Por ejemplo, si tu aplicación está adjuntando una foto a un mensaje, tu aplicación también puede tener un modelo de `Foto` que represente esa foto. En ese caso, ¿no sería conveniente simplemente pasar el modelo `Photo` al método `attach`? Los objetos adjuntables te permiten hacer precisamente eso.

Para empezar, implemente la interfaz `Illuminate\Contracts\Mail\Attachable` en el objeto que se adjuntará a los mensajes. Esta interfaz dicta que su clase define un método `toMailAttachment` que devuelve una instancia `Illuminate\Mail\Attachment`:

    <?php

    namespace App\Models;

    use Illuminate\Contracts\Mail\Attachable;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Mail\Attachment;

    class Photo extends Model implements Attachable
    {
        /**
         * Get the attachable representation of the model.
         *
         * @return \Illuminate\Mail\Attachment
         */
        public function toMailAttachment()
        {
            return Attachment::fromPath('/path/to/file');
        }
    }

Una vez que hayas definido tu objeto adjuntable, puedes devolver una instancia de ese objeto desde el método `attachments` cuando construyas un mensaje de correo electrónico:

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [$this->photo];
    }

Por supuesto, los datos adjuntos pueden almacenarse en un servicio de almacenamiento de archivos remoto como Amazon S3. Por lo tanto, Laravel también te permite generar instancias de adjuntos a partir de datos que se almacenan en uno de los [discos del sistema de archivos](/docs/%7B%7Bversion%7D%7D/filesystem) de tu aplicación:

    // Create an attachment from a file on your default disk...
    return Attachment::fromStorage($this->path);

    // Create an attachment from a file on a specific disk...
    return Attachment::fromStorageDisk('backblaze', $this->path);

Además, puede crear instancias de adjuntos a través de datos que tenga en memoria. Para ello, proporcione un closure al método `fromData`. El closure debe devolver los datos en bruto que representan el archivo adjunto:

    return Attachment::fromData(fn () => $this->content, 'Photo Name');

Laravel también proporciona métodos adicionales que puedes utilizar para personalizar tus adjuntos. Por ejemplo, puede utilizar los métodos `as` y `withMime` para personalizar el nombre del archivo y el tipo MIME:

    return Attachment::fromPath('/path/to/file')
            ->as('Photo Name')
            ->withMime('image/jpeg');

[]()

### Cabeceras

A veces puede necesitar adjuntar cabeceras adicionales al mensaje saliente. Por ejemplo, puedes necesitar establecer un `Message-Id` personalizado u otras cabeceras de texto arbitrarias.

Para ello, define un método `headers` en tu mailable. El método `headers` debe devolver una instancia `Illuminate\Mail\Mailables\Headers`. Esta clase acepta `messageId`, `referencias` y parámetros de `texto`. Por supuesto, puede proporcionar sólo los parámetros que necesita para su mensaje en particular:

    use Illuminate\Mail\Mailables\Headers;

    /**
     * Get the message headers.
     *
     * @return \Illuminate\Mail\Mailables\Headers
     */
    public function headers()
    {
        return new Headers(
            messageId: 'custom-message-id@example.com',
            references: ['previous-message@example.com'],
            text: [
                'X-Custom-Header' => 'Custom Value',
            ],
        );
    }

[]()

### Etiquetas y metadatos

Algunos proveedores de correo electrónico de terceros, como Mailgun y Postmark, admiten "etiquetas" y "metadatos" de mensajes, que pueden utilizarse para agrupar y realizar un seguimiento de los correos electrónicos enviados por su aplicación. Puede añadir etiquetas y metadatos a un mensaje de correo electrónico a través de su definición de `sobre`:

    use Illuminate\Mail\Mailables\Envelope;

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Order Shipped',
            tags: ['shipment'],
            metadata: [
                'order_id' => $this->order->id,
            ],
        );
    }

Si su aplicación utiliza el controlador Mailgun, puede consultar la documentación de Mailgun para obtener más información sobre [etiquetas](https://documentation.mailgun.com/en/latest/user_manual.html#tagging-1) y [metadatos](https://documentation.mailgun.com/en/latest/user_manual.html#attaching-data-to-messages). Del mismo modo, también puede consultar la documentación de Postmark para obtener más información sobre su compatibilidad con [etiquetas](https://postmarkapp.com/blog/tags-support-for-smtp) y [metadatos](https://postmarkapp.com/support/article/1125-custom-metadata-faq).

Si su aplicación utiliza Amazon SES para enviar correos electrónicos, debe utilizar el método de `metadatos` para adjuntar ["etiquetas" SES](https://docs.aws.amazon.com/ses/latest/APIReference/API_MessageTag.html) al mensaje.

[]()

### Personalización del mensaje Symfony

Las capacidades de correo de Laravel son impulsadas por Symfony Mailer. Laravel te permite registrar callbacks personalizados que serán invocados con la instancia de Symfony Message antes de enviar el mensaje. Esto te da la oportunidad de personalizar profundamente el mensaje antes de que sea enviado. Para lograrlo, define un parámetro `using` en tu definición de `Envelope`:

    use Illuminate\Mail\Mailables\Envelope;
    use Symfony\Component\Mime\Email;

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Order Shipped',
            using: [
                function (Email $message) {
                    // ...
                },
            ]
        );
    }

[]()

## Mailables Markdown

Los mensajes Markdown mailable le permiten aprovechar las plantillas y componentes pre-construidos de las [notificaciones de correo](/docs/%7B%7Bversion%7D%7D/notifications#mail-notifications) en sus mailables. Dado que los mensajes están escritos en Markdown, Laravel es capaz de renderizar hermosas plantillas HTML responsivas para los mensajes, mientras que también genera automáticamente una contraparte de texto plano.

[]()

### Generación de mensajes Markdown

Para generar un mailable con una plantilla Markdown correspondiente, puedes utilizar la opción `--markdown` del comando `make:mail` Artisan:

```shell
php artisan make:mail OrderShipped --markdown=emails.orders.shipped
```

Luego, cuando configures la definición de `Contenido` mailable dentro de su método de `contenido`, utiliza el parámetro `markdown` en lugar del parámetro `view`:

    use Illuminate\Mail\Mailables\Content;

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.orders.shipped',
            with: [
                'url' => $this->orderUrl,
            ],
        );
    }

[]()

### Escribir mensajes Markdown

Los mailables Markdown utilizan una combinación de componentes Blade y sintaxis Markdown que te permiten construir fácilmente mensajes de correo mientras aprovechas los componentes de interfaz de usuario de correo electrónico pre-construidos de Laravel:

```blade
<x-mail::message>
# Order Shipped

Your order has been shipped!

<x-mail::button :url="$url">
View Order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
```

> **Nota**  
> No utilice sangría excesiva al escribir mensajes de correo electrónico Markdown. Según los estándares de Markdown, los analizadores sintácticos de Markdown renderizarán el contenido sangrado como bloques de código.

[]()

#### Componente de botón

El componente Button muestra un enlace de botón centrado. El componente acepta dos argumentos, una `url` y un `color` opcional. Los colores admitidos son `primario`, `éxito` y `error`. Puede añadir tantos componentes de botón a un mensaje como desee:

```blade
<x-mail::button :url="$url" color="success">
View Order
</x-mail::button>
```

[]()

#### Componente de panel

El componente panel muestra el bloque de texto en un panel con un color de fondo ligeramente diferente al del resto del mensaje. Esto le permite llamar la atención sobre un bloque de texto determinado:

```blade
<x-mail::panel>
This is the panel content.
</x-mail::panel>
```

[]()

#### Componente de tabla

El componente tabla permite transformar una tabla Markdown en una tabla HTML. El componente acepta la tabla Markdown como contenido. La alineación de las columnas de la tabla se realiza utilizando la sintaxis predeterminada de alineación de tablas de Markdown:

```blade
<x-mail::table>
| Laravel       | Table         | Example  |
| ------------- |:-------------:| --------:|
| Col 2 is      | Centered      | $10      |
| Col 3 is      | Right-Aligned | $20      |
</x-mail::table>
```

[]()

### Personalización de los componentes

Puede exportar todos los componentes de correo Markdown a su propia aplicación para personalizarlos. Para exportar los componentes, utilice el comando `vendor:publish` Artisan para publicar la etiqueta asset `laravel-mail`:

```shell
php artisan vendor:publish --tag=laravel-mail
```

Este comando publicará los componentes de correo Markdown en el directorio `resources/views/vendor/mail`. El directorio `mail` contendrá un directorio `html` y un directorio `text`, cada uno con sus respectivas representaciones de cada componente disponible. Puede personalizar estos componentes como desee.

[]()

#### Personalizar el CSS

Tras exportar los componentes, el directorio `resources/views/vendor/mail/html/themes` contendrá un archivo `default.css`. Puede personalizar el CSS de este archivo y sus estilos se convertirán automáticamente en estilos CSS en línea dentro de las representaciones HTML de sus mensajes de correo Markdown.

Si desea crear un tema completamente nuevo para los componentes Markdown de Laravel, puede colocar un archivo CSS en el directorio `html/themes`. Después de nombrar y guardar tu archivo CSS, actualiza la opción `theme` del archivo de configuración `config/mail.php` de tu aplicación para que coincida con el nombre de tu nuevo tema.

Para personalizar el tema de un mailable individual, puede establecer la propiedad `$theme` de la clase mailable con el nombre del tema que debe utilizarse al enviar ese mailable.

[]()

## Envío de correo

Para enviar un mensaje, utilice el método `to` en la [facade](/docs/%7B%7Bversion%7D%7D/facades) `Mail`. El método `to` acepta una dirección de correo electrónico, una instancia de usuario o una colección de usuarios. Si pasas un objeto o una colección de objetos, el mailer utilizará automáticamente sus propiedades `email` y `name` cuando determine los destinatarios del correo, así que asegúrate de que estos atributos están disponibles en tus objetos. Una vez que haya especificado sus destinatarios, puede pasar una instancia de su clase mailable al método `send`:

    <?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;
    use App\Mail\OrderShipped;
    use App\Models\Order;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Mail;

    class OrderShipmentController extends Controller
    {
        /**
         * Ship the given order.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
        public function store(Request $request)
        {
            $order = Order::findOrFail($request->order_id);

            // Ship the order...

            Mail::to($request->user())->send(new OrderShipped($order));
        }
    }

No estás limitado a especificar únicamente los destinatarios "to" cuando envías un mensaje. Puedes establecer los destinatarios "to", "cc" y "bcc" encadenando sus respectivos métodos:

    Mail::to($request->user())
        ->cc($moreUsers)
        ->bcc($evenMoreUsers)
        ->send(new OrderShipped($order));

[]()

#### Bucle sobre destinatarios

Ocasionalmente, puede necesitar enviar un mailable a una lista de destinatarios iterando sobre un array de destinatarios / direcciones de correo electrónico. Sin embargo, dado que el método `to` añade direcciones de correo electrónico a la lista de destinatarios del mailable, cada iteración a través del bucle enviará otro correo electrónico a cada destinatario anterior. Por lo tanto, siempre debe volver a crear la instancia de mailable para cada destinatario:

    foreach (['taylor@example.com', 'dries@example.com'] as $recipient) {
        Mail::to($recipient)->send(new OrderShipped($order));
    }

[]()

#### Envío de correo a través de un mailer específico

Por defecto, Laravel enviará el correo electrónico utilizando el mailer configurado como `predeterminado` en el fichero de configuración de `correo` de tu aplicación. Sin embargo, puede utilizar el método `mailer` para enviar un mensaje utilizando una configuración de mailer específica:

    Mail::mailer('postmark')
            ->to($request->user())
            ->send(new OrderShipped($order));

[]()

### Cola de correo

[]()

#### Puesta en cola de un mensaje de correo

Dado que el envío de mensajes de correo electrónico puede afectar negativamente al tiempo de respuesta de tu aplicación, muchos desarrolladores optan por poner en cola los mensajes de correo electrónico para enviarlos en segundo plano. Laravel hace esto fácil usando su [API de cola unificada](/docs/%7B%7Bversion%7D%7D/queues) incorporada. Para poner en cola un mensaje de correo, utilice el método `queue` en la facade `Mail` después de especificar los destinatarios del mensaje:

    Mail::to($request->user())
        ->cc($moreUsers)
        ->bcc($evenMoreUsers)
        ->queue(new OrderShipped($order));

Este método se encargará automáticamente de empujar un trabajo a la cola para que el mensaje se envíe en segundo plano. Deberá [configurar sus colas](/docs/%7B%7Bversion%7D%7D/queues) antes de utilizar esta función.

[]()

#### Puesta en cola de mensajes diferida

Si desea retrasar la entrega de un mensaje de correo electrónico en cola, puede utilizar el método `later`. Como primer argumento, el método `later` acepta una instancia `DateTime` que indica cuándo debe enviarse el mensaje:

    Mail::to($request->user())
        ->cc($moreUsers)
        ->bcc($evenMoreUsers)
        ->later(now()->addMinutes(10), new OrderShipped($order));

[]()

#### Envío a colas específicas

Dado que todas las clases mailables generadas utilizando el comando `make:` mail hacen uso del rasgo `Illuminate\Bus\Queueable`, puede llamar a los métodos `onQueue` y `onConnection` en cualquier instancia de clase mailable, permitiéndole especificar la conexión y el nombre de la cola para el mensaje:

    $message = (new OrderShipped($order))
                    ->onConnection('sqs')
                    ->onQueue('emails');

    Mail::to($request->user())
        ->cc($moreUsers)
        ->bcc($evenMoreUsers)
        ->queue($message);

[]()

#### Puesta en cola por defecto

Si tienes clases enviables que quieres que siempre estén en cola, puedes implementar el contrato `ShouldQueue` en la clase. Ahora, incluso si llama al método `send` al enviar, el mailable seguirá en cola ya que implementa el contrato:

    use Illuminate\Contracts\Queue\ShouldQueue;

    class OrderShipped extends Mailable implements ShouldQueue
    {
        //
    }

[]()

#### Mailables en cola y transacciones de base de datos

Cuando los mailables en cola se envían dentro de transacciones de base de datos, pueden ser procesados por la cola antes de que la transacción de base de datos se haya confirmado. Cuando esto ocurre, cualquier actualización que haya realizado en los modelos o registros de la base de datos durante la transacción de la base de datos puede no reflejarse aún en la base de datos. Además, es posible que los modelos o registros de base de datos creados durante la transacción no existan en la base de datos. Si su mailable depende de estos modelos, pueden producirse errores inesperados cuando se procese el trabajo que envía el mailable en cola.

Si la opción de configuración `after_commit` de su conexión de cola está establecida en `false`, aún puede indicar que un mailable en cola particular debe ser enviado después de que todas las transacciones de base de datos abiertas hayan sido confirmadas llamando al método `afterCommit` cuando envíe el mensaje de correo:

    Mail::to($request->user())->send(
        (new OrderShipped($order))->afterCommit()
    );

Alternativamente, puede llamar al método `afterCommit` desde el constructor de su mailable:

    <?php

    namespace App\Mail;

    use Illuminate\Bus\Queueable;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Mail\Mailable;
    use Illuminate\Queue\SerializesModels;

    class OrderShipped extends Mailable implements ShouldQueue
    {
        use Queueable, SerializesModels;

        /**
         * Create a new message instance.
         *
         * @return void
         */
        public function __construct()
        {
            $this->afterCommit();
        }
    }

> **Nota**  
> Para obtener más información sobre cómo solucionar estos problemas, consulte la documentación relativa a los trabajos en cola [y las transacciones de base de datos](/docs/%7B%7Bversion%7D%7D/queues#jobs-and-database-transactions).

[]()

## Renderización de Mailables

A veces puede que desee capturar el contenido HTML de un mailable sin enviarlo. Para ello, puede llamar al método `render` del mailable. Este método devolverá el contenido HTML evaluado del mailable como una cadena:

    use App\Mail\InvoicePaid;
    use App\Models\Invoice;

    $invoice = Invoice::find(1);

    return (new InvoicePaid($invoice))->render();

[]()

### Vista previa de los mensajes en el navegador

Cuando se diseña la plantilla de un mailable, es conveniente previsualizar rápidamente el mailable renderizado en el navegador como una plantilla típica de Blade. Por esta razón, Laravel permite devolver cualquier mailable directamente desde un closure ruta o controlador. Cuando un mailable es devuelto, será renderizado y mostrado en el navegador, permitiéndote previsualizar rápidamente su diseño sin necesidad de enviarlo a una dirección de correo real:

    Route::get('/mailable', function () {
        $invoice = App\Models\Invoice::find(1);

        return new App\Mail\InvoicePaid($invoice);
    });

> **Advertencia**  
> [Los archivos adjuntos en línea](#inline-attachments) no se mostrarán cuando se previsualice un mailable en el navegador. Para previsualizar estos mailables, debe enviarlos a una aplicación de prueba de correo electrónico como [MailHog](https://github.com/mailhog/MailHog) o [HELO](https://usehelo.com).

[]()

## Localización de Mailables

Laravel permite enviar mailables en una configuración regional distinta de la configuración regional actual de la solicitud, e incluso recordará esta configuración regional si el correo se pone en cola.

Para ello, la facade `Mail` ofrece un método `locale` para establecer el idioma deseado. La aplicación cambiará a esta configuración regional cuando se esté evaluando la plantilla del mailable y volverá a la configuración regional anterior cuando la evaluación haya finalizado:

    Mail::to($request->user())->locale('es')->send(
        new OrderShipped($order)
    );

[]()

### Idiomas preferidos por el usuario

A veces, las aplicaciones almacenan la configuración regional preferida de cada usuario. Implementando el contrato `HasLocalePreference` en uno o más de sus modelos, puede indicar a Laravel que utilice esta configuración regional almacenada al enviar correo:

    use Illuminate\Contracts\Translation\HasLocalePreference;

    class User extends Model implements HasLocalePreference
    {
        /**
         * Get the user's preferred locale.
         *
         * @return string
         */
        public function preferredLocale()
        {
            return $this->locale;
        }
    }

Una vez que haya implementado la interfaz, Laravel utilizará automáticamente la configuración regional preferida al enviar mailables y notificaciones al modelo. Por lo tanto, no hay necesidad de llamar al método `locale` cuando se utiliza esta interfaz:

    Mail::to($request->user())->send(new OrderShipped($order));

[]()

## Pruebas de Mailables

Laravel proporciona una variedad de métodos para inspeccionar la estructura de tu mailable. Además, Laravel proporciona varios métodos convenientes para comprobar que tu mailable contiene el contenido que esperas. Estos métodos son: `assertSeeInHtml`, `assertDontSeeInHtml`, `assertSeeInOrderInHtml`, `assertSeeInText`, `assertDontSeeInText`, `assertSeeInOrderInText`, `assertHasAttachment`, `assertHasAttachedData`, `assertHasAttachmentFromStorage`, y `assertHasAttachmentFromStorageDisk`.

Como es de esperar, las aserciones "HTML" afirman que la versión HTML de su mensaje contiene una cadena determinada, mientras que las aserciones "text" afirman que la versión en texto plano de su mensaje contiene una cadena determinada:

    use App\Mail\InvoicePaid;
    use App\Models\User;

    public function test_mailable_content()
    {
        $user = User::factory()->create();

        $mailable = new InvoicePaid($user);

        $mailable->assertFrom('jeffrey@example.com');
        $mailable->assertTo('taylor@example.com');
        $mailable->assertHasCc('abigail@example.com');
        $mailable->assertHasBcc('victoria@example.com');
        $mailable->assertHasReplyTo('tyler@example.com');
        $mailable->assertHasSubject('Invoice Paid');
        $mailable->assertHasTag('example-tag');
        $mailable->assertHasMetadata('key', 'value');

        $mailable->assertSeeInHtml($user->email);
        $mailable->assertSeeInHtml('Invoice Paid');
        $mailable->assertSeeInOrderInHtml(['Invoice Paid', 'Thanks']);

        $mailable->assertSeeInText($user->email);
        $mailable->assertSeeInOrderInText(['Invoice Paid', 'Thanks']);

        $mailable->assertHasAttachment('/path/to/file');
        $mailable->assertHasAttachment(Attachment::fromPath('/path/to/file'));
        $mailable->assertHasAttachedData($pdfData, 'name.pdf', ['mime' => 'application/pdf']);
        $mailable->assertHasAttachmentFromStorage('/path/to/file', 'name.pdf', ['mime' => 'application/pdf']);
        $mailable->assertHasAttachmentFromStorageDisk('s3', '/path/to/file', 'name.pdf', ['mime' => 'application/pdf']);
    }

[]()

#### Pruebas de envío por correo

Le sugerimos que compruebe el contenido de sus mailables por separado de las tests que afirman que un mailable determinado ha sido "enviado" a un usuario concreto. Para saber cómo test que los mailables han sido enviados, consulte nuestra documentación sobre el [Mail fake](/docs/%7B%7Bversion%7D%7D/mocking#mail-fake).

[]()

## Correo y desarrollo local

Cuando desarrolle una aplicación que envíe correo electrónico, probablemente no querrá enviar correos electrónicos a direcciones de correo electrónico activas. Laravel proporciona varias maneras de "desactivar" el envío real de correos electrónicos durante el desarrollo local.

[]()

#### Controlador de registro

En lugar de enviar sus correos electrónicos, el controlador de correo `de registro` escribirá todos los mensajes de correo electrónico a sus archivos de registro para su inspección. Típicamente, este controlador sólo se utilizaría durante el desarrollo local. Para más información sobre la configuración de su aplicación por entorno, consulte la [documentación de configuración](/docs/%7B%7Bversion%7D%7D/configuration#environment-configuration).

[]()

#### HELO / Mailtrap / MailHog

Alternativamente, puede utilizar un servicio como [HELO](https://usehelo.com) o [Mailtrap](https://mailtrap.io) y el controlador `smtp` para enviar sus mensajes de correo electrónico a un buzón "ficticio" donde puede verlos en un verdadero cliente de correo electrónico. Este enfoque tiene la ventaja de permitirle inspeccionar realmente los correos electrónicos finales en el visor de mensajes de Mailtrap.

Si estás usando [Laravel Sail](/docs/%7B%7Bversion%7D%7D/sail), puedes previsualizar tus mensajes usando [MailHog](https://github.com/mailhog/MailHog). Cuando Sail se está ejecutando, puede acceder a la interfaz de MailHog en: `http://localhost:8025`.

[]()

#### Uso de una `dirección` global

Por último, puedes especificar una dirección global "to" invocando al método `alwaysTo` que ofrece la facade `Mail`. Típicamente, este método debería ser llamado desde el método de `arranque` de uno de los proveedores de servicio de tu aplicación:

    use Illuminate\Support\Facades\Mail;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment('local')) {
            Mail::alwaysTo('taylor@example.com');
        }
    }

[]()

## Eventos

Laravel dispara dos eventos durante el proceso de envío de mensajes de correo. El evento `MessageSending` se dispara antes de que un mensaje sea enviado, mientras que el evento `MessageSent` se dispara después de que un mensaje ha sido enviado. Recuerde, estos eventos se disparan cuando el correo está siendo *enviado*, no cuando está en cola. Puedes registrar escuchadores de eventos para este evento en tu proveedor de servicios `App\Providers\EventServiceProvider`:

    use App\Listeners\LogSendingMessage;
    use App\Listeners\LogSentMessage;
    use Illuminate\Mail\Events\MessageSending;
    use Illuminate\Mail\Events\MessageSent;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        MessageSending::class => [
            LogSendingMessage::class,
        ],

        MessageSent::class => [
            LogSentMessage::class,
        ],
    ];

[]()

## Transportes personalizados

Laravel incluye una variedad de transportes de correo, sin embargo, es posible que desee escribir sus propios transportes para entregar correo electrónico a través de otros servicios que Laravel no soporta fuera de la caja. Para empezar, define una clase que extienda la clase `Symfony\Component\Mailer\Transport\AbstractTransport`. A continuación, implementar los métodos `doSend` y `__toString()` en su transporte:

    use MailchimpTransactional\ApiClient;
    use Symfony\Component\Mailer\SentMessage;
    use Symfony\Component\Mailer\Transport\AbstractTransport;
    use Symfony\Component\Mime\MessageConverter;

    class MailchimpTransport extends AbstractTransport
    {
        /**
         * The Mailchimp API client.
         *
         * @var \MailchimpTransactional\ApiClient
         */
        protected $client;

        /**
         * Create a new Mailchimp transport instance.
         *
         * @param  \MailchimpTransactional\ApiClient  $client
         * @return void
         */
        public function __construct(ApiClient $client)
        {
            $this->client = $client;
        }

        /**
         * {@inheritDoc}
         */
        protected function doSend(SentMessage $message): void
        {
            $email = MessageConverter::toEmail($message->getOriginalMessage());

            $this->client->messages->send(['message' => [
                'from_email' => $email->getFrom(),
                'to' => collect($email->getTo())->map(function ($email) {
                    return ['email' => $email->getAddress(), 'type' => 'to'];
                })->all(),
                'subject' => $email->getSubject(),
                'text' => $email->getTextBody(),
            ]]);
        }

        /**
         * Get the string representation of the transport.
         *
         * @return string
         */
        public function __toString(): string
        {
            return 'mailchimp';
        }
    }

Una vez que haya definido su transporte personalizado, puede registrarlo a través del método `extend` proporcionado por la facade `Mail`. Normalmente, esto debería hacerse dentro del método `boot` del proveedor de servicios `AppServiceProvider` de su aplicación. Un argumento `$config` será pasado al closure proporcionado al método `extend`. Este argumento contendrá el array configuración definido para el mailer en el fichero de configuración `config/mail.php` de la aplicación:

    use App\Mail\MailchimpTransport;
    use Illuminate\Support\Facades\Mail;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Mail::extend('mailchimp', function (array $config = []) {
            return new MailchimpTransport(/* ... */);
        })
    }

Una vez que su transporte personalizado ha sido definido y registrado, usted puede crear una definición de mailer dentro del archivo de configuración config/mail `.` php de su aplicación que utilice el nuevo transporte:

    'mailchimp' => [
        'transport' => 'mailchimp',
        // ...
    ],

[]()

### Transportes Symfony adicionales

Laravel incluye soporte para algunos transportes de correo existentes mantenidos por Symfony como Mailgun y Postmark. Sin embargo, es posible que desees extender Laravel con soporte para otros transportes mantenidos por Symfony. Puedes hacerlo requiriendo el mailer Symfony necesario a través de Composer y registrando el transporte con Laravel. Por ejemplo, puede instalar y registrar el "Sendinblue" Symfony mailer:

```none
composer require symfony/sendinblue-mailer
```

Una vez instalado el paquete Sendinblue mailer, puede añadir una entrada para sus credenciales de la API de Sendinblue en el archivo de configuración de `servicios` de su aplicación:

    'sendinblue' => [
        'key' => 'your-api-key',
    ],

Por último, puede utilizar el método `extend` de la facade `Mail` para registrar el transporte con Laravel. Normalmente, esto debería hacerse dentro del método `boot` de un proveedor de servicios:

    use Illuminate\Support\Facades\Mail;
    use Symfony\Component\Mailer\Bridge\Sendinblue\Transport\SendinblueTransportFactory;
    use Symfony\Component\Mailer\Transport\Dsn;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Mail::extend('sendinblue', function () {
            return (new SendinblueTransportFactory)->create(
                new Dsn(
                    'sendinblue+api',
                    'default',
                    config('services.sendinblue.key')
                )
            );
        });
    }
