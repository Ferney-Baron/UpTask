( function() {

    obtenerTareas();
    let tareas = [];

    const nuevaTareaBtn = document.querySelector('#agregar-tarea');
    nuevaTareaBtn.addEventListener('click', mostrarFormulario);

    async function obtenerTareas() {

        const tareaId = obtenerProyecto();
        const url = `/api/tareas?id=${tareaId}`;

        try {
            const request = await fetch(url);
            const response = await request.json();

            // const { tareas } = response;
            tareas = response.tareas;
            mostrarTareas();
        } catch (error) {
            console.log(error);
        }
    }

    function mostrarTareas() {
        limpiarTareas();
        const contenedorTareas = document.querySelector('#listado-tareas');

        if(!tareas.length) {
            const textoNotareas = document.createElement('LI');
            textoNotareas.textContent = 'No hay Tareas';
            textoNotareas.classList.add('no-tareas');

            contenedorTareas.appendChild(textoNotareas);

            return;
        }

        const estados = {
            0: 'Pendiente',
            1: 'Completada'
        }

        tareas.forEach( tarea => {
            const contenedorTarea = document.createElement('LI');
            contenedorTarea.dataset.tareaId = tarea.id;
            contenedorTarea.classList.add('tarea');

            const nombreTarea = document.createElement('P');
            nombreTarea.textContent = tarea.nombre;

            const opcionesDiv = document.createElement('DIV');
            opcionesDiv.classList.add('opciones');

            const btnEstadoTarea = document.createElement('BUTTON');
            btnEstadoTarea.classList.add('estado-tarea');
            btnEstadoTarea.classList.add(`${estados[tarea.estado].toLowerCase()}`);
            btnEstadoTarea.textContent = `${estados[tarea.estado]}`
            btnEstadoTarea.dataset.estadoTarea = tarea.estado;

            const btnEliminarTarea = document.createElement('BUTTON');
            btnEliminarTarea.classList.add('eliminar-tarea');
            btnEliminarTarea.dataset.idTarea = tarea.id;
            btnEliminarTarea.textContent = 'Eliminar';

            opcionesDiv.appendChild(btnEstadoTarea);
            opcionesDiv.appendChild(btnEliminarTarea);

            contenedorTarea.appendChild(nombreTarea);
            contenedorTarea.appendChild(opcionesDiv);

            contenedorTareas.appendChild(contenedorTarea);
            console.log(btnEstadoTarea)
        });
    }

    function mostrarFormulario() {
        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        modal.innerHTML = `
            <form class="formulario nueva-tarea">
                <legend>Añade una nueva tarea</legend>
                <div class="campo">
                    <label>Tarea</label>
                    <input
                        type="text"
                        name="tarea"
                        placeholder="Añadir Tarea al royecto Actual"
                        id="tarea"
                    />
                </div>
                <div class="opciones">
                    <input type="submit" class="submit-nueva-tarea" value="Añadir Tarea"/>
                    <button type="button" class="cerrar-modal">Cancelar</button>
                </div>
            </form>
        `;

        setTimeout(() => {
            const formulario = document.querySelector('.formulario');
            formulario.classList.add('animar');
        }, 400);

        document.querySelector('.dashboard').appendChild(modal);

        modal.addEventListener('click', e => {
            e.preventDefault();

            if( e.target.classList.contains('cerrar-modal')) {
                const formulario = document.querySelector('.formulario');
                formulario.classList.add('cerrar');
                setTimeout(() => {
                    modal.remove();
                }, 400);
            }
            if(e.target.classList.contains('submit-nueva-tarea'))  {
                submitFormularioNuevaTarea();
            }
        });
    }

    function submitFormularioNuevaTarea()  {
        const tarea = document.querySelector('#tarea').value.trim();

        if(!tarea) {
            mostrarAlerta('El Nombre de la Alerta es Obligatorio', 'error', document.querySelector('.formulario legend'));   
            return;
        }

        agregarTarea(tarea);
    }

    function mostrarAlerta(mensaje, tipo, referencia)  {

        const alertaPrevia = document.querySelector('.alerta');
        if(alertaPrevia) {
            alertaPrevia.remove();
        }

        const alerta = document.createElement('DIV');
        alerta.classList.add('alerta', tipo);
        alerta.textContent = mensaje;

        referencia.parentNode.insertBefore(alerta, referencia.nextSibling);

        setTimeout(() => {
            alerta.remove();
        }, 5000);
    }

    async function agregarTarea(tarea) {
        const datos = new FormData();
        datos.append('nombre', tarea);
        datos.append('url', obtenerProyecto());

        try {
            const respuesta = await fetch('http://localhost:3000/api/tarea', {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();
            console.log(resultado);
            mostrarAlerta(resultado.mensaje, resultado.tipo, document.querySelector('.formulario legend'));   

            if(resultado.tipo === 'exito') {
                const modal = document.querySelector('.modal');

                setTimeout(() => {
                    modal.remove();
                }, 3000);

                const tareaObj = {
                    id : String(resultado.id),
                    nombre : tarea,
                    estado : '0',
                    proyectoId : resultado.proyectoId
                }

                tareas = [...tareas, tareaObj];
                mostrarTareas();
            }

        } catch (error) {
            console.log(error);
        }
    }

    function obtenerProyecto() {
        const proyectoParams = new URLSearchParams(window.location.search);
        const proyecto = Object.fromEntries(proyectoParams.entries());
        return proyecto.id;
    }

    function limpiarTareas() {
        const listadoTareas = document.querySelectorAll('#listado-tareas');
       
        while(listadoTareas.firstChild) {
            listadoTareas.removeChild(listadoTareas.firstChild)
        }
    }
})();