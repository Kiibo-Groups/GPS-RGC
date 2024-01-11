 <!-- Right modal content -->
 <div id="assign-devices-{{ $row->id }}" class="modal fade modal-slide-right" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-right">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Asignación de Dispositivo {{ $row->name }}
                </h5> 
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">  </button>
            </div>

            {!! Form::open(['url' => [$form_url_gps],'files' => true,'method' => 'POST']) !!}
            <div class="modal-body">
                <input type="hidden" name="truck_box_id" value="{{ $row->id }}">
                <div class="row">
                    <div class="form-group col-md-12" style="text-align: left">
                        <label for="inputEmail4" >Selecciona el dispositivo a asignar</label>
                        <select name="gps_devices_id" class="form-control">
                            @foreach($gps as $gps) 
                                <option value="{{ $gps->id }}" @if($gps->id == $row->gps) selected @endif>
                                    {{ $gps->name_device }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Asignar Dispositivo</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
