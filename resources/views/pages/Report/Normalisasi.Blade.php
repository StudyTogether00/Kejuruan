@extends('layout.Layout')
@php
    $session = Session::get('data.data');
    $userid = $session['role'] == 'admin' ? '' : $session['userid'];
    $fullname = $session['role'] == 'admin' ? '' : $session['fullname'];
@endphp

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if ($session['role'] == 'admin')
                        <div class="material-datatables">
                            <table id="datatables" class="table table-striped table-no-bordered table-hover" cellspacing="0"
                                width="100%" style="width:100%">
                                <thead>
                                    <tr>
                                        <th colspan="6">
                                            <x-button type="button" class="btn-outline-info" icon="fa fa-refresh"
                                                label="Refresh" onclick="Refresh()" />
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">NIS/NISN</th>
                                        <th class="text-center">Nama Siswa</th>
                                        <th class="text-center">Hasil</th>
                                        <th class="disabled-sorting text-center">Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    @else
                        <div class="material-datatables">
                            <table id="tableRekap" class="table table-striped table-no-bordered table-hover" cellspacing="0"
                                width="100%" style="width:100%">
                                <thead>
                                    <tr>
                                        <th colspan="6">
                                            <x-button type="button" class="btn-outline-info" icon="fa fa-refresh"
                                                label="Refresh" onclick="DtRekap()" />
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>No</th>
                                        <th>Jurusan</th>
                                        <th>Nilai Akhir</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($session['role'] == 'admin')
        {{-- Table Rekap Modal --}}
        <x-modal-form id="RekapData" title="labelRekap" class="modal-lg">
            <div class="modal-body">
                <div class="row">
                    <div class="material-datatables col-sm-12">
                        <table id="tableRekap" class="table table-striped table-no-bordered table-hover" cellspacing="0"
                            width="100%" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jurusan</th>
                                    <th>Nilai Akhir</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </x-modal-form>
    @endif

    {{-- Add Edit Modal --}}
    <x-modal-form id="AddEditData" title="labelAddEdit" class="modal-lg">
        <div class="modal-body">
            <div class="row">
                <div class="material-datatables col-sm-12">
                    <table id="tableMaple" class="table table-striped table-no-bordered table-hover" cellspacing="0"
                        width="100%" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kriteria</th>
                                <th>Bobot</th>
                                <th>Nilai</th>
                                <th>Matrix</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-left">Nilai Akhir</th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </x-modal-form>
@endsection
@push('scripts')
    <script type="text/javascript">
        let table, id_tbl = "#datatables";
        let table1, table2, tridx = 0,
            tahun = "2024";
        let nama_siswa = "{{ $fullname }}",
            nisn = "{{ $userid }}",
            kd_jurusan = "",
            dtDetail = {};
        let processData = {};

        Refresh = function() {
            if (!$.fn.DataTable.isDataTable(id_tbl)) {
                let dtu = {
                    id: id_tbl,
                    data: {
                        url: $apiUrl + "Report/Normalisasi/Keputusan",
                        param: function() {
                            var d = {};
                            d.tahun = tahun;
                            return JSON.stringify(d);
                        }
                    }
                };
                table = PDataTables(dtu, [{
                    "data": null,
                    "className": "text-center",
                    "render": function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    "data": "nisn",
                }, {
                    "data": "nama_siswa",
                }, {
                    "data": "nama_jurusan",
                }, {
                    "data": null,
                    "orderable": false,
                    "className": "text-center",
                    render: function(data, type, row, meta) {
                        let html = "";
                        html += btnDataTable("View Detail Nilai", "btn-outline-primary view",
                            "fa fa-eye btn-outline-primary", true);
                        return html;
                    }
                }]);
                table.on('click', '.view', function() {
                    $tr = $(this).closest('tr');
                    var data = table.row($tr).data();
                    nama_siswa = data.nama_siswa;
                    nisn = data.nisn;
                    $("#MRekapData h4[labelRekap]").text("Data Nilai Siswa " + nama_siswa);
                    DtRekap();
                    ShowModal("MRekapData");
                });
            } else {
                table.ajax.reload();
            }
        }

        DtRekap = function() {
            if (!$.fn.DataTable.isDataTable("#tableRekap")) {
                let dtu = {
                    id: "#tableRekap",
                    data: {
                        url: $apiUrl + "Report/Normalisasi/NilaiMatrix",
                        param: function() {
                            var d = {};
                            d.tahun = tahun;
                            d.nisn = nisn;
                            return JSON.stringify(d);
                        }
                    }
                };
                table2 = PDataTables(dtu, [{
                    "data": null,
                    "className": "text-center",
                    "render": function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                }, {
                    "data": "nama_jurusan",
                }, {
                    "data": "nilai_akhir",
                }, {
                    "data": null,
                    "orderable": false,
                    "className": "text-center",
                    render: function(data, type, row, meta) {
                        let html = "";
                        html += btnDataTable("View Detail Nilai", "btn-outline-primary view",
                            "fa fa-eye btn-outline-primary", true);
                        return html;
                    }
                }]);
                table2.on('click', '.view', function() {
                    $tr = $(this).closest('tr');
                    var data = table2.row($tr).data();
                    let form_id = "#FAddEditData";
                    kd_jurusan = data.kd_jurusan;
                    $("#MAddEditData h4[labelAddEdit]").text("Detail Nilai Siswa " + nama_siswa +
                        " untuk Jurusan " + data.nama_jurusan);
                    LoadNilai();
                    ShowModal("MAddEditData");
                });
            } else {
                table2.ajax.reload();
            }
        }

        DtJurusan = function() {
            SendAjax({
                url: $apiUrl + "MasterData/Jurusan/List",
            }, function(result) {
                let html = "";
                $.each(result.data, function(index, value) {
                    html += '<option value="' + value.kd_jurusan + '">' + value.nama_jurusan +
                        '</option>';
                });
                if (html != "") {
                    $(html).insertAfter("[name='kd_jurusan'] option:first");
                    $(".selectpicker").selectpicker('refresh');
                }
                $("[name='kd_jurusan']").val("").change();
            });
        }

        LoadNilai = function() {
            if (!$.fn.DataTable.isDataTable("#tableMaple")) {
                let dtu = {
                    id: "#tableMaple",
                    data: {
                        url: $apiUrl + "Report/Normalisasi/NilaiPerNIS",
                        param: function() {
                            var d = {};
                            d.tahun = tahun;
                            d.nisn = nisn;
                            d.kd_jurusan = kd_jurusan;
                            return JSON.stringify(d);
                        }
                    },
                    config: {
                        footerCallback: function(row, data, start, end, display) {
                            let api = this.api();
                            // Remove the formatting to get integer data for summation
                            let intVal = function(i) {
                                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 :
                                    typeof i === 'number' ? i : 0;
                            };

                            let NilaiAkhir = data.reduce(function(a, b) {
                                    return intVal(a) + (intVal(b.bobot) / 100 * intVal(b.nilai));
                                },
                                0);
                            let NilaiMatrix = data.reduce(function(a, b) {
                                    return intVal(a) + (intVal(b.bobot) *
                                        (intVal(b.nilai) / intVal(b.maxnilai)));
                                },
                                0);

                            // Update footer
                            $(api.column(3).footer()).html(Dec2DataTable.display(NilaiAkhir));
                            $(api.column(4).footer()).html(Dec2DataTable.display(NilaiMatrix));
                        },
                        bFilter: false,
                        bPaginate: false,
                        bLengthChange: false,
                        bInfo: false,
                    }
                };

                table1 = PDataTables(dtu, [{
                    "data": null,
                    "className": "text-center",
                    "render": function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    "data": "nama_matapelajaran",
                }, {
                    "data": "bobot",
                    "className": "text-right",
                    render: Dec2DataTable
                }, {
                    "data": "nilai",
                    "className": "text-right",
                    render: Dec2DataTable
                }, {
                    "data": null,
                    "className": "text-right",
                    render: function(data, type, row, meta) {
                        return Dec2DataTable.display(data.nilai / data.maxnilai);
                    }
                }]);
            } else {
                table1.ajax.reload();
            }
        }

        DownloadReport = function() {

        }

        $(document).ready(function() {
            @if ($session['role'] == 'admin')
                Refresh();
            @else
                DtRekap();
            @endif
            DtJurusan();
        });
    </script>
@endpush
