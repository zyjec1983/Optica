<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Views/pacientes/index.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Vista: presentación HTML responsiva (Bootstrap 5).
 * ==================================================================
 */

 /** @var array $pacientes */ /** @var string $busqueda */ ?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <h2 class="fw-bold"><i class="bi bi-people-fill me-2"></i>Pacientes</h2>
    <a href="<?= e(app_url('/pacientes/nuevo')) ?>" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Nuevo
    </a>
</div>

<div class="card card-custom p-4">
    <form method="get" action="<?= e(app_url('/pacientes')) ?>" class="row g-2 mb-3" id="busquedaForm">
        <div class="col-md-9">
            <div class="position-relative">
                <input type="text" name="q" id="busquedaPaciente" class="form-control"
                       placeholder="Buscar por nombre, cédula o teléfono..." value="<?= e($busqueda) ?>"
                       autocomplete="off" minlength="2">
                <div class="list-group position-absolute w-100 shadow" id="coincidenciasLista" style="z-index:1030; display:none;"></div>
            </div>
            <div class="form-text" id="coincidenciasInfo"></div>
        </div>
        <div class="col-md-3 d-grid">
            <button class="btn btn-secondary"><i class="bi bi-search"></i> Filtrar</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>Paciente</th>
                <th>Cédula</th>
                <th>Edad</th>
                <th>Teléfono</th>
                <th>Tutor</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($pacientes) > 0): ?>
                <?php foreach ($pacientes as $p):
                    $nombre = trim($p['primer_nombre'] . ' ' . $p['segundo_nombre'] . ' ' . $p['apellido_paterno'] . ' ' . $p['apellido_materno']);
                    $edad = format_edad($p['fecha_nacimiento']);
                    $mayor = calcular_edad($p['fecha_nacimiento'])['mayor'];
                    ?>
                    <tr>
                        <td><strong><?= e($nombre) ?></strong>
                            <small class="text-muted d-block"><?= e($p['email'] ?: '') ?></small>
                        </td>
                        <td><?= e($p['identificacion']) ?></td>
                        <td>
                            <span class="badge <?= $mayor ? 'bg-secondary' : 'bg-danger' ?>"
                                  title="Nacido: <?= e($p['fecha_nacimiento']) ?>">
                                <?= e($edad) ?>
                            </span>
                        </td>
                        <td><?= e($p['telefono'] ?: '—') ?></td>
                        <td>
                            <?php if (!empty($p['rep_nombres'])): ?>
                                <span class="badge bg-warning text-dark"><?= e($p['rep_nombres']) ?> (<?= e($p['rep_parentesco']) ?>)</span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('/pacientes/editar/' . $p['id'])) ?>" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a class="btn btn-sm btn-outline-info" href="<?= e(app_url('/examenes?paciente_id=' . $p['id'])) ?>" title="Historial de exámenes">
                                <i class="bi bi-clock-history"></i>
                            </a>
                            <form method="post" action="<?= e(app_url('/pacientes/eliminar/' . $p['id'])) ?>" class="d-inline"
                                  data-confirm="¿Eliminar al paciente <?= e($nombre) ?>? Se marcará como eliminado; la información no se borra del sistema.">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-muted text-center py-4">
                    No se encontraron pacientes<?= $busqueda !== '' ? ' con "' . e($busqueda) . '"' : '' ?>.
                </td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    (function () {
        const input = document.getElementById('busquedaPaciente');
        const lista = document.getElementById('coincidenciasLista');
        const info = document.getElementById('coincidenciasInfo');
        const form = document.getElementById('busquedaForm');
        const buscarUrl = <?= json_encode(app_url('/pacientes/buscar')) ?>;
        let timeout = null;

        function nombre(p) {
            return [p.primer_nombre, p.segundo_nombre, p.apellido_paterno, p.apellido_materno]
                .filter(Boolean).join(' ').trim();
        }

        function ocultar() {
            lista.style.display = 'none';
            lista.innerHTML = '';
        }

        input.addEventListener('input', function () {
            clearTimeout(timeout);
            const q = input.value.trim();
            if (q.length < 2) { ocultar(); info.textContent = ''; return; }
            timeout = setTimeout(function () {
                fetch(buscarUrl + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        ocultar();
                        if (data.length === 0) {
                            info.textContent = 'Sin coincidencias para "' + q + '".';
                            return;
                        }
                        info.textContent = data.length + (data.length === 1 ? ' coincidencia encontrada' : ' coincidencias encontradas');
                        data.forEach(function (p) {
                            const item = document.createElement('button');
                            item.type = 'button';
                            item.className = 'list-group-item list-group-item-action text-start';
                            const nombreEl = document.createElement('div');
                            nombreEl.className = 'fw-semibold';
                            nombreEl.textContent = nombre(p);
                            const detalle = document.createElement('small');
                            detalle.className = 'text-muted';
                            detalle.textContent = (p.identificacion || '') + (p.telefono ? ' · ' + p.telefono : '');
                            item.appendChild(nombreEl);
                            item.appendChild(detalle);
                            item.addEventListener('click', function () {
                                input.value = nombre(p) + ' ' + (p.identificacion || '');
                                form.submit();
                            });
                            lista.appendChild(item);
                        });
                        lista.style.display = 'block';
                    })
                    .catch(function () { ocultar(); });
            }, 250);
        });

        document.addEventListener('click', function (e) {
            if (!lista.contains(e.target) && e.target !== input) ocultar();
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') ocultar();
        });
    })();
</script>