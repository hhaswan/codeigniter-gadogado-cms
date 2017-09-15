<form method="GET" action="{{ base_url(uri_string()) }}" class="form-horizontal">
    <h4 class="text-bold">Filter Data</h4>
    <p>Gunakan form di bawah ini untuk mendapatkan data yang lebih sepesifik.</p>
    <hr style="margin:-5px 0px 10px;"/>
    {{ $filter }}
    <div class="form-group text-right">
        <div class="col-sm-offset-3 col-sm-9">
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Cari Data</button>
            </div>
        </div>
    </div>
</form>