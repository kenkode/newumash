@include('includes.head')

<div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                
                
                <div class="login-panel panel panel-default">
                      
                    <div class="panel-body">
                    @if ($message != "")
                    <div class="alert alert-danger">
                    {{ $message }}
                    </div>
                    @endif
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <img src="{{asset('public/uploads/logo/'.$organization->logo)}}" alt="logo" width="60%">

                        <br>
               
                        {{ Confide::makeLoginForm()->render() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
