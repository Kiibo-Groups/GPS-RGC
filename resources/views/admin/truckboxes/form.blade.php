<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">   
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name_truck_box" class="form-label">Nombre de la Caja</label>
                            <input type="text" name="name_truck_box" id="name_truck_box" class="form-control" value="{{ $data->name_truck_box }}" @if (!$data->id) required="required" @endif>
                        </div> 

                        <div class="col-md-6 mb-3">
                            <label for="id_truck_box" class="form-label">Identificador unico</label>
                            <input type="text" name="id_truck_box" id="id_truck_box" class="form-control" value="{{ $data->id_truck_box }}" @if (!$data->id) required="required" @endif>
                        </div> 
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="descript_truck_box" class="form-label">Descripción Corta</label>
                            <input type="tel" name="descript_truck_box" id="descript_truck_box" class="form-control" value="{{ $data->descript_truck_box }}">
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