from pathlib import Path
from io import BytesIO
import shutil

from reportlab.pdfgen import canvas
from reportlab.lib.pagesizes import landscape, A4
from reportlab.lib.colors import HexColor, white
from pypdf import PdfReader, PdfWriter


SOURCE = Path(r"C:\Users\dani_\OneDrive\Desktop\facu\02_Analisis_y_Diseno\ANALISIS\10_CruzNegra_Proyecto_Documentacion_Actualizada.pdf")
BACKUP = SOURCE.with_name("10_CruzNegra_Proyecto_Documentacion_Actualizada_antes_modelo_dominio.pdf")
TEMP_PAGE = Path(r"C:\laragon\www\sistema\tmp\pdfs\mapa_revision\modelo_dominio_actualizado.pdf")
TEMP_FINAL = Path(r"C:\laragon\www\sistema\tmp\pdfs\mapa_revision\documentacion_con_modelo.pdf")

W, H = landscape(A4)
NAVY = HexColor("#172033")
BLUE = HexColor("#2557D6")
GREEN = HexColor("#00966D")
MUTED = HexColor("#667085")
TEXT = HexColor("#344054")
LINE = HexColor("#98A2B3")
PALE = HexColor("#F7F9FA")
PALE_BLUE = HexColor("#EFF4FF")


def fit_text(c, text, max_width, font="Helvetica", size=6.4):
    if c.stringWidth(text, font, size) <= max_width:
        return text
    ellipsis = "..."
    while text and c.stringWidth(text + ellipsis, font, size) > max_width:
        text = text[:-1]
    return text + ellipsis


def entity(c, x, y, width, height, title, fields, highlight=False):
    c.setFillColor(PALE_BLUE if highlight else white)
    c.setStrokeColor(BLUE if highlight else HexColor("#B8C1D1"))
    c.setLineWidth(1.3 if highlight else 0.8)
    c.roundRect(x, y, width, height, 5, fill=1, stroke=1)

    header_h = 21
    c.setFillColor(BLUE if highlight else GREEN)
    c.roundRect(x, y + height - header_h, width, header_h, 5, fill=1, stroke=0)
    c.rect(x, y + height - header_h, width, header_h - 5, fill=1, stroke=0)
    c.setFillColor(white)
    c.setFont("Helvetica-Bold", 7.5 if not highlight else 8.2)
    c.drawCentredString(x + width / 2, y + height - 14, title)

    c.setFillColor(TEXT)
    c.setFont("Helvetica", 6.25)
    cursor = y + height - header_h - 11
    for field in fields:
        c.drawString(x + 7, cursor, fit_text(c, field, width - 14))
        cursor -= 9.2


def relation(c, x1, y1, x2, y2, left_card, right_card, label):
    c.setStrokeColor(LINE)
    c.setLineWidth(0.8)
    c.line(x1, y1, x2, y2)
    c.setFillColor(MUTED)
    c.setFont("Helvetica-Bold", 6.2)
    c.drawString(x1 + (3 if x2 >= x1 else -10), y1 + 3, left_card)
    c.drawString(x2 + (-10 if x2 >= x1 else 3), y2 + 3, right_card)
    mx, my = (x1 + x2) / 2, (y1 + y2) / 2
    c.setFillColor(GREEN)
    c.setFont("Helvetica-Oblique", 6.2)
    c.drawCentredString(mx, my + 4, label)


def make_domain_page(path):
    c = canvas.Canvas(str(path), pagesize=(W, H))
    c.setTitle("Modelo de dominio actualizado - Modulo Proyectos")

    c.setFillColor(NAVY)
    c.setFont("Helvetica-Bold", 15)
    c.drawString(47, 558, "CRUZNEGRA")
    c.setFillColor(GREEN)
    c.setFont("Helvetica", 7.2)
    c.drawString(47, 546, "Sistema de Gestión Interna · Documentación de Análisis y Diseño")
    c.setFillColor(MUTED)
    c.setFont("Helvetica", 7)
    c.drawRightString(795, 558, "Análisis y Diseño de Sistemas · Octubre 2026")
    c.drawRightString(795, 546, "Alcance exclusivo: módulo Proyectos · Modelo verificado con el código")
    c.setStrokeColor(GREEN)
    c.setLineWidth(1)
    c.line(47, 535, 795, 535)

    c.setFillColor(NAVY)
    c.setFont("Helvetica-Bold", 16)
    c.drawString(47, 512, "Modelo de dominio actualizado")
    c.setFillColor(MUTED)
    c.setFont("Helvetica", 8)
    c.drawString(47, 498, "Entidades, claves de relación y cardinalidades implementadas en modelos Eloquent y migraciones.")

    top_y, top_h, top_w = 380, 96, 163
    top_x = [47, 239, 431, 623]
    entity(c, top_x[0], top_y, top_w, top_h, "CLIENTE", [
        "id (PK)", "nombre · apellido · email", "telefono · empresa", "estado: activo | inactivo"
    ])
    entity(c, top_x[1], top_y, top_w, top_h, "USUARIO", [
        "id (PK) · cliente_id (FK, nullable)", "name · apellido · email", "estado · password", "roles mediante Spatie Permission"
    ])
    entity(c, top_x[2], top_y, top_w, top_h, "SPRINT", [
        "id (PK) · proyecto_id (FK)", "nombre · descripcion", "fecha_inicio · fecha_fin",
        "estado · resumen_ia", "Tarea.sprint_id es opcional"
    ])
    entity(c, top_x[3], top_y, top_w, top_h, "HITO", [
        "id (PK) · proyecto_id (FK)", "nombre · descripcion", "fecha_objetivo", "completado (booleano)"
    ])

    project_x, project_y, project_w, project_h = 301, 246, 240, 112
    entity(c, project_x, project_y, project_w, project_h, "PROYECTO", [
        "id (PK) · cliente_id (FK) · pm_id (FK Usuario)",
        "nombre · descripcion", "fecha_inicio · fecha_fin_estimada",
        "estado: pendiente | en_progreso | completado | cancelado",
        "created_at · updated_at"
    ], highlight=True)

    bottom_y, bottom_h, bottom_w = 73, 132, 145
    bottom_x = [47, 202, 357, 512, 667]
    entity(c, bottom_x[0], bottom_y, bottom_w, bottom_h, "TAREA", [
        "proyecto_id (FK)", "sprint_id (FK, nullable)", "solicitud_cambio_id (nullable)",
        "asignado_a (FK Usuario)", "titulo · estado · prioridad", "fecha_limite · orden"
    ])
    entity(c, bottom_x[1], bottom_y, bottom_w, bottom_h, "SOLICITUD CAMBIO", [
        "proyecto_id (FK)", "solicitado_por (FK Usuario)", "titulo · descripcion",
        "estado", "prioridad"
    ])
    entity(c, bottom_x[2], bottom_y, bottom_w, bottom_h, "ACTUALIZACIÓN", [
        "proyecto_id (FK)", "creado_por (FK Usuario)", "titulo · descripcion",
        "tipo · fecha", "visible_cliente (booleano)"
    ])
    entity(c, bottom_x[3], bottom_y, bottom_w, bottom_h, "ENTREGABLE IA", [
        "proyecto_id (FK)", "generado_por · aprobado_por?", "titulo · contenido · tipo",
        "origen · modelo_ia · estado", "visible_cliente · generado_en", "contexto_fuente · mensaje_error"
    ])
    entity(c, bottom_x[4], bottom_y, bottom_w, bottom_h, "FACTURA", [
        "proyecto_id (FK)", "emitida_por (FK Usuario)", "numero · monto · detalle",
        "fecha_emision", "fecha_vencimiento · estado"
    ])

    # Relaciones directas con Proyecto.
    relation(c, 128, 380, 344, 358, "1", "N", "posee")
    relation(c, 320, 380, 390, 358, "1", "N", "gestiona como PM")
    relation(c, 512, 380, 456, 358, "N", "1", "organiza")
    relation(c, 704, 380, 498, 358, "N", "1", "marca")
    for bx, label in zip([119, 274, 429, 584, 739], ["contiene", "recibe", "informa", "genera", "factura"]):
        relation(c, project_x + project_w / 2, project_y, bx, bottom_y + bottom_h, "1", "N", label)

    # Nota de relaciones complementarias que evita líneas cruzadas ilegibles.
    c.setFillColor(PALE)
    c.setStrokeColor(HexColor("#D7DDE5"))
    c.roundRect(47, 30, 765, 31, 5, fill=1, stroke=1)
    c.setFillColor(GREEN)
    c.setFont("Helvetica-Bold", 6.8)
    c.drawString(58, 48, "Relaciones complementarias verificadas")
    c.setFillColor(TEXT)
    c.setFont("Helvetica", 6.45)
    c.drawString(58, 37, "Cliente 1:N Usuario · Sprint 1:N Tarea (opcional) · SolicitudCambio 1:N Tarea (opcional) · Usuario crea actualizaciones, genera/aprueba entregables, solicita cambios, ejecuta tareas y emite facturas.")

    c.showPage()
    c.save()


def footer_overlay(page_number, total, width, height):
    stream = BytesIO()
    c = canvas.Canvas(stream, pagesize=(width, height))
    c.setFillColor(white)
    c.rect(0, 0, width, 38, fill=1, stroke=0)
    c.setFillColor(MUTED)
    c.setFont("Helvetica", 6.5)
    if page_number in (2, 3):
        section = "Modelo de datos"
    elif height > width:
        section = "Contratos de operación · Proyectos"
    else:
        section = "Documentación del módulo Proyectos"
    c.drawString(47, 10, section)
    c.drawRightString(width - 47, 10, f"DOC-PRO-2026-010 · Página {page_number} de {total}")
    c.save()
    stream.seek(0)
    return PdfReader(stream).pages[0]


def integrate():
    if not BACKUP.exists():
        shutil.copy2(SOURCE, BACKUP)

    make_domain_page(TEMP_PAGE)
    # La copia preservada es siempre la base de 24 páginas. Así el script es
    # idempotente y nunca vuelve a insertar el diagrama sobre sí mismo.
    original = PdfReader(str(BACKUP))
    inserted = PdfReader(str(TEMP_PAGE)).pages[0]
    pages = []
    for index, page in enumerate(original.pages):
        pages.append(page)
        if index == 1:
            pages.append(inserted)

    total = len(pages)
    writer = PdfWriter()
    for number, page in enumerate(pages, start=1):
        width = float(page.mediabox.width)
        height = float(page.mediabox.height)
        page.merge_page(footer_overlay(number, total, width, height), over=True)
        writer.add_page(page)

    with TEMP_FINAL.open("wb") as output:
        writer.write(output)

    # Reabrir antes de reemplazar confirma que el resultado es legible.
    check = PdfReader(str(TEMP_FINAL))
    if len(check.pages) != 25:
        raise RuntimeError(f"Se esperaban 25 páginas y se obtuvieron {len(check.pages)}")
    shutil.copy2(TEMP_FINAL, SOURCE)


if __name__ == "__main__":
    integrate()
