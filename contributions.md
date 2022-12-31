# Guía de contribución

- [Informes de errores](#bug-reports)
- [Preguntas de soporte](#support-questions)
- [Debate sobre el desarrollo del núcleo](#core-development-discussion)
- [¿Qué rama?](#which-branch)
- [Activos compilados](#compiled-assets)
- [Vulnerabilidades de seguridad](#security-vulnerabilities)
- [Estilo de codificación](#coding-style)
  - [PHPDoc](#phpdoc)
  - [EstiloCI](#styleci)
- [Código de conducta](#code-of-conduct)

[]()

## Informes de errores

Para fomentar la colaboración activa, Laravel recomienda encarecidamente pull requests, no sólo informes de errores. Los pull requests sólo se revisarán cuando estén marcados como "listos para revisión" (no en estado "borrador") y todas las tests de las nuevas características estén superadas. Las pull requests no activas que permanezcan en estado "borrador" se cerrarán al cabo de unos días.

Sin embargo, si envías un informe de error, tu problema debe contener un título y una descripción clara del problema. También debería incluir tanta información relevante como sea posible y un ejemplo de código que demuestre el problema. El objetivo de un informe de error es facilitarle a usted y a otros la reproducción del error y el desarrollo de una solución.

Recuerde que los informes de errores se crean con la esperanza de que otras personas con el mismo problema puedan colaborar con usted para resolverlo. No espere que el informe de fallo vea automáticamente alguna actividad o que otros se lancen a solucionarlo. Crear un informe de fallo sirve para ayudarte a ti mismo y a los demás a iniciar el camino para solucionar el problema. Si quieres colaborar, puedes ayudar corrigiendo [cualquier error que aparezca en nuestros gestores de incidencias](https://github.com/issues?q=is%3Aopen+is%3Aissue+label%3Abug+user%3Alaravel). Debes estar autenticado en GitHub para ver todas las incidencias de Laravel.

El código fuente de Laravel se gestiona en GitHub, y hay repositorios para cada uno de los proyectos de Laravel:

<div class="content-list" markdown="1"/>

- [Aplicación Laravel](https://github.com/laravel/laravel)
- [Arte Laravel](https://github.com/laravel/art)
- [Documentación de Laravel](https://github.com/laravel/docs)
- [Laravel Dusk](https://github.com/laravel/dusk)
- [Laravel Cajero Stripe](https://github.com/laravel/cashier)
- [Laravel Cajero Paddle](https://github.com/laravel/cashier-paddle)
- [Laravel Echo](https://github.com/laravel/echo)
- [Laravel Envoy](https://github.com/laravel/envoy)
- [Laravel Framework](https://github.com/laravel/framework)
- [Laravel Homestead](https://github.com/laravel/homestead)
- [Laravel Homestead Build Scripts](https://github.com/laravel/settler)
- [Laravel Horizon](https://github.com/laravel/horizon)
- [Laravel Jetstream](https://github.com/laravel/jetstream)
- [Laravel Passport](https://github.com/laravel/passport)
- [Laravel Pint](https://github.com/laravel/pint)
- [Laravel Sail](https://github.com/laravel/sail)
- [Laravel Sanctum](https://github.com/laravel/sanctum)
- [Laravel Scout](https://github.com/laravel/scout)
- [Laravel Socialite](https://github.com/laravel/socialite)
- [Laravel Telescope](https://github.com/laravel/telescope)
- [Laravel Sitio web](https://github.com/laravel/laravel.com-next)

[object Object]

[]()

## Preguntas de soporte

Los rastreadores de problemas de Laravel en GitHub no están pensados para proporcionar ayuda o soporte de Laravel. En su lugar, utiliza uno de los siguientes canales:

<div class="content-list" markdown="1"/>

- [Discusiones GitHub](https://github.com/laravel/framework/discussions)
- [Foros Laracasts](https://laracasts.com/discuss)
- [Foros Laravel.io](https://laravel.io/forum)
- [StackOverflow](https://stackoverflow.com/questions/tagged/laravel)
- [Discord](https://discord.gg/laravel)
- [Larachat](https://larachat.co)
- [IRC](https://web.libera.chat/?nick=artisan\&channels=#laravel)

[object Object]

[]()

## Core Development Discussion

Puedes proponer nuevas características o mejoras del comportamiento existente de Laravel en el [tablón de discusión de GitHub](https://github.com/laravel/framework/discussions) del repositorio del framework Laravel. Si propones una nueva característica, por favor, estate dispuesto a implementar al menos parte del código que sería necesario para completar la característica.

Las discusiones informales sobre errores, nuevas características e implementación de características existentes tienen lugar en el canal `#internals` del [servidor Discord de Laravel](https://discord.gg/laravel). Taylor Otwell, el mantenedor de Laravel, suele estar presente en el canal los días laborables de 8am-5pm (UTC-06:00 o América/Chicago), y esporádicamente en el canal en otros momentos.

[]()

## ¿Qué rama?

**Todas las** correcciones de errores deben ser enviados a la última versión que soporta correcciones de errores (actualmente `9.x`). Las correcciones de errores **nunca** deben enviarse a la rama `maestra` a menos que corrijan características que sólo existen en la próxima versión.

Las características**menores** que sean **totalmente compatibles** con la versión actual pueden enviarse a la última rama estable (actualmente 9 `.`x).

Las nuevas funciones**importantes** o las funciones con cambios de última hora deben enviarse siempre a la rama `maestra`, que contiene la próxima versión.

[]()

## Activos compilados

Si estás enviando un cambio que afectará a un archivo compilado, como la mayoría de los archivos en `resources/css` o `resources/js` del repositorio `laravel/laravel`, no confirmes los archivos compilados. Debido a su gran tamaño, no pueden ser revisados de forma realista por un mantenedor. Esto podría ser explotado como una forma de inyectar código malicioso en Laravel. Para prevenir esto de forma defensiva, todos los archivos compilados serán generados y confirmados por los mantenedores de Laravel.

[]()

## Vulnerabilidades de seguridad

Si descubre una vulnerabilidad de seguridad en Laravel, envíe un correo electrónico a Taylor Otwell a [](mailto:taylor@laravel.com)[taylor@laravel.com.](mailto:taylor@laravel.com) Todas las vulnerabilidades de seguridad serán abordadas con prontitud.

[]()

## Estilo de codificación

Laravel sigue el estándar de codificación [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) y el estándar de autocarga [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md).

[]()

### PHPDoc

A continuación se muestra un ejemplo de un bloque de documentación de Laravel válido. Observe que el atributo `@param` va seguido de dos espacios, el tipo de argumento, dos espacios más y, por último, el nombre de la variable:

    /**
     * Register a binding with the container.
     *
     * @param  string|array  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     *
     * @throws \Exception
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        //
    }

[]()

### EstiloCI

No te preocupes si el estilo de tu código no es perfecto. [StyleCI](https://styleci.io/) fusionará automáticamente cualquier corrección de estilo en el repositorio de Laravel después de que se fusionen los pull requests. Esto nos permite centrarnos en el contenido de la contribución y no en el estilo del código.

[]()

## Código de conducta

El código de conducta de Laravel se deriva del código de conducta de Ruby. Cualquier violación del código de conducta puede ser reportada a Taylor Otwell[(taylor@laravel.com)](mailto:taylor@laravel.com):

<div class="content-list" markdown="1"/>

- Los participantes serán tolerantes con las opiniones contrarias.
- Los participantes deben asegurarse de que su lenguaje y sus acciones están libres de ataques personales y comentarios personales despectivos.
- Al interpretar las palabras y acciones de los demás, los participantes deben suponer siempre buenas intenciones.
- No se tolerará ningún comportamiento que pueda considerarse razonablemente acoso.

[object Object]
