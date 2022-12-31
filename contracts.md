# Contratos

- [Introducción](#introduction)
  - [Contratos Vs. facades](#contracts-vs-facades)
- [Cuándo utilizar contratos](#when-to-use-contracts)
- [Cómo utilizar los contratos](#how-to-use-contracts)
- [Referencia de contratos](#contract-reference)

[]()

## Introducción

Los "contratos" de Laravel son un conjunto de interfaces que definen los servicios básicos proporcionados por el framework. Por ejemplo, un contrato `Illuminate\Contracts\Queue\Queue` define los métodos necesarios para poner trabajos en cola, mientras que el contrato `Illuminate\Contracts\Mail\Mailer` define los métodos necesarios para enviar correo electrónico.

Cada contrato tiene una implementación correspondiente proporcionada por el framework. Por ejemplo, Laravel proporciona una implementación de cola con una variedad de controladores, y una implementación de mailer que es alimentado por [Symfony Mailer](https://symfony.com/doc/6.0/mailer.html).

Todos los contratos de Laravel viven en [su propio repositorio de GitHub](https://github.com/illuminate/contracts). Esto proporciona un punto de referencia rápido para todos los contratos disponibles, así como un único paquete desacoplado que se puede utilizar cuando se construyen paquetes que interactúan con los servicios de Laravel.

[]()

### Contratos Vs. facades

Las [facades](/docs/%7B%7Bversion%7D%7D/facades) y funciones de ayuda de Laravel proporcionan una forma sencilla de utilizar los servicios de Laravel sin necesidad de teclear y resolver contratos fuera del contenedor de servicios. En la mayoría de los casos, cada facade tiene un contrato equivalente.

A diferencia de facades facades, que no requieren que las exijas en el constructor de tu clase, los contratos te permiten definir dependencias explícitas para tus clases. Algunos desarrolladores prefieren definir explícitamente sus dependencias de esta manera y por lo tanto prefieren usar contratos, mientras que otros desarrolladores disfrutan de la conveniencia de las facades. **En general, la mayoría de las aplicaciones pueden utilizar facades sin problemas durante el desarrollo.**

[]()

## Cuándo utilizar contratos

La decisión de utilizar contratos o facades dependerá del gusto personal y de los gustos de tu equipo de desarrollo. Tanto los contratos como las facades se pueden utilizar para crear aplicaciones Laravel robustas y bien probadas. Los contratos y las facades no son mutuamente excluyentes. Algunas partes de tus aplicaciones pueden usar facades mientras que otras dependen de contratos. Mientras mantengas enfocadas las responsabilidades de tus clases, notarás muy pocas diferencias prácticas entre usar contratos y facades.

En general, la mayoría de las aplicaciones pueden utilizar facades sin problemas durante el desarrollo. Si estás construyendo un paquete que se integra con múltiples frameworks PHP puede que desees utilizar el paquete `illuminate/contracts` para definir tu integración con los servicios de Laravel sin necesidad de requerir las implementaciones concretas de Laravel en el fichero `composer.json` de tu paquete.

[]()

## Cómo utilizar los contratos

Entonces, ¿cómo se obtiene una implementación de un contrato? En realidad es bastante sencillo.

Muchos tipos de clases en Laravel se resuelven a través del contenedor de [servicios](/docs/%7B%7Bversion%7D%7D/container), incluyendo controladores, escuchadores de eventos, middleware, trabajos en cola, e incluso closures rutas. Por lo tanto, para obtener una implementación de un contrato, puedes simplemente "type-hint" la interfaz en el constructor de la clase que se está resolviendo.

Por ejemplo, echa un vistazo a este listener de eventos:

    <?php

    namespace App\Listeners;

    use App\Events\OrderWasPlaced;
    use App\Models\User;
    use Illuminate\Contracts\Redis\Factory;

    class CacheOrderInformation
    {
        /**
         * The Redis factory implementation.
         *
         * @var \Illuminate\Contracts\Redis\Factory
         */
        protected $redis;

        /**
         * Create a new event handler instance.
         *
         * @param  \Illuminate\Contracts\Redis\Factory  $redis
         * @return void
         */
        public function __construct(Factory $redis)
        {
            $this->redis = $redis;
        }

        /**
         * Handle the event.
         *
         * @param  \App\Events\OrderWasPlaced  $event
         * @return void
         */
        public function handle(OrderWasPlaced $event)
        {
            //
        }
    }

Cuando se resuelva el receptor de eventos, el contenedor de servicios leerá las sugerencias de tipo en el constructor de la clase e inyectará el valor apropiado. Para saber más sobre cómo registrar cosas en el contenedor de servicios, consulta [su documentación](/docs/%7B%7Bversion%7D%7D/container).

[]()

## Referencia de contratos

Esta tabla proporciona una referencia rápida a todos los contratos de Laravel y sus facades equivalentes:

| Contrato                                                                                                                                             | Referencias facade        |
| ---------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------- |
| [Illuminate\Contracts\Auth\Access\Authorizable](https://github.com/illuminate/contracts/blob/{{version}}/Auth/Access/Authorizable.php)               |                           |
| [Contratos de autorización de acceso](https://github.com/illuminate/contracts/blob/{{version}}/Auth/Access/Gate.php)                                 | `Gate`                    |
| [Contratos de autenticación autenticables](https://github.com/illuminate/contracts/blob/{{version}}/Auth/Authenticatable.php)                        |                           |
| [IlluminateContractsAuthCanResetPassword](https://github.com/illuminate/contracts/blob/{{version}}/Auth/CanResetPassword.php)                        |                           |
| [IluminateContractsAuth\Factory](https://github.com/illuminate/contracts/blob/{{version}}/Auth/Factory.php)                                          | `Auth`                    |
| [IlluminateContractsAuth\Guard](https://github.com/illuminate/contracts/blob/{{version}}/Auth/Guard.php)                                             | `Auth::guard()`           |
| [IlluminateContracts\Auth\PasswordBroker](https://github.com/illuminate/contracts/blob/{{version}}/Auth/PasswordBroker.php)                          | `Password::broker()`      |
| [IluminateContracts\Auth\PasswordBrokerFactory](https://github.com/illuminate/contracts/blob/{{version}}/Auth/PasswordBrokerFactory.php)             | `Password`                |
| [IlluminateContractsAuth\StatefulGuard](https://github.com/illuminate/contracts/blob/{{version}}/Auth/StatefulGuard.php)                             |                           |
| [IlluminateContractsAuthSupportsBasicAuth](https://github.com/illuminate/contracts/blob/{{version}}/Auth/SupportsBasicAuth.php)                      |                           |
| [IlluminateContracts\Auth\UserProvider](https://github.com/illuminate/contracts/blob/{{version}}/Auth/UserProvider.php)                              |                           |
| [IlluminateContracts\Bus\Dispatcher](https://github.com/illuminate/contracts/blob/{{version}}/Bus/Dispatcher.php)                                    | `Bus`                     |
| [IlluminateContractsBusQueueingDispatcher](https://github.com/illuminate/contracts/blob/{{version}}/Bus/QueueingDispatcher.php)                      | `Bus::dispatchToQueue()`  |
| [IlluminateContractsBroadcasting\Factory](https://github.com/illuminate/contracts/blob/{{version}}/Broadcasting/Factory.php)                         | `Broadcast`               |
| [IlluminateContractsBroadcasting\Broadcaster](https://github.com/illuminate/contracts/blob/{{version}}/Broadcasting/Broadcaster.php)                 | `Broadcast::connection()` |
| [IlluminateContracts\Broadcasting\ShouldBroadcast](https://github.com/illuminate/contracts/blob/{{version}}/Broadcasting/ShouldBroadcast.php)        |                           |
| [Contratos de radiodifusión que deben emitirse ahora](https://github.com/illuminate/contracts/blob/{{version}}/Broadcasting/ShouldBroadcastNow.php)  |                           |
| [IluminarContratosCacheFactory](https://github.com/illuminate/contracts/blob/{{version}}/Cache/Factory.php)                                          | `Cache`                   |
| [IlluminateContracts\cache\Lock](https://github.com/illuminate/contracts/blob/{{version}}/Cache/Lock.php)                                            |                           |
| [IlluminateContracts\cache\LockProvider](https://github.com/illuminate/contracts/blob/{{version}}/Cache/LockProvider.php)                            |                           |
| [IlluminateContracts\cache\Repository](https://github.com/illuminate/contracts/blob/{{version}}/Cache/Repository.php)                                | `Cache::driver()`         |
| [IlluminateContracts\cache\Store](https://github.com/illuminate/contracts/blob/{{version}}/Cache/Store.php)                                          |                           |
| [IluminateContractsConfigRepository](https://github.com/illuminate/contracts/blob/{{version}}/Config/Repository.php)                                 | `Config`                  |
| [IlluminateContractsConsole\Application](https://github.com/illuminate/contracts/blob/{{version}}/Console/Application.php)                           |                           |
| [IlluminateContractsConsoleKernel](https://github.com/illuminate/contracts/blob/{{version}}/Console/Kernel.php)                                      | `Artisan`                 |
| [IlluminateContractsContainerContenedor](https://github.com/illuminate/contracts/blob/{{version}}/Container/Container.php)                           | `App`                     |
| [IlluminateContracts\Cookie\Factory](https://github.com/illuminate/contracts/blob/{{version}}/Cookie/Factory.php)                                    | `Cookie`                  |
| [IluminarContractsCookieQueueingFactory](https://github.com/illuminate/contracts/blob/{{version}}/Cookie/QueueingFactory.php)                        | `Cookie::queue()`         |
| [Illuminate\Contracts\Database\ModelIdentifier](https://github.com/illuminate/contracts/blob/{{version}}/Database/ModelIdentifier.php)               |                           |
| [IlluminateContractsDebug\ExceptionHandler](https://github.com/illuminate/contracts/blob/{{version}}/Debug/ExceptionHandler.php)                     |                           |
| [IlluminateContractsEncryptionEncrypter](https://github.com/illuminate/contracts/blob/{{version}}/Encryption/Encrypter.php)                          | `Crypt`                   |
| [IlluminateContracts\Events\Dispatcher](https://github.com/illuminate/contracts/blob/{{version}}/Events/Dispatcher.php)                              | `Event`                   |
| [IlluminateContracts\Filesystem\Cloud](https://github.com/illuminate/contracts/blob/{{version}}/Filesystem/Cloud.php)                                | `Storage::cloud()`        |
| [IlluminateContracts\Filesystem\Factory](https://github.com/illuminate/contracts/blob/{{version}}/Filesystem/Factory.php)                            | `Storage`                 |
| [IlluminateContracts\Filesystem\Filesystem](https://github.com/illuminate/contracts/blob/{{version}}/Filesystem/Filesystem.php)                      | `Storage::disk()`         |
| [IlluminateContracts\Foundation\Application](https://github.com/illuminate/contracts/blob/{{version}}/Foundation/Application.php)                    | `App`                     |
| [IlluminateContracts\Hashing\Hasher](https://github.com/illuminate/contracts/blob/{{version}}/Hashing/Hasher.php)                                    | `Hash`                    |
| [IlluminateContracts\Http\Kernel](https://github.com/illuminate/contracts/blob/{{version}}/Http/Kernel.php)                                          |                           |
| [IlluminateContracts\Mail\MailQueue](https://github.com/illuminate/contracts/blob/{{version}}/Mail/MailQueue.php)                                    | `Mail::queue()`           |
| [IlluminateContracts\Mail\Mailable](https://github.com/illuminate/contracts/blob/{{version}}/Mail/Mailable.php)                                      |                           |
| [IlluminateContractsMail\Mailer](https://github.com/illuminate/contracts/blob/{{version}}/Mail/Mailer.php)                                           | `Mail`                    |
| [IlluminateContracts\Notifications\Dispatcher](https://github.com/illuminate/contracts/blob/{{version}}/Notifications/Dispatcher.php)                | `Notification`            |
| [IlluminateContractsNotificationsFactory](https://github.com/illuminate/contracts/blob/{{version}}/Notifications/Factory.php)                        | `Notification`            |
| [IlluminateContracts\Pagination\LengthAwarePaginator](https://github.com/illuminate/contracts/blob/{{version}}/Pagination/LengthAwarePaginator.php)  |                           |
| [IlluminateContracts\Pagination\Paginator](https://github.com/illuminate/contracts/blob/{{version}}/Pagination/Paginator.php)                        |                           |
| [IluminarContratosPaginación\NHub](https://github.com/illuminate/contracts/blob/{{version}}/Pipeline/Hub.php)                                        |                           |
| [IluminarContractsPipeline\Pipeline](https://github.com/illuminate/contracts/blob/{{version}}/Pipeline/Pipeline.php)                                 |                           |
| [IlluminateContractsQueueResolverEntidades](https://github.com/illuminate/contracts/blob/{{version}}/Queue/EntityResolver.php)                       |                           |
| [IlluminateContractsQueueEntityResolver](https://github.com/illuminate/contracts/blob/{{version}}/Queue/Factory.php)                                 | `Queue`                   |
| [IlluminateContractsQueueJob](https://github.com/illuminate/contracts/blob/{{version}}/Queue/Job.php)                                                |                           |
| [IlluminateContractsQueueMonitor](https://github.com/illuminate/contracts/blob/{{version}}/Queue/Monitor.php)                                        | `Queue`                   |
| [IluminateContractsQueue\Queue](https://github.com/illuminate/contracts/blob/{{version}}/Queue/Queue.php)                                            | `Queue::connection()`     |
| [IlluminateContractsQueueQueueableCollection](https://github.com/illuminate/contracts/blob/{{version}}/Queue/QueueableCollection.php)                |                           |
| [IlluminateContractsQueueQueueableEntity](https://github.com/illuminate/contracts/blob/{{version}}/Queue/QueueableEntity.php)                        |                           |
| [IlluminateContracts\Queue\ShouldQueue](https://github.com/illuminate/contracts/blob/{{version}}/Queue/ShouldQueue.php)                              |                           |
| [IlluminateContracts\Redis\Factory](https://github.com/illuminate/contracts/blob/{{version}}/Redis/Factory.php)                                      | `Redis`                   |
| [IlluminateContracts\Routing\BindingRegistrar](https://github.com/illuminate/contracts/blob/{{version}}/Routing/BindingRegistrar.php)                | `Route`                   |
| [IlluminateContracts\Routing\Registrar](https://github.com/illuminate/contracts/blob/{{version}}/Routing/Registrar.php)                              | `Route`                   |
| [IlluminateContracts\Routing\ResponseFactory](https://github.com/illuminate/contracts/blob/{{version}}/Routing/ResponseFactory.php)                  | `Response`                |
| [IlluminateContractsRouting\UrlGenerator](https://github.com/illuminate/contracts/blob/{{version}}/Routing/UrlGenerator.php)                         | `URL`                     |
| [IlluminateContractsRouting\UrlRoutable](https://github.com/illuminate/contracts/blob/{{version}}/Routing/UrlRoutable.php)                           |                           |
| [IlluminateContracts\Session\Session](https://github.com/illuminate/contracts/blob/{{version}}/Session/Session.php)                                  | `Session::driver()`       |
| [IlluminateContractsSupportArrayable](https://github.com/illuminate/contracts/blob/{{version}}/Support/Arrayable.php)                                |                           |
| [IlluminateContractsSupport\Htmlable](https://github.com/illuminate/contracts/blob/{{version}}/Support/Htmlable.php)                                 |                           |
| [IlluminateContractsSupportJsonable](https://github.com/illuminate/contracts/blob/{{version}}/Support/Jsonable.php)                                  |                           |
| [IlluminateContractsSupportMessageBag](https://github.com/illuminate/contracts/blob/{{version}}/Support/MessageBag.php)                              |                           |
| [IlluminateContractsSupportMessageProvider](https://github.com/illuminate/contracts/blob/{{version}}/Support/MessageProvider.php)                    |                           |
| [IlluminateContractsSupportRenderable](https://github.com/illuminate/contracts/blob/{{version}}/Support/Renderable.php)                              |                           |
| [IlluminateContractsSupportResponsable](https://github.com/illuminate/contracts/blob/{{version}}/Support/Responsable.php)                            |                           |
| [IlluminateContractsTranslationLoader](https://github.com/illuminate/contracts/blob/{{version}}/Translation/Loader.php)                              |                           |
| [IlluminateContracts\Translation\Translator](https://github.com/illuminate/contracts/blob/{{version}}/Translation/Translator.php)                    | `Lang`                    |
| [IlluminateContracts\Validation\Factory](https://github.com/illuminate/contracts/blob/{{version}}/Validation/Factory.php)                            | `Validator`               |
| [IluminarContractsValidationImplicitRule](https://github.com/illuminate/contracts/blob/{{version}}/Validation/ImplicitRule.php)                      |                           |
| [IlluminateContracts\Validation\Rule](https://github.com/illuminate/contracts/blob/{{version}}/Validation/Rule.php)                                  |                           |
| [IlluminateContractsValidation\ValidatesWhenResolved](https://github.com/illuminate/contracts/blob/{{version}}/Validation/ValidatesWhenResolved.php) |                           |
| [IlluminateContracts\Validation\Validator](https://github.com/illuminate/contracts/blob/{{version}}/Validation/Validator.php)                        | `Validator::make()`       |
| [IlluminateContracts\View\Engine](https://github.com/illuminate/contracts/blob/{{version}}/View/Engine.php)                                          |                           |
| [IlluminateContractsViewFactory](https://github.com/illuminate/contracts/blob/{{version}}/View/Factory.php)                                          | `View`                    |
| [IlluminateContracts\View\View](https://github.com/illuminate/contracts/blob/{{version}}/View/View.php)                                              | `View::make()`            |
