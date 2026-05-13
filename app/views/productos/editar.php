<section class="card form-card wide">
    <h1>Editar producto</h1>
    <form method="post" class="form-grid">
        <label>Título
            <input type="text" name="titulo" required maxlength="150" value="<?= htmlspecialchars($_POST['titulo'] ?? $producto['titulo']) ?>">
        </label>
        <label>Precio
            <input type="number" name="precio" step="0.01" min="0.01" required value="<?= htmlspecialchars($_POST['precio'] ?? $producto['precio']) ?>">
        </label>
        <label>Categoría
            <?php $categoriaActual = $_POST['categoria'] ?? ($producto['categoria'] ?? ''); ?>
            <select name="categoria" required>
                <option value="">Selecciona una categoría</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= htmlspecialchars($categoria) ?>" <?= $categoriaActual === $categoria ? 'selected' : '' ?>>
                        <?= htmlspecialchars($categoria) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Estado del producto
            <?php $estadoActual = $_POST['estado_producto'] ?? ($producto['estado_producto'] ?? ''); ?>
            <select name="estado_producto" required>
                <option value="">Selecciona el estado</option>
                <?php foreach ($estadosProducto as $estadoProducto): ?>
                    <option value="<?= htmlspecialchars($estadoProducto) ?>" <?= $estadoActual === $estadoProducto ? 'selected' : '' ?>>
                        <?= htmlspecialchars($estadoProducto) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="full">Descripción
            <textarea name="descripcion" required rows="5"><?= htmlspecialchars($_POST['descripcion'] ?? $producto['descripcion']) ?></textarea>
        </label>
        <button class="btn primary full" type="submit">Guardar cambios</button>
    </form>
</section>
