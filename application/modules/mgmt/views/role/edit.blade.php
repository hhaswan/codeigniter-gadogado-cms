@extends('layouts.management.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-title pull-left btn-group">
                    <a href="{{ str_replace('/edit/'.$id, '', base_url(uri_string())) }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-arrow-left"></i> <span class="hidden-xs"> Management Data</span>
                    </a>
                </div>
                <div class="box-title pull-right btn-group">
                    <a href="{{ str_replace('/edit/'.$id, '/create', base_url(uri_string())) }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> <span class="hidden-xs">Tambah Data</span>
                    </a>
                </div>
            </div>
                <div class="box-body">
                    <div class="col-lg-7">
                        @if(flash('MSG_ERROR'))
                            <div class="text-danger text-center">{{flash('MSG_ERROR')}}</div>
                        @endif
                        <form method="post" action="{{ base_url(uri_string()) }}">
                            <input type="hidden" value="_patch" name="method" />
                            <div class="form-group">
                                <label>Nama Role</label>
                                <input type="text" class="form-control" name="name" placeholder="Nama Role" value="{{ $q[0]->name }}" required>
                            </div>
                            <div class="form-group">
                                <label>Alias Role (lowercase)</label>
                                <input type="text" class="form-control" name="alias" placeholder="Alias Role; e.g: writer" value="{{ $q[0]->alias }}">
                            </div>
                            <div class="form-group pull-right">
                                <button type="submit" class="btn btn-primary" name="submit" value="submit">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-5">
                        <h4 class="text-bold">Petunjuk:</h4>
                        <p>Edit beberapa informasi di samping untuk melakukan perubahan terhadap data ini.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
<script>

</script>
@endsection
