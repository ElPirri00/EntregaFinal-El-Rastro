<section class="hero">
    <span>Mercado de segunda mano</span>
    <h1>Compra y vende rápido en tu ciudad</h1>
</section>

<section class="filters card">
    <form action="<?= app_url() ?>" method="get" class="filter-grid">
        <input type="hidden" name="url" value="producto/index">
        <input type="text" name="q" placeholder="Categoría, producto o palabra clave" value="<?= htmlspecialchars($q ?? '') ?>">
        <button class="btn primary" type="submit">Buscar</button>
    </form>
</section>

<section class="product-grid" aria-label="Listado de productos">
    <?php if (empty($productos)): ?>
        <div class="card empty">No hay productos disponibles con esos criterios.</div>
    <?php endif; ?>
    <?php foreach ($productos as $producto): ?>
        <article class="product-card">
            <a href="<?= app_url('producto/detalle/' . (int)$producto['id_producto']) ?>">
                <img src="<?= htmlspecialchars(image_url($producto['imagen'] ?? null)) ?>" alt="<?= htmlspecialchars($producto['titulo']) ?>">
                <h2><?= htmlspecialchars($producto['titulo']) ?></h2>
                <p class="seller">Vendedor: <?= htmlspecialchars($producto['vendedor']) ?></p>
                <strong><?= number_format((float)$producto['precio'], 2, ',', '.') ?> €</strong>
            </a>
        </article>
    <?php endforeach; ?>
</section>
