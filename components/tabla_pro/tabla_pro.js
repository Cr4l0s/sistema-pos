document.addEventListener("DOMContentLoaded", function () {
    const tabla = document.querySelector("#tablaPro");
    if (!tabla) return;

    let endpoint = tabla.dataset.endpoint;
    const ordenDefault = tabla.dataset.ordenDefault || 'id';

    console.log("🔍 Endpoint original:", endpoint);

    // Verificar si globalIdPais existe, si no, crearlo
    if (typeof globalIdPais === 'undefined') {
        window.globalIdPais = 0;
        console.log("🆕 globalIdPais no existía, creado con valor:", globalIdPais);
    } else {
        console.log("🔍 VERIFICACIÓN GLOBAL - typeof globalIdPais:", typeof globalIdPais);
        console.log("🔍 VERIFICACIÓN GLOBAL - globalIdPais:", globalIdPais);
    }

    if (window.location.href.includes('inicio_pais')) {
        endpoint = "components/tabla_pro/tabla_endpoint_paises.php";
        console.log("🔍 Endpoint forzado para países:", endpoint);
    }

    let pagina = 1;
    let orden = ordenDefault;
    let direccion = "asc";
    let filas = 10;
    let busqueda = "";
    let categoria = 0;

    function getBaseUrl() {
        const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
        return window.location.origin + (isLocalhost ? '/practica_sventas_desa/' : '/');
    }

    function construirUrlVista() {
        const baseUrl = getBaseUrl() + endpoint.trim();

        console.log("🔍 URL BASE:", baseUrl);

        const params = {
            pagina: pagina,
            orden: orden,
            direccion: direccion,
            filas: filas,
            buscar: busqueda,
            categoria: categoria
        };

        if (typeof globalIdPais !== 'undefined' && globalIdPais > 0) {
            params.idPais = globalIdPais;
            console.log("🌍 Usando globalIdPais:", globalIdPais);
        } else {
            console.log("⚠️ globalIdPais NO DEFINIDO o es 0");
        }

        console.log("📦 Parámetros:", params);

        const queryString = Object.keys(params)
            .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`)
            .join('&');

        const urlFinal = `${baseUrl}?${queryString}`;

        console.log("🔍 URL FINAL:", urlFinal);

        return urlFinal;
    }

    function construirUrlExportacion() {
        const baseUrl = getBaseUrl() + endpoint.trim();

        const params = {
            pagina: 1,
            orden: ordenDefault,
            direccion: "asc",
            filas: -1,
            buscar: '',
            categoria: 0,
            sin_acciones: 1
        };

        if (typeof globalIdPais !== 'undefined' && globalIdPais > 0) {
            params.idPais = globalIdPais;
        }

        const queryString = Object.keys(params)
            .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`)
            .join('&');

        return `${baseUrl}?${queryString}`;
    }

    function obtenerEncabezados() {
        const encabezados = [];
        document.querySelectorAll('#tablaPro thead th').forEach((th, index) => {
            if (index < document.querySelectorAll('#tablaPro thead th').length - 1) {
                encabezados.push(th.innerText.replace('↑↓', '').trim());
            }
        });
        return encabezados;
    }

    function obtenerTitulo() {
        return document.querySelector('.card-header h4')?.innerText || 'Listado';
    }

    function cargarTabla() {
        console.log("🚀 CARGANDO TABLA - Página:", pagina);
        const urlFinal = construirUrlVista();

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

    const buscarInput = document.querySelector("#buscarTabla");
    if (buscarInput) {
        buscarInput.addEventListener("keyup", function (e) {
            busqueda = e.target.value;
            pagina = 1;
            cargarTabla();
        });
    }

    const filasSelect = document.querySelector("#filasTabla");
    if (filasSelect) {
        filasSelect.addEventListener("change", function (e) {
            filas = parseInt(e.target.value);
            pagina = 1;
            cargarTabla();
        });
    }

    const filtroCategoria = document.querySelector("#filtroCategoria");
    if (filtroCategoria) {
        categoria = parseInt(filtroCategoria.value) || 0;
        filtroCategoria.addEventListener("change", function (e) {
            categoria = parseInt(e.target.value) || 0;
            pagina = 1;
            cargarTabla();
        });
    }

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

    document.addEventListener("click", function (e) {
        const th = e.target.closest(".sortable");
        if (th) {
            orden = th.dataset.col;
            direccion = direccion === "asc" ? "desc" : "asc";
            pagina = 1;
            cargarTabla();
        }
    });

    document.getElementById("exportExcel")?.addEventListener("click", function () {
        console.log("📊 Exportando a Excel...");
        const urlTodos = construirUrlExportacion();
        const titulo = obtenerTitulo();
        const encabezados = obtenerEncabezados();
        const estilos = `<style>table{border-collapse:collapse;width:100%}th,td{border:1px solid #000;padding:4px}th{background-color:#f2f2f2}</style>`;

        fetch(urlTodos)
            .then(response => response.json())
            .then(data => {
                let tablaCompleta = '<table><thead><tr>';
                encabezados.forEach(th => tablaCompleta += `<th>${th}</th>`);
                tablaCompleta += '</tr></thead><tbody>' + data.html + '</tbody></tr>';
                const htmlCompleto = `<html><head><meta charset="UTF-8"><title>Exportación ${titulo}</title>${estilos}</head><body><h2>${titulo} - TODOS LOS REGISTROS</h2>${tablaCompleta}</body></html>`;
                const blob = new Blob([htmlCompleto], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = `${titulo.toLowerCase().replace(/\s+/g, '_')}_completo_${new Date().toISOString().slice(0, 10)}.xlsx`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(link.href);
            })
            .catch(error => { console.error('❌ Error:', error); alert('Error al exportar a Excel'); });
    });

    document.getElementById("exportPrint")?.addEventListener("click", function () {
        console.log("🖨️ Preparando impresión...");
        const urlTodos = construirUrlExportacion();
        const titulo = obtenerTitulo();
        const encabezados = obtenerEncabezados();

        fetch(urlTodos)
            .then(response => response.json())
            .then(data => {
                let tablaCompleta = '<table><thead><tr>';
                encabezados.forEach(th => tablaCompleta += `<th>${th}</th>`);
                tablaCompleta += '</tr></thead><tbody>' + data.html + '</tbody></table>';
                const ventana = window.open('', '_blank');
                if (!ventana) { alert("Permite los pop-ups para este sitio."); return; }
                ventana.document.write(`<!DOCTYPE html><html><head><title>Imprimir ${titulo}</title><style>body{font-family:Arial;padding:20px}table{border-collapse:collapse;width:100%}th,td{border:1px solid #000;padding:8px}th{background-color:#f2f2f2}</style></head><body><h2>${titulo} - TODOS LOS REGISTROS</h2>${tablaCompleta}<script>window.onload=function(){setTimeout(function(){window.print();},500)};window.onafterprint=function(){window.close()};setTimeout(function(){window.close()},30000);<\/script></body></html>`);
                ventana.document.close();
            })
            .catch(error => { console.error('❌ Error:', error); alert('Error al preparar la impresión'); });
    });

    document.getElementById("exportPDF")?.addEventListener("click", function () {
        console.log("📑 Exportando a PDF...");
        const urlTodos = construirUrlExportacion();
        const titulo = obtenerTitulo();
        const encabezados = obtenerEncabezados();
        const btnPDF = this;
        const textoOriginal = btnPDF.innerHTML;
        btnPDF.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Generando PDF...';
        btnPDF.disabled = true;

        fetch(urlTodos)
            .then(response => response.json())
            .then(data => {
                const filas = [];
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data.html;
                const trs = tempDiv.querySelectorAll('tr');
                trs.forEach(tr => {
                    const filaData = [];
                    const tds = tr.querySelectorAll('td');
                    tds.forEach(td => {
                        let valor = td.innerText.trim();
                        if (valor.includes('✅')) valor = 'Sí';
                        else if (valor.includes('❌')) valor = 'No';
                        else valor = valor.replace(/[🔍📝🗑️]/g, '').trim();
                        filaData.push(valor || '—');
                    });
                    if (filaData.length > 0) filas.push(filaData);
                });

                const { jsPDF } = window.jspdf;
                const doc = new jsPDF({ orientation: 'landscape', unit: 'mm' });
                doc.setFontSize(14);
                doc.text(titulo + " - TODOS LOS REGISTROS", 14, 15);
                doc.setFontSize(10);
                doc.text(`Generado: ${new Date().toLocaleDateString()}`, 14, 22);
                doc.autoTable({ head: [encabezados], body: filas, startY: 25, styles: { fontSize: 8, cellPadding: 2 }, headStyles: { fillColor: [41, 128, 185], textColor: 255 }, margin: { top: 30 } });
                const nombreArchivo = titulo.toLowerCase().replace(/\s+/g, '_').replace(/[áéíóúñ]/g, c => ({ 'á': 'a', 'é': 'e', 'í': 'i', 'ó': 'o', 'ú': 'u', 'ñ': 'n' }[c] || c)) + '_completo_' + new Date().toISOString().slice(0, 10) + '.pdf';
                doc.save(nombreArchivo);
            })
            .catch(error => { console.error('❌ Error:', error); alert('Error al exportar a PDF: ' + error.message); })
            .finally(() => { btnPDF.innerHTML = textoOriginal; btnPDF.disabled = false; });
    });

    cargarTabla();
});