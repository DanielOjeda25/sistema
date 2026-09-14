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
   "create() carga clientes y usuarios · store(req) valida los datos · Proyecto::create(data) persiste y redirige con mensaje success"],
  ["02_programador_ve_tareas", "DSD 2 · El Programador consulta tareas",
   "index() con visiblePara, filtros y paginación (15) · show(t) por Route Model Binding · 403 si puedeVer falla"],
  ["03_edita_tarea_tablero", "DSD 3 · Edición de tarea desde el tablero",
   "PATCH AJAX con JSON · 422 con errores / 200 con la tarea · el JS mueve la tarjeta sin recargar"],
  ["04_pm_elimina_tarea", "DSD 4 · El PM elimina una tarea",
   "DELETE AJAX · confirmación con modal propio · 200 {ok:true} y la tarjeta desaparece"],
  ["05_jefe_aprueba_solicitud", "DSD 5 · El Jefe aprueba una solicitud de cambio",
   "update(req, s) fija estado = aprobada · enum pendiente | aprobada | rechazada · redirect con success"],
  ["06_po_gestiona_entregables", "DSD 6 · El PO gestiona los entregables IA",
   "update(req, e) acepta borrador | revisado | aprobado · redirect al listado con success"],
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

function laminaImagen(titulo, subtitulo, imgPath, textoFigura, numFigura) {
  const s = pres.addSlide();
  s.background = { color: "FFFFFF" };
  s.addText(titulo, { x: 0.5, y: 0.22, w: W - 1, h: 0.5, fontSize: 24, bold: true, color: TEXT, fontFace: "Segoe UI", margin: 0 });
  s.addText(subtitulo, { x: 0.5, y: 0.72, w: W - 1, h: 0.35, fontSize: 13, color: MUTED, fontFace: "Segoe UI", margin: 0 });

  const { w: pw, h: ph } = pngSize(imgPath);
  const maxH = 5.1, maxW = 12.2;
  let h = maxH, w = maxH * (pw / ph);
  if (w > maxW) { w = maxW; h = maxW * (ph / pw); }
  s.addImage({ path: imgPath, x: (W - w) / 2, y: 1.15 + (5.1 - h) / 2, w, h });

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
for (const [file, titulo, desc] of dsds) {
  laminaImagen(titulo, "Diseño detallado del caso de uso (PlantUML, generado desde el código real)",
    path.join(HD, file + ".png"), desc, null);
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
