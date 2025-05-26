   <div class="container">
        
        <div class="row justify-content-center p-3">
            <div class="col-lg-10">
                <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #667eea;">
                    <div class="card-body p-3">
                        <div class="row mb-3">
                            <h5 class="text-center mb-2">¡Hola Carlos! Registra tu Llegada</h5>
                            <h4 class="text-center mb-2 text-primary">CONTROL DE PUNTUALIDAD</h4>
                        </div>

                        <div class="row justify-content-center p-5 shadow-lg">
                            <form id="FormAsistencias">
                                <input type="hidden" id="asi_id" name="asi_id">

                                <div class="row mb-3 justify-content-center">
                                    <div class="col-lg-8">
                                        <label for="asi_actividad" class="form-label">ACTIVIDAD</label>
                                        <select name="asi_actividad" class="form-select" id="asi_actividad">
                                            <option value="" class="text-center"> -- SELECCIONA TU ACTIVIDAD -- </option>
                                            <?php foreach($actividades as $actividad): ?>
                                                <option value="<?= $actividad->act_id ?>" 
                                                        data-hora="<?= $actividad->act_hora_esperada ?>"
                                                        data-fecha="<?= $actividad->act_fecha_esperada ?>">
                                                    <?= $actividad->act_nombre ?> - <?= date('d/m/Y H:i', strtotime($actividad->act_fecha_esperada)) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3 justify-content-center">
                                    <div class="col-lg-4">
                                        <label for="asi_fecha_asistencia" class="form-label">FECHA DE ASISTENCIA</label>
                                        <input type="date" class="form-control" id="asi_fecha_asistencia" name="asi_fecha_asistencia">
                                    </div>
                                    <div class="col-lg-4">
                                        <label for="asi_hora_llegada" class="form-label">HORA DE LLEGADA</label>
                                        <input type="time" class="form-control" id="asi_hora_llegada" name="asi_hora_llegada">
                                    </div>
                                </div>

                                <div class="row mb-3 justify-content-center" id="info_actividad" style="display: none;">
                                    <div class="col-lg-8">
                                        <div class="alert alert-info">
                                            <i class="bi bi-clock me-2"></i>
                                            <strong>Hora esperada:</strong> <span id="hora_esperada_display"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row justify-content-center mt-5">
                                    <div class="col-auto">
                                        <button class="btn btn-success" type="submit" id="BtnRegistrar">
                                            <i class="bi bi-check-circle me-1"></i> Registrar Llegada
                                        </button>
                                    </div>

                                    <div class="col-auto">
                                        <button class="btn btn-warning d-none" type="button" id="BtnModificar">
                                            <i class="bi bi-pencil-square me-1"></i> Modificar
                                        </button>
                                    </div>

                                    <div class="col-auto">
                                        <button class="btn btn-secondary" type="reset" id="BtnLimpiar">
                                            <i class="bi bi-arrow-clockwise me-1"></i> Limpiar
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
                <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #00b894;">
                    <div class="card-body p-3">
                        <h3 class="text-center text-success">MIS REGISTROS DE ASISTENCIA</h3>
                        <p class="text-center text-muted">Carlos, aquí puedes ver tu historial de puntualidad</p>

                        <div class="table-responsive p-2">
                            <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableAsistencias">
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <button class="floating-btn" id="btnReportePuntualidad">
        <i class="bi bi-graph-up"></i>
        Ver Estadísticas
    </button>

    <!-- Modal para estadísticas -->
    <div class="modal fade" id="modalEstadisticas" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-graph-up me-2"></i>Estadísticas de Puntualidad
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="contenidoEstadisticas">
                    <!-- Las estadísticas se cargarán aquí -->
                </div>
            </div>
        </div>
    </div>

    <script src="<?= asset('build/js/asistencia/index.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>