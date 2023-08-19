( function() {

    const nuevaTareaBtn = document.querySelector('#agregar-tarea');
    nuevaTareaBtn.addEventListener('click', mostrarFormulario);

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

        document.querySelector('body').appendChild(modal);

        modal.addEventListener('click', e => {
            e.preventDefault();

            const formulario = document.querySelector('.formulario');
            formulario.classList.add('cerrar');

            if( e.target.classList.contains('cerrar-modal') ) {

                setTimeout(() => {
                    modal.remove();
                }, 400);
            }

            console.log(e.target);
        });
    }

})();