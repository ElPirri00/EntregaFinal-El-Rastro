<section class="admin-simple">
    <h1>Panel de administración</h1>
    <p class="muted">Panel básico para controlar productos y usuarios.</p>

    <h2>Productos</h2>
    <div class="table-wrap">
        <table class="simple-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Vendedor</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $p): ?>
                    <tr>
                        <td><?= (int)$p['id_producto'] ?></td>
                        <td><?= htmlspecialchars($p['titulo']) ?></td>
                        <td><?= htmlspecialchars($p['vendedor']) ?></td>
                        <td><?= number_format((float)$p['precio'], 2) ?> €</td>
                        <td><?= htmlspecialchars($p['estado']) ?></td>
                        <td>
                            <?php if ($p['estado'] === 'eliminado'): ?>
                                <a class="btn tiny" href="<?= app_url('admin/activarProducto/' . (int)$p['id_producto']) ?>">Activar</a>
                            <?php else: ?>
                                <a class="btn tiny danger" href="<?= app_url('admin/ocultarProducto/' . (int)$p['id_producto']) ?>">Ocultar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h2>Usuarios</h2>
    <div class="table-wrap">
        <table class="simple-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= (int)$u['id_usuario'] ?></td>
                        <td><?= htmlspecialchars($u['nombre']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['tipo']) ?></td>
                        <td><?= ((int)$u['activo'] === 1) ? 'Activo' : 'Bloqueado' ?></td>
                        <td>
                            <?php if ((int)$u['id_usuario'] === (int)$_SESSION['usuario']['id_usuario']): ?>
                                <span class="muted">Tu cuenta</span>
                            <?php elseif ((int)$u['activo'] === 1): ?>
                                <a class="btn tiny danger" href="<?= app_url('admin/bloquearUsuario/' . (int)$u['id_usuario']) ?>">Bloquear</a>
                            <?php else: ?>
                                <a class="btn tiny" href="<?= app_url('admin/activarUsuario/' . (int)$u['id_usuario']) ?>">Activar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
