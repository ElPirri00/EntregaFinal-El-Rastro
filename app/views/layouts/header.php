<?php $pageTitle = $titulo ?? APP_NAME; ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?> | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= asset_url('assets/css/estilo.css') ?>">
</head>
<body>
<header class="navbar">
    <a href="<?= app_url('producto/index') ?>" class="brand">
        <img src="<?= asset_url('assets/img/logo.png') ?>" alt="Logo El Rastro">
        <span>El Rastro</span>
    </a>
    <form class="search" action="<?= app_url() ?>" method="get">
        <input type="hidden" name="url" value="producto/index">
        <input type="search" name="q" placeholder="Busca en el mercado" value="<?= htmlspecialchars($q ?? '') ?>">
        <button type="submit" aria-label="Buscar">🔍</button>
    </form>
    <nav class="nav-actions">
        <a class="nav-link active" href="<?= app_url('producto/index') ?>">Inicio</a>
        <a class="nav-link" href="<?= app_url('producto/vender') ?>">Vender</a>
        <?php if (!empty($_SESSION['usuario'])): ?>
            <a class="nav-link" href="<?= app_url('mensaje/index') ?>">Mensajes</a>
            <a class="nav-link" href="<?= app_url('valoracion/index') ?>">Valoraciones</a>
            <?php if (($_SESSION['usuario']['tipo'] ?? '') === 'administrador'): ?>
                <a class="nav-link" href="<?= app_url('admin/index') ?>">Admin</a>
            <?php endif; ?>
            <a class="nav-link" href="<?= app_url('usuario/perfil') ?>">Mi perfil</a>
            <a class="nav-link outline" href="<?= app_url('auth/logout') ?>">Salir</a>
        <?php else: ?>
            <a class="nav-link outline" href="<?= app_url('auth/login') ?>">Iniciar sesión</a>
        <?php endif; ?>
    </nav>
</header>
<main class="page">
    <?php if (!empty($_SESSION['exito'])): ?>
        <div class="alert success"><?= htmlspecialchars($_SESSION['exito']); unset($_SESSION['exito']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>
