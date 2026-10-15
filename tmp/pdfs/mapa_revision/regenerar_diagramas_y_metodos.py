from pathlib import Path
import shutil
import textwrap

from reportlab.pdfgen import canvas
from reportlab.lib.pagesizes import landscape, A4
from reportlab.lib.colors import HexColor, white
from pypdf import PdfReader


DIR = Path(r"C:\Users\dani_\OneDrive\Desktop\facu\02_Analisis_y_Diseno\ANALISIS")
DIAGRAMAS = DIR / "02_CruzNegra_Diagramas_CasosUso_Dominio.pdf"
DIAGRAMAS_BACKUP = DIR / "02_CruzNegra_Diagramas_CasosUso_Dominio_antes_actualizacion_codigo.pdf"
METODOS = DIR / "11_CruzNegra_Proyecto_Metodos_y_Funciones.pdf"

W, H = landscape(A4)
NAVY = HexColor("#172033")
BLUE = HexColor("#2557D6")
GREEN = HexColor("#00966D")
ORANGE = HexColor("#D65A19")
PURPLE = HexColor("#7A3FF2")
MUTED = HexColor("#667085")
TEXT = HexColor("#344054")
LINE = HexColor("#B8C1D1")
PALE = HexColor("#F7F9FA")
PALE_BLUE = HexColor("#EFF4FF")


def header(c, title, subtitle, page, total, section):
    c.setFillColor(NAVY)
    c.setFont("Helvetica-Bold", 15)
    c.drawString(47, 558, "CRUZNEGRA")
    c.setFillColor(GREEN)
    c.setFont("Helvetica", 7.2)
    c.drawString(47, 546, "Sistema de Gestión Interna - Documentación de Análisis y Diseño")
    c.setFillColor(MUTED)
    c.setFont("Helvetica", 7)
    c.drawRightString(795, 558, "Análisis y Diseño de Sistemas - Octubre 2026")
    c.drawRightString(795, 546, section)
    c.setStrokeColor(GREEN)
    c.setLineWidth(1)
    c.line(47, 535, 795, 535)
    c.setFillColor(NAVY)
    c.setFont("Helvetica-Bold", 16)
    c.drawString(47, 512, title)
    c.setFillColor(MUTED)
    c.setFont("Helvetica", 8)
    c.drawString(47, 498, subtitle)
    c.setFillColor(MUTED)
    c.setFont("Helvetica", 6.5)
    c.drawString(47, 12, section)
    c.drawRightString(795, 12, f"DOC-PRO-2026-011 - Página {page} de {total}")


def fit(c, text, width, font="Helvetica", size=6.4):
    if c.stringWidth(text, font, size) <= width:
        return text
    suffix = "..."
    while text and c.stringWidth(text + suffix, font, size) > width:
        text = text[:-1]
    return text + suffix


def entity(c, x, y, width, height, title, fields, highlight=False):
    c.setFillColor(PALE_BLUE if highlight else white)
    c.setStrokeColor(BLUE if highlight else LINE)
    c.setLineWidth(1.3 if highlight else 0.8)
    c.roundRect(x, y, width, height, 5, fill=1, stroke=1)
    hh = 21
    c.setFillColor(BLUE if highlight else GREEN)
    c.roundRect(x, y + height - hh, width, hh, 5, fill=1, stroke=0)
    c.rect(x, y + height - hh, width, hh - 5, fill=1, stroke=0)
    c.setFillColor(white)
    c.setFont("Helvetica-Bold", 7.5 if not highlight else 8.2)
    c.drawCentredString(x + width / 2, y + height - 14, title)
    c.setFillColor(TEXT)
    c.setFont("Helvetica", 6.2)
    yy = y + height - hh - 11
    for field in fields:
        c.drawString(x + 7, yy, fit(c, field, width - 14))
        yy -= 9.1


def relation(c, x1, y1, x2, y2, card1, card2, label):
    c.setStrokeColor(HexColor("#98A2B3"))
    c.setLineWidth(0.8)
    c.line(x1, y1, x2, y2)
    c.setFillColor(MUTED)
    c.setFont("Helvetica-Bold", 6.2)
    c.drawString(x1 + (3 if x2 >= x1 else -10), y1 + 3, card1)
    c.drawString(x2 + (-10 if x2 >= x1 else 3), y2 + 3, card2)
    c.setFillColor(GREEN)
    c.setFont("Helvetica-Oblique", 6.1)
    c.drawCentredString((x1 + x2) / 2, (y1 + y2) / 2 + 4, label)


ROLE_COLORS = {
    "Jefe": GREEN,
    "PM": BLUE,
    "PO": PURPLE,
    "Prog": ORANGE,
    "Cliente": HexColor("#C2415B"),
}


def use_case(c, x, y, w, h, code, title, roles):
    c.setFillColor(white)
    c.setStrokeColor(HexColor("#CCD4E0"))
    c.setLineWidth(0.9)
    c.roundRect(x, y, w, h, 12, fill=1, stroke=1)
    c.setFillColor(BLUE)
    c.setFont("Helvetica-Bold", 7)
    c.drawString(x + 10, y + h - 15, code)
    c.setFillColor(TEXT)
    c.setFont("Helvetica-Bold", 7.4)
    c.drawString(x + 10, y + h - 29, fit(c, title, w - 20, "Helvetica-Bold", 7.4))
    badge_x = x + 10
    for role in roles:
        bw = c.stringWidth(role, "Helvetica-Bold", 5.8) + 12
        c.setFillColor(ROLE_COLORS[role])
        c.roundRect(badge_x, y + 7, bw, 13, 6, fill=1, stroke=0)
        c.setFillColor(white)
        c.setFont("Helvetica-Bold", 5.8)
        c.drawCentredString(badge_x + bw / 2, y + 11, role)
        badge_x += bw + 4


def draw_use_cases(c):
    header(c, "Casos de uso actualizados", "Asociaciones y permisos verificados contra routes/web.php y controladores.", 1, 2,
           "Diagramas - Casos de uso y dominio")
    c.setFillColor(PALE)
    c.setStrokeColor(LINE)
    c.roundRect(47, 55, 748, 420, 9, fill=1, stroke=1)
    c.setFillColor(NAVY)
    c.setFont("Helvetica-Bold", 9)
    c.drawString(60, 456, "Sistema Cruz Negra - Módulo Proyectos")

    cases = [
        ("CU-01", "Crear proyecto", ["Jefe", "PM"]),
        ("CU-02", "Consultar proyectos visibles", ["Jefe", "PM", "PO", "Prog", "Cliente"]),
        ("CU-03", "Editar proyecto", ["Jefe", "PM"]),
        ("CU-04", "Eliminar proyecto", ["Jefe"]),
        ("CU-05", "Planificar tareas, hitos y sprints", ["Jefe", "PM", "PO"]),
        ("CU-06", "Registrar actualización", ["Jefe", "PM", "PO", "Prog"]),
        ("CU-07", "Gestionar solicitud de cambio", ["Jefe", "PM", "PO"]),
        ("CU-08", "Gestionar entregables", ["Jefe", "PM", "PO", "Prog"]),
        ("CU-09", "Generar informe de avance IA", ["Jefe", "PM", "PO", "Prog"]),
        ("CU-10", "Aprobar o retirar informe IA", ["Jefe", "PM", "PO"]),
        ("CU-11", "Generar resumen IA de sprint", ["Jefe", "PM", "PO", "Prog"]),
        ("CU-12", "Ver portal e informes aprobados", ["Cliente"]),
    ]
    xs = [67, 250, 433, 616]
    ys = [350, 230, 110]
    for index, (code, title, roles) in enumerate(cases):
        use_case(c, xs[index % 4], ys[index // 4], 161, 92, code, title, roles)

    c.setFillColor(white)
    c.setStrokeColor(HexColor("#D7DDE5"))
    c.roundRect(67, 67, 710, 30, 5, fill=1, stroke=1)
    c.setFillColor(MUTED)
    c.setFont("Helvetica", 6.8)
    c.drawString(78, 83, "Los distintivos de rol expresan la asociación actor-caso de uso. Cliente: solo proyectos de su empresa y contenido aprobado.")
    c.drawString(78, 72, "Programador figura como Prog. Las operaciones de escritura están protegidas por middleware; la visibilidad también se verifica en el dominio.")


def draw_domain(c):
    header(c, "Modelo de dominio actualizado", "Entidades, claves de relación y cardinalidades implementadas en modelos Eloquent y migraciones.", 2, 2,
           "Diagramas - Casos de uso y dominio")
    top_y, top_h, top_w = 380, 96, 163
    top_x = [47, 239, 431, 623]
    entity(c, top_x[0], top_y, top_w, top_h, "CLIENTE", ["id (PK)", "nombre - apellido - email", "telefono - empresa", "estado: activo | inactivo"])
    entity(c, top_x[1], top_y, top_w, top_h, "USUARIO", ["id (PK) - cliente_id (FK, nullable)", "name - apellido - email", "estado - password", "roles con Spatie Permission"])
    entity(c, top_x[2], top_y, top_w, top_h, "SPRINT", ["id (PK) - proyecto_id (FK)", "nombre - descripcion", "fecha_inicio - fecha_fin", "estado - resumen_ia"])
    entity(c, top_x[3], top_y, top_w, top_h, "HITO", ["id (PK) - proyecto_id (FK)", "nombre - descripcion", "fecha_objetivo", "completado (booleano)"])

    px, py, pw, ph = 301, 246, 240, 112
    entity(c, px, py, pw, ph, "PROYECTO", [
        "id (PK) - cliente_id (FK) - pm_id (FK Usuario)", "nombre - descripcion",
        "fecha_inicio - fecha_fin_estimada", "estado: pendiente | en_progreso | completado | cancelado",
        "created_at - updated_at"
    ], True)

    by, bh, bw = 73, 132, 145
    bx = [47, 202, 357, 512, 667]
    entity(c, bx[0], by, bw, bh, "TAREA", ["proyecto_id (FK)", "sprint_id (FK, nullable)", "solicitud_cambio_id (nullable)", "asignado_a (FK Usuario)", "titulo - estado - prioridad", "fecha_limite - orden"])
    entity(c, bx[1], by, bw, bh, "SOLICITUD CAMBIO", ["proyecto_id (FK)", "solicitado_por (FK Usuario)", "titulo - descripcion", "estado", "prioridad"])
    entity(c, bx[2], by, bw, bh, "ACTUALIZACIÓN", ["proyecto_id (FK)", "creado_por (FK Usuario)", "titulo - descripcion", "tipo - fecha", "visible_cliente"])
    entity(c, bx[3], by, bw, bh, "ENTREGABLE IA", ["proyecto_id (FK)", "generado_por - aprobado_por?", "titulo - contenido - tipo", "origen - modelo_ia - estado", "visible_cliente - generado_en", "contexto_fuente - mensaje_error"])
    entity(c, bx[4], by, bw, bh, "FACTURA", ["proyecto_id (FK)", "emitida_por (FK Usuario)", "numero - monto - detalle", "fecha_emision", "fecha_vencimiento - estado"])

    relation(c, 128, 380, 344, 358, "1", "N", "posee")
    relation(c, 320, 380, 390, 358, "1", "N", "gestiona como PM")
    relation(c, 512, 380, 456, 358, "N", "1", "organiza")
    relation(c, 704, 380, 498, 358, "N", "1", "marca")
    for dest, label in zip([119, 274, 429, 584, 739], ["contiene", "recibe", "informa", "genera", "factura"]):
        relation(c, px + pw / 2, py, dest, by + bh, "1", "N", label)

    c.setFillColor(PALE)
    c.setStrokeColor(HexColor("#D7DDE5"))
    c.roundRect(47, 30, 765, 31, 5, fill=1, stroke=1)
    c.setFillColor(GREEN)
    c.setFont("Helvetica-Bold", 6.8)
    c.drawString(58, 48, "Relaciones complementarias verificadas")
    c.setFillColor(TEXT)
    c.setFont("Helvetica", 6.35)
    c.drawString(58, 37, "Cliente 1:N Usuario - Sprint 1:N Tarea opcional - SolicitudCambio 1:N Tarea opcional - Usuario crea actualizaciones, genera/aprueba entregables, solicita cambios, ejecuta tareas y emite facturas.")


def draw_method_card(c, x, y, w, h, title, methods, color=GREEN):
    c.setFillColor(white)
    c.setStrokeColor(LINE)
    c.roundRect(x, y, w, h, 6, fill=1, stroke=1)
    c.setFillColor(color)
    c.roundRect(x, y + h - 24, w, 24, 6, fill=1, stroke=0)
    c.rect(x, y + h - 24, w, 18, fill=1, stroke=0)
    c.setFillColor(white)
    c.setFont("Helvetica-Bold", 8)
    c.drawString(x + 10, y + h - 16, title)
    yy = y + h - 38
    for signature, description in methods:
        c.setFillColor(NAVY)
        c.setFont("Helvetica-Bold", 6.8)
        c.drawString(x + 10, yy, fit(c, signature, w - 20, "Helvetica-Bold", 6.8))
        yy -= 10
        c.setFillColor(MUTED)
        c.setFont("Helvetica", 6.25)
        lines = textwrap.wrap(description, width=max(28, int((w - 20) / 3.8)))[:2]
        for line in lines:
            c.drawString(x + 10, yy, line)
            yy -= 8
        yy -= 5


def draw_controller_methods(c):
    header(c, "Métodos del controlador de Proyectos", "Firmas, visibilidad y responsabilidades verificadas en ProyectoController y rutas.", 1, 4,
           "Métodos y funciones - Módulo Proyectos")
    draw_method_card(c, 47, 56, 360, 420, "ProyectoController - navegación y consulta", [
        ("public index(Request)", "Elige listado interno o portal Cliente según el rol autenticado."),
        ("private misProyectos(User)", "Obtiene proyectos visibles y contadores de tareas e hitos."),
        ("private listadoInterno(Request)", "Aplica texto, estado, cliente, PM, fechas y paginación de 15."),
        ("public show(Request, Proyecto, ProjectContextBuilder)", "Comprueba puedeVer() y deriva a la experiencia del rol."),
        ("private portalCliente(Proyecto)", "Reúne línea de tiempo, novedades, cambios, avance e informes aprobados."),
        ("private fichaInterna(Request, Proyecto, Builder)", "Carga operación interna, progreso, actualizaciones e informes IA."),
    ], BLUE)
    draw_method_card(c, 428, 56, 367, 420, "ProyectoController - escritura", [
        ("public store(Request)", "Valida y crea. La fecha inicial debe ser hoy o posterior."),
        ("public update(Request, Proyecto)", "Valida y actualiza datos y asociaciones del proyecto."),
        ("public destroy(Proyecto)", "Elimina el proyecto; la ruta está reservada al Jefe."),
        ("GET /proyectos", "index(); cualquier usuario autenticado, con visibilidad por empresa."),
        ("GET /proyectos/{proyecto}", "show(); devuelve 403 cuando el Cliente intenta abrir un proyecto ajeno."),
        ("POST y PUT/PATCH", "store/update; Jefe y PM mediante middleware de rol."),
        ("DELETE /proyectos/{proyecto}", "destroy; acceso exclusivo del rol Jefe."),
    ], GREEN)


def draw_model_methods(c):
    header(c, "Métodos del modelo y entidades relacionadas", "Relaciones Eloquent, scopes de visibilidad y reglas de acceso implementadas.", 2, 4,
           "Métodos y funciones - Módulo Proyectos")
    draw_method_card(c, 47, 266, 360, 210, "Proyecto", [
        ("casts()", "Convierte fecha_inicio y fecha_fin_estimada en fechas."),
        ("scopeVisiblePara(Builder, User)", "Restringe al Cliente por cliente_id; roles internos ven todos."),
        ("cliente() / pm()", "Relaciones belongsTo con Cliente y Usuario responsable."),
        ("tareas() / sprints() / hitos()", "Colecciones de planificación y ejecución del proyecto."),
        ("entregablesIa() / facturas()", "Colecciones de resultados y facturación."),
        ("actualizaciones() / solicitudesCambio()", "Novedades publicables y pedidos de cambio relacionados."),
    ], BLUE)
    draw_method_card(c, 428, 266, 367, 210, "Usuario y Cliente", [
        ("User::esCliente()", "Determina si la cuenta tiene rol Cliente."),
        ("User::puedeVer(Model)", "Comprueba pertenencia del proyecto a la empresa representada."),
        ("User::scopeAsignables()", "Limita asignaciones a PM, PO y Programador."),
        ("User::proyectosComoPm()", "Proyectos gestionados por el usuario mediante pm_id."),
        ("Cliente::proyectos() / usuarios()", "Proyectos y cuentas asociadas a la ficha Cliente."),
    ], GREEN)
    draw_method_card(c, 47, 56, 238, 188, "Trabajo", [
        ("Tarea::scopeVisiblePara()", "Filtra tareas por empresa para el Cliente."),
        ("proyecto() / sprint()", "Proyecto obligatorio y sprint opcional."),
        ("asignado() / solicitudCambio()", "Responsable y origen opcional de la tarea."),
        ("Sprint::tareas()", "Actividades organizadas dentro del sprint."),
    ], ORANGE)
    draw_method_card(c, 302, 56, 238, 188, "Seguimiento", [
        ("Hito::scopeVisiblePara()", "Aplica el límite de visibilidad por empresa."),
        ("SolicitudCambio::tareas()", "Tareas nacidas de una solicitud aceptada."),
        ("ActualizacionProyecto::autor()", "Usuario que registró la novedad."),
        ("Factura::emisor()", "Usuario responsable de emitir la factura."),
    ], PURPLE)
    draw_method_card(c, 557, 56, 238, 188, "Entregables", [
        ("EntregableIA::scopeVisiblePara()", "Cliente ve manuales o IA aprobados y publicados."),
        ("generador() / aprobador()", "Usuarios que generaron y aprobaron el contenido."),
        ("TIPOS", "Documento, informe, avance, resumen, transcripción y diagrama."),
    ], GREEN)


def draw_ai_methods(c):
    header(c, "Funciones de IA, progreso y portal Cliente", "Servicios desacoplados que preparan contexto, generan, revisan y publican resultados.", 3, 4,
           "Métodos y funciones - Módulo Proyectos")
    draw_method_card(c, 47, 266, 360, 210, "Informes de avance", [
        ("ProjectContextBuilder::build(Proyecto)", "Construye contexto publicable: proyecto, progreso, tareas, hitos y actualizaciones visibles."),
        ("ProjectReportService::generate(Proyecto, User)", "Genera un EntregableIA borrador y registra errores sin publicarlo."),
        ("ProjectReportGenerator::generate(array)", "Contrato intercambiable del proveedor de inteligencia artificial."),
        ("OpenRouterProjectReportGenerator::generate(array)", "Invoca modelo primario/fallback y limpia el texto resultante."),
    ], BLUE)
    draw_method_card(c, 428, 266, 367, 210, "Aprobación y publicación", [
        ("InformeIAController::store()", "Solicita el borrador y devuelve éxito o error controlado."),
        ("InformeIAController::publish()", "Aprueba, marca visible y registra aprobador y fecha."),
        ("InformeIAController::unpublish()", "Retira el informe del Cliente y vuelve a revisado."),
        ("EntregableIA::scopeVisiblePara()", "Evita exponer al Cliente borradores, fallos o IA no aprobada."),
    ], GREEN)
    draw_method_card(c, 47, 56, 360, 188, "Línea de tiempo", [
        ("LineaDeTiempo::construir(Proyecto)", "Combina hitos y sprints, ordena y prepara puntos visibles."),
        ("hitos() / sprints()", "Calculan detalle, avance, completado y atraso."),
        ("marcarEstados()", "Asigna Completado, Atrasado, En curso o Pendiente."),
        ("colapsarHistorial()", "Resume etapas antiguas cuando el historial crece."),
    ], PURPLE)
    draw_method_card(c, 428, 56, 367, 188, "Resumen de sprint", [
        ("SprintContextBuilder::build(Sprint)", "Prepara el contexto de actividades y avance del sprint."),
        ("SprintSummaryService::generate(Sprint, bool)", "Usa caché salvo regeneración forzada y persiste resumen_ia."),
        ("OpenRouterSprintSummaryGenerator::generate(array)", "Genera resumen estructurado con modelo y fallback."),
        ("SprintSummaryController::store()", "Expone JSON con resumen, modelo y bandera cacheado."),
    ], ORANGE)


def flow_box(c, x, y, w, h, number, title, detail, color):
    c.setFillColor(white)
    c.setStrokeColor(color)
    c.setLineWidth(1.1)
    c.roundRect(x, y, w, h, 7, fill=1, stroke=1)
    c.setFillColor(color)
    c.circle(x + 18, y + h - 18, 10, fill=1, stroke=0)
    c.setFillColor(white)
    c.setFont("Helvetica-Bold", 7)
    c.drawCentredString(x + 18, y + h - 21, str(number))
    c.setFillColor(NAVY)
    c.setFont("Helvetica-Bold", 7.5)
    c.drawString(x + 34, y + h - 18, fit(c, title, w - 44, "Helvetica-Bold", 7.5))
    c.setFillColor(MUTED)
    c.setFont("Helvetica", 6.4)
    yy = y + h - 34
    for line in textwrap.wrap(detail, width=max(25, int((w - 20) / 3.7)))[:3]:
        c.drawString(x + 10, yy, line)
        yy -= 8


def draw_flow(c):
    header(c, "Flujo funcional hasta el resumen visible", "Secuencia real de llamadas y controles desde la carga del trabajo hasta el portal Cliente.", 4, 4,
           "Métodos y funciones - Módulo Proyectos")
    steps = [
        ("Crear proyecto", "ProyectoController::store valida y persiste Cliente, PM, fechas y estado.", BLUE),
        ("Planificar", "Se cargan hitos, sprints y tareas vinculadas al proyecto.", PURPLE),
        ("Registrar avances", "ActualizacionProyectoController::store guarda novedades y decide visibilidad.", ORANGE),
        ("Construir contexto", "ProjectContextBuilder::build calcula progreso y reúne datos publicables.", GREEN),
        ("Generar borrador IA", "ProjectReportService::generate crea EntregableIA como borrador no visible.", BLUE),
        ("Revisar y publicar", "InformeIAController::publish aprueba y habilita visible_cliente.", PURPLE),
        ("Consultar portal", "ProyectoController::portalCliente muestra línea, avances e informes aprobados.", GREEN),
        ("Retirar si corresponde", "unpublish oculta el informe y conserva trazabilidad para revisión.", ORANGE),
    ]
    xs = [47, 237, 427, 617]
    ys = [315, 150]
    for i, (title, detail, color) in enumerate(steps):
        flow_box(c, xs[i % 4], ys[i // 4], 168, 125, i + 1, title, detail, color)
        if i % 4 != 3:
            c.setStrokeColor(HexColor("#98A2B3"))
            c.setLineWidth(1.2)
            c.line(xs[i % 4] + 168, ys[i // 4] + 62, xs[i % 4] + 190, ys[i // 4] + 62)
    c.setStrokeColor(HexColor("#98A2B3"))
    c.line(701, 315, 701, 285)
    c.line(701, 285, 131, 285)
    c.line(131, 285, 131, 275)
    c.setFillColor(PALE)
    c.setStrokeColor(LINE)
    c.roundRect(47, 65, 748, 55, 7, fill=1, stroke=1)
    c.setFillColor(GREEN)
    c.setFont("Helvetica-Bold", 7.3)
    c.drawString(60, 101, "Regla de publicación")
    c.setFillColor(TEXT)
    c.setFont("Helvetica", 6.8)
    c.drawString(60, 87, "El modelo de IA nunca publica directamente. El resultado nace como borrador, requiere aprobación de Jefe/PM/PO y solo entonces se muestra al Cliente.")
    c.drawString(60, 75, "El Cliente queda limitado a su empresa mediante visiblePara() y puedeVer(); los informes fallidos o retirados no se exponen.")


def generate_diagrams():
    if not DIAGRAMAS_BACKUP.exists():
        shutil.copy2(DIAGRAMAS, DIAGRAMAS_BACKUP)
    c = canvas.Canvas(str(DIAGRAMAS), pagesize=(W, H))
    c.setTitle("Cruz Negra - Casos de uso y modelo de dominio actualizados")
    draw_use_cases(c)
    c.showPage()
    draw_domain(c)
    c.showPage()
    c.save()


def generate_methods():
    c = canvas.Canvas(str(METODOS), pagesize=(W, H))
    c.setTitle("Cruz Negra - Métodos y funciones del módulo Proyectos")
    for drawer in [draw_controller_methods, draw_model_methods, draw_ai_methods, draw_flow]:
        drawer(c)
        c.showPage()
    c.save()


def verify():
    expected = [(DIAGRAMAS, 2), (METODOS, 4)]
    for path, pages in expected:
        reader = PdfReader(str(path))
        if len(reader.pages) != pages:
            raise RuntimeError(f"{path.name}: se esperaban {pages} páginas y se obtuvieron {len(reader.pages)}")
        if reader.is_encrypted:
            raise RuntimeError(f"{path.name}: el PDF quedó cifrado")


if __name__ == "__main__":
    generate_diagrams()
    generate_methods()
    verify()
