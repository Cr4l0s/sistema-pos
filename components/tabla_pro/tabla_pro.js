document.addEventListener("DOMContentLoaded", function () {
    const tabla = document.querySelector("#tablaPro");
    if (!tabla) return;

    let endpoint = tabla.dataset.endpoint;
    const ordenDefault = tabla.dataset.ordenDefault || 'id';

    console.log("🔍 Endpoint original:", endpoint);

    if (window.location.href.includes('inicio_pais')) {
        endpoint = "components/tabla_pro/tabla_endpoint_paises.php";
        console.log("🔍 Endpoint forzado para países:", endpoint);
    }

    if (!endpoint) {
        console.error("Error: No se definió el endpoint en la tabla");
        return;
    }

    let pagina = 1;
    let orden = ordenDefault;
    let direccion = "asc";
    let filas = 10;
    let busqueda = "";
    let categoria = 0;

    // ============================================
    // FUNCIÓN CARGAR TABLA
    // ============================================
    function cargarTabla() {
        // Detectar si estamos en localhost o en servidor
        const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';

        let basePath = '';
        if (isLocalhost) {
            basePath = '/practica_sventas_desa/';
            console.log("🌐 Entorno LOCAL detectado");
        } else {
            basePath = '/';
            console.log("🌐 Entorno SERVIDOR detectado");
        }

        let url = window.location.origin + basePath + endpoint +
            `?pagina=${pagina}&orden=${orden}&direccion=${direccion}&filas=${filas}&buscar=${encodeURIComponent(busqueda)}&categoria=${categoria}`;

        console.log("📡 Cargando URL:", url);

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.html) {
                    document.querySelector("#tablaProBody").innerHTML = data.html;
                }
                if (data.paginacion) {
                    document.querySelector("#tablaProPaginacion").innerHTML = data.paginacion;
                }
            })
            .catch(error => {
                console.error('❌ Error cargando la tabla:', error);
                document.querySelector("#tablaProBody").innerHTML =
                    '<tr><td colspan="10" class="text-center text-danger">Error al cargar datos</td></tr>';
            });
    }

    // Búsqueda
    const buscarInput = document.querySelector("#buscarTabla");
    if (buscarInput) {
        buscarInput.addEventListener("keyup", function (e) {
            busqueda = e.target.value;
            pagina = 1;
            cargarTabla();
        });
    }

    // Selector de filas
    const filasSelect = document.querySelector("#filasTabla");
    if (filasSelect) {
        filasSelect.addEventListener("change", function (e) {
            filas = parseInt(e.target.value);
            pagina = 1;
            cargarTabla();
        });
    }

    // Filtro de categorías
    const filtroCategoria = document.querySelector("#filtroCategoria");
    if (filtroCategoria) {
        categoria = parseInt(filtroCategoria.value) || 0;
        filtroCategoria.addEventListener("change", function (e) {
            categoria = parseInt(e.target.value) || 0;
            pagina = 1;
            cargarTabla();
        });
    }

    // Paginación
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("pagina-btn")) {
            pagina = parseInt(e.target.dataset.page);
            cargarTabla();
        }
    });

    // Ordenamiento
    document.addEventListener("click", function (e) {
        const th = e.target.closest(".sortable");
        if (th) {
            orden = th.dataset.col;
            direccion = direccion === "asc" ? "desc" : "asc";
            pagina = 1;
            cargarTabla();
        }
    });

    // ============================================
    // EXPORTACIÓN A EXCEL (VERSIÓN HTML - FORMATO ORIGINAL)
    // ============================================
    document.getElementById("exportExcel")?.addEventListener("click", function () {
        console.log("📊 Exportando a Excel...");

        const tablaOriginal = document.querySelector("#tablaPro");

        // Crear una copia de la tabla
        const tablaClone = tablaOriginal.cloneNode(true);

        // 1. Eliminar la columna de Acciones (última columna)
        // Encabezados
        const theadRows = tablaClone.querySelectorAll('thead tr');
        theadRows.forEach(row => {
            const ths = row.querySelectorAll('th');
            if (ths.length > 0) {
                ths[ths.length - 1].remove(); // Eliminar último th
            }
        });

        // Filas de datos
        const tbodyRows = tablaClone.querySelectorAll('tbody tr');
        tbodyRows.forEach(row => {
            const celdas = row.querySelectorAll('td');
            if (celdas.length > 0) {
                celdas[celdas.length - 1].remove(); // Eliminar último td
            }
        });

        // 2. Convertir iconos a texto
        const celdasConIconos = tablaClone.querySelectorAll('td');
        celdasConIconos.forEach(celda => {
            let html = celda.innerHTML;
            // Reemplazar iconos por texto
            html = html.replace(/✅/g, 'Sí');
            html = html.replace(/❌/g, 'No');
            html = html.replace(/🔍|📝|🗑️|↑|↓|↑↓/g, '');
            celda.innerHTML = html;
        });

        // 3. Eliminar elementos interactivos
        const elementos = tablaClone.querySelectorAll('button, form, i, .btn, .badge, svg');
        elementos.forEach(el => el.remove());

        // 4. Obtener el HTML limpio
        let tablaHTML = tablaClone.outerHTML;

        // 5. Limpiar atributos de estilos y clases
        tablaHTML = tablaHTML.replace(/class="[^"]*"/g, '');
        tablaHTML = tablaHTML.replace(/style="[^"]*"/g, '');
        tablaHTML = tablaHTML.replace(/data-[^=]*="[^"]*"/g, '');

        // 6. Agregar estilos básicos
        const estilos = `
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 4px; }
        th { background-color: #f2f2f2; }
    </style>
    `;

        // 7. Crear el HTML completo
        const htmlCompleto = `
    <html>
        <head>
            <meta charset="UTF-8">
            <title>Exportación</title>
            ${estilos}
        </head>
        <body>
            <h2>Listado de Productos</h2>
            ${tablaHTML}
        </body>
    </html>
    `;

        // 8. Descargar como .xls
        const blob = new Blob([htmlCompleto], { type: 'application/vnd.ms-excel' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'productos_' + new Date().toISOString().slice(0, 10) + '.xls';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(link.href);
    });


    // ============================================
    // EXPORTACIÓN IMPRESIÓN
    // ============================================
    document.getElementById("exportPrint")?.addEventListener("click", function () {
        console.log("========== INICIO IMPRESIÓN ==========");

        const tabla = document.querySelector("#tablaPro");
        if (!tabla) {
            alert("Error: No se encontró la tabla");
            return;
        }

        const encabezados = [];
        const ths = document.querySelectorAll('#tablaPro thead th');

        for (let i = 0; i < ths.length - 1; i++) {
            let texto = ths[i].innerText.replace('↑↓', '').trim();
            encabezados.push(texto);
        }

        const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
        let basePath = isLocalhost ? '/practica_sventas_desa/' : '/';

        const url = window.location.origin + basePath + endpoint +
            `?pagina=${pagina}&orden=${orden}&direccion=${direccion}&filas=${filas}&buscar=${encodeURIComponent(busqueda)}&sin_acciones=1&categoria=${categoria}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                let tablaCompleta = '<table class="table table-striped table-bordered">';
                tablaCompleta += '<thead><tr>';
                encabezados.forEach(th => {
                    tablaCompleta += `<th>${th}</th>`;
                });
                tablaCompleta += '</tr></thead>';
                tablaCompleta += '<tbody>';
                tablaCompleta += data.html;
                tablaCompleta += '</tbody></table>';

                const ventana = window.open('', '_blank');
                if (!ventana) {
                    alert("Por favor, permite los pop-ups para este sitio.");
                    return;
                }

                ventana.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Imprimir Tabla</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; } 
                        table { border-collapse: collapse; width: 100%; } 
                        th, td { border: 1px solid #000; padding: 8px; } 
                        th { background-color: #f2f2f2; }
                    </style>
                </head>
                <body>
                    ${tablaCompleta}
                    <script>
                        window.onload = function() { setTimeout(function() { window.print(); }, 500); };
                        window.onafterprint = function() { window.close(); };
                        setTimeout(function() { window.close(); }, 30000);
                    <\/script>
                </body>
                </html>
            `);
                ventana.document.close();
            })
            .catch(error => {
                console.error('❌ Error:', error);
                alert('Error al preparar la impresión');
            });
    });

    // ============================================
    // EXPORTACIÓN PDF
    // ============================================
    document.getElementById("exportPDF")?.addEventListener("click", function () {
        const cabeceras = [];
        const thead = tabla.querySelector('thead');
        if (thead) {
            const ths = thead.querySelectorAll('th');
            ths.forEach((th, index) => {
                if (index < ths.length - 1) {
                    cabeceras.push(th.innerText.replace('↑↓', '').trim());
                }
            });
        }

        const filas = [];
        const tbody = tabla.querySelector('tbody');
        if (tbody) {
            const filasTr = tbody.querySelectorAll('tr');
            filasTr.forEach(tr => {
                const celdas = tr.querySelectorAll('td');
                if (celdas.length > 0) {
                    const filaData = [];
                    for (let i = 0; i < celdas.length - 1; i++) {
                        let valor = celdas[i].innerText.trim();
                        valor = valor.replace(/[✅❌🔍📝🗑️]/g, '');
                        filaData.push(valor);
                    }
                    filas.push(filaData);
                }
            });
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        const titulo = document.querySelector('.card-header h4')?.innerText || 'Listado';
        doc.text(titulo, 14, 15);

        doc.autoTable({
            head: [cabeceras],
            body: filas,
            startY: 25,
            styles: { fontSize: 8, cellPadding: 2 },
            headStyles: { fillColor: [41, 128, 185], textColor: 255, fontSize: 9 },
            alternateRowStyles: { fillColor: [245, 245, 245] },
        });

        const nombreArchivo = titulo.toLowerCase().replace(/\s+/g, '_') + '_' +
            new Date().toISOString().slice(0, 10) + '.pdf';
        doc.save(nombreArchivo);
    });

    // Carga inicial
    cargarTabla();
});