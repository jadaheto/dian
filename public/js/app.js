function addItem() {
    alert("Funcionalidad de agregar múltiples items pendiente para v2 (JavaScript dinámico)");
}

document.getElementById('invoiceForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Recolectar datos
    const formData = new FormData(this);
    const data = {
        prefix: 'SETT',
        number: Math.floor(Math.random() * 10000) + 1, // Simular consecutivo
        customer: {
            id_type: formData.get('customer[id_type]'),
            id_number: formData.get('customer[id_number]'),
            name: formData.get('customer[name]'),
            email: formData.get('customer[email]'),
            dv: '3' // Hardcoded demo
        },
        payment_form: formData.get('payment_form'),
        items: [
            {
                code: formData.get('items[0][code]'),
                name: formData.get('items[0][name]'),
                quantity: parseFloat(formData.get('items[0][quantity]')),
                price: parseFloat(formData.get('items[0][price]')),
                tax_rate: 19.00
            }
        ]
    };

    const resultArea = document.getElementById('resultArea');
    const resultContent = document.getElementById('resultContent');
    
    resultArea.style.display = 'block';
    resultContent.textContent = 'Procesando... Generando XML, Firmando y Enviando a DIAN...';

    try {
        const response = await fetch('/api/invoices/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        resultContent.textContent = JSON.stringify(result, null, 4);
        
        if (response.ok) {
            resultContent.style.color = 'green';
        } else {
            resultContent.style.color = 'red';
        }

    } catch (error) {
        resultContent.textContent = 'Error de comunicación: ' + error.message;
        resultContent.style.color = 'red';
    }
});
