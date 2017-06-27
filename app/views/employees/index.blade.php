@extends('layouts.main')
<style type="text/css"></style>
@section('content')

<br><br>


<div class="row" >
  
  <div class="col-lg-12">
    <hr>

  </div>
</div>


<div class="row" >
  


  <div class="col-lg-12" >

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

   <div class="panel panel-default">
      <div class="panel-heading">
          <a class="btn btn-info btn-sm" href="{{ URL::to('employees/create')}}">new employee</a>
        </div>
        <div class="panel-body">

      <table id="users" class="table table-condensed table-bordered table-responsive table-hover" style="font-size:12px">


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

        @if($employee->is_approved == 1)

        <tr>

          <td> {{ $i }}</td>
          <td>{{ $employee->personal_file_number }}</td>
          @if($employee->middle_name == null || $employee->middle_name == '')
          <td width="150">{{ $employee->first_name.' '.$employee->last_name}}</td>
          @else
          <td width="150">{{ $employee->first_name.' '.$employee->middle_name.' '.$employee->last_name}}</td>
          @endif
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
          
                  <ul class="dropdown-menu" role="menu">

                    <li><a href="{{URL::to('employees/view/'.$employee->id)}}">View</a></li>

                    @if($employee->is_approved == 0)
                    <li><a href="{{URL::to('employees/approve/'.$employee->id)}}">Approve</a></li>
                    @endif

                    <li><a href="{{URL::to('employees/edit/'.$employee->id)}}">Update</a></li>
                   @if($employee->is_approved == 1)
                    <li><a href="{{URL::to('employees/deactivate/'.$employee->id)}}" onclick="return (confirm('Are you sure you want to deactivate this employee?'))">Deactivate</a></li>
                    @endif
                  </ul>
              </div>

                    </td>
        </tr>

        @else
        <tr style="color: red">

          <td> {{ $i }}</td>
          <td>{{ $employee->personal_file_number }}</td>
          @if($employee->middle_name == null || $employee->middle_name == '')
          <td width="150">{{ $employee->first_name.' '.$employee->last_name.' (Not Approved)'}}</td>
          @else
          <td width="150">{{ $employee->first_name.' '.$employee->middle_name.' '.$employee->last_name.' (Not Approved)'}}</td>
          @endif
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
          
                  <ul class="dropdown-menu" role="menu">

                    <li><a href="{{URL::to('employees/view/'.$employee->id)}}">View</a></li>

                    @if($employee->is_approved == 0)
                    <li><a href="{{URL::to('employees/approve/'.$employee->id)}}">Approve</a></li>
                    @endif

                    <li><a href="{{URL::to('employees/edit/'.$employee->id)}}">Update</a></li>
                   @if($employee->is_approved == 1)
                    <li><a href="{{URL::to('employees/deactivate/'.$employee->id)}}" onclick="return (confirm('Are you sure you want to deactivate this employee?'))">Deactivate</a></li>
                    @endif
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


<div class="row">

  <div class="col-lg-12">
    <hr>
  </div>  

  

  
</div>
@stop
