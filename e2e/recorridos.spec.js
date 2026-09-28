import { test, expect } from '@playwright/test';

const usuarios = {
  jefe: 'jefe@example.com',
  pm: 'pm@example.com',
  po: 'po@example.com',
  dev: 'dev@example.com',
  cliente: 'cliente@example.com',
};

async function login(page, email) {
  await page.goto('/login');
  await page.fill('#email', email);
  await page.fill('#password', '1234');
  await page.click('button[type=submit]');
  await page.waitForURL('**/dashboard');
}

test('cada rol entra con su clave y aterriza en el dashboard', async ({ page }) => {
  for (const [rol, email] of Object.entries(usuarios)) {
    await login(page, email);
    await expect(page).toHaveURL(/\/dashboard$/);
    await expect(page.getByRole('link', { name: 'Dashboard' })).toBeVisible();
    // Limpiamos la sesion para probar el siguiente rol desde cero.
    await page.context().clearCookies();
  }
});

test('el cliente queda bloqueado por URL en las pantallas internas', async ({ page }) => {
  await login(page, usuarios.cliente);

  for (const ruta of ['/tareas', '/tareas/tablero', '/hitos', '/solicitudes-cambio', '/sprints']) {
    await page.goto(ruta);
    await expect(page.locator('body')).toContainText('403', { timeout: 5000 });
  }
});

test('el cliente no ve botones de creacion en sus listados', async ({ page }) => {
  await login(page, usuarios.cliente);

  await page.goto('/entregables');
  await expect(page.getByText('Nuevo Entregable')).toHaveCount(0);
  await expect(page.getByText('Borrador de manual de usuario')).toBeVisible();

  await page.goto('/proyectos');
  await expect(page.getByText('Nuevo Proyecto')).toHaveCount(0);
});

test('el equipo si ve sus acciones de creacion', async ({ page }) => {
  await login(page, usuarios.pm);

  await page.goto('/entregables');
  await expect(page.getByRole('button', { name: '+ Nuevo Entregable' })).toBeVisible();

  await page.goto('/solicitudes-cambio');
  await expect(page.getByRole('button', { name: '+ Nueva Solicitud' })).toBeVisible();
});
