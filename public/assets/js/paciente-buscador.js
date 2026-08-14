/**
 * ==================================================================
 *  ARCHIVO: public/assets/js/paciente-buscador.js
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Buscador de pacientes con coincidencias en vivo
 *               (por cédula, nombres o apellidos) usando el endpoint
 *               GET /pacientes/buscar?q=. Sin jQuery, sin innerHTML
 *               (textContent: sin riesgo de XSS).
 * ==================================================================
 */
window.PacienteBuscador = (function () {
    'use strict';

    function nombre(p) {
        return [p.primer_nombre, p.segundo_nombre, p.apellido_paterno, p.apellido_materno]
            .filter(Boolean).join(' ').trim();
    }

    function byId(el, fallback) {
        return typeof el === 'string' ? document.querySelector(el) : (el || fallback);
    }

    /**
     * Config:
     *   input        (obligatorio) selector o elemento del campo de texto.
     *   hidden       (opcional) selector o elemento <input hidden> que recibe el id.
     *   lista        (opcional) contenedor del listado (si no, se crea junto al input).
     *   info         (opcional) elemento donde se muestra "X coincidencias".
     *   url          URL base del endpoint (por defecto /pacientes/buscar).
     *   minLength    mínimo de caracteres para buscar (por defecto 2).
     *   onSelect     función(p) al elegir una coincidencia.
     *   enterSeleccionaPrimero  Enter elige el primer resultado (por defecto true).
     */
    function init(config) {
        var input = byId(config.input);
        if (!input) return;

        var hidden = byId(config.hidden);
        var info = byId(config.info);
        var lista = byId(config.lista);
        var url = config.url || '/pacientes/buscar';
        var minLength = config.minLength || 2;
        var onSelect = config.onSelect || null;
        var enterSelectsFirst = config.enterSeleccionaPrimero !== false;
        var timeout = null;
        var primerItem = null;
        var seleccionActual = '';

        if (!lista) {
            lista = document.createElement('div');
            lista.className = 'list-group position-absolute w-100 shadow';
            lista.style.zIndex = '1030';
            lista.style.display = 'none';
            input.parentNode.appendChild(lista);
        }

        function ocultar() {
            lista.style.display = 'none';
            lista.innerHTML = '';
            primerItem = null;
        }

        function elegir(p) {
            ocultar();
            seleccionActual = nombre(p) + ' ' + (p.identificacion || '');
            if (hidden) hidden.value = String(p.id);
            if (onSelect) {
                onSelect(p);
            } else {
                input.value = seleccionActual;
            }
        }

        function mostrar(data, q) {
            ocultar();
            if (info) {
                info.textContent = data.length === 0
                    ? 'Sin coincidencias para "' + q + '".'
                    : data.length + (data.length === 1 ? ' coincidencia encontrada' : ' coincidencias encontradas');
            }
            data.forEach(function (p) {
                var item = document.createElement('button');
                item.type = 'button';
                item.className = 'list-group-item list-group-item-action text-start';

                var n = document.createElement('div');
                n.className = 'fw-semibold';
                n.textContent = nombre(p);

                var det = document.createElement('small');
                det.className = 'text-muted';
                det.textContent = (p.identificacion || '') + (p.telefono ? ' · ' + p.telefono : '');

                item.appendChild(n);
                item.appendChild(det);
                item.addEventListener('click', function () { elegir(p); });
                lista.appendChild(item);
                if (!primerItem) primerItem = item;
            });
            if (data.length > 0) lista.style.display = 'block';
        }

        input.addEventListener('input', function () {
            clearTimeout(timeout);
            if (hidden) hidden.value = ''; // si el usuario modifica el texto, se anula la selección previa
            var q = input.value.trim();
            if (q.length < minLength) { ocultar(); if (info) info.textContent = ''; return; }
            timeout = setTimeout(function () {
                fetch(url + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } })
                    .then(function (r) { return r.json(); })
                    .then(function (data) { mostrar(data, q); })
                    .catch(function () { ocultar(); });
            }, 250);
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') ocultar();
            if (e.key === 'Enter' && enterSelectsFirst && primerItem) {
                e.preventDefault();
                primerItem.click();
            }
        });

        document.addEventListener('click', function (e) {
            if (!lista.contains(e.target) && e.target !== input) ocultar();
        });
    }

    return { init: init };
})();
