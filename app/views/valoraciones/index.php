<section class="card profile">
    <h1>Mis compras y valoraciones</h1>
    <p class="muted">Puedes valorar al vendedor una sola vez después de completar una compra.</p>

    <div class="profile-products">
        <?php foreach ($compras as $compra): ?>
            <article class="profile-product rating-row">
                <div>
                    <strong><?= htmlspecialchars($compra['titulo']) ?></strong>
                    <span>Vendedor: <?= htmlspecialchars($compra['vendedor']) ?></span>
                    <span>Compra: <?= htmlspecialchars($compra['fecha']) ?> · <?= number_format((float)$compra['precio'], 2, ',', '.') ?> €</span>
                </div>
                <div class="row-actions">
                    <?php if (empty($compra['id_valoracion'])): ?>
                        <a class="btn primary small" href="<?= app_url('valoracion/crear/' . (int)$compra['id_compra']) ?>">Valorar vendedor</a>
                    <?php else: ?>
                        <span class="badge success">Valorado: <?= (int)$compra['puntuacion'] ?>/5</span>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
        <?php if (empty($compras)): ?><p>No tienes compras todavía.</p><?php endif; ?>
    </div>
</section>
