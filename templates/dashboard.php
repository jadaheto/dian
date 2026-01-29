<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sist. Facturación DIAN 1.9</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Factura DIAN</h2>
        <a href="#" class="nav-link active">Dashboard</a>
        <a href="#" class="nav-link">Nueva Factura</a>
        <a href="#" class="nav-link">Historial</a>
        <a href="#" class="nav-link">Configuración</a>
        <a href="#" class="nav-link">Cerrar Sesión</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header class="header">
            <h3>Bienvenido, Empresa Demo SAS</h3>
            <div>
                <span class="badge bg-success">Habilitado</span>
            </div>
        </header>

        <div class="container">
            <!-- Stats Row -->
            <div class="row">
                <div class="col">
                    <div class="card">
                        <h5>Facturas Hoy</h5>
                        <h2>5</h2>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <h5>Ventas Mes</h5>
                        <h2>$ 12.5M</h2>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <h5>Estado DIAN</h5>
                        <p class="text-success">Conectado</p>
                    </div>
                </div>
            </div>

            <!-- Invoice Form -->
            <div class="card">
                <div class="card-header">Nueva Factura de Venta</div>
                <form id="invoiceForm">
                    <!-- Cliente -->
                    <h4>Datos del Cliente</h4>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Identificación</label>
                                <input type="text" class="form-control" name="customer[id_number]" value="222222222"
                                    required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Tipo ID</label>
                                <select class="form-control" name="customer[id_type]">
                                    <option value="13">Cédula</option>
                                    <option value="31" selected>NIT</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Nombre / Razón Social</label>
                                <input type="text" class="form-control" name="customer[name]" value="Adquirente Pruebas"
                                    required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="customer[email]"
                                    value="cliente@example.com" required>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Detalles -->
                    <h4>Detalle de Productos</h4>
                    <div id="itemsContainer">
                        <div class="row item-row">
                            <div class="col">
                                <input type="text" class="form-control" placeholder="Código" name="items[0][code]"
                                    value="P-001">
                            </div>
                            <div class="col">
                                <input type="text" class="form-control" placeholder="Descripción" name="items[0][name]"
                                    value="Servicio de Desarrollo">
                            </div>
                            <div class="col">
                                <input type="number" class="form-control" placeholder="Cant." name="items[0][quantity]"
                                    value="1">
                            </div>
                            <div class="col">
                                <input type="number" class="form-control" placeholder="Precio" name="items[0][price]"
                                    value="100000">
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn" style="margin-top:10px;" onclick="addItem()">+ Agregar
                        Item</button>

                    <hr>

                    <!-- Totales y Acción -->
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Forma de Pago</label>
                                <select class="form-control" name="payment_form">
                                    <option value="1">Contado</option>
                                    <option value="2">Crédito</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6" style="text-align: right;">
                            <button type="submit" class="btn btn-primary btn-lg">Generar y Enviar a DIAN</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Result Area -->
            <div id="resultArea" style="display:none;" class="card">
                <div class="card-header">Resultado de Transacción</div>
                <pre id="resultContent" style="background: #f8f9fa; padding: 10px; border-radius: 5px;"></pre>
            </div>

        </div>
    </div>

    <script src="/js/app.js"></script>
</body>

</html>