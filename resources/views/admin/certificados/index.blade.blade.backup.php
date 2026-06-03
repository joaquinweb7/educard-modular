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
                    @foreach ($certificados as $certificado) <tr>
                        <td class="checkbox-column"> {{ $certificado->id }} </td>
                        <td>{{ $certificado->nombre_estudiante }}</td>
                        <td>{{ $certificado->nombre_curso }}</td>
                        <td>{{ $certificado->carnet }}</td>
                        <td>{{ $certificado->email }}</td>
                        <td>{{ $certificado->codigo }}</td>
                        <td>
                            <div class="d-flex">
                                <div class="usr-img-frame me-2 rounded-circle"> 
                                    <a href="{{ route('admin.certificados.test', ['plantilla' => $certificado->plantilla->id]) }}" target="_blank"><img alt="avatar" class="img-fluid rounded-circle" style="width: 38px; height: 38px;  object-fit: contain;" src="{{ asset('storage/'.$certificado->plantilla->imagen) }}"> </div>
                            </div>
                        </td>
                        <td><span class="inv-date"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg> {{ $certificado->created_at }} </span> </td>
                        <td class="conteiner">
                            <div class="row">
                                <div class="col-sm-12 d-flex"> 
                                    <form method="POST" action="{{ route('admin.certificados.smtp.sendEmail', ['certificado' => $certificado->id]) }}">
                                        @csrf
                                        <button onclick="return confirm('¿Seguro que quieres enviar el certificado al correo?: {{ $certificado->email }}')" class="badge badge-light-secondary text-start me-2 action-edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-send"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                        </button>
                                    </form>

                                    <a class="badge badge-light-primary text-start me-2 action-edit"
                                        href="{{ route('admin.certificados.edit', ['certificado' => $certificado->id]) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="feather feather-edit-3">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                    </a>
                                    <form method="POST"
                                        action="{{ route('admin.certificados.destroy', ['certificado' => $certificado->id]) }}">
                                        @csrf @method('DELETE') 
                                        <button class="badge badge-light-danger text-start action-delete" type="submit"
                                            onclick="return confirm('Estás seguro?')"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-trash">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                </path></svg> 
                                        </button> 
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr> 
                    @endforeach 
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('plugins/jquery.3.7.1.js') }}"></script>
<script src="{{ asset('plugins/src/table/datatable/datatables.js') }}"></script>
<script src="{{ asset('plugins/src/table/datatable/button-ext/dataTables.buttons.min.js') }}"></script>
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
                a?($(this).prop("checked", !0), $(this).closest("tr").addClass("active")): ($(this).prop("checked", !1), $(this).closest("tr").removeClass("active"))
            })
        }),
        tb_var.on("change", "tbody tr .new-control", function() {
            $(this).parents("tr").toggleClass("active")
        })
    }

    var invoiceList = $('#invoice-list').DataTable({
        "dom": "<'inv-list-top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'l<'dt-action-buttons align-self-center'B>><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f<'toolbar align-self-center'>>>>" +
            "<'table-responsive'tr>" +
            "<'inv-list-bottom-section d-sm-flex justify-content-sm-between text-center'<'inv-list-pages-count  mb-sm-0 mb-3'i><'inv-list-pagination'p>>",

        headerCallback:function(e, a, t, n, s) {
            e.getElementsByTagName("th")[0].innerHTML=`
            <div class="form-check form-check-primary d-block new-control">
                <input class="form-check-input chk-parent" type="checkbox" id="form-check-default">
            </div>`
        },
        columnDefs:[{
            targets:0,
            width:"30px",
            className:"",
            orderable:!1,
            render:function(e, a, t, n) {
                return `
                <div class="form-check form-check-primary d-block new-control">
                    <input class="form-check-input child-chk" type="checkbox" id="form-check-default">
                </div>`
            },
        }],
        buttons: [
            {
                text: 'Crear nuevo certificado',
                className: 'btn btn-primary',
                action: function(e, dt, node, config ) {
                    window.location = '/certificados/create';
                }
            }
        ],
        "oLanguage": {
            "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
            "sInfo": "Mostrando pagina _PAGE_ de _PAGES_",
            "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            "sSearchPlaceholder": "Buscar...",
            "sLengthMenu": "Resultados :  _MENU_",
        },
        "stripeClasses": [],
        "lengthMenu": [7, 10, 20, 50],
        "pageLength": 10
    });

    multiCheck(invoiceList);
</script>
@endpush
