# Laravel Dusk

- [Introducción](#introduction)
- [Instalación](#installation)
  - [Gestión de instalaciones de ChromeDriver](#managing-chromedriver-installations)
  - [Uso de otros navegadores](#using-other-browsers)
- [Primeros pasos](#getting-started)
  - [Generación de tests](#generating-tests)
  - [Migración de bases de datos](#migrations)
  - [Ejecución de tests](#running-tests)
  - [Manejo del entorno](#environment-handling)
- [Conceptos básicos sobre navegadores](#browser-basics)
  - [Creación de navegadores](#creating-browsers)
  - [Navegación](#navigation)
  - [Cambio de Tamaño de las Ventanas del Navegador](#resizing-browser-windows)
  - [Macros del Navegador](#browser-macros)
  - [Autenticación](#authentication)
  - [Cookies](#cookies)
  - [Ejecutar JavaScript](#executing-javascript)
  - [Captura de pantalla](#taking-a-screenshot)
  - [Almacenamiento en disco de la salida de la consola](#storing-console-output-to-disk)
  - [Almacenamiento en disco del código fuente de la página](#storing-page-source-to-disk)
- [Interacción con elementos](#interacting-with-elements)
  - [Selectores de anochecer](#dusk-selectors)
  - [Texto, Valores y Atributos](#text-values-and-attributes)
  - [Interacción con formularios](#interacting-with-forms)
  - [Adjuntar archivos](#attaching-files)
  - [Pulsar botones](#pressing-buttons)
  - [Enlaces](#clicking-links)
  - [Uso del teclado](#using-the-keyboard)
  - [Uso del ratón](#using-the-mouse)
  - [Diálogos JavaScript](#javascript-dialogs)
  - [Selectores de Alcance](#scoping-selectors)
  - [Esperando Elementos](#waiting-for-elements)
  - [Desplazamiento de un Elemento a la Vista](#scrolling-an-element-into-view)
- [Aserciones disponibles](#available-assertions)
- [Páginas](#pages)
  - [Generación de páginas](#generating-pages)
  - [Configuración de páginas](#configuring-pages)
  - [Navegación por las páginas](#navigating-to-pages)
  - [Selectores abreviados](#shorthand-selectors)
  - [Métodos de página](#page-methods)
- [Componentes](#components)
  - [Generación de componentes](#generating-components)
  - [Uso de componentes](#using-components)
- [Integración Continua](#continuous-integration)
  - [tests-on-heroku-ci">Heroku CI](<#running-\<glossary variable=>)
  - [tests-on-travis-ci">Travis CI](<#running-\<glossary variable=>)
  - [tests-on-github-actions">Acciones GitHub](<#running-\<glossary variable=>)

[]()

## Introducción

[Laravel Dusk](https://github.com/laravel/dusk) proporciona una API de pruebas y automatización del navegador expresiva y fácil de usar. Por defecto, Dusk no requiere que instales JDK o Selenium en tu ordenador local. En su lugar, Dusk utiliza una instalación independiente [de ChromeDriver](https://sites.google.com/chromium.org/driver). Sin embargo, eres libre de utilizar cualquier otro controlador compatible con Selenium que desees.

[]()

## Instalación

Para comenzar, debes instalar [Google Chrome](https://www.google.com/chrome) y agregar la dependencia `laravel/dusk` Composer a tu proyecto:

```shell
composer require --dev laravel/dusk
```

> **AdvertenciaSi**estás registrando manualmente el proveedor de servicios de Dusk, **nunca** debes registrarlo en tu entorno de producción, ya que hacerlo podría llevar a que usuarios arbitrarios puedan autenticarse con tu aplicación.

Después de instalar el paquete Dusk, ejecuta el comando `dusk`:install Artisan. El comando `dusk`:install creará un directorio `tests`, una test ejemplo de Dusk, e instalará el binario Chrome Driver para tu sistema operativo:

```shell
php artisan dusk:install
```

Luego, establece la variable de entorno `APP_URL` en el archivo `.env` de tu aplicación. Este valor debe coincidir con la URL que utilizas para acceder a tu aplicación en un navegador.

> **NotaSi**estás utilizando [Laravel Sail](/docs/%7B%7Bversion%7D%7D/sail) para gestionar tu entorno de desarrollo local, consulta también la documentación de Sail sobre la [configuración y ejecución de tests D](/docs/%7B%7Bversion%7D%7D/sail#laravel-dusk)usk.

[]()

### Gestión de instalaciones de ChromeDriver

Si deseas instalar una versión de ChromeDriver diferente a la instalada por Laravel Dusk mediante el comando `dusk:install`, puedes utilizar el comando `dusk:chrome-driver`:

```shell
# Install the latest version of ChromeDriver for your OS...
php artisan dusk:chrome-driver

# Install a given version of ChromeDriver for your OS...
php artisan dusk:chrome-driver 86

# Install a given version of ChromeDriver for all supported OSs...
php artisan dusk:chrome-driver --all

# Install the version of ChromeDriver that matches the detected version of Chrome / Chromium for your OS...
php artisan dusk:chrome-driver --detect
```

> **AtenciónDusk**requiere que los binarios de `chromedriver` sean ejecutables. Si tienes problemas para ejecutar Dusk, debes asegurarte de que los binarios son ejecutables utilizando el siguiente comando: `chmod -R 0755 vendor/laravel/dusk/bin/`.

[]()

### Uso de otros navegadores

Por defecto, Dusk utiliza Google Chrome y una instalación independiente de [ChromeDriver](https://sites.google.com/chromium.org/driver) para ejecutar tus pruebas tests navegador. Sin embargo, puedes iniciar tu propio servidor Selenium y ejecutar tus tests contra cualquier navegador que desees.

Para empezar, abre tu archivo `tests.php`, que es el caso de test base de Dusk para tu aplicación. Dentro de este archivo, puedes eliminar la llamada al método `startChromeDriver`. Esto evitará que Dusk inicie automáticamente el ChromeDriver:

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        // static::startChromeDriver();
    }

A continuación, puede modificar el método `del controlador` para conectarse a la URL y al puerto de su elección. Además, puede modificar las "capacidades deseadas" que deben pasarse al WebDriver:

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        return RemoteWebDriver::create(
            'http://localhost:4444/wd/hub', DesiredCapabilities::phantomjs()
        );
    }

[]()

## Empezando

[]()

### Generación de tests

Para generar una test Dusk, usa el comando `dusk:make` Artisan. La test generada será colocada en el directorio `tests`:

```shell
php artisan dusk:make LoginTest
```

[]()

### Migración de bases de datos

La mayoría de las tests que escribas interactuarán con páginas que recuperan datos de la base de datos de tu aplicación; sin embargo, tus tests Dusk nunca deben usar el trait ` RefreshDatabase  `. El trait `RefreshDatabase` aprovecha transacciones de base de datos que no serán aplicables o disponibles a través de peticiones HTTP. En su lugar, utilice el rasgo `DatabaseMigrations`, que vuelve a migrar la base de datos para cada test:

    <?php

    namespace Tests\Browser;

    use App\Models\User;
    use Illuminate\Foundation\Testing\DatabaseMigrations;
    use Laravel\Dusk\Chrome;
    use Tests\DuskTestCase;

    class ExampleTest extends DuskTestCase
    {
        use DatabaseMigrations;
    }

> **AdvertenciaLas**bases de datos en memoria**SQLite**no pueden utilizarse al ejecutar tests Dusk. Dado que el navegador se ejecuta dentro de su propio proceso, no podrá acceder a las bases de datos en memoria de otros procesos.

[]()

### Ejecución de tests

Para ejecutar tus tests de navegador, ejecuta el comando `dusk` Artisan:

```shell
php artisan dusk
```

Si tuviste fallos en test la última vez que ejecutaste el comando `dusk`, puedes ahorrar tiempo volviendo a ejecutar primero las tests que fallaron utilizando el comando `dusk:fails`:

```shell
php artisan dusk:fails
```

El comando `dusk` acepta cualquier argumento que sea normalmente aceptado por el ejecutor de test PHPUnit, como por ejemplo permitirle ejecutar sólo las tests de un [grupo](https://phpunit.readthedocs.io/en/9.5/annotations.html#group) determinado:

```shell
php artisan dusk --group=foo
```

> **NotaSi**estás utilizando [Laravel Sail](/docs/%7B%7Bversion%7D%7D/sail) para gestionar tu entorno de desarrollo local, consulta la documentación de Sail sobre la [configuración y ejecución de tests Dusk](/docs/%7B%7Bversion%7D%7D/sail#laravel-dusk).

[]()

#### Iniciando manualmente ChromeDriver

Por defecto, Dusk intentará iniciar automáticamente ChromeDriver. Si esto no funciona en su sistema, puede iniciar ChromeDriver manualmente antes de ejecutar el comando `dusk`. Si eliges iniciar ChromeDriver manualmente, debes comentar la siguiente línea de tu archivo `tests.` php:

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        // static::startChromeDriver();
    }

Además, si inicias ChromeDriver en un puerto distinto de 9515, debes modificar el método `driver` de la misma clase para reflejar el puerto correcto:

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        return RemoteWebDriver::create(
            'http://localhost:9515', DesiredCapabilities::chrome()
        );
    }

[]()

### Manejo del entorno

Para forzar a Dusk a usar su propio archivo de entorno cuando ejecute tests, cree un archivo `.env.dusk.{environment}` en la raíz de su proyecto. Por ejemplo, si va a iniciar el comando `dusk` desde su entorno `local`, debe crear un archivo .env `.dusk.local`.

Cuando ejecute tests, Dusk respaldará su archivo `.env` y renombrará su ambiente Dusk a . `env`. Una vez que las tests hayan finalizado, tu archivo . `env` será restaurado.

[]()

## Conceptos básicos sobre navegadores

[]()

### Creación de navegadores

Para empezar, vamos a escribir una test que verifica que podemos iniciar sesión en nuestra aplicación. Después de generar una test, podemos modificarla para navegar a la página de inicio de sesión, introducir algunas credenciales y hacer clic en el botón "Iniciar sesión". Para crear una instancia del navegador, puedes llamar al método `browse` desde dentro de tu test Dusk:

    <?php

    namespace Tests\Browser;

    use App\Models\User;
    use Illuminate\Foundation\Testing\DatabaseMigrations;
    use Laravel\Dusk\Chrome;
    use Tests\DuskTestCase;

    class ExampleTest extends DuskTestCase
    {
        use DatabaseMigrations;

        /**
         * A basic browser test example.
         *
         * @return void
         */
        public function test_basic_example()
        {
            $user = User::factory()->create([
                'email' => 'taylor@laravel.com',
            ]);

            $this->browse(function ($browser) use ($user) {
                $browser->visit('/login')
                        ->type('email', $user->email)
                        ->type('password', 'password')
                        ->press('Login')
                        ->assertPathIs('/home');
            });
        }
    }

Como puedes ver en el ejemplo anterior, el método `browse` acepta un closure. Una instancia del navegador será pasada automáticamente a este closure por Dusk y es el objeto principal usado para interactuar y hacer aserciones contra tu aplicación.

[]()

#### Creando Múltiples Navegadores

A veces puedes necesitar múltiples navegadores para realizar correctamente una test. Por ejemplo, múltiples navegadores pueden ser necesarios para test una pantalla de chat que interactúa con websockets. Para crear múltiples navegadores, simplemente añade más argumentos de navegador a la firma del closure dado al método `browse`:

    $this->browse(function ($first, $second) {
        $first->loginAs(User::find(1))
              ->visit('/home')
              ->waitForText('Message');

        $second->loginAs(User::find(2))
               ->visit('/home')
               ->waitForText('Message')
               ->type('message', 'Hey Taylor')
               ->press('Send');

        $first->waitForText('Hey Taylor')
              ->assertSee('Jeffrey Way');
    });

[]()

### Navegación

El método `visit` se puede utilizar para navegar a un URI dado dentro de su aplicación:

    $browser->visit('/login');

Puede utilizar el método `visitRoute` para navegar a una [ruta con nombre](/docs/%7B%7Bversion%7D%7D/routing#named-routes):

    $browser->visitRoute('login');

Puede navegar "atrás" y "adelante" utilizando los métodos `atrás` y `adelante`:

    $browser->back();

    $browser->forward();

Puede utilizar el método `refresh` para refrescar la página:

    $browser->refresh();

[]()

### Cambio de Tamaño de las Ventanas del Navegador

Puede utilizar el método `resize` para ajustar el tamaño de la ventana del navegador:

    $browser->resize(1920, 1080);

Puede utilizar el método `maximize` para maximizar la ventana del navegador:

    $browser->maximize();

El método `fitContent` redimensionará la ventana del navegador para ajustarla al tamaño de su contenido:

    $browser->fitContent();

Cuando una test falla, Dusk redimensionará automáticamente el navegador para ajustarse al contenido antes de tomar una captura de pantalla. Puede desactivar esta función llamando al método `disableFitOnFailure` dentro de su test:

    $browser->disableFitOnFailure();

Puede utilizar el método `move` para mover la ventana del navegador a una posición diferente en la pantalla:

    $browser->move($x = 100, $y = 100);

[]()

### Macros del Navegador

Si desea definir un método de navegación personalizado que pueda reutilizar en varias tests, puede utilizar el método `macro` de la clase `Browser`. Típicamente, deberías llamar a este método desde el método de `arranque` de un [proveedor de servicios](/docs/%7B%7Bversion%7D%7D/providers):

    <?php

    namespace App\Providers;

    use Illuminate\Support\ServiceProvider;
    use Laravel\Dusk\Browser;

    class DuskServiceProvider extends ServiceProvider
    {
        /**
         * Register Dusk's browser macros.
         *
         * @return void
         */
        public function boot()
        {
            Browser::macro('scrollToElement', function ($element = null) {
                $this->script("$('html, body').animate({ scrollTop: $('$element').offset().top }, 0);");

                return $this;
            });
        }
    }

La función `macro` acepta un nombre como primer argumento y un closure como segundo. El closure de la macro se ejecutará cuando se llame a la macro como método en una instancia `del navegador`:

    $this->browse(function ($browser) use ($user) {
        $browser->visit('/pay')
                ->scrollToElement('#credit-card-details')
                ->assertSee('Enter Credit Card Details');
    });

[]()

### Autenticación

A menudo, estará probando páginas que requieren autenticación. Puede usar el método `loginAs` de Dusk para evitar interactuar con la pantalla de login de su aplicación durante cada test. El método `loginAs` acepta una clave primaria asociada a tu modelo autenticable o a una instancia de modelo autenticable:

    use App\Models\User;

    $this->browse(function ($browser) {
        $browser->loginAs(User::find(1))
              ->visit('/home');
    });

> **WarningDespués de**utilizar el método `loginAs`, la sesión de usuario se mantendrá para todas las tests dentro del archivo.

[]()

### Cookies

Puede utilizar el método `cookie` para obtener o establecer el valor de una cookie encriptada. Por defecto, todas las cookies creadas por Laravel están encriptadas:

    $browser->cookie('name');

    $browser->cookie('name', 'Taylor');

Puedes usar el método `plainCookie` para obtener o establecer el valor de una cookie no encriptada:

    $browser->plainCookie('name');

    $browser->plainCookie('name', 'Taylor');

Puedes usar el método `deleteCookie` para borrar la cookie dada:

    $browser->deleteCookie('name');

[]()

### Ejecutar JavaScript

Puede utilizar el método `script` para ejecutar sentencias JavaScript arbitrarias dentro del navegador:

    $browser->script('document.documentElement.scrollTop = 0');

    $browser->script([
        'document.body.scrollTop = 0',
        'document.documentElement.scrollTop = 0',
    ]);

    $output = $browser->script('return window.location.pathname');

[]()

### Captura de pantalla

Puede utilizar el método `screenshot` para tomar una captura de pantalla y almacenarla con el nombre de archivo dado. Todas las capturas de pantalla se almacenarán en el directorio `tests`:

    $browser->screenshot('filename');

El método `responsiveScreenshots` se puede utilizar para tomar una serie de capturas de pantalla en varios puntos de interrupción:

    $browser->responsiveScreenshots('filename');

[]()

### Almacenamiento en disco de la salida de la consola

Puede utilizar el método `storeConsoleLog` para escribir la salida de la consola del navegador actual en el disco con el nombre de archivo dado. La salida de la consola se almacenará en el directorio `tests`:

    $browser->storeConsoleLog('filename');

[]()

### Almacenamiento en disco del código fuente de la página

Puede utilizar el método `storeSource` para escribir la fuente de la página actual en el disco con el nombre de archivo dado. El código fuente de la página se almacenará en el directorio `tests`:

    $browser->storeSource('filename');

[]()

## Interacción con elementos

[]()

### Selectores de anochecer

Elegir buenos selectores CSS para interactuar con elementos es una de las partes más difíciles de escribir tests Dusk. Con el tiempo, los cambios en el frontend pueden causar que selectores CSS como los siguientes rompan tus tests:

    // HTML...

    <button>Login</button>

    // Test...

    $browser->click('.login-page .container div > button');

Los selectores de Dusk te permiten concentrarte en escribir tests efectivas en lugar de recordar selectores CSS. Para definir un selector, añade un atributo `dusk` a tu elemento HTML. Luego, cuando interactúes con un navegador Dusk, prefija el selector con `@` para manipular el elemento adjunto dentro de tu test:

    // HTML...

    <button dusk="login-button">Login</button>

    // Test...

    $browser->click('@login-button');

[]()

### Texto, Valores y Atributos

[]()

#### Recuperación y establecimiento de valores

Dusk proporciona varios métodos para interactuar con el valor actual, el texto mostrado y los atributos de los elementos de la página. Por ejemplo, para obtener el "valor" de un elemento que coincide con un selector CSS o Dusk dado, utilice el método `value`:

    // Retrieve the value...
    $value = $browser->value('selector');

    // Set the value...
    $browser->value('selector', 'value');

Puede utilizar el método `inputValue` para obtener el "valor" de un elemento de entrada que tenga un nombre de campo determinado:

    $value = $browser->inputValue('field');

[]()

#### Recuperación de texto

El método `text` puede utilizarse para recuperar el texto mostrado de un elemento que coincida con el selector dado:

    $text = $browser->text('selector');

[]()

#### Recuperación de atributos

Por último, el método `attribute` puede utilizarse para recuperar el valor de un atributo de un elemento que coincida con el selector dado:

    $attribute = $browser->attribute('selector', 'value');

[]()

### Interacción con formularios

[]()

#### Introducir valores

Dusk proporciona una variedad de métodos para interactuar con formularios y elementos de entrada. En primer lugar, veamos un ejemplo de introducción de texto en un campo de entrada:

    $browser->type('email', 'taylor@laravel.com');

Observa que, aunque el método acepta uno si es necesario, no estamos obligados a pasar un selector CSS al método `type`. Si no se proporciona un selector CSS, Dusk buscará un campo de `entrada` o `textarea` con el atributo `name` dado.

Para añadir texto a un campo sin borrar su contenido, puedes usar el método `append`:

    $browser->type('tags', 'foo')
            ->append('tags', ', bar, baz');

Puedes borrar el valor de una entrada usando el método `clear`:

    $browser->clear('email');

Puedes instruir a Dusk para que escriba lentamente usando el método `typeSlowly`. Por defecto, Dusk hará una pausa de 100 milisegundos entre pulsaciones de teclas. Para personalizar la cantidad de tiempo entre pulsaciones de teclas, puedes pasar el número apropiado de milisegundos como tercer argumento del método:

    $browser->typeSlowly('mobile', '+1 (202) 555-5555');

    $browser->typeSlowly('mobile', '+1 (202) 555-5555', 300);

Puede utilizar el método `appendSlowly` para añadir texto lentamente:

    $browser->type('tags', 'foo')
            ->appendSlowly('tags', ', bar, baz');

[]()

#### Desplegables

Para seleccionar un valor disponible en un elemento de `selección`, puede utilizar el método `select`. Al igual que el método `type`, el método `select` no requiere un selector CSS completo. Al pasar un valor al método `select`, debe pasar el valor de la opción subyacente en lugar del texto mostrado:

    $browser->select('size', 'Large');

Puede seleccionar una opción aleatoria omitiendo el segundo argumento:

    $browser->select('size');

Proporcionando un array como segundo argumento al método `select`, puede ordenar al método que seleccione múltiples opciones:

    $browser->select('categories', ['Art', 'Music']);

[]()

#### Casillas de verificación

Para "marcar" una casilla de entrada, puede utilizar el método `check`. Como muchos otros métodos relacionados con la entrada, no se requiere un selector CSS completo. Si no se puede encontrar un selector CSS coincidente, Dusk buscará una casilla de verificación con un atributo de `nombre` coincidente:

    $browser->check('terms');

El método `uncheck` puede utilizarse para "desmarcar" una entrada de casilla de verificación:

    $browser->uncheck('terms');

[]()

#### Botones de radio

Para "seleccionar" una opción de entrada `radio`, puede utilizar el método `radio`. Como muchos otros métodos relacionados con entradas, no se requiere un selector CSS completo. Si no se puede encontrar un selector CSS coincidente, Dusk buscará una entrada de `radio` con atributos de `nombre` y `valor` coincidentes:

    $browser->radio('size', 'large');

[]()

### Adjuntar archivos

El método `attach` puede utilizarse para adjuntar un archivo a un elemento de entrada de `archivo`. Como muchos otros métodos relacionados con la entrada, no se requiere un selector CSS completo. Si no se puede encontrar un selector CSS coincidente, Dusk buscará una entrada de `archivo` con un atributo de `nombre` coincidente:

    $browser->attach('photo', __DIR__.'/photos/mountains.png');

> **AdvertenciaLa**función adjuntar requiere que la extensión `Zip` PHP esté instalada y habilitada en su servidor.

[]()

### Pulsar botones

El método `press` puede usarse para pulsar un elemento botón en la página. El argumento dado al método `press` puede ser el texto a mostrar del botón o un selector CSS / Dusk:

    $browser->press('Login');

Cuando se envían formularios, muchas aplicaciones desactivan el botón de envío del formulario después de pulsarlo y lo vuelven a activar cuando se completa la petición HTTP de envío del formulario. Para pulsar un botón y esperar a que se vuelva a activar, puede utilizar el método `pressAndWaitFor`:

    // Press the button and wait a maximum of 5 seconds for it to be enabled...
    $browser->pressAndWaitFor('Save');

    // Press the button and wait a maximum of 1 second for it to be enabled...
    $browser->pressAndWaitFor('Save', 1);

[]()

### Enlaces

Para pulsar un enlace, puede utilizar el método `clickLink` en la instancia del navegador. El método `clickLink` hará clic en el enlace que tenga el texto mostrado:

    $browser->clickLink($linkText);

Puede utilizar el método `seeLink` para determinar si un enlace con el texto indicado es visible en la página:

    if ($browser->seeLink($linkText)) {
        // ...
    }

> **WarningEstos**métodos interactúan con jQuery. Si jQuery no está disponible en la página, Dusk lo inyectará automáticamente en la página para que esté disponible durante la duración de la test.

[]()

### Uso del teclado

El método `keys` le permite proporcionar secuencias de entrada más complejas a un elemento dado que las permitidas normalmente por el método `type`. Por ejemplo, puede indicar a Dusk que mantenga pulsadas las teclas modificadoras mientras introduce valores. En este ejemplo, la tecla `shift` se mantendrá pulsada mientras se introduce `taylor` en el elemento que coincide con el selector dado. Después de escribir `taylor`, se escribirá `swift` sin ninguna tecla modificadora:

    $browser->keys('selector', ['{shift}', 'taylor'], 'swift');

Otro caso de uso valioso para el método `keys` es enviar una combinación de "atajo de teclado" al selector CSS primario para tu aplicación:

    $browser->keys('.app', ['{command}', 'j']);

> **NotaTodas**las teclas modificadoras como `{command}` están envueltas en caracteres `{}`, y coinciden con las constantes definidas en la clase `Facebook\WebDriver\WebDriverKeys`, que puede [encontrarse en GitHub](https://github.com/php-webdriver/php-webdriver/blob/master/lib/WebDriverKeys.php).

[]()

### Uso del ratón

[]()

#### Clic en elementos

El método `click` puede ser usado para hacer click en un elemento que coincida con el selector CSS o Dusk dado:

    $browser->click('.selector');

El método `clickAtXPath` puede ser usado para hacer click en un elemento que coincida con la expresión XPath dada:

    $browser->clickAtXPath('//div[@class = "selector"]');

El método `clickAtPoint` puede utilizarse para hacer clic en el elemento superior en un par de coordenadas dadas relativas al área visible del navegador:

    $browser->clickAtPoint($x = 0, $y = 0);

El método `doubleClick` puede utilizarse para simular el doble clic de un ratón:

    $browser->doubleClick();

El método `rightClick` puede utilizarse para simular el clic derecho de un ratón:

    $browser->rightClick();

    $browser->rightClick('.selector');

El método `clickAndHold` puede utilizarse para simular que se pulsa y se mantiene pulsado un botón del ratón. Una llamada posterior al método `releaseMouse` deshará este comportamiento y liberará el botón del ratón:

    $browser->clickAndHold()
            ->pause(1000)
            ->releaseMouse();

[]()

#### Mouseover

El método `mouseover` puede utilizarse cuando se necesita mover el ratón sobre un elemento que coincida con el selector CSS o Dusk dado:

    $browser->mouseover('.selector');

[]()

#### Arrastrar y soltar

El método `arrastrar` puede utilizarse para arrastrar un elemento que coincida con el selector dado a otro elemento:

    $browser->drag('.from-selector', '.to-selector');

También puede arrastrar un elemento en una sola dirección:

    $browser->dragLeft('.selector', $pixels = 10);
    $browser->dragRight('.selector', $pixels = 10);
    $browser->dragUp('.selector', $pixels = 10);
    $browser->dragDown('.selector', $pixels = 10);

Finalmente, puedes arrastrar un elemento por un desplazamiento dado:

    $browser->dragOffset('.selector', $x = 10, $y = 10);

[]()

### Diálogos JavaScript

Dusk proporciona varios métodos para interactuar con diálogos JavaScript. Por ejemplo, puedes usar el método `waitForDialog` para esperar a que aparezca un diálogo JavaScript. Este método acepta un argumento opcional que indica cuántos segundos hay que esperar a que aparezca el diálogo:

    $browser->waitForDialog($seconds = null);

El método `assertDialogOpened` se puede utilizar para afirmar que un diálogo se ha mostrado y contiene el mensaje dado:

    $browser->assertDialogOpened('Dialog message');

Si el diálogo JavaScript contiene un prompt, puede utilizar el método `typeInDialog` para escribir un valor en el prompt:

    $browser->typeInDialog('Hello World');

Para cerrar un diálogo JavaScript abierto haciendo clic en el botón "Aceptar", puedes invocar el método `acceptDialog`:

    $browser->acceptDialog();

Para cerrar un diálogo JavaScript abierto pulsando el botón "Cancelar", puede invocar el método `dismissDialog`:

    $browser->dismissDialog();

[]()

### Selectores de Alcance

A veces es posible que desee realizar varias operaciones, mientras que el ámbito de todas las operaciones dentro de un selector dado. Por ejemplo, es posible que desee afirmar que un texto sólo existe dentro de una tabla y, a continuación, haga clic en un botón dentro de esa tabla. Para ello puede utilizar el método `with`. Todas las operaciones realizadas dentro del closure dado al método `with` se aplicarán al selector original:

    $browser->with('.table', function ($table) {
        $table->assertSee('Hello World')
              ->clickLink('Delete');
    });

Ocasionalmente puede necesitar ejecutar aserciones fuera del ámbito actual. Para ello puede utilizar los métodos `elsewhere` y `elsewhereWhenAvailable`:

     $browser->with('.table', function ($table) {
        // Current scope is `body .table`...

        $browser->elsewhere('.page-title', function ($title) {
            // Current scope is `body .page-title`...
            $title->assertSee('Hello World');
        });

        $browser->elsewhereWhenAvailable('.page-title', function ($title) {
            // Current scope is `body .page-title`...
            $title->assertSee('Hello World');
        });
     });

[]()

### Esperando Elementos

Cuando se prueban aplicaciones que utilizan JavaScript de forma extensiva, a menudo es necesario "esperar" a que ciertos elementos o datos estén disponibles antes de proceder con una test. Dusk hace que esto sea pan comido. Utilizando diversos métodos, puede esperar a que los elementos se hagan visibles en la página o incluso esperar a que una determinada expresión de JavaScript se evalúe como `verdadera`.

[]()

#### Esperando

Si sólo necesita pausar la test durante un número determinado de milisegundos, utilice el método `pause`:

    $browser->pause(1000);

Si necesita pausar la test sólo si una condición dada es `verdadera`, utilice el método `pauseIf`:

    $browser->pauseIf(App::environment('production'), 1000);

Del mismo modo, si necesita pausar la test a menos que una condición dada sea `verdadera`, puede utilizar el método `pauseUnless`:

    $browser->pauseUnless(App::environment('testing'), 1000);

[]()

#### Esperar selectores

El método `waitFor` puede utilizarse para detener la ejecución de la test hasta que el elemento que coincida con el selector CSS o Dusk se muestre en la página. Por defecto, la test se detendrá un máximo de cinco segundos antes de lanzar una excepción. Si es necesario, puede pasar un umbral de tiempo de espera personalizado como segundo argumento del método:

    // Wait a maximum of five seconds for the selector...
    $browser->waitFor('.selector');

    // Wait a maximum of one second for the selector...
    $browser->waitFor('.selector', 1);

También puede esperar hasta que el elemento que coincida con el selector dado contenga el texto dado:

    // Wait a maximum of five seconds for the selector to contain the given text...
    $browser->waitForTextIn('.selector', 'Hello World');

    // Wait a maximum of one second for the selector to contain the given text...
    $browser->waitForTextIn('.selector', 'Hello World', 1);

También puede esperar hasta que el elemento que coincide con el selector dado no aparezca en la página:

    // Wait a maximum of five seconds until the selector is missing...
    $browser->waitUntilMissing('.selector');

    // Wait a maximum of one second until the selector is missing...
    $browser->waitUntilMissing('.selector', 1);

O puede esperar hasta que el elemento que coincide con el selector dado esté habilitado o deshabilitado:

    // Wait a maximum of five seconds until the selector is enabled...
    $browser->waitUntilEnabled('.selector');

    // Wait a maximum of one second until the selector is enabled...
    $browser->waitUntilEnabled('.selector', 1);

    // Wait a maximum of five seconds until the selector is disabled...
    $browser->waitUntilDisabled('.selector');

    // Wait a maximum of one second until the selector is disabled...
    $browser->waitUntilDisabled('.selector', 1);

[]()

#### Seleccionar selectores cuando estén disponibles

En ocasiones, es posible que desee esperar a que aparezca un elemento que coincida con un selector determinado y, a continuación, interactuar con el elemento. Por ejemplo, es posible que desee esperar hasta que una ventana modal esté disponible y, a continuación, pulse el botón "Aceptar" dentro del modal. Para ello se puede utilizar el método `whenAvailable`. Todas las operaciones con elementos que se realicen dentro del closure dado se aplicarán al selector original:

    $browser->whenAvailable('.modal', function ($modal) {
        $modal->assertSee('Hello World')
              ->press('OK');
    });

[]()

#### Esperar texto

El método `waitForText` puede utilizarse para esperar hasta que el texto dado se muestre en la página:

    // Wait a maximum of five seconds for the text...
    $browser->waitForText('Hello World');

    // Wait a maximum of one second for the text...
    $browser->waitForText('Hello World', 1);

Puede utilizar el método `waitUntilMissingText` para esperar hasta que el texto mostrado haya sido eliminado de la página:

    // Wait a maximum of five seconds for the text to be removed...
    $browser->waitUntilMissingText('Hello World');

    // Wait a maximum of one second for the text to be removed...
    $browser->waitUntilMissingText('Hello World', 1);

[]()

#### Esperar enlaces

El método `waitForLink` puede utilizarse para esperar hasta que el texto del enlace se muestre en la página:

    // Wait a maximum of five seconds for the link...
    $browser->waitForLink('Create');

    // Wait a maximum of one second for the link...
    $browser->waitForLink('Create', 1);

[]()

#### Esperar entradas

El método `waitForInput` puede utilizarse para esperar hasta que el campo de entrada dado sea visible en la página:

    // Wait a maximum of five seconds for the input...
    $browser->waitForInput($field);

    // Wait a maximum of one second for the input...
    $browser->waitForInput($field, 1);

[]()

#### Esperar la ubicación de la página

Cuando se hace una aserción de ruta como `$browser->assertPathIs('/home')`, la aserción puede fallar si `window.location.pathname` se está actualizando de forma asíncrona. Puedes usar el método `waitForLocation` para esperar a que la ubicación sea un valor dado:

    $browser->waitForLocation('/secret');

El método `waitForLocation` también se puede utilizar para esperar a que la ubicación actual de la ventana sea una URL completa:

    $browser->waitForLocation('https://example.com/path');

También puede esperar la ubicación de una [ruta con nombre](/docs/%7B%7Bversion%7D%7D/routing#named-routes):

    $browser->waitForRoute($routeName, $parameters);

[]()

#### Esperar a que se recargue una página

Si necesitas esperar a que una página se recargue después de realizar una acción, utiliza el método `waitForReload`:

    use Laravel\Dusk\Browser;

    $browser->waitForReload(function (Browser $browser) {
        $browser->press('Submit');
    })
    ->assertSee('Success!');

Dado que la necesidad de esperar a que se recargue la página suele producirse después de hacer clic en un botón, puede utilizar el método `clickAndWaitForReload` por comodidad:

    $browser->clickAndWaitForReload('.selector')
            ->assertSee('something');

[]()

#### Esperar en expresiones JavaScript

En ocasiones, es posible que desee detener la ejecución de una test hasta que una expresión JavaScript determinada se evalúe como `verdadera`. Para ello, utilice el método `waitUntil`. Al pasar una expresión a este método, no es necesario incluir la palabra clave `return` ni un punto y coma al final:

    // Wait a maximum of five seconds for the expression to be true...
    $browser->waitUntil('App.data.servers.length > 0');

    // Wait a maximum of one second for the expression to be true...
    $browser->waitUntil('App.data.servers.length > 0', 1);

[]()

#### Esperando Expresiones Vue

Los métodos `waitUntilVue` y `waitUntilVueIsNot` pueden utilizarse para esperar hasta que un atributo de [un componente Vue](https://vuejs.org) tenga un valor determinado:

    // Wait until the component attribute contains the given value...
    $browser->waitUntilVue('user.name', 'Taylor', '@user');

    // Wait until the component attribute doesn't contain the given value...
    $browser->waitUntilVueIsNot('user.name', null, '@user');

[]()

#### Esperando Eventos JavaScript

El método `waitForEvent` puede utilizarse para pausar la ejecución de una test hasta que se produzca un evento JavaScript:

    $browser->waitForEvent('load');

El escuchador de eventos se adjunta al ámbito actual, que es el elemento `body` por defecto. Cuando se utiliza un selector de ámbito, el receptor de eventos se adjuntará al elemento coincidente:

    $browser->with('iframe', function ($iframe) {
        // Wait for the iframe's load event...
        $iframe->waitForEvent('load');
    });

También puedes proporcionar un selector como segundo argumento al método `waitForEvent` para adjuntar el receptor de eventos a un elemento específico:

    $browser->waitForEvent('load', '.selector');

También puedes esperar eventos en los objetos `documento` y `ventana`:

    // Wait until the document is scrolled...
    $browser->waitForEvent('scroll', 'document');

    // Wait a maximum of five seconds until the window is resized...
    $browser->waitForEvent('resize', 'window', 5);

[]()

#### Esperando con un Callback

Muchos de los métodos "wait" de Dusk se basan en el método `waitUsing` subyacente. Puedes usar este método directamente para esperar a que un closure dado devuelva `true`. El método `waitUsing` acepta el número máximo de segundos a esperar, el intervalo en el que el closure debe ser evaluado, el closure, y un mensaje de fallo opcional:

    $browser->waitUsing(10, 1, function () use ($something) {
        return $something->isReady();
    }, "Something wasn't ready in time.");

[]()

### Desplazamiento de un Elemento a la Vista

A veces no puedes hacer click en un elemento porque está fuera del área visible del navegador. El método `scrollIntoView` desplazará la ventana del navegador hasta que el elemento en el selector dado esté dentro de la vista:

    $browser->scrollIntoView('.selector')
            ->click('.selector');

[]()

## Aserciones disponibles

Dusk proporciona una variedad de aserciones que puedes hacer contra tu aplicación. Todas las aserciones disponibles están documentadas en la siguiente lista:

<style>
    .collection-method-list &gt; p {
        columnas: 10.8em 3; -moz-columns: 10.8em 3; -webkit-columns: 10.8em 3;
    }

    .collection-method-list a {
        display: block;
        overflow: oculto;
        text-overflow: ellipsis;
        espacio en blanco: nowrap;
    }
</style>

<div class="collection-method-list" markdown="1"/>

[assertTitleassertTitleContainsassertUrlIsassertSchemeIsassertSchemeIsNotassertHostIsassertHostIsNotassertPortIsassertPortIsNotassertPathBeginsWithassertPathIsassertPathIsNotassertRouteIsassertQueryStringHasassertQueryStringMissingassertFragmentIsassertFragmentBeginsWithassertFragmentIsNotassertHasCookieassertHasPlainCookieassertCookieMissingassertPlainCookieMissingassertCookieValueassertPlainCookieValueassertSeeassertDontSeeassertSeeInassertDontSeeInassertSeeAnythingInassertSeeNothingInassertScriptassertSourceHasassertSourceMissingassertSeeLinkassertDontSeeLinkassertInputValueassertInputValueIsNotassertCheckedassertNotCheckedassertIndeterminateassertRadioSelectedassertRadioNotSelectedassertSelectedassertNotSelectedassertSelectHasOptionsassertSelectMissingOptionsassertSelectHasOptionassertSelectMissingOptionassertValueassertValueIsNotassertAttributeassertAttributeContainsassertAriaAttributeassertDataAttributeassertVisibleassertPresentassertNotPresentassertMissingassertInputPresentassertInputMissingassertDialo](#assert-dialog-opened)[g](#assert-vue-does-not-contain)

[object Object]

[]()

#### assertTitle

Comprueba que el título de la página coincide con el texto indicado:

    $browser->assertTitle($title);

[]()

#### assertTitleContains

Compruebe que el título de la página contiene el texto indicado:

    $browser->assertTitleContains($title);

[]()

#### assertUrlIs

Compruebe que la URL actual (sin la cadena de consulta) coincide con la cadena indicada:

    $browser->assertUrlIs($url);

[]()

#### assertSchemeIs

Comprueba que el esquema de URL actual coincide con el esquema dado:

    $browser->assertSchemeIs($scheme);

[]()

#### assertSchemeIsNot

Comprueba que el esquema de URL actual no coincide con el esquema dado:

    $browser->assertSchemeIsNot($scheme);

[]()

#### assertHostIs

Comprueba si el host de la URL actual coincide con el host indicado:

    $browser->assertHostIs($host);

[]()

#### assertHostIsNot

Comprueba que el host de la URL actual no coincide con el host indicado:

    $browser->assertHostIsNot($host);

[]()

#### assertPortIs

Comprueba que el puerto de la URL actual coincide con el puerto indicado:

    $browser->assertPortIs($port);

[]()

#### assertPortIsNot

Comprueba que el puerto de la URL actual no coincide con el puerto indicado:

    $browser->assertPortIsNot($port);

[]()

#### assertPathBeginsWith

Comprueba que la ruta de la URL actual empieza por la ruta indicada:

    $browser->assertPathBeginsWith('/home');

[]()

#### assertPathIs

Comprueba que la ruta actual coincide con la ruta indicada:

    $browser->assertPathIs('/home');

[]()

#### assertPathIsNot

Compruebe que la ruta actual no coincide con la ruta indicada:

    $browser->assertPathIsNot('/home');

[]()

#### assertRouteIs

Comprueba que la URL actual coincide con la URL [de la ruta](/docs/%7B%7Bversion%7D%7D/routing#named-routes) indicada:

    $browser->assertRouteIs($name, $parameters);

[]()

#### assertQueryStringHas

Comprueba que el parámetro de cadena de consulta está presente:

    $browser->assertQueryStringHas($name);

Comprueba que el parámetro de cadena de consulta está presente y tiene un valor determinado:

    $browser->assertQueryStringHas($name, $value);

[]()

#### assertQueryStringMissing

Comprueba que el parámetro de cadena de consulta no está presente:

    $browser->assertQueryStringMissing($name);

[]()

#### assertFragmentIs

Comprueba que el fragmento hash actual de la URL coincide con el fragmento dado:

    $browser->assertFragmentIs('anchor');

[]()

#### assertFragmentBeginsWith

Comprueba que el fragmento hash actual de la URL comienza por el fragmento indicado:

    $browser->assertFragmentBeginsWith('anchor');

[]()

#### assertFragmentIsNot

Compruebe que el fragmento hash actual de la URL no coincide con el fragmento indicado:

    $browser->assertFragmentIsNot('anchor');

[]()

#### assertHasCookie

Compruebe que la cookie cifrada está presente:

    $browser->assertHasCookie($name);

[]()

#### assertHasPlainCookie

Compruebe que la cookie no cifrada está presente:

    $browser->assertHasPlainCookie($name);

[]()

#### assertCookieMissing

Afirmar que la cookie cifrada dada no está presente:

    $browser->assertCookieMissing($name);

[]()

#### assertPlainCookieMissing

Afirmar que la cookie no cifrada dada no está presente:

    $browser->assertPlainCookieMissing($name);

[]()

#### assertCookieValue

Afirmar que una cookie cifrada tiene un valor determinado:

    $browser->assertCookieValue($name, $value);

[]()

#### assertPlainCookieValue

Afirmar que una cookie no cifrada tiene un valor determinado:

    $browser->assertPlainCookieValue($name, $value);

[]()

#### assertVer

Compruebe que el texto indicado está presente en la página:

    $browser->assertSee($text);

[]()

#### assertDontSee

Afirmar que el texto dado no está presente en la página:

    $browser->assertDontSee($text);

[]()

#### assertSeeIn

Compruebe que el texto indicado está presente en el selector:

    $browser->assertSeeIn($selector, $text);

[]()

#### assertDontSeeIn

Comprobar que el texto dado no está presente en el selector:

    $browser->assertDontSeeIn($selector, $text);

[]()

#### assertSeeAnythingIn

Comprueba que no hay ningún texto en el selector:

    $browser->assertSeeAnythingIn($selector);

[]()

#### assertSeeNothingIn

Comprobar que no hay ningún texto en el selector:

    $browser->assertSeeNothingIn($selector);

[]()

#### assertScript

Comprobar que la expresión JavaScript dada se evalúa con el valor dado:

    $browser->assertScript('window.isLoaded')
            ->assertScript('document.readyState', 'complete');

[]()

#### assertSourceTiene

Comprobar que el código fuente está presente en la página:

    $browser->assertSourceHas($code);

[]()

#### assertSourceFalta

Compruebe que el código fuente indicado no está presente en la página:

    $browser->assertSourceMissing($code);

[]()

#### assertSeeLink

Comprobar que el enlace indicado está presente en la página:

    $browser->assertSeeLink($linkText);

[]()

#### assertDontSeeLink

Comprobar que el enlace dado no está presente en la página:

    $browser->assertDontSeeLink($linkText);

[]()

#### assertInputValue

Comprueba que el campo de entrada tiene el valor indicado:

    $browser->assertInputValue($field, $value);

[]()

#### assertInputValueIsNot

Compruebe que el campo de entrada no tiene el valor indicado:

    $browser->assertInputValueIsNot($field, $value);

[]()

#### assertChecked

Compruebe que la casilla de verificación está marcada:

    $browser->assertChecked($field);

[]()

#### assertNotChecked

Asegúrese de que la casilla de verificación no está marcada:

    $browser->assertNotChecked($field);

[]()

#### assertIndeterminate

Comprobar que la casilla de verificación está en un estado indeterminado:

    $browser->assertIndeterminate($field);

[]()

#### assertRadioSelected

Comprobar que el campo de opción está seleccionado:

    $browser->assertRadioSelected($field, $value);

[]()

#### assertRadioNotSelected

Comprueba que el campo de opción no está seleccionado:

    $browser->assertRadioNotSelected($field, $value);

[]()

#### assertSelected

Comprueba que el campo desplegable tiene el valor seleccionado:

    $browser->assertSelected($field, $value);

[]()

#### assertNotSelected

Asegúrese de que el desplegable dado no tiene seleccionado el valor dado:

    $browser->assertNotSelected($field, $value);

[]()

#### assertSelectHasOptions

Comprueba que la array de valores dada está disponible para ser seleccionada:

    $browser->assertSelectHasOptions($field, $values);

[]()

#### assertSelectMissingOptions

Asegúrese de que la array de valores dada no está disponible para ser seleccionada:

    $browser->assertSelectMissingOptions($field, $values);

[]()

#### assertSelectHasOption

Asegúrese de que el valor dado está disponible para ser seleccionado en el campo dado:

    $browser->assertSelectHasOption($field, $value);

[]()

#### assertSelectMissingOption

Asegúrese de que el valor dado no está disponible para ser seleccionado:

    $browser->assertSelectMissingOption($field, $value);

[]()

#### assertValue

Compruebe que el elemento que coincide con el selector indicado tiene el valor indicado:

    $browser->assertValue($selector, $value);

[]()

#### assertValueIsNot

Asegúrese de que el elemento que coincide con el selector indicado no tiene el valor indicado:

    $browser->assertValueIsNot($selector, $value);

[]()

#### assertAttribute

Asegúrese de que el elemento que coincide con el selector indicado tiene el valor indicado en el atributo proporcionado:

    $browser->assertAttribute($selector, $attribute, $value);

[]()

#### assertAttributeContains

Asegúrese de que el elemento que coincide con el selector indicado contiene el valor indicado en el atributo indicado:

    $browser->assertAttributeContains($selector, $attribute, $value);

[]()

#### assertAriaAttribute

Asegúrese de que el elemento que coincide con el selector indicado tiene el valor indicado en el atributo aria indicado:

    $browser->assertAriaAttribute($selector, $attribute, $value);

Por ejemplo, dado el marcado `<button aria-label="Add"></button>`, puede comprobar el atributo `aria-label` así:

    $browser->assertAriaAttribute('button', 'label', 'Add')

[]()

#### assertDataAttribute

Asegúrese de que el elemento que coincide con el selector indicado tiene el valor indicado en el atributo data indicado:

    $browser->assertDataAttribute($selector, $attribute, $value);

Por ejemplo, dado el marcado `<tr id="row-1" data-content="attendees"></tr>`, puedes afirmar contra el atributo `data-label` así:

    $browser->assertDataAttribute('#row-1', 'content', 'attendees')

[]()

#### assertVisible

Asegúrese de que el elemento que coincide con el selector indicado es visible:

    $browser->assertVisible($selector);

[]()

#### assertPresent

Comprueba que el elemento que coincide con el selector indicado está presente en la fuente:

    $browser->assertPresent($selector);

[]()

#### assertNotPresent

Asegúrese de que el elemento que coincide con el selector dado no está presente en la fuente:

    $browser->assertNotPresent($selector);

[]()

#### assertMissing

Afirmar que el elemento que coincide con el selector dado no es visible:

    $browser->assertMissing($selector);

[]()

#### assertInputPresent

Comprueba que existe una entrada con el nombre indicado:

    $browser->assertInputPresent($name);

[]()

#### assertInputMissing

Asegúrese de que la entrada con el nombre indicado no está presente en la fuente:

    $browser->assertInputMissing($name);

[]()

#### assertDialogOpened

Compruebe que se ha abierto un cuadro de diálogo JavaScript con el mensaje indicado:

    $browser->assertDialogOpened($message);

[]()

#### assertEnabled

Compruebe que el campo indicado está activado:

    $browser->assertEnabled($field);

[]()

#### assertDisabled

Compruebe que el campo en cuestión está desactivado:

    $browser->assertDisabled($field);

[]()

#### assertButtonEnabled

Compruebe que el botón indicado está activado:

    $browser->assertButtonEnabled($button);

[]()

#### assertButtonDisabled

Comprueba que el botón está desactivado:

    $browser->assertButtonDisabled($button);

[]()

#### assertFocused

Comprueba que el campo está seleccionado:

    $browser->assertFocused($field);

[]()

#### assertNotFocused

Asegura que el campo dado no está enfocado:

    $browser->assertNotFocused($field);

[]()

#### assertAuthenticated

Comprobar que el usuario está autenticado:

    $browser->assertAuthenticated();

[]()

#### assertGuest

Asegura que el usuario no está autenticado:

    $browser->assertGuest();

[]()

#### assertAuthenticatedAs

Comprobar que el usuario está autenticado como el usuario dado:

    $browser->assertAuthenticatedAs($user);

[]()

#### assertVue

Dusk incluso te permite hacer aserciones sobre el estado de los datos de [los componentes de Vue](https://vuejs.org). Por ejemplo, imagina que tu aplicación contiene el siguiente componente Vue:

    // HTML...

    <profile dusk="profile-component"></profile>

    // Component Definition...

    Vue.component('profile', {
        template: '<div>{{ user.name }}</div>',

        data: function () {
            return {
                user: {
                    name: 'Taylor'
                }
            };
        }
    });

Puedes afirmar sobre el estado del componente Vue así:

    /**
     * A basic Vue test example.
     *
     * @return void
     */
    public function testVue()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertVue('user.name', 'Taylor', '@profile-component');
        });
    }

[]()

#### assertVueIsNot

Afirmar que una determinada propiedad de datos del componente Vue no coincide con el valor dado:

    $browser->assertVueIsNot($property, $value, $componentSelector = null);

[]()

#### assertVueContiene

Asegurar que una propiedad de datos de un componente Vue es un array y contiene el valor dado:

    $browser->assertVueContains($property, $value, $componentSelector = null);

[]()

#### assertVueNoContiene

Comprobar que la propiedad data de un componente Vue es un array y no contiene el valor dado:

    $browser->assertVueDoesNotContain($property, $value, $componentSelector = null);

[]()

## Páginas

A veces, tests requieren que se realicen varias acciones complicadas en secuencia. Esto puede hacer que tus tests sean más difíciles de leer y entender. Dusk Pages te permite definir acciones expresivas que pueden ser ejecutadas en una página dada a través de un único método. Las páginas también le permiten definir atajos a selectores comunes para su aplicación o para una sola página.

[]()

### Generación de páginas

Para generar un objeto página, ejecuta el comando `dusk:page` Artisan. Todos los objetos página serán colocados en el directorio `tests` de tu aplicación:

    php artisan dusk:page Login

[]()

### Configuración de páginas

Por defecto, las páginas tienen tres métodos: `url`, `assert` y `elements`. Ahora discutiremos los métodos `url` y `assert`. El método `elements` se tratará [con más detalle a continuación](#shorthand-selectors).

[]()

#### El método `url`

El método `url` debe devolver la ruta de la URL que representa la página. Dusk utilizará esta URL cuando navegue a la página en el navegador:

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/login';
    }

[]()

#### El método `assert`

El método `assert` puede hacer cualquier aserción necesaria para verificar que el navegador está realmente en la página dada. En realidad no es necesario colocar nada dentro de este método; sin embargo, eres libre de hacer estas aserciones si lo deseas. Estas aserciones se ejecutarán automáticamente cuando se navegue a la página:

    /**
     * Assert that the browser is on the page.
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
    }

[]()

### Navegación por las páginas

Una vez definida la página, puede navegar hasta ella utilizando el método `visitar`:

    use Tests\Browser\Pages\Login;

    $browser->visit(new Login);

A veces puede que ya estés en una página determinada y necesites "cargar" los selectores y métodos de la página en el contexto de test actual. Esto es habitual cuando se pulsa un botón y se es redirigido a una página determinada sin navegar explícitamente hasta ella. En esta situación, puede utilizar el método `on` para cargar la página:

    use Tests\Browser\Pages\CreatePlaylist;

    $browser->visit('/dashboard')
            ->clickLink('Create Playlist')
            ->on(new CreatePlaylist)
            ->assertSee('@create');

[]()

### Selectores abreviados

El método `elements` dentro de las clases de página te permite definir atajos rápidos y fáciles de recordar para cualquier selector CSS de tu página. Por ejemplo, definamos un atajo para el campo de entrada "email" de la página de inicio de sesión de la aplicación:

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@email' => 'input[name=email]',
        ];
    }

Una vez definido el atajo, puede utilizar el selector abreviado en cualquier lugar en el que normalmente utilizaría un selector CSS completo:

    $browser->type('@email', 'taylor@laravel.com');

[]()

#### Selectores abreviados globales

Después de instalar Dusk, se colocará una clase `Page` base en tu directorio `tests`. Esta clase contiene un método `siteElements` que puede usarse para definir selectores abreviados globales que deberían estar disponibles en todas las páginas de tu aplicación:

    /**
     * Get the global element shortcuts for the site.
     *
     * @return array
     */
    public static function siteElements()
    {
        return [
            '@element' => '#selector',
        ];
    }

[]()

### Métodos de página

Además de los métodos por defecto definidos en las páginas, puede definir métodos adicionales que pueden ser utilizados en todas sus tests. Por ejemplo, imaginemos que estamos construyendo una aplicación de gestión de música. Una acción común para una página de la aplicación podría ser crear una lista de reproducción. En lugar de reescribir la lógica para crear una lista de reproducción en cada test, puede definir un método `createPlaylist` en una clase de página:

    <?php

    namespace Tests\Browser\Pages;

    use Laravel\Dusk\Browser;

    class Dashboard extends Page
    {
        // Other page methods...

        /**
         * Create a new playlist.
         *
         * @param  \Laravel\Dusk\Browser  $browser
         * @param  string  $name
         * @return void
         */
        public function createPlaylist(Browser $browser, $name)
        {
            $browser->type('name', $name)
                    ->check('share')
                    ->press('Create Playlist');
        }
    }

Una vez definido el método, puede utilizarlo en cualquier test que utilice la página. La instancia del navegador se pasará automáticamente como primer argumento a los métodos de página personalizados:

    use Tests\Browser\Pages\Dashboard;

    $browser->visit(new Dashboard)
            ->createPlaylist('My Playlist')
            ->assertSee('My Playlist');

[]()

## Componentes

Los componentes son similares a los "objetos de página" de Dusk, pero están pensados para piezas de interfaz de usuario y funcionalidad que se reutilizan en toda la aplicación, como una barra de navegación o una ventana de notificación. Como tales, los componentes no están vinculados a URLs específicas.

[]()

### Generación de componentes

Para generar un componente, ejecuta el comando `dusk:component` Artisan. Los nuevos componentes se colocan en el directorio `tests`:

    php artisan dusk:component DatePicker

Como se muestra arriba, un "selector de fecha" es un ejemplo de un componente que podría existir a lo largo de su aplicación en una variedad de páginas. Puede resultar engorroso escribir manualmente la lógica de automatización del navegador para seleccionar una fecha en docenas de tests a lo largo de su conjunto de test. En su lugar, podemos definir un componente Dusk para representar el selector de fecha, permitiéndonos encapsular esa lógica dentro del componente:

    <?php

    namespace Tests\Browser\Components;

    use Laravel\Dusk\Browser;
    use Laravel\Dusk\Component as BaseComponent;

    class DatePicker extends BaseComponent
    {
        /**
         * Get the root selector for the component.
         *
         * @return string
         */
        public function selector()
        {
            return '.date-picker';
        }

        /**
         * Assert that the browser page contains the component.
         *
         * @param  Browser  $browser
         * @return void
         */
        public function assert(Browser $browser)
        {
            $browser->assertVisible($this->selector());
        }

        /**
         * Get the element shortcuts for the component.
         *
         * @return array
         */
        public function elements()
        {
            return [
                '@date-field' => 'input.datepicker-input',
                '@year-list' => 'div > div.datepicker-years',
                '@month-list' => 'div > div.datepicker-months',
                '@day-list' => 'div > div.datepicker-days',
            ];
        }

        /**
         * Select the given date.
         *
         * @param  \Laravel\Dusk\Browser  $browser
         * @param  int  $year
         * @param  int  $month
         * @param  int  $day
         * @return void
         */
        public function selectDate(Browser $browser, $year, $month, $day)
        {
            $browser->click('@date-field')
                    ->within('@year-list', function ($browser) use ($year) {
                        $browser->click($year);
                    })
                    ->within('@month-list', function ($browser) use ($month) {
                        $browser->click($month);
                    })
                    ->within('@day-list', function ($browser) use ($day) {
                        $browser->click($day);
                    });
        }
    }

[]()

### Uso de componentes

Una vez definido el componente, podemos seleccionar fácilmente una fecha dentro del selector de fechas desde cualquier test. Y, si la lógica necesaria para seleccionar una fecha cambia, sólo tenemos que actualizar el componente:

    <?php

    namespace Tests\Browser;

    use Illuminate\Foundation\Testing\DatabaseMigrations;
    use Laravel\Dusk\Browser;
    use Tests\Browser\Components\DatePicker;
    use Tests\DuskTestCase;

    class ExampleTest extends DuskTestCase
    {
        /**
         * A basic component test example.
         *
         * @return void
         */
        public function testBasicExample()
        {
            $this->browse(function (Browser $browser) {
                $browser->visit('/')
                        ->within(new DatePicker, function ($browser) {
                            $browser->selectDate(2019, 1, 30);
                        })
                        ->assertSee('January');
            });
        }
    }

[]()

## Integración Continua

> **AdvertenciaLa mayoría de las**configuraciones de integración continua de Dusk esperan que tu aplicación Laravel sea servida usando el servidor de desarrollo PHP incorporado en el puerto 8000. Por lo tanto, antes de continuar, debes asegurarte de que tu entorno de integración continua tiene un valor de variable de entorno `APP_URL` de `http://127.0.0.1:8000`.

[tests-on-heroku-ci">]()

### Heroku CI

Para ejecutar tests pruebas de Dusk en [Heroku CI](https://www.heroku.com/continuous-integration), agregue el siguiente paquete de compilación y scripts de Google Chrome a su archivo `app.json` de Heroku:

    {
      "environments": {
        "test": {
          "buildpacks": [
            { "url": "heroku/php" },
            { "url": "https://github.com/heroku/heroku-buildpack-google-chrome" }
          ],
          "scripts": {
            "test-setup": "cp .env.testing .env",
            "test": "nohup bash -c './vendor/laravel/dusk/bin/chromedriver-linux > /dev/null 2>&1 &' && nohup bash -c 'php artisan serve --no-reload > /dev/null 2>&1 &' && php artisan dusk"
          }
        }
      }
    }

[tests-on-travis-ci">]()

### Travis CI

Para ejecutar sus tests Dusk en [Travis](https://travis-ci.org) CI, utilice la siguiente configuración `.travis.yml`. Dado que Travis CI no es un entorno gráfico, necesitaremos dar algunos pasos extra para lanzar un navegador Chrome. Además, usaremos `php artisan serve` para lanzar el servidor web integrado de PHP:

```yaml
language: php

php:
  - 7.3

addons:
  chrome: stable

install:
  - cp .env.testing .env
  - travis_retry composer install --no-interaction --prefer-dist
  - php artisan key:generate
  - php artisan dusk:chrome-driver

before_script:
  - google-chrome-stable --headless --disable-gpu --remote-debugging-port=9222 http://localhost &
  - php artisan serve --no-reload &

script:
  - php artisan dusk
```

[tests-on-github-actions">]()

### Acciones GitHub

Si estás usando [GitHub Actions](https://github.com/features/actions) para ejecutar tus tests Dusk, puedes usar el siguiente archivo de configuración como punto de partida. Al igual que TravisCI, usaremos el comando php artisan `serve` para lanzar el servidor web integrado de PHP:

```yaml
name: CI
on: [push]
jobs:

  dusk-php:
    runs-on: ubuntu-latest
    env:
      APP_URL: "http://127.0.0.1:8000"
      DB_USERNAME: root
      DB_PASSWORD: root
      MAIL_MAILER: log
    steps:
      - uses: actions/checkout@v3
      - name: Prepare The Environment
        run: cp .env.example .env
      - name: Create Database
        run: |
          sudo systemctl start mysql
          mysql --user="root" --password="root" -e "CREATE DATABASE \`my-database\` character set UTF8mb4 collate utf8mb4_bin;"
      - name: Install Composer Dependencies
        run: composer install --no-progress --prefer-dist --optimize-autoloader
      - name: Generate Application Key
        run: php artisan key:generate
      - name: Upgrade Chrome Driver
        run: php artisan dusk:chrome-driver --detect
      - name: Start Chrome Driver
        run: ./vendor/laravel/dusk/bin/chromedriver-linux &
      - name: Run Laravel Server
        run: php artisan serve --no-reload &
      - name: Run Dusk Tests
        run: php artisan dusk
      - name: Upload Screenshots
        if: failure()
        uses: actions/upload-artifact@v2
        with:
          name: screenshots
          path: tests/Browser/screenshots
      - name: Upload Console Logs
        if: failure()
        uses: actions/upload-artifact@v2
        with:
          name: console
          path: tests/Browser/console
```
