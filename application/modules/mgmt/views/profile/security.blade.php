<p class="text-justify">
    <h4 class="text-bold">Keamanan Akun</h4>
    Tabel di bawah ini adalah riwayat penggunaan / login akun Anda. Apabila Anda tidak mengenali data login
    di bawah ini, Anda dapat menghapus sesi login tersebut menggunakan tombol yang tersedia.
</p>
<br/>
<div class="table-responsive mailbox-messages">
    <table class="table table-bordered table-hover table-striped">
        <thead>
            <th class="text-center" style="vertical-align:middle;"><i class="fa fa-tv"></i> Platform</th>
            <th class="text-center" style="vertical-align:middle;"><i class="fa fa-chrome"></i> Browser</th>
            <th class="text-center" style="vertical-align:middle;"><i class="fa fa-link"></i> IP Address</th>
            <th class="text-center" style="vertical-align:middle;"><i class="fa fa-calendar"></i> Login</th>
            <th class="text-center" style="vertical-align:middle;">Aksi</th>
        </thead>
        <tbody>
            @if(isset($sessions))
                @foreach($sessions as $row)
                    @php $this->agent->parse($row->user_agent) @endphp
                    <tr>
                        <td>
                            <h4 style="padding:0px 10px;">
                                <b>{{ $this->agent->platform() }}</b>
                                <p class="small">
                                    @if($this->agent->is_mobile())
                                        Mobile / Tablet Platform
                                    @else
                                        Desktop Platform
                                    @endif
                                </p>
                            </h4>
                        </td>
                        <td class="text-center" style="vertical-align:middle;">{{ $this->agent->browser() }}</td>
                        <td class="text-center" style="vertical-align:middle;">{{ $row->ip_address }}</td>
                        <td class="text-center" style="vertical-align:middle;">{{ \Carbon\Carbon::parse($row->created_at)->diffForHumans() }}</td>
                        <td class="text-center" style="vertical-align:middle;">
                            @if(session((new Admin_Controller)->admin_identifier)['token'] != $row->token)
                                <button type="button" data-id="{{ $row->token }}" class="btn btn-danger terminate-session btn-sm"><i class="fa fa-times"></i></button>
                            @else
                                <a href="#" data-toggle="tooltip" title="Ini merupakan sesi login Anda saat ini. Logout untuk menghapus">Sesi Anda</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@section('custom_js')
<script>
    $(document).on("click", ".terminate-session", function(){
        var id = $(this).data('id');
        $.ajax({
            url     : "{{base_url('/mgmt/profile/ajax_terminate_session')}}",
            data    : { token : id },
            type    : "delete",
            dataType: "json",
            success: function(data){
                if(data.status){
                    $('#security').html(data.html);
                }
            }
        });
    });
</script>
@endsection