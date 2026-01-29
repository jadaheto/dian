# Sistema de Facturación Electrónica DIAN 1.9 (Col)

Sistema completo de facturación electrónica adaptado a la normativa colombiana (Resolución 000042 de 2020 y Anexo Técnico 1.9). Desarrollado en **PHP 8.1**, **MySQL**, **Docker** y **JavaScript**.

![Dashboard Preview](https://via.placeholder.com/800x400?text=Dashboard+Facturacion+DIAN)

## 🚀 Características
- **Cumplimiento DIAN 1.9**: XML UBL 2.1, CUFE (SHA-384), QR Code.
- **Firma Digital**: Implementación nativa de XAdES-BES.
- **Arquitectura Limpia**: Separación de Dominio, Infraestructura y Controladores.
- **Dockerizado**: Listo para desplegar en Easypanel, Portainer o VPS.
- **Interfaz Moderna**: Dashboard responsivo para gestión de facturas.

## 📋 Requisitos
- Docker & Docker Compose
- Certificado Digital (.p12) emitido por una entidad autorizada (ONAC).

## 🛠️ Instalación Rápida

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/usuario/dian-billing.git
   cd dian-billing
   ```

2. **Configurar Entorno**
   Copie el archivo de ejemplo (si existe) o use los valores por defecto en `docker-compose.yml`.

3. **Iniciar Contenedores**
   ```bash
   docker-compose up -d --build
   ```

4. **Acceder**
   Visite `http://localhost:8080`.

## 📂 Estructura del Proyecto
```
/src
  /Domain       # Lógica de Negocio (Entidades, Servicios DIAN)
  /Controllers  # Controladores HTTP
  /Utils        # Helpers (Zip, Format)
/public         # Assets y Entry Point (index.php)
/templates      # Vistas HTML
/storage        # XMLs generados, Certificados, Logs
```

## 🔐 Seguridad
- **Certificados**: Se almacenan en volumen persistente. Asegúrese de restringir permisos en producción.
- **Base de Datos**: Contraseñas definidas en variables de entorno. Cambiar para producción.
- **Validación**: Algoritmo CUFE implementado según especificación técnica DIAN.

## 📄 Licencia
MIT.
