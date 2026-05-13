<section class="card form-card">
    <h1>Únete a El Rastro</h1>
    <form method="post" class="form-grid">
        <label>Nombre
            <input type="text" name="nombre" required>
        </label>
        <label>Email
            <input type="email" name="email" required>
        </label>
        <label>Contraseña
            <input type="password" name="contrasena" minlength="6" required>
        </label>
        <label>Dirección
            <input type="text" name="direccion">
        </label>
        <label class="full">Método de pago
            <input type="text" name="metodo_pago" placeholder="Tarjeta, PayPal...">
        </label>
        <button class="btn primary full" type="submit">Registrarse</button>
    </form>
</section>
