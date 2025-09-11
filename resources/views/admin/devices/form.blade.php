<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card">   
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="name_device" class="form-label">Nombre del Dispositivo</label>
                            <input type="text" name="name_device" id="name_device" class="form-control" value="{{ $data->name_device }}" @if (!$data->id) required="required" @endif>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Número para envio de comandos</label>
                            <div class="input-group">
                                <span class="input-group-text">+52</span>
                                <input type="tel" 
                                    name="phone" 
                                    id="phone" 
                                    class="form-control" 
                                    pattern="[1-9][0-9]{9}"
                                    maxlength="10"
                                    placeholder="10 dígitos sin lada"
                                    value="{{ substr($data->phone, 3) ?? '' }}"
                                    @if (!$data->id) required="required" @endif
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0,10);"
                                    onkeyup="validatePhone(this)">
                            </div>
                            <div class="form-text text-muted">Formato: 10 dígitos (ej: 4491234567)</div>
                            <div id="phoneError" class="invalid-feedback">Por favor ingresa un número válido de 10 dígitos</div>
                        </div>
                        
                        <script>
                            function validatePhone(input) {
                                const phoneRegex = /^[1-9][0-9]{9}$/;
                                const isValid = phoneRegex.test(input.value);
                                
                                if (isValid) {
                                    input.classList.remove('is-invalid');
                                    input.classList.add('is-valid');
                                } else {
                                    input.classList.remove('is-valid');
                                    input.classList.add('is-invalid');
                                }
                            }

                            // Asegurarse de que el valor en el formulario incluya el +52 al enviar
                            document.querySelector('form').addEventListener('submit', function(e) {
                                const phoneInput = document.getElementById('phone');
                                if (phoneInput.value) {
                                    phoneInput.value =  phoneInput.value;
                                }
                            });
                        </script>

                        <div class="col-md-6 mb-3">
                            <label for="uuid_device" class="form-label">UUID/IMEI del Dispositivo</label>
                            <input type="text" name="uuid_device" id="uuid_device" class="form-control" value="{{ $data->uuid_device }}" @if (!$data->id) required="required" @endif>
                        </div> 
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="descript_device" class="form-label">Descripción Corta</label>
                            <input type="tel" name="descript_device" id="descript_device" class="form-control" value="{{ $data->descript_device }}">
                        </div> 

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status"  id="example-select" class="form-select" required="required">
                                <option value="0" @if($data->status == 0) selected @endif>Activo</option>
                                <option value="1" @if($data->status == 1) selected @endif>Inactivo</option>
                            </select>
                        </div>
                    </div> 
                </div>

                <div class="mt-5" style="justify-items: end;display: grid;padding:20px;">
                    <button type="submit" class="btn btn-primary mb-2 btn-pill">
                        @if(!$data->id)
                        Agregar
                        @else 
                        Actualizar
                        @endif
                    </button>
                </div>
            </div>
        </div>           
    </div>
</div>