@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/src/table/datatable/datatables.css') }}">









@endpush
@section('content')
<div class="col-xl-12 col-lg-12 col-sm-12 layout-top-spacing layout-spacing"> 
    @if (session('success')) 
    <div class="alert alert-light-success alert-dismissible fade show border-0 mb-4" role="alert" id="alert-div"> <button
        type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> <svg
            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="feather feather-x close" data-bs-dismiss="alert">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg> </button> <strong id="type-strong">ÉXITO</strong>
        <span id="remaining-span">{{ session('success')}}</span> 
    </div> 
    @endif 
    @if (session('error')) 
    <div class="alert alert-light-danger alert-dismissible fade show border-0 mb-4" role="alert" id="alert-div"> <button
        type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> <svg
            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="feather feather-x close" data-bs-dismiss="alert">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg> </button> <strong id="type-strong">ERROR</strong>
        <span id="remaining-span">{{ session('error')}}</span> 
    </div> 
    @endif 
    <div class="widget-content widget-content-area br-8">
        <div class="table-wrapper">
        <div class="row mb-3">
            <div class="col">
                <input type="text" id="filtro-nombre" class="form-control" placeholder="Buscar por nombre">
            </div>
            <div class="col">
                <input type="text" id="filtro-carnet" class="form-control" placeholder="Buscar por carnet">
            </div>
            <div class="col">
                <input type="text" id="filtro-email" class="form-control" placeholder="Buscar por correo">
            </div>
            <div class="col">
                <input type="text" id="filtro-codigo" class="form-control" placeholder="Buscar por código">
            </div>
            </div>

            <table id="invoice-list" class="table dt-table-hover" style="width:100%">
            <thead>
                <tr>
                <th class="checkbox-column"> Record no. </th>
                <th>Nombre</th>
                <th>Curso</th>
                <th>Carnet</th>
                <th>Correo</th>
                <th>CÓDIGO</th>
                <th>Certificado</th>
                <th>Creado</th>
                <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody> {{-- <-- SIN @foreach --}}
            </table>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('plugins/jquery.3.7.1.js') }}"></script>
<script src="{{ asset('plugins/src/table/datatable/datatables.js') }}"></script>
<script src="{{ asset('plugins/src/table/datatable/button-ext/dataTables.buttons.min.js') }}"></script>

{{-- Buttons --}}
<script src="{{ asset('plugins/src/table/datatable/button-ext/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/src/table/datatable/button-ext/jszip.min.js') }}"></script>    
<script src="{{ asset('plugins/src/table/datatable/button-ext/buttons.html5.min.js') }}"></script>
<script src="{{ asset('plugins/src/table/datatable/button-ext/buttons.print.min.js') }}"></script>
<script src="{{ asset('plugins/src/table/datatable/custom_miscellaneous.js') }}"></script>

<script>
    function checkall(clickchk, relChkbox) {
        var checker = $('#' + clickchk);
        var multichk = $('.' + relChkbox);
        checker.click(function () {
            multichk.prop('checked', $(this).prop('checked'));
        });
    }

    function multiCheck(tb_var) {
        tb_var.on("change", ".chk-parent", function() {
            var e=$(this).closest("table").find("td:first-child .child-chk"), a=$(this).is(":checked");
            $(e).each(function() {
                a?($(this).prop("checked", true), $(this).closest("tr").addClass("active"))
                 :($(this).prop("checked", false), $(this).closest("tr").removeClass("active"))
            })
        });
        tb_var.on("change", "tbody tr .new-control", function() {
            $(this).parents("tr").toggleClass("active")
        });
    }

    var invoiceList = $('#invoice-list').DataTable({
        processing: true,
        serverSide: true,
        deferRender: true,
        searchDelay: 0,
        ajax: {
            url: '{{ route('admin.certificados.data') }}',
            type: 'GET',
            // Si prefieres POST:
            // type: 'POST',
            // headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
            data: function (d) {
                d.filtro_nombre = $('#filtro-nombre').val();
                d.filtro_carnet = $('#filtro-carnet').val();
                d.filtro_email  = $('#filtro-email').val();
                d.filtro_codigo = $('#filtro-codigo').val();
            }
        },
        dom: "<'inv-list-top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'l<'dt-action-buttons align-self-center'B>><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f<'toolbar align-self-center'>>>>" +
             "<'table-responsive'tr>" +
             "<'inv-list-bottom-section d-sm-flex justify-content-sm-between text-center'<'inv-list-pages-count  mb-sm-0 mb-3'i><'inv-list-pagination'p>>",

        headerCallback:function(e, a, t, n, s) {
            e.getElementsByTagName("th")[0].innerHTML=`
            <div class="form-check form-check-primary d-block new-control">
                <input class="form-check-input chk-parent" type="checkbox" id="form-check-default">
            </div>`
        },

        columnDefs: [{
            targets: 0,           // checkbox
            width: "30px",
            orderable: false,
            searchable: false,
            data: null,           // dummy
            render: function() {
                return `
                <div class="form-check form-check-primary d-block new-control">
                    <input class="form-check-input child-chk" type="checkbox" id="form-check-default">
                </div>`;
            },
        }],

        // Botón que ya tenías
        buttons: [
            {
                text: 'Crear nuevo certificado',
                className: 'btn btn-primary',
                action: function() { window.location = '/certificados/create'; }
            },
            { extend: 'copy', className: 'btn' },
            { extend: 'csv', className: 'btn' },
            { extend: 'excel', className: 'btn' },
            { extend: 'print', className: 'btn' }
        ],
        oLanguage: {
            oPaginate: {
                sPrevious: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                sNext:     '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
            },
            sInfo: "Mostrando pagina _PAGE_ de _PAGES_",
            sSearch: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            sSearchPlaceholder: "Buscar...",
            sLengthMenu: "Resultados :  _MENU_",
        },

        stripeClasses: [],
        lengthMenu: [7, 10, 20, 50],
        pageLength: 10,

        // Mapa EXACTO a lo que devuelve el endpoint
        columns: [
            { data: null },     // checkbox
            { data: 'nombre_estudiante', name:'nombre_estudiante' },
            { data: 'nombre_curso_id',      name:'nombre_curso_id' },
            { data: 'carnet',            name:'carnet' },
            { data: 'email',             name:'email' },
            { data: 'codigo',            name:'codigo' },
            { data: 'certificado',       orderable:false, searchable:false },
            { data: 'created_at',        name:'created_at' },
            { data: 'acciones',          orderable:false, searchable:false },
        ],

        // Orden inicial (por nombre o cambia a created_at si prefieres)
        order: [[1, 'asc']],
    });
    $('#filtro-nombre, #filtro-carnet, #filtro-email, #filtro-codigo')
    .on('keyup change', function() {
        invoiceList.draw();
    });

    // Delegados para checks (funciona con redraw)
    multiCheck($('#invoice-list'));



</script>  

@endpush
