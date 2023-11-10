<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">   
                <div class="card-body">
                   <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="unidad" class="form-label">Unidad</label>
                        <input type="text" name="unidad" id="unidad" class="form-control" value="{{ $data->unidad }}" @if (!$data->id) required="required" @endif>
                    </div> 

                    <div class="col-md-6 mb-3">
                        <label for="operador" class="form-label">Operador</label>
                        <input type="text" name="operador" id="operador" class="form-control" value="{{ $data->operador }}" @if (!$data->id) required="required" @endif>
                    </div> 

                    <div class="col-md-6 mb-3">
                        <label for="origen" class="form-label">Origen</label>
                        <input type="text" name="origen" id="origen" class="form-control" value="{{ $data->origen }}" @if (!$data->id) required="required" @endif>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="destino" class="form-label">Destino</label>
                        <input type="text" name="destino" id="destino" class="form-control" value="{{ $data->destino }}" @if (!$data->id) required="required" @endif>
                    </div>  
                    
                   </div>

                   <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status"  id="example-select" class="form-select" required="required">
                                <option value="0" @if($data->status == 0) selected @endif>Activo</option>
                                <option value="1" @if($data->status == 1) selected @endif>Inactivo</option>
                            </select>
                        </div>
                    
                        <div class="col-md-6 mb-3">
                            @if($data->id)
                            <label for="new_password" class="form-label">Cambiar contraseña <small>(Solo si desea hacerlo)</small> </label>
                            <input type="password" name="new_password" class="form-control">
                            @else
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required="required">
                            @endif
                        </div> 

                        
                        <div class="col-md-12 mb-3">
                            <label for="inputEmail6">Asignar Permisos <small>(Ver/Editar)</small> </label>
                            <select name="perm[]" class="form-select selectize-close-btn" multiple="true">
                                @foreach(DB::table('perm')->get() as $p)
                                <option value="{{ $p->name }}" @if(in_array($p->name,$array)) selected @endif>{{ $p->name }}</option>
                                @endforeach
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