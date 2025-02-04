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
                                    </th>
                                </tr>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">NIS/NISN</th>
                                    <th class="text-center">Nama Siswa</th>
                                    <th class="text-center">Nilai Rata2</th>
                                    <th class="text-center">Status</th>
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
                <div class="material-datatables col-sm-12">
                    <table id="tableMaple" class="table table-striped table-no-bordered table-hover" cellspacing="0"
                        width="100%" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kriteria</th>
                                <th>Nilai</th>
                                <th class="disabled-sorting text-center">Actions</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-left">Rata2 Nilai</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <x-button type="button" class="btn-outline-secondary mr-1" label="Close" data-dismiss="modal" />
            <x-button type="submit" class="btn-outline-primary" onclick="Save()">Save</x-button>
        </div>
    </x-modal-form>
    {{-- Add Edit Detail Modal --}}
    <x-modal-form id="AddEditDataDetail" title="labelAddEdit">
        <div class="modal-body">
            <div class="row">
                <x-form-group class="col-sm-12 col-md-12" label="Nilai" name="nilai" required />
            </div>
        </div>
        <div class="modal-footer">
            <x-button type="button" class="btn-outline-secondary mr-1" label="Close" data-dismiss="modal" />
            <x-button type="submit" class="btn-outline-primary" onclick="SaveDetail()">Save</x-button>
        </div>
    </x-modal-form>

    {{-- Delete Modal --}}
    <x-modal-form id="DelData">
        <div class="modal-body">
            <p></p>
        </div>
        <div class="modal-footer">
            <x-button type="button" class="btn-outline-secondary mr-1" label="Close" data-dismiss="modal" />
            <x-button type="submit" class="btn-outline-danger" onclick="Delete()">
                Delete
            </x-button>
        </div>
    </x-modal-form>
@endsection
@push('scripts')
    <script type="text/javascript">
        let table, id_tbl = "#datatables";
        let table1, tridx = 0,
            tahun = "2024";
        let nama_siswa = "",
            dtDetail = {};
        let processData = {};

        Refresh = function() {
            if (!$.fn.DataTable.isDataTable(id_tbl)) {
                let dtu = {
                    id: id_tbl,
                    data: {
                        url: $apiUrl + "Process/Nilai/List",
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
                        return meta.row /*+ meta.settings._iDisplayStart*/ + 1;
                    }
                }, {
                    "data": "nisn",
                }, {
                    "data": "nama_siswa",
                }, {
                    "data": "rata2",
                    "className": "text-right",
                    render: Dec2DataTable
                }, {
                    "data": "setup",
                    render: function(data, type, row, meta) {
                        var html = ""
                        if (data == 0) {
                            html = "Not Set";
                        } else {
                            html = "Done";
                        }
                        return html
                    }
                }, {
                    "data": null,
                    "orderable": false,
                    "className": "text-center",
                    render: function(data, type, row, meta) {
                        let html = "";
                        html += btnDataTable("Setup Bobot", "btn-outline-primary edit",
                            "fa fa-edit btn-outline-primary", true);
                        if (data.setup > 0) {
                            html += btnDataTable("Remove Bobot", "btn-outline-danger delete",
                                "fa fa-trash btn-outline-danger");
                        }
                        return html;
                    }
                }]);
                table.on('click', '.edit', function() {
                    $tr = $(this).closest('tr');
                    var data = table.row($tr).data();
                    ShowData("Edit", data);
                });
                table.on('click', '.delete', function() {
                    $tr = $(this).closest('tr');
                    var data = table.row($tr).data();
                    processData = {
                        tahun: data.tahun,
                        nisn: data.nisn,
                    };
                    $("#FDelData p").html("Are you sure to delete data nilai siswa <b>" +
                        data.nama_siswa + "</b> ?");
                    ShowModal("MDelData");
                });
            } else {
                table.ajax.reload();
            }
        }

        ShowData = function(act = "Add", data = "") {
            let form_id = "#FAddEditData";
            nama_siswa = data.nama_siswa;
            $("#MAddEditData h4[labelAddEdit]").text("Input Nilai Siswa (" + nama_siswa + ")");
            // Get Data Bobot
            SendAjax({
                url: $apiUrl + "Process/Nilai/DataNilai",
                param: {
                    tahun: tahun,
                    nisn: data.nisn,
                }
            }, function(result) {
                processData = {
                    tahun: tahun,
                    nisn: data.nisn,
                    dtmapel: result.data
                };
                LoadNilai(processData.dtmapel);
                $(form_id).parsley().reset();
                ShowModal("MAddEditData");
            }, function() {
                Loader();
            });
        }

        LoadNilai = function(data) {
            if (!$.fn.DataTable.isDataTable("#tableMaple")) {
                let dtu = {
                    id: "#tableMaple",
                    type: "manual",
                    data: data,
                    config: {
                        footerCallback: function(row, data, start, end, display) {
                            let api = this.api();
                            // Remove the formatting to get integer data for summation
                            let intVal = function(i) {
                                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 :
                                    typeof i === 'number' ? i : 0;
                            };

                            // Total over all pages
                            let Count = 0;
                            let RataNilai = api.column(2).data().reduce(function(a, b) {
                                Count++;
                                return intVal(a) + intVal(b);
                            }, 0);
                            RataNilai = RataNilai / Count;
                            // Update footer
                            $(api.column(2).footer()).html(Dec2DataTable.display(RataNilai));
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
                    "data": "nilai",
                    "className": "text-right",
                    render: Dec2DataTable
                }, {
                    "data": null,
                    "orderable": false,
                    "className": "text-center",
                    render: function(data, type, row, meta) {
                        let html = "";
                        html += btnDataTable("Edit Nilai", "btn-outline-primary edit",
                            "fa fa-edit btn-outline-primary");
                        return html;
                    }
                }]);
                table1.on('click', '.edit', function() {
                    $tr = $(this).closest('tr');
                    tridx = table1.row($tr).index();
                    var data = table1.row($tr).data();
                    ShowDataDetail("Edit", data);
                });
            } else {
                $('#tableMaple').DataTable().clear();
                $('#tableMaple').DataTable().rows.add(data);
                $('#tableMaple').DataTable().draw();
            }
        }

        ShowDataDetail = function(act = "Add", data = "") {
            let form_id = "#FAddEditDataDetail";
            let lbldetail = act + " Nilai Mata Pelajaran (" + data.nama_matapelajaran + ")";
            $("#MAddEditDataDetail h4[labelAddEdit]").text(lbldetail);
            dtDetail = {
                action: act,
                kd_matapelajaran: (act == "Add" ? "" : data.kd_matapelajaran),
                nama_matapelajaran: (act == "Add" ? "" : data.nama_matapelajaran),
                nilai: (act == "Add" ? "" : data.nilai),
            };
            $(form_id + " [name='nilai']").val(dtDetail.nilai).change();
            $(form_id).parsley().reset();
            ShowModal("MAddEditDataDetail", undefined, true);
        }

        SaveDetail = function() {
            let form_id = "#FAddEditDataDetail";
            if ($(form_id).parsley().validate()) {
                if (dtDetail.action == 'Add') {} else {
                    processData.dtmapel[tridx].nilai = $(form_id + " [name='nilai']").val();
                    LoadNilai(processData.dtmapel);
                    ShowModal("MAddEditDataDetail", "hide");
                }
            }
        }

        Save = function() {
            let form_id = "#FAddEditData";
            Loader("show");
            let data = {
                url: $apiUrl + "Process/Nilai/Save",
                param: processData
            };
            SendAjax(data, function(result) {
                MessageNotif(result.message, "success");
                Refresh();
                ShowModal("MAddEditData", "hide");
            }, function() {
                Loader();
            });
        }

        Delete = function() {
            Loader("show");
            let data = {
                url: $apiUrl + "Process/Nilai/Delete",
                param: processData
            };
            SendAjax(data, function(result) {
                MessageNotif(result.message, "success");
                Refresh();
                ShowModal("MDelData", "hide");
            }, function() {
                Loader();
            });
        }

        $(document).ready(function() {
            Refresh();
        });
    </script>
@endpush
