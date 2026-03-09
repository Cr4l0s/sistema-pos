document.addEventListener("DOMContentLoaded", function () {
    const tabla = document.querySelector("#tablaPro");
    if (!tabla) return;

    const endpoint = tabla.dataset.endpoint;
    const ordenDefault = tabla.dataset.ordenDefault || 'id';

    console.log("Endpoint:", endpoint);
    console.log("Orden default:", ordenDefault);

    if (!endpoint) {
        console.error("Error: No se definió el endpoint en la tabla");
        return;
    }

    let pagina = 1;
    let orden = ordenDefault;
    let direccion = "asc";
    let filas = 10;
    let busqueda = "";

    function cargarTabla() {
        const url = `${endpoint}?pagina=${pagina}&orden=${orden}&direccion=${direccion}&filas=${filas}&buscar=${encodeURIComponent(busqueda)}`;
        console.log("Cargando URL:", url);

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                document.querySelector("#tablaProBody").innerHTML = data.html;
                document.querySelector("#tablaProPaginacion").innerHTML = data.paginacion;
            })
            .catch(error => {
                console.error('Error cargando la tabla:', error);
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

    // Exportación IMPRESIÓN
    document.getElementById("exportPrint")?.addEventListener("click", function() {
        const tablaHTML = tabla.outerHTML;
        
        const ventanaImpresion = window.open('', '_blank');
        ventanaImpresion.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Imprimir Tabla</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; } 
                    table { border-collapse: collapse; width: 100%; } 
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; } 
                    th { background-color: #f2f2f2; }
                    @media print { body { padding: 0; } }
                </style>
            </head>
            <body>
                ${tablaHTML}
                <script>
                    window.onload = function() {
                        setTimeout(function() {
                            window.print();
                            setTimeout(function() { window.close(); }, 500);
                        }, 500);
                    };
                <\/script>
            </body>
            </html>
        `);
        ventanaImpresion.document.close();
    });

    // Exportación EXCEL
    document.getElementById("exportExcel")?.addEventListener("click", function() {
        const tablaHTML = tabla.outerHTML;
        const form = document.createElement("form");
        form.method = "POST";
        form.action = "includes/exportar_excel.php";
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "tabla";
        input.value = tablaHTML;
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    });

    // Exportación PDF - DESCARGA ARCHIVO PDF
    document.getElementById("exportPDF")?.addEventListener("click", function() {
        // Obtener cabeceras
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
        
        // Obtener datos
        const filas = [];
        const tbody = tabla.querySelector('tbody');
        if (tbody) {
            const filasTr = tbody.querySelectorAll('tr');
            filasTr.forEach(tr => {
                const celdas = tr.querySelectorAll('td');
                if (celdas.length > 0) {
                    const filaData = [];
                    for (let i = 0; i < celdas.length - 1; i++) {
                        filaData.push(celdas[i].innerText.trim());
                    }
                    filas.push(filaData);
                }
            });
        }
        
        // Crear PDF
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
                              new Date().toISOString().slice(0,10) + '.pdf';
        doc.save(nombreArchivo);
    });

    // Carga inicial
    cargarTabla();
});