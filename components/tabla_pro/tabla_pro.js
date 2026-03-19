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
    // FUNCIÓN PARA OBTENER BASE URL
    // ============================================
    function getBaseUrl() {
        const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
        return window.location.origin + (isLocalhost ? '/practica_sventas_desa/' : '/');
    }

    // ============================================
    // FUNCIÓN PARA CONSTRUIR URL (separa vista de exportación)
    // ============================================
    function construirUrlVista() {
        const baseUrl = getBaseUrl() + endpoint.trim();
        const params = new URLSearchParams({
            pagina: pagina,
            orden: orden,
            direccion: direccion,
            filas: filas,
            buscar: busqueda,
            categoria: categoria
        });
        return `${baseUrl}?${params.toString()}`;
    }

    function construirUrlExportacion() {
        const baseUrl = getBaseUrl() + endpoint.trim();
        const params = new URLSearchParams({
            pagina: 1,
            orden: ordenDefault,
            direccion: "asc",
            filas: -1,
            buscar: '',
            categoria: 0,
            sin_acciones: 1
        });
        return `${baseUrl}?${params.toString()}`;
    }

    // ============================================
    // FUNCIÓN PARA OBTENER ENCABEZADOS
    // ============================================
    function obtenerEncabezados() {
        const encabezados = [];
        document.querySelectorAll('#tablaPro thead th').forEach((th, index) => {
            if (index < document.querySelectorAll('#tablaPro thead th').length - 1) {
                encabezados.push(th.innerText.replace('↑↓', '').trim());
            }
        });
        return encabezados;
    }

    // ============================================
    // FUNCIÓN PARA OBTENER TÍTULO
    // ============================================
    function obtenerTitulo() {
        return document.querySelector('.card-header h4')?.innerText || 'Listado';
    }

    // ============================================
    // FUNCIÓN CARGAR TABLA
    // ============================================
    function cargarTabla() {
        const urlFinal = construirUrlVista();

        console.log("📡 URL FINAL:", urlFinal);
        console.log("📊 Página solicitada:", pagina);
        console.log("🔍 Búsqueda:", busqueda);
        console.log("📏 Filas:", filas);

        fetch(urlFinal)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("📦 Datos recibidos del servidor:", data);

                if (data.html) {
                    document.querySelector("#tablaProBody").innerHTML = data.html;
                    console.log("✅ Tabla actualizada");
                }

                const paginacionDiv = document.querySelector("#tablaProPaginacion");
                if (paginacionDiv) {
                    paginacionDiv.innerHTML = data.paginacion || "";
                    console.log("✅ Paginación actualizada:", data.paginacion);
                }

                const botones = document.querySelectorAll('.pagina-btn');
                let ultimaPagina = 1;

                if (botones.length > 0) {
                    botones.forEach(btn => {
                        const pageNum = parseInt(btn.dataset.page);
                        if (pageNum > ultimaPagina) ultimaPagina = pageNum;
                    });

                    console.log("📄 Última página disponible:", ultimaPagina);

                    if (pagina > ultimaPagina) {
                        console.log(`⚠️ Página actual (${pagina}) es mayor que última página (${ultimaPagina}). Reseteando a 1`);
                        pagina = 1;
                        cargarTabla();
                        return;
                    }

                    botones.forEach(btn => {
                        btn.classList.remove('active');
                        if (parseInt(btn.dataset.page) === pagina) {
                            btn.classList.add('active');
                        }
                    });
                } else {
                    console.log("📄 No hay botones de paginación (única página)");
                }
            })
            .catch(error => {
                console.error('❌ Error cargando la tabla:', error);
                document.querySelector("#tablaProBody").innerHTML =
                    '<tr><td colspan="7" class="text-center text-danger">Error al cargar datos</td></tr>';
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
            const nuevaPagina = parseInt(e.target.dataset.page);
            console.log("🖱️ Click en página:", nuevaPagina, "Actual era:", pagina);

            if (nuevaPagina !== pagina) {
                pagina = nuevaPagina;
                cargarTabla();
            } else {
                console.log("ℹ️ Misma página, ignorando");
            }
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
// EXPORTACIÓN A EXCEL (TODOS LOS REGISTROS - SIN FILTROS)
// ============================================
document.getElementById("exportExcel")?.addEventListener("click", function () {
    console.log("📊 Exportando TODOS los registros a Excel (sin filtros)...");

    const urlTodos = construirUrlExportacion();
    const titulo = obtenerTitulo();
    const encabezados = obtenerEncabezados();

    const estilos = `
        <style>
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #000; padding: 4px; }
            th { background-color: #f2f2f2; }
        </style>
    `;

    fetch(urlTodos)
        .then(response => response.json())
        .then(data => {
            let tablaCompleta = '<table>';
            tablaCompleta += '<thead><tr>';
            encabezados.forEach(th => {
                tablaCompleta += `<th>${th}</th>`;
            });
            tablaCompleta += '</tr></thead>';
            tablaCompleta += '<tbody>';
            tablaCompleta += data.html;
            tablaCompleta += '</tbody></table>';

            const htmlCompleto = `
                <html>
                    <head>
                        <meta charset="UTF-8">
                        <title>Exportación ${titulo}</title>
                        ${estilos}
                    </head>
                    <body>
                        <h2>${titulo} - TODOS LOS REGISTROS</h2>
                        ${tablaCompleta}
                    </body>
                </html>
            `;

            // CAMBIO 1: Tipo MIME para Excel moderno
            const blob = new Blob([htmlCompleto], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            
            // CAMBIO 2: Extensión .xlsx
            link.download = `${titulo.toLowerCase().replace(/\s+/g, '_')}_completo_${new Date().toISOString().slice(0, 10)}.xlsx`;
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(link.href);
        })
        .catch(error => {
            console.error('❌ Error exportando a Excel:', error);
            alert('Error al exportar a Excel');
        });
});
    // ============================================
    // EXPORTACIÓN IMPRESIÓN (TODOS LOS REGISTROS - SIN FILTROS)
    // ============================================
    document.getElementById("exportPrint")?.addEventListener("click", function () {
        console.log("🖨️ Preparando impresión de TODOS los registros (sin filtros)...");

        const urlTodos = construirUrlExportacion();
        const titulo = obtenerTitulo();
        const encabezados = obtenerEncabezados();

        fetch(urlTodos)
            .then(response => response.json())
            .then(data => {
                let tablaCompleta = '<table>';
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
                        <title>Imprimir ${titulo}</title>
                        <style>
                            body { font-family: Arial, sans-serif; padding: 20px; } 
                            table { border-collapse: collapse; width: 100%; } 
                            th, td { border: 1px solid #000; padding: 8px; } 
                            th { background-color: #f2f2f2; }
                        </style>
                    </head>
                    <body>
                        <h2>${titulo} - TODOS LOS REGISTROS</h2>
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
                console.error('❌ Error preparando impresión:', error);
                alert('Error al preparar la impresión');
            });
    });

    // ============================================
    // EXPORTACIÓN PDF (TODOS LOS REGISTROS - SIN FILTROS)
    // ============================================
    document.getElementById("exportPDF")?.addEventListener("click", function () {
        console.log("📑 Exportando TODOS los registros a PDF (sin filtros)...");

        const urlTodos = construirUrlExportacion();
        const titulo = obtenerTitulo();
        const encabezados = obtenerEncabezados();

        // Mostrar indicador de carga
        const btnPDF = this;
        const textoOriginal = btnPDF.innerHTML;
        btnPDF.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generando PDF...';
        btnPDF.disabled = true;

        console.log("📡 URL PDF:", urlTodos);
        console.log("📋 Encabezados:", encabezados);

        fetch(urlTodos)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("📦 Datos recibidos para PDF:", data);

                if (!data.html || data.html.trim() === '') {
                    throw new Error('No hay datos para exportar');
                }

                console.log("📄 HTML recibido:", data.html.substring(0, 200) + "...");

                // Procesar los datos para PDF
                const filas = [];

                // Crear un elemento temporal para parsear el HTML
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data.html;

                // Buscar todas las filas (tr) directamente
                const trs = tempDiv.querySelectorAll('tr');

                console.log(`📊 Encontradas ${trs.length} filas en el HTML`);

                if (trs.length === 0) {
                    // Intentar parsear de otra forma si no encuentra tr
                    console.log("⚠️ No se encontraron <tr>, intentando parsear el HTML como texto plano");

                    // Dividir por </tr> para obtener filas individuales
                    const filasHtml = data.html.split('</tr>').filter(f => f.trim() !== '');

                    filasHtml.forEach(filaHtml => {
                        // Extraer celdas
                        const celdas = filaHtml.match(/<td[^>]*>(.*?)<\/td>/g) || [];
                        const filaData = [];

                        celdas.forEach((celda, index) => {
                            let valor = celda.replace(/<[^>]*>/g, '').trim();

                            // Determinar si es la columna Tienda (basado en el contenido)
                            if (valor.includes('✅')) {
                                valor = 'Sí';
                            } else if (valor.includes('❌')) {
                                valor = 'No';
                            } else {
                                // Limpiar solo iconos de acción, NO los de Sí/No
                                valor = valor.replace(/[🔍📝🗑️]/g, '').trim();
                            }

                            filaData.push(valor || '—');
                        });

                        if (filaData.length > 0) {
                            filas.push(filaData);
                        }
                    });
                } else {
                    // Procesar normalmente con querySelectorAll
                    trs.forEach(tr => {
                        const filaData = [];
                        const tds = tr.querySelectorAll('td');

                        tds.forEach(td => {
                            let valor = td.innerText.trim();

                            // Convertir ✅ a "Sí" y ❌ a "No" para el PDF
                            if (valor.includes('✅')) {
                                valor = 'Sí';
                            } else if (valor.includes('❌')) {
                                valor = 'No';
                            } else {
                                // Limpiar otros iconos
                                valor = valor.replace(/[🔍📝🗑️]/g, '').trim();
                            }

                            filaData.push(valor || '—');
                        });

                        if (filaData.length > 0) {
                            filas.push(filaData);
                        }
                    });
                }

                console.log(`✅ ${filas.length} filas procesadas para PDF`);

                if (filas.length > 0) {
                    console.log("📊 Primera fila:", filas[0]);
                }

                if (filas.length === 0) {
                    throw new Error('No se pudieron procesar las filas para el PDF');
                }

                // Verificar que el número de columnas coincide
                const columnasEsperadas = encabezados.length;
                const columnasReales = filas[0].length;

                console.log(`📏 Columnas esperadas: ${columnasEsperadas}, columnas reales: ${columnasReales}`);

                if (columnasReales !== columnasEsperadas) {
                    console.warn(`⚠️ Discrepancia en columnas: esperaba ${columnasEsperadas}, recibí ${columnasReales}`);

                    // Ajustar filas si es necesario
                    if (columnasReales > columnasEsperadas) {
                        filas.forEach(f => f.pop());
                    }
                }

                if (typeof window.jspdf === 'undefined') {
                    throw new Error('jsPDF no está cargado.');
                }

                const { jsPDF } = window.jspdf;
                const doc = new jsPDF({
                    orientation: 'landscape',
                    unit: 'mm'
                });

                // Título
                doc.setFontSize(14);
                doc.text(titulo + " - TODOS LOS REGISTROS", 14, 15);
                doc.setFontSize(10);
                doc.text(`Generado: ${new Date().toLocaleDateString()}`, 14, 22);

                // Configuración de la tabla
                doc.autoTable({
                    head: [encabezados],
                    body: filas,
                    startY: 25,
                    styles: {
                        fontSize: 8,
                        cellPadding: 2,
                        overflow: 'linebreak',
                        cellWidth: 'wrap'
                    },
                    headStyles: {
                        fillColor: [41, 128, 185],
                        textColor: 255,
                        fontSize: 9,
                        halign: 'center'
                    },
                    alternateRowStyles: {
                        fillColor: [245, 245, 245]
                    },
                    margin: { top: 30 },
                    didDrawPage: function (data) {
                        doc.setFontSize(8);
                        doc.text(
                            'Página ' + data.pageNumber,
                            data.settings.margin.left,
                            doc.internal.pageSize.height - 10
                        );
                    }
                });

                const nombreArchivo = titulo.toLowerCase()
                    .replace(/\s+/g, '_')
                    .replace(/[áéíóúñ]/g, function (c) {
                        const equivalencias = { 'á': 'a', 'é': 'e', 'í': 'i', 'ó': 'o', 'ú': 'u', 'ñ': 'n' };
                        return equivalencias[c] || c;
                    }) + '_completo_' +
                    new Date().toISOString().slice(0, 10) + '.pdf';

                doc.save(nombreArchivo);
                console.log("✅ PDF generado correctamente");
            })
            .catch(error => {
                console.error('❌ Error exportando a PDF:', error);
                alert('Error al exportar a PDF: ' + error.message);
            })
            .finally(() => {
                btnPDF.innerHTML = textoOriginal;
                btnPDF.disabled = false;
            });
    });
    // ============================================
    // CARGA INICIAL
    // ============================================
    cargarTabla();
});