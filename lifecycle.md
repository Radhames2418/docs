# Ciclo de vida de la petición

- [Introducción](#introduction)
- [Visión general del ciclo de vida](#lifecycle-overview)
  - [Primeros pasos](#first-steps)
  - [Núcleos HTTP / Consola](#http-console-kernels)
  - [Proveedores de servicios](#service-providers)
  - [Enrutamiento](#routing)
  - [Finalización](#finishing-up)
- [Centrarse en los proveedores de servicios](#focus-on-service-providers)

[]()

## Introducción

Cuando se utiliza cualquier herramienta en el "mundo real", uno se siente más seguro si entiende cómo funciona esa herramienta. El desarrollo de aplicaciones no es diferente. Cuando entiendes cómo funcionan tus herramientas de desarrollo, te sientes más cómodo y seguro utilizándolas.

El objetivo de este documento es darte una buena visión general de alto nivel de cómo funciona el framework Laravel. Al conocer mejor el framework en general, todo parece menos "mágico" y te sentirás más seguro construyendo tus aplicaciones. Si no entiendes todos los términos a la primera, ¡no te desanimes! Sólo trata de obtener una comprensión básica de lo que está pasando, y tu conocimiento crecerá a medida que explores otras secciones de la documentación.

[]()

## Visión general del ciclo de vida

[]()

### Primeros pasos

El punto de entrada para todas las peticiones a una aplicación Laravel es el archivo `public/index.php`. Todas las solicitudes son dirigidas a este archivo por la configuración de su servidor web (Apache / Nginx). El archivo index `.` php no contiene mucho código. Más bien, es un punto de partida para cargar el resto del framework.

El archivo `index.` php carga la definición del autoloader generado por Composer, y luego recupera una instancia de la aplicación Laravel desde `bootstrap/app.php`. La primera acción tomada por Laravel es crear una instancia de la aplicación / [contenedor de servicios](/docs/%7B%7Bversion%7D%7D/container).

[]()

### Núcleos HTTP / Consola

A continuación, la solicitud entrante se envía al núcleo HTTP o al núcleo de consola, dependiendo del tipo de solicitud que esté entrando en la aplicación. Estos dos núcleos sirven como la ubicación central a través de la cual fluyen todas las peticiones. Por ahora, vamos a centrarnos en el núcleo HTTP, que se encuentra en `app/Http/Kernel.php`.

El núcleo HTTP extiende la clase `Illuminate\Foundation\Http\Kernel`, que define un array de `bootstrappers` que se ejecutarán antes de que se ejecute la petición. Estos bootstrappers configuran el manejo de errores, configuran el registro, [detectan el entorno de la aplicación](/docs/%7B%7Bversion%7D%7D/configuration#environment-configuration), y realizan otras tareas que deben hacerse antes de que la solicitud sea realmente manejada. Típicamente, estas clases manejan la configuración interna de Laravel de la que no necesitas preocuparte.

El núcleo HTTP también define una lista de [middleware](/docs/%7B%7Bversion%7D%7D/middleware) HTTP que todas las solicitudes deben pasar antes de ser manejadas por la aplicación. Estos middleware manejan la lectura y escritura de la [sesión HT](/docs/%7B%7Bversion%7D%7D/session)TP, determinan si la aplicación está en modo de mantenimiento, [verifican el token CSRF](/docs/%7B%7Bversion%7D%7D/csrf), y más. Hablaremos más sobre esto pronto.

La firma del método `handle` del núcleo HTTP es bastante simple: recibe una `petición` y devuelve una `respuesta`. Piensa en el kernel como una gran caja negra que representa toda tu aplicación. Aliméntalo con peticiones HTTP y devolverá respuestas HTTP.

[]()

### Proveedores de servicios

Una de las acciones más importantes del kernel bootstrapping es cargar los [proveedores de](/docs/%7B%7Bversion%7D%7D/providers) servicio para tu aplicación. Los proveedores de servicios son responsables de arrancar todos los componentes del framework, como la base de datos, la cola, la validación y los componentes de enrutamiento. Todos los proveedores de servicios de la aplicación se configuran en la array `proveedores` del archivo de configuración `config/app.php`.

Laravel iterará a través de esta lista de proveedores e instanciará cada uno de ellos. Después de instanciar los proveedores, el método `register` será llamado en todos los proveedores. Luego, una vez que todos los proveedores han sido registrados, el método `boot` será llamado en cada proveedor. Esto es para que los proveedores de servicios puedan depender de que cada contenedor vinculante esté registrado y disponible en el momento en que se ejecute su método de `arranque`.

Esencialmente cada característica importante ofrecida por Laravel es arrancada y configurada por un proveedor de servicios. Dado que arrancan y configuran tantas características ofrecidas por el framework, los proveedores de servicios son el aspecto más importante de todo el proceso de arranque de Laravel.

[]()

### Enrutamiento

Uno de los proveedores de servicios más importantes en su aplicación es el `App\Providers\RouteServiceProvider`. Este proveedor de servicios carga los archivos de ruta contenidos en el directorio de `rutas` de tu aplicación. Adelante, ¡abre el código `de RouteServiceProvider` y echa un vistazo a cómo funciona!

Una vez que la aplicación ha sido arrancada y todos los proveedores de servicios se han registrado, la `solicitud` será entregada al enrutador para su envío. El enrutador enviará la solicitud a una ruta o controlador, así como ejecutar cualquier middleware específico de la ruta.

Losmiddleware proporcionan un mecanismo conveniente para filtrar o examinar las peticiones HTTP que entran en tu aplicación. Por ejemplo, Laravel incluye un middleware que verifica si el usuario de tu aplicación está autenticado. Si el usuario no está autenticado, el middleware redirigirá al usuario a la pantalla de login. Sin embargo, si el usuario está autenticado, el middleware permitirá que la solicitud continúe en la aplicación. Algunos middleware se asignan a todas las rutas dentro de la aplicación, como los definidos en la propiedad `$middleware` de tu kernel HTTP, mientras que otros sólo se asignan a rutas específicas o grupos de rutas. Puedes aprender más sobre middleware leyendo la [documentación](/docs/%7B%7Bversion%7D%7D/middleware) completa sobre [middleware](/docs/%7B%7Bversion%7D%7D/middleware).

Si la solicitud pasa a través de todos los middleware asignados a la ruta correspondiente, se ejecutará el método de la ruta o del controlador y la respuesta devuelta por el método de la ruta o del controlador se enviará de vuelta a través de la cadena de middleware de la ruta.

[]()

### Finalización

Una vez que el método de ruta o controlador devuelve una respuesta, la respuesta viajará de vuelta hacia fuera a través middleware middleware de la ruta, dando a la aplicación la oportunidad de modificar o examinar la respuesta saliente.

Finalmente, una vez que la respuesta viaja de vuelta a través middleware middleware, el método `handle` del kernel HTTP devuelve el objeto respuesta y el archivo `index.` php llama al método `send` sobre la respuesta devuelta. El método `send` envía el contenido de la respuesta al navegador web del usuario. ¡Hemos terminado nuestro viaje a través de todo el ciclo de vida de las peticiones de Laravel!

[]()

## Enfoque en los Proveedores de Servicios

Los proveedores de servicios son realmente la clave para arrancar una aplicación Laravel. Se crea la instancia de la aplicación, se registran los proveedores de servicio, y la petición se entrega a la aplicación bootstrapped. Así de sencillo.

Tener una comprensión firme de cómo una aplicación Laravel se construye y bootstrapped a través de proveedores de servicios es muy valioso. Los proveedores de servicio por defecto de tu aplicación se almacenan en el directorio `app/Providers`.

Por defecto, el `AppServiceProvider` está bastante vacío. Este proveedor es un buen lugar para añadir el propio bootstrapping de tu aplicación y los enlaces del contenedor de servicios. Para aplicaciones grandes, es posible que desee crear varios proveedores de servicios, cada uno con bootstrapping más granular para servicios específicos utilizados por su aplicación.
