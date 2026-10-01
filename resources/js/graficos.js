/*
 * Graficos del sistema (Chart.js).
 *
 * Cada <canvas data-grafico="..."> se convierte en un grafico leyendo su
 * data-valores (JSON). Tipos soportados:
 *   - dona: distribucion de un total (etiqueta, valor, color opcional)
 *   - barras-h: barras horizontales (etiqueta, valor)
 */
import Chart from 'chart.js/auto';

const MARCA = '#00b87d';
const PALETA = ['#00b87d', '#38bdf8', '#f59e0b', '#f87171', '#a78bfa', '#94a3b8'];

function iniciar() {
    document.querySelectorAll('canvas[data-grafico]').forEach((canvas) => {
        if (canvas.dataset.graficoIniciado) return;
        canvas.dataset.graficoIniciado = '1';

        let valores = [];
        try { valores = JSON.parse(canvas.dataset.valores || '[]'); } catch { valores = []; }

        const opcionesBase = {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 900, easing: 'easeOutQuart' },
        };

        if (canvas.dataset.grafico === 'dona') {
            new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: valores.map((v) => v.etiqueta),
                    datasets: [{
                        data: valores.map((v) => v.valor),
                        backgroundColor: valores.map((v, i) => v.color ?? PALETA[i % PALETA.length]),
                        borderColor: '#ffffff',
                        borderWidth: 2,
                        hoverOffset: 6,
                    }],
                },
                options: {
                    ...opcionesBase,
                    cutout: '62%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true, padding: 12, font: { size: 11 } },
                        },
                    },
                },
            });
        }

        if (canvas.dataset.grafico === 'barras-h') {
            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: valores.map((v) => v.etiqueta),
                    datasets: [{
                        data: valores.map((v) => v.valor),
                        backgroundColor: valores.map((v) => v.color ?? MARCA),
                        borderRadius: 6,
                        maxBarThickness: 22,
                    }],
                },
                options: {
                    ...opcionesBase,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: (ctx) => ` ${ctx.parsed.x} ${ctx.parsed.x === 1 ? 'tarea' : 'tareas'}` } },
                    },
                    scales: {
                        x: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } }, grid: { color: '#f1f5f9' } },
                        y: { ticks: { font: { size: 11 } }, grid: { display: false } },
                    },
                },
            });
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', iniciar);
} else {
    iniciar();
}
