import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";
import { data } from "jquery";

const FormActividades = document.getElementById('FormActividades');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnActividadesHoy = document.getElementById('BtnActividadesHoy');
const InputActNombre = document.getElementById('act_nombre');
const InputActFecha = document.getElementById('act_fecha_esperada');
const InputActHora = document.getElementById('act_hora_esperada');

const ValidarFecha = () => {
    console.log(" ValidarFecha ejecutándose - SIN RESTRICCIONES");
    
    if (InputActFecha.value === '') {
        InputActFecha.classList.remove('is-valid', 'is-invalid');
        return;
    }

    InputActFecha.classList.remove('is-invalid');
    InputActFecha.classList.add('is-valid');
    
    console.log(" Fecha validada SIN restricciones");
}

const ValidarNombreActividad = () => {
    const nombre = InputActNombre.value.trim();

    if (nombre.length < 1) {
        InputActNombre.classList.remove('is-valid', 'is-invalid');
    } else {
        if (nombre.length < 3) {
            Swal.fire({
                position: "center",
                icon: "info",
                title: "Nombre muy corto",
                text: "Carlos, el nombre de la actividad debe tener al menos 3 caracteres",
                showConfirmButton: true,
            });
            InputActNombre.classList.remove('is-valid');
            InputActNombre.classList.add('is-invalid');
        } else {
            InputActNombre.classList.remove('is-invalid');
            InputActNombre.classList.add('is-valid');
        }
    }
}

const GuardarActividad = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormActividades, ['act_id'])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Carlos, debes completar todos los campos obligatorios",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    const body = new FormData(FormActividades);
    const url = '/parcial1_kvsc/public/actividad/guardarAPI';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        console.log(datos);
        const { codigo, mensaje } = datos;

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "¡Excelente!",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarActividades();
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
    BtnGuardar.disabled = false;
}

const BuscarActividades = async () => {
    const url = '/parcial1_kvsc/public/actividad/buscarAPI';
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
        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Sin actividades",
                text: mensaje,
                showConfirmButton: true,
            });
        }
    } catch (error) {
        console.log(error);
    }
}

const BuscarActividadesHoy = async () => {
    const url = '/parcial1_kvsc/public/actividad/actividadesHoyAPI';
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;

        if (codigo == 1) {
            if (data.length > 0) {
                datatable.clear().draw();
                datatable.rows.add(data).draw();
                
                await Swal.fire({
                    position: "center",
                    icon: "info",
                    title: "Actividades de hoy",
                    text: `Carlos, tienes ${data.length} actividad(es) programada(s) para hoy`,
                    showConfirmButton: true,
                });
            } else {
                await Swal.fire({
                    position: "center",
                    icon: "info",
                    title: "Sin actividades",
                    text: "Carlos, no tienes actividades programadas para hoy. ¡Día libre!",
                    showConfirmButton: true,
                });
            }
        }
    } catch (error) {
        console.log(error);
    }
}

const datatable = new DataTable('#TableActividades', {
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
    columns: [
        {
            title: 'No.',
            data: 'act_id',
            width: '5%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { 
            title: 'Actividad', 
            data: 'act_nombre',
            width: '25%'
        },
        { 
            title: 'Descripción', 
            data: 'act_descripcion',
            width: '30%',
            render: (data, type, row) => {
                return data || 'Sin descripción';
            }
        },
        { 
            title: 'Fecha Esperada', 
            data: 'act_fecha_esperada',
            width: '15%',
            render: (data, type, row) => {
                if (type === 'display') {
                    const fecha = new Date(data);
                    return fecha.toLocaleDateString('es-GT');
                }
                return data;
            }
        },
        { 
            title: 'Hora Esperada', 
            data: 'act_hora_esperada',
            width: '10%',
            render: (data, type, row) => {
                if (type === 'display') {
                    const fecha = new Date(data);
                    return fecha.toLocaleTimeString('es-GT', { 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    });
                }
                return data;
            }
        },
        {
            title: 'Estado',
            data: 'act_fecha_esperada',
            width: '10%',
            render: (data, type, row) => {
                const fechaActividad = new Date(data);
                const hoy = new Date();
                
                if (fechaActividad.toDateString() === hoy.toDateString()) {
                    return '<span class="badge bg-primary">Hoy</span>';
                } else if (fechaActividad > hoy) {
                    return '<span class="badge bg-success">Próxima</span>';
                } else {
                    return '<span class="badge bg-secondary">Pasada</span>';
                }
            }
        },
        {
            title: 'Acciones',
            data: 'act_id',
            searchable: false,
            orderable: false,
            width: '15%',
            render: (data, type, row, meta) => {
                return `
                 <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning btn-sm modificar mx-1' 
                         data-id="${data}" 
                         data-nombre="${row.act_nombre}"  
                         data-descripcion="${row.act_descripcion || ''}"  
                         data-fecha="${row.act_fecha_esperada}"  
                         data-hora="${row.act_hora_esperada}"  
                         title="Modificar actividad">
                         <i class='bi bi-pencil-square'></i>
                     </button>
                     <button class='btn btn-danger btn-sm eliminar mx-1' 
                         data-id="${data}"
                         title="Eliminar actividad">
                        <i class="bi bi-trash3"></i>
                     </button>
                 </div>`;
            }
        }
    ]
});

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset;

    document.getElementById('act_id').value = datos.id;
    document.getElementById('act_nombre').value = datos.nombre;
    document.getElementById('act_descripcion').value = datos.descripcion;
    
    const fechaCompleta = new Date(datos.fecha);
    const fechaSolo = fechaCompleta.toISOString().split('T')[0]; 
    
    const horaCompleta = new Date(datos.hora);
    const horaSolo = horaCompleta.toTimeString().slice(0, 5); 
    
    document.getElementById('act_fecha_esperada').value = fechaSolo;
    document.getElementById('act_hora_esperada').value = horaSolo;

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

const limpiarTodo = () => {
    FormActividades.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
    
    const inputs = FormActividades.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.classList.remove('is-valid', 'is-invalid');
    });
}

const ModificarActividad = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormActividades, [''])) {
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

    const body = new FormData(FormActividades);
    const url = '/parcial1_kvsc/public/actividad/modificarAPI';
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
            BuscarActividades();
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

const EliminarActividad = async (e) => {
    const idActividad = e.currentTarget.dataset.id;

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "warning",
        title: "¿Eliminar actividad?",
        text: 'Carlos, ¿estás seguro que deseas eliminar esta actividad?',
        showConfirmButton: true,
        confirmButtonText: 'Sí, Eliminar',
        confirmButtonColor: '#d33',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        const body = new FormData();
        body.append('act_id', idActividad);
        
        const url = '/parcial1_kvsc/public/actividad/eliminarAPI';
        const config = {
            method: 'POST',
            body
        }

        try {
            const consulta = await fetch(url, config);
            
            const contentType = consulta.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('La respuesta del servidor no es JSON válida');
            }
            
            const respuesta = await consulta.json();
            const { codigo, mensaje } = respuesta;

            if (codigo == 1) {
                await Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "¡Eliminado!",
                    text: mensaje,
                    showConfirmButton: true,
                });

                BuscarActividades();
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
            console.log('Error completo:', error);
            
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error de conexión",
                text: "No se pudo eliminar la actividad. Revisa la consola para más detalles.",
                showConfirmButton: true,
            });
        }
    }
}

BuscarActividades();


datatable.on('click', '.eliminar', EliminarActividad);
datatable.on('click', '.modificar', llenarFormulario);
FormActividades.addEventListener('submit', GuardarActividad);
InputActNombre.addEventListener('blur', ValidarNombreActividad);
InputActFecha.addEventListener('change', ValidarFecha);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarActividad);
BtnActividadesHoy.addEventListener('click', BuscarActividadesHoy);