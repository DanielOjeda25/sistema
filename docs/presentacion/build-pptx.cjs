// Genera docs/presentacion/CRUZNEGRA_DSD_CU_Reales.pptx
// Uso: node docs/presentacion/build-pptx.js   (desde la raiz del proyecto)
const pptxgen = require(process.env.PPTXGENJS_PATH || "pptxgenjs");
const fs = require("fs");
const path = require("path");

const ROOT = path.resolve(__dirname, "../..");
const HD = path.join(ROOT, "docs/dsd/png-hd");
const CU = path.join(ROOT, "docs/cu-reales/png");

// Paleta: fondo oscuro para portada/cierre, blanco para contenido
const BG_DARK = "0F172A";
const PRIMARY = "4F46E5";
const ACCENT = "00B87D";
const TEXT = "1F2937";
const MUTED = "64748B";

// dimensiones de un PNG (IHDR)
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

const cus = [
  ["cu1_pasos", "CU Real 1 · El PM crea un proyecto",
   "Listado → modal de creación → formulario completo → guardado con mensaje de éxito"],
  ["cu2_pasos", "CU Real 2 · El Programador consulta tareas",
   "Listado con relaciones → filtro por estado Pendiente → ficha de detalle"],
  ["cu3_pasos", "CU Real 3 · Edición desde el tablero",
   "Tarjeta → cambio de estado a En progreso → tarjeta movida por AJAX"],
  ["cu4_pasos", "CU Real 4 · Eliminación de tarea",
   "Tablero → modal de edición → confirmación propia → tarjeta eliminada"],
  ["cu5_pasos", "CU Real 5 · Aprobación de una solicitud",
   "Listado → modal Aprobada → flash de éxito y fila actualizada"],
  ["cu6_pasos", "CU Real 6 · Gestión de entregables IA",
   "Listado → modal Aprobado → listado actualizado"],
];

const pres = new pptxgen();
pres.layout = "LAYOUT_WIDE";
pres.author = "Equipo CRUZNEGRA";
pres.title = "CRUZNEGRA — DSD y Casos de Uso Reales";

// ---- portada (oscura)
let s = pres.addSlide();
s.background = { color: BG_DARK };
s.addText("CRUZNEGRA", { x: 0, y: 2.1, w: W, h: 1.1, align: "center", fontSize: 60, bold: true, color: "FFFFFF", fontFace: "Segoe UI" });
s.addText("DSD y Casos de Uso Reales", { x: 0, y: 3.2, w: W, h: 0.7, align: "center", fontSize: 28, color: ACCENT, fontFace: "Segoe UI" });
s.addText("Sistema de Gestión Interna · Laravel 12 · Capturas de septiembre 2026", { x: 0, y: 4.0, w: W, h: 0.5, align: "center", fontSize: 15, color: "94A3B8", fontFace: "Segoe UI" });

// ---- lámina por DSD (fondo blanco, diagrama HD centrado)
for (const [file, titulo, desc] of dsds) {
  const slide = pres.addSlide();
  slide.background = { color: "FFFFFF" };
  slide.addText(titulo, { x: 0.5, y: 0.25, w: W - 1, h: 0.55, fontSize: 28, bold: true, color: TEXT, fontFace: "Segoe UI", margin: 0 });
  slide.addText(desc, { x: 0.5, y: 0.85, w: W - 1, h: 0.4, fontSize: 14, color: MUTED, fontFace: "Segoe UI", margin: 0 });

  const img = path.join(HD, file + ".png");
  const { w: pw, h: ph } = pngSize(img);
  const maxH = 5.6, maxW = 12.2;
  let h = maxH, w = maxH * (pw / ph);
  if (w > maxW) { w = maxW; h = maxW * (ph / pw); }
  slide.addImage({ path: img, x: (W - w) / 2, y: 1.4 + (5.6 - h) / 2, w, h });

  slide.addText("Diseño detallado — generado con PlantUML desde el código real", { x: 0.5, y: H - 0.42, w: W - 1, h: 0.3, fontSize: 11, color: MUTED, fontFace: "Segoe UI", align: "right", margin: 0 });
}

// ---- separador de sección (oscuro)
s = pres.addSlide();
s.background = { color: BG_DARK };
s.addText("Casos de uso reales", { x: 0, y: 3.0, w: W, h: 0.9, align: "center", fontSize: 44, bold: true, color: "FFFFFF", fontFace: "Segoe UI" });
s.addText("Los mismos CU ejecutados en la web real, paso a paso", { x: 0, y: 3.9, w: W, h: 0.5, align: "center", fontSize: 18, color: ACCENT, fontFace: "Segoe UI" });

// ---- lámina por CU (storyboard completo)
for (const [file, titulo, desc] of cus) {
  const slide = pres.addSlide();
  slide.background = { color: "FFFFFF" };
  slide.addText(titulo, { x: 0.5, y: 0.25, w: W - 1, h: 0.55, fontSize: 28, bold: true, color: TEXT, fontFace: "Segoe UI", margin: 0 });
  slide.addText(desc, { x: 0.5, y: 0.85, w: W - 1, h: 0.4, fontSize: 14, color: MUTED, fontFace: "Segoe UI", margin: 0 });

  const img = path.join(CU, file + ".png");
  const { w: pw, h: ph } = pngSize(img);
  const maxH = 5.55, maxW = 12.2;
  let h = maxH, w = maxH * (pw / ph);
  if (w > maxW) { w = maxW; h = maxW * (ph / pw); }
  slide.addImage({ path: img, x: (W - w) / 2, y: 1.35 + (5.55 - h) / 2, w, h });

  slide.addText("Capturas reales de localhost:8000 — cada número corresponde a un mensaje del DSD", { x: 0.5, y: H - 0.42, w: W - 1, h: 0.3, fontSize: 11, color: MUTED, fontFace: "Segoe UI", align: "right", margin: 0 });
}

// ---- cierre (oscuro)
s = pres.addSlide();
s.background = { color: BG_DARK };
s.addText("Gracias", { x: 0, y: 3.0, w: W, h: 0.9, align: "center", fontSize: 48, bold: true, color: "FFFFFF", fontFace: "Segoe UI" });
s.addText("github.com/DanielOjeda25/sistema · localhost:8000", { x: 0, y: 4.0, w: W, h: 0.5, align: "center", fontSize: 15, color: "94A3B8", fontFace: "Segoe UI" });

const out = path.join(__dirname, "CRUZNEGRA_DSD_CU_Reales.pptx");
pres.writeFile({ fileName: out }).then(() => console.log("listo:", out));
