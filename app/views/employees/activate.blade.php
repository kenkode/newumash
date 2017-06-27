@extends('layouts.organization')
@section('content')

<br><br>



<div class="row">
  
  <div class="col-lg-12">
    <hr>

  </div>
</div>


<div class="row">
  


  <div class="col-lg-12">

@if (Session::has('flash_message'))

      <div class="alert alert-success">
      {{ Session::get('flash_message') }}
     </div>
    @endif

     @if (Session::has('delete_message'))

      <div class="alert alert-danger">
      {{ Session::get('delete_message') }}
     </div>
    @endif

  <div class="panel panel-danger">
      <div class="panel-heading">
          <h4>Deactivated Employees</h4>
        </div>
        <div class="panel-body">

      <table id="users" class="table table-condensed table-bordered table-responsive table-hover" style="font-size:11px">


      <thead>

        <th>#</th>
        <th>PFN</th>
        <th width="150">Employee Name</th>
        <th>ID</th>
        <th>KRA PIN</th>
        <th>NSSF NO.</th>
        <th>NHIF NO.</th>
        <th>Gender</th>
        <th>Branch</th>
        <th>Department</th>

        <th>Action</th>

      </thead>

      <tfoot>

        <th>#</th>
        <th>PFN</th>
        <th width="150">Employee Name</th>
        <th>ID</th>
        <th>Kra Pin</th>
        <th>Nssf NO.</th>
        <th>Nhif NO.</th>
        <th>Gender</th>
        <th>Branch</th>
        <th>Department</th>

      </tfoot>
      <tbody>

        <?php $i = 1; ?>
        @foreach($employees as $employee)

        <tr>

          <td> {{ $i }}</td>
          <td>{{ $employee->personal_file_number }}</td>
          <td width="150">{{ $employee->first_name.' '.$employee->last_name}}</td>
          <td>{{ $employee->identity_number }}</td>
          <td>{{ $employee->pin }}</td>
          <td>{{ $employee->social_security_number }}</td>
          <td>{{ $employee->hospital_insurance_number }}</td>
          <td>{{ $employee->gender }}</td>
          <?php if( $employee->branch_id!=0){ ?>
          <td>{{ Branch::getName($employee->branch_id) }}</td>
          <?php }else{?>
          <td></td>
          <?php } ?>
           <?php if( $employee->department_id!= 0){ ?>
          <td>{{ Department::getName($employee->department_id) }}</td>
          <?php }else{?>
          <td></td>
          <?php } ?>
                   <td>

                  <div class="btn-group">
                  <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    Action <span class="caret"></span>
                  </button>
          
                  <ul class="dropdown-menu" style="margin-left:0px" role="menu">

                    <li><a href="{{URL::to('employees/viewdeactive/'.$employee->id)}}">View</a></li>

                    <li><a href="{{URL::to('employees/edit/'.$employee->id)}}">Update</a></li>
                   
                    <li><a href="{{URL::to('employees/activate/'.$employee->id)}}" onclick="return (confirm('Are you sure you want to activate this employee?'))">Activate</a></li>
                    
                  </ul>
              </div>

                    </td>
        </tr>

        <?php $i++; ?>
        @endforeach


      </tbody>


    </table>
</div>
</div>

  </div>  


<div class="row">

  <div class="col-lg-12">
    <hr>
  </div>  

  

  
</div>
@stop
