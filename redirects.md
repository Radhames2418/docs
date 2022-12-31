# Redirecciones HTTP

- [Creación de redireccionamientos](#creating-redirects)
- [Redirección a rutas con nombre](#redirecting-named-routes)
- [Redirección a acciones de controlador](#redirecting-controller-actions)
- [Redirección con datos de sesión flasheados](#redirecting-with-flashed-session-data)

[]()

## Creación de redirecciones

Las respuestas de redirección son instancias de la clase `Illuminate\Http\RedirectResponse`, y contienen las cabeceras adecuadas necesarias para redirigir al usuario a otra URL. Hay varias maneras de generar una instancia `RedirectResponse`. El método más sencillo es utilizar el ayudante de `redirección` global:

    Route::get('/dashboard', function () {
        return redirect('/home/dashboard');
    });

A veces es posible que desee redirigir al usuario a su ubicación anterior, como cuando un formulario enviado no es válido. Puede hacerlo utilizando la función global de ayuda a la `redirección`. Dado que esta función utiliza la [sesión](/docs/%7B%7Bversion%7D%7D/session), asegúrese de que la ruta que llama a la función de `vuelta` está utilizando el grupo de middleware `web` o tiene todo el middleware sesión aplicado:

    Route::post('/user/profile', function () {
        // Validate the request...

        return back()->withInput();
    });

[]()

## Redirección a rutas con nombre

Cuando se llama al ayudante de `redirección` sin parámetros, se devuelve una instancia de `Illuminate\Routing\Redirector`, lo que permite llamar a cualquier método de la instancia `Redirector`. Por ejemplo, para generar una `RedirectResponse` a una ruta con nombre, puede utilizar el método `route`:

    return redirect()->route('login');

Si su ruta tiene parámetros, puede pasarlos como segundo argumento al método de `ruta`:

    // For a route with the following URI: profile/{id}

    return redirect()->route('profile', ['id' => 1]);

[]()

#### Rellenando parámetros a través de modelos Eloquent

Si está redirigiendo a una ruta con un parámetro "ID" que está siendo rellenado desde un modelo Eloquent, puede pasar el propio modelo. El ID se extraerá automáticamente:

    // For a route with the following URI: profile/{id}

    return redirect()->route('profile', [$user]);

Si desea personalizar el valor que se coloca en el parámetro de ruta, debe anular el método `getRouteKey` en su modelo Eloquent:

    /**
     * Get the value of the model's route key.
     *
     * @return mixed
     */
    public function getRouteKey()
    {
        return $this->slug;
    }

[]()

## Redirección a acciones de controlador

También puede generar redirecciones a [acciones del controlador](/docs/%7B%7Bversion%7D%7D/controllers). Para ello, pase el controlador y el nombre de la acción al método de `acción`:

    use App\Http\Controllers\HomeController;

    return redirect()->action([HomeController::class, 'index']);

Si la ruta de tu controlador requiere parámetros, puedes pasarlos como segundo argumento al método de `acción`:

    return redirect()->action(
        [UserController::class, 'profile'], ['id' => 1]
    );

[]()

## Redirección con datos de sesión flasheados

La redirección a una nueva URL y la [transmisión de datos a la](/docs/%7B%7Bversion%7D%7D/session#flash-data) sesión suelen hacerse al mismo tiempo. Normalmente, esto se hace después de realizar con éxito una acción cuando se muestra un mensaje de éxito a la sesión. Para mayor comodidad, puede crear una instancia de `RedirectResponse` y enviar los datos a la sesión en una única cadena de métodos fluida:

    Route::post('/user/profile', function () {
        // Update the user's profile...

        return redirect('/dashboard')->with('status', 'Profile updated!');
    });

Puede utilizar el método `withInput` proporcionado por la instancia `RedirectResponse` para enviar los datos de entrada de la solicitud actual a la sesión antes de redirigir al usuario a una nueva ubicación. Una vez que los datos de entrada han sido transferidos a la sesión, puede [recuperarlos](/docs/%7B%7Bversion%7D%7D/requests#retrieving-old-input) fácilmente durante la siguiente petición:

    return back()->withInput();

Después de redirigir al usuario, puede mostrar el mensaje de la [sesión](/docs/%7B%7Bversion%7D%7D/session). Por ejemplo, utilizando la [sintaxis de Blade](/docs/%7B%7Bversion%7D%7D/blade):

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
