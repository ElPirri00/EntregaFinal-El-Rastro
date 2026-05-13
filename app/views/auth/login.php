<section class="login-wrap">
    <div class="card form-card">
        <img class="form-logo" src="<?= asset_url('assets/img/logo.png') ?>" alt="Logo El Rastro">
        <h1>Te damos la bienvenida</h1>
        <form method="post" class="stack">
            <label>Email
                <input type="email" name="email" required>
            </label>
            <label>Contraseña
                <input type="password" name="contrasena" required>
            </label>
            <button class="btn primary" type="submit">Acceder al rastro</button>
            <a href="<?= app_url('auth/registro') ?>">¿No tienes cuenta? Regístrate</a>
        </form>
    </div>
</section>
