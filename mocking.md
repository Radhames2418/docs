# Mocking

- [Introducción](#introduction)
- [Simulación de objetos](#mocking-objects)
- [facades burlonas](#mocking-facades)
  - [facade-spies">facade Espías](<#\<glossary variable=>)
- [Falsos autobuses](#bus-fake)
  - [Cadenas de trabajos](#bus-job-chains)
  - [Lotes de trabajos](#job-batches)
- [Falsificación de eventos](#event-fake)
  - [Falsificación de eventos](#scoped-event-fakes)
- [Falsificación HTTP](#http-fake)
- [Falsificación de correo](#mail-fake)
- [Falsificación de notificación](#notification-fake)
- [Falsificación de colas](#queue-fake)
  - [Cadenas de trabajos](#job-chains)
- [Falsificación de almacenamiento](#storage-fake)
- [Interacción con el tiempo](#interacting-with-time)

[]()

## Introducción

Al probar aplicaciones Laravel, es posible que desee "simular" ciertos aspectos de su aplicación para que no se ejecuten realmente durante una test determinada. Por ejemplo, al probar un controlador que envía un evento, es posible que desee mock los oyentes de eventos para que no se ejecuten durante la test. Esto le permite test sólo la respuesta HTTP del controlador sin preocuparse por la ejecución de los escuchadores de eventos, ya que los escuchadores de eventos pueden ser probados en su propio caso de test.

Laravel proporciona métodos útiles para simular eventos, trabajos y otras facades. Estos ayudantes proporcionan principalmente una capa de conveniencia sobre Mockery para que no tengas que hacer manualmente complicadas llamadas a métodos de Mockery.

[]()

## Objetos Mocking

Cuando imites un objeto que va a ser inyectado en tu aplicación a través del [contenedor de servicios](/docs/%7B%7Bversion%7D%7D/container) de Laravel, necesitarás enlazar tu instancia imitada en el contenedor como un enlace de `instancia`. Esto le indicará al contenedor que utilice tu instancia simulada del objeto en lugar de construir el objeto por sí mismo:

    use App\Service;
    use Mockery;
    use Mockery\MockInterface;

    public function test_something_can_be_mocked()
    {
        $this->instance(
            Service::class,
            Mockery::mock(Service::class, function (MockInterface $mock) {
                $mock->shouldReceive('process')->once();
            })
        );
    }

Con el fin de hacer esto más conveniente, puede utilizar el método `mock` que es proporcionado por la clase de caso de test base de Laravel. Por ejemplo, el siguiente ejemplo es equivalente al ejemplo anterior:

    use App\Service;
    use Mockery\MockInterface;

    $mock = $this->mock(Service::class, function (MockInterface $mock) {
        $mock->shouldReceive('process')->once();
    });

Puedes utilizar el método `partialMock` cuando sólo necesites mock algunos métodos de un objeto. Los métodos que no son imitados serán ejecutados normalmente cuando sean llamados:

    use App\Service;
    use Mockery\MockInterface;

    $mock = $this->partialMock(Service::class, function (MockInterface $mock) {
        $mock->shouldReceive('process')->once();
    });

De forma similar, si quieres [espiar](http://docs.mockery.io/en/latest/reference/spies.html) un objeto, la clase base test Laravel ofrece un método `spy` que envuelve al método `Mockery::spy`. Los espías son similares a mocks; sin embargo, los espías registran cualquier interacción entre el espía y el código que se está probando, lo que le permite hacer afirmaciones después de que se ejecute el código:

    use App\Service;

    $spy = $this->spy(Service::class);

    // ...

    $spy->shouldHaveReceived('process');

[]()

## facades de mocking

A diferencia de las llamadas a métodos estáticos tradicionales, las [facades](/docs/%7B%7Bversion%7D%7D/facades) (incluidas [facades#real-time-facades"> facades en tiempo real](</docs/%7B%7Bversion%7D%7D/\<glossary variable=>)) pueden burlarse. Esto proporciona una gran ventaja sobre los métodos estáticos tradicionales y le garantiza la misma comprobabilidad que tendría si utilizara la inyección de dependencias tradicional. Al realizar pruebas, es posible que a menudo desee mock una llamada a una facade de Laravel que se produce en uno de sus controladores. Por ejemplo, considere la siguiente acción del controlador:

    <?php

    namespace App\Http\Controllers;

    use Illuminate\Support\Facades\Cache;

    class UserController extends Controller
    {
        /**
         * Retrieve a list of all users of the application.
         *
         * @return \Illuminate\Http\Response
         */
        public function index()
        {
            $value = Cache::get('key');

            //
        }
    }

Podemos mock la llamada a la facade de `cache` utilizando el método `shouldReceive`, que devolverá una instancia de un mock [Mockery](https://github.com/padraic/mockery). Dado que facades son resueltas y gestionadas por el [contenedor de servicios](/docs/%7B%7Bversion%7D%7D/container) de Laravel, son mucho más probables que una clase estática típica. Por ejemplo, vamos a mock nuestra llamada al método `get` de la facade `cache`:

    <?php

    namespace Tests\Feature;

    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Foundation\Testing\WithoutMiddleware;
    use Illuminate\Support\Facades\Cache;
    use Tests\TestCase;

    class UserControllerTest extends TestCase
    {
        public function testGetIndex()
        {
            Cache::shouldReceive('get')
                        ->once()
                        ->with('key')
                        ->andReturn('value');

            $response = $this->get('/users');

            // ...
        }
    }

> **Advertencia**  
> No debes mock la facade `Request`. En su lugar, pasa la entrada que desees a los [métodos de prueba HTTP](/docs/%7B%7Bversion%7D%7D/http-tests) como `get` y `post` cuando ejecutes tu test. Del mismo modo, en lugar de imitar la facade `Config`, llama al método `Config::` set en tus tests.

[]()

### facade Espías

Si quieres [espiar](http://docs.mockery.io/en/latest/reference/spies.html) una facade, puedes llamar al método `spy` de la facade correspondiente. Los espías son similares a mocks; sin embargo, los espías registran cualquier interacción entre el espía y el código que se está probando, permitiéndote hacer afirmaciones después de que el código se ejecute:

    use Illuminate\Support\Facades\Cache;

    public function test_values_are_be_stored_in_cache()
    {
        Cache::spy();

        $response = $this->get('/');

        $response->assertStatus(200);

        Cache::shouldHaveReceived('put')->once()->with('name', 'Taylor', 10);
    }

[]()

## Bus Fake

Cuando se prueba código que envía trabajos, normalmente se quiere afirmar que un trabajo dado ha sido enviado pero no ponerlo en cola o ejecutarlo. Esto se debe a que la ejecución del trabajo normalmente puede ser probada en una clase de test separada.

Puedes utilizar el método `fake` de la facade `Bus` para evitar que los trabajos se envíen a la cola. Entonces, después de ejecutar el código bajo test, puedes inspeccionar qué trabajos intentó enviar la aplicación utilizando los métodos `assertDispatched` y `assertNotDispatched`:

    <?php

    namespace Tests\Feature;

    use App\Jobs\ShipOrder;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Foundation\Testing\WithoutMiddleware;
    use Illuminate\Support\Facades\Bus;
    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        public function test_orders_can_be_shipped()
        {
            Bus::fake();

            // Perform order shipping...

            // Assert that a job was dispatched...
            Bus::assertDispatched(ShipOrder::class);

            // Assert a job was not dispatched...
            Bus::assertNotDispatched(AnotherJob::class);

            // Assert that a job was dispatched synchronously...
            Bus::assertDispatchedSync(AnotherJob::class);

            // Assert that a job was not dispatched synchronously...
            Bus::assertNotDispatchedSync(AnotherJob::class);

            // Assert that a job was dispatched after the response was sent...
            Bus::assertDispatchedAfterResponse(AnotherJob::class);

            // Assert a job was not dispatched after response was sent...
            Bus::assertNotDispatchedAfterResponse(AnotherJob::class);

            // Assert no jobs were dispatched...
            Bus::assertNothingDispatched();
        }
    }

Puedes pasar un closure a los métodos disponibles para afirmar que se ha enviado un trabajo que pasa un determinado "test de verdad". Si se ha enviado al menos un trabajo que supera la test de verdad dada, la afirmación tendrá éxito. Por ejemplo, es posible que desee afirmar que un trabajo fue despachado para un pedido específico:

    Bus::assertDispatched(function (ShipOrder $job) use ($order) {
        return $job->order->id === $order->id;
    });

[]()

#### Falsificación de un subconjunto de trabajos

Si sólo desea evitar que se envíen determinados trabajos, puede pasar los trabajos que deben falsificarse al método `fake`:

    /**
     * Test order process.
     */
    public function test_orders_can_be_shipped()
    {
        Bus::fake([
            ShipOrder::class,
        ]);

        // ...
    }

Puede falsificar todos los trabajos excepto un conjunto de trabajos especificados utilizando el método `except`:

    Bus::fake()->except([
        ShipOrder::class,
    ]);

[]()

### Cadenas de trabajos

El método `assertChained` de la facade `Bus` puede utilizarse para afirmar que se ha enviado una [cadena de trabajos](/docs/%7B%7Bversion%7D%7D/queues#job-chaining). El método `assertChained` acepta un array de trabajos encadenados como primer argumento:

    use App\Jobs\RecordShipment;
    use App\Jobs\ShipOrder;
    use App\Jobs\UpdateInventory;
    use Illuminate\Support\Facades\Bus;

    Bus::assertChained([
        ShipOrder::class,
        RecordShipment::class,
        UpdateInventory::class
    ]);

Como puedes ver en el ejemplo anterior, el array de trabajos encadenados puede ser un array de nombres de clases de trabajos. Sin embargo, también puedes proporcionar un array de instancias de trabajos reales. Al hacerlo, Laravel se asegurará de que las instancias de los trabajos sean de la misma clase y tengan los mismos valores de propiedades que los trabajos encadenados enviados por tu aplicación:

    Bus::assertChained([
        new ShipOrder,
        new RecordShipment,
        new UpdateInventory,
    ]);

[]()

### Lotes de trabajos

El método `assertBatched` de facade `Bus` puede utilizarse para afirmar que se ha enviado un [lote de](/docs/%7B%7Bversion%7D%7D/queues#job-batching) trabajos. El closure dado al método `assertBatched` recibe una instancia de `Illuminate\Bus\PendingBatch`, que puede utilizarse para inspeccionar los trabajos dentro del lote:

    use Illuminate\Bus\PendingBatch;
    use Illuminate\Support\Facades\Bus;

    Bus::assertBatched(function (PendingBatch $batch) {
        return $batch->name == 'import-csv' &&
               $batch->jobs->count() === 10;
    });

[]()

#### Comprobación de la interacción entre trabajos y lotes

Además, puede que ocasionalmente necesites test la interacción de un trabajo individual con su lote subyacente. Por ejemplo, puede que necesite test si un trabajo canceló el procesamiento posterior de su lote. Para ello, debe asignar un lote falso al trabajo mediante el método `withFakeBatch`. El método `withFakeBatch` devuelve una tupla que contiene la instancia del trabajo y el lote falso:

    [$job, $batch] = (new ShipOrder)->withFakeBatch();

    $job->handle();

    $this->assertTrue($batch->cancelled());
    $this->assertEmpty($batch->added);

[]()

## Falsificación de eventos

Al probar código que envía eventos, es posible que desee instruir a Laravel para que no ejecute los oyentes del evento. Utilizando el método `falso` de facade `eventos`, puede evitar que los oyentes se ejecuten, ejecutar el código bajo test, y luego afirmar qué eventos fueron despachados por su aplicación utilizando los métodos `assertDispatched`, `assertNotDispatched`, y `assertNothingDispatched`:

    <?php

    namespace Tests\Feature;

    use App\Events\OrderFailedToShip;
    use App\Events\OrderShipped;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Foundation\Testing\WithoutMiddleware;
    use Illuminate\Support\Facades\Event;
    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        /**
         * Test order shipping.
         */
        public function test_orders_can_be_shipped()
        {
            Event::fake();

            // Perform order shipping...

            // Assert that an event was dispatched...
            Event::assertDispatched(OrderShipped::class);

            // Assert an event was dispatched twice...
            Event::assertDispatched(OrderShipped::class, 2);

            // Assert an event was not dispatched...
            Event::assertNotDispatched(OrderFailedToShip::class);

            // Assert that no events were dispatched...
            Event::assertNothingDispatched();
        }
    }

Puede pasar un closure a los métodos `assertDispatched` o `assertNotDispatched` para afirmar que se ha enviado un evento que supera una "prueba de verdad" determinada. Si se ha enviado al menos un evento que supera la prueba de test dada, la afirmación tendrá éxito:

    Event::assertDispatched(function (OrderShipped $event) use ($order) {
        return $event->order->id === $order->id;
    });

Si simplemente quieres comprobar que un receptor de eventos está escuchando un evento determinado, puedes utilizar el método `assertListening`:

    Event::assertListening(
        OrderShipped::class,
        SendShipmentNotification::class
    );

> **Aviso**  
> Después de llamar a `Event::fake()`, no se ejecutará ningún escuchador de eventos. Por tanto, si tus tests utilizan fábricas de modelos que dependen de eventos, como la creación de un UUID durante el evento de `creación de` un modelo, deberías llamar a Event::fake( `)` **después de** utilizar tus fábricas.

[]()

#### Falsificación de un subconjunto de eventos

Si sólo quieres falsificar escuchadores de eventos para un conjunto específico de eventos, puedes pasarlos al método `fake` o `fakeFor`:

    /**
     * Test order process.
     */
    public function test_orders_can_be_processed()
    {
        Event::fake([
            OrderCreated::class,
        ]);

        $order = Order::factory()->create();

        Event::assertDispatched(OrderCreated::class);

        // Other events are dispatched as normal...
        $order->update([...]);
    }

Puedes falsificar todos los eventos excepto un conjunto de eventos específicos utilizando el método `except`:

    Event::fake()->except([
        OrderCreated::class,
    ]);

[]()

### Falsificación de eventos

Si sólo quieres falsificar escuchadores de eventos para una parte de tu test, puedes utilizar el método `fakeFor`:

    <?php

    namespace Tests\Feature;

    use App\Events\OrderCreated;
    use App\Models\Order;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Support\Facades\Event;
    use Illuminate\Foundation\Testing\WithoutMiddleware;
    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        /**
         * Test order process.
         */
        public function test_orders_can_be_processed()
        {
            $order = Event::fakeFor(function () {
                $order = Order::factory()->create();

                Event::assertDispatched(OrderCreated::class);

                return $order;
            });

            // Events are dispatched as normal and observers will run ...
            $order->update([...]);
        }
    }

[]()

## Falsificación HTTP

El método `fake` de la facade `Http` permite instruir al cliente HTTP para que devuelva respuestas stubbed / dummy cuando se realizan peticiones. Para más información sobre cómo falsificar peticiones HTTP salientes, consulte la [documentación de pruebas](/docs/%7B%7Bversion%7D%7D/http-client#testing) del cliente HTTP.

[]()

## Falsificación de correo

Puede utilizar el método `fake` de la facade `Mail` para evitar el envío de correo. Normalmente, el envío de correo no está relacionado con el código que está probando. Lo más probable es que baste con afirmar que Laravel ha recibido instrucciones de enviar un mailable dado.

Después de llamar al método `falso` de facade `Mail`, puede afirmar que se ordenó el envío de [mailables](/docs/%7B%7Bversion%7D%7D/mail) a los usuarios e incluso inspeccionar los datos que recibieron los mailables:

    <?php

    namespace Tests\Feature;

    use App\Mail\OrderShipped;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Foundation\Testing\WithoutMiddleware;
    use Illuminate\Support\Facades\Mail;
    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        public function test_orders_can_be_shipped()
        {
            Mail::fake();

            // Perform order shipping...

            // Assert that no mailables were sent...
            Mail::assertNothingSent();

            // Assert that a mailable was sent...
            Mail::assertSent(OrderShipped::class);

            // Assert a mailable was sent twice...
            Mail::assertSent(OrderShipped::class, 2);

            // Assert a mailable was not sent...
            Mail::assertNotSent(AnotherMailable::class);
        }
    }

Si está poniendo en cola mailables para su entrega en segundo plano, debe utilizar el método `assertQueued` en lugar de `assertSent`:

    Mail::assertQueued(OrderShipped::class);

    Mail::assertNotQueued(OrderShipped::class);

    Mail::assertNothingQueued();

Puede pasar un closure a los métodos `assertSent`, `assertNotSent`, `assertQueued`, o `assertNotQueued` para afirmar que se envió un mailable que pasa una "prueba de verdad" dada. Si se ha enviado al menos un mensaje que supera la test de veracidad, la afirmación tendrá éxito:

    Mail::assertSent(function (OrderShipped $mail) use ($order) {
        return $mail->order->id === $order->id;
    });

Al llamar a los métodos de aserción de facade `Mail`, la instancia de mailable aceptada por el closure proporcionado expone métodos útiles para examinar el mailable:

    Mail::assertSent(OrderShipped::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email) &&
               $mail->hasCc('...') &&
               $mail->hasBcc('...') &&
               $mail->hasReplyTo('...') &&
               $mail->hasFrom('...') &&
               $mail->hasSubject('...');
    });

La instancia mailable también incluye varios métodos útiles para examinar los adjuntos de un mailable:

    use Illuminate\Mail\Mailables\Attachment;

    Mail::assertSent(OrderShipped::class, function ($mail) {
        return $mail->hasAttachment(
            Attachment::fromPath('/path/to/file')
                    ->as('name.pdf')
                    ->withMime('application/pdf')
        );
    });

    Mail::assertSent(OrderShipped::class, function ($mail) {
        return $mail->hasAttachment(
            Attachment::fromStorageDisk('s3', '/path/to/file')
        );
    });

    Mail::assertSent(OrderShipped::class, function ($mail) use ($pdfData) {
        return $mail->hasAttachment(
            Attachment::fromData(fn () => $pdfData, 'name.pdf')
        );
    });

Puede que haya notado que hay dos métodos para afirmar que el correo no fue enviado: `assertNotSent` y `assertNotQueued`. A veces es posible que desee afirmar que ningún correo fue enviado **o** puesto en cola. Para ello, puede utilizar los métodos `assertNothingOutgoing` y `assertNotOutgoing`:

    Mail::assertNothingOutgoing();

    Mail::assertNotOutgoing(function (OrderShipped $mail) use ($order) {
        return $mail->order->id === $order->id;
    });

[]()

#### Comprobación de contenido enviable por correo

Le sugerimos que compruebe el contenido de sus mailables por separado de las tests que afirman que un mailable determinado fue "enviado" a un usuario específico. Para saber cómo test el contenido de sus mailables, consulte nuestra documentación sobre las pruebas [de mailables](/docs/%7B%7Bversion%7D%7D/mail#testing-mailables).

[]()

## Falsificación de notificación

Puede utilizar el método `fake` de la facade `Notification` para evitar que se envíen notificaciones. Normalmente, el envío de notificaciones no está relacionado con el código que se está probando. Lo más probable es que baste con afirmar que Laravel recibió instrucciones de enviar una notificación determinada.

Después de llamar al método `falso` de la facade `Notificación`, puedes afirmar que [las notificaciones](/docs/%7B%7Bversion%7D%7D/notifications) fueron instruidas para ser enviadas a los usuarios e incluso inspeccionar los datos que las notificaciones recibieron:

    <?php

    namespace Tests\Feature;

    use App\Notifications\OrderShipped;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Foundation\Testing\WithoutMiddleware;
    use Illuminate\Support\Facades\Notification;
    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        public function test_orders_can_be_shipped()
        {
            Notification::fake();

            // Perform order shipping...

            // Assert that no notifications were sent...
            Notification::assertNothingSent();

            // Assert a notification was sent to the given users...
            Notification::assertSentTo(
                [$user], OrderShipped::class
            );

            // Assert a notification was not sent...
            Notification::assertNotSentTo(
                [$user], AnotherNotification::class
            );

            // Assert that a given number of notifications were sent...
            Notification::assertCount(3);
        }
    }

Puede pasar un closure a los métodos `assertSentTo` o `assertNotSentTo` para afirmar que se envió una notificación que pasa una "prueba de verdad" dada. Si se ha enviado al menos una notificación que supera la test de veracidad, la afirmación tendrá éxito:

    Notification::assertSentTo(
        $user,
        function (OrderShipped $notification, $channels) use ($order) {
            return $notification->order->id === $order->id;
        }
    );

[]()

#### Notificaciones bajo demanda

Si el código que está probando envía [notificaciones bajo demanda](/docs/%7B%7Bversion%7D%7D/notifications#on-demand-notifications), puede test que la notificación bajo demanda se envió mediante el método `assertSentOnDemand`:

    Notification::assertSentOnDemand(OrderShipped::class);

Pasando un closure como segundo argumento al método `assertSentOnDemand`, puedes determinar si una notificación bajo demanda se envió a la dirección "route" correcta:

    Notification::assertSentOnDemand(
        OrderShipped::class,
        function ($notification, $channels, $notifiable) use ($user) {
            return $notifiable->routes['mail'] === $user->email;
        }
    );

[]()

## Falsificación de cola

Puede utilizar el método `falso` de la facade `Queue` para evitar que los trabajos en cola sean empujados a la cola. Lo más probable es que baste con afirmar simplemente que Laravel ha recibido instrucciones de enviar un determinado trabajo a la cola, ya que los propios trabajos en cola pueden probarse en otra clase de test.

Después de llamar al método `fake` de la facade `Queue`, se puede afirmar que la aplicación intentó enviar trabajos a la cola:

    <?php

    namespace Tests\Feature;

    use App\Jobs\AnotherJob;
    use App\Jobs\FinalJob;
    use App\Jobs\ShipOrder;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Foundation\Testing\WithoutMiddleware;
    use Illuminate\Support\Facades\Queue;
    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        public function test_orders_can_be_shipped()
        {
            Queue::fake();

            // Perform order shipping...

            // Assert that no jobs were pushed...
            Queue::assertNothingPushed();

            // Assert a job was pushed to a given queue...
            Queue::assertPushedOn('queue-name', ShipOrder::class);

            // Assert a job was pushed twice...
            Queue::assertPushed(ShipOrder::class, 2);

            // Assert a job was not pushed...
            Queue::assertNotPushed(AnotherJob::class);
        }
    }

Puede pasar un closure a los métodos `assertPushed` o `assertNotPushed` para afirmar que se ha enviado un trabajo que supera una "prueba de verdad" determinada. Si se ha enviado al menos un trabajo que supera la test de veracidad dada, la afirmación tendrá éxito:

    Queue::assertPushed(function (ShipOrder $job) use ($order) {
        return $job->order->id === $order->id;
    });

Si sólo necesita falsificar trabajos específicos mientras permite que los demás trabajos se ejecuten normalmente, puede pasar los nombres de clase de los trabajos que deben falsificarse al método `fake`:

    public function test_orders_can_be_shipped()
    {
        Queue::fake([
            ShipOrder::class,
        ]);
        
        // Perform order shipping...

        // Assert a job was pushed twice...
        Queue::assertPushed(ShipOrder::class, 2);
    }

[]()

### Cadenas de trabajos

Los métodos `assertPushedWithChain` y `assertPushedWithoutChain` de facade `Queue` pueden utilizarse para inspeccionar la cadena de trabajos de un trabajo enviado. El método `assertPushedWithChain` acepta el trabajo principal como primer argumento y un array de trabajos encadenados como segundo argumento:

    use App\Jobs\RecordShipment;
    use App\Jobs\ShipOrder;
    use App\Jobs\UpdateInventory;
    use Illuminate\Support\Facades\Queue;

    Queue::assertPushedWithChain(ShipOrder::class, [
        RecordShipment::class,
        UpdateInventory::class
    ]);

Como puedes ver en el ejemplo anterior, el array de trabajos encadenados puede ser un array de nombres de clases de trabajos. Sin embargo, también puedes proporcionar un array de instancias de trabajos reales. Al hacerlo, Laravel se asegurará de que las instancias de trabajo son de la misma clase y tienen los mismos valores de propiedad de los trabajos encadenados enviados por su aplicación:

    Queue::assertPushedWithChain(ShipOrder::class, [
        new RecordShipment,
        new UpdateInventory,
    ]);

Puede utilizar el método `assertPushedWithoutChain` para comprobar que un trabajo se ha enviado sin una cadena de trabajos:

    Queue::assertPushedWithoutChain(ShipOrder::class);

[]()

## Falsificación de almacenamiento

El método `fake` de la facade `Storage` permite generar fácilmente un disco falso que, combinado con las utilidades de generación de ficheros de la clase `Illuminate\Http\UploadedFile`, simplifica enormemente las pruebas de carga de ficheros. Por ejemplo:

    <?php

    namespace Tests\Feature;

    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Foundation\Testing\WithoutMiddleware;
    use Illuminate\Http\UploadedFile;
    use Illuminate\Support\Facades\Storage;
    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        public function test_albums_can_be_uploaded()
        {
            Storage::fake('photos');

            $response = $this->json('POST', '/photos', [
                UploadedFile::fake()->image('photo1.jpg'),
                UploadedFile::fake()->image('photo2.jpg')
            ]);

            // Assert one or more files were stored...
            Storage::disk('photos')->assertExists('photo1.jpg');
            Storage::disk('photos')->assertExists(['photo1.jpg', 'photo2.jpg']);

            // Assert one or more files were not stored...
            Storage::disk('photos')->assertMissing('missing.jpg');
            Storage::disk('photos')->assertMissing(['missing.jpg', 'non-existing.jpg']);

            // Assert that a given directory is empty...
            Storage::disk('photos')->assertDirectoryEmpty('/wallpapers');
        }
    }

Por defecto, el método `falso` borrará todos los archivos de su directorio temporal. Si desea mantener estos archivos, puede utilizar el método "persistentFake" en su lugar. Para más información sobre pruebas de carga de archivos, puede consultar [tests#testing-file-uploads">la documentación de pruebas HTTP](</docs/%7B%7Bversion%7D%7D/http-\<glossary variable=>).

> **Advertencia**  
> El método de `imagen` requiere la [extensión GD](https://www.php.net/manual/en/book.image.php).

[]()

## Interactuando con el tiempo

Al realizar pruebas, puede que ocasionalmente necesites modificar el tiempo devuelto por helpers como `now` o `Illuminate\Support\Carbon::now()`. Afortunadamente, la clase base de test Laravel incluye helpers que permiten manipular la hora actual:

    use Illuminate\Support\Carbon;

    public function testTimeCanBeManipulated()
    {
        // Travel into the future...
        $this->travel(5)->milliseconds();
        $this->travel(5)->seconds();
        $this->travel(5)->minutes();
        $this->travel(5)->hours();
        $this->travel(5)->days();
        $this->travel(5)->weeks();
        $this->travel(5)->years();

        // Freeze time and resume normal time after executing closure...
        $this->freezeTime(function (Carbon $time) {
            // ...
        });

        // Travel into the past...
        $this->travel(-5)->hours();

        // Travel to an explicit time...
        $this->travelTo(now()->subHours(6));

        // Return back to the present time...
        $this->travelBack();
    }
