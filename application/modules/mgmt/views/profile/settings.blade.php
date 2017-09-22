<p class="text-justify">
    <h4 class="text-bold">Pengaturan Akun</h4>
    Apabila terdapat perubahan data pada akun Anda, silakan masukkan melalui form di bawah ini.
</p>
<br/>
<form method="POST" action="{{ base_url(uri_string()) }}" class="form-horizontal">
    @if(flash('MSG_ERROR'))
    <div class="form-group">
        <div class="alert alert-danger col-sm-offset-1 col-lg-9">
            <h4><i class="icon fa fa-ban"></i> Error!</h4>
            {{flash('MSG_ERROR')}}
        </div>
    </div>                        
    @endif
    <div class="form-group">
        <label for="name" class="col-sm-2 col-sm-offset-1 control-label">Nama Lengkap</label>
        <div class="col-sm-7">
            <input type="text" class="form-control" name="nama" id="name" placeholder="Nama Lengkap" value="{{ $name }}" required>
        </div>
    </div>
    <div class="form-group">
        <label for="email" class="col-sm-2 col-sm-offset-1 control-label">Email</label>
        <div class="col-sm-7">
            <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="{{ $email }}" required>
        </div>
    </div>
    <div class="form-group">
        <label for="bio" class="col-sm-2 col-sm-offset-1 control-label">Deskripsi (Bio)</label>
        <div class="col-sm-7">
            <textarea name="bio" class="form-control" rows="3" placeholder="Deskripsi (Bio) Anda...">{{ strip_tags($bio); }}</textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 col-sm-offset-1 control-label">Password</label>
        <div class="col-sm-7">
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-default"><i class="fa fa-refresh"></i> Reset Password</button>                                
        </div>
    </div>
    
    <div class="form-group text-right">
        <div class="col-sm-offset-1 col-sm-9">
            <div class="btn-group">
                <button type="submit" name="submit" value="submit" class="btn btn-primary"><i class="fa fa-check"></i> Simpan</button>
            </div>
        </div>
    </div>
</form>
<div class="modal fade" id="modal-default">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{base_url(uri_string())}}">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"> &times; </span>
                    </button>
                    <h4 class="modal-title"><i class="fa fa-asterisk"></i> Reset Password</h4>
                </div>
                <div class="modal-body">
                    <p>Untuk mengatur ulang password Anda, silakan masukkan password Anda saat ini, dan password baru Anda</p>
                    <div class="form-group has-feedback">
                        <label for="password_current">Password Saat Ini</label>            
                        <input type="password" class="form-control" id="password_current" name="password_current" placeholder="Password Saat Ini" required>
                        <span class="fa fa-lock form-control-feedback"></span>
                    </div>
                    <hr/>
                    <div class="form-group has-feedback">
                        <label for="password">Password Baru</label>            
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <span class="fa fa-lock form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi Password" required>
                        <span class="fa fa-lock form-control-feedback"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Tutup</button>
                        <button type="submit" name="submit_password" value="submit_password" class="btn btn-primary"><i class="fa fa-check"></i> Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>