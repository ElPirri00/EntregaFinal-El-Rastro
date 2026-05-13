<section class="card form-card">
    <h1>Valorar vendedor</h1>
    <p>Compra: <strong><?= htmlspecialchars($compra['titulo']) ?></strong></p>
    <p>Vendedor: <strong><?= htmlspecialchars($compra['vendedor']) ?></strong></p>

    <form method="post" class="form-grid">
        <label class="full">Puntuación
            <select name="puntuacion" required>
                <option value="">Selecciona una puntuación</option>
                <option value="5">★★★★★ 5 - Excelente</option>
                <option value="4">★★★★☆ 4 - Muy bien</option>
                <option value="3">★★★☆☆ 3 - Correcto</option>
                <option value="2">★★☆☆☆ 2 - Mejorable</option>
                <option value="1">★☆☆☆☆ 1 - Mala experiencia</option>
            </select>
        </label>
        <label class="full">Comentario opcional
            <textarea name="comentario" rows="4" maxlength="500" placeholder="Describe brevemente tu experiencia con el vendedor."><?= htmlspecialchars($_POST['comentario'] ?? '') ?></textarea>
        </label>
        <button class="btn primary full" type="submit">Guardar valoración</button>
        <a class="btn secondary full" href="<?= app_url('valoracion/index') ?>">Ahora no</a>
    </form>
</section>
