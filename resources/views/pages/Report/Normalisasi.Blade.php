@extends('layout.Layout')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="material-datatables">
                        <table id="datatables" class="table table-striped table-no-bordered table-hover" cellspacing="0"
                            width="100%" style="width:100%">
                            <thead>
                                <tr>
                                    <th colspan="6">
                                        <x-button type="button" class="btn-outline-info" icon="fa fa-refresh"
                                            label="Refresh" onclick="Refresh()" />
                                        <x-button type="button" class="btn-outline-success" icon="fa fa-download"
                                            label="Download" onclick="Download()" />
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
                </div>
            </div>
        </div>
    </div>
    {{-- Add Edit Modal --}}
    <x-modal-form id="AddEditData" title="labelAddEdit" class="modal-lg">
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-6">
                    <x-form-group type="select" class="col-sm-12 col-md-12" label="Jurusan" name="kd_jurusan"
                        onchange="LoadNilai()">
                        <option value="" disabled>--Choose Jurusan--</option>
                    </x-form-group>
                </div>
                <div class="material-datatables col-sm-12">
                    <table id="tableMaple" class="table table-striped table-no-bordered table-hover" cellspacing="0"
                        width="100%" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Mata Pelajaran</th>
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
        let table1, tridx = 0,
            tahun = "2024";
        let nama_siswa = "",
            nisn = "",
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
                    ShowData("Edit", data);
                });
            } else {
                table.ajax.reload();
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

        ShowData = function(act = "Add", data = "") {
            let form_id = "#FAddEditData";
            nama_siswa = data.nama_siswa;
            nisn = data.nisn;
            $("#MAddEditData h4[labelAddEdit]").text("Nilai Siswa " + nama_siswa);
            LoadNilai();
            ShowModal("MAddEditData");
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
                            d.kd_jurusan = $("[name='kd_jurusan']").val();
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

                            // console.log(data);
                            let NilaiAkhir = data.reduce(function(a, b) {
                                return intVal(a) + (intVal(b.bobot) / 100 * intVal(b.nilai));
                            }, 0);

                            // Update footer
                            $(api.column(3).footer()).html(Dec2DataTable.display(NilaiAkhir));
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
            Refresh();
            DtJurusan();
        });
    </script>
@endpush
