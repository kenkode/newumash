<p>
Hello Admin, 
</p>

<p>The following employee has been created and needs your approval: </p>
<br>

<table>

<thead style="background-color:gray; color:white;">
	<th colspan="2">Employee Details</th>
</thead>
<tbody>
	<tr>
		<td>Payroll Number:</td><td>{{$employee->personal_file_number}}</td>
	</tr>
	<tr>
		<td>Surname:</td><td>{{$employee->last_name}}</td>
	</tr>
    
    <tr>
		<td>First name:</td><td>{{$employee->first_name}}</td>
	</tr>

	<tr>
		<td>Middle name:</td><td>{{$employee->middle_name}}</td>
	</tr>
	

</tbody>
	
</table>




<br><br>
<p>Regards,</p>
<?php $orgname=Organization::find(1)->pluck('name'); ?>
<p>{{$orgname}}</p>