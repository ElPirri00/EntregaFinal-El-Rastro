<section class="card form-card wide">
    <h1>Subir un producto</h1>
    <form method="post" enctype="multipart/form-data" class="form-grid">
        <label>Título
            <input type="text" name="titulo" required maxlength="150" value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>">
        </label>
        <label>Precio
            <input type="number" name="precio" step="0.01" min="0.01" required value="<?= htmlspecialchars($_POST['precio'] ?? '') ?>">
        </label>
        <label>Categoría
            <select name="categoria" required>
                <option value="">Selecciona una categoría</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= htmlspecialchars($categoria) ?>" <?= (($_POST['categoria'] ?? '') === $categoria) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($categoria) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Estado del producto
            <select name="estado_producto" required>
                <option value="">Selecciona el estado</option>
                <?php foreach ($estadosProducto as $estadoProducto): ?>
                    <option value="<?= htmlspecialchars($estadoProducto) ?>" <?= (($_POST['estado_producto'] ?? '') === $estadoProducto) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($estadoProducto) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="full">Descripción
            <textarea name="descripcion" required rows="5"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
        </label>
        <label class="full">Fotos del producto
            <input type="file" name="imagenes[]" multiple accept="image/*">
        </label>
        <button class="btn primary full" type="submit">Publicar</button>
    </form>
</section>
