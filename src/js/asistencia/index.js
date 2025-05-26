import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario, Toast } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormAsistencias = document.getElementById('FormAsistencias');
const BtnRegistrar = document.getElementById('BtnRegistrar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const SelectActividad = document.getElementById('asi_actividad');
const InfoActividad = document.getElementById('info_actividad');
const HoraEsperadaDisplay = document.getElementById('hora_esperada_display');
const FechaAsistencia = document.getElementById('asi_fecha_asistencia');
const HoraLlegada = document.getElementById('asi_hora_llegada');
const BtnReportePuntualidad = document.getElementById('btnReportePuntualidad');

// Establecer fecha actual por defecto
const hoy = new Date().toISOString().split('T')[0];
FechaAsistencia.value = hoy;

// Mostrar información de la actividad seleccionada
const MostrarInfoActividad = () => {
    const selectedOption = SelectActividad.options[SelectActividad.selectedIndex];
    
    if (SelectActividad.value && selectedOption.dataset.hora) {
        const horaEsperada = selectedOption.dataset.hora;
        HoraEsperadaDisplay.textContent = horaEsperada;
        InfoActividad.style.display = 'block';
    } else {
        InfoActividad.style.display = 'none';
    }
}

// Establecer hora actual por defecto
const EstablecerHoraActual = () => {
    const ahora = new Date();
    const horaActual = ahora.toTimeString().slice(0, 5);
    HoraLlegada.value = horaActual;
}

const RegistrarAsistencia = async (event) => {
    event.preventDefault();
    BtnRegistrar.disabled = true;

    if (!validarFormulario(FormAsistencias, ['asi_id'])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Carlos, debes completar todos los campos obligatorios",
            showConfirmButton: true,
        });
        BtnRegistrar.disabled = false;
        return;
    }

    const body = new FormData(FormAsistencias);
    const url = '/parcial1_kvsc/public/asistencia/registrarAPI';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        console.log(datos);
        const { codigo, mensaje, puntual, minutos_diferencia } = datos;

        if (codigo == 1) {
            const icono = puntual ? 'success' : 'warning';
            const titulo = puntual ? '¡Excelente Carlos!' : 'Carlos, llegaste tarde';
            
            await Swal.fire({
                position: "center",
                icon: icono,
                title: titulo,
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarAsistencias();
        } else {
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }
    } catch (error) {
        console.log(error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo conectar con el servidor",
            showConfirmButton: true,
        });
    }
    BtnRegistrar.disabled = false;
}

const BuscarAsistencias = async () => {
    const url = '/parcial1_kvsc/public/asistencia/buscarAPI';
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;

        if (codigo == 1) {
            datatable.clear().draw();
            datatable.rows.add(data).draw();
            
            Toast.fire({
                icon: 'success',
                title: mensaje
            });
        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Sin registros",
                text: mensaje,
                showConfirmButton: true,
            });
        }
    } catch (error) {
        console.log(error);
    }
}

const datatable = new DataTable('#TableAsistencias', {
    dom: `
        <"row mt-3 justify-content-between" 
            <"col" l> 
            <"col" B> 
            <"col-3" f>
        >
        t
        <"row mt-3 justify-content-between" 
            <"col-md-3 d-flex align-items-center" i> 
            <"col-md-8 d-flex justify-content-end" p>
        >
    `,
    language: lenguaje,
    data: [],
    order: [[1, 'desc'], [2, 'desc']], // Ordenar por fecha y hora descendente
    columns: [
        {
            title: 'No.',
            data: 'asi_id',
            width: '5%',
            orderable: false,
            render: (data, type, row, meta) => meta.row + 1
        },
        { 
            title: 'Fecha', 
            data: 'asi_fecha_asistencia',
            width: '12%',
            render: (data, type, row) => {
                if (type === 'display') {
                    const fecha = new Date(data);
                    return fecha.toLocaleDateString('es-GT');
                }
                return data;
            }
        },
        { 
            title: 'Actividad', 
            data: 'act_nombre',
            width: '25%'
        },
        { 
            title: 'Hora Esperada', 
            data: 'act_hora_esperada',
            width: '12%'
        },
        { 
            title: 'Hora Llegada', 
            data: 'asi_hora_llegada',
            width: '12%'
        },
        {
            title: 'Diferencia',
            data: 'asi_minutos_diferencia',
            width: '10%',
            render: (data, type, row) => {
                if (data == 0) {
                    return '<span class="text-success fw-bold">Exacto</span>';
                } else if (data > 0) {
                    return `<span class="text-danger">+${data} min</span>`;
                } else {
                    return `<span class="text-primary">${data} min</span>`;
                }
            }
        },
        {
            title: 'Estado',
            data: 'asi_fue_puntual',
            width: '10%',
            render: (data, type, row) => {
                if (data === 't' || data === true) {
                    return '<span class="badge badge-puntual">Puntual</span>';
                } else {
                    return '<span class="badge badge-tarde">Tarde</span>';
                }
            }
        },
        {
            title: 'Acciones',
            data: 'asi_id',
            width: '14%',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                 <div class='d-flex justify-content-center flex-wrap'>
                     <button class='btn btn-warning btn-sm modificar mx-1 my-1' 
                         data-id="${data}" 
                         data-actividad="${row.asi_actividad}"  
                         data-fecha="${row.asi_fecha_asistencia}"  
                         data-hora="${row.asi_hora_llegada}"
                         title="Modificar registro">
                         <i class='bi bi-pencil-square'></i>
                     </button>
                     <button class='btn btn-danger btn-sm eliminar mx-1 my-1' 
                         data-id="${data}"
                         data-actividad="${row.act_nombre}"
                         title="Eliminar registro">
                        <i class="bi bi-trash3"></i>
                     </button>
                 </div>`;
            }
        }
    ]
});

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset;

    document.getElementById('asi_id').value = datos.id;
    document.getElementById('asi_actividad').value = datos.actividad;
    document.getElementById('asi_fecha_asistencia').value = datos.fecha;
    document.getElementById('asi_hora_llegada').value = datos.hora;

    // Mostrar info de actividad
    MostrarInfoActividad();

    BtnRegistrar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    FormAsistencias.scrollIntoView({ 
        behavior: 'smooth', 
        block: 'start' 
    });
}

const limpiarTodo = () => {
    FormAsistencias.reset();
    
    // Restablecer fecha actual
    FechaAsistencia.value = hoy;
    
    BtnRegistrar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
    InfoActividad.style.display = 'none';
    
    // Limpiar validaciones visuales
    document.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
        el.classList.remove('is-valid', 'is-invalid');
    });
}

const ModificarAsistencia = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormAsistencias, [''])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Carlos, debes completar todos los campos",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(FormAsistencias);
    const url = '/parcial1_kvsc/public/asistencia/modificarAPI';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "¡Actualizado!",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarAsistencias();
        } else {
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }
    } catch (error) {
        console.log(error);
    }
    BtnModificar.disabled = false;
}

const EliminarAsistencia = async (event) => {
    const id = event.currentTarget.dataset.id;
    const actividad = event.currentTarget.dataset.actividad;
    
    const resultado = await Swal.fire({
        title: '¿Estás seguro Carlos?',
        text: `¿Quieres eliminar el registro de "${actividad}"? Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (!resultado.isConfirmed) {
        return;
    }
    
    const body = new FormData();
    body.append('asi_id', id);

    const url = '/parcial1_kvsc/public/asistencia/eliminarAPI';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Eliminado",
                text: mensaje,
                showConfirmButton: true,
            });
            BuscarAsistencias();
        } else {
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }
    } catch (error) {
        console.log(error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            text: "Error de conexión al eliminar el registro",
            showConfirmButton: true,
        });
    }
}

const MostrarEstadisticas = async () => {
    const url = '/parcial1_kvsc/public/asistencia/reportePuntualidadAPI';
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        
        if (datos.codigo == 1) {
            const { estadisticas_generales, por_actividad } = datos;
            
            let contenidoHTML = `
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3>${estadisticas_generales.puntuales || 0}</h3>
                                <p>Asistencias Puntuales</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h3>${estadisticas_generales.tardanzas || 0}</h3>
                                <p>Llegadas Tarde</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3>${estadisticas_generales.porcentaje_puntualidad || 0}%</h3>
                                <p>Porcentaje de Puntualidad</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3>${estadisticas_generales.promedio_minutos || 0} min</h3>
                                <p>Promedio de Diferencia</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            if (por_actividad && por_actividad.length > 0) {
                contenidoHTML += `
                    <h5>Por Actividad:</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Actividad</th>
                                    <th>Total</th>
                                    <th>Puntuales</th>
                                    <th>Promedio</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                por_actividad.forEach(actividad => {
                    contenidoHTML += `
                        <tr>
                            <td>${actividad.act_nombre}</td>
                            <td>${actividad.total_asistencias}</td>
                            <td><span class="badge bg-success">${actividad.puntuales}</span></td>
                            <td>${actividad.promedio_minutos} min</td>
                        </tr>
                    `;
                });
                
                contenidoHTML += `
                            </tbody>
                        </table>
                    </div>
                `;
            }
            
            document.getElementById('contenidoEstadisticas').innerHTML = contenidoHTML;
            
            const modal = new bootstrap.Modal(document.getElementById('modalEstadisticas'));
            modal.show();
        }
        
    } catch (error) {
        console.log(error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar las estadísticas'
        });
    }
}

// Event Listeners
BuscarAsistencias();
FormAsistencias.addEventListener('submit', RegistrarAsistencia);
SelectActividad.addEventListener('change', MostrarInfoActividad);
datatable.on('click', '.modificar', llenarFormulario);
datatable.on('click', '.eliminar', EliminarAsistencia);
BtnModificar.addEventListener('click', ModificarAsistencia);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnReportePuntualidad.addEventListener('click', MostrarEstadisticas);

// Establecer hora actual al cargar la página
EstablecerHoraActual();