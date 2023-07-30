<div class="contenedor reestablecer">
    
<?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Coloca tu Nuevo Password</p>

        <form class="formulario" method="POST" action="/reestablecer">

            <div class="campo">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    placeholder="Tu Password" 
                    name="password"
                />
            </div>

            <input type="submit" class="boton" value="Guardar Password">
        </form>

        <div class="acciones">
            <a href="/">¿Ya Tienes Cuenta? Inicia Sesión</a>
            <a href="/olvide">¿Olvidaste tu Password?</a>
        </div>
    </div>
</div>