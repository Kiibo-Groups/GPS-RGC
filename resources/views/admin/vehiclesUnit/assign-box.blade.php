 <!-- Right modal content -->
 <div id="assign-box-{{$row->id}}" class="modal fade modal-slide-right" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-right">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Asignación de Unidades
                </h5> 
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">  </button>
            </div>
            {!! Form::open(['url' => [$form_url_box],'files' => true,'method' => 'POST']) !!}
            <div class="modal-body">
                <input type="hidden" name="vehicle_units_id" value="{{ $row->id }}">
                <div class="row">
                    <div class="form-group col-md-12" style="text-align: left">
                        <label for="inputEmail4" >Selecciona la unidad a asignar</label>
                        <select name="truck_box_id" class="form-control">
                            @foreach($boxes as $bx) 
                                <option value="{{ $bx->id }}" @if($bx->id == $row->box) selected @endif>
                                    {{ $bx->name_truck_box }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Asignar Unidad</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
