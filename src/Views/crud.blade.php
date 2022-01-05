<style>
    .table thead th {
        font-size: 7px;
    }

</style>
<section id="basic-vertical-layouts">
    <div class="row match-height">
        <div class="col-md-12 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Erendi CRUD Generator</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="alert alert-danger print-error-msg" style="display: none">
                            <ul></ul>
                        </div>
                        <div class="alert alert-success print-success-msg" style="display: none">
                            <ul></ul>
                        </div>
                        <form class="form-data" id="" method="post" action="{{ route('crud.insert') }}">
                            {{-- <form class="form-data" id="dynamic_form" method="post"> --}}
                            @csrf
                            <div class="form-body">
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="modellabel">Nama Model<sup
                                                            class="text-danger">*</sup></label>
                                                    <input type="text" class="form-control" name="modelName"
                                                        placeholder="masuk nama model">
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="modellabel">Custom Nama Table</label>
                                                    <input type="text" class="form-control" name="customTableName"
                                                        placeholder="masuk custom nama table">
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="modellabel">Pagination</label>
                                                    <input type="number" class="form-control" name="paginationRecord"
                                                        value="10">
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                    <div class="col-md-12">
                                        <h6>Fields</h6>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead style="font-size: 9px;" class="bg-dark text-white">
                                                    <tr class="text-white">
                                                        <th width="200" class="p-1 text-white">
                                                            <center>FieldName</center>
                                                        </th>
                                                        <th width="200" class="p-1 text-white">
                                                            <center>DB type</center>
                                                        </th>
                                                        <th width="200" class="p-1 text-white">
                                                            <center>Validation</center>
                                                        </th>
                                                        <th width="200" class="p-1 text-white">
                                                            <center>Html Type</center>
                                                        </th>
                                                        <th class="p-1 text-white">
                                                            <center>Primary</center>
                                                        </th>
                                                        <th class="p-1 text-white">
                                                            <center>is Foreign</center>
                                                        </th>
                                                        <th class="p-1 text-white">
                                                            <center>Searchable</center>
                                                        </th>
                                                        <th class="p-1 text-white">
                                                            <center>Fillable</center>
                                                        </th>
                                                        <th class="p-1 text-white">
                                                            <center>in Form</center>
                                                        </th>
                                                        <th class="p-1 text-white">
                                                            <center>in Index</center>
                                                        </th>
                                                        <th class="p-1 text-white">
                                                            <center></center>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="addnewitem"></tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <h6>Relations</h6>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead style="font-size: 9px;" class="bg-dark text-white">
                                                    <tr class="text-white">
                                                        <th width="200" class="p-1 text-white">
                                                            <center>Relation type</center>
                                                        </th>
                                                        <th width="200" class="p-1 text-white">
                                                            <center>Foreign Model<sup>*</sup></center>
                                                        </th>
                                                        <th width="200" class="p-1 text-white">
                                                            <center>Foreign Key</center>
                                                        </th>
                                                        <th class="p-1 text-white" width="200">
                                                            <center>Local Key</center>
                                                        </th>
                                                        <th class="p-1 text-white" width="200">
                                                            <center>Field Show <i class="bx bx-info-circle"
                                                                    data-toggle="tooltip" data-placement="top"
                                                                    title="Pada select box file mana yang akan di tampilkan dari foreign model, contoh jika anda memiliki table post denga field : title, text, created_at, dll maka pilih salah satu dari field tersebut"></i>
                                                            </center>

                                                        </th>
                                                        <th class="p-1 text-white" width="70">
                                                            <center>
                                                                <button type="button" class="btn btn-success btn-sm"
                                                                    id="addrelation"><strong>+</strong></button>
                                                            </center>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="addrelationtable"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end border-top">
                                        <button type="submit" class="btn btn-primary btn-sm mr-1 mb-1 mt-1"
                                            id="save">Proses</button>
                                        <a class="btn btn-light-secondary btn-sm mr-1 mb-1 mt-1" href="#"> Batal</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
