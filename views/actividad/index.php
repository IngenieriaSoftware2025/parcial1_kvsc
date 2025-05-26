<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">¡Bienvenido Carlos a tu Aplicación de Gestión de Puntualidad!</h5>
                    <h4 class="text-center mb-2 text-primary">REGISTRO DE ACTIVIDADES</h4>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormActividades">
                        <input type="hidden" id="act_id" name="act_id">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-8">
                                <label for="act_nombre" class="form-label">NOMBRE DE LA ACTIVIDAD</label>
                                <input type="text" class="form-control" id="act_nombre" name="act_nombre" placeholder="Ej: Clase de Matemáticas, Reunión de trabajo, etc.">
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-8">
                                <label for="act_descripcion" class="form-label">DESCRIPCIÓN DE LA ACTIVIDAD</label>
                                <textarea class="form-control" id="act_descripcion" name="act_descripcion" rows="3" placeholder="Describe brevemente la actividad..."></textarea>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-4">
                                <label for="act_fecha_esperada" class="form-label">FECHA ESPERADA</label>
                                <input type="date" class="form-control" id="act_fecha_esperada" name="act_fecha_esperada">
                            </div>
                            <div class="col-lg-4">
                                <label for="act_hora_esperada" class="form-label">HORA ESPERADA</label>
                                <input type="time" class="form-control" id="act_hora_esperada" name="act_hora_esperada">
                            </div>
                        </div>

                        <div class="row justify-content-center mt-5">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="fas fa-save"></i> Guardar Actividad
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificar">
                                    <i class="fas fa-edit"></i> Modificar
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-secondary" type="reset" id="BtnLimpiar">
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
                <h3 class="text-center text-success">MIS ACTIVIDADES PROGRAMADAS</h3>
                <p class="text-center text-muted">Carlos, aquí puedes ver todas tus actividades y horarios esperados</p>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableActividades">
                        
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="position-fixed bottom-0 end-0 m-4">
    <button class="btn btn-primary rounded-circle" id="BtnActividadesHoy" style="width: 60px; height: 60px;" title="Ver actividades de hoy">
        <i class="fas fa-calendar-day"></i>
    </button>
</div>


    <script src="<?= asset('build/js/actividad/index.js') ?>"></script>