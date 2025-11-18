<x-app-layout>
  <main class="main-content">
    <x-app.navbar />

    <div class="container py-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="m-0">Nueva factura</h4>
        <a href="{{ route('facturas.index') }}" class="btn btn-secondary">Volver</a>
      </div>

      {{-- Errores --}}
      @if ($errors->any())
        <div class="alert alert-danger">
          <b>Hay errores en el formulario:</b>
          <ul class="mb-0">
            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
          </ul>
        </div>
      @endif

      @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      <div class="card">
        <div class="card-body">
          <form action="{{ route('facturas.store') }}" method="POST">
            @csrf

            {{-- Datos del cliente --}}
            <div class="card mb-3">
              <div class="card-header">
                Datos del cliente
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label for="socio_id" class="form-label">Socio (opcional)</label>
                  <select name="socio_id" id="socio_id" class="form-select">
                    <option value="">-- Seleccione un socio --</option>
                    @foreach ($socios as $socio)
                      <option value="{{ $socio->id }}" {{ old('socio_id') == $socio->id ? 'selected' : '' }} >
                        {{ $socio->apellidos }} {{ $socio->nombres }} ({{ $socio->cedula }})
                      </option>
                    @endforeach
                  </select>
                  <div class="form-text">
                    Si seleccionas un socio, los datos del cliente se pueden rellenar automáticamente.
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="cliente_nombre" class="form-control"
                           value="{{ old('cliente_nombre') }}" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="cliente_apellido" class="form-control"
                           value="{{ old('cliente_apellido') }}">
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Cédula</label>
                    <input type="text" name="cliente_cedula" class="form-control"
                           value="{{ old('cliente_cedula') }}">
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="cliente_telefono" class="form-control"
                           value="{{ old('cliente_telefono') }}">
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="cliente_email" class="form-control"
                           value="{{ old('cliente_email') }}">
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Fecha *</label>
                  <input type="date" name="fecha" class="form-control"
                         value="{{ old('fecha', now()->format('Y-m-d')) }}" required>
                </div>
              </div>
            </div>

            {{-- Selección de tipo de ítem --}}
            <div class="card mb-3">
              <div class="card-header">
                Selecciona el tipo de ítem
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label for="tipo_item" class="form-label">¿Qué tipo de ítem deseas agregar?</label>
                  <select name="tipo_item" id="tipo_item" class="form-select" required>
                    <option value="">— Selecciona —</option>
                    <option value="beneficio">Beneficio</option>
                    <option value="servicio">Servicio</option>
                  </select>
                </div>
              </div>
            </div>

            {{-- Detalle --}}
            <div class="card mb-3" id="detalle-items" style="display:none;">
              <div class="card-header d-flex justify-content-between align-items-center">
                <span>Detalle de la factura</span>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-item">
                  + Agregar ítem
                </button>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-sm align-middle" id="tabla-items">
                    <thead class="table-light">
                      <tr>
                        <th style="width: 35%;">Descripción</th>
                        <th style="width: 15%;">Cantidad</th>
                        <th style="width: 20%;">Precio</th>
                        <th style="width: 20%;">Subtotal</th>
                        <th style="width: 10%;"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="item-row">
                        <td>
                          <!-- Este campo se actualizará según el tipo de ítem -->
                          <select name="items[descripcion][]" class="form-select descripcion-select" required>
                            <option value="">— Selecciona un ítem —</option>
                          </select>
                        </td>
                        <td>
                          <input type="number" name="items[cantidad][]" class="form-control cantidad-input"
                                 value="1" min="1" required>
                        </td>
                        <td>
                          <input type="number" step="0.01" min="0"
                                 name="items[precio][]" class="form-control precio-input"
                                 value="0.00" required>
                        </td>
                        <td class="text-end subtotal-text">
                          $ 0.00
                        </td>
                        <td class="text-center">
                          <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item">
                            ✕
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div class="text-end mt-2">
                  <strong>Total estimado: $ <span id="total-estimado">0.00</span></strong>
                </div>
              </div>
            </div>

            <div class="mt-3 d-flex gap-2 justify-content-between">
              <a href="{{ route('facturas.index') }}" class="btn btn-secondary">Cancelar</a>
              <button type="submit" class="btn btn-primary">Guardar factura</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const tipoItemSelect = document.getElementById('tipo_item');
        const detalleItemsDiv = document.getElementById('detalle-items');
        const tabla = document.querySelector('#tabla-items tbody');
        const btnAdd = document.getElementById('btn-add-item');

        tipoItemSelect.addEventListener('change', function () {
          const tipoItem = tipoItemSelect.value;
          if (tipoItem === 'beneficio') {
            detalleItemsDiv.style.display = 'block';
            // Mostrar solo el select de Beneficio
            document.querySelectorAll('.descripcion-select').forEach(select => {
              select.innerHTML = '<option value="">— Selecciona un Beneficio —</option>';
              @foreach ($beneficios as $beneficio)
                select.innerHTML += `<option value="{{ $beneficio->id }}" data-valor="{{ $beneficio->valor }}">
                                      {{ $beneficio->nombre }} (${{ number_format($beneficio->valor, 2) }})
                                      </option>`;
              @endforeach
            });
          } else if (tipoItem === 'servicio') {
            detalleItemsDiv.style.display = 'block';
            // Mostrar solo el select de Servicio
            document.querySelectorAll('.descripcion-select').forEach(select => {
              select.innerHTML = '<option value="">— Selecciona un Servicio —</option>';
              @foreach ($servicios as $servicio)
                select.innerHTML += `<option value="{{ $servicio->id }}" data-valor="{{ $servicio->valor }}">
                                      {{ $servicio->nombre }} (${{ number_format($servicio->valor, 2) }})
                                      </option>`;
              @endforeach
            });
          } else {
            detalleItemsDiv.style.display = 'none';
          }
        });

        // Agregar fila de ítem
        btnAdd.addEventListener('click', function () {
          const primeraFila = tabla.querySelector('tr.item-row');
          const nuevaFila = primeraFila.cloneNode(true);

          // Resetear select
          nuevaFila.querySelector('.descripcion-select').innerHTML = '<option value="">— Selecciona un ítem —</option>';

          if (tipoItemSelect.value === 'beneficio') {
            nuevaFila.querySelector('.descripcion-select').innerHTML = '<option value="">— Selecciona un Beneficio —</option>';
            @foreach ($beneficios as $beneficio)
              nuevaFila.querySelector('.descripcion-select').innerHTML += `<option value="{{ $beneficio->id }}" data-valor="{{ $beneficio->valor }}">
                {{ $beneficio->nombre }} (${{ number_format($beneficio->valor, 2) }})
                </option>`;
            @endforeach
          } else if (tipoItemSelect.value === 'servicio') {
            nuevaFila.querySelector('.descripcion-select').innerHTML = '<option value="">— Selecciona un Servicio —</option>';
            @foreach ($servicios as $servicio)
              nuevaFila.querySelector('.descripcion-select').innerHTML += `<option value="{{ $servicio->id }}" data-valor="{{ $servicio->valor }}">
                {{ $servicio->nombre }} (${{ number_format($servicio->valor, 2) }})
                </option>`;
            @endforeach
          }

          // Resetear valores de cantidad y precio
          nuevaFila.querySelector('.cantidad-input').value = 1;
          nuevaFila.querySelector('.precio-input').value = '0.00';
          nuevaFila.querySelector('.subtotal-text').textContent = '$ 0.00';

          tabla.appendChild(nuevaFila);
        });

        // Actualizar el subtotal y total de la factura
        function actualizarSubtotalFila(row) {
          const cantidad = parseFloat(row.querySelector('.cantidad-input').value) || 0;
          const precio = parseFloat(row.querySelector('.precio-input').value) || 0;
          const subtotal = cantidad * precio;
          row.querySelector('.subtotal-text').textContent = '$ ' + subtotal.toFixed(2);
          return subtotal;
        }

        function actualizarTotal() {
          let total = 0;
          document.querySelectorAll('#tabla-items tbody tr.item-row').forEach(row => {
            total += actualizarSubtotalFila(row);
          });
          document.getElementById('total-estimado').textContent = total.toFixed(2);
        }

        tabla.addEventListener('change', function () {
          actualizarTotal();
        });

        tabla.addEventListener('input', function () {
          actualizarTotal();
        });

        tabla.addEventListener('click', function (e) {
          if (e.target.classList.contains('btn-remove-item')) {
            const row = e.target.closest('tr.item-row');
            row.remove();
            actualizarTotal();
          }
        });

        actualizarTotal();
      });
    </script>

    <x-app.footer />
  </main>
</x-app-layout>
