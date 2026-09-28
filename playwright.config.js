import { defineConfig } from '@playwright/test';

// Los recorridos E2E corren contra el servidor de desarrollo (php artisan serve
// en el puerto 8000) usando las cuentas de prueba del seed. Son de solo lectura:
// entran, miran y salen, para no ensuciar datos de desarrollo.
export default defineConfig({
  testDir: './e2e',
  timeout: 90000,
  retries: 0,
  use: {
    baseURL: 'http://127.0.0.1:8000',
    locale: 'es-AR',
    screenshot: 'only-on-failure',
  },
  projects: [
    { name: 'chromium', use: { browserName: 'chromium' } },
  ],
});
