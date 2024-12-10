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
                                    <th>NIS/NISN</th>
                                    <th>Nama Siswa</th>
                                    <th>Jenis Kelamin</th>
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
    <x-modal-form id="AddEditData" title="labelAddEdit">
        <div class="modal-body">
            <div class="row">
                <x-form-group class="col-sm-12 col-md-12" label="NIS/NISN" name="nisn" required />
                <x-form-group class="col-sm-12 col-md-12" label="Nama Siswa" name="nama_siswa" required />
                <x-form-group type="select" class="col-sm-12 col-md-12" label="Jenis Kelamin" name="jenis_kelamin"
                    required>
                    <option value="" disabled>--Choose Kelamin--</option>
                    <option value="0">Pria</option>
                    <option value="1">Wanita</option>
                </x-form-group>
            </div>
        </div>
        <div class="modal-footer">
            <x-button type="button" class="btn-outline-secondary mr-1" label="Close" data-dismiss="modal" />
            <x-button type="submit" class="btn-outline-primary" onclick="Save()">Save</x-button>
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
        let processData = {};

        Refresh = function() {
            if (!$.fn.DataTable.isDataTable(id_tbl)) {
                let dtu = {
                    id: id_tbl,
                    data: {
                        url: $apiUrl + "MasterData/Siswa/List"
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
                    "data": "jenis_kelamin",
                    render: function(data, type, row, meta) {
                        var html = ""
                        if (data == 0) {
                            html = "Pria";
                        } else {
                            html = "Wanita";
                        }
                        return html
                    }
                }, {
                    "data": null,
                    "orderable": false,
                    "className": "text-center",
                    render: function(data, type, row, meta) {
                        let html = "";
                        html += btnDataTable("Edit Siswa", "btn-outline-primary edit",
                            "fa fa-edit btn-outline-primary", true);
                        html += btnDataTable("Delete Siswa", "btn-outline-danger delete",
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
                        nisn: data.nisn
                    };
                    $("#FDelData p").html("Are you sure to delete data NISN <b>" + data.nisn +
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
            $("h4[labelAddEdit]").text(act + " Data Siswa");
            processData = {
                action: act,
                nisn: (act == "Add" ? "" : data.nisn),
                nama_siswa: (act == "Add" ? "" : data.nama_siswa),
                jenis_kelamin: (act == "Add" ? "" : data.jenis_kelamin)
            };
            act == "Add" ? $(form_id + " [name='nisn']").removeAttr('disabled') : $(form_id + " [name='nisn']").attr('disabled', true);
            $(form_id + " [name='nisn']").val(processData.nisn).change();
            $(form_id + " [name='nama_siswa']").val(processData.nama_siswa).change();
            $(form_id + " [name='jenis_kelamin']").val(processData.jenis_kelamin).change();

            $(form_id).parsley().reset();
            ShowModal("MAddEditData");
        }

        function Save() {
            let form_id = "#FAddEditData";
            if ($(form_id).parsley().validate()) {
                Loader("show");
                processData.nisn = $(form_id + " [name='nisn']").val();
                processData.nama_siswa = $(form_id + " [name='nama_siswa']").val();
                processData.jenis_kelamin = $(form_id + " [name='jenis_kelamin']").val();
                let data = {
                    url: $apiUrl + "MasterData/Siswa/Save",
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
                url: $apiUrl + "MasterData/Siswa/Delete",
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
