// Genera docs/presentacion/CRUZNEGRA_DSD_CU_Reales.pptx
// Uso: NODE_PATH=$(npm root -g) node docs/presentacion/build-pptx.cjs
const pptxgen = require("pptxgenjs");
const fs = require("fs");
const path = require("path");

const ROOT = path.resolve(__dirname, "../..");
const HD = path.join(ROOT, "docs/dsd/png-hd");
const CAP = path.join(ROOT, "docs/cu-reales/capturas");

// Paleta: fondo oscuro para portada/cierre/separador, blanco para contenido
const BG_DARK = "0F172A";
const ACCENT = "00B87D";
const TEXT = "1F2937";
const MUTED = "64748B";

function pngSize(file) {
  const b = fs.readFileSync(file);
  return { w: b.readUInt32BE(16), h: b.readUInt32BE(20) };
}

const W = 13.33, H = 7.5;

const dsds = [
  ["01_pm_crea_proyecto", "DSD 1 · El PM crea un proyecto",
   "El PM abre el formulario de creación y el sistema prepara los desplegables: la lista de clientes y la de usuarios que pueden actuar como Project Manager (mensajes create() y las consultas a Cliente y User). Al enviar el formulario, el controller valida los datos (nombre, fechas, estado, cliente y PM obligatorios). Si algo falla, vuelve al formulario mostrando los errores; si está todo correcto, crea el proyecto, lo guarda en la base de datos y vuelve al listado con el mensaje «Proyecto creado correctamente.»",
   "create() · store(req) · validate · Proyecto::create(data) · redirect con success"],
  ["02_programador_ve_tareas", "DSD 2 · El Programador consulta tareas",
   "El Programador entra al listado de tareas. Primero el sistema determina qué puede ver: si el usuario tuviera rol Cliente, solo vería las tareas de su empresa (scope visiblePara). Luego aplica la búsqueda y el filtro de estado elegidos y devuelve las tareas paginadas de a 15, cada una con su proyecto y su responsable ya cargados. Al abrir el detalle de una tarea, el sistema la busca por su id (Route Model Binding), verifica que el usuario tenga permiso para verla —si no, responde 403—, carga el proyecto, el asignado y la solicitud de cambio asociada, y muestra la ficha completa.",
   "index() · visiblePara · when(q/estado) · paginate(15) · show(t) · puedeVer"],
  ["03_edita_tarea_tablero", "DSD 3 · Edición de tarea desde el tablero",
   "El PM abre una tarjeta del tablero: el modal «Editar tarea» se completa con los datos que la tarjeta guarda en su atributo data-tarea. Al cambiar el estado a «En progreso» y guardar, el navegador envía una petición PATCH con JSON al servidor, que valida los campos. Si la validación falla responde 422 con los errores; si es correcta, actualiza la tarea en la base de datos (update = fill + save) y responde 200 con la tarea ya actualizada y sus relaciones. El JavaScript entonces reemplaza la tarjeta y la mueve a su nueva columna sin recargar la página.",
   "update(req, t) · validate · update(data) · 422 con errores / 200 JSON"],
  ["04_pm_elimina_tarea", "DSD 4 · El PM elimina una tarea",
   "El PM pulsa «Eliminar» dentro del modal de la tarea y el sistema pide confirmación con un modal propio, no con el confirm() del navegador. Al confirmar, se envía DELETE /tareas/{id}: el sistema busca la tarea por su id y la borra de la base de datos con delete(). Responde 200 con {ok:true} y el JavaScript quita la tarjeta del tablero y actualiza los contadores de cada columna, sin recargar. Si la tarea no existiera, el Route Model Binding responde 404 antes de llegar al controller.",
   "destroy(req, t) · delete() : bool · 200 {ok:true} · 404 si no existe"],
  ["05_jefe_aprueba_solicitud", "DSD 5 · El Jefe aprueba una solicitud de cambio",
   "El Jefe abre el detalle de una solicitud de cambio, que muestra el proyecto afectado, quién la solicitó y su estado actual. Al editarla y fijar el estado en «aprobada», el controller valida los datos —el estado solo puede ser pendiente, aprobada o rechazada—, actualiza el registro en la base mediante fill(data) + save() y redirige al listado con el mensaje «Solicitud de cambio actualizada correctamente.»",
   "show(s) · update(req, s) · validate · update(data) · redirect con success"],
  ["06_po_gestiona_entregables", "DSD 6 · El PO gestiona los entregables IA",
   "El PO entra al listado de entregables IA, que muestra cada entregable con su proyecto, tipo, estado y quién lo generó (paginado de a 15). Al abrir uno, el sistema carga sus relaciones proyecto y generador. Al editarlo, el controller valida que el estado solo pueda tomar los valores borrador, revisado o aprobado —además del resto de los campos obligatorios—, actualiza el registro con update(data) y vuelve al listado con el mensaje de éxito.",
   "index() · show(e) · update(req, e) · validate · update(data) · redirect"],
];

// Cada CU: una lámina POR PASO, con "Figura N" y la acción que se ejecuta
const cus = [
  {
    titulo: "CU Real 1 · El PM crea un proyecto",
    archivos: ["cu1_paso1_listado.png", "cu1_paso2_modal.png", "cu1_paso3_completo.png", "cu1_paso4_guardado.png"],
    acciones: [
      "presiona «+ Nuevo Proyecto» en el listado; el controller ejecuta create() y carga la lista de clientes y usuarios para los desplegables",
      "observa el modal de creación «Nuevo Proyecto», que reemplaza a la página de formulario aparte",
      "completa nombre, descripción, fechas, cliente y Project Manager, y envía el formulario; store(req) valida el array de datos",
      "ve el mensaje «Proyecto creado correctamente.»: la validación pasó, Proyecto::create(data) guardó el registro y redirigió al listado",
    ],
  },
  {
    titulo: "CU Real 2 · El Programador consulta las tareas",
    archivos: ["cu2_paso1_listado.png", "cu2_paso2_filtro.png", "cu2_paso3_detalle.png"],
    acciones: [
      "abre el listado de tareas: index() trae las tareas con sus relaciones proyecto y asignado; el Programador solo tiene acción de lectura",
      "aplica el filtro de estado «Pendiente»: la consulta when(estado) actúa sobre el scope visiblePara, con paginación de 15",
      "abre el detalle con show(t): Route Model Binding y carga de proyecto, asignado y solicitud de cambio",
    ],
  },
  {
    titulo: "CU Real 3 · Edición de tarea desde el tablero",
    archivos: ["cu3_paso1_modal.png", "cu3_paso2_estado.png", "cu3_paso3_movida.png"],
    acciones: [
      "abre la tarjeta de la tarea en el tablero; el modal «Editar tarea» toma los datos del atributo data-tarea (JSON)",
      "cambia el estado a «En progreso» y envía PATCH /tareas/{id} con JSON (Accept: application/json)",
      "recibe la respuesta 200 con la tarea y sus relaciones: el JS la mueve a la columna En progreso sin recargar la página",
    ],
  },
  {
    titulo: "CU Real 4 · El PM elimina una tarea",
    archivos: ["cu4_paso1_tablero.png", "cu4_paso2_modal.png", "cu4_paso3_confirmar.png", "cu4_paso4_eliminada.png"],
    acciones: [
      "abre la tarjeta a eliminar en el tablero de tareas",
      "pulsa «Eliminar» dentro del modal de edición: dispara DELETE /tareas/{id} por AJAX",
      "confirma en el modal propio del sistema, no en el confirm() nativo del navegador",
      "ve la respuesta 200 {ok:true}: la tarjeta desaparece del tablero y el contador baja sin recargar",
    ],
  },
  {
    titulo: "CU Real 5 · El Jefe aprueba una solicitud de cambio",
    archivos: ["cu5_paso1_listado.png", "cu5_paso2_aprobada.png", "cu5_paso3_guardada.png"],
    acciones: [
      "revisa el listado de solicitudes de cambio con su proyecto, solicitante, estado y prioridad",
      "abre la edición en el modal y fija estado = «aprobada» (enum: pendiente | aprobada | rechazada)",
      "guarda: update(req, s) con fill(data) + save() redirige al listado con mensaje de éxito y la fila pasa a Aprobada",
    ],
  },
  {
    titulo: "CU Real 6 · El PO gestiona los entregables IA",
    archivos: ["cu6_paso1_listado.png", "cu6_paso2_aprobado.png", "cu6_paso3_guardado.png"],
    acciones: [
      "abre el listado de entregables IA con su proyecto, tipo, estado y generador",
      "abre el modal de edición; el estado solo admite borrador, revisado o aprobado; fija «Aprobado»",
      "guarda: el listado muestra «Borrador de manual de usuario» como Aprobado",
    ],
  },
];

const pres = new pptxgen();
pres.layout = "LAYOUT_WIDE";
pres.author = "Equipo CRUZNEGRA";
pres.title = "CRUZNEGRA — DSD y Casos de Uso Reales";

function laminaImagen(titulo, subtitulo, imgPath, textoFigura, numFigura, descLegible) {
  const s = pres.addSlide();
  s.background = { color: "FFFFFF" };
  s.addText(titulo, { x: 0.5, y: 0.2, w: W - 1, h: 0.5, fontSize: 24, bold: true, color: TEXT, fontFace: "Segoe UI", margin: 0 });

  const conDesc = !!descLegible;
  const maxH = conDesc ? 4.15 : 5.1;
  const imgY = conDesc ? 0.85 : 1.15;
  const { w: pw, h: ph } = pngSize(imgPath);
  let h = maxH, w = maxH * (pw / ph);
  if (w > 12.2) { w = 12.2; h = 12.2 * (ph / pw); }
  s.addImage({ path: imgPath, x: (W - w) / 2, y: imgY + (maxH - h) / 2, w, h });

  if (conDesc) {
    s.addText([
      { text: "Qué representa: ", options: { bold: true, color: "4F46E5" } },
      { text: descLegible, options: { color: TEXT } },
    ], { x: 0.6, y: 5.15, w: W - 1.2, h: 1.55, fontSize: 13.5, fontFace: "Segoe UI", align: "left", valign: "top", margin: 0, lineSpacingMultiple: 1.15 });
    s.addText(textoFigura, { x: 0.6, y: 6.85, w: W - 1.2, h: 0.3, fontSize: 11, color: MUTED, fontFace: "Segoe UI", align: "right", margin: 0 });
    return;
  }

  const runs = numFigura
    ? [{ text: `Figura ${numFigura}. `, options: { bold: true, color: "4F46E5" } }, { text: textoFigura, options: { color: TEXT } }]
    : textoFigura;
  s.addText(runs, { x: 0.7, y: 6.45, w: W - 1.4, h: 0.8, fontSize: 13, fontFace: "Segoe UI", align: "left", valign: "top", margin: 0 });
}

// ---- portada
let s = pres.addSlide();
s.background = { color: BG_DARK };
s.addText("CRUZNEGRA", { x: 0, y: 2.1, w: W, h: 1.1, align: "center", fontSize: 60, bold: true, color: "FFFFFF", fontFace: "Segoe UI" });
s.addText("DSD y Casos de Uso Reales", { x: 0, y: 3.2, w: W, h: 0.7, align: "center", fontSize: 28, color: ACCENT, fontFace: "Segoe UI" });
s.addText("Sistema de Gestión Interna · Laravel 12 · Capturas de septiembre 2026", { x: 0, y: 4.0, w: W, h: 0.5, align: "center", fontSize: 15, color: "94A3B8", fontFace: "Segoe UI" });

// ---- DSD (una lámina por diagrama, en alta resolución)
for (const [file, titulo, desc, mensajes] of dsds) {
  laminaImagen(titulo, "Diseño detallado del caso de uso", path.join(HD, file + ".png"), "Mensajes: " + mensajes, null, desc);
}

// ---- separador
s = pres.addSlide();
s.background = { color: BG_DARK };
s.addText("Casos de uso reales", { x: 0, y: 3.0, w: W, h: 0.9, align: "center", fontSize: 44, bold: true, color: "FFFFFF", fontFace: "Segoe UI" });
s.addText("Los mismos CU ejecutados en la web real: una lámina por paso", { x: 0, y: 3.9, w: W, h: 0.5, align: "center", fontSize: 18, color: ACCENT, fontFace: "Segoe UI" });

// ---- CU: una lámina por paso, figuras numeradas en forma consecutiva
let figura = 1;
cus.forEach(cu => {
  cu.archivos.forEach((archivo, i) => {
    const total = cu.archivos.length;
    const accion = cu.acciones[i];
    laminaImagen(
      `${cu.titulo}  ·  paso ${i + 1} de ${total}`,
      "Ejecución real en la web del sistema",
      path.join(CAP, archivo),
      accion.charAt(0).toUpperCase() + accion.slice(1) + ".",
      figura
    );
    figura++;
  });
});

// ---- cierre
s = pres.addSlide();
s.background = { color: BG_DARK };
s.addText("Gracias", { x: 0, y: 3.0, w: W, h: 0.9, align: "center", fontSize: 48, bold: true, color: "FFFFFF", fontFace: "Segoe UI" });
s.addText("github.com/DanielOjeda25/sistema · localhost:8000", { x: 0, y: 4.0, w: W, h: 0.5, align: "center", fontSize: 15, color: "94A3B8", fontFace: "Segoe UI" });

const out = path.join(__dirname, "CRUZNEGRA_DSD_CU_Reales.pptx");
pres.writeFile({ fileName: out }).then(() => console.log("listo:", out, "| figuras totales:", figura - 1));
