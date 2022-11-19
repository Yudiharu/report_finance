@extends('adminlte::page')

@section('title', 'Laporan Aging AP')

@section('content_header')
    
@stop

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
@include('sweet::alert')
<body onLoad="panggil()">
    <div class="box box-solid">
        <div class="modal fade" id="button4"  role="dialog">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">Laporan <b>Aging AP</b></h4>
                </div>
                @include('errors.validation')
                {!! Form::open(['route' => ['laporanagingap.export'],'method' => 'get','id'=>'form', 'target'=>"_blank"]) !!}
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            {{ Form::label('jenis', 'Vendor :') }}              
                                            {{Form::select('vendor', ['SEMUA'=>'SEMUA','Vendor'=>$Vendor], null, ['class'=> 'form-control select2','style'=>'width: 100%','placeholder' => '','id'=>'jenis1','required'=>'required'])}}
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            {{ Form::label('tanggal_awal', 'Dari Tanggal:') }}
                                            {{ Form::date('tanggal_awal',\Carbon\Carbon::now(), ['class'=> 'form-control','id'=>'tanggal1']) }}
                                        </div>
                                    </div>
                                    
                                    {{ Form::hidden('tanggal_akhir',\Carbon\Carbon::now(), ['class'=> 'form-control','id'=>'tanggal2','placeholder'=>'Periode Baru','readonly']) }}

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            {{ Form::label('pilih2', 'Rekap Laporan:') }}              
                                            {{Form::select('rekap', ['Detail' => 'Detail', 'Rekap' => 'Rekap'], null, ['class'=> 'form-control select2','style'=>'width: 100%','placeholder' => '','id'=>'tipe1','required'=>'required','onchange'=>"pilihan();"])}}
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group2">
                                            {{ Form::label('pilih', 'Format Laporan:') }}              
                                            {{Form::select('jenis_report', ['PDF' => 'PDF', 'excel' => 'Excel'], null, ['class'=> 'form-control select2','style'=>'width: 100%','placeholder' => '','id'=>'report1'])}}
                                        </div>
                                    </div>
                                    <!-- <div class="col-sm-8">  
                                        <input type="checkbox" name="ttd" value="1"/>&nbsp;Cetak TTD di halaman baru<br>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="row">
                                {{ Form::submit('Cetak', ['class' => 'btn btn-success crud-submit']) }}
                                {{ Form::button('Close', ['class' => 'btn btn-danger','data-dismiss'=>'modal']) }}&nbsp;
                            </div>
                        </div>
                    {!! Form::close() !!}            
              </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>

</div>
</body>
@stop

@push('css')

@endpush
@push('js')
  
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.select2').select2({
            placeholder: "Pilih",
            allowClear: true,
        });

        function cetakpdf() {
            var registerForm = $("#ADD");
            var formData = registerForm.serialize();

            swal({
            title: "Cetak PDF?",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Ya, Cetak!",
            cancelButtonText: "Batal",
            reverseButtons: !0
        }).then(function (e) {
            if (e.value === true) {
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url:'{!! route('laporanagingap.export') !!}',
                    type:'GET',
                    data:formData,
                    success:function(result) {
                            swal("Berhasil!<br><b>PDF berhasil dicetak</b>");
                    },
                error : function() {
                        swal("GAGAL!<br><b>PDF gagal dicetak</b>");
                    }
                });

            } else {
                e.dismiss;
            }

        }, function (dismiss) {
            return false;
        })

        }
        
        function pilihan() {
            var rekapan = $('#tipe1').val();
            if (rekapan == 'Rekap') {
                $('.form-group2').hide();
                $('#report1').val('PDF');
            }else {
                $('.form-group2').show();
                $('#report1').val('').trigger('change');
            }
        }

        function pilih() {
            var pilih = $("#jenis1").val();

            if (pilih == 'Stock') {
                $('.form-group1').show();
                $('.form-group2').hide();
                document.getElementById("kategori1").required = true; 
            }else{
                $('.form-group2').show();
                $('.form-group1').hide();
                document.getElementById("kategori1").required = false; 
            }
        }

        function load(){
            $('#button4').modal('show');
            $('.form-group2').hide();
        }

        function panggil(){
            load();
            startTime();
        }


        function refreshTable() {
             $('#data-table').DataTable().ajax.reload(null,false);;
        }

        $('.modal-dialog').draggable({
            handle: ".modal-header"
        });

        $('.modal-dialog').resizable({
    
        });
    </script>
@endpush