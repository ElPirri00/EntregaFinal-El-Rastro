<section class="modal-card card checkout-card">
    <h1>Confirmar compra</h1>
    <p>Vas a comprar <strong><?= htmlspecialchars($producto['titulo']) ?></strong> por <strong><?= number_format((float)$producto['precio'], 2, ',', '.') ?> €</strong>.</p>

    <form method="post" class="form-grid" id="paymentForm">
        <label class="full">Método de pago
            <select name="metodo_pago" id="paymentMethod" required>
                <?php $metodoActual = $_POST['metodo_pago'] ?? ($_SESSION['usuario']['metodo_pago'] ?? 'Tarjeta'); ?>
                <?php foreach (['Tarjeta', 'PayPal', 'Bizum', 'Efectivo'] as $metodo): ?>
                    <option value="<?= htmlspecialchars($metodo) ?>" <?= $metodoActual === $metodo ? 'selected' : '' ?>><?= htmlspecialchars($metodo) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <div class="payment-fields full" data-payment-fields="Tarjeta">
            <label>Titular de la tarjeta
                <input type="text" name="titular_tarjeta" autocomplete="cc-name" value="<?= htmlspecialchars($_POST['titular_tarjeta'] ?? $_SESSION['usuario']['nombre'] ?? '') ?>">
            </label>
            <label>Número de tarjeta
                <input type="text" name="numero_tarjeta" inputmode="numeric" autocomplete="cc-number" placeholder="0000 0000 0000 0000" value="<?= htmlspecialchars($_POST['numero_tarjeta'] ?? '') ?>">
            </label>
            <label>Caducidad
                <input type="text" name="caducidad_tarjeta" autocomplete="cc-exp" placeholder="MM/AA" value="<?= htmlspecialchars($_POST['caducidad_tarjeta'] ?? '') ?>">
            </label>
            <label>CVV
                <input type="password" name="cvv_tarjeta" inputmode="numeric" autocomplete="cc-csc" maxlength="4" placeholder="123">
            </label>
            <p class="muted full">Por seguridad, la aplicación solo guarda el método de pago seleccionado, no los datos de la tarjeta.</p>
        </div>

        <div class="payment-fields full" data-payment-fields="PayPal">
            <label class="full">Email de PayPal
                <input type="email" name="email_paypal" placeholder="correo@ejemplo.com" value="<?= htmlspecialchars($_POST['email_paypal'] ?? '') ?>">
            </label>
        </div>

        <div class="payment-fields full" data-payment-fields="Bizum">
            <label class="full">Teléfono Bizum
                <input type="tel" name="telefono_bizum" inputmode="numeric" maxlength="9" placeholder="600123123" value="<?= htmlspecialchars($_POST['telefono_bizum'] ?? '') ?>">
            </label>
        </div>

        <div class="payment-fields full" data-payment-fields="Efectivo">
            <label class="full">Dirección para quedar
                <input type="text" name="direccion_envio" value="<?= htmlspecialchars($_POST['direccion_envio'] ?? $_SESSION['usuario']['direccion'] ?? '') ?>">
            </label>
        </div>

        <button class="btn primary full" type="submit">Confirmar compra</button>
        <a class="btn secondary full" href="<?= app_url('producto/detalle/' . (int)$producto['id_producto']) ?>">Cancelar</a>
    </form>
</section>
