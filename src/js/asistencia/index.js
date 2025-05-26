import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

console.log("🚀 Iniciando sistema de asistencia...");

// 🎯 ELEMENTOS DEL DOM PARA ASISTENCIAS
const FormAsistencias = document.getElementById('FormAsistencias');
const BtnRegistrar = document.getElementById('BtnRegistrar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const SelectActividad = document.getElementById('asi_actividad');
const TableAsistencias = document.getElementById('TableAsistencias');

// Variables de estado
let registrandoAsistencia = false;

console.log("📋 Elementos encontrados:", {
    form: !!FormAsistencias,
    btnRegistrar: !!BtnRegistrar,
    selectActividad: !!SelectActividad,
    tabla: !!TableAsistencias
});

// 🎯 FUNCIÓN: Registrar Asistencia
const RegistrarAsistencia = async (event) => {
    console.log("🎯 Iniciando registro de asistencia...");
    event.preventDefault();
    
    // Prevenir doble envío
    if (registrandoAsistencia) {
        console.log("⚠️ Ya se está procesando un registro");
        return;
    }
    
    registrandoAsistencia = true;
    BtnRegistrar.disabled = true;

    if (!validarFormulario(FormAsistencias, ['asi_id'])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Carlos, debes seleccionar una actividad",
            showConfirmButton: true,
        });
        BtnRegistrar.disabled = false;
        registrandoAsistencia = false;
        return;
    }

    const body = new FormData(FormAsistencias);

    const url = '/parcial1_kvsc/asistencia/registrarAPI';
    const config = {
        method: 'POST',
        body
    }

    try {
        // Mostrar estado de carga
        BtnRegistrar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registrando...';
        
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        console.log("📄 Datos recibidos:", datos);
        
        const { codigo, mensaje } = datos

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "¡Excelente!",
                text: mensaje,
                confirmButtonText: "¡Genial!",
                timer: 4000,
                timerProgressBar: true
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
        console.error("💥 Error completo:", error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo conectar con el servidor",
            showConfirmButton: true,
        });
    }
    
    // Rehabilitar botón
    BtnRegistrar.disabled = false;
    BtnRegistrar.innerHTML = '<i class="fas fa-clock"></i> Registrar Mi Llegada';
    registrandoAsistencia = false;
}

// 🎯 FUNCIÓN: Buscar Asistencias
const BuscarAsistencias = async () => {
    const url = '/parcial1_kvsc/asistencia/buscarAPI';
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos

        if (codigo == 1) {
            if (datatable) {
                datatable.clear().draw();
                datatable.rows.add(data).draw();
            } else {
                mostrarAsistenciasEnTabla(data);
            }
            console.log(`✅ Se cargaron ${data.length} asistencias`);
        } else {
            console.log("⚠️ No se pudieron cargar asistencias:", mensaje);
            if (TableAsistencias) {
                TableAsistencias.innerHTML = `
                    <thead>
                        <tr>
                            <th colspan="6" class="text-center text-warning">
                                No se pudieron cargar las asistencias
                            </th>
                        </tr>
                    </thead>
                `;
            }
        }
    } catch (error) {
        console.error("❌ Error al cargar asistencias:", error);
        if (TableAsistencias) {
            TableAsistencias.innerHTML = `
                <thead>
                    <tr>
                        <th colspan="6" class="text-center text-danger">
                            Error al cargar las asistencias: ${error.message}
                        </th>
                    </tr>
                </thead>
            `;
        }
    }
}

// 🎯 FUNCIÓN: Mostrar asistencias en tabla
const mostrarAsistenciasEnTabla = (asistencias) => {
    if (!TableAsistencias) return;
    
    let html = `
        <thead class="table-dark">
            <tr>
                <th>Fecha y Hora</th>
                <th>Actividad</th>
                <th>Hora Esperada</th>
                <th>Diferencia</th>
                <th>¿Puntual?</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
    `;
    
    if (asistencias && asistencias.length > 0) {
        asistencias.forEach(asistencia => {
            const esPuntual = parseInt(asistencia.asi_fue_puntual) === 1;
            const badgePuntual = esPuntual 
                ? '<span class="badge bg-success"><i class="fas fa-check"></i> SÍ</span>' 
                : '<span class="badge bg-danger"><i class="fas fa-times"></i> NO</span>';
            
            const diferencia = parseInt(asistencia.asi_minutos_diferencia);
            let textoDiferencia = '';
            
            if (diferencia <= 0) {
                textoDiferencia = `<span class="text-success"><i class="fas fa-clock"></i> ${Math.abs(diferencia)} min temprano</span>`;
            } else {
                textoDiferencia = `<span class="${esPuntual ? 'text-warning' : 'text-danger'}"><i class="fas fa-clock"></i> ${diferencia} min tarde</span>`;
            }
            
            html += `
                <tr>
                    <td>
                        <small class="text-muted">
                            <i class="fas fa-calendar-day"></i>
                            ${asistencia.fecha_formateada || asistencia.asi_timestamp_registro}
                        </small>
                    </td>
                    <td>
                        <strong class="text-primary">
                            <i class="fas fa-tasks"></i> ${asistencia.act_nombre}
                        </strong>
                    </td>
                    <td>
                        <span class="badge bg-info">
                            <i class="fas fa-clock"></i> ${asistencia.act_hora_esperada}
                        </span>
                    </td>
                    <td>${textoDiferencia}</td>
                    <td class="text-center">${badgePuntual}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-danger eliminar" 
                                data-id="${asistencia.asi_id}"
                                title="Eliminar asistencia">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    } else {
        html += `
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="fas fa-info-circle fa-2x mb-3 d-block"></i>
                    <h6>No hay asistencias registradas aún</h6>
                    <small>¡Registra tu primera asistencia arriba!</small>
                </td>
            </tr>
        `;
    }
    
    html += '</tbody>';
    TableAsistencias.innerHTML = html;
}

// 🎯 DATATABLE: Configuración para asistencias
let datatable = null;

const inicializarDataTable = () => {
    if (TableAsistencias && typeof DataTable !== 'undefined') {
        try {
            datatable = new DataTable('#TableAsistencias', {
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
                responsive: true,
                pageLength: 10,
                order: [[0, 'desc']], // Ordenar por fecha descendente
                columns: [
                    {
                        title: 'Fecha y Hora',
                        data: 'fecha_formateada',
                        render: (data, type, row) => {
                            return `<small class="text-muted"><i class="fas fa-calendar-day"></i> ${data || row.asi_timestamp_registro}</small>`;
                        }
                    },
                    {
                        title: 'Actividad',
                        data: 'act_nombre',
                        render: (data, type, row) => {
                            return `<strong class="text-primary"><i class="fas fa-tasks"></i> ${data}</strong>`;
                        }
                    },
                    {
                        title: 'Hora Esperada',
                        data: 'act_hora_esperada',
                        render: (data, type, row) => {
                            return `<span class="badge bg-info"><i class="fas fa-clock"></i> ${data}</span>`;
                        }
                    },
                    {
                        title: 'Diferencia',
                        data: 'asi_minutos_diferencia',
                        render: (data, type, row) => {
                            const diferencia = parseInt(data);
                            const esPuntual = parseInt(row.asi_fue_puntual) === 1;
                            
                            if (diferencia <= 0) {
                                return `<span class="text-success"><i class="fas fa-clock"></i> ${Math.abs(diferencia)} min temprano</span>`;
                            } else {
                                return `<span class="${esPuntual ? 'text-warning' : 'text-danger'}"><i class="fas fa-clock"></i> ${diferencia} min tarde</span>`;
                            }
                        }
                    },
                    {
                        title: '¿Puntual?',
                        data: 'asi_fue_puntual',
                        render: (data, type, row) => {
                            const esPuntual = parseInt(data) === 1;
                            return esPuntual 
                                ? '<span class="badge bg-success"><i class="fas fa-check"></i> SÍ</span>' 
                                : '<span class="badge bg-danger"><i class="fas fa-times"></i> NO</span>';
                        }
                    },
                    {
                        title: 'Acciones',
                        data: 'asi_id',
                        searchable: false,
                        orderable: false,
                        render: (data, type, row, meta) => {
                            return `
                             <div class='d-flex justify-content-center'>
                                 <button class='btn btn-sm btn-outline-danger eliminar' 
                                     data-id="${data}"
                                     title="Eliminar asistencia">
                                     <i class='fas fa-trash-alt'></i>
                                 </button>
                             </div>`;
                        }
                    }
                ]
            });
            
            console.log("✅ DataTable inicializado para asistencias");
        } catch (error) {
            console.log("⚠️ Error al inicializar DataTable:", error);
            console.log("ℹ️ Continuando con tabla básica");
        }
    }
}

// 🎯 FUNCIÓN: Limpiar formulario
const limpiarTodo = () => {
    if (FormAsistencias) {
        FormAsistencias.reset();
        
        // Limpiar clases de validación
        const inputs = FormAsistencias.querySelectorAll('.form-control, .form-select');
        inputs.forEach(input => {
            input.classList.remove('is-valid', 'is-invalid');
        });
        
        console.log("🧹 Formulario limpiado");
    }
}

// 🎯 FUNCIÓN: Eliminar Asistencia
const EliminarAsistencia = async (e) => {
    const idAsistencia = e.currentTarget.dataset.id;
    console.log("🗑️ Solicitando eliminar asistencia ID:", idAsistencia);

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "warning",
        title: "¿Estás seguro?",
        text: 'Esta acción no se puede deshacer',
        showConfirmButton: true,
        confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'Cancelar',
        cancelButtonColor: '#6c757d',
        showCancelButton: true,
        reverseButtons: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        try {
            // Mostrar loading
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('asi_id', idAsistencia);
            
            const url = `/parcial1_kvsc/asistencia/eliminarAPI`;
            const config = {
                method: 'POST',
                body: formData
            }

            const consulta = await fetch(url, config);
            const respuesta = await consulta.json();
            const { codigo, mensaje } = respuesta;

            if (codigo == 1) {
                await Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "¡Eliminado!",
                    text: mensaje,
                    confirmButtonText: "Entendido",
                    confirmButtonColor: "#28a745",
                    timer: 3000,
                    timerProgressBar: true
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
            console.error("Error al eliminar:", error);
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error de conexión",
                text: "No se pudo eliminar la asistencia",
                showConfirmButton: true,
            });
        }
    }
}

// 🎯 FUNCIÓN: Configurar event listeners de tabla
const configurarEventListenersTabla = () => {
    if (datatable) {
        datatable.on('click', '.eliminar', EliminarAsistencia);
    } else if (TableAsistencias) {
        // Delegar eventos para tabla básica
        TableAsistencias.addEventListener('click', (e) => {
            if (e.target.closest('.eliminar')) {
                EliminarAsistencia(e);
            }
        });
    }
}

// 🎯 INICIALIZACIÓN: Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    console.log("📱 DOM listo - Inicializando asistencias...");
    
    // Inicializar DataTable si está disponible
    inicializarDataTable();
    
    // Configurar eventos de tabla
    configurarEventListenersTabla();
    
    console.log("✅ Módulo de asistencias iniciado correctamente");
});

// 🎯 EVENT LISTENERS: Configuración principal
if (FormAsistencias) {
    FormAsistencias.addEventListener('submit', RegistrarAsistencia);
}

if (BtnLimpiar) {
    BtnLimpiar.addEventListener('click', (e) => {
        e.preventDefault();
        limpiarTodo();
    });
}

// 🎯 CARGAR DATOS INICIALES
BuscarAsistencias();

console.log("🏁 Script de asistencia cargado completamente");