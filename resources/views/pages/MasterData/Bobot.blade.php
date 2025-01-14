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
                                    <th>No</th>
                                    <th>Jurusan</th>
                                    <th>Count Kriteria</th>
                                    <th>Status</th>
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
                                <th colspan="6">
                                    <x-button type="button" class="btn-outline-success" icon="fa fa-plus" label="Add"
                                        onclick="AddDetail()" />
                                </th>
                            </tr>
                            <tr>
                                <th>No</th>
                                <th>Kriteria</th>
                                <th>Bobot</th>
                                <th class="disabled-sorting text-center">Actions</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-left">Total Bobot</th>
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
                <x-form-group type="select" class="col-sm-12 col-md-12" label="Kriteria" name="kd_matapelajaran"
                    required>
                    <option value="" disabled>--Choose Kriteria--</option>
                </x-form-group>
                <x-form-group class="col-sm-12 col-md-12" label="Bobot" name="bobot" required />
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
        let table1, TotPerse = 0,
            tridx = 0;
        let nama_jurusan = "",
            dtDetail = {},
            dtKecuali = [];
        let processData = {};

        Refresh = function() {
            if (!$.fn.DataTable.isDataTable(id_tbl)) {
                let dtu = {
                    id: id_tbl,
                    data: {
                        url: $apiUrl + "MasterData/Bobot/List"
                    }
                };
                table = PDataTables(dtu, [{
                    "data": null,
                    "className": "text-center",
                    "render": function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    "data": "nama_jurusan",
                }, {
                    "data": "cmapel",
                    "className": "text-center",
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
                        kd_jurusan: data.kd_jurusan
                    };
                    $("#FDelData p").html("Are you sure to delete Setup bobot <b>" + data
                        .nama_jurusan + "</b> ?");
                    ShowModal("MDelData");
                });
            } else {
                table.ajax.reload();
            }
        }

        ShowData = function(act = "Add", data = "") {
            let form_id = "#FAddEditData";
            nama_jurusan = data.nama_jurusan;
            $("#MAddEditData h4[labelAddEdit]").text("Setup Data Bobot (" + nama_jurusan + ")");
            dtKecuali = [];
            // Get Data Bobot
            SendAjax({
                url: $apiUrl + "MasterData/Bobot/DataBobot",
                param: {
                    kd_jurusan: data.kd_jurusan
                }
            }, function(result) {
                processData = {
                    kd_jurusan: data.kd_jurusan,
                    dtmapel: result.data
                };
                $.each(processData.dtmapel, function(index, value) {
                    dtKecuali.push(value.kd_matapelajaran);
                });
                LoadBobot(processData.dtmapel);
                $(form_id).parsley().reset();
                ShowModal("MAddEditData");
            }, function() {
                Loader();
            });
        }

        LoadBobot = function(data) {
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
                            TotPerse = api.column(2).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                            // Update footer
                            $(api.column(2).footer()).html(Dec2DataTable.display(TotPerse));
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
                    "className": "text-center",
                    render: Dec2DataTable
                }, {
                    "data": null,
                    "orderable": false,
                    "className": "text-center",
                    render: function(data, type, row, meta) {
                        let html = "";
                        html += btnDataTable("Setup Bobot", "btn-outline-primary edit",
                            "fa fa-edit btn-outline-primary", true);
                        html += btnDataTable("Remove Bobot", "btn-outline-danger delete",
                            "fa fa-trash btn-outline-danger");
                        return html;
                    }
                }]);
                table1.on('click', '.edit', function() {
                    $tr = $(this).closest('tr');
                    tridx = table1.row($tr).index();
                    var data = table1.row($tr).data();
                    ShowDataDetail("Edit", data);
                });
                table1.on('click', '.delete', function() {
                    $tr = $(this).closest('tr');
                    tridx = table1.row($tr).index();
                    var data = table1.row($tr).data();
                    processData.dtmapel.splice(tridx, 1);
                    dtKecuali.splice(dtKecuali.indexOf(data.kd_matapelajaran), 1);
                    LoadBobot(processData.dtmapel);
                });
            } else {
                $('#tableMaple').DataTable().clear();
                $('#tableMaple').DataTable().rows.add(data);
                $('#tableMaple').DataTable().draw();
            }
        }

        AddDetail = function() {
            ShowDataDetail();
        }

        ShowDataDetail = function(act = "Add", data = "") {
            let form_id = "#FAddEditDataDetail";
            let lbldetail = act + " Data Kriteria (" + nama_jurusan + ")";
            $("#MAddEditDataDetail h4[labelAddEdit]").text(lbldetail);
            dtDetail = {
                action: act,
                kd_matapelajaran: (act == "Add" ? "" : data.kd_matapelajaran),
                nama_matapelajaran: (act == "Add" ? "" : data.nama_matapelajaran),
                bobot: (act == "Add" ? "" : data.bobot),
            };
            $(form_id + " [name='kd_matapelajaran']").removeAttr('disabled').find('option:not(:first)').remove().end();
            if (act == "Add") {
                SendAjax({
                    url: $apiUrl + "MasterData/Bobot/MapleReady",
                    param: {
                        kd_jurusan: processData.kd_jurusan,
                        dtmapel: dtKecuali
                    }
                }, function(result) {
                    let html = "";
                    $.each(result.data, function(index, value) {
                        html += '<option value="' + value.kd_matapelajaran + '">' + value
                            .nama_matapelajaran + '</option>';
                    });
                    if (html != "") {
                        $(html).insertAfter(form_id + " [name = 'kd_matapelajaran'] option:first");
                        $(form_id + " .selectpicker").selectpicker('refresh');
                    }
                    $(form_id + " [name='kd_matapelajaran']").val(dtDetail.kd_matapelajaran).change();
                    $(form_id + " [name='bobot']").val(dtDetail.bobot).change();
                    $(form_id).parsley().reset();
                    ShowModal("MAddEditDataDetail", undefined, true);
                }, function() {
                    Loader();
                });
            } else {
                let html = '<option value="' + dtDetail.kd_matapelajaran + '">' + dtDetail.nama_matapelajaran +
                    '</option>';
                $(html).insertAfter(form_id + " [name = 'kd_matapelajaran'] option:first");
                $(form_id + " .selectpicker").selectpicker('refresh');
                $(form_id + " [name='kd_matapelajaran']").attr('disabled', true).val(dtDetail.kd_matapelajaran)
                    .change();
                $(form_id + " [name='bobot']").val(dtDetail.bobot).change();
                $(form_id).parsley().reset();
                ShowModal("MAddEditDataDetail", undefined, true);
            }
        }

        SaveDetail = function() {
            let form_id = "#FAddEditDataDetail";
            if ($(form_id).parsley().validate()) {
                if (dtDetail.action == 'Add') {
                    processData.dtmapel.push({
                        kd_matapelajaran: $(form_id + " [name='kd_matapelajaran']").val(),
                        nama_matapelajaran: $(form_id + " [name='kd_matapelajaran'] [value='" + $(form_id +
                            " [name='kd_matapelajaran']").val() + "']").text(),
                        bobot: $(form_id + " [name='bobot']").val(),
                    });
                    dtKecuali.push($(form_id + " [name='kd_matapelajaran']").val());
                    LoadBobot(processData.dtmapel);
                    ShowModal("MAddEditDataDetail", "hide");
                } else {
                    processData.dtmapel[tridx].kd_matapelajaran = $(form_id + " [name='kd_matapelajaran']").val();
                    processData.dtmapel[tridx].nama_matapelajaran = $(form_id + " [name='kd_matapelajaran'] [value='" +
                        $(form_id + " [name='kd_matapelajaran']").val() + "']").text();
                    processData.dtmapel[tridx].bobot = $(form_id + " [name='bobot']").val();
                    LoadBobot(processData.dtmapel);
                    ShowModal("MAddEditDataDetail", "hide");
                }
            }
        }

        Save = function() {
            let form_id = "#FAddEditData";
            if (TotPerse == 100) {
                Loader("show");
                let data = {
                    url: $apiUrl + "MasterData/Bobot/Save",
                    param: processData
                };
                SendAjax(data, function(result) {
                    MessageNotif(result.message, "success");
                    Refresh();
                    ShowModal("MAddEditData", "hide");
                }, function() {
                    Loader();
                });
            } else {
                MessageNotif("Total Persen Harus 100%!", "warning");
            }
        }

        Delete = function() {
            Loader("show");
            let data = {
                url: $apiUrl + "MasterData/Bobot/Delete",
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
