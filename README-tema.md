# Tema Landing — guía de arranque

## Instalación en el entorno de desarrollo

Copiar la carpeta `landing/` a `wp-content/themes/` dentro del contenedor y activar:

```bash
docker compose cp landing landing-wp:/var/www/html/wp-content/themes/landing
docker compose run --rm wpcli wp theme activate landing
```

Verificar la sintaxis antes de activar:

```bash
docker compose exec landing-wp sh -c 'find /var/www/html/wp-content/themes/landing -name "*.php" -exec php -l {} \;'
```

## Qué toca cambiar cuando llegue el diseño

Prácticamente todo se concentra en `theme.json`:

| Qué entrega diseño | Dónde va |
| --- | --- |
| Paleta | `settings.color.palette` — mantener los slugs, cambiar solo los hex |
| Tipografía | `settings.typography.fontFamilies` + archivos `.woff2` en `assets/fonts/` |
| Escala de tamaños | `settings.typography.fontSizes` |
| Espaciados | `settings.spacing.spacingSizes` |
| Anchos de contenedor | `settings.layout` |

Los patterns **no llevan colores ni tamaños en duro**: todos referencian
los slugs de `theme.json`. Por eso cambiar los tokens repinta el sitio entero.

## Tipografía

Faltan los archivos reales. Ubicarlos en:

- `assets/fonts/landing-sans-400.woff2`
- `assets/fonts/landing-sans-600.woff2`

Se autoalojan a propósito: evita una conexión externa en el primer pintado
y el problema de privacidad que suele saltar en revisión corporativa.
Confirmar con diseño que la licencia permita autoalojamiento.

## Formulario

El pattern `contacto` embebe `[fluentform id="1"]`. Ajustar el ID al del
formulario real una vez creado en Fluent Forms.

## Notas de rendimiento

- La imagen del hero necesita la clase `is-hero` en el bloque de imagen.
  Es lo que le quita el `loading="lazy"` y le pone `fetchpriority="high"`.
  Sin esa clase, el LCP se degrada.
- Las variantes de imagen se generan en WebP. El original subido no se toca.
- `wp_omit_loading_attr_threshold` está en 1 porque solo el hero está
  sobre el pliegue en esta landing.

## Medición

Medir recién con contenido e imágenes reales. Lighthouse sobre placeholders
no da información útil.

```bash
npx lighthouse http://34.132.80.1 --preset=desktop --view
npx lighthouse http://34.132.80.1 --view   # perfil móvil
```
