<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">¡Bienvenido Carlos a tu Control de Puntualidad!</h5>
                    <h4 class="text-center mb-2 text-primary">REGISTRO DE ASISTENCIA</h4>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">
                    <form id="FormAsistencias">
                        <input type="hidden" id="asi_id" name="asi_id">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-8">
                                <label for="asi_actividad" class="form-label">SELECCIONA TU ACTIVIDAD</label>
                                <select name="asi_actividad" class="form-select" id="asi_actividad" required>
                                    <option value="" class="text-center"> -- ELIGE LA ACTIVIDAD A LA QUE LLEGASTE -- </option>
                                    <?php foreach ($actividades as $a): ?>
                                        <option value="<?= $a->act_id ?>">
                                            <?= $a->act_nombre ?> - Esperada: <?= date('H:i', strtotime($a->act_hora_esperada)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-5">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnRegistrar">
                                    <i class="fas fa-clock"></i> Registrar Mi Llegada
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-secondary" type="button" id="BtnLimpiar">
                                    <i class="fas fa-broom"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #28a745;">
            <div class="card-body p-3">
                <h3 class="text-center text-success">MIS REGISTROS DE PUNTUALIDAD</h3>
                <p class="text-center text-muted">Carlos, aquí puedes ver tu historial de asistencias</p>

                <!-- SECCIÓN DE FILTROS -->
                <div class="card mb-4" style="background-color: #f8f9fa;">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-filter"></i> Filtros de Búsqueda
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="filtro_actividad" class="form-label">Filtrar por Actividad:</label>
                                <select name="filtro_actividad" class="form-select" id="filtro_actividad">
                                    <option value="" class="text-center"> -- Todas las Actividades -- </option>
                                    <?php foreach($actividades as $a): ?>
                                        <option value="<?= $a->act_id ?>">
                                            <?= $a->act_nombre ?> - Esperada: <?= date('H:i', strtotime($a->act_hora_esperada)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filtro_fecha" class="form-label">Filtrar por Fecha:</label>
                                <input type="date" class="form-control" id="filtro_fecha">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button class="btn btn-primary me-2" id="btn_filtrar">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <button class="btn btn-outline-secondary" id="btn_limpiar_filtros">
                                    <i class="fas fa-eraser"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABLA DE ASISTENCIAS -->
                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableAsistencias">
                        <thead class="table-dark">
                            <tr>
                                <th colspan="6" class="text-center">
                                    <i class="fas fa-spinner fa-spin"></i> Cargando asistencias...
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script externo de asistencia - CORREGIDO -->
<script src="<?= asset('build/js/asistencia/index.js.js') ?>"></script>