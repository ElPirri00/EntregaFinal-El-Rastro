<?php
    $pollRuta = null;
    if ($chat) {
        $pollRuta = 'mensaje/poll/' . (int)$chat['id_contacto'];
        if (!empty($chat['id_producto'])) $pollRuta .= '/' . (int)$chat['id_producto'];
    }
?>
<section class="card messages" data-messages-page data-poll-url="<?= $pollRuta ? app_url($pollRuta) : '' ?>" data-current-user="<?= (int)($_SESSION['usuario']['id_usuario'] ?? 0) ?>" data-list-poll-url="<?= app_url('mensaje/poll') ?>">
    <aside class="chat-list" data-chat-list>
        <h1>Chats recientes</h1>
        <?php if (empty($conversaciones)): ?>
            <p>No tienes conversaciones todavía.</p>
        <?php endif; ?>
        <?php foreach ($conversaciones as $c): ?>
            <?php
                $ruta = 'mensaje/ver/' . (int)$c['id_contacto'];
                if (!empty($c['id_producto'])) $ruta .= '/' . (int)$c['id_producto'];
                $activo = $chat && (int)$chat['id_contacto'] === (int)$c['id_contacto'] && (int)($chat['id_producto'] ?? 0) === (int)($c['id_producto'] ?? 0);
            ?>
            <a class="chat-preview <?= $activo ? 'active' : '' ?>" href="<?= app_url($ruta) ?>">
                <strong><?= htmlspecialchars($c['contacto']) ?></strong>
                <?php if (!empty($c['producto'])): ?>
                    <span><?= htmlspecialchars($c['producto']) ?></span>
                <?php endif; ?>
                <small><?= htmlspecialchars($c['ultimo_mensaje']) ?></small>
            </a>
        <?php endforeach; ?>
    </aside>

    <div class="chat-box" data-chat-box>
        <?php if ($chat): ?>
            <h2>Chat con <?= htmlspecialchars($chat['contacto']) ?></h2>
            <?php if (!empty($chat['producto'])): ?>
                <p class="chat-product">Producto: <strong><?= htmlspecialchars($chat['producto']) ?></strong></p>
            <?php endif; ?>
            <div class="message-list thread" data-thread>
                <?php foreach ($mensajes as $m): ?>
                    <?php $propio = (int)$m['id_emisor'] === (int)$_SESSION['usuario']['id_usuario']; ?>
                    <article class="message-item <?= $propio ? 'own' : '' ?>">
                        <strong><?= htmlspecialchars($m['emisor']) ?></strong>
                        <p><?= htmlspecialchars($m['contenido']) ?></p>
                        <small><?= htmlspecialchars($m['fecha']) ?></small>
                    </article>
                <?php endforeach; ?>
                <?php if (empty($mensajes)): ?>
                    <p>Aún no hay mensajes. Escribe el primero para contactar con el vendedor.</p>
                <?php endif; ?>
            </div>
            <p class="chat-status" data-chat-status>Actualización automática activada.</p>
            <form method="post" action="<?= app_url('mensaje/enviar') ?>" class="send-message">
                <input type="hidden" name="id_contacto" value="<?= (int)$chat['id_contacto'] ?>">
                <input type="hidden" name="id_producto" value="<?= (int)($chat['id_producto'] ?? 0) ?>">
                <input type="text" name="contenido" placeholder="Escribe un mensaje" required maxlength="500">
                <button class="btn primary" type="submit">➜</button>
            </form>
        <?php else: ?>
            <h2>Selecciona una conversación</h2>
            <p>Desde la ficha de un producto puedes pulsar <strong>Contactar vendedor</strong> para abrir un chat automáticamente.</p>
        <?php endif; ?>
    </div>
</section>
