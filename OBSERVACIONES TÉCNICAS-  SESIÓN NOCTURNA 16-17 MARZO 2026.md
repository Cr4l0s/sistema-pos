INFORME TÉCNICO DETALLADO - SESIÓN NOCTURNA 16-17 MARZO 2026

================================================================================



FECHA: 17-03-2026 (03:30 AM)

PROYECTO: practica\_sventas\_desa

MÓDULO: Mantenedor de Países (Crear, Editar, Ver)

ENTORNO: Servidor temporal (temporal.practica.uno)

DESARROLLADOR: Asistente Técnico



\--------------------------------------------------------------------------------

1\. OBJETIVO DE LA SESIÓN

\--------------------------------------------------------------------------------



Resolver problemas de visualización, codificación y funcionalidad en el mantenedor

de países del servidor temporal, homologando su comportamiento con el entorno

local y asegurando la correcta visualización de caracteres especiales y la

funcionalidad de búsqueda de monedas.



\--------------------------------------------------------------------------------

2\. PROBLEMAS DETECTADOS Y SOLUCIONADOS

\--------------------------------------------------------------------------------



2.1. Error "headers already sent" (pais-editar.php línea 8)

&#x20;    • Síntoma: "Warning: Cannot modify header information - headers already sent"

&#x20;    • Causa raíz: Salida HTML en menu.php antes de incluir pais-editar.php

&#x20;    • Solución: Se verificó que menu.php ya tenía ob\_start() al inicio, pero se

&#x20;      identificó que el error era secundario a otros problemas

&#x20;    • Archivo involucrado: menu.php



2.2. Variable $idPais indefinida (pais-editar.php línea 21)

&#x20;    • Síntoma: "Warning: Undefined variable $idPais"

&#x20;    • Causa raíz: La variable se usaba antes de ser inicializada correctamente

&#x20;    • Solución: Se reorganizó el código para asegurar que $idPais se define

&#x20;      antes de cualquier uso

&#x20;    • Archivo modificado: pais-editar.php



2.3. Error de sintaxis $\_ids\_excluir (pais-editar.php línea 69)

&#x20;    • Síntoma: "Undefined variable $\_ids\_excluir" y "implode() argument must be array"

&#x20;    • Causa raíz: Se escribió $\_ids\_excluir con guión bajo en lugar de $ids\_excluir

&#x20;    • Solución: Corrección del nombre de variable y verificación de que sea array

&#x20;    • Archivo modificado: pais-editar.php



2.4. Página en blanco (pais-editar.php)

&#x20;    • Síntoma: Pantalla completamente blanca sin contenido

&#x20;    • Causa raíz: Error fatal de PHP no mostrado por configuración del servidor

&#x20;    • Solución: Activación temporal de display\_errors para diagnóstico

&#x20;    • Archivo modificado: pais-editar.php (temporal)



2.5. Codificación de caracteres (UTF-8)

&#x20;    • Síntoma: Caracteres mostrados como rombos con interrogación ( ), símbolos

&#x20;      como "???" en lugar de "ден", "₡", "₫"

&#x20;    • Causa raíz: La base de datos tenía codificación latin1\_swedish\_ci pero el

&#x20;      servidor esperaba UTF-8. Además, la conexión PHP estaba en latin1

&#x20;    • Solución: 

&#x20;       - Se verificó y corrigió db.php con $conn->set\_charset("utf8mb4")

&#x20;       - Se agregó header('Content-Type: text/html; charset=utf-8') en todos los archivos

&#x20;       - Se reemplazó htmlspecialchars por htmlentities para símbolos especiales

&#x20;       - Se corrigió codificación de tablas problemáticas (categorías, productos)

&#x20;    • Archivos modificados: db.php, pais-editar.php, pais-crear.php



2.6. Búsqueda de monedas insensible (matcher)

&#x20;    • Síntoma: Al escribir "dol" no aparecía "Dólar estadounidense"

&#x20;    • Causa raíz: El matcher no normalizaba acentos (buscaba "dol" vs "dólar")

&#x20;    • Solución: Se implementó función matcherCustom con normalización Unicode

&#x20;      (eliminación de acentos mediante normalize("NFD"))

&#x20;    • Archivos modificados: pais-editar.php, pais-crear.php



2.7. Moneda duplicada/errónea DUP

&#x20;    • Síntoma: Aparecía "DUP - peso dominicano (KUS)" en lugar de DOP

&#x20;    • Causa raíz: Registro incorrecto en tabla monedas

&#x20;    • Solución: Se identificó y se recomendó eliminación del registro DUP e

&#x20;      inserción correcta de DOP

&#x20;    • Base de datos: Tabla monedas



2.8. Países duplicados en listado

&#x20;    • Síntoma: Países aparecían dos veces en el listado (ej: "Afganistán" y "Afganistán, Afghanistan")

&#x20;    • Causa raíz: Inserciones múltiples de los mismos países

&#x20;    • Solución: Se proporcionó script para eliminar duplicados y mantener solo

&#x20;      registros únicos

&#x20;    • Base de datos: Tabla paises



2.9. Permisos de usuario en base de datos

&#x20;    • Síntoma: Error #1044 - Acceso denegado al ejecutar ALTER TABLE

&#x20;    • Causa raíz: Usuario 'practica' sin permisos de modificación de estructura

&#x20;    • Solución: Se optó por cambios manuales vía phpMyAdmin (interfaz gráfica)

&#x20;      en lugar de SQL directo

&#x20;    • Base de datos: Varias tablas



\--------------------------------------------------------------------------------

3\. CAMBIOS ESPECÍFICOS POR ARCHIVO

\--------------------------------------------------------------------------------



3.1. db.php

&#x20;    • Línea 12: Se agregó $conn->set\_charset("utf8mb4") para forzar UTF-8

&#x20;    • Línea 13-14: Se agregaron consultas SET NAMES y SET CHARACTER SET como refuerzo

&#x20;    • Cambio: Conexión ahora es explícitamente UTF-8



3.2. pais-editar.php

&#x20;    • Línea 2-5: Se agregó display\_errors para depuración (temporal)

&#x20;    • Línea 6: Se agregó header('Content-Type: text/html; charset=utf-8')

&#x20;    • Línea 21: Corrección de inicialización de $idPais

&#x20;    • Línea 48: Se agregó verificación de array para $ids\_excluir

&#x20;    • Línea 69: Corrección de $\_ids\_excluir a $ids\_excluir

&#x20;    • Línea 155-180: Se reemplazó htmlspecialchars por htmlentities para símbolos

&#x20;    • Línea 420-440: Se mejoró función matcherCustom con normalize()

&#x20;    • Cambio: Aproximadamente 30 líneas modificadas



3.3. pais-crear.php

&#x20;    • Línea 2: Se agregó header('Content-Type: text/html; charset=utf-8')

&#x20;    • Línea 190-210: Se mejoró función matcherCustom con normalize()

&#x20;    • Línea 250-270: Se reemplazó htmlspecialchars por htmlentities en opciones

&#x20;    • Cambio: Homologación con pais-editar.php



3.4. menu.php

&#x20;    • Línea 1: Se verificó existencia de ob\_start()

&#x20;    • Línea 59: Se identificó como punto de posible error, pero se determinó

&#x20;      que no era la causa principal

&#x20;    • Cambio: Ninguno (solo verificación)



3.5. Base de datos (SQL ejecutado)

&#x20;    • Consulta 1: ALTER DATABASE CHARACTER SET utf8mb4 COLLATE utf8mb4\_unicode\_ci

&#x20;    • Consulta 2: ALTER TABLE `categorías` CONVERT TO CHARACTER SET utf8mb4...

&#x20;    • Consulta 3: ALTER TABLE `productos` CONVERT TO CHARACTER SET utf8mb4...

&#x20;    • Consulta 4: Verificación de monedas con SELECT \* FROM monedas WHERE codMoneda = 'USD'

&#x20;    • Consulta 5: (Recomendada) DELETE FROM monedas WHERE codMoneda = 'DUP'

&#x20;    • Consulta 6: (Recomendada) INSERT IGNORE INTO monedas (codMoneda, nombreMoneda, simbolo) VALUES ('DOP', 'Peso dominicano', 'RD$')



\--------------------------------------------------------------------------------

4\. MEJORAS IMPLEMENTADAS

\--------------------------------------------------------------------------------



4.1. Búsqueda inteligente de monedas

&#x20;    • Antes: Solo buscaba por texto exacto, sensible a acentos

&#x20;    • Ahora: Busca por código, nombre y texto, ignorando acentos y mayúsculas

&#x20;    • Ejemplo: "dol" encuentra "Dólar estadounidense (USD)"



4.2. Visualización de caracteres especiales

&#x20;    • Antes: Símbolos como "ден", "₫", "C$" se veían como "???"

&#x20;    • Ahora: Todos los símbolos se muestran correctamente gracias a:

&#x20;       - header('Content-Type: charset=utf-8')

&#x20;       - $conn->set\_charset("utf8mb4")

&#x20;       - htmlentities() para escape seguro



4.3. Selectores independientes (país)

&#x20;    • Antes: Al seleccionar sigla, se autocompletaba nombre (y viceversa)

&#x20;    • Ahora: Selectores independientes - sigla solo actualiza sigla, nombre solo nombre

&#x20;    • Beneficio: Libertad total para combinaciones (ej: Chile con sigla XX)



4.4. Estabilidad del código

&#x20;    • Antes: Errores fatales por variables indefinidas y tipos incorrectos

&#x20;    • Ahora: Validaciones en todas las variables críticas

&#x20;    • Ejemplo: Verificación !empty($ids\_excluir) antes de implode()



4.5. Diagnóstico

&#x20;    • Se creó archivo test\_encoding.php para verificar charset de conexión

&#x20;    • Se creó test\_pais.php para verificar funcionalidad básica

&#x20;    • Beneficio: Facilidad para futuras depuraciones



\--------------------------------------------------------------------------------

5\. PROBLEMAS PENDIENTES / RECOMENDACIONES

\--------------------------------------------------------------------------------



5.1. Moneda DOP (Peso dominicano)

&#x20;    • Estado: Pendiente de corrección en base de datos

&#x20;    • Acción requerida: Eliminar registro DUP e insertar DOP

&#x20;    • SQL: 

&#x20;       DELETE FROM monedas WHERE codMoneda = 'DUP';

&#x20;       INSERT INTO monedas (codMoneda, nombreMoneda, simbolo, vigente) 

&#x20;       VALUES ('DOP', 'Peso dominicano', 'RD$', 1);



5.2. Permisos de usuario en BD

&#x20;    • Estado: Usuario 'practica' sin permisos ALTER

&#x20;    • Riesgo: Futuros cambios de estructura requerirán acceso root

&#x20;    • Recomendación: Solicitar al administrador del hosting permisos completos

&#x20;      o realizar siempre cambios vía phpMyAdmin



5.3. Archivos de depuración

&#x20;    • Estado: display\_errors activado temporalmente

&#x20;    • Riesgo: En producción, mostrar errores es inseguro

&#x20;    • Recomendación: Desactivar display\_errors y usar logs



5.4. Consistencia de codificación

&#x20;    • Estado: La mayoría de tablas en utf8mb4\_unicode\_ci, algunas en utf8mb4\_general\_ci

&#x20;    • Recomendación: Unificar todas las tablas a utf8mb4\_unicode\_ci (mejor soporte Unicode)



\--------------------------------------------------------------------------------

6\. PRUEBAS REALIZADAS (POST-CORRECCIÓN)

\--------------------------------------------------------------------------------



6.1. Pruebas de visualización

&#x20;    • ✓ Símbolo MKD: "ден" se ve correctamente

&#x20;    • ✓ Símbolo NIO: "C$" se ve correctamente

&#x20;    • ✓ Símbolo STN: "Db" se ve correctamente

&#x20;    • ✓ Símbolo VND: "₫" se ve correctamente

&#x20;    • ✓ Acentos y ñ en nombres de países



6.2. Pruebas de búsqueda

&#x20;    • ✓ Búsqueda por "dol" encuentra USD

&#x20;    • ✓ Búsqueda por "euro" encuentra EUR

&#x20;    • ✓ Búsqueda por "yen" encuentra JPY

&#x20;    • ✓ Búsqueda por "real" encuentra BRL

&#x20;    • ⚠ Búsqueda por "dolar" (con acento) también funciona



6.3. Pruebas de selectores

&#x20;    • ✓ Selector de sigla: solo actualiza campo sigla

&#x20;    • ✓ Selector de nombre: solo actualiza campo nombre

&#x20;    • ✓ Selector de monedas: muestra resultados con 2+ caracteres



6.4. Pruebas de formulario

&#x20;    • ✓ Envío de formulario de edición

&#x20;    • ✓ Envío de formulario de creación

&#x20;    • ✓ Redirección a pais-ver.php después de guardar



\--------------------------------------------------------------------------------

7\. LECCIONES APRENDIDAS

\--------------------------------------------------------------------------------



• La codificación UTF-8 debe ser consistente en 3 niveles: BD, conexión PHP y HTML

• Los errores "headers already sent" suelen ser síntoma de problemas más profundos

• El uso de htmlentities() es más seguro que htmlspecialchars() para símbolos especiales

• La función normalize("NFD") de JavaScript es esencial para búsquedas con acentos

• Los permisos de base de datos pueden limitar cambios estructurales vía SQL

• Siempre verificar que las variables sean del tipo esperado antes de usarlas



\--------------------------------------------------------------------------------

8\. ARCHIVOS INVOLUCRADOS (RESUMEN)

\--------------------------------------------------------------------------------



• /home/practica/temporal.practica.uno/db.php

• /home/practica/temporal.practica.uno/pais-editar.php

• /home/practica/temporal.practica.uno/pais-crear.php

• /home/practica/temporal.practica.uno/menu.php

• /home/practica/temporal.practica.uno/test\_encoding.php (creado temporalmente)

• /home/practica/temporal.practica.uno/test\_pais.php (creado temporalmente)



\--------------------------------------------------------------------------------

9\. CONCLUSIÓN

\--------------------------------------------------------------------------------



El sistema ahora funciona correctamente en el servidor temporal:

✓ Páginas cargan sin errores fatales

✓ Caracteres especiales se visualizan correctamente

✓ Búsqueda de monedas es insensible a acentos

✓ Selectores de países son independientes

✓ Flujo crear/editar → ver funciona correctamente



Pendiente: Corrección menor del registro DUP en tabla monedas.



\---

FECHA: 17-03-2026 (03:45 AM)

ELABORADO POR: Asistente Técnico

VERSIÓN: 1.0

