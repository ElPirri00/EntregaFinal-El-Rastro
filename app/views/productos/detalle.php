<section class="detail card">
    <div class="detail-media">
        <h1>Ficha del producto</h1>
        <?php $imgs = !empty($imagenes) ? $imagenes : [['url'=>'assets/img/producto-placeholder.svg']]; ?>
        <div class="carousel" data-carousel>
            <button class="carousel-btn prev" type="button" data-carousel-prev aria-label="Imagen anterior">‹</button>
            <img class="carousel-main" data-carousel-main src="<?= htmlspecialchars(image_url($imgs[0]['url'])) ?>" alt="<?= htmlspecialchars($producto['titulo']) ?>">
            <button class="carousel-btn next" type="button" data-carousel-next aria-label="Imagen siguiente">›</button>
            <div class="carousel-thumbs" role="list">
                <?php foreach ($imgs as $i => $img): ?>
                    <button type="button" class="thumb <?= $i === 0 ? 'selected' : '' ?>" data-carousel-thumb data-src="<?= htmlspecialchars(image_url($img['url'])) ?>" aria-label="Ver imagen <?= $i + 1 ?>">
                        <img src="<?= htmlspecialchars(image_url($img['url'])) ?>" alt="Miniatura <?= $i + 1 ?>">
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="detail-title">
            <h2><?= htmlspecialchars($producto['titulo']) ?></h2>
            <strong><?= number_format((float)$producto['precio'], 2, ',', '.') ?> €</strong>
        </div>
        <div class="product-meta">
            <span><?= htmlspecialchars($producto['categoria'] ?? 'Otros') ?></span>
            <span><?= htmlspecialchars($producto['estado_producto'] ?? 'Usado') ?></span>
        </div>
        <p><?= nl2br(htmlspecialchars($producto['descripcion'])) ?></p>
    </div>

    <aside class="seller-box">
        <h2>Información del vendedor</h2>
        <dl>
            <dt>Nombre</dt><dd><?= htmlspecialchars($producto['vendedor']) ?></dd>
            <dt>Valoración</dt>
            <dd>
                <?php if (!empty($producto['valoracion_total'])): ?>
                    <span class="rating-summary">
                        <strong><?= htmlspecialchars((string)$producto['valoracion_media']) ?>/5</strong>
                        · <?= (int)$producto['valoracion_total'] ?> opiniones
                    </span>
                <?php else: ?>
                    Sin valoraciones todavía
                <?php endif; ?>
            </dd>
            <dt>Ciudad / dirección</dt><dd><?= htmlspecialchars($producto['direccion']) ?></dd>
            <dt>Categoría</dt><dd><?= htmlspecialchars($producto['categoria'] ?? 'Otros') ?></dd>
            <dt>Estado del producto</dt><dd><?= htmlspecialchars($producto['estado_producto'] ?? 'Usado') ?></dd>
            <dt>Disponibilidad</dt><dd><?= htmlspecialchars($producto['estado']) ?></dd>
        </dl>

        <button class="btn secondary" type="button" data-toggle-opinions aria-expanded="false" aria-controls="sellerOpinions">
            Ver opiniones
        </button>

        <section id="sellerOpinions" class="seller-opinions" data-opinions-panel hidden>
            <h3>Últimas opiniones</h3>
            <?php if (empty($valoracionesVendedor)): ?>
                <p class="muted">Este vendedor todavía no tiene opiniones.</p>
            <?php else: ?>
                <?php foreach ($valoracionesVendedor as $valoracion): ?>
                    <article class="opinion-item">
                        <div class="opinion-stars" aria-label="<?= (int)$valoracion['puntuacion'] ?> de 5 estrellas">
                            <?= str_repeat('★', (int)$valoracion['puntuacion']) ?><?= str_repeat('☆', 5 - (int)$valoracion['puntuacion']) ?>
                        </div>
                        <?php if (trim((string)$valoracion['comentario']) !== ''): ?>
                            <p>“<?= htmlspecialchars($valoracion['comentario']) ?>”</p>
                        <?php else: ?>
                            <p class="muted">Sin comentario escrito.</p>
                        <?php endif; ?>
                        <small>
                            Por <?= htmlspecialchars($valoracion['autor']) ?>
                            <?php if (!empty($valoracion['producto'])): ?>
                                · Compra: <?= htmlspecialchars($valoracion['producto']) ?>
                            <?php endif; ?>
                            · <?= date('d/m/Y', strtotime($valoracion['fecha'])) ?>
                        </small>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <?php if ($producto['estado'] === 'disponible'): ?>
            <?php if (!empty($_SESSION['usuario']) && (int)$_SESSION['usuario']['id_usuario'] !== (int)$producto['id_usuario']): ?>
                <a class="btn secondary" href="<?= app_url('mensaje/contactar/' . (int)$producto['id_producto']) ?>">Contactar vendedor</a>
            <?php elseif (empty($_SESSION['usuario'])): ?>
                <a class="btn secondary" href="<?= app_url('auth/login') ?>">Inicia sesión para contactar</a>
            <?php endif; ?>
            <a class="btn primary" href="<?= app_url('producto/comprar/' . (int)$producto['id_producto']) ?>">Comprar</a>
        <?php else: ?>
            <button class="btn disabled" disabled>Producto vendido</button>
        <?php endif; ?>
    </aside>
</section>
