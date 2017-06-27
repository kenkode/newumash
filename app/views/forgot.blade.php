@include('includes.head')

<div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">

                <div class="login-panel panel panel-default">

                    <div class="panel-body">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <center>
                <img src="{{ asset('public/uploads/logo/'.$organization->logo) }}" alt="UMASH LOGO" width="45%"/>
                </center>
                        <br>

                        <form method="POST" action="{{ URL::to('/users/forgot_password') }}" accept-charset="UTF-8">
    <input type="hidden" name="_token" value="{{{ Session::getToken() }}}">
        <p><strong>Provide a valid email address: </strong></p>
    <div class="form-group">
        <label for="email">{{{ Lang::get('confide::confide.e_mail') }}}</label>
            <input class="form-control" type="text" name="email" id="email" value="{{{ Input::old('email') }}}">
    </div>
<div class="form-group">
     <input class="btn btn-primary" type="submit" value="Send Reset Link">
</div>
    @if (Session::get('error'))
        <div class="alert alert-error alert-danger">{{{ Session::get('error') }}}</div>
    @endif

    @if (Session::get('notice'))
        <div class="alert">{{{ Session::get('notice') }}}</div>
    @endif
</form>



                    </div>
                </div>
            </div>
        </div>
    </div>


