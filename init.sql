-- Tabla de Empresas (Emisores)
CREATE TABLE IF NOT EXISTS companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nit VARCHAR(20) NOT NULL UNIQUE,
    dv CHAR(1) NOT NULL, -- Dígito de verificación
    company_name VARCHAR(255) NOT NULL,
    trade_name VARCHAR(255), -- Nombre comercial
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    address VARCHAR(255) NOT NULL,
    city_code VARCHAR(10) NOT NULL, -- Código DANE municipio
    department_code VARCHAR(10) NOT NULL, -- Código DANE departamento
    postal_code VARCHAR(10),
    tax_level_code VARCHAR(10) NOT NULL DEFAULT 'O-13', -- Responsabilidad fiscal (ej: Gran Contribuyente)
    regime_code VARCHAR(10) NOT NULL DEFAULT '48', -- Régimen (48: Resp. IVA, 49: No Resp. IVA)
    certificate_path VARCHAR(255), -- Ruta al .p12
    certificate_password VARCHAR(255), -- Contraseña del certificado (Debería estar encriptada)
    software_id VARCHAR(100), -- ID del software en DIAN
    test_set_id VARCHAR(100), -- ID del set de pruebas
    pin VARCHAR(100), -- PIN del software
    environment ENUM('TEST', 'PROD') DEFAULT 'TEST',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de Resoluciones de Facturación
CREATE TABLE IF NOT EXISTS resolutions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    resolution_number VARCHAR(50) NOT NULL,
    prefix VARCHAR(10) NOT NULL,
    start_number INT NOT NULL,
    end_number INT NOT NULL,
    current_number INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    technical_key VARCHAR(255) NOT NULL, -- Clave técnica DIAN
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

-- Tabla de Clientes (Adquirentes)
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    identification_type VARCHAR(5) NOT NULL, -- 13: Cédula, 31: NIT, etc.
    identification_number VARCHAR(20) NOT NULL,
    dv CHAR(1),
    name VARCHAR(255) NOT NULL, -- Razón social o Nombre completo
    email VARCHAR(255) NOT NULL, -- Para envío de la factura
    address VARCHAR(255),
    phone VARCHAR(50),
    city_code VARCHAR(10),
    department_code VARCHAR(10),
    tax_level_code VARCHAR(10) DEFAULT 'R-99-PN', -- Responsabilidad tributaria
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    UNIQUE KEY unique_customer (company_id, identification_number)
);

-- Tabla de Productos
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    code VARCHAR(50) NOT NULL, -- Código interno
    name VARCHAR(255) NOT NULL,
    price DECIMAL(16, 2) NOT NULL, -- Precio unitario antes de impuestos
    tax_rate DECIMAL(5, 2) DEFAULT 19.00, -- % IVA
    unit_measure_code VARCHAR(10) DEFAULT '94', -- 94: Unidad (Estándar UBL)
    is_excluded BOOLEAN DEFAULT FALSE, -- Si es excluido de IVA
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

-- Tabla de Facturas (Cabecera)
CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    customer_id INT NOT NULL,
    resolution_id INT NOT NULL,
    invoice_type_code VARCHAR(5) DEFAULT '01', -- 01: Factura de Venta, 03: Contingencia
    prefix VARCHAR(10) NOT NULL,
    number INT NOT NULL, -- Consecutivo
    issue_date DATE NOT NULL,
    issue_time TIME NOT NULL,
    payment_form VARCHAR(5) DEFAULT '1', -- 1: Contado, 2: Crédito
    payment_method VARCHAR(5) DEFAULT '10', -- 10: Efectivo, etc.
    due_date DATE, -- Vencimiento
    
    -- Totales
    subtotal DECIMAL(16, 2) NOT NULL,
    tax_amount DECIMAL(16, 2) NOT NULL,
    total DECIMAL(16, 2) NOT NULL,
    
    -- Campos DIAN
    cufe VARCHAR(255), -- Código Único de Facturación Electrónica
    qr_data TEXT, -- Datos para generar el QR
    signature_value TEXT, -- Firma digital
    uubl_xml_path VARCHAR(255), -- Ruta archivo XML firmado
    
    -- Estado
    dian_status ENUM('DRAFT', 'SIGNED', 'SENT', 'ACCEPTED', 'REJECTED') DEFAULT 'DRAFT',
    dian_response_code VARCHAR(10),
    dian_response_message TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (resolution_id) REFERENCES resolutions(id)
);

-- Tabla de Detalles de Factura
CREATE TABLE IF NOT EXISTS invoice_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    product_code VARCHAR(50),
    product_name VARCHAR(255) NOT NULL,
    quantity DECIMAL(12, 2) NOT NULL,
    unit_price DECIMAL(16, 2) NOT NULL,
    total_price DECIMAL(16, 2) NOT NULL,
    tax_rate DECIMAL(5, 2) NOT NULL,
    tax_amount DECIMAL(16, 2) NOT NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id)
);

-- Tabla de Auditoría / Logs DIAN
CREATE TABLE IF NOT EXISTS dian_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT,
    event_type VARCHAR(50), -- SEND, STATUS_CHECK
    request_payload LONGTEXT, -- XML enviado
    response_payload LONGTEXT, -- Respuesta SOAP
    status_code VARCHAR(10),
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id)
);
