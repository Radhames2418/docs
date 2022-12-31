# Difusión

- [Introducción](#introduction)
- [Instalación en el servidor](#server-side-installation)
  - [Configuración](#configuration)
  - [Canales Pusher](#pusher-channels)
  - [Ably](#ably)
  - [Alternativas de código abierto](#open-source-alternatives)
- [Instalación en el cliente](#client-side-installation)
  - [Canales Pusher](#client-pusher-channels)
  - [Ably](#client-ably)
- [Concepto](#concept-overview)
  - [Uso de una aplicación de ejemplo](#using-example-application)
- [Definición de Eventos de Difusión](#defining-broadcast-events)
  - [Nombre de la Difusión](#broadcast-name)
  - [Datos de difusión](#broadcast-data)
  - [Cola de difusión](#broadcast-queue)
  - [Condiciones de difusión](#broadcast-conditions)
  - [Transmisiones y Transacciones de Base de Datos](#broadcasting-and-database-transactions)
- [Autorización de canales](#authorizing-channels)
  - [Definición de rutas de autorización](#defining-authorization-routes)
  - [Definición de Callbacks de Autorización](#defining-authorization-callbacks)
  - [Definición de clases de canales](#defining-channel-classes)
- [Difusión de eventos](#broadcasting-events)
  - [Sólo a Otros](#only-to-others)
  - [Personalización de la conexión](#customizing-the-connection)
- [Recepción de emisiones](#receiving-broadcasts)
  - [Escucha de eventos](#listening-for-events)
  - [Abandonar un canal](#leaving-a-channel)
  - [Espacios de nombres](#namespaces)
- [Canales de presencia](#presence-channels)
  - [Autorización de canales de presencia](#authorizing-presence-channels)
  - [Unirse a canales de presencia](#joining-presence-channels)
  - [Transmisión a canales de presencia](#broadcasting-to-presence-channels)
- [Difusión de modelos](#model-broadcasting)
  - [Convenciones de difusión de modelos](#model-broadcasting-conventions)
  - [Escucha de transmisiones de modelos](#listening-for-model-broadcasts)
- [Eventos de cliente](#client-events)
- [Notificaciones](#notifications)

[]()

## Introducción

En muchas aplicaciones web modernas, los WebSockets se utilizan para implementar interfaces de usuario actualizadas en tiempo real. Cuando se actualiza algún dato en el servidor, se suele enviar un mensaje a través de una conexión WebSocket para que lo gestione el cliente. Los WebSockets proporcionan una alternativa más eficiente que sondear continuamente el servidor de su aplicación para los cambios de datos que deben reflejarse en su interfaz de usuario.

Por ejemplo, imagine que su aplicación puede exportar los datos de un usuario a un archivo CSV y enviárselo por correo electrónico. Sin embargo, la creación de este archivo CSV lleva varios minutos, por lo que decide crear y enviar el CSV dentro de un [trabajo en cola](/docs/%7B%7Bversion%7D%7D/queues). Cuando el CSV ha sido creado y enviado al usuario, podemos utilizar la difusión de eventos para enviar un evento `App\Events\UserDataExported` que es recibido por el JavaScript de nuestra aplicación. Una vez que el evento es recibido, podemos mostrar un mensaje al usuario de que su CSV ha sido enviado por correo electrónico sin necesidad de actualizar la página.

Para ayudarle en la construcción de este tipo de características, Laravel hace que sea fácil de "difusión" de su lado del servidor Laravel [eventos](/docs/%7B%7Bversion%7D%7D/events) a través de una conexión WebSocket. La difusión de sus eventos Laravel le permite compartir los mismos nombres de eventos y datos entre su aplicación Laravel del lado del servidor y su aplicación JavaScript del lado del cliente.

Los conceptos básicos detrás de la difusión son simples: los clientes se conectan a canales con nombre en el frontend, mientras que su aplicación Laravel difunde eventos a estos canales en el backend. Estos eventos pueden contener cualquier dato adicional que desees poner a disposición del frontend.

[]()

#### Drivers soportados

Por defecto, Laravel incluye dos controladores de difusión del lado del servidor para que elijas: [Pusher Channels](https://pusher.com/channels) y [Ably](https://ably.io). Sin embargo, los paquetes impulsados por la comunidad, como [laravel-websockets](https://beyondco.de/docs/laravel-websockets/getting-started/introduction) y [soketi](https://docs.soketi.app/), proporcionan controladores de difusión adicionales que no requieren proveedores de difusión comerciales.

> **Nota**  
> Antes de sumergirte en la transmisión de eventos, asegúrate de haber leído la documentación de Laravel sobre [eventos y listeners](/docs/%7B%7Bversion%7D%7D/events).

[]()

## Instalación en el servidor

Para empezar a utilizar la transmisión de eventos de Laravel, tenemos que hacer algunas configuraciones dentro de la aplicación Laravel, así como instalar algunos paquetes.

La difusión de eventos se realiza mediante un controlador de difusión del lado del servidor que difunde los eventos de Laravel para que Laravel Echo (una biblioteca JavaScript) pueda recibirlos en el cliente del navegador. No te preocupes - vamos a caminar a través de cada parte del proceso de instalación paso a paso.

[]()

### Configuración

Toda la configuración de transmisión de eventos de tu aplicación se almacena en el archivo de configuración `config/broadcasting.php`. Laravel soporta varios controladores de difusión fuera de la caja: [Pusher Channels](https://pusher.com/channels), [Redis](/docs/%7B%7Bversion%7D%7D/redis), y un controlador de `registro` para el desarrollo local y la depuración. Además, se incluye un controlador `null` que permite deshabilitar totalmente la difusión durante las pruebas. Se incluye un ejemplo de configuración para cada uno de estos controladores en el archivo de configuración `config/broadcasting.php`.

[]()

#### Proveedor de servicios de difusión

Antes de transmitir cualquier evento, primero tendrá que registrar el `App\Providers\BroadcastServiceProvider`. En las nuevas aplicaciones Laravel, sólo tienes que descomentar este proveedor en el array `providers` de tu fichero de configuración `config/app.` php. Este `BroadcastServiceProvider` contiene el código necesario para registrar las rutas de autorización de difusión y callbacks.

[]()

#### Configuración de la cola

También necesitarás configurar y ejecutar una [cola de trabajo](/docs/%7B%7Bversion%7D%7D/queues). Toda la difusión de eventos se realiza a través de trabajos en cola para que el tiempo de respuesta de su aplicación no se vea seriamente afectado por los eventos que se difunden.

[]()

### Canales Pusher

Si planeas transmitir tus eventos usando [Pusher Channels](https://pusher.com/channels), debes instalar Pusher Channels PHP SDK usando el gestor de paquetes Composer:

```shell
composer require pusher/pusher-php-server
```

A continuación, debes configurar tus credenciales de Pusher Channels en el archivo de configuración `config/broadcasting.php`. Un ejemplo de configuración de Pusher Channels ya está incluido en este archivo, permitiéndote especificar rápidamente tu clave, secreto e ID de aplicación. Típicamente, estos valores deben establecerse a través de las [variables de entorno](/docs/%7B%7Bversion%7D%7D/configuration#environment-configuration) `PUSHER_APP_KEY`, `PUSHER_APP_SECRET`, y `PUSHER_APP_ID`:

```ini
PUSHER_APP_ID=your-pusher-app-id
PUSHER_APP_KEY=your-pusher-key
PUSHER_APP_SECRET=your-pusher-secret
PUSHER_APP_CLUSTER=mt1
```

La configuración `pusher` del archivo `config/broadcasting.` php también permite especificar `opciones` adicionales que son soportadas por Channels, como el cluster.

A continuación, tendrá que cambiar su controlador de difusión a `pusher` en su archivo `.env`:

```ini
BROADCAST_DRIVER=pusher
```

Por último, ya está listo para instalar y configurar [Laravel Echo](#client-side-installation), que recibirá los eventos de difusión en el lado del cliente.

[]()

#### Alternativas a Pusher de código abierto

Los paquetes [laravel-websockets](https://github.com/beyondcode/laravel-websockets) y [soketi](https://docs.soketi.app/) proporcionan servidores WebSocket compatibles con Pusher para Laravel. Estos paquetes permiten aprovechar toda la potencia de difusión de Laravel sin un proveedor WebSocket comercial. Para más información sobre la instalación y uso de estos paquetes, consulta nuestra documentación sobre [alternativas de código](#open-source-alternatives) abierto.

[]()

### Ably

Si planeas retransmitir tus eventos usando [Ably](https://ably.io), debes instalar el SDK PHP de Ably usando el gestor de paquetes Composer:

```shell
composer require ably/ably-php
```

A continuación, debe configurar sus credenciales de Ably en el archivo de configuración `config/broadcasting.php`. Un ejemplo de configuración de Ably ya está incluido en este archivo, lo que le permite especificar rápidamente su clave. Normalmente, este valor debe establecerse a través de la [variable de entorno](/docs/%7B%7Bversion%7D%7D/configuration#environment-configuration) `ABLY_KEY`:

```ini
ABLY_KEY=your-ably-key
```

A continuación, tendrá que cambiar su controlador de difusión a `ably` en su archivo . `env`:

```ini
BROADCAST_DRIVER=ably
```

Por último, estás listo para instalar y configurar [Laravel Echo](#client-side-installation), que recibirá los eventos de difusión en el lado del cliente.

[]()

### Alternativas de código abierto

[]()

#### PHP

El paquete [laravel-websockets](https://github.com/beyondcode/laravel-websockets) es un paquete WebSocket compatible con Pusher para Laravel. Este paquete le permite aprovechar toda la potencia de difusión de Laravel sin un proveedor WebSocket comercial. Para obtener más información sobre la instalación y el uso de este paquete, consulte su [documentación oficial](https://beyondco.de/docs/laravel-websockets).

[]()

#### Nodo

[Soketi](https://github.com/soketi/soketi) es un servidor WebSocket basado en Node y compatible con Pusher para Laravel. Bajo el capó, Soketi utiliza µWebSockets.js para una escalabilidad y velocidad extremas. Este paquete le permite aprovechar toda la potencia de difusión de Laravel sin un proveedor WebSocket comercial. Para más información sobre la instalación y uso de este paquete, consulta su [documentación oficial](https://docs.soketi.app/).

[]()

## Instalación en el lado del cliente

[]()

### Canales Pusher

[Laravel Echo](https://github.com/laravel/echo) es una librería JavaScript que facilita la suscripción a canales y la escucha de eventos emitidos por el controlador de difusión del lado del servidor. Puedes instalar Echo a través del gestor de paquetes NPM. En este ejemplo, también instalaremos el paquete `pusher-js` ya que utilizaremos el emisor Pusher Channels:

```shell
npm install --save-dev laravel-echo pusher-js
```

Una vez instalado Echo, estás listo para crear una nueva instancia de Echo en el JavaScript de tu aplicación. Un buen lugar para hacer esto es en la parte inferior del archivo `resources/js/bootstrap.js` que se incluye con el framework Laravel. Por defecto, un ejemplo de configuración de Echo ya está incluido en este archivo - sólo tienes que descomentarla:

```js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});
```

Una vez que hayas descomentado y ajustado la configuración de Echo de acuerdo a tus necesidades, puedes compilar los assets de tu aplicación:

```shell
npm run dev
```

> **Nota**  
> Para saber más sobre la compilación de los activos JavaScript de tu aplicación, consulta la documentación en [Vite](/docs/%7B%7Bversion%7D%7D/vite).

[]()

#### Utilizar una instancia de cliente existente

Si ya tienes una instancia preconfigurada del cliente Pusher Channels que te gustaría que Echo utilizara, puedes pasársela a Echo a través de la opción de configuración `del cliente`:

```js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const options = {
    broadcaster: 'pusher',
    key: 'your-pusher-channels-key'
}

window.Echo = new Echo({
    ...options,
    client: new Pusher(options.key, options)
});
```

[]()

### Ably

[Laravel Echo](https://github.com/laravel/echo) es una librería JavaScript que facilita la suscripción a canales y la escucha de eventos emitidos por tu controlador de emisión del lado del servidor. Puedes instalar Echo a través del gestor de paquetes NPM. En este ejemplo, también instalaremos el paquete `pusher-js`.

Puede que te preguntes por qué instalar la librería `pusher-js` JavaScript aunque estemos usando Ably para difundir nuestros eventos. Afortunadamente, Ably incluye un modo de compatibilidad con Pusher que nos permite utilizar el protocolo Pusher cuando escuchamos eventos en nuestra aplicación cliente:

```shell
npm install --save-dev laravel-echo pusher-js
```

**Antes de continuar, debes habilitar el soporte del protocolo Pusher en la configuración de tu aplicación Ably. Puedes habilitar esta característica en la sección "Protocol Adapter Settings" del panel de configuración de tu aplicación Ably.**

Una vez instalado Echo, estás listo para crear una nueva instancia de Echo en el JavaScript de tu aplicación. Un buen lugar para hacer esto es en la parte inferior del archivo `resources/js/bootstrap.js` que se incluye con el framework Laravel. Por defecto, un ejemplo de configuración de Echo ya está incluido en este archivo; sin embargo, la configuración por defecto en el archivo `bootstrap`.js está pensada para Pusher. Puedes copiar la configuración de abajo para hacer la transición de tu configuración a Ably:

```js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_ABLY_PUBLIC_KEY,
    wsHost: 'realtime-pusher.ably.io',
    wsPort: 443,
    disableStats: true,
    encrypted: true,
});
```

Tenga en cuenta que nuestra configuración Ably Echo hace referencia a una variable de entorno `VITE_ABLY_PUBLIC_KEY`. El valor de esta variable debe ser tu clave pública de Ably. Su clave pública es la porción de su clave Ably que ocurre antes del carácter `:`.

Una vez que haya descomentado y ajustado la configuración de Echo según sus necesidades, puede compilar los activos de su aplicación:

```shell
npm run dev
```

> **Nota**  
> Para obtener más información sobre la compilación de los activos JavaScript de su aplicación, consulte la documentación en [Vite](/docs/%7B%7Bversion%7D%7D/vite).

[]()

## Concepto

La transmisión de eventos de Laravel le permite transmitir sus eventos Laravel del lado del servidor a su aplicación JavaScript del lado del cliente utilizando un enfoque basado en controladores para WebSockets. Actualmente, Laravel incluye [Pusher Channels](https://pusher.com/channels) y [Ably](https://ably.io) drivers. Los eventos pueden ser fácilmente consumidos en el lado del cliente utilizando el paquete [Laravel Echo](#client-side-installation) JavaScript.

Los eventos se difunden a través de "canales", que pueden especificarse como públicos o privados. Cualquier visitante de su aplicación puede suscribirse a un canal público sin ningún tipo de autenticación o autorización, sin embargo, con el fin de suscribirse a un canal privado, un usuario debe ser autenticado y autorizado a escuchar en ese canal.

> **Nota**  
> Si desea explorar alternativas de código abierto a Pusher, consulte las [alternativas de código](#open-source-alternatives) abierto.

[]()

### Uso de una aplicación de ejemplo

Antes de sumergirnos en cada componente de la difusión de eventos, vamos a tener una visión general de alto nivel utilizando una tienda de comercio electrónico como ejemplo.

En nuestra aplicación, supongamos que tenemos una página que permite a los usuarios ver el estado del envío de sus pedidos. Supongamos también que se dispara un evento `OrderShipmentStatusUpdated` cuando la aplicación procesa una actualización del estado del envío:

    use App\Events\OrderShipmentStatusUpdated;

    OrderShipmentStatusUpdated::dispatch($order);

[]()

#### La interfaz `ShouldBroadcast`

Cuando un usuario está viendo uno de sus pedidos, no queremos que tenga que actualizar la página para ver las actualizaciones de estado. En su lugar, queremos transmitir las actualizaciones a la aplicación a medida que se crean. Por lo tanto, tenemos que marcar el evento `OrderShipmentStatusUpdated` con la interfaz `ShouldBroadcast`. Esto instruirá a Laravel para difundir el evento cuando se dispara:

    <?php

    namespace App\Events;

    use App\Models\Order;
    use Illuminate\Broadcasting\Channel;
    use Illuminate\Broadcasting\InteractsWithSockets;
    use Illuminate\Broadcasting\PresenceChannel;
    use Illuminate\Broadcasting\PrivateChannel;
    use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
    use Illuminate\Queue\SerializesModels;

    class OrderShipmentStatusUpdated implements ShouldBroadcast
    {
        /**
         * The order instance.
         *
         * @var \App\Order
         */
        public $order;
    }

La interfaz `ShouldBroadcast` requiere que nuestro evento defina un método `broadcastOn`. Este método es responsable de devolver los canales en los que el evento debería emitirse. Un stub vacío de este método ya está definido en las clases de evento generadas, así que sólo tenemos que rellenar sus detalles. Sólo queremos que el creador del pedido pueda ver las actualizaciones de estado, por lo que emitiremos el evento en un canal privado vinculado al pedido:

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\PrivateChannel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('orders.'.$this->order->id);
    }

[]()

#### Autorización de canales

Recuerda, los usuarios deben estar autorizados para escuchar en canales privados. Podemos definir nuestras reglas de autorización de canales en el archivo `routes/channels.` php de nuestra aplicación. En este ejemplo, necesitamos verificar que cualquier usuario que intente escuchar en el canal privado `orders.` 1 es realmente el creador de la orden:

    use App\Models\Order;

    Broadcast::channel('orders.{orderId}', function ($user, $orderId) {
        return $user->id === Order::findOrNew($orderId)->user_id;
    });

El método `channel` acepta dos argumentos: el nombre del canal y un callback que devuelve `true` o `false` indicando si el usuario está autorizado a escuchar en el canal.

Todas las retrollamadas de autorización reciben el usuario autenticado como primer argumento y cualquier parámetro comodín adicional como argumentos posteriores. En este ejemplo, estamos utilizando el marcador de posición `{orderId}` para indicar que la parte "ID" del nombre del canal es un comodín.

[]()

#### Escuchar la retransmisión de eventos

A continuación, todo lo que queda es escuchar el evento en nuestra aplicación JavaScript. Podemos hacerlo utilizando [Laravel Echo](#client-side-installation). Primero, usaremos el método `private` para suscribirnos al canal privado. Luego, podemos usar el método `listen` para escuchar el evento `OrderShipmentStatusUpdated`. Por defecto, todas las propiedades públicas del evento se incluirán en el evento broadcast:

```js
Echo.private(`orders.${orderId}`)
    .listen('OrderShipmentStatusUpdated', (e) => {
        console.log(e.order);
    });
```

[]()

## Definición de Eventos de Difusión

Para informar a Laravel que un evento dado debe ser difundido, debes implementar la interfaz `IlluminateContracts\Broadcasting\ShouldBroadcast` en la clase del evento. Esta interfaz ya está importada en todas las clases de evento generadas por el framework, por lo que puedes añadirla fácilmente a cualquiera de tus eventos.

La interfaz `ShouldBroadcast` requiere que implementes un único método: `broadcastOn`. El método `broadcastOn` debe devolver un canal o array de canales por los que el evento debe emitir. Los canales deben ser instancias de `Channel`, `PrivateChannel`, o `PresenceChannel`. Las instancias de `Channel` representan canales públicos a los que cualquier usuario puede suscribirse, mientras que `PrivateChannels` y `PresenceChannels` representan canales privados que requieren [autorización de canal](#authorizing-channels):

    <?php

    namespace App\Events;

    use App\Models\User;
    use Illuminate\Broadcasting\Channel;
    use Illuminate\Broadcasting\InteractsWithSockets;
    use Illuminate\Broadcasting\PresenceChannel;
    use Illuminate\Broadcasting\PrivateChannel;
    use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
    use Illuminate\Queue\SerializesModels;

    class ServerCreated implements ShouldBroadcast
    {
        use SerializesModels;

        /**
         * The user that created the server.
         *
         * @var \App\Models\User
         */
        public $user;

        /**
         * Create a new event instance.
         *
         * @param  \App\Models\User  $user
         * @return void
         */
        public function __construct(User $user)
        {
            $this->user = $user;
        }

        /**
         * Get the channels the event should broadcast on.
         *
         * @return Channel|array
         */
        public function broadcastOn()
        {
            return new PrivateChannel('user.'.$this->user->id);
        }
    }

Después de implementar la interfaz `ShouldBroadcast`, sólo tienes que [disparar el evento](/docs/%7B%7Bversion%7D%7D/events) como lo harías normalmente. Una vez disparado el evento, un [trabajo en cola](/docs/%7B%7Bversion%7D%7D/queues) emitirá automáticamente el evento utilizando el controlador de emisión especificado.

[]()

### Nombre de la emisión

Por defecto, Laravel transmitirá el evento utilizando el nombre de la clase del evento. Sin embargo, puedes personalizar el nombre de la transmisión definiendo un método `broadcastAs` en el evento:

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'server.created';
    }

Si personalizas el nombre de la emisión usando el método `broadcastAs`, debes asegurarte de registrar tu listener con un carácter `.` al principio. Esto indicará a Echo que no anteponga el espacio de nombres de la aplicación al evento:

    .listen('.server.created', function (e) {
        ....
    });

[]()

### Datos de difusión

Cuando se difunde un evento, todas sus propiedades `públicas` se serializan automáticamente y se difunden como la carga útil del evento, lo que te permite acceder a cualquiera de sus datos públicos desde tu aplicación JavaScript. Así, por ejemplo, si tu evento tiene una única propiedad pública `$user` que contiene un modelo de Eloquent, la carga útil del evento sería:

```json
{
    "user": {
        "id": 1,
        "name": "Patrick Stewart"
        ...
    }
}
```

Sin embargo, si deseas tener un control más preciso sobre la carga útil de difusión, puedes añadir un método `broadcastWith` a tu evento. Este método debería devolver el array de datos que deseas emitir como carga útil del evento:

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return ['id' => $this->user->id];
    }

[]()

### Cola de difusión

Por defecto, cada evento de difusión se coloca en la cola predeterminada para la conexión de cola predeterminada especificada en su archivo de configuración `queue.php`. Puede personalizar la conexión y el nombre de la cola utilizados por el emisor definiendo las propiedades de `conexión` y `cola` en su clase de evento:

    /**
     * The name of the queue connection to use when broadcasting the event.
     *
     * @var string
     */
    public $connection = 'redis';

    /**
     * The name of the queue on which to place the broadcasting job.
     *
     * @var string
     */
    public $queue = 'default';

Alternativamente, puede personalizar el nombre de la cola definiendo un método `broadcastQueue` en su evento:

    /**
     * The name of the queue on which to place the broadcasting job.
     *
     * @return string
     */
    public function broadcastQueue()
    {
        return 'default';
    }

Si quieres retransmitir tu evento utilizando la cola de `sincronización` en lugar del controlador de cola por defecto, puedes implementar la interfaz `ShouldBroadcastNow` en lugar de `ShouldBroadcast`:

    <?php

    use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

    class OrderShipmentStatusUpdated implements ShouldBroadcastNow
    {
        //
    }

[]()

### Condiciones de difusión

A veces quieres emitir tu evento sólo si se cumple una condición determinada. Puedes definir estas condiciones añadiendo un método `broadcastWhen` a tu clase de evento:

    /**
     * Determine if this event should broadcast.
     *
     * @return bool
     */
    public function broadcastWhen()
    {
        return $this->order->value > 100;
    }

[]()

#### Transmisiones y Transacciones de Base de Datos

Cuando los eventos de difusión se envían dentro de transacciones de base de datos, pueden ser procesados por la cola antes de que la transacción de base de datos se haya comprometido. Cuando esto ocurre, cualquier actualización que haya hecho a los modelos o registros de la base de datos durante la transacción de la base de datos puede no estar reflejada en la base de datos. Además, es posible que los modelos o registros de base de datos creados durante la transacción no existan en la base de datos. Si su evento depende de estos modelos, pueden producirse errores inesperados cuando se procese el trabajo que emite el evento.

Si la opción de configuración `after_commit` de su conexión de cola está establecida en `false`, puede indicar que un evento de difusión particular debe ser enviado después de que todas las transacciones de base de datos abiertas hayan sido confirmadas definiendo una propiedad `$afterCommit` en la clase del evento:

    <?php

    namespace App\Events;

    use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
    use Illuminate\Queue\SerializesModels;

    class ServerCreated implements ShouldBroadcast
    {
        use SerializesModels;

        public $afterCommit = true;
    }

> **Nota**  
> Para obtener más información sobre cómo solucionar estos problemas, consulte la documentación relativa a los trabajos en cola [y las transacciones de base de datos](/docs/%7B%7Bversion%7D%7D/queues#jobs-and-database-transactions).

[]()

## Autorización de canales

Los canales privados requieren que autorices que el usuario actualmente autenticado pueda realmente escuchar en el canal. Esto se consigue haciendo una petición HTTP a tu aplicación Laravel con el nombre del canal y permitiendo que tu aplicación determine si el usuario puede escuchar en ese canal. Cuando se utiliza [Laravel Echo](#client-side-installation), la solicitud HTTP para autorizar suscripciones a canales privados se hará automáticamente, sin embargo, es necesario definir las rutas adecuadas para responder a estas solicitudes.

[]()

### Definición de rutas de autorización

Afortunadamente, Laravel hace que sea fácil de definir las rutas para responder a las solicitudes de autorización de canal. En el `App\Providers\BroadcastServiceProvider` incluido con tu aplicación Laravel, verás una llamada al método `Broadcast::routes`. Este método registrará la ruta `/broadcasting/auth` para gestionar las peticiones de autorización:

    Broadcast::routes();

El método `Broadcast::routes` colocará automáticamente sus rutas dentro del grupo `web` middleware; sin embargo, puede pasar una array de atributos de ruta al método si desea personalizar los atributos asignados:

    Broadcast::routes($attributes);

[]()

#### Personalización del punto final de autorización

Por defecto, Echo utilizará el endpoint `/broadcasting/auth` para autorizar el acceso al canal. Sin embargo, puede especificar su propio punto final de autorización pasando la opción de configuración `authEndpoint` a su instancia de Echo:

```js
window.Echo = new Echo({
    broadcaster: 'pusher',
    // ...
    authEndpoint: '/custom/endpoint/auth'
});
```

[]()

#### Personalización de la petición de autorización

Puedes personalizar la forma en que Laravel Echo realiza las peticiones de autorización proporcionando un autorizador personalizado al inicializar Echo:

```js
window.Echo = new Echo({
    // ...
    authorizer: (channel, options) => {
        return {
            authorize: (socketId, callback) => {
                axios.post('/api/broadcasting/auth', {
                    socket_id: socketId,
                    channel_name: channel.name
                })
                .then(response => {
                    callback(null, response.data);
                })
                .catch(error => {
                    callback(error);
                });
            }
        };
    },
})
```

[]()

### Definición de Callbacks de Autorización

A continuación, tenemos que definir la lógica que determinará si el usuario autenticado actualmente puede escuchar un canal determinado. Esto se hace en el archivo `routes/channels.` php que se incluye con tu aplicación. En este archivo, puede utilizar el método `Broadcast::channel` para registrar las retrollamadas de autorización del canal:

    Broadcast::channel('orders.{orderId}', function ($user, $orderId) {
        return $user->id === Order::findOrNew($orderId)->user_id;
    });

El método `channel` acepta dos argumentos: el nombre del canal y un callback que devuelve `true` o `false` indicando si el usuario está autorizado a escuchar en el canal.

Todas las retrollamadas de autorización reciben como primer argumento el usuario autenticado en ese momento y como argumentos posteriores cualquier parámetro comodín adicional. En este ejemplo, estamos utilizando el marcador de posición `{orderId}` para indicar que la parte "ID" del nombre del canal es un comodín.

[]()

#### Retrollamada de autorización Modelo de enlace

Al igual que las rutas HTTP, las rutas de canal también pueden aprovechar la [vinculación](/docs/%7B%7Bversion%7D%7D/routing#route-model-binding) implícita y explícita [del modelo de ruta](/docs/%7B%7Bversion%7D%7D/routing#route-model-binding). Por ejemplo, en lugar de recibir una cadena o un ID numérico de pedido, puede solicitar una instancia real del modelo de `pedido`:

    use App\Models\Order;

    Broadcast::channel('orders.{order}', function ($user, Order $order) {
        return $user->id === $order->user_id;
    });

> **Advertencia**  
> A diferencia de la vinculación de modelos de rutas HTTP, la vinculación de modelos de canal no admite el [alcance](/docs/%7B%7Bversion%7D%7D/routing#implicit-model-binding-scoping) automático de la [vinculación de modelos impl](/docs/%7B%7Bversion%7D%7D/routing#implicit-model-binding-scoping)ícita. Sin embargo, esto no suele ser un problema, ya que la mayoría de los canales se pueden clasificar en función de la clave primaria única de un único modelo.

[]()

#### Autenticación de devolución de llamada de autorización

Los canales de difusión privados y de presencia autentican al usuario actual a través del guarda de autenticación predeterminado de su aplicación. Si el usuario no está autenticado, la autorización del canal se deniega automáticamente y la llamada de retorno de autorización nunca se ejecuta. Sin embargo, puedes asignar múltiples guardias personalizados que autentiquen la solicitud entrante si es necesario:

    Broadcast::channel('channel', function () {
        // ...
    }, ['guards' => ['web', 'admin']]);

[]()

### Definición de clases de canales

Si su aplicación está consumiendo muchos canales diferentes, su archivo `routes/channels.` php podría volverse voluminoso. Por lo tanto, en lugar de utilizar closures para autorizar canales, puede utilizar clases de canal. Para generar una clase de canal, utiliza el comando `make:channel` de Artisan. Este comando colocará una nueva clase de canal en el directorio `App/Broadcasting`.

```shell
php artisan make:channel OrderChannel
```

Luego, registre su canal en su archivo `routes/channels.php`:

    use App\Broadcasting\OrderChannel;

    Broadcast::channel('orders.{order}', OrderChannel::class);

Finalmente, puede colocar la lógica de autorización para su canal en el método `join` de la clase channel. Este método de `unión` albergará la misma lógica que habría colocado típicamente en el closure autorización de su canal. También puede aprovechar la vinculación del modelo del canal:

    <?php

    namespace App\Broadcasting;

    use App\Models\Order;
    use App\Models\User;

    class OrderChannel
    {
        /**
         * Create a new channel instance.
         *
         * @return void
         */
        public function __construct()
        {
            //
        }

        /**
         * Authenticate the user's access to the channel.
         *
         * @param  \App\Models\User  $user
         * @param  \App\Models\Order  $order
         * @return array|bool
         */
        public function join(User $user, Order $order)
        {
            return $user->id === $order->user_id;
        }
    }

> **Nota**  
> Como muchas otras clases en Laravel, las clases de canal serán resueltas automáticamente por el [contenedor de servicios](/docs/%7B%7Bversion%7D%7D/container). Por lo tanto, puedes escribir cualquier dependencia requerida por tu canal en su constructor.

[]()

## Difusión de eventos

Una vez que has definido un evento y lo has marcado con la interfaz `ShouldBroadcast`, sólo necesitas disparar el evento utilizando el método de envío del evento. El despachador de eventos se dará cuenta de que el evento está marcado con la interfaz `ShouldBroadcast` y pondrá el evento en cola para su difusión:

    use App\Events\OrderShipmentStatusUpdated;

    OrderShipmentStatusUpdated::dispatch($order);

[]()

### Sólo a Otros

Cuando construyas una aplicación que utilice difusión de eventos, puede que ocasionalmente necesites difundir un evento a todos los suscriptores de un canal dado excepto al usuario actual. Para ello puede utilizar el ayudante de `difusión` y el método `toOthers`:

    use App\Events\OrderShipmentStatusUpdated;

    broadcast(new OrderShipmentStatusUpdated($update))->toOthers();

Para entender mejor cuando puede querer utilizar el método `toOthers`, imaginemos una aplicación de lista de tareas donde un usuario puede crear una nueva tarea introduciendo un nombre de tarea. Para crear una tarea, la aplicación puede realizar una solicitud a una URL `/task` que transmite la creación de la tarea y devuelve una representación JSON de la nueva tarea. Cuando la aplicación JavaScript recibe la respuesta del punto final, puede insertar directamente la nueva tarea en su lista de tareas:

```js
axios.post('/task', task)
    .then((response) => {
        this.tasks.push(response.data);
    });
```

Sin embargo, recuerde que también transmitimos la creación de la tarea. Si su aplicación JavaScript también está escuchando este evento para añadir tareas a la lista de tareas, tendrá tareas duplicadas en su lista: una del punto final y otra de la difusión. Puede solucionar esto utilizando el método `toOthers` para indicar al emisor que no emita el evento al usuario actual.

> **Advertencia**  
> Su evento debe utilizar el rasgo `Illuminate\Broadcasting\InteractsWithSockets` para poder llamar al método `toOthers`.

[]()

#### Configuración

Cuando inicializas una instancia de Laravel Echo, se asigna un ID de socket a la conexión. Si estás usando una instancia global de [Axios](https://github.com/mzabriskie/axios) para hacer peticiones HTTP desde tu aplicación JavaScript, el socket ID se adjuntará automáticamente a cada petición saliente como cabecera `X-Socket-ID`. Entonces, cuando llames al método `toOthers`, Laravel extraerá el socket ID de la cabecera e instruirá al emisor para que no emita a ninguna conexión con ese socket ID.

Si no está utilizando una instancia global de Axios, necesitará configurar manualmente su aplicación JavaScript para enviar la cabecera `X-Socket-ID` con todas las peticiones salientes. Puede recuperar el ID del socket utilizando el método `Echo.socketId`:

```js
var socketId = Echo.socketId();
```

[]()

### Personalización de la conexión

Si tu aplicación interactúa con múltiples conexiones de difusión y quieres difundir un evento utilizando un difusor distinto al predeterminado, puedes especificar a qué conexión enviar un evento utilizando el método `via`:

    use App\Events\OrderShipmentStatusUpdated;

    broadcast(new OrderShipmentStatusUpdated($update))->via('pusher');

Alternativamente, puedes especificar la conexión de transmisión del evento llamando al método `broadcastVia` dentro del constructor del evento. Sin embargo, antes de hacerlo, debes asegurarte de que la clase del evento utiliza el rasgo `InteractsWithBroadcasting`:

    <?php

    namespace App\Events;

    use Illuminate\Broadcasting\Channel;
    use Illuminate\Broadcasting\InteractsWithBroadcasting;
    use Illuminate\Broadcasting\InteractsWithSockets;
    use Illuminate\Broadcasting\PresenceChannel;
    use Illuminate\Broadcasting\PrivateChannel;
    use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
    use Illuminate\Queue\SerializesModels;

    class OrderShipmentStatusUpdated implements ShouldBroadcast
    {
        use InteractsWithBroadcasting;

        /**
         * Create a new event instance.
         *
         * @return void
         */
        public function __construct()
        {
            $this->broadcastVia('pusher');
        }
    }

[]()

## Recepción de emisiones

[]()

### Escucha de eventos

Una vez que has [instalado e instanciado Laravel Echo](#client-side-installation), estás listo para empezar a escuchar los eventos que se emiten desde tu aplicación Laravel. En primer lugar, utiliza el método `channel` para recuperar una instancia de un canal, a continuación, llama al método `listen` para escuchar un evento especificado:

```js
Echo.channel(`orders.${this.order.id}`)
    .listen('OrderShipmentStatusUpdated', (e) => {
        console.log(e.order.name);
    });
```

Si deseas escuchar eventos en un canal privado, utiliza el método `private` en su lugar. Puedes continuar encadenando llamadas al método `listen` para escuchar múltiples eventos en un único canal:

```js
Echo.private(`orders.${this.order.id}`)
    .listen(/* ... */)
    .listen(/* ... */)
    .listen(/* ... */);
```

[]()

#### Dejar de escuchar eventos

Si quieres dejar de escuchar un evento determinado sin [salir del](#leaving-a-channel) canal, puedes utilizar el método `stopListening`:

```js
Echo.private(`orders.${this.order.id}`)
    .stopListening('OrderShipmentStatusUpdated')
```

[]()

### Abandonar un canal

Para abandonar un canal, puedes llamar al método `leaveChannel` de tu instancia Echo:

```js
Echo.leaveChannel(`orders.${this.order.id}`);
```

Si desea abandonar un canal y también sus canales privados y de presencia asociados, puede llamar al método `leave`:

```js
Echo.leave(`orders.${this.order.id}`);
```

[]()

### Espacios de nombres

Es posible que haya notado en los ejemplos anteriores que no especificamos el espacio de nombres completo `App\Events` para las clases de eventos. Esto se debe a que Echo asumirá automáticamente que los eventos se encuentran en el espacio de nombres `AppEvents`. Sin embargo, puede configurar el espacio de nombres raíz cuando instale Echo pasando una opción de configuración de `espacio de nombres`:

```js
window.Echo = new Echo({
    broadcaster: 'pusher',
    // ...
    namespace: 'App.Other.Namespace'
});
```

Alternativamente, puedes prefijar las clases de eventos con un `.` cuando te suscribas a ellos usando Echo. Esto le permitirá especificar siempre el nombre completo de la clase:

```js
Echo.channel('orders')
    .listen('.Namespace\\Event\\Class', (e) => {
        //
    });
```

[]()

## Canales de presencia

Los canales de presencia se basan en la seguridad de los canales privados a la vez que exponen la característica adicional de saber quién está suscrito al canal. Esto facilita la creación de potentes funciones de aplicaciones colaborativas, como notificar a los usuarios cuando otro usuario está viendo la misma página o enumerar los habitantes de una sala de chat.

[]()

### Autorización de canales de presencia

Todos los canales de presencia son también canales privados; por lo tanto, los usuarios deben estar [autorizados para acceder a ellos](#authorizing-channels). Sin embargo, al definir las retrollamadas de autorización para los canales de presencia, no devolverás `true` si el usuario está autorizado a unirse al canal. En su lugar, debe devolver un array de datos sobre el usuario.

Los datos devueltos por el callback de autorización estarán disponibles para los oyentes de eventos del canal de presencia en tu aplicación JavaScript. Si el usuario no está autorizado a unirse al canal de presencia, debe devolver `false` o `null`:

    Broadcast::channel('chat.{roomId}', function ($user, $roomId) {
        if ($user->canJoinRoom($roomId)) {
            return ['id' => $user->id, 'name' => $user->name];
        }
    });

[]()

### Unirse a canales de presencia

Para unirse a un canal de presencia, puede utilizar el método `join` de Echo. El método `join` devolverá una implementación de `PresenceChannel` que, junto con exponer el método `listen`, te permite suscribirte a los eventos `here`, `joining` y `leaving`.

```js
Echo.join(`chat.${roomId}`)
    .here((users) => {
        //
    })
    .joining((user) => {
        console.log(user.name);
    })
    .leaving((user) => {
        console.log(user.name);
    })
    .error((error) => {
        console.error(error);
    });
```

El callback `here` será ejecutado inmediatamente una vez que el canal sea unido exitosamente, y recibirá un array conteniendo la información de usuario para todos los otros usuarios actualmente suscritos al canal. El método `joining` se ejecutará cuando un nuevo usuario se una al canal, mientras que el método `leaving` se ejecutará cuando un usuario abandone el canal. El método de `error` se ejecutará cuando el endpoint de autenticación devuelva un código de estado HTTP distinto de 200 o si hay un problema analizando el JSON devuelto.

[]()

### Transmisión a canales de presencia

Los canales de presencia pueden recibir eventos al igual que los canales públicos o privados. Usando el ejemplo de una sala de chat, podemos querer transmitir eventos `NewMessage` al canal de presencia de la sala. Para ello, devolveremos una instancia de `PresenceChannel` desde el método `broadcastOn` del evento:

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('room.'.$this->message->room_id);
    }

Al igual que con otros eventos, puede utilizar el ayudante de `difusión` y el método `toOthers` para excluir al usuario actual de la recepción de la difusión:

    broadcast(new NewMessage($message));

    broadcast(new NewMessage($message))->toOthers();

Como en otros tipos de eventos, puedes escuchar los eventos enviados a canales de presencia usando el método `listen` de Echo:

```js
Echo.join(`chat.${roomId}`)
    .here(/* ... */)
    .joining(/* ... */)
    .leaving(/* ... */)
    .listen('NewMessage', (e) => {
        //
    });
```

[]()

## Difusión de modelos

> **Aviso**  
> Antes de leer la siguiente documentación sobre difusión de modelos, te recomendamos que te familiarices con los conceptos generales de los servicios de difusión de modelos de Laravel, así como con la forma de crear y escuchar manualmente eventos de difusión.

Es común transmitir eventos cuando los [modelos Eloquent](/docs/%7B%7Bversion%7D%7D/eloquent) de tu aplicación son creados, actualizados o borrados. Por supuesto, esto se puede conseguir fácilmente [definiendo](/docs/%7B%7Bversion%7D%7D/eloquent#events) manualmente [eventos personalizados para los cambios de estado](/docs/%7B%7Bversion%7D%7D/eloquent#events) de los modelos Eloquent y marcando esos eventos con la interfaz `ShouldBroadcast`.

Sin embargo, si no estás utilizando estos eventos para ningún otro propósito en tu aplicación, puede ser engorroso crear clases de eventos con el único propósito de transmitirlos. Para remediar esto, Laravel te permite indicar que un modelo Eloquent debe transmitir automáticamente sus cambios de estado.

Para empezar, su modelo Eloquent debe utilizar el rasgo `Illuminate\Database\Eloquent\BroadcastsEvents`. Además, el modelo debe definir un método `broadcastOn`, que devolverá una array de canales en los que deben emitirse los eventos del modelo:

```php
<?php

namespace App\Models;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use BroadcastsEvents, HasFactory;

    /**
     * Get the user that the post belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the channels that model events should broadcast on.
     *
     * @param  string  $event
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn($event)
    {
        return [$this, $this->user];
    }
}
```

Una vez que tu modelo incluya este rasgo y defina sus canales de difusión, empezará a difundir eventos automáticamente cuando se cree, actualice, elimine, elimine o restaure una instancia del modelo.

Además, te habrás dado cuenta de que el método `broadcastOn` recibe un argumento de tipo string `$event`. Este argumento contiene el tipo de evento que ha ocurrido en el modelo y tendrá un valor de `creado`, `actualizado`, `borrado`, `eliminado` o `restaurado`. Inspeccionando el valor de esta variable, puede determinar a qué canales (si los hay) debe emitir el modelo para un evento en particular:

```php
/**
 * Get the channels that model events should broadcast on.
 *
 * @param  string  $event
 * @return \Illuminate\Broadcasting\Channel|array
 */
public function broadcastOn($event)
{
    return match ($event) {
        'deleted' => [],
        default => [$this, $this->user],
    };
}
```

[]()

#### Personalización de la difusión de modelos Creación de eventos

Ocasionalmente, es posible que desee personalizar la forma en que Laravel crea el evento de difusión del modelo subyacente. Puedes conseguirlo definiendo un método `newBroadcastableEvent` en tu modelo Eloquent. Este método debe devolver una instancia `Illuminate\Database\Eloquent\BroadcastableModelEventOccurred`:

```php
use Illuminate\Database\Eloquent\BroadcastableModelEventOccurred;

/**
 * Create a new broadcastable model event for the model.
 *
 * @param  string  $event
 * @return \Illuminate\Database\Eloquent\BroadcastableModelEventOccurred
 */
protected function newBroadcastableEvent($event)
{
    return (new BroadcastableModelEventOccurred(
        $this, $event
    ))->dontBroadcastToCurrentUser();
}
```

[]()

### Convenciones de difusión de modelos

[]()

#### Convenciones de canales

Como habrá observado, el método `broadcastOn` del ejemplo de modelo anterior no devolvía instancias de `canal`. En su lugar, se devolvían directamente modelos Eloquent. Si una instancia de modelo Eloquent es devuelta por el método `broadcastOn` de tu modelo (o está contenida en un array devuelto por el método), Laravel instanciará automáticamente una instancia de canal privado para el modelo utilizando el nombre de la clase del modelo y el identificador de clave primaria como nombre del canal.

Así, un modelo `App\Models\User` con un `id` de `1` se convertiría en una instancia `Illuminate\Broadcasting\PrivateChannel` con un nombre de `App.Models.User.1`. Por supuesto, además de devolver instancias de modelo Eloquent desde el método `broadcastOn` de su modelo, puede devolver instancias de `canal` completas para tener un control total sobre los nombres de canal del modelo:

```php
use Illuminate\Broadcasting\PrivateChannel;

/**
 * Get the channels that model events should broadcast on.
 *
 * @param  string  $event
 * @return \Illuminate\Broadcasting\Channel|array
 */
public function broadcastOn($event)
{
    return [new PrivateChannel('user.'.$this->id)];
}
```

Si planeas devolver explícitamente una instancia de canal desde el método `broadcastOn` de tu modelo, puedes pasar una instancia de modelo Eloquent al constructor del canal. Al hacerlo, Laravel utilizará las convenciones de canal de modelo discutidas anteriormente para convertir el modelo Eloquent en una cadena de nombre de canal:

```php
return [new Channel($this->user)];
```

Si necesita determinar el nombre del canal de un modelo, puede llamar al método `broadcastChannel` en cualquier instancia del modelo. Por ejemplo, este método devuelve la cadena `App.Models.User.1` para un modelo `App\Models\User` con un `id` de `1`:

```php
$user->broadcastChannel()
```

[]()

#### Convenciones de eventos

Dado que los eventos de difusión de modelos no están asociados con un evento "real" dentro del directorio `App\Events` de su aplicación, se les asigna un nombre y una carga útil basada en convenciones. La convención de Laravel es difundir el evento utilizando el nombre de la clase del modelo (sin incluir el espacio de nombres) y el nombre del evento del modelo que desencadenó la difusión.

Así, por ejemplo, una actualización del modelo `App\Models\Post` emitiría un evento a su aplicación del lado del cliente como `PostUpdated` con la siguiente carga útil:

```json
{
    "model": {
        "id": 1,
        "title": "My first post"
        ...
    },
    ...
    "socket": "someSocketId",
}
```

La eliminación del modelo `App\Models\User` emitiría un evento llamado `UserDeleted`.

Si lo desea, puede definir un nombre de difusión personalizado y la carga útil mediante la adición de un método `broadcastAs` y `broadcastWith` a su modelo. Estos métodos reciben el nombre del evento / operación del modelo que se está produciendo, lo que le permite personalizar el nombre del evento y la carga útil para cada operación del modelo. Si se devuelve `null` desde el método `broadcastAs`, Laravel utilizará las convenciones de nombres de eventos de difusión del modelo discutidas anteriormente al difundir el evento:

```php
/**
 * The model event's broadcast name.
 *
 * @param  string  $event
 * @return string|null
 */
public function broadcastAs($event)
{
    return match ($event) {
        'created' => 'post.created',
        default => null,
    };
}

/**
 * Get the data to broadcast for the model.
 *
 * @param  string  $event
 * @return array
 */
public function broadcastWith($event)
{
    return match ($event) {
        'created' => ['title' => $this->title],
        default => ['model' => $this],
    };
}
```

[]()

### Escucha de transmisiones de modelos

Una vez que hayas añadido el rasgo `BroadcastsEvents` a tu modelo y definido el método `broadcastOn` de tu modelo, estarás listo para empezar a escuchar los eventos transmitidos del modelo dentro de tu aplicación cliente. Antes de empezar, puedes consultar la documentación completa sobre la escucha de [eventos](#listening-for-events).

Primero, usa el método `private` para recuperar una instancia de un canal, luego llama al método `listen` para escuchar un evento especificado. Típicamente, el nombre del canal dado al método `privado` debe corresponder a las [convenciones de transmisión de modelos](#model-broadcasting-conventions) de Laravel.

Una vez que hayas obtenido una instancia del canal, puedes utilizar el método `listen` para escuchar un evento en particular. Dado que los eventos de difusión de modelo no están asociados con un evento "real" dentro del directorio `App\Events` de su aplicación, el [nombre](#model-broadcasting-event-conventions) del evento debe ir precedido de un `.` para indicar que no pertenece a un espacio de nombres en particular. Cada evento de difusión de modelo tiene una propiedad de `modelo` que contiene todas las propiedades difundibles del modelo:

```js
Echo.private(`App.Models.User.${this.user.id}`)
    .listen('.PostUpdated', (e) => {
        console.log(e.model);
    });
```

[]()

## Eventos de cliente

> **Nota**  
> Cuando utilice [Pusher Channels](https://pusher.com/channels), debe activar la opción "Client Events" en la sección "App Settings" del [panel de control de](https://dashboard.pusher.com/) su aplicación para poder enviar eventos de cliente.

A veces es posible que desee transmitir un evento a otros clientes conectados sin golpear su aplicación Laravel en absoluto. Esto puede ser particularmente útil para cosas como las notificaciones de "escritura", donde se quiere alertar a los usuarios de su aplicación que otro usuario está escribiendo un mensaje en una pantalla determinada.

Para transmitir eventos de cliente, puede utilizar el método `whisper` de Echo:

```js
Echo.private(`chat.${roomId}`)
    .whisper('typing', {
        name: this.user.name
    });
```

Para escuchar eventos de cliente, puedes utilizar el método `listenForWhisper`:

```js
Echo.private(`chat.${roomId}`)
    .listenForWhisper('typing', (e) => {
        console.log(e.name);
    });
```

[]()

## Notificaciones

Al emparejar la difusión de eventos con las [notificaciones](/docs/%7B%7Bversion%7D%7D/notifications), tu aplicación JavaScript puede recibir nuevas notificaciones a medida que se producen sin necesidad de actualizar la página. Antes de empezar, asegúrate de leer la documentación sobre el uso [del canal de difusión de notificaciones](/docs/%7B%7Bversion%7D%7D/notifications#broadcast-notifications).

Una vez que haya configurado una notificación para utilizar el canal de difusión, puede escuchar los eventos de difusión utilizando el método de `notificación` de Echo. Recuerda, el nombre del canal debe coincidir con el nombre de la clase de la entidad que recibe las notificaciones:

```js
Echo.private(`App.Models.User.${userId}`)
    .notification((notification) => {
        console.log(notification.type);
    });
```

En este ejemplo, todas las notificaciones enviadas a las instancias `App\Models\User` a través del canal de `difusión` serían recibidas por el callback. El `BroadcastServiceProvider` por defecto que viene con el framework Laravel incluye un callback de autorización de canal para el canal `App.Models.User.{id}`.
