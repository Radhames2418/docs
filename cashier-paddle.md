# Cajero Laravel (Paddle)

- [Introducción](#introduction)
- [Actualización del cajero](#upgrading-cashier)
- [Instalación](#installation)
  - [Sandbox de Pádel](#paddle-sandbox)
  - [Migraciones de Base de Datos](#database-migrations)
- [Configuración](#configuration)
  - [Modelo de Facturación](#billable-model)
  - [Claves API](#api-keys)
  - [Paddle JS](#paddle-js)
  - [Configuración de Divisas](#currency-configuration)
  - [Anulación de modelos por defecto](#overriding-default-models)
- [Conceptos básicos](#core-concepts)
  - [Enlaces de pago](#pay-links)
  - [Pago en línea](#inline-checkout)
  - [Identificación de usuarios](#user-identification)
- [Precios](#prices)
- [Clientes](#customers)
  - [Valores predeterminados del cliente](#customer-defaults)
- [Suscripciones](#subscriptions)
  - [Creación de suscripciones](#creating-subscriptions)
  - [Comprobación del estado de la suscripción](#checking-subscription-status)
  - [Cargos únicos de suscripción](#subscription-single-charges)
  - [Actualización de la información de pago](#updating-payment-information)
  - [Cambio de planes](#changing-plans)
  - [Cantidad de suscripciones](#subscription-quantity)
  - [Modificadores de suscripción](#subscription-modifiers)
  - [Pausa de suscripciones](#pausing-subscriptions)
  - [Cancelación de suscripciones](#cancelling-subscriptions)
- [Pruebas de suscripción](#subscription-trials)
  - [Con método de pago por adelantado](#with-payment-method-up-front)
  - [Sin método de pago por adelantado](#without-payment-method-up-front)
- [Manejo de Webhooks de Paddle](#handling-paddle-webhooks)
  - [Definición de manejadores de eventos Webhook](#defining-webhook-event-handlers)
  - [Verificación de Firmas Webhook](#verifying-webhook-signatures)
- [Cargos simples](#single-charges)
  - [Cargo Simple](#simple-charge)
  - [Cargo de Productos](#charging-products)
  - [Reembolso de pedidos](#refunding-orders)
- [Recibos](#receipts)
  - [Pagos anteriores y futuros](#past-and-upcoming-payments)
- [Gestión de pagos fallidos](#handling-failed-payments)
- [Probando](#testing)

[]()

## Introducción

[Laravel Cashier Paddle](https://github.com/laravel/cashier-paddle) proporciona una interfaz expresiva y fluida para los servicios de facturación de suscripciones [de Paddle](https://paddle.com). Maneja casi todo el código de facturación de suscripciones que tanto temes. Además de la gestión básica de suscripciones, Cajero puede manejar: cupones, intercambio de suscripciones, "cantidades" de suscripciones, periodos de gracia de cancelación, y más.

Mientras trabajas con Cajero te recomendamos que también revises [las guías de usuario](https://developer.paddle.com/guides) y la [documentación API](https://developer.paddle.com/api-reference) de Paddle.

[]()

## Actualizar Cajero

Cuando actualices a una nueva versión de Cajero, es importante que revises cuidadosamente [la guía de actualización](https://github.com/laravel/cashier-paddle/blob/master/UPGRADE.md).

[]()

## Instalación

Primero, instala el paquete Cajero para Paddle usando el gestor de paquetes Composer:

```shell
composer require laravel/cashier-paddle
```

> **Advertencia**  
> Para asegurar que Cajero maneja correctamente todos los eventos de Paddle, recuerda [configurar el manejo de webhooks de Cajero](#handling-paddle-webhooks).

[]()

### Sandbox de Paddle

Durante el desarrollo local y staging, deberías [registrar una cuenta Paddle Sandbox](https://developer.paddle.com/getting-started/sandbox). Esta cuenta te proporcionará un entorno para test y desarrollar tus aplicaciones sin realizar pagos reales. Puede usar los [test-cards">números de tarjeta](<https://developer.paddle.com/getting-started/sandbox#\<glossary variable=>) de [test-cards">test](<https://developer.paddle.com/getting-started/sandbox#\<glossary variable=>) de Paddle para simular varios escenarios de pago.

Cuando utilices el entorno Sandbox de Paddle, debes establecer la variable de entorno `PADDLE_SANDBOX` en `true` dentro del archivo `.env` de tu aplicación:

```ini
PADDLE_SANDBOX=true
```

Cuando haya terminado de desarrollar su aplicación, puede [solicitar una cuenta de vendedor](https://paddle.com) de Paddle. Antes de que su aplicación sea puesta en producción, Paddle necesitará aprobar el dominio de su aplicación.

[]()

### Migraciones de Base de Datos

El proveedor de servicios Cashier registra su propio directorio de migración de base de datos, así que recuerda migrar tu base de datos después de instalar el paquete. Las migraciones de Cajero crearán una nueva tabla de `clientes`. Además, se creará una nueva tabla de `suscripciones` para almacenar todas las suscripciones de sus clientes. Por último, se creará una nueva tabla de `recibos` para almacenar toda la información de recibos de su aplicación:

```shell
php artisan migrate
```

Si necesita sobrescribir las migraciones que se incluyen con Cajero, puede publicarlas utilizando el comando `vendor:publish` Artisan:

```shell
php artisan vendor:publish --tag="cashier-migrations"
```

Si desea evitar que las migraciones de Cajero se ejecuten por completo, puede utilizar el `ignoreMigrations` proporcionado por Cajero. Típicamente, este método debe ser llamado en el método `register` de su `AppServiceProvider`:

    use Laravel\Paddle\Cashier;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Cashier::ignoreMigrations();
    }

[]()

## Configuración

[]()

### Modelo de Facturación

Antes de utilizar Cashier, debe añadir el rasgo `Billable` a la definición de su modelo de usuario. Este rasgo proporciona varios métodos que le permiten realizar tareas comunes de facturación, tales como la creación de suscripciones, la aplicación de cupones y la actualización de la información del método de pago:

    use Laravel\Paddle\Billable;

    class User extends Authenticatable
    {
        use Billable;
    }

Si tienes entidades facturables que no son usuarios, también puedes añadir el trait a esas clases:

    use Illuminate\Database\Eloquent\Model;
    use Laravel\Paddle\Billable;

    class Team extends Model
    {
        use Billable;
    }

[]()

### Claves API

A continuación, debes configurar tus claves de Pádel en el fichero `.env` de tu aplicación. Puedes recuperar tus claves API de Paddle desde el panel de control de Paddle:

```ini
PADDLE_VENDOR_ID=your-paddle-vendor-id
PADDLE_VENDOR_AUTH_CODE=your-paddle-vendor-auth-code
PADDLE_PUBLIC_KEY="your-paddle-public-key"
PADDLE_SANDBOX=true
```

La variable de entorno `PADDLE_SANDBOX` debe establecerse en `true` cuando utilice [el entorno Sandbox de](#paddle-sandbox) Paddle. La variable `PADDLE_SANDBOX` debe establecerse en `false` si estás desplegando tu aplicación a producción y estás usando el entorno de vendedor en vivo de Paddle.

[]()

### Paddle JS

Paddle se basa en su propia librería JavaScript para iniciar el widget de pago de Paddle. Puede cargar la biblioteca JavaScript colocando la directiva `@paddleJS` Blade justo antes de la etiqueta `</head>` de cierre del diseño de su aplicación:

```blade
<head>
    ...

    @paddleJS
</head>
```

[]()

### Configuración de Divisas

La moneda por defecto del Cajero es el Dólar Estadounidense (USD). Puedes cambiar la moneda por defecto definiendo una variable de entorno `CASHIER_CURRENCY` dentro del archivo `.env` de tu aplicación:

```ini
CASHIER_CURRENCY=EUR
```

Además de configurar la moneda del Cajero, también puede especificar la configuración regional que se utilizará cuando se formateen los valores monetarios para mostrarlos en las facturas. Internamente, Cajero utiliza [la clase `NumberFormatter` de PHP](https://www.php.net/manual/en/class.numberformatter.php) para establecer la configuración regional de la moneda:

```ini
CASHIER_CURRENCY_LOCALE=nl_BE
```

> **Advertencia**  
> Para utilizar otras localizaciones distintas de `en`, asegúrese de que la extensión `ext-intl` PHP está instalada y configurada en su servidor.

[]()

### Anulación de modelos por defecto

Puede ampliar los modelos utilizados internamente por Cajero definiendo su propio modelo y ampliando el modelo correspondiente de Cajero:

    use Laravel\Paddle\Subscription as CashierSubscription;

    class Subscription extends CashierSubscription
    {
        // ...
    }

Después de definir su modelo, puede indicar a Cajero que utilice su modelo personalizado a través de la clase `Laravel\Paddle\Cashier`. Normalmente, debes informar a Cashier sobre tus modelos personalizados en el método `boot` de la clase `AppProviders\AppServiceProvider` de tu aplicación:

    use App\Models\Cashier\Receipt;
    use App\Models\Cashier\Subscription;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Cashier::useReceiptModel(Receipt::class);
        Cashier::useSubscriptionModel(Subscription::class);
    }

[]()

## Conceptos básicos

[]()

### Enlaces de pago

Paddle carece de una amplia API CRUD para realizar cambios de estado de suscripción. Por lo tanto, la mayoría de las interacciones con Paddle se realizan a través de su [widget](https://developer.paddle.com/guides/how-tos/checkout/paddle-checkout) de pago. Antes de poder mostrar el widget de pago, debemos generar un "enlace de pago" utilizando Cajero. Un "enlace de pago" informará al widget de pago de la operación de facturación que deseamos realizar:

    use App\Models\User;
    use Illuminate\Http\Request;

    Route::get('/user/subscribe', function (Request $request) {
        $payLink = $request->user()->newSubscription('default', $premium = 34567)
            ->returnTo(route('home'))
            ->create();

        return view('billing', ['payLink' => $payLink]);
    });

Cashier incluye un [componente Blade](/docs/%7B%7Bversion%7D%7D/blade#components) `paddle-button`. Podemos pasar la URL del enlace de pago a este componente como un "prop". Cuando se pulse este botón, se mostrará el widget de pago de Paddle:

```html
<x-paddle-button :url="$payLink" class="px-8 py-4">
    Subscribe
</x-paddle-button>
```

Por defecto, se mostrará un botón con el estilo estándar de Paddle. Puede eliminar todo el estilo de Paddle añadiendo el atributo `data-theme="none"` al componente:

```html
<x-paddle-button :url="$payLink" class="px-8 py-4" data-theme="none">
    Subscribe
</x-paddle-button>
```

El widget de pago de Paddle es asíncrono. Una vez que el usuario crea o actualiza una suscripción dentro del widget, Paddle enviará a tu aplicación webhooks para que puedas actualizar correctamente el estado de la suscripción en nuestra propia base de datos. Por lo tanto, es importante que configure correctamente [los web](#handling-paddle-webhooks) hooks para adaptarse a los cambios de estado de Paddle.

Para más información sobre enlaces de pago, puedes consultar [la documentación de la API de Paddle sobre generación de enlaces de pago](https://developer.paddle.com/api-reference/product-api/pay-links/createpaylink).

> **Advertencia**  
> Después de un cambio de estado de suscripción, el retraso para recibir el webhook correspondiente suele ser mínimo, pero debe tenerlo en cuenta en su aplicación considerando que la suscripción de su usuario podría no estar disponible inmediatamente después de completar el pago.

[]()

#### Creación manual de enlaces de pago

También puede renderizar manualmente un enlace de pago sin utilizar los componentes Blade incorporados en Laravel. Para empezar, genere la URL del enlace de pago como se ha demostrado en los ejemplos anteriores:

    $payLink = $request->user()->newSubscription('default', $premium = 34567)
        ->returnTo(route('home'))
        ->create();

A continuación, simplemente adjunte la URL del enlace de pago a un elemento `a` en su HTML:

    <a href="#!" class="ml-4 paddle_button" data-override="{{ $payLink }}">
        Paddle Checkout
    </a>

[]()

#### Pagos que requieren confirmación adicional

A veces se requiere una verificación adicional para confirmar y procesar un pago. Cuando esto ocurre, Paddle presentará una pantalla de confirmación de pago. Las pantallas de confirmación de pago presentadas por Paddle o Cajero pueden adaptarse al flujo de pago de un banco o emisor de tarjeta específico y pueden incluir confirmación adicional de la tarjeta, un pequeño cargo temporal, autenticación de dispositivo independiente u otras formas de verificación.

[]()

### Pago en línea

Si no desea utilizar el widget de pago "superpuesto" de Paddle, Paddle también ofrece la opción de mostrar el widget en línea. Aunque este enfoque no le permite ajustar ninguno de los campos HTML de la caja, le permite incrustar el widget dentro de su aplicación.

Para facilitarle la introducción del pago en línea, Cajero incluye un componente Blade `paddle-checkout`. Para empezar, debe [generar un enlace](#pay-links) de pago y pasar el enlace de pago al atributo `override` del componente:

```blade
<x-paddle-checkout :override="$payLink" class="w-full" />
```

Para ajustar la altura del componente de pago en línea, puede pasar el atributo `height` al componente Blade:

```blade
<x-paddle-checkout :override="$payLink" class="w-full" height="500" />
```

[]()

#### Pago en línea sin enlaces de pago

También puede personalizar el widget con opciones personalizadas en lugar de utilizar un enlace de pago:

```blade
@php
$options = [
    'product' => $productId,
    'title' => 'Product Title',
];
@endphp

<x-paddle-checkout :options="$options" class="w-full" />
```

Por favor, consulte la [guía de Paddle sobre Inline Checkout](https://developer.paddle.com/guides/how-tos/checkout/inline-checkout) así como su [referencia de parámetros](https://developer.paddle.com/reference/paddle-js/parameters) para más detalles sobre las opciones disponibles del inline checkout.

> **Advertencia**  
> Si desea utilizar también la opción `passthrough` al especificar opciones personalizadas, deberá proporcionar una array clave / valor como valor. Cajero se encargará automáticamente de convertir la array a una cadena JSON. Además, la opción passthrough `customer_id` está reservada para uso interno de Cajero.

[]()

#### Renderizando Manualmente un Pago Inline

También puede generar manualmente un pago en línea sin utilizar los componentes Blade incorporados en Laravel. Para empezar, genere la URL del enlace de pago [como se ha demostrado en los ejemplos anteriores](#pay-links).

A continuación, puede utilizar Paddle.js para inicializar el pago. Para mantener este ejemplo simple, demostraremos esto usando [Alpine.js](https://github.com/alpinejs/alpine); sin embargo, eres libre de traducir este ejemplo a tu propia pila frontend:

```alpine
<div class="paddle-checkout" x-data="{}" x-init="
    Paddle.Checkout.open({
        override: {{ $payLink }},
        method: 'inline',
        frameTarget: 'paddle-checkout',
        frameInitialHeight: 366,
        frameStyle: 'width: 100%; background-color: transparent; border: none;'
    });
">
</div>
```

[]()

### Identificación de usuarios

A diferencia de Stripe, los usuarios de Paddle son únicos en todo Paddle, no únicos por cuenta de Paddle. Debido a esto, la API de Paddle no proporciona actualmente un método para actualizar los detalles de un usuario, como su dirección de correo electrónico. Cuando se generan enlaces de pago, Paddle identifica a los usuarios usando el parámetro `customer_email`. Al crear una suscripción, Paddle intentará hacer coincidir el email proporcionado por el usuario con un usuario existente de Paddle.

A la luz de este comportamiento, hay algunas cosas importantes a tener en cuenta cuando se utiliza Cajero y Paddle. En primer lugar, debe tener en cuenta que aunque las suscripciones en Cashier están vinculadas al mismo usuario de la aplicación, **podrían estar vinculadas a usuarios diferentes dentro de los sistemas internos de** Paddle. En segundo lugar, cada suscripción tiene su propia información de método de pago conectada y también podría tener diferentes direcciones de correo electrónico dentro de los sistemas internos de Paddle (dependiendo de qué correo electrónico se asignó al usuario cuando se creó la suscripción).

Por lo tanto, al mostrar las suscripciones siempre debe informar al usuario qué dirección de correo electrónico o información de método de pago está conectada a la suscripción en una base por suscripción. La recuperación de esta información se puede hacer con los siguientes métodos proporcionados por el modelo `Laravel\Paddle\Subscription`:

    $subscription = $user->subscription('default');

    $subscription->paddleEmail();
    $subscription->paymentMethod();
    $subscription->cardBrand();
    $subscription->cardLastFour();
    $subscription->cardExpirationDate();

Actualmente no hay forma de modificar la dirección de correo electrónico de un usuario a través de la API de Paddle. Cuando un usuario desea actualizar su dirección de correo electrónico dentro de Paddle, la única forma de hacerlo es ponerse en contacto con el servicio de atención al cliente de Paddle. Al comunicarse con Paddle, deben proporcionar el valor `paddleEmail` de la suscripción para ayudar a Paddle a actualizar el usuario correcto.

[]()

## Precios

Paddle te permite personalizar los precios por moneda, esencialmente permitiéndote configurar diferentes precios para diferentes países. Cajero Paddle le permite recuperar todos los precios de un producto determinado utilizando el método `productPrices`. Este método acepta los IDs de los productos de los que desea recuperar los precios:

    use Laravel\Paddle\Cashier;

    $prices = Cashier::productPrices([123, 456]);

La moneda se determinará en base a la dirección IP de la solicitud; sin embargo, puede proporcionar opcionalmente un país específico para recuperar los precios:

    use Laravel\Paddle\Cashier;

    $prices = Cashier::productPrices([123, 456], ['customer_country' => 'BE']);

Una vez obtenidos los precios, puede mostrarlos como desee:

```blade
<ul>
    @foreach ($prices as $price)
        <li>{{ $price->product_title }} - {{ $price->price()->gross() }}</li>
    @endforeach
</ul>
```

También puede mostrar el precio neto (sin impuestos) y mostrar el importe de los impuestos por separado:

```blade
<ul>
    @foreach ($prices as $price)
        <li>{{ $price->product_title }} - {{ $price->price()->net() }} (+ {{ $price->price()->tax() }} tax)</li>
    @endforeach
</ul>
```

Si ha obtenido precios para planes de suscripción, puede mostrar su precio inicial y recurrente por separado:

```blade
<ul>
    @foreach ($prices as $price)
        <li>{{ $price->product_title }} - Initial: {{ $price->initialPrice()->gross() }} - Recurring: {{ $price->recurringPrice()->gross() }}</li>
    @endforeach
</ul>
```

Para más información, [consulte la documentación de la API de Paddle sobre precios](https://developer.paddle.com/api-reference/checkout-api/prices/getprices).

[]()

#### Clientes

Si un usuario ya es cliente y desea mostrar los precios que se aplican a ese cliente, puede hacerlo recuperando los precios directamente de la instancia del cliente:

    use App\Models\User;

    $prices = User::find(1)->productPrices([123, 456]);

Internamente, Cajero utilizará el [método`paddleCountry`](#customer-defaults) del usuario para recuperar los precios en su moneda. Así, por ejemplo, un usuario que viva en Estados Unidos verá los precios en USD mientras que un usuario en Bélgica verá los precios en EUR. Si no se encuentra una moneda que coincida, se utilizará la moneda por defecto del producto. Puede personalizar todos los precios de un producto o plan de suscripción en el panel de control de Pádel.

[]()

#### Cupones

También puede optar por mostrar los precios después de una reducción de cupón. Al llamar al método `productPrices`, los cupones pueden pasarse como una cadena delimitada por comas:

    use Laravel\Paddle\Cashier;

    $prices = Cashier::productPrices([123, 456], [
        'coupons' => 'SUMMERSALE,20PERCENTOFF'
    ]);

A continuación, muestre los precios calculados mediante el método `price`:

```blade
<ul>
    @foreach ($prices as $price)
        <li>{{ $price->product_title }} - {{ $price->price()->gross() }}</li>
    @endforeach
</ul>
```

Puede mostrar los precios `listados` originales (sin descuentos de cupones) utilizando el método `listPrice`:

```blade
<ul>
    @foreach ($prices as $price)
        <li>{{ $price->product_title }} - {{ $price->listPrice()->gross() }}</li>
    @endforeach
</ul>
```

> **Advertencia**  
> Al utilizar la API de precios, Paddle sólo permite aplicar cupones a productos de compra única y no a planes de suscripción.

[]()

## Clientes

[]()

### Clientes por defecto

Cajero le permite definir algunos valores por defecto útiles para sus clientes al crear enlaces de pago. Establecer estos valores predeterminados te permite rellenar previamente la dirección de correo electrónico, el país y el código postal de un cliente para que pueda pasar inmediatamente a la parte de pago del widget de pago. Puedes establecer estos valores por defecto anulando los siguientes métodos en tu modelo facturable:

    /**
     * Get the customer's email address to associate with Paddle.
     *
     * @return string|null
     */
    public function paddleEmail()
    {
        return $this->email;
    }

    /**
     * Get the customer's country to associate with Paddle.
     *
     * This needs to be a 2 letter code. See the link below for supported countries.
     *
     * @return string|null
     * @link https://developer.paddle.com/reference/platform-parameters/supported-countries
     */
    public function paddleCountry()
    {
        //
    }

    /**
     * Get the customer's postal code to associate with Paddle.
     *
     * See the link below for countries which require this.
     *
     * @return string|null
     * @link https://developer.paddle.com/reference/platform-parameters/supported-countries#countries-requiring-postcode
     */
    public function paddlePostcode()
    {
        //
    }

Estos valores predeterminados se utilizarán para cada acción en Cajero que genere un [enlace de pago](#pay-links).

[]()

## Suscripciones

[]()

### Creación de suscripciones

Para crear una suscripción, primero recupere una instancia de su modelo de facturación de su base de datos, que normalmente será una instancia de `App\Models\User`. Una vez que haya recuperado la instancia del modelo, puede utilizar el método `newSubscription` para crear el enlace de pago de la suscripción del modelo:

    use Illuminate\Http\Request;

    Route::get('/user/subscribe', function (Request $request) {
        $payLink = $request->user()->newSubscription('default', $premium = 12345)
            ->returnTo(route('home'))
            ->create();

        return view('billing', ['payLink' => $payLink]);
    });

El primer argumento pasado al método `newSubscription` debe ser el nombre interno de la suscripción. Si su aplicación sólo ofrece una única suscripción, puede llamarlo `predeterminado` o `primario`. Este nombre de suscripción es sólo para uso interno de la aplicación y no debe mostrarse a los usuarios. Además, no debería contener espacios y nunca debería cambiarse después de crear la suscripción. El segundo argumento dado al método `newSubscription` es el plan específico al que se está suscribiendo el usuario. Este valor debe corresponder al identificador del plan en Paddle. El método `returnTo` acepta una URL a la que el usuario será redirigido después de completar con éxito la compra.

El método `create` creará un enlace de pago que puede utilizar para generar un botón de pago. El botón de pago se puede generar utilizando el [componente Blade](/docs/%7B%7Bversion%7D%7D/blade#components) `paddle-button` que se incluye con Cashier Paddle:

```blade
<x-paddle-button :url="$payLink" class="px-8 py-4">
    Subscribe
</x-paddle-button>
```

Después de que el usuario haya finalizado su pago, un webhook `subscription_created` será enviado desde Paddle. Cajero recibirá este webhook y configurará la suscripción para su cliente. Para asegurarte de que todos los webhooks son correctamente recibidos y gestionados por tu aplicación, asegúrate de que has [configurado](#handling-paddle-webhooks) correctamente [la gestión de webhooks](#handling-paddle-webhooks).

[]()

#### Detalles Adicionales

Si desea especificar detalles adicionales del cliente o de la suscripción, puede hacerlo pasándolos como una array de pares clave / valor al método `create`. Para saber más sobre los campos adicionales soportados por Paddle, consulte la documentación de Paddle sobre la [generación de enlaces de pago](https://developer.paddle.com/api-reference/product-api/pay-links/createpaylink):

    $payLink = $user->newSubscription('default', $monthly = 12345)
        ->returnTo(route('home'))
        ->create([
            'vat_number' => $vatNumber,
        ]);

[]()

#### Cupones

Si desea aplicar un cupón al crear la suscripción, puede utilizar el método `withCoupon`:

    $payLink = $user->newSubscription('default', $monthly = 12345)
        ->returnTo(route('home'))
        ->withCoupon('code')
        ->create();

[]()

#### Metadatos

También puede pasar una array de metadatos utilizando el método `withMetadata`:

    $payLink = $user->newSubscription('default', $monthly = 12345)
        ->returnTo(route('home'))
        ->withMetadata(['key' => 'value'])
        ->create();

> **Advertencia**  
> Cuando proporcione metadatos, evite utilizar `subscription_name` como clave de metadatos. Esta clave está reservada para uso interno de Cajero.

[]()

### Comprobación del estado de la suscripción

Una vez que un usuario está suscrito a su aplicación, puede comprobar su estado de suscripción utilizando una variedad de métodos convenientes. En primer lugar, el método `subscribed` devuelve `true` si el usuario tiene una suscripción activa, incluso si la suscripción está actualmente dentro de su periodo de prueba:

    if ($user->subscribed('default')) {
        //
    }

El método `subscribed` también es un gran candidato para un [middleware rutas](/docs/%7B%7Bversion%7D%7D/middleware), permitiéndote filtrar el acceso a rutas y controladores basándote en el estado de suscripción del usuario:

    <?php

    namespace App\Http\Middleware;

    use Closure;

    class EnsureUserIsSubscribed
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
            if ($request->user() && ! $request->user()->subscribed('default')) {
                // This user is not a paying customer...
                return redirect('billing');
            }

            return $next($request);
        }
    }

Si desea determinar si un usuario está aún dentro de su periodo de prueba, puede utilizar el método `onTrial`. Este método puede ser útil para determinar si debe mostrar un aviso al usuario de que aún se encuentra dentro de su periodo de prueba:

    if ($user->subscription('default')->onTrial()) {
        //
    }

El método `subscribedToPlan` se puede utilizar para determinar si el usuario está suscrito a un plan determinado basándose en un ID de plan de Pádel dado. En este ejemplo, determinaremos si la suscripción `por defecto` del usuario está suscrita activamente al plan mensual:

    if ($user->subscribedToPlan($monthly = 12345, 'default')) {
        //
    }

Pasando un array al método `subscribedToPlan`, puede determinar si la suscripción por `defecto` del usuario está suscrita activamente al plan mensual o anual:

    if ($user->subscribedToPlan([$monthly = 12345, $yearly = 54321], 'default')) {
        //
    }

El método `recurring` puede utilizarse para determinar si el usuario está suscrito actualmente y ya no se encuentra dentro de su periodo de prueba:

    if ($user->subscription('default')->recurring()) {
        //
    }

[]()

#### Estado de suscripción cancelada

Para determinar si el usuario fue una vez un suscriptor activo pero ha cancelado su suscripción, puede utilizar el método `cancelado`:

    if ($user->subscription('default')->cancelled()) {
        //
    }

También puede determinar si un usuario ha cancelado su suscripción, pero todavía está en su "período de gracia" hasta que la suscripción expire por completo. Por ejemplo, si un usuario cancela una suscripción el 5 de marzo que originalmente estaba programada para expirar el 10 de marzo, el usuario está en su "período de gracia" hasta el 10 de marzo. Tenga en cuenta que el método `subscribed` sigue devolviendo `true` durante este tiempo:

    if ($user->subscription('default')->onGracePeriod()) {
        //
    }

Para determinar si el usuario ha cancelado su suscripción y ya no se encuentra dentro de su "periodo de gracia", puede utilizar el método `finalizado`:

    if ($user->subscription('default')->ended()) {
        //
    }

[]()

#### Estado de la suscripción vencida

Si falla el pago de una suscripción, ésta se marcará como `vencida`. Cuando su suscripción se encuentre en este estado, no estará activa hasta que el cliente haya actualizado su información de pago. Puede determinar si una suscripción está vencida utilizando el método `pastDue` en la instancia de suscripción:

    if ($user->subscription('default')->pastDue()) {
        //
    }

Cuando una suscripción está vencida, debe indicar al usuario que [actualice su información de pago](#updating-payment-information). Puedes configurar cómo se gestionan las suscripciones vencidas en [los ajustes de suscripción de Paddle](https://vendors.paddle.com/subscription-settings).

Si desea que las suscripciones se sigan considerando activas cuando están `vencidas`, puede utilizar el método `keepPastDueSubscriptionsActive` proporcionado por Cajero. Típicamente, este método debería ser llamado en el método `register` de su `AppServiceProvider`:

    use Laravel\Paddle\Cashier;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Cashier::keepPastDueSubscriptionsActive();
    }

> **Advertencia**  
> Cuando una suscripción está en estado `past_due` no puede ser cambiada hasta que la información de pago haya sido actualizada. Por lo tanto, los métodos `swap` y `updateQuantity` lanzarán una excepción cuando la suscripción esté en estado `past_due`.

[]()

#### Ámbitos de suscripción

La mayoría de los estados de suscripción también están disponibles como ámbitos de consulta para que pueda consultar fácilmente en su base de datos las suscripciones que se encuentran en un estado determinado:

    // Get all active subscriptions...
    $subscriptions = Subscription::query()->active()->get();

    // Get all of the cancelled subscriptions for a user...
    $subscriptions = $user->subscriptions()->cancelled()->get();

A continuación encontrará una lista completa de los ámbitos disponibles:

    Subscription::query()->active();
    Subscription::query()->onTrial();
    Subscription::query()->notOnTrial();
    Subscription::query()->pastDue();
    Subscription::query()->recurring();
    Subscription::query()->ended();
    Subscription::query()->paused();
    Subscription::query()->notPaused();
    Subscription::query()->onPausedGracePeriod();
    Subscription::query()->notOnPausedGracePeriod();
    Subscription::query()->cancelled();
    Subscription::query()->notCancelled();
    Subscription::query()->onGracePeriod();
    Subscription::query()->notOnGracePeriod();

[]()

### Cargos únicos de suscripción

Los cargos únicos de suscripción le permiten cobrar a los suscriptores un cargo único además de sus suscripciones:

    $response = $user->subscription('default')->charge(12.99, 'Support Add-on');

A diferencia de [los cargos únicos](#single-charges), este método cargará inmediatamente el método de pago almacenado del cliente para la suscripción. El importe del cargo debe definirse siempre en la moneda de la suscripción.

[]()

### Actualización de la información de pago

Paddle siempre guarda un método de pago por suscripción. Si desea actualizar el método de pago predeterminado para una suscripción, primero debe generar una "URL de actualización" de la suscripción utilizando el método `updateUrl` en el modelo de suscripción:

    use App\Models\User;

    $user = User::find(1);

    $updateUrl = $user->subscription('default')->updateUrl();

A continuación, puede utilizar la URL generada en combinación con el componente Blade `paddle-button` proporcionado por Cashier para permitir que el usuario inicie el widget Paddle y actualice su información de pago:

```html
<x-paddle-button :url="$updateUrl" class="px-8 py-4">
    Update Card
</x-paddle-button>
```

Cuando un usuario haya terminado de actualizar su información, un webhook `subscription_updated` será enviado por Paddle y los detalles de la suscripción serán actualizados en la base de datos de tu aplicación.

[]()

### Cambio de planes

Después de que un usuario se haya suscrito a tu aplicación, puede que ocasionalmente quiera cambiar a un nuevo plan de suscripción. Para actualizar el plan de suscripción de un usuario, debe pasar el identificador del plan de Paddle al método de `intercambio` de la suscripción:

    use App\Models\User;

    $user = User::find(1);

    $user->subscription('default')->swap($premium = 34567);

Si desea intercambiar planes y facturar inmediatamente al usuario en lugar de esperar a su próximo ciclo de facturación, puede utilizar el método `swapAndInvoice`:

    $user = User::find(1);

    $user->subscription('default')->swapAndInvoice($premium = 34567);

> **Advertencia**  
> Los planes no pueden intercambiarse cuando una prueba está activa. Para más información sobre esta limitación, consulte la [documentación de Paddle](https://developer.paddle.com/api-reference/subscription-api/users/updateuser#usage-notes).

[]()

#### Prorrateos

Por defecto, Paddle prorratea los cargos al cambiar de plan. El método `noProrate` puede usarse para actualizar las suscripciones sin prorratear los cargos:

    $user->subscription('default')->noProrate()->swap($premium = 34567);

[]()

### Cantidad de suscripciones

A veces las suscripciones se ven afectadas por la "cantidad". Por ejemplo, una aplicación de gestión de proyectos puede cobrar 10\$ al mes por proyecto. Para aumentar o disminuir fácilmente la cantidad de su suscripción, utilice los métodos `incrementQuantity` y `decrementQuantity`:

    $user = User::find(1);

    $user->subscription('default')->incrementQuantity();

    // Add five to the subscription's current quantity...
    $user->subscription('default')->incrementQuantity(5);

    $user->subscription('default')->decrementQuantity();

    // Subtract five from the subscription's current quantity...
    $user->subscription('default')->decrementQuantity(5);

Alternativamente, puede establecer una cantidad específica usando el método `updateQuantity`:

    $user->subscription('default')->updateQuantity(10);

El método `noProrate` puede utilizarse para actualizar la cantidad de la suscripción sin prorratear los cargos:

    $user->subscription('default')->noProrate()->updateQuantity(10);

[]()

### Modificadores de suscripción

Los modificadores de suscripción le permiten implementar la [facturación medida](https://developer.paddle.com/guides/how-tos/subscriptions/metered-billing#using-subscription-price-modifiers) o ampliar las suscripciones con complementos.

Por ejemplo, es posible que desee ofrecer un complemento de "Asistencia Premium" con su suscripción estándar. Puede crear este modificador así:

    $modifier = $user->subscription('default')->newModifier(12.99)->create();

El ejemplo anterior añadirá un suplemento de 12,99 \$ a la suscripción. Por defecto, este cargo se repetirá en cada intervalo que haya configurado para la suscripción. Si lo desea, puede añadir una descripción legible al modificador utilizando el método de `descripción` del modificador:

    $modifier = $user->subscription('default')->newModifier(12.99)
        ->description('Premium Support')
        ->create();

Para ilustrar cómo implementar la facturación medida utilizando modificadores, imagine que su aplicación cobra por mensaje SMS enviado por el usuario. Primero, debes crear un plan de 0\$ en tu panel de Paddle. Una vez suscrito el usuario a este plan, puedes añadir modificadores que representen cada cargo individual a la suscripción:

    $modifier = $user->subscription('default')->newModifier(0.99)
        ->description('New text message')
        ->oneTime()
        ->create();

Como puedes ver, hemos invocado el método `oneTime` al crear este modificador. Este método asegurará que el modificador sólo se cobre una vez y no se repita cada intervalo de facturación.

[]()

#### Recuperación de modificadores

Puede recuperar una lista de todos los modificadores de una suscripción mediante el método `modifiers`:

    $modifiers = $user->subscription('default')->modifiers();

    foreach ($modifiers as $modifier) {
        $modifier->amount(); // $0.99
        $modifier->description; // New text message.
    }

[]()

#### Eliminación de modificadores

Los modificadores pueden ser eliminados invocando el método `delete` en una instancia de `Laravel\Paddle\Modifier`:

    $modifier->delete();

[]()

### Pausa de suscripciones

Para pausar una suscripción, llame al método de `pausa` en la suscripción del usuario:

    $user->subscription('default')->pause();

Cuando se pausa una suscripción, Cajero establecerá automáticamente la columna `paused_from` en su base de datos. Esta columna se utiliza para saber cuándo el método `pausado` debe empezar a devolver `verdadero`. Por ejemplo, si un cliente pausa una suscripción el 1 de marzo, pero la suscripción no estaba programada para repetirse hasta el 5 de marzo, el método `pausado` continuará devolviendo `false` hasta el 5 de marzo. Esto se hace porque normalmente se permite a un usuario seguir utilizando una aplicación hasta el final de su ciclo de facturación.

Puede determinar si un usuario ha pausado su suscripción pero todavía está en su "periodo de gracia" usando el método `onPausedGracePeriod`:

    if ($user->subscription('default')->onPausedGracePeriod()) {
        //
    }

Para reanudar una suscripción en pausa, puede llamar al método `unpause` en la suscripción del usuario:

    $user->subscription('default')->unpause();

> **Advertencia**  
> Una suscripción no puede modificarse mientras está en pausa. Si desea cambiar a un plan diferente o actualizar las cantidades, primero debe reanudar la suscripción.

[]()

### Cancelación de suscripciones

Para cancelar una suscripción, llame al método `cancelar` de la suscripción del usuario:

    $user->subscription('default')->cancel();

Cuando se cancela una suscripción, Cajero establecerá automáticamente la columna `ends_at` en su base de datos. Esta columna se utiliza para saber cuándo el método `suscrito` debe empezar a devolver `false`. Por ejemplo, si un cliente cancela una suscripción el 1 de marzo, pero la suscripción no estaba programada para finalizar hasta el 5 de marzo, el método `suscrito` continuará devolviendo `true` hasta el 5 de marzo. Esto se hace porque normalmente se permite a un usuario seguir utilizando una aplicación hasta el final de su ciclo de facturación.

Puede determinar si un usuario ha cancelado su suscripción, pero todavía están en su "período de gracia" utilizando el método `onGracePeriod`:

    if ($user->subscription('default')->onGracePeriod()) {
        //
    }

Si desea cancelar una suscripción inmediatamente, puede llamar al método `cancelNow` en la suscripción del usuario:

    $user->subscription('default')->cancelNow();

> **Advertencia**  
> Las suscripciones de Paddle no pueden reanudarse después de la cancelación. Si tu cliente desea reanudar su suscripción, tendrá que suscribirse a una nueva suscripción.

[]()

## Pruebas de suscripción

[]()

### Con método de pago por adelantado

> **Advertencia**  
> Al probar y recopilar los detalles del método de pago por adelantado, Paddle impide cualquier cambio de suscripción, como intercambiar planes o actualizar cantidades. Si quieres permitir a un cliente cambiar de plan durante un periodo de prueba, la suscripción debe ser cancelada y creada de nuevo.

Si desea ofrecer periodos de prueba a sus clientes sin dejar de recopilar la información del método de pago por adelantado, debe utilizar el método `trialDays` al crear sus enlaces de pago de suscripción:

    use Illuminate\Http\Request;

    Route::get('/user/subscribe', function (Request $request) {
        $payLink = $request->user()->newSubscription('default', $monthly = 12345)
                    ->returnTo(route('home'))
                    ->trialDays(10)
                    ->create();

        return view('billing', ['payLink' => $payLink]);
    });

Este método establecerá la fecha de finalización del periodo de prueba en el registro de suscripción dentro de la base de datos de tu aplicación, así como indicará a Paddle que no comience a facturar al cliente hasta después de esta fecha.

> **Advertencia**  
> Si la suscripción del cliente no se cancela antes de la fecha de finalización del periodo de prueba, se le cobrará tan pronto como expire el periodo de prueba, por lo que debe asegurarse de notificar a sus usuarios la fecha de finalización del periodo de prueba.

Puede determinar si el usuario está dentro de su periodo de prueba utilizando el método `onTrial` de la instancia de usuario o el método `onTrial` de la instancia de suscripción. Los dos ejemplos siguientes son equivalentes:

    if ($user->onTrial('default')) {
        //
    }

    if ($user->subscription('default')->onTrial()) {
        //
    }

Para determinar si una prueba existente ha caducado, puede utilizar los métodos `hasExpiredTrial`:

    if ($user->hasExpiredTrial('default')) {
        //
    }

    if ($user->subscription('default')->hasExpiredTrial()) {
        //
    }

[]()

#### Definición de Días de Prueba en Pádel / Caja

Puede elegir definir cuántos días de prueba reciben sus planes en el panel de Paddle o pasarlos siempre explícitamente usando Cajero. Si elige definir los días de prueba de su plan en Paddle debe tener en cuenta que las nuevas suscripciones, incluyendo las nuevas suscripciones para un cliente que tuvo una suscripción en el pasado, siempre recibirán un periodo de prueba a menos que llame explícitamente al método `trialDays(0)`.

[]()

### Sin método de pago por adelantado

Si desea ofrecer períodos de prueba sin recopilar la información del método de pago del usuario por adelantado, puede establecer la columna `trial_ends_at` en el registro de cliente adjunto a su usuario en la fecha de finalización de prueba que desee. Esto se hace normalmente durante el registro del usuario:

    use App\Models\User;

    $user = User::create([
        // ...
    ]);

    $user->createAsCustomer([
        'trial_ends_at' => now()->addDays(10)
    ]);

Cajero se refiere a este tipo de prueba como "prueba genérica", ya que no está vinculada a ninguna suscripción existente. El método `onTrial` en la instancia de `Usuario` devolverá `true` si la fecha actual no ha pasado el valor de `trial_ends_at`:

    if ($user->onTrial()) {
        // User is within their trial period...
    }

Una vez que esté listo para crear una suscripción real para el usuario, puede utilizar el método `newSubscription` como de costumbre:

    use Illuminate\Http\Request;

    Route::get('/user/subscribe', function (Request $request) {
        $payLink = $user->newSubscription('default', $monthly = 12345)
            ->returnTo(route('home'))
            ->create();

        return view('billing', ['payLink' => $payLink]);
    });

Para recuperar la fecha de finalización de la prueba del usuario, puede utilizar el método `trialEndsAt`. Este método devolverá una instancia de fecha Carbon si el usuario está en periodo de prueba o `null` si no lo está. También puede pasar un parámetro opcional de nombre de suscripción si desea obtener la fecha de finalización de la prueba para una suscripción específica distinta de la predeterminada:

    if ($user->onTrial()) {
        $trialEndsAt = $user->trialEndsAt('main');
    }

Puede utilizar el método `onGenericTrial` si desea saber específicamente que el usuario se encuentra dentro de su período de prueba "genérico" y aún no ha creado una suscripción real:

    if ($user->onGenericTrial()) {
        // User is within their "generic" trial period...
    }

> **Advertencia**  
> No hay forma de extender o modificar un periodo de prueba en una suscripción de Paddle después de que haya sido creada.

[]()

## Manejo de Webhooks de Paddle

Paddle puede notificar a su aplicación una variedad de eventos a través de webhooks. Por defecto, el proveedor de servicios de Cashier registra una ruta que apunta al controlador de webhooks de Cashier. Este controlador manejará todas las peticiones webhook entrantes.

Por defecto, este controlador gestionará automáticamente la cancelación de suscripciones que tengan demasiados cargos fallidos[(definidos por la configuración de reclamaciones de Pad](https://vendors.paddle.com/recover-settings#dunning-form-id)dle), actualizaciones de suscripción y cambios de método de pago; sin embargo, como pronto descubriremos, puede ampliar este controlador para gestionar cualquier evento webhook de Paddle que desee.

Para asegurarse de que su aplicación puede manejar los webhooks de Paddle, asegúrese de [configurar la URL del webhook en el panel de control de Paddle](https://vendors.paddle.com/alerts-webhooks). Por defecto, el controlador webhook de Cajero responde a la ruta `/paddle/webhook` URL. La lista completa de todos los webhooks que debes habilitar en el panel de control de Paddle son:

- Suscripción creada
- Suscripción actualizada
- Suscripción cancelada
- Pago efectuado
- Pago de suscripción realizado

> **Advertencia**  
> Asegúrate de proteger las peticiones entrantes con el middleware [verificación de firma de webhooks](/docs/%7B%7Bversion%7D%7D/cashier-paddle#verifying-webhook-signatures) incluido en Cajero.

[]()

#### Webhooks y protección CSRF

Dado que los webhooks de Paddle necesitan saltarse la [protección CSRF](/docs/%7B%7Bversion%7D%7D/csrf) de Laravel, asegúrate de listar el URI como una excepción en tu `App\Http\middleware\VerifyCsrfToken` middleware o lista la ruta fuera del grupo `web` middleware:

    protected $except = [
        'paddle/*',
    ];

[]()

#### Webhooks y Desarrollo Local

Para que Paddle pueda enviar los webhooks de tu aplicación durante el desarrollo local, necesitarás exponer tu aplicación a través de un servicio de compartición de sitios como [Ngrok](https://ngrok.com/) o [Expose](https://expose.dev/docs/introduction). Si estás desarrollando tu aplicación localmente usando Laravel [Sail](/docs/%7B%7Bversion%7D%7D/sail), puedes usar el [comando](/docs/%7B%7Bversion%7D%7D/sail#sharing-your-site) de compartición de sitios de Sail.

[]()

### Definición de manejadores de eventos Webhook

Cajero gestiona automáticamente la cancelación de la suscripción en cargos fallidos y otros webhooks comunes de Paddle. Sin embargo, si tiene eventos webhook adicionales que le gustaría manejar, puede hacerlo escuchando los siguientes eventos que son enviados por Cajero:

- `Laravel\Paddle\Events\WebhookReceived`
- `Laravel\Paddle\Events\WebhookHandled`

Ambos eventos contienen la carga completa del webhook de Paddle. Por ejemplo, si deseas gestionar el webhook `invoice.payment_succeeded`, puedes registrar un [listener](/docs/%7B%7Bversion%7D%7D/events#defining-listeners) que gestione el evento:

    <?php

    namespace App\Listeners;

    use Laravel\Paddle\Events\WebhookReceived;

    class PaddleEventListener
    {
        /**
         * Handle received Paddle webhooks.
         *
         * @param  \Laravel\Paddle\Events\WebhookReceived  $event
         * @return void
         */
        public function handle(WebhookReceived $event)
        {
            if ($event->payload['alert_name'] === 'payment_succeeded') {
                // Handle the incoming event...
            }
        }
    }

Una vez definido tu listener, puedes registrarlo en el `EventServiceProvider` de tu aplicación:

    <?php

    namespace App\Providers;

    use App\Listeners\PaddleEventListener;
    use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
    use Laravel\Paddle\Events\WebhookReceived;

    class EventServiceProvider extends ServiceProvider
    {
        protected $listen = [
            WebhookReceived::class => [
                PaddleEventListener::class,
            ],
        ];
    }

Cashier también emite eventos dedicados al tipo de webhook recibido. Además de la carga útil completa de Paddle, también contienen los modelos relevantes que se utilizaron para procesar el webhook, como el modelo de facturación, la suscripción o el recibo:

<div class="content-list" markdown="1"/>

- `Laravel\Paddle\Events\PaymentSucceeded`
- `Laravel\Paddle\Events\SubscriptionPaymentSucceeded`
- `Laravel\Paddle\Events\SubscriptionCreated`
- `Laravel\Paddle\Events\SubscriptionUpdated`
- `Laravel\Paddle\Events\SubscriptionCancelled`

[object Object]

También puede anular la ruta webhook incorporada por defecto definiendo la variable de entorno `CASHIER_WEBHOOK` en el archivo `.env` de su aplicación. Este valor debe ser la URL completa de su ruta webhook y debe coincidir con la URL establecida en su panel de control de Paddle:

```ini
CASHIER_WEBHOOK=https://example.com/my-paddle-webhook-url
```

[]()

### Verificación de Firmas Webhook

Para asegurar tus webhooks, puedes usar [las firmas de webhook de Pad](https://developer.paddle.com/webhook-reference/verifying-webhooks)dle. Por comodidad, Cajero incluye automáticamente un middleware que valida que la petición webhook entrante de Paddle es válida.

Para habilitar la verificación webhook, asegúrate de que la variable de entorno `PADDLE_PUBLIC_KEY` está definida en el archivo . `env` de tu aplicación. La clave pública puede recuperarse desde el panel de control de tu cuenta de Paddle.

[]()

## Cargos únicos

[]()

### Cargo Simple

Si deseas realizar un cargo único a un cliente, puedes utilizar el método `charge` en una instancia de modelo facturable para generar un enlace de pago para el cargo. El método de `cargo` acepta el importe del cargo (flotante) como primer argumento y una descripción del cargo como segundo argumento:

    use Illuminate\Http\Request;

    Route::get('/store', function (Request $request) {
        return view('store', [
            'payLink' => $user->charge(12.99, 'Action Figure')
        ]);
    });

Después de generar el enlace de pago, puede utilizar el componente Blade `paddle-button` proporcionado por Cashier para permitir al usuario iniciar el widget Paddle y completar el cargo:

```blade
<x-paddle-button :url="$payLink" class="px-8 py-4">
    Buy
</x-paddle-button>
```

El método `charge` acepta un array como tercer argumento, permitiéndote pasar cualquier opción que desees a la creación del enlace de pago de Paddle. Consulta [la documentación](https://developer.paddle.com/api-reference/product-api/pay-links/createpaylink) de Paddle para obtener más información sobre las opciones disponibles al crear cargos:

    $payLink = $user->charge(12.99, 'Action Figure', [
        'custom_option' => $value,
    ]);

Los cargos se realizan en la moneda especificada en la opción de configuración cashier `.currency`. Por defecto, esto se establece en USD. Puedes anular la moneda por defecto definiendo la variable de entorno `CASHIER_CURRENCY` en el archivo `.env` de tu aplicación:

```ini
CASHIER_CURRENCY=EUR
```

También puede [anular los precios por divisa](https://developer.paddle.com/api-reference/product-api/pay-links/createpaylink#price-overrides) utilizando el sistema de ajuste dinámico de precios de Paddle. Para ello, pasa un array de precios en lugar de una cantidad fija:

    $payLink = $user->charge([
        'USD:19.99',
        'EUR:15.99',
    ], 'Action Figure');

[]()

### Cargo de Productos

Si desea realizar un cargo único contra un producto específico configurado en Paddle, puede utilizar el método `chargeProduct` en una instancia de modelo facturable para generar un enlace de pago:

    use Illuminate\Http\Request;

    Route::get('/store', function (Request $request) {
        return view('store', [
            'payLink' => $request->user()->chargeProduct($productId = 123)
        ]);
    });

A continuación, puede proporcionar el enlace de pago al componente `paddle-button` para permitir al usuario inicializar el widget de Paddle:

```blade
<x-paddle-button :url="$payLink" class="px-8 py-4">
    Buy
</x-paddle-button>
```

El método `chargeProduct` acepta un array como segundo argumento, lo que te permite pasar las opciones que desees a la creación del enlace de pago subyacente de Paddle. Consulte [la documentación](https://developer.paddle.com/api-reference/product-api/pay-links/createpaylink) de Paddle sobre las opciones disponibles para crear cargos:

    $payLink = $user->chargeProduct($productId, [
        'custom_option' => $value,
    ]);

[]()

### Reembolso de Pedidos

Si necesita reembolsar un pedido de Pádel, puede utilizar el método de `reembolso`. Este método acepta el ID del pedido de Pádel como primer argumento. Puede recuperar los recibos de un determinado modelo facturable utilizando el método `recibos`:

    use App\Models\User;

    $user = User::find(1);

    $receipt = $user->receipts()->first();

    $refundRequestId = $user->refund($receipt->order_id);

Puede especificar opcionalmente una cantidad específica a reembolsar así como una razón para el reembolso:

    $receipt = $user->receipts()->first();

    $refundRequestId = $user->refund(
        $receipt->order_id, 5.00, 'Unused product time'
    );

> **Nota**  
> Puede utilizar `$refundRequestId` como referencia para el reembolso cuando se ponga en contacto con el soporte de Paddle.

[]()

## Recibos

Puedes recuperar fácilmente un array de recibos de un modelo facturable a través de la propiedad `receipts`:

    use App\Models\User;

    $user = User::find(1);

    $receipts = $user->receipts;

Al listar los recibos del cliente, puede utilizar los métodos de la instancia de recibo para mostrar la información relevante del recibo. Por ejemplo, puede listar cada recibo en una tabla, permitiendo al usuario descargar fácilmente cualquiera de los recibos:

```html
<table>
    @foreach ($receipts as $receipt)
        <tr>
            <td>{{ $receipt->paid_at->toFormattedDateString() }}</td>
            <td>{{ $receipt->amount() }}</td>
            <td><a href="{{ $receipt->receipt_url }}" target="_blank">Download</a></td>
        </tr>
    @endforeach
</table>
```

[]()

### Pagos pasados y futuros

Puede utilizar los métodos `lastPayment` y `nextPayment` para recuperar y mostrar los pagos pasados o futuros de un cliente para suscripciones recurrentes:

    use App\Models\User;

    $user = User::find(1);

    $subscription = $user->subscription('default');

    $lastPayment = $subscription->lastPayment();
    $nextPayment = $subscription->nextPayment();

Ambos métodos devolverán una instancia de `Laravel\Paddle\Payment`; sin embargo, `nextPayment` devolverá `null` cuando el ciclo de facturación haya terminado (como cuando se ha cancelado una suscripción):

```blade
Next payment: {{ $nextPayment->amount() }} due on {{ $nextPayment->date()->format('d/m/Y') }}
```

[]()

## Gestión de pagos fallidos

Los pagos de suscripciones fallan por varias razones, como tarjetas caducadas o una tarjeta con fondos insuficientes. Cuando esto ocurra, le recomendamos que deje que Paddle gestione los fallos de pago por usted. En concreto, puedes [configurar los correos electrónicos de facturación automática de](https://vendors.paddle.com/subscription-settings) Paddle en tu panel de Paddle.

Alternativamente, puedes realizar una personalización más precisa capturando el webhook [`subscription_payment_failed`](https://developer.paddle.com/webhook-reference/subscription-alerts/subscription-payment-failed) y habilitando la opción "Subscription Payment Failed" en la configuración de Webhook de tu panel de Paddle:

    <?php

    namespace App\Http\Controllers;

    use Laravel\Paddle\Http\Controllers\WebhookController as CashierController;

    class WebhookController extends CashierController
    {
        /**
         * Handle subscription payment failed.
         *
         * @param  array  $payload
         * @return void
         */
        public function handleSubscriptionPaymentFailed($payload)
        {
            // Handle the failed subscription payment...
        }
    }

[]()

## Probando

Mientras realiza las pruebas, debería test manualmente su flujo de facturación para asegurarse de que su integración funciona como se espera.

Para tests automatizadas, incluyendo aquellas ejecutadas en un entorno CI, puedes usar [el Cliente HTTP de Laravel](/docs/%7B%7Bversion%7D%7D/http-client#testing) para simular llamadas HTTP a Paddle. Aunque esto no test las respuestas reales de Paddle, proporciona una forma de test tu aplicación sin llamar realmente a la API de Paddle.
