<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">   
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name_unit" class="form-label">Nombre de la unidad</label>
                            <input type="text" name="name_unit" id="name_unit" class="form-control" value="{{ $data->name_unit }}" @if (!$data->id) required="required" @endif>
                        </div> 

                        <div class="col-md-6 mb-3">
                            <label for="id_unit" class="form-label">Identificador unico</label>
                            <input type="text" name="id_unit" id="id_unit" class="form-control" value="{{ $data->id_unit }}" @if (!$data->id) required="required" @endif>
                        </div> 
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="registration_unit" class="form-label">Matricula y/o # Folio</label>
                            <input type="text" name="registration_unit" id="registration_unit" class="form-control" value="{{ $data->registration_unit }}" @if (!$data->id) required="required" @endif>
                        </div> 

                        <div class="col-md-6 mb-3">
                            <label for="descript" class="form-label">Descripción Corta</label>
                            <input type="tel" name="descript" id="descript" class="form-control" value="{{ $data->descript }}">
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