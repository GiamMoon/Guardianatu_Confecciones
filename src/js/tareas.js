(function () {
    const nuevaTareaBtn = document.querySelector("#agregar-tarea");
    nuevaTareaBtn.addEventListener("click", mostrarFormulario);

    function mostrarFormulario() {
    fetch('/obtener-clientes')
        .then(response => response.json())
        .then(clientes => {
            const selectHTML = `
                <div class="campo">
                    <label>Seleccionar Cliente</label>
                    <select name="cliente" id="cliente" class="seleccion-cliente">
                        ${clientes.map(cliente => `<option value="${cliente.id}">${cliente.nombres} ${cliente.apellidos}</option>`).join('')}
                    </select>
                </div>
            `;

            const modal = document.createElement("DIV");
            modal.classList.add("modal");
            modal.innerHTML = `
                <form class="formulario nueva-tarea">
                    <legend>Añade una nueva tarea</legend>
                    ${selectHTML}
                    <div class="campo">
                        <label>Tarea</label>
                        <input
                            type="text"
                            name="tarea"
                            placeholder="Añadir Tarea al Proyecto Actual"
                            id="tarea"/>
                    </div>
                    <div class="opciones">
                        <input type="submit" class="submit-nueva-tarea" value="Añadir Tarea"/>
                        <button type="button" class="cerrar-modal">Cancelar</button>
                    </div>
                </form>`;

            setTimeout(() => {
                const formulario = modal.querySelector(".formulario");
                formulario.classList.add("animar");
                // Inicializa select2
                $('.seleccion-cliente').select2();
            }, 0);

            modal.addEventListener("click", async function (e) {
                e.preventDefault();
                if (e.target.classList.contains("cerrar-modal")) {
                    const formulario = modal.querySelector(".formulario");
                    formulario.classList.add("cerrar");
                    setTimeout(() => {
                        modal.remove();
                    }, 500);
                }

                if (e.target.classList.contains("submit-nueva-tarea")) {
                    await submitFormularioNuevaTarea();

                    // Limpia el contenido del campo de texto después de agregar una tarea
                    const tareaInput = modal.querySelector("#tarea");
                    tareaInput.value = "";
                }
            });

            document.querySelector("body").appendChild(modal);
        })
        .catch(error => {
            console.error('Error al obtener la lista de clientes:', error);
        });
}


    async function submitFormularioNuevaTarea() {
        const tarea = document.querySelector("#tarea").value.trim();
        const cliente_id = document.querySelector("#cliente").value;
    
        if (!cliente_id) {
            mostrarAlerta("ID de cliente no proporcionado", "error", document.querySelector(".formulario legend"));
            return;
        }
    
        if (tarea === "") {
            mostrarAlerta("El nombre de la tarea es obligatorio", "error", document.querySelector(".formulario legend"));
            return;
        }
    
        // Datos del formulario que se enviarán al servidor
        const formData = {
            cliente_id,
            tarea,
        };

        // Obtén el ID del usuario de sesión
        const usuarioId = obtenerIdUsuario(); // Ajusta esta función según cómo obtienes el ID del usuario de sesión

        // Añade el ID del usuario al formulario
        formData.usuario_id = usuarioId;

        // Envía una solicitud POST al servidor
        try {
            const response = await fetch('/crear-tarea', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            });
    
            const result = await response.json();
    
            if (result.success) {
                // Muestra la alerta verde si la tarea se agregó correctamente
                mostrarAlerta(result.mensaje, "success", document.querySelector(".formulario legend"));
            } else {
                // Muestra la alerta de error si hay algún problema
                mostrarAlerta(result.error, "error", document.querySelector(".formulario legend"));
            }
        } catch (error) {
            console.error('Error al enviar la tarea al servidor:', error);
            mostrarAlerta('Error al enviar la tarea al servidor', 'error', document.querySelector('.formulario legend'));
        }
    }
    
    function mostrarAlerta(mensaje, tipo, referencia) {
        const alertaPrevia = document.querySelector(".alerta");
        if (alertaPrevia) {
            alertaPrevia.remove();
        }
    
        const alerta = document.createElement("DIV");
        alerta.classList.add("alerta", tipo);
        alerta.textContent = mensaje;
    
        // Cambia el fondo a verde si es una alerta de éxito
        if (tipo === "success") {
            alerta.style.backgroundColor = "green";
        }
    
        referencia.parentElement.insertBefore(alerta, referencia.nextElementSibling);
    
        setTimeout(() => {
            alerta.remove();
        }, 5000);
    }

    // Ajusta esta función según cómo obtienes el ID del usuario de sesión
    function obtenerIdUsuario() {
        return 1; // Por ejemplo, aquí retornamos un valor fijo como ejemplo
    }

})();
