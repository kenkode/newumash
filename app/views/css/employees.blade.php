@extends('layouts.css')
@section('content')
<br/>

<div class="row">
	<div class="col-lg-12">
  <h3>Employees Portal</h3>

<hr>
</div>	
</div>


<div class="row">
	<div class="col-lg-12" style="margin-top: 2%;">

  @if (Session::get('alert'))
            <div class="alert alert-warning alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <strong>{{Session::get('alert')}}</strong> 
          </div>    
        @endif
    @if (Session::get('notice'))
            <div class="alert alert-info alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <strong>{{Session::get('notice')}}</strong> 
          </div>    
        @endif

    <div class="panel panel-default">
      <div class="panel-heading">
          <p>Active Employees</p>
        </div>
        <div class="panel-body">


  
      <table id="users" class="table table-condensed table-bordered table-responsive table-hover">


      <thead>

        <th>#</th>
        <th>Employee Number</th>
        <th>Employee Name</th>
      

        <th></th>
        

      </thead>

      <tfoot>

        <th>#</th>
        <th>Employee Number</th>
        <th>Employee Name</th>
        
      </tfoot>

      <tbody>

        <?php $i = 1; ?>
        @foreach($employees as $employee)
        @if($employee->in_employment == 'Y')
        <tr>

          <td> {{ $i }}</td>
          <td>{{ $employee->personal_file_number }}</td>
          <td>{{ $employee->first_name.' '.$employee->middle_name.' '.$employee->last_name }}</td>
     
           <td>

                  <div class="btn-group">
                  <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    Action <span class="caret"></span>
                  </button>
          
                  <ul class="dropdown-menu" role="menu">
                      @if($employee->is_css_active == false)
                    <li><a href="{{URL::to('portal/activate/'.$employee->id)}}">Activate</a></li>
                  
                        @endif
                   
                           @if($employee->is_css_active == true)
                    <li><a href="{{URL::to('portal/deactivate/'.$employee->id)}}">Deactivate</a></li>
 @endif
                    

                    <li><a href="{{URL::to('css/reset/'.$employee->id)}}">Reset Password</a></li>
                    
                  </ul>
              </div>

                    </td>





        </tr>

        @else
        <tr style="color: red">

          <td> {{ $i }}</td>
          <td>{{ $employee->personal_file_number }}</td>
          <td>{{ $employee->first_name.' '.$employee->middle_name.' '.$employee->last_name.' (Deactive Employee)' }}</td>
     
           <td>

                  <div class="btn-group">
                  <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    Action <span class="caret"></span>
                  </button>
          
                  <ul class="dropdown-menu" role="menu">
                      @if($employee->is_css_active == false)
                    <li><a href="{{URL::to('portal/activate/'.$employee->id)}}">Activate</a></li>
                  
                        @endif
                   
                           @if($employee->is_css_active == true)
                    <li><a href="{{URL::to('portal/deactivate/'.$employee->id)}}">Deactivate</a></li>
 @endif
                    

                    <li><a href="{{URL::to('css/reset/'.$employee->id)}}">Reset Password</a></li>
                    
                  </ul>
              </div>

                    </td>





        </tr>
        @endif

        <?php $i++; ?>
        @endforeach


      </tbody>


    </table>
  </div>


  </div>

</div>
























@stop