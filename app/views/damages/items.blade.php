@extends('layouts.master')

@section('head')

    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/start/jquery-ui.css" />
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script>
        (function($){

            $(document).on('ready', iniciar);

            function iniciar() {
               convertirBotones();
            } //iniciar

        })(jQuery);

        function convertirBotones()
        {
            $('.button').button();
        }

        function validar() {
            if ($('#comments').val() != '') {
                $('#damageStoreForm').submit();
            } else {
                alert('Faltan campos por diligenciar.');
            }
        }
    </script>

@stop

@section('content')

        <div class="panel panel-warning">
            <div class="panel-heading">
                <span class="glyphicon glyphicon-flag"></span>
                Código del daño: {{ $damage->id }}
            </div>
            <div class="panel-body">
                <ul class="damage">
                    <li><strong>Estado:</strong> {{ $damage->status }}</li>
                    <li><strong>Fecha de creación:</strong> {{ $damage->created_at }}</li>
                    <li><strong>Para la sucursal:</strong> {{ $damage->branch->name }}</li>
                    <li><strong>Usuario:</strong> {{ $damage->user->name }}</li>
                    <li><strong>Fecha de modificación:</strong> {{ $damage->updated_at }}</li>
                </ul>
                <p><strong>Comentarios del remisionero:</strong> {{ $damage->comments }}</p>

                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Cód. Artículo</th>
                        <th>Nombre del artículo</th>
                        <th>Cantidad</th>
                    </tr>
                    @foreach($ditems as $ditem)
                        <tr>
                            <td>{{ $ditem->article->id }}</td>
                            <td>{{ $ditem->article->name }}</td>
                            <td>{{ $ditem->amount .' '. $ditem->article->unit }}</td>
                        </tr>
                    @endforeach
                </table>
            </div><!-- /.panel-body -->
            <div class="panel-footer">
                @if(isset($damage->DamageStore->comments))

                    <p class="label label-info">Finalizado por {{ $damage->DamageStore->user->name }}</p>
                    <p class="alert alert-success">
                        <span class="glyphicon glyphicon-comment"></span>
                        {{ $damage->DamageStore->comments }}
                    </p>

                @elseif( ( (Auth::user()->permitido('bodeguero') && $damage->branch->id == Auth::user()->roles()->first()->branch->id) || Auth::user()->permitido('remisionero') || Auth::user()->permitido('administrador') ) && $damage->status == 'pendiente')

                    {{ Form::open(array('url' => 'damages/damage-store', 'id' => 'damageStoreForm')) }}
                        {{ Form::input('hidden', 'damage', $damage->id) }}
                        {{ Form::input('hidden', 'branch_id', $damage->branch->id) }}
                        {{ Form::textarea('comments', '', array('id' => 'comments', 'rows' => '3', 'class' => 'form-control', 'placeholder' => 'Comentarios del bodeguero.', 'maxlength' => '255', 'required')) }}
                         <a href="#damageStoreModal" class="button" data-toggle="modal">
                            <span class="glyphicon glyphicon-floppy-save"></span>
                            Finalizar daño
                        </a>
                        {{ Form::submit('Enviar', array('class' => 'hidden')) }}
                    {{ Form::close() }}

                    @if(Auth::user()->permitido('remisionero') || Auth::user()->permitido('administrador'))

                            {{ '<a href="'. url('damages/cancel/'. $damage->id) .'" class="btn btn-danger btn-sm">
                                <span class="glyphicon glyphicon-minus-sign"></span>
                                Cancelar remisión
                            </a>' }}

                    @endif

                @endif
            </div><!-- /.panel-footer -->
        </div><!-- /.panel -->

      <!-- Modal -->
      <div class="modal fade" id="damageStoreModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="false">&times;</button>
              <h4 class="modal-title">Confirmar</h4>
            </div>
            <div class="modal-body">
              ¿Deseas finalizar el daño? Si haces clic en <strong>Sí</strong> el stock disminuirá.
            </div>
            <div class="modal-footer">
              <a href="#" class="btn btn-danger" data-dismiss="modal">No</a>
              <a href="javascript:validar();" class="btn btn-primary">Sí</a>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div><!-- /.modal -->

@stop