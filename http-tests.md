# tests HTTP

- [Introducción](#introduction)
- [Realizar solicitudes](#making-requests)
  - [Personalización de las cabeceras de las solicitudes](#customizing-request-headers)
  - [Cookies](#cookies)
  - [Sesión / Autenticación](#session-and-authentication)
  - [Depuración de respuestas](#debugging-responses)
  - [Manejo de Excepciones](#exception-handling)
- [Pruebas de API JSON](#testing-json-apis)
  - [Pruebas fluidas de JSON](#fluent-json-testing)
- [Pruebas de carga de archivos](#testing-file-uploads)
- [Pruebas de vistas](#testing-views)
  - [Hoja de renderizado y componentes](#rendering-blade-and-components)
- [Aserciones disponibles](#available-assertions)
  - [Aserciones de respuesta](#response-assertions)
  - [Aserciones de autenticación](#authentication-assertions)

[]()

## Introducción

Laravel proporciona una API muy fluida para realizar peticiones HTTP a tu aplicación y examinar las respuestas. Por ejemplo, echa un vistazo a la test de función definida a continuación:

    <?php

    namespace Tests\Feature;

    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Foundation\Testing\WithoutMiddleware;
    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        /**
         * A basic test example.
         *
         * @return void
         */
        public function test_a_basic_request()
        {
            $response = $this->get('/');

            $response->assertStatus(200);
        }
    }

El método `get` realiza una petición `GET` a la aplicación, mientras que el método `assertStatus` afirma que la respuesta devuelta debe tener el código de estado HTTP dado. Además de esta simple aserción, Laravel también contiene una variedad de aserciones para inspeccionar las cabeceras de respuesta, el contenido, la estructura JSON, y más.

[]()

## Realización de solicitudes

Para realizar una solicitud a su aplicación, puede invocar los métodos `get`, `post`, `put`, `patch` o `delete` dentro de su test. Estos métodos no envían una petición HTTP "real" a su aplicación. En su lugar, se simula internamente toda la solicitud de red.

En lugar de devolver una instancia `Illuminate\Http\Response`, los métodos de solicitud de test devuelven una instancia de `Illuminate\Testing\TestResponse`, que proporciona una [variedad de afirmaciones útiles](#available-assertions) que le permiten inspeccionar las respuestas de su aplicación:

    <?php

    namespace Tests\Feature;

    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Foundation\Testing\WithoutMiddleware;
    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        /**
         * A basic test example.
         *
         * @return void
         */
        public function test_a_basic_request()
        {
            $response = $this->get('/');

            $response->assertStatus(200);
        }
    }

En general, cada una de sus tests sólo debe hacer una solicitud a su aplicación. Pueden producirse comportamientos inesperados si se ejecutan varias peticiones en un mismo método de test.

> **Nota**  
> Para mayor comodidad, el middleware CSRF se desactiva automáticamente al ejecutar las tests.

[]()

### Personalización de las cabeceras de solicitud

Puede utilizar el método `withHeaders` para personalizar las cabeceras de la petición antes de enviarla a la aplicación. Este método le permite añadir cualquier cabecera personalizada que desee a la petición:

    <?php

    namespace Tests\Feature;

    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        /**
         * A basic functional test example.
         *
         * @return void
         */
        public function test_interacting_with_headers()
        {
            $response = $this->withHeaders([
                'X-Header' => 'Value',
            ])->post('/user', ['name' => 'Sally']);

            $response->assertStatus(201);
        }
    }

[]()

### Cookies

Puede utilizar los métodos `withCookie` o `withCookies` para establecer los valores de las cookies antes de realizar una petición. El método `withCookie` acepta un nombre de cookie y un valor como sus dos argumentos, mientras que el método `withCookies` acepta un array de pares nombre / valor:

    <?php

    namespace Tests\Feature;

    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        public function test_interacting_with_cookies()
        {
            $response = $this->withCookie('color', 'blue')->get('/');

            $response = $this->withCookies([
                'color' => 'blue',
                'name' => 'Taylor',
            ])->get('/');
        }
    }

[]()

### Sesión / Autenticación

Laravel proporciona varios ayudantes para interactuar con la sesión durante las pruebas HTTP. En primer lugar, puedes establecer los datos de la sesión en un array dado utilizando el método `withSession`. Esto es útil para cargar la sesión con datos antes de emitir una petición a tu aplicación:

    <?php

    namespace Tests\Feature;

    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        public function test_interacting_with_the_session()
        {
            $response = $this->withSession(['banned' => false])->get('/');
        }
    }

La sesión de Laravel se utiliza normalmente para mantener el estado del usuario autenticado en ese momento. Por lo tanto, el método de ayuda `actingAs` proporciona una forma sencilla de autenticar a un usuario dado como el usuario actual. Por ejemplo, podemos utilizar una [fábrica de modelos](/docs/%7B%7Bversion%7D%7D/eloquent-factories) para generar y autenticar un usuario:

    <?php

    namespace Tests\Feature;

    use App\Models\User;
    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        public function test_an_action_that_requires_authentication()
        {
            $user = User::factory()->create();

            $response = $this->actingAs($user)
                             ->withSession(['banned' => false])
                             ->get('/');
        }
    }

También puede especificar qué guarda debe usarse para autenticar al usuario pasando el nombre del guarda como segundo argumento al método `actingAs`. El guarda que se proporcione al método `actingAs` también se convertirá en el guarda por defecto mientras dure la test:

    $this->actingAs($user, 'web')

[]()

### Depuración de respuestas

Después de realizar una petición de test a su aplicación, puede utilizar los métodos `dump`, `dumpHeaders` y `dumpSession` para examinar y depurar el contenido de la respuesta:

    <?php

    namespace Tests\Feature;

    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        /**
         * A basic test example.
         *
         * @return void
         */
        public function test_basic_test()
        {
            $response = $this->get('/');

            $response->dumpHeaders();

            $response->dumpSession();

            $response->dump();
        }
    }

Alternativamente, puede utilizar los métodos `dd`, `ddHeaders`, y `ddSession` para volcar información sobre la respuesta y luego detener la ejecución:

    <?php

    namespace Tests\Feature;

    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        /**
         * A basic test example.
         *
         * @return void
         */
        public function test_basic_test()
        {
            $response = $this->get('/');

            $response->ddHeaders();

            $response->ddSession();

            $response->dd();
        }
    }

[]()

### Manejo de excepciones

A veces puedes querer test que tu aplicación está lanzando una excepción específica. Para asegurarte de que la excepción no es capturada por el manejador de excepciones de Laravel y devuelta como respuesta HTTP, puedes invocar el método `withoutExceptionHandling` antes de hacer tu petición:

    $response = $this->withoutExceptionHandling()->get('/');

Además, si desea asegurarse de que su aplicación no está utilizando características que han sido obsoletas por el lenguaje PHP o las bibliotecas de su aplicación está utilizando, puede invocar el método `withoutDeprecationHandling` antes de hacer su solicitud. Cuando el manejo de la depreciación está deshabilitado, las advertencias de depreciación se convertirán en excepciones, causando así que su test falle:

    $response = $this->withoutDeprecationHandling()->get('/');

[]()

## Pruebas de API JSON

Laravel también proporciona varios ayudantes para probar APIs JSON y sus respuestas. Por ejemplo, los métodos `json`, `getJson`, `postJson`, `putJson`, `patchJson`, `deleteJson` y `optionsJson` pueden utilizarse para emitir peticiones JSON con varios verbos HTTP. También puedes pasar fácilmente datos y cabeceras a estos métodos. Para empezar, vamos a escribir una test para realizar una petición `POST` a `/api/user` y comprobar que se devuelven los datos JSON esperados:

    <?php

    namespace Tests\Feature;

    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        /**
         * A basic functional test example.
         *
         * @return void
         */
        public function test_making_an_api_request()
        {
            $response = $this->postJson('/api/user', ['name' => 'Sally']);

            $response
                ->assertStatus(201)
                ->assertJson([
                    'created' => true,
                ]);
        }
    }

Además, se puede acceder a los datos de respuesta JSON como variables de array en la respuesta, por lo que es conveniente para usted para inspeccionar los valores individuales devueltos dentro de una respuesta JSON:

    $this->assertTrue($response['created']);

> **Nota**  
> El método `assertJson` convierte la respuesta en un array y utiliza `PHPUnit::assertArraySubset` para verificar que el array dado existe dentro de la respuesta JSON devuelta por la aplicación. Por lo tanto, si hay otras propiedades en la respuesta JSON, esta test pasará siempre y cuando el fragmento dado esté presente.

[]()

#### Comprobación de coincidencias exactas de JSON

Como se ha mencionado anteriormente, el método `assertJson` puede utilizarse para comprobar que un fragmento de JSON existe en la respuesta JSON. Si quieres verificar que un array dado **coincide exactamente** con el JSON devuelto por tu aplicación, debes utilizar el método `assertExactJson`:

    <?php

    namespace Tests\Feature;

    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        /**
         * A basic functional test example.
         *
         * @return void
         */
        public function test_asserting_an_exact_json_match()
        {
            $response = $this->postJson('/user', ['name' => 'Sally']);

            $response
                ->assertStatus(201)
                ->assertExactJson([
                    'created' => true,
                ]);
        }
    }

[]()

#### Comprobación de rutas JSON

Si desea comprobar que la respuesta JSON contiene los datos especificados en una ruta determinada, utilice el método `assertJsonPath`:

    <?php

    namespace Tests\Feature;

    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        /**
         * A basic functional test example.
         *
         * @return void
         */
        public function test_asserting_a_json_paths_value()
        {
            $response = $this->postJson('/user', ['name' => 'Sally']);

            $response
                ->assertStatus(201)
                ->assertJsonPath('team.owner.name', 'Darian');
        }
    }

El método `assertJsonPath` también acepta un closure, que puede utilizarse para determinar dinámicamente si la aserción debe pasar:

    $response->assertJsonPath('team.owner.name', fn ($name) => strlen($name) >= 3);

[]()

### Pruebas fluidas de JSON

Laravel también ofrece una bonita forma de test con fluidez las respuestas JSON de tu aplicación. Para empezar, pasa un closure al método `assertJson`. Este closure se invocará con una instancia de `Illuminate\Testing\Fluent\AssertableJson` que se puede utilizar para hacer afirmaciones contra el JSON que fue devuelto por su aplicación. El método `where` puede utilizarse para hacer afirmaciones sobre un atributo concreto del JSON, mientras que el método `missing` puede utilizarse para afirmar que falta un atributo concreto en el JSON:

    use Illuminate\Testing\Fluent\AssertableJson;

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function test_fluent_json()
    {
        $response = $this->getJson('/users/1');

        $response
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('id', 1)
                     ->where('name', 'Victoria Faith')
                     ->where('email', fn ($email) => str($email)->is('victoria@gmail.com'))
                     ->whereNot('status', 'pending')
                     ->missing('password')
                     ->etc()
            );
    }

#### Entendiendo el método `etc`

En el ejemplo anterior, te habrás dado cuenta de que hemos invocado el método `etc` al final de nuestra cadena de aserciones. Este método informa a Laravel que puede haber otros atributos presentes en el objeto JSON. Si no se utiliza el método `etc`, la test fallará si existen en el objeto JSON otros atributos contra los que no se hicieron aserciones.

La intención detrás de este comportamiento es protegerte de exponer involuntariamente información sensible en tus respuestas JSON forzándote a hacer explícitamente una aserción contra el atributo o permitir explícitamente atributos adicionales a través del método `etc`.

Sin embargo, debe tener en cuenta que no incluir el método `etc` en su cadena de aserción no garantiza que no se añadan atributos adicionales a las matrices anidadas dentro de su objeto JSON. El método `etc` sólo garantiza que no existen atributos adicionales en el nivel de anidamiento en el que se invoca el método `etc`.

[]()

#### Afirmación de la presencia o ausencia de atributos

Para afirmar que un atributo está presente o ausente, puede utilizar los métodos `has` y `missing`:

    $response->assertJson(fn (AssertableJson $json) =>
        $json->has('data')
             ->missing('message')
    );

Además, los métodos `hasAll` y `missingAll` permiten afirmar la presencia o ausencia de varios atributos simultáneamente:

    $response->assertJson(fn (AssertableJson $json) =>
        $json->hasAll(['status', 'data'])
             ->missingAll(['message', 'code'])
    );

Puedes utilizar el método `hasAny` para determinar si al menos uno de una lista dada de atributos está presente:

    $response->assertJson(fn (AssertableJson $json) =>
        $json->has('status')
             ->hasAny('data', 'message', 'code')
    );

[]()

#### Comprobación de colecciones JSON

A menudo, la ruta devolverá una respuesta JSON que contiene varios elementos, como varios usuarios:

    Route::get('/users', function () {
        return User::all();
    });

En estas situaciones, podemos utilizar el método `has` del objeto JSON fluido para realizar afirmaciones sobre los usuarios incluidos en la respuesta. Por ejemplo, afirmemos que la respuesta JSON contiene tres usuarios. A continuación, haremos algunas afirmaciones sobre el primer usuario de la colección utilizando el método `first`. El `primer` método acepta un closure que recibe otra cadena JSON asertable que podemos utilizar para hacer aserciones sobre el primer objeto de la colección JSON:

    $response
        ->assertJson(fn (AssertableJson $json) =>
            $json->has(3)
                 ->first(fn ($json) =>
                    $json->where('id', 1)
                         ->where('name', 'Victoria Faith')
                         ->where('email', fn ($email) => str($email)->is('victoria@gmail.com'))
                         ->missing('password')
                         ->etc()
                 )
        );

[]()

#### Aserciones sobre la colección JSON

A veces, las rutas de tu aplicación devolverán colecciones JSON a las que se asignan claves con nombre:

    Route::get('/users', function () {
        return [
            'meta' => [...],
            'users' => User::all(),
        ];
    })

Al probar estas rutas, puedes utilizar el método `has` para confirmar el número de elementos de la colección. Además, puede utilizar el método `has` para abarcar una cadena de aserciones:

    $response
        ->assertJson(fn (AssertableJson $json) =>
            $json->has('meta')
                 ->has('users', 3)
                 ->has('users.0', fn ($json) =>
                    $json->where('id', 1)
                         ->where('name', 'Victoria Faith')
                         ->where('email', fn ($email) => str($email)->is('victoria@gmail.com'))
                         ->missing('password')
                         ->etc()
                 )
        );

Sin embargo, en lugar de realizar dos llamadas separadas al método `has` para comprobar la colección de `usuarios`, puede realizar una única llamada que proporcione un closure como tercer parámetro. Al hacerlo, el closure se invocará automáticamente y se aplicará al primer elemento de la colección:

    $response
        ->assertJson(fn (AssertableJson $json) =>
            $json->has('meta')
                 ->has('users', 3, fn ($json) =>
                    $json->where('id', 1)
                         ->where('name', 'Victoria Faith')
                         ->where('email', fn ($email) => str($email)->is('victoria@gmail.com'))
                         ->missing('password')
                         ->etc()
                 )
        );

[]()

#### Comprobación de tipos JSON

Es posible que sólo desee afirmar que las propiedades en la respuesta JSON son de un determinado tipo. La clase `Illuminate\Testing\Fluent\AssertableJson` proporciona los métodos `whereType` y `whereAllType` para hacer precisamente eso:

    $response->assertJson(fn (AssertableJson $json) =>
        $json->whereType('id', 'integer')
             ->whereAllType([
                'users.0.name' => 'string',
                'meta' => 'array'
            ])
    );

Puede especificar múltiples tipos utilizando el carácter `|`, o pasando un array de tipos como segundo parámetro al método `whereType`. La aserción tendrá éxito si el valor de la respuesta es cualquiera de los tipos listados:

    $response->assertJson(fn (AssertableJson $json) =>
        $json->whereType('name', 'string|null')
             ->whereType('id', ['string', 'integer'])
    );

Los métodos `whereType` y `whereAllType` reconocen los siguientes tipos: `string`, `integer`, `double`, `boolean`, `array` y `null`.

[]()

## Pruebas de carga de archivos

La clase `Illuminate\Http\UploadedFile` proporciona un método `falso` que se puede utilizar para generar archivos ficticios o imágenes para las pruebas. Esto, combinado con el método `falso` de la facade `Storage`, simplifica enormemente las pruebas de carga de archivos. Por ejemplo, puedes combinar estas dos características para test fácilmente un formulario de subida de avatares:

    <?php

    namespace Tests\Feature;

    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Foundation\Testing\WithoutMiddleware;
    use Illuminate\Http\UploadedFile;
    use Illuminate\Support\Facades\Storage;
    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        public function test_avatars_can_be_uploaded()
        {
            Storage::fake('avatars');

            $file = UploadedFile::fake()->image('avatar.jpg');

            $response = $this->post('/avatar', [
                'avatar' => $file,
            ]);

            Storage::disk('avatars')->assertExists($file->hashName());
        }
    }

Si desea afirmar que un archivo determinado no existe, puede utilizar el método `assertMissing` proporcionado por la facade `Storage`:

    Storage::fake('avatars');

    // ...

    Storage::disk('avatars')->assertMissing('missing.jpg');

[]()

#### Personalización de archivos falsos

Al crear ficheros utilizando el método `fake` proporcionado por la clase `UploadedFile`, puede especificar la anchura, altura y tamaño de la imagen (en kilobytes) para test mejor las reglas de validación de su aplicación:

    UploadedFile::fake()->image('avatar.jpg', $width, $height)->size(100);

Además de crear imágenes, puede crear archivos de cualquier otro tipo utilizando el método `create`:

    UploadedFile::fake()->create('document.pdf', $sizeInKilobytes);

Si es necesario, puede pasar un argumento `$mimeType` al método para definir explícitamente el tipo MIME que debe devolver el archivo:

    UploadedFile::fake()->create(
        'document.pdf', $sizeInKilobytes, 'application/pdf'
    );

[]()

## Pruebas de vistas

Laravel también permite renderizar una vista sin realizar una petición HTTP simulada a la aplicación. Para ello, puedes llamar al método `view` dentro de tu test. El método `view` acepta el nombre de la vista y un array opcional de datos. El método devuelve una instancia de `Illuminate\Testing\TestView`, que ofrece varios métodos para hacer afirmaciones convenientemente sobre el contenido de la vista:

    <?php

    namespace Tests\Feature;

    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        public function test_a_welcome_view_can_be_rendered()
        {
            $view = $this->view('welcome', ['name' => 'Taylor']);

            $view->assertSee('Taylor');
        }
    }

La clase `TestView` proporciona los siguientes métodos de aserción: `assertSee`, `assertSeeInOrder`, `assertSeeText`, `assertSeeTextInOrder`, `assertDontSee`, y `assertDontSeeText`.

Si es necesario, puedes obtener el contenido de la vista sin procesar convirtiendo la instancia `TestView` en una cadena:

    $contents = (string) $this->view('welcome');

[]()

#### Compartiendo Errores

Algunas vistas pueden depender de errores compartidos en la bolsa de errores [global proporcionada por Laravel](/docs/%7B%7Bversion%7D%7D/validation#quick-displaying-the-validation-errors). Para hidratar la bolsa de errores con mensajes de error, puede utilizar el método `withViewErrors`:

    $view = $this->withViewErrors([
        'name' => ['Please provide a valid name.']
    ])->view('form');

    $view->assertSee('Please provide a valid name.');

[]()

### Hoja de renderizado y componentes

Si es necesario, puede utilizar el método `blade` para evaluar y renderizar una cadena [Blade](/docs/%7B%7Bversion%7D%7D/blade) sin procesar. Al igual que el método `view`, el método `blade` devuelve una instancia de `Illuminate\Testing\TestView`:

    $view = $this->blade(
        '<x-component :name="$name" />',
        ['name' => 'Taylor']
    );

    $view->assertSee('Taylor');

Puede utilizar el método `component` para evaluar y representar un [componente](/docs/%7B%7Bversion%7D%7D/blade#components) Blade. El método `component` devuelve una instancia de `Illuminate\Testing\TestComponent`:

    $view = $this->component(Profile::class, ['name' => 'Taylor']);

    $view->assertSee('Taylor');

[]()

## Aserciones disponibles

[]()

### Aserciones de respuesta

La clase `Illuminate\Testing\TestResponse` de Laravel proporciona una variedad de métodos de aserción personalizados que puede utilizar al probar su aplicación. Estas aserciones se puede acceder en la respuesta que es devuelto por el `json`, `obtener`, `publicar`, `poner`, y `eliminar` los métodos de test:

<style>
    .collection-method-list &gt; p {
        columnas: 14.4em 2; -moz-columns: 14.4em 2; -webkit-columns: 14.4em 2;
    }

    .collection-method-list a {
        display: block;
        overflow: oculto;
        text-overflow: ellipsis;
        espacio en blanco: nowrap;
    }
</style>

<div class="collection-method-list" markdown="1"/>

[assertCookieassertCookieExpiredassertCookieNotExpiredassertCookieMissingassertCreatedassertDontSeeassertDontSeeTextassertDownloadassertExactJsonassertForbiddenassertHeaderassertHeaderMissingassertJsonassertJsonCountassertJsonFragmentassertJsonMissingassertJsonMissingExactassertJsonMissingValidationErrorsassertJsonPathassertJsonMissingPathassertJsonStructureassertJsonValidationErrorsassertJsonValidationErrorForassertLocationassertContentassertNoContentassertStreamedContentassertNotFoundassertOkassertPlainCookieassertRedirectassertRedirectContainsassertRedirectToRouteassertRedirectToSignedRouteassertSeeassertSeeInOrderassertSeeTextassertSeeTextInOrderassertSessionHasassertSessionHasInputassertSessionHasAllassertSessionHasErrorsassertSessionHasErrorsInassertSessionHasNoErrorsassertSessionDoesntHaveErrorsassertSessionMissingassertStatusassertSuccessfulassertUnauthorizedassertUnprocessableassertValidassertInvalidassertViewHasassertViewHasAllassertViewIsassertViewMissing](#assert-view-missing)

[object Object]

[]()

#### assertCookie

Comprueba que la respuesta contiene la cookie indicada:

    $response->assertCookie($cookieName, $value = null);

[]()

#### assertCookieExpired

Compruebe que la respuesta contiene la cookie indicada y que ha caducado:

    $response->assertCookieExpired($cookieName);

[]()

#### assertCookieNotExpired

Compruebe que la respuesta contiene la cookie indicada y que no ha caducado:

    $response->assertCookieNotExpired($cookieName);

[]()

#### assertCookieMissing

Compruebe que la respuesta no contiene la cookie indicada:

    $response->assertCookieMissing($cookieName);

[]()

#### assertCreated

Compruebe que la respuesta tiene un código de estado HTTP 201:

    $response->assertCreated();

[]()

#### assertDontSee

Asegurar que la cadena dada no está contenida en la respuesta devuelta por la aplicación. Esta aserción escapará automáticamente de la cadena dada a menos que pase un segundo argumento de `false`:

    $response->assertDontSee($value, $escaped = true);

[]()

#### assertDontSeeText

Asegurar que la cadena dada no está contenida en el texto de la respuesta. Esta aserción escapará automáticamente de la cadena dada a menos que se pase un segundo argumento de `false`. Este método pasará el contenido de la respuesta a la función `strip_tags` de PHP antes de hacer la afirmación:

    $response->assertDontSeeText($value, $escaped = true);

[]()

#### assertDownload

Asegurar que la respuesta es una "descarga". Normalmente, esto significa que la ruta invocada que devolvió la respuesta devolvió una respuesta `Response::download`, `BinaryFileResponse` o `Storage::download`:

    $response->assertDownload();

Si lo desea, puede aseverar que al fichero descargable se le asignó un nombre de fichero dado:

    $response->assertDownload('image.jpg');

[]()

#### assertExactJson

Afirmar que la respuesta contiene una coincidencia exacta de los datos JSON dados:

    $response->assertExactJson(array $data);

[]()

#### assertForbidden

Afirmar que la respuesta tiene un código de estado HTTP prohibido (403):

    $response->assertForbidden();

[]()

#### assertHeader

Comprueba que la respuesta contiene la cabecera y el valor indicados:

    $response->assertHeader($headerName, $value = null);

[]()

#### assertHeaderMissing

Compruebe que la respuesta no contiene la cabecera y el valor indicados:

    $response->assertHeaderMissing($headerName);

[]()

#### assertJson

Comprueba que la respuesta contiene los datos JSON indicados:

    $response->assertJson(array $data, $strict = false);

El método `assertJson` convierte la respuesta en un array y utiliza `PHPUnit::assertArraySubset` para verificar que el array dado existe dentro de la respuesta JSON devuelta por la aplicación. Por lo tanto, si hay otras propiedades en la respuesta JSON, esta test pasará siempre y cuando el fragmento dado esté presente.

[]()

#### assertJsonCount

Comprueba que la respuesta JSON tiene una array con el número esperado de elementos en la clave dada:

    $response->assertJsonCount($count, $key = null);

[]()

#### assertJsonFragment

Comprueba que la respuesta contiene los datos JSON indicados en cualquier parte de la respuesta:

    Route::get('/users', function () {
        return [
            'users' => [
                [
                    'name' => 'Taylor Otwell',
                ],
            ],
        ];
    });

    $response->assertJsonFragment(['name' => 'Taylor Otwell']);

[]()

#### assertJsonMissing

Comprueba que la respuesta no contiene los datos JSON indicados:

    $response->assertJsonMissing(array $data);

[]()

#### assertJsonMissingExact

Se comprueba que la respuesta no contiene los datos JSON exactos:

    $response->assertJsonMissingExact(array $data);

[]()

#### assertJsonMissingValidationErrors

Comprueba que la respuesta no tiene errores de validación JSON para las claves dadas:

    $response->assertJsonMissingValidationErrors($keys);

> **Nota**  
> El método más genérico [assertValid](#assert-valid) puede ser utilizado para afirmar que una respuesta no tiene errores de validación que fueron devueltos como JSON **y** que ningún error fue flasheado al almacenamiento de sesión.

[]()

#### assertJsonPath

Asegurar que la respuesta contiene los datos dados en la ruta especificada:

    $response->assertJsonPath($path, $expectedValue);

Por ejemplo, si su aplicación devuelve la siguiente respuesta JSON:

```json
{
    "user": {
        "name": "Steve Schoger"
    }
}
```

Puede afirmar que la propiedad `name` del objeto `user` coincide con un valor dado de este modo:

    $response->assertJsonPath('user.name', 'Steve Schoger');

[]()

#### assertJsonMissingPath

Afirmar que la respuesta no contiene la ruta dada:

    $response->assertJsonMissingPath($path);

Por ejemplo, si su aplicación devuelve la siguiente respuesta JSON:

```json
{
    "user": {
        "name": "Steve Schoger"
    }
}
```

Puede afirmar que no contiene la propiedad `email` del objeto `user`:

    $response->assertJsonMissingPath('user.email');

[]()

#### assertJsonStructure

Compruebe que la respuesta tiene una estructura JSON determinada:

    $response->assertJsonStructure(array $structure);

Por ejemplo, si la respuesta JSON devuelta por tu aplicación contiene los siguientes datos:

```json
{
    "user": {
        "name": "Steve Schoger"
    }
}
```

Puedes afirmar que la estructura JSON coincide con tus expectativas de esta forma:

    $response->assertJsonStructure([
        'user' => [
            'name',
        ]
    ]);

A veces, las respuestas JSON devueltas por tu aplicación pueden contener matrices de objetos:

```json
{
    "user": [
        {
            "name": "Steve Schoger",
            "age": 55,
            "location": "Earth"
        },
        {
            "name": "Mary Schoger",
            "age": 60,
            "location": "Earth"
        }
    ]
}
```

En este caso, puede utilizar el carácter `*` para comprobar la estructura de todos los objetos de la array:

    $response->assertJsonStructure([
        'user' => [
            '*' => [
                 'name',
                 'age',
                 'location'
            ]
        ]
    ]);

[]()

#### assertJsonValidationErrors

Asegúrese de que la respuesta tiene los errores de validación JSON dados para las claves dadas. Este método debe utilizarse cuando se comprueban respuestas en las que los errores de validación se devuelven como una estructura JSON en lugar de enviarse a la sesión:

    $response->assertJsonValidationErrors(array $data, $responseKey = 'errors');

> **Nota**  
> El método más genérico [assertInvalid](#assert-invalid) se puede utilizar para afirmar que una respuesta tiene errores de validación devueltos como JSON **o** que los errores se mostraron en el almacenamiento de sesión.

[]()

#### assertJsonValidationErrorPara

Afirmar que la respuesta tiene errores de validación JSON para la clave dada:

    $response->assertJsonValidationErrorFor(string $key, $responseKey = 'errors');

[]()

#### assertLocation

Comprobar que la respuesta tiene el valor URI indicado en la cabecera `Location`:

    $response->assertLocation($uri);

[]()

#### assertContent

Comprobar que la cadena indicada coincide con el contenido de la respuesta:

    $response->assertContent($value);

[]()

#### assertNoContent

Comprueba que la respuesta tiene el código de estado HTTP indicado y no tiene contenido:

    $response->assertNoContent($status = 204);

[]()

#### assertStreamedContent

Compruebe que la cadena indicada coincide con el contenido de la respuesta transmitida:

    $response->assertStreamedContent($value);

[]()

#### assertNotFound

Comprobar que la respuesta tiene un código de estado HTTP no encontrado (404):

    $response->assertNotFound();

[]()

#### assertOk

Comprobar que la respuesta tiene un código de estado HTTP 200:

    $response->assertOk();

[]()

#### assertPlainCookie

Comprobar que la respuesta contiene la cookie no cifrada dada:

    $response->assertPlainCookie($cookieName, $value = null);

[]()

#### assertRedirect

Compruebe que la respuesta es una redirección al URI indicado:

    $response->assertRedirect($uri);

[]()

#### assertRedirectContiene

Comprueba si la respuesta redirige a un URI que contiene la cadena indicada:

    $response->assertRedirectContains($string);

[]()

#### assertRedirectToRoute

Afirmar que la respuesta es una redirección a la [ruta](/docs/%7B%7Bversion%7D%7D/routing#named-routes) indicada:

    $response->assertRedirectToRoute($name = null, $parameters = []);

[]()

#### assertRedirectToSignedRoute

Afirmar que la respuesta es una redirección a la ruta [firmada](/docs/%7B%7Bversion%7D%7D/urls#signed-urls) dada:

    $response->assertRedirectToSignedRoute($name = null, $parameters = []);

[]()

#### assertVer

Asegurar que la cadena dada está contenida en la respuesta. Esta aserción escapará automáticamente de la cadena dada a menos que pase un segundo argumento de `false`:

    $response->assertSee($value, $escaped = true);

[]()

#### assertSeeInOrder

Asegúrese de que las cadenas indicadas están ordenadas en la respuesta. Esta aserción escapará automáticamente de las cadenas dadas a menos que pase un segundo argumento de `false`:

    $response->assertSeeInOrder(array $values, $escaped = true);

[]()

#### assertSeeText

Afirmar que la cadena dada está contenida en el texto de la respuesta. Esta aserción escapará automáticamente de la cadena dada a menos que se pase un segundo argumento de `false`. El contenido de la respuesta será pasado a la función PHP `strip_tags` antes de que se haga la afirmación:

    $response->assertSeeText($value, $escaped = true);

[]()

#### assertSeeTextInOrder

Asegura que las cadenas dadas están contenidas en orden dentro del texto de respuesta. Esta aserción escapará automáticamente las cadenas dadas a menos que pase un segundo argumento de `false`. El contenido de la respuesta se pasará a la función `strip_tags` de PHP antes de que se realice la aserción:

    $response->assertSeeTextInOrder(array $values, $escaped = true);

[]()

#### assertSessionHas

Afirmar que la sesión contiene el dato dado:

    $response->assertSessionHas($key, $value = null);

Si es necesario, se puede proporcionar un closure como segundo argumento del método `assertSessionHas`. La aserción pasará si el closure devuelve `verdadero`:

    $response->assertSessionHas($key, function ($value) {
        return $value->name === 'Taylor Otwell';
    });

[]()

#### assertSessionHasInput

Asegurar que la sesión tiene un valor dado en el [array entrada flasheado](/docs/%7B%7Bversion%7D%7D/responses#redirecting-with-flashed-session-data):

    $response->assertSessionHasInput($key, $value = null);

Si es necesario, se puede proporcionar un closure como segundo argumento del método `assertSessionHasInput`. La aserción pasará si el closure devuelve `true`:

    $response->assertSessionHasInput($key, function ($value) {
        return Crypt::decryptString($value) === 'secret';
    });

[]()

#### assertSessionHasAll

Asegúrese de que la sesión contiene una array de pares clave/valor:

    $response->assertSessionHasAll(array $data);

Por ejemplo, si la sesión de tu aplicación contiene las claves `name` y `status`, puedes afirmar que ambas existen y tienen los valores especificados de esta forma:

    $response->assertSessionHasAll([
        'name' => 'Taylor Otwell',
        'status' => 'active',
    ]);

[]()

#### assertSessionHasErrors

Afirme que la sesión contiene un error para las `$claves` dadas. Si `$claves` es una array asociativa, asegúrese de que la sesión contiene un mensaje de error específico (valor) para cada campo (clave). Este método debe utilizarse cuando se prueban rutas que envían errores de validación a la sesión en lugar de devolverlos como una estructura JSON:

    $response->assertSessionHasErrors(
        array $keys, $format = null, $errorBag = 'default'
    );

Por ejemplo, para afirmar que los campos `nombre` y `correo electrónico` tienen mensajes de error de validación que fueron enviados a la sesión, puede invocar el método `assertSessionHasErrors` de esta manera:

    $response->assertSessionHasErrors(['name', 'email']);

O, puede afirmar que un campo dado tiene un mensaje de error de validación en particular:

    $response->assertSessionHasErrors([
        'name' => 'The given name was invalid.'
    ]);

> **Nota**  
> El método más genérico [assertInvalid](#assert-invalid) se puede utilizar para afirmar que una respuesta tiene errores de validación devueltos como JSON **o** que los errores se transfirieron al almacenamiento de la sesión.

[]()

#### assertSessionHasErrorsIn

Afirma que la sesión contiene un error para las `$claves` dadas dentro de una bolsa [de errores](/docs/%7B%7Bversion%7D%7D/validation#named-error-bags) específica. Si `$keys` es un array asociativo, afirma que la sesión contiene un mensaje de error específico (valor) para cada campo (clave), dentro de la bolsa de errores:

    $response->assertSessionHasErrorsIn($errorBag, $keys = [], $format = null);

[]()

#### assertSessionNoTieneErrores

Afirmar que la sesión no tiene errores de validación:

    $response->assertSessionHasNoErrors();

[]()

#### assertSessionNoTieneErrores

Asegurar que la sesión no tiene errores de validación para las claves dadas:

    $response->assertSessionDoesntHaveErrors($keys = [], $format = null, $errorBag = 'default');

> **Nota**  
> El método más genérico [assertValid](#assert-valid) puede ser utilizado para afirmar que una respuesta no tiene errores de validación que fueron devueltos como JSON **y** que ningún error fue flasheado al almacenamiento de la sesión.

[]()

#### assertSessionSinErrores

Afirmar que la sesión no contiene la clave dada:

    $response->assertSessionMissing($key);

[]()

#### assertStatus

Comprobar que la respuesta tiene un código de estado HTTP determinado:

    $response->assertStatus($code);

[]()

#### assertSuccessful

Asegurar que la respuesta tiene un código de estado HTTP correcto (>= 200 y < 300):

    $response->assertSuccessful();

[]()

#### assertUnauthorized

Afirmar que la respuesta tiene un código de estado HTTP no autorizado (401):

    $response->assertUnauthorized();

[]()

#### assertUnprocessable

Afirmar que la respuesta tiene una entidad no procesable (422) Código de estado HTTP:

    $response->assertUnprocessable();

[]()

#### assertValid

Afirmar que la respuesta no tiene errores de validación para las claves dadas. Este método puede utilizarse para afirmar contra respuestas en las que los errores de validación se devuelven como una estructura JSON o en las que los errores de validación se han transmitido a la sesión:

    // Assert that no validation errors are present...
    $response->assertValid();

    // Assert that the given keys do not have validation errors...
    $response->assertValid(['name', 'email']);

[]()

#### assertInvalid

Afirmar que la respuesta tiene errores de validación para las claves dadas. Este método puede utilizarse para confirmar respuestas en las que los errores de validación se devuelven como una estructura JSON o en las que los errores de validación se han transmitido a la sesión:

    $response->assertInvalid(['name', 'email']);

También puede afirmar que una clave dada tiene un mensaje de error de validación concreto. Al hacerlo, puede proporcionar el mensaje completo o sólo una pequeña parte del mensaje:

    $response->assertInvalid([
        'name' => 'The name field is required.',
        'email' => 'valid email address',
    ]);

[]()

#### assertViewHas

Afirmar que la vista de respuesta contiene un dato determinado:

    $response->assertViewHas($key, $value = null);

Si pasas un closure como segundo argumento al método `assertViewHas`, podrás inspeccionar y hacer afirmaciones sobre un dato concreto de la vista:

    $response->assertViewHas('user', function (User $user) {
        return $user->name === 'Taylor';
    });

Además, se puede acceder a los datos de la vista como variables de array en la respuesta, lo que permite inspeccionarlos cómodamente:

    $this->assertEquals('Taylor', $response['name']);

[]()

#### assertViewHasAll

Afirmar que la vista de respuesta tiene una lista dada de datos:

    $response->assertViewHasAll(array $data);

Este método se puede utilizar para afirmar que la vista simplemente contiene datos que coinciden con las claves dadas:

    $response->assertViewHasAll([
        'name',
        'email',
    ]);

O puede afirmar que los datos de la vista están presentes y tienen valores específicos:

    $response->assertViewHasAll([
        'name' => 'Taylor Otwell',
        'email' => 'taylor@example.com,',
    ]);

[]()

#### assertViewIs

Afirmar que la vista dada fue devuelta por la ruta:

    $response->assertViewIs($value);

[]()

#### assertViewMissing

Afirmar que la clave de datos dada no estaba disponible para la vista devuelta en la respuesta de la aplicación:

    $response->assertViewMissing($key);

[]()

### Aserciones de autenticación

Laravel también proporciona una variedad de aserciones relacionadas con la autenticación que puedes utilizar dentro de las tests características de tu aplicación. Tenga en cuenta que estos métodos se invocan en la propia clase de test y no en la instancia `Illuminate\Testing\TestResponse` devuelta por métodos como `get` y `post`.

[]()

#### assertAuthenticated

Comprueba que un usuario está autenticado:

    $this->assertAuthenticated($guard = null);

[]()

#### assertGuest

Comprobar que un usuario no está autenticado:

    $this->assertGuest($guard = null);

[]()

#### assertAuthenticatedAs

Comprobar que un usuario específico está autenticado:

    $this->assertAuthenticatedAs($user, $guard = null);
