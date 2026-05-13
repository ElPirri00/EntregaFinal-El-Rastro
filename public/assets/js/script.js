document.addEventListener('DOMContentLoaded', function () {
    prepararCarrusel();
    prepararPago();
    prepararMensajes();
    prepararOpiniones();
});

function prepararCarrusel() {
    var carruseles = document.querySelectorAll('[data-carousel]');

    carruseles.forEach(function (carrusel) {
        var imagenPrincipal = carrusel.querySelector('[data-carousel-main]');
        var miniaturas = carrusel.querySelectorAll('[data-carousel-thumb]');
        var botonAnterior = carrusel.querySelector('[data-carousel-prev]');
        var botonSiguiente = carrusel.querySelector('[data-carousel-next]');
        var posicion = 0;

        function mostrarImagen(nuevaPosicion) {
            if (miniaturas.length == 0) return;

            if (nuevaPosicion < 0) nuevaPosicion = miniaturas.length - 1;
            if (nuevaPosicion >= miniaturas.length) nuevaPosicion = 0;

            posicion = nuevaPosicion;
            imagenPrincipal.src = miniaturas[posicion].dataset.src;

            miniaturas.forEach(function (miniatura, i) {
                miniatura.classList.toggle('selected', i == posicion);
            });
        }

        miniaturas.forEach(function (miniatura, i) {
            miniatura.addEventListener('click', function () {
                mostrarImagen(i);
            });
        });

        if (botonAnterior) {
            botonAnterior.addEventListener('click', function () {
                mostrarImagen(posicion - 1);
            });
        }

        if (botonSiguiente) {
            botonSiguiente.addEventListener('click', function () {
                mostrarImagen(posicion + 1);
            });
        }
    });
}

function prepararPago() {
    var metodo = document.getElementById('paymentMethod');
    var grupos = document.querySelectorAll('[data-payment-fields]');

    if (!metodo) return;

    function actualizarCamposPago() {
        grupos.forEach(function (grupo) {
            var activo = grupo.dataset.paymentFields == metodo.value;
            grupo.hidden = !activo;

            grupo.querySelectorAll('input').forEach(function (input) {
                input.required = activo;
            });
        });
    }

    metodo.addEventListener('change', actualizarCamposPago);
    actualizarCamposPago();
}

function prepararMensajes() {
    var pagina = document.querySelector('[data-messages-page]');
    if (!pagina) return;

    var urlLista = pagina.dataset.listPollUrl;
    var urlChat = pagina.dataset.pollUrl;
    var usuarioActual = Number(pagina.dataset.currentUser || 0);
    var contenedorChats = pagina.querySelector('[data-chat-list]');
    var contenedorMensajes = pagina.querySelector('[data-thread]');
    var estado = pagina.querySelector('[data-chat-status]');

    function limpiar(texto) {
        var div = document.createElement('div');
        div.textContent = texto == null ? '' : texto;
        return div.innerHTML;
    }

    function rutaChat(chat) {
        var ruta = 'mensaje/ver/' + Number(chat.id_contacto);
        if (chat.id_producto) ruta += '/' + Number(chat.id_producto);
        return window.location.pathname + '?url=' + ruta;
    }

    function pintarChats(chats) {
        if (!contenedorChats) return;

        var html = '<h1>Chats recientes</h1>';

        if (chats.length == 0) {
            contenedorChats.innerHTML = html + '<p>No tienes conversaciones todavía.</p>';
            return;
        }

        chats.forEach(function (chat) {
            html += '<a class="chat-preview" href="' + rutaChat(chat) + '">';
            html += '<strong>' + limpiar(chat.contacto) + '</strong>';
            if (chat.producto) html += '<span>' + limpiar(chat.producto) + '</span>';
            html += '<small>' + limpiar(chat.ultimo_mensaje) + '</small>';
            html += '</a>';
        });

        contenedorChats.innerHTML = html;
    }

    function pintarMensajes(mensajes) {
        if (!contenedorMensajes) return;

        if (mensajes.length == 0) {
            contenedorMensajes.innerHTML = '<p>Aún no hay mensajes. Escribe el primero para contactar con el vendedor.</p>';
            return;
        }

        var bajarAlFinal = contenedorMensajes.scrollTop + contenedorMensajes.clientHeight >= contenedorMensajes.scrollHeight - 60;
        var html = '';

        mensajes.forEach(function (m) {
            var propio = Number(m.id_emisor) == usuarioActual;
            html += '<article class="message-item ' + (propio ? 'own' : '') + '">';
            html += '<strong>' + limpiar(m.emisor) + '</strong>';
            html += '<p>' + limpiar(m.contenido) + '</p>';
            html += '<small>' + limpiar(m.fecha) + '</small>';
            html += '</article>';
        });

        contenedorMensajes.innerHTML = html;
        if (bajarAlFinal) contenedorMensajes.scrollTop = contenedorMensajes.scrollHeight;
    }

    function actualizarMensajes() {
        fetch(urlChat || urlLista, { cache: 'no-store' })
            .then(function (respuesta) { return respuesta.json(); })
            .then(function (datos) {
                if (!datos.ok) return;
                pintarChats(datos.conversaciones || []);
                pintarMensajes(datos.mensajes || []);
                if (estado) estado.textContent = 'Mensajes actualizados automáticamente.';
            })
            .catch(function () {
                if (estado) estado.textContent = 'No se pudo actualizar. Se reintentará en unos segundos.';
            });
    }

    actualizarMensajes();
    setInterval(actualizarMensajes, 5000);
}

function prepararOpiniones() {
    var boton = document.querySelector('[data-toggle-opinions]');
    var panel = document.querySelector('[data-opinions-panel]');

    if (!boton || !panel) return;

    boton.addEventListener('click', function () {
        panel.hidden = !panel.hidden;
        boton.textContent = panel.hidden ? 'Ver opiniones' : 'Ocultar opiniones';
        boton.setAttribute('aria-expanded', panel.hidden ? 'false' : 'true');
    });
}
