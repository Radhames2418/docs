# Laravel Socialite

- [Introducción](#introduction)
- [Instalación](#installation)
- [Actualización de Socialite](#upgrading-socialite)
- [Configuración](#configuration)
- [Autenticación](#authentication)
  - [Enrutamiento](#routing)
  - [Autenticación y almacenamiento](#authentication-and-storage)
  - [Ámbitos de acceso](#access-scopes)
  - [Parámetros opcionales](#optional-parameters)
- [Recuperación de datos de usuario](#retrieving-user-details)

[]()

## Introducción

Además de la típica autenticación basada en formularios, Laravel también proporciona una forma sencilla y cómoda de autenticarse con proveedores OAuth utilizando [Laravel Socialite](https://github.com/laravel/socialite). Socialite actualmente soporta autenticación a través de Facebook, Twitter, LinkedIn, Google, GitHub, GitLab, y Bitbucket.

> **Nota**  
> Adaptadores para otras plataformas están disponibles a través de la comunidad impulsada [Socialite Proveedores](https://socialiteproviders.com/) sitio web.

[]()

## Instalación

Para empezar con Socialite, utiliza el gestor de paquetes Composer para añadir el paquete a las dependencias de tu proyecto:

```shell
composer require laravel/socialite
```

[]()

## Actualización de Socialite

Cuando actualice a una nueva versión principal de Socialite, es importante que revise detenidamente [la guía de actualización](https://github.com/laravel/socialite/blob/master/UPGRADE.md).

[]()

## Configuración

Antes de utilizar Socialite, necesitarás añadir credenciales para los proveedores OAuth que utiliza tu aplicación. Por lo general, estas credenciales se pueden recuperar mediante la creación de una "aplicación de desarrollador" en el panel de control del servicio con el que se va a autenticar.

Estas credenciales deben colocarse en el archivo de configuración `config/services.php` de tu aplicación, y deben utilizar la clave `facebook`, `twitter` (OAuth 1.0), `twitter-oauth-2` (OAuth 2.0), `linkedin`, `google`, `github`, `gitlab`, o `bitbucket`, dependiendo de los proveedores que requiera tu aplicación:

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => 'http://example.com/callback-url',
    ],

> **Nota**  
> Si la opción de `redirección` contiene una ruta relativa, se resolverá automáticamente a una URL completa.

[]()

## Autenticación

[]()

### Enrutamiento

Para autenticar usuarios usando un proveedor OAuth, necesitarás dos rutas: una para redirigir al usuario al proveedor OAuth, y otra para recibir la llamada de retorno del proveedor después de la autenticación. Las rutas de ejemplo a continuación demuestran la implementación de ambas rutas:

    use Laravel\Socialite\Facades\Socialite;

    Route::get('/auth/redirect', function () {
        return Socialite::driver('github')->redirect();
    });

    Route::get('/auth/callback', function () {
        $user = Socialite::driver('github')->user();

        // $user->token
    });

El método de `redirección` proporcionado por la facade `de Socialite` se encarga de redirigir al usuario al proveedor de OAuth, mientras que el método de `usuario` examinará la solicitud entrante y recuperará la información del usuario del proveedor después de que haya aprobado la solicitud de autenticación.

[]()

### Autenticación y almacenamiento

Una vez que el usuario ha sido recuperado del proveedor OAuth, puedes determinar si el usuario existe en la base de datos de tu aplicación y [autenticarlo](/docs/%7B%7Bversion%7D%7D/authentication#authenticate-a-user-instance). Si el usuario no existe en la base de datos de tu aplicación, típicamente crearás un nuevo registro en tu base de datos para representar al usuario:

    use App\Models\User;
    use Illuminate\Support\Facades\Auth;
    use Laravel\Socialite\Facades\Socialite;

    Route::get('/auth/callback', function () {
        $githubUser = Socialite::driver('github')->user();

        $user = User::updateOrCreate([
            'github_id' => $githubUser->id,
        ], [
            'name' => $githubUser->name,
            'email' => $githubUser->email,
            'github_token' => $githubUser->token,
            'github_refresh_token' => $githubUser->refreshToken,
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    });

> **Nota**  
> Para más información sobre qué información de usuario está disponible en proveedores OAuth específicos, consulta la documentación sobre [recuperación de detalles de usuario](#retrieving-user-details).

[]()

### Ámbitos de acceso

Antes de redirigir al usuario, puede utilizar el método `scopes` para especificar los "ámbitos" que deben incluirse en la solicitud de autenticación. Este método fusionará todos los ámbitos especificados previamente con los ámbitos que especifiques:

    use Laravel\Socialite\Facades\Socialite;

    return Socialite::driver('github')
        ->scopes(['read:user', 'public_repo'])
        ->redirect();

Puedes sobrescribir todos los ámbitos existentes en la solicitud de autenticación utilizando el método `setScopes`:

    return Socialite::driver('github')
        ->setScopes(['read:user', 'public_repo'])
        ->redirect();

[]()

### Parámetros opcionales

Algunos proveedores de OAuth admiten otros parámetros opcionales en la solicitud de redirección. Para incluir cualquier parámetro opcional en la petición, llama al método `with` con un array asociativo:

    use Laravel\Socialite\Facades\Socialite;

    return Socialite::driver('google')
        ->with(['hd' => 'example.com'])
        ->redirect();

> **Advertencia**  
> Cuando utilice el método `with`, tenga cuidado de no pasar ninguna palabra clave reservada como `state` o `response_type`.

[]()

## Recuperación de datos de usuario

Después de que el usuario es redirigido de vuelta a la ruta de autenticación de tu aplicación, puedes recuperar los detalles del usuario utilizando el método `user` de Socialite. El objeto user devuelto por el método `user` proporciona una variedad de propiedades y métodos que puedes utilizar para almacenar información sobre el usuario en tu propia base de datos.

Dependiendo de si el proveedor de OAuth con el que te estás autenticando soporta OAuth 1.0 u OAuth 2.0, las propiedades y métodos de este objeto pueden variar:

    use Laravel\Socialite\Facades\Socialite;

    Route::get('/auth/callback', function () {
        $user = Socialite::driver('github')->user();

        // OAuth 2.0 providers...
        $token = $user->token;
        $refreshToken = $user->refreshToken;
        $expiresIn = $user->expiresIn;

        // OAuth 1.0 providers...
        $token = $user->token;
        $tokenSecret = $user->tokenSecret;

        // All providers...
        $user->getId();
        $user->getNickname();
        $user->getName();
        $user->getEmail();
        $user->getAvatar();
    });

[]()

#### Recuperación de datos de usuario de un token (OAuth2)

Si ya tienes un token de acceso válido para un usuario, puedes recuperar sus datos de usuario utilizando el método `userFromToken` de Socialite:

    use Laravel\Socialite\Facades\Socialite;

    $user = Socialite::driver('github')->userFromToken($token);

[]()

#### Obtención de datos de usuario a partir de un token y un secreto (OAuth1)

Si ya tienes un token y un secreto válidos para un usuario, puedes recuperar sus datos de usuario utilizando el método `userFromTokenAndSecret` de Socialite:

    use Laravel\Socialite\Facades\Socialite;

    $user = Socialite::driver('twitter')->userFromTokenAndSecret($token, $secret);

[]()

#### Autenticación sin estado

El método `stateless` se puede utilizar para desactivar la verificación del estado de la sesión. Esto es útil cuando se añade autenticación social a una API sin estado que no utiliza sesiones basadas en cookies:

    use Laravel\Socialite\Facades\Socialite;

    return Socialite::driver('google')->stateless()->user();

> **Advertencia**  
> La autenticación sin estado no está disponible para el controlador Twitter OAuth 1.0.
