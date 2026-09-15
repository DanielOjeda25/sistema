# Presentación — DSD y CU Reales

**`CRUZNEGRA_DSD_CU_Reales.pptx`** — lista para abrir en PowerPoint: 15 láminas
(portada, 6 DSD en alta resolución, separador, 6 CU con capturas numeradas, cierre).

- Los DSD están renderizados a 200 DPI (`docs/dsd/png-hd/`): nítidos en proyector.
- Los CU muestran los storyboards con pasos numerados (`docs/cu-reales/png/`).
- Para regenerar el archivo tras cambiar diagramas o capturas:

```bash
npm install -g pptxgenjs
NODE_PATH=$(npm root -g) node docs/presentacion/build-pptx.cjs
```

Versión web interactiva (con zoom en vivo, misma contenido):
`public/presentacion/index.html` → http://localhost:8000/presentacion/
