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
                                    <th colspan="5">
                                        <x-button type="button" class="btn-outline-success" icon="fa fa-plus"
                                            label="Add" onclick="Add()" />
                                        <x-button type="button" class="btn-outline-info" icon="fa fa-refresh"
                                            label="Refresh" onclick="Refresh()" />
                                    </th>
                                </tr>
                                <tr>
                                    <th>No</th>
                                    <th>Jurusan</th>
                                    <th>Bobot</th>
                                    <th class="disabled-sorting text-center">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript">
        let table, id_tbl = "#datatables";
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
                    "data": "kd_matapelajaran",
                }, {
                    "data": "nama_matapelajaran",
                }, {
                    "data": null,
                    "orderable": false,
                    "className": "text-center",
                    render: function(data, type, row, meta) {
                        let html = "";
                        html += btnDataTable("Edit Bobot", "btn-outline-primary edit",
                            "fa fa-edit btn-outline-primary", true);
                        html += btnDataTable("Delete Bobot", "btn-outline-danger delete",
                            "fa fa-trash btn-outline-danger");
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
                        kd_matapelajaran: data.kd_matapelajaran
                    };
                    $("#FDelData p").html("Are you sure to delete data code Mata Pelajaran <b>" + data
                        .kd_matapelajaran +
                        "</b> ?");
                    ShowModal("MDelData");
                });
            } else {
                table.ajax.reload();
            }
        }

        Add = function() {
            ShowData();
        }

        ShowData = function(act = "Add", data = "") {
            let form_id = "#FAddEditData";
            $("h4[labelAddEdit]").text(act + " Data Mata Pelajaran");
            processData = {
                action: act,
                kd_matapelajaran: (act == "Add" ? "" : data.kd_matapelajaran),
                nama_matapelajaran: (act == "Add" ? "" : data.nama_matapelajaran)
            };
            $(form_id + " [name='kd_matapelajaran']").val(processData.kd_matapelajaran).change();
            $(form_id + " [name='nama_matapelajaran']").val(processData.nama_matapelajaran).change();

            $(form_id).parsley().reset();
            ShowModal("MAddEditData");
        }

        function Save() {
            let form_id = "#FAddEditData";
            if ($(form_id).parsley().validate()) {
                Loader("show");
                processData.kd_matapelajaran = $(form_id + " [name='kd_matapelajaran']").val();
                processData.nama_matapelajaran = $(form_id + " [name='nama_matapelajaran']").val();
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
