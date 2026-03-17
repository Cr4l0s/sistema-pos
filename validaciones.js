/**
 * validaciones.js - Validaciones del lado del cliente
 * @author Sistema de Ventas
 * @version 1.0
 */

const Validaciones = {
    // VALIDACIONES PARA PRODUCTOS
    codigoBarras: function (codigo) {
        return /^[0-9]{13}$/.test(codigo);
    },

    precio: function (precio) {
        return /^[0-9]+(\.[0-9]{1,2})?$/.test(precio) && parseFloat(precio) > 0;
    },

    nombreProducto: function (nombre) {
        return /^[a-zA-ZáéíóúñÑ0-9\s]+$/.test(nombre);
    },

    stock: function (stock) {
        return /^[0-9]+$/.test(stock) && parseInt(stock) >= 0;
    },

    // VALIDACIONES PARA USUARIOS
    email: function (email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    },

    nombreUsuario: function (nombre) {
        return /^[a-zA-ZáéíóúñÑ\s]+$/.test(nombre);
    },

    // VALIDACIONES PARA PAÍSES
    siglaPais: function (sigla) {
        return /^[A-Z]{2}$/.test(sigla.toUpperCase());
    },

    nombrePais: function (nombre) {
        return /^[a-zA-ZáéíóúñÑ\s]+$/.test(nombre);
    },

    // ===== VALIDACIONES DE URLs =====
    validarURL: function(url) {
        return /^(https?|ftp):\/\/([a-z0-9-]+\.)+[a-z]{2,}(:\d+)?(\/[^\s]*)?$/i.test(url);
    },

    validarURLYouTube: function(url) {
        return /^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[a-zA-Z0-9_-]{11}$/.test(url);
    },

    validarURLImagen: function(url) {
        return /^(https?:\/\/).+\.(jpg|jpeg|png|gif|webp|bmp|svg)(\?.*)?$/i.test(url);
    },

    validarURLVideo: function(url) {
        return /^(https?:\/\/).+\.(mp4|webm|ogg|mov|avi|wmv)(\?.*)?$/i.test(url);
    },

    validarURLSegura: function(url) {
        return /^https:\/\/([a-z0-9-]+\.)+[a-z]{2,}(:\d+)?(\/[^\s]*)?$/i.test(url);
    },

    extraerDominio: function(url) {
        const match = url.match(/^(https?|ftp):\/\/([^\/]+)/);
        return match ? match[2] : null;
    },

    // VALIDACIONES GENERALES
    required: function (valor) {
        return valor && valor.trim().length > 0;
    },

    limpiar: function (input) {
        return input.replace(/[<>"']/g, '');
    }
};

// VALIDAR FORMULARIO DE PRODUCTO
function validarFormularioProducto() {
    const nombre = document.querySelector('input[name="nombre_producto"]')?.value;
    const codigo = document.querySelector('input[name="codigo_barras"]')?.value;
    const precioVenta = document.querySelector('input[name="precio_venta"]')?.value;
    const stockActual = document.querySelector('input[name="stock_actual"]')?.value;
    const precioCompras = document.querySelector('input[name="precio_compras"]')?.value;

    if (!Validaciones.required(nombre)) {
        alert('El nombre del producto es obligatorio');
        return false;
    }
    if (!Validaciones.nombreProducto(nombre)) {
        alert('El nombre contiene caracteres no válidos');
        return false;
    }

    if (codigo && !Validaciones.codigoBarras(codigo)) {
        alert('El código de barras debe tener 13 dígitos numéricos');
        return false;
    }

    if (!Validaciones.required(precioVenta)) {
        alert('El precio de venta es obligatorio');
        return false;
    }
    if (!Validaciones.precio(precioVenta)) {
        alert('El precio de venta no es válido');
        return false;
    }

    if (precioCompras && parseFloat(precioCompras) > 0 && !Validaciones.precio(precioCompras)) {
        alert('El precio de costo no es válido');
        return false;
    }

    if (stockActual && !Validaciones.stock(stockActual)) {
        alert('El stock actual debe ser un número entero positivo');
        return false;
    }

    if (parseFloat(precioVenta) <= parseFloat(precioCompras)) {
        alert('El precio de venta debe ser mayor al precio de costo');
        return false;
    }

    return true;
}

// VALIDAR FORMULARIO DE USUARIO
function validarFormularioUsuario() {
    const nombres = document.querySelector('input[name="nombres"]')?.value;
    const email = document.querySelector('input[name="email"]')?.value;
    const password = document.querySelector('input[name="password"]')?.value;

    if (!Validaciones.required(nombres)) {
        alert('El nombre es obligatorio');
        return false;
    }
    if (!Validaciones.nombreUsuario(nombres)) {
        alert('El nombre contiene caracteres no válidos');
        return false;
    }

    if (!Validaciones.required(email)) {
        alert('El email es obligatorio');
        return false;
    }
    if (!Validaciones.email(email)) {
        alert('El email no es válido');
        return false;
    }

    if (password && password.length < 6) {
        alert('La contraseña debe tener al menos 6 caracteres');
        return false;
    }

    return true;
}

// VALIDAR FORMULARIO DE PAÍS
function validarFormularioPais() {
    const sigla = document.querySelector('#siglaInput')?.value;
    const nombre = document.querySelector('#nombreInput')?.value;

    if (!Validaciones.required(sigla)) {
        alert('La sigla del país es obligatoria');
        return false;
    }
    if (!Validaciones.siglaPais(sigla)) {
        alert('La sigla debe tener 2 letras mayúsculas');
        return false;
    }

    if (!Validaciones.required(nombre)) {
        alert('El nombre del país es obligatorio');
        return false;
    }
    if (!Validaciones.nombrePais(nombre)) {
        alert('El nombre contiene caracteres no válidos');
        return false;
    }

    return true;
}

// VALIDAR FORMULARIO DE CATEGORÍA (NUEVO)
function validarFormularioCategoria() {
    const nombre = document.querySelector('input[name="nombre_categoria"]')?.value;
    const urlImagen = document.querySelector('input[name="url_imagen"]')?.value;

    if (!Validaciones.required(nombre)) {
        alert('El nombre de la categoría es obligatorio');
        return false;
    }

    if (urlImagen && !Validaciones.validarURLImagen(urlImagen)) {
        alert('La URL de la imagen no es válida. Formatos permitidos: jpg, jpeg, png, gif, webp, bmp, svg');
        return false;
    }

    return true;
}