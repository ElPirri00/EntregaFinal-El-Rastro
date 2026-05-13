<section class="card profile">
    <h1>Mi perfil</h1>
    <h2>Tus productos</h2>
    <div class="profile-products">
        <?php foreach ($productos as $p): ?>
            <article class="profile-product">
                <img src="<?= htmlspecialchars(image_url($p['imagen'] ?? null)) ?>" alt="<?= htmlspecialchars($p['titulo']) ?>">
                <div>
                    <strong><?= htmlspecialchars($p['titulo']) ?></strong>
                    <span><?= htmlspecialchars($p['estado']) ?></span>
                </div>
                <div class="row-actions">
                    <a class="btn secondary small" href="<?= app_url('producto/editar/' . (int)$p['id_producto']) ?>">Editar</a>
                    <a class="btn danger small" href="<?= app_url('producto/eliminar/' . (int)$p['id_producto']) ?>" onclick="return confirm('¿Eliminar producto?')">Eliminar</a>
                </div>
            </article>
        <?php endforeach; ?>
        <?php if (empty($productos)): ?><p>No tienes productos publicados.</p><?php endif; ?>
    </div>

    <form method="post" class="form-grid profile-form">
        <h2 class="full">Editar datos</h2>
        <label>Nombre
            <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
        </label>
        <label>Dirección
            <input type="text" name="direccion" value="<?= htmlspecialchars($usuario['direccion']) ?>">
        </label>
        <p class="full muted">El método de pago se selecciona únicamente en el momento de realizar una compra. No se edita desde el perfil.</p>
        <button class="btn primary full" type="submit">Confirmar cambios</button>
    </form>
</section>
