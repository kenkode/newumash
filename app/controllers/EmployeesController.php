<?php

class EmployeesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		
		$employees = Employee::getActiveEmployee();

		 Audit::logaudit('Employees', 'view', 'viewed employee list');

     if(Entrust::can('approve_employee')){
        $employees = Employee::getAllEmployee();
     }else{
        $employees = Employee::getActiveEmployee();
     }

		return View::make('employees.index', compact('employees'));
	}

    public function createcitizenship()
	{
      $postcitizen = Input::all();
      $data = array('name' => $postcitizen['name'], 
      	            'organization_id' => 1,
      	            'created_at' => DB::raw('NOW()'),
      	            'updated_at' => DB::raw('NOW()'));
      $check = DB::table('citizenships')->insertGetId( $data );

		if($check > 0){
         
		Audit::logaudit('Citizenships', 'create', 'created: '.$postcitizen['name']);
        return $check;
        }else{
         return 1;
        }
      
	} 

	public function createeducation()
	{
      $posteducation = Input::all();
      $data = array('education_name' => $posteducation['name'], 
      	            'organization_id' => 1,
      	            'created_at' => DB::raw('NOW()'),
      	            'updated_at' => DB::raw('NOW()'));
      $check = DB::table('education')->insertGetId( $data );

		if($check > 0){
         
		Audit::logaudit('Educations', 'create', 'created: '.$posteducation['name']);
        return $check;
        }else{
         return 1;
        }
      
	}  

     public function createbank()
	{
      $postbank = Input::all();
      $data = array('bank_name' => $postbank['name'], 
      	            'bank_code' => $postbank['code'], 
      	            'organization_id' => 1,
      	            'created_at' => DB::raw('NOW()'),
      	            'updated_at' => DB::raw('NOW()'));
      $check = DB::table('banks')->insertGetId( $data );

		if($check > 0){
         
		Audit::logaudit('Banks', 'create', 'created: '.$postbank['name']);
        return $check;
        }else{
         return 1;
        }
      
	} 

	public function createbankbranch()
	{
      $postbankbranch = Input::all();
      $data = array('bank_branch_name' => $postbankbranch['name'], 
      	            'branch_code' => $postbankbranch['code'], 
      	            'bank_id' => $postbankbranch['bid'], 
      	            'organization_id' => 1,
      	            'created_at' => DB::raw('NOW()'),
      	            'updated_at' => DB::raw('NOW()'));
      $check = DB::table('bank_branches')->insertGetId( $data );

		if($check > 0){
         
		Audit::logaudit('Bank Branches', 'create', 'created: '.$postbankbranch['name']);
        return $check;
        }else{
         return 1;
        }
      
	} 

     public function createbranch()
	{
      $postbranch = Input::all();
      $data = array('name' => $postbranch['name'],
      	            'created_at' => DB::raw('NOW()'),
      	            'updated_at' => DB::raw('NOW()'));
      $check = DB::table('branches')->insertGetId( $data );

		if($check > 0){
         
		Audit::logaudit('Banks', 'create', 'created: '.$postbranch['name']);
        return $check;
        }else{
         return 1;
        }
      
	} 

    
    public function createdepartment()
	{
      $postdept = Input::all();
      $data = array('department_name' => $postdept['name'], 
      	            'organization_id' => 1,
      	            'created_at' => DB::raw('NOW()'),
      	            'updated_at' => DB::raw('NOW()'));
      $check = DB::table('departments')->insertGetId( $data );

		if($check > 0){
         
		Audit::logaudit('Departments', 'create', 'created: '.$postdept['name']);
        return $check;
        }else{
         return 1;
        }
      
	} 

    public function createtype()
	{
      $posttype = Input::all();
      $data = array('employee_type_name' => $posttype['name'], 
      	            'organization_id' => 1,
      	            'created_at' => DB::raw('NOW()'),
      	            'updated_at' => DB::raw('NOW()'));
      $check = DB::table('employee_type')->insertGetId( $data );

		if($check > 0){
         
		Audit::logaudit('Employee Types', 'create', 'created: '.$posttype['name']);
        return $check;
        }else{
         return 1;
        }
      
	} 

	public function creategroup()
	{
      $postgroup = Input::all();
      $data = array('job_group_name' => $postgroup['name'], 
      	            'organization_id' => 1,
      	            'created_at' => DB::raw('NOW()'),
      	            'updated_at' => DB::raw('NOW()'));
      $check = DB::table('job_group')->insertGetId( $data );

		if($check > 0){
         
		Audit::logaudit('Job Groups', 'create', 'created: '.$postgroup['name']);
        return $check;
        }else{
         return 1;
        }
      
	} 

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
    $currency = Currency::find(1);
    $employees = Employee::all();
		$branches = Branch::all();
		$departments = Department::all();
		$jgroups = Jobgroup::all();
		$etypes = EType::all();
		$banks = Bank::all();
		$bbranches = BBranch::all();
		$educations = Education::all();
		$citizenships = Citizenship::all();
    $allowances = Allowance::all();
    $deductions = Deduction::all();
		$pfn = Employee::orderBy('id', 'DESC')->first();
		return View::make('employees.create', compact('currency','employees','citizenships','pfn','branches','departments','etypes','jgroups','banks','bbranches','educations','allowances','deductions'));
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	$validator = Validator::make($data = Input::all(), Employee::$rules,Employee::$messages);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		
        
        try
        {
        $employee = new Employee;

       if ( Input::hasFile('image')) {

            $file = Input::file('image');
            $name = time().'-'.$file->getClientOriginalName();
            $file = $file->move('public/uploads/employees/photo', $name);
            $input['file'] = '/public/uploads/employees/photo'.$name;
            $employee->photo = $name;
        }else{
        	$employee->photo = 'default_photo.png';
        }

        if ( Input::hasFile('signature')) {

            $file = Input::file('signature');
            $name = time().'-'.$file->getClientOriginalName();
            $file = $file->move('public/uploads/employees/signature/', $name);
            $input['file'] = '/public/uploads/employees/signature/'.$name;
            $employee->signature = $name;
        }else{
        	$employee->signature = 'sign_av.jpg';
        }

		$employee->personal_file_number = Input::get('personal_file_number');
		$employee->first_name = Input::get('fname');
		$employee->last_name = Input::get('lname');
		$employee->middle_name = Input::get('mname');
		$employee->identity_number = Input::get('identity_number');
		if(Input::get('passport_number') != null){
		$employee->passport_number = Input::get('passport_number');
	    }else{
        $employee->passport_number = null;
	    }
	    if(Input::get('pin') != null){
		$employee->pin = Input::get('pin');
		}else{
        $employee->pin = null;
	    }
	    if(Input::get('social_security_number') != null){
		$employee->social_security_number = Input::get('social_security_number');
	    }else{
        $employee->social_security_number = null;
	    }
	    if(Input::get('hospital_insurance_number') != null){
		$employee->hospital_insurance_number = Input::get('hospital_insurance_number');
	    }else{
        $employee->hospital_insurance_number = null;
	    }
	    if(Input::get('work_permit_number') != null){
		$employee->work_permit_number = Input::get('work_permit_number');
	    }else{
        $employee->work_permit_number = null;
	    }
        $employee->job_title = Input::get('jtitle');
        $employee->education_type_id = Input::get('education');
        $a = str_replace( ',', '', Input::get('pay') );
        $employee->basic_pay = $a;
        $employee->gender = Input::get('gender');
        $employee->marital_status = Input::get('status');
        $employee->yob = Input::get('dob');
        $employee->citizenship_id = Input::get('citizenship');
        $employee->mode_of_payment = Input::get('modep');
        if(Input::get('bank_account_number') != null ){
        $employee->bank_account_number = Input::get('bank_account_number');
        }else{
        $employee->bank_account_number = null;
	    }
	    if(Input::get('bank_eft_code') != null ){
        $employee->bank_eft_code = Input::get('bank_eft_code');
        }else{
        $employee->bank_eft_code = null;
        }if(Input::get('swift_code') != null ){
        $employee->swift_code = Input::get('swift_code');
        }else{
        $employee->swift_code = null;
        }
        if(Input::get('email_office') != null ){
        $employee->email_office = Input::get('email_office');
        }else{
        $employee->email_office = null;
        }
        if(Input::get('email_personal') != null ){
        $employee->email_personal = Input::get('email_personal');
        }else{
        $employee->email_personal = null;
        }
        if(Input::get('telephone_mobile') != null ){
        $employee->telephone_mobile = Input::get('telephone_mobile');
        }else{
        $employee->telephone_mobile = null;
        }
        $employee->postal_address = Input::get('address');
        $employee->postal_zip = Input::get('zip');
        $employee->date_joined = Input::get('djoined');
  	    $employee->bank_id = Input::get('bank_id');
  	    $employee->bank_branch_id = Input::get('bbranch_id');
  	    $employee->branch_id = Input::get('branch_id');
  	    $employee->department_id = Input::get('department_id');
  	    $employee->job_group_id = Input::get('jgroup_id');
		$employee->type_id = Input::get('type_id');
		if(Input::get('i_tax') != null ){
		$employee->income_tax_applicable = '1';
	    }else{
	    $employee->income_tax_applicable = '0';
	    }
	    if(Input::get('i_tax_relief') != null ){
	    $employee->income_tax_relief_applicable = '1';
	    }else{
	    $employee->income_tax_relief_applicable = '0';
	    }
	    if(Input::get('a_nhif') != null ){
	    $employee->hospital_insurance_applicable = '1';
	    }else{
	    $employee->hospital_insurance_applicable = '0';
	    }
	    if(Input::get('a_nssf') != null ){
		$employee->social_security_applicable = '1';
	    }else{
	    $employee->social_security_applicable = '0';
	    }
	    $employee->custom_field1 = Input::get('omode');
		$employee->organization_id = '1';
        $employee->start_date = Input::get('startdate');
        $employee->end_date = Input::get('enddate');

    $employee->is_approved = 0;
		$employee->save();

    if(Input::get('supervisor') != null || Input::get('supervisor') != ""){

    $supervisor = new Supervisor;

    $supervisor->supervisor_id = Input::get('supervisor');

    $supervisor->employee_id = $employee->id;
        
        $supervisor->save();
        }

    Audit::logaudit('Employee', 'create', 'created: '.$employee->personal_file_number.'-'.$employee->first_name.' '.$employee->last_name);

    $insertedId = $employee->id;

    //parse_str(Input::get('kindata'),$output);
     

      //parse_str(Input::get('docinfo'),$data);

    for($i=0;$i<count(Input::get('kin_first_name'));$i++){
        if((Input::get('kin_first_name')[$i] != '' || Input::get('kin_first_name')[$i] != null) && (Input::get('kin_last_name')[$i] != '' || Input::get('kin_last_name')[$i] != null)){
        $kin = new Nextofkin;
        $kin->employee_id=$insertedId;
        $kin->first_name = Input::get('kin_first_name')[$i];
        $kin->last_name = Input::get('kin_last_name')[$i];
        $kin->middle_name = Input::get('kin_middle_name')[$i];
        $kin->relationship = Input::get('relationship')[$i];
        $kin->contact = Input::get('contact')[$i];
        $kin->id_number = Input::get('id_number')[$i];

        $kin->save();

        Audit::logaudit('NextofKins', 'create', 'created: '.Input::get('kin_first_name')[$i].' for '.Employee::getEmployeeName($insertedId));
       }
     }

      $files = Input::file('path');
      $j = 0;

       foreach($files as $file){
       
       if ( Input::hasFile('path') && (Input::get('doc_name')[$j] != null || Input::get('doc_name')[$j] != '')){
       $document= new Document;
        
        $document->employee_id = $insertedId;

            $name = time().'-'.$file->getClientOriginalName();
            $file = $file->move('public/uploads/employees/documents/', $name);
            $input['file'] = '/public/uploads/employees/documents/'.$name;
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $document->document_path = $name;
            $document->document_name = Input::get('doc_name')[$j].'.'.$extension;
        

        $document->description = Input::get('description')[$j];

        $document->from_date = Input::get('fdate')[$j];

        $document->expiry_date = Input::get('edate')[$j];

        $document->save();

       Audit::logaudit('Documents', 'create', 'created: '.Input::get('doc_name')[$j].' for '.Employee::getEmployeeName($insertedId));
       $j=$j+1;
       }
       }

       $email = Confide::user()->email;

       Mail::send( 'emails.approveemployee', array('employee'=>$employee), function( $message ) use ($email)
    {
        
        $message->to($email )->subject( 'Employee Approval' );
    });

		return Redirect::route('employees.index')->withFlashMessage('Employee successfully created!');
		 }
    catch (FormValidationException $e)
    {
        return Redirect::back()->withInput()->withErrors($e->getErrors());
    }
	}

	public function getIndex(){
  
    return Redirect::route('employees.index')->withFlashMessage('Employee successfully created!');
    
        
	}

  public function serializeDoc(){
  
    parse_str(Input::get('docinfo'),$data);

    return $data;
    
        
  }

	/**
	 * Display the specified branch.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$employee = Employee::findOrFail($id);

		return View::make('employees.show', compact('employee'));
	}

	/**
	 * Show the form for editing the specified branch.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$employee = Employee::find($id);
    $employees = Employee::all();
		$branches = Branch::all();
		$departments = Department::all();
		$jgroups = Jobgroup::all();
		$etypes = EType::all();
		$citizenships = Citizenship::all();
		$contract = DB::table('employee')
		          ->join('employee_type','employee.type_id','=','employee_type.id')
		          ->where('type_id',2)
		          ->first();
		$banks = Bank::all();
		$bbranches = BBranch::where('bank_id',$employee ->bank_id)->get();
		$educations = Education::all();
    $kins = Nextofkin::where('employee_id',$id)->get();
    $docs = Document::where('employee_id',$id)->get();
    $countk = Nextofkin::where('employee_id',$id)->count();
    $countd = Document::where('employee_id',$id)->count();
    $currency = Currency::find(1);
    $supervisor = Supervisor::where('employee_id',$id)->first();
    $count = Supervisor::where('employee_id',$id)->count();
    $subordinates = Employee::all();
    $allowances = Allowance::all();
    $deductions = Deduction::all();
		return View::make('employees.edit', compact('count','subordinates','supervisor','currency','countk','countd','docs','kins','citizenships','contract','branches','educations','departments','etypes','jgroups','banks','bbranches','employee','allowances','deductions'));
	}

	/**
	 * Update the specified branch in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$employee = Employee::findOrFail($id);

		//$validator = Employee::validateUpdate(Input::all(), $id);

		$validator = Validator::make(Input::all(), Employee::rolesUpdate($employee->id),Employee::$messages);

		//$validator = Validator::make($data = Input::all(), Employee::$rules,Employee::$messages);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

        if ( Input::hasFile('image')) {

            $file = Input::file('image');
            $name = time().'-'.$file->getClientOriginalName();
            $file = $file->move('public/uploads/employees/photo', $name);
            $input['file'] = '/public/uploads/employees/photo'.$name;
            $employee->photo = $name;
        }else{
        	$employee->photo = Input::get('photo');
        }

        if ( Input::hasFile('signature')) {

            $file = Input::file('signature');
            $name = time().'-'.$file->getClientOriginalName();
            $file = $file->move('public/uploads/employees/signature/', $name);
            $input['file'] = '/public/uploads/employees/signature/'.$name;
            $employee->signature = $name;
        }else{
        	$employee->signature = Input::get('sign');
        }

		$employee->personal_file_number = Input::get('personal_file_number');
		$employee->first_name = Input::get('fname');
		$employee->last_name = Input::get('lname');
		$employee->middle_name = Input::get('mname');
		$employee->identity_number = Input::get('identity_number');
		if(Input::get('passport_number') != null){
		$employee->passport_number = Input::get('passport_number');
	    }else{
        $employee->passport_number = null;
	    }
	    if(Input::get('pin') != null){
		$employee->pin = Input::get('pin');
		}else{
        $employee->pin = null;
	    }
	    if(Input::get('social_security_number') 
        != null){
		$employee->social_security_number = Input::get('social_security_number');
	    }else{
        $employee->social_security_number = null;
	    }
	    if(Input::get('hospital_insurance_number') != null){
		$employee->hospital_insurance_number = Input::get('hospital_insurance_number');
	    }else{
        $employee->hospital_insurance_number = null;
	    }
	    if(Input::get('work_permit_number') != null){
		$employee->work_permit_number = Input::get('work_permit_number');
	    }else{
        $employee->work_permit_number = null;
	    }
        $employee->job_title = Input::get('jtitle');
        $a = str_replace( ',', '', Input::get('pay') );
        $employee->basic_pay = $a;
        $employee->education_type_id = Input::get('education');
        $employee->gender = Input::get('gender');
        $employee->marital_status = Input::get('status');
        $employee->yob = Input::get('dob');
        //$employee->citizenship_id = Input::get('citizenship');
        $employee->mode_of_payment = Input::get('modep');
        if(Input::get('bank_account_number') != null ){
        $employee->bank_account_number = Input::get('bank_account_number');
        }else{
        $employee->bank_account_number = null;
	    }
	    if(Input::get('bank_eft_code') != null ){
        $employee->bank_eft_code = Input::get('bank_eft_code');
        }else{
        $employee->bank_eft_code = null;
        }if(Input::get('swift_code') != null ){
        $employee->swift_code = Input::get('swift_code');
        }else{
        $employee->swift_code = null;
        }
        if(Input::get('email_office') != null ){
        $employee->email_office = Input::get('email_office');
        }else{
        $employee->email_office = null;
        }
        if(Input::get('email_personal') != null ){
        $employee->email_personal = Input::get('email_personal');
        }else{
        $employee->email_personal = null;
        }
        if(Input::get('telephone_mobile') != null ){
        $employee->telephone_mobile = Input::get('telephone_mobile');
        }else{
        $employee->telephone_mobile = null;
        }
        $employee->postal_address = Input::get('address');
        $employee->postal_zip = Input::get('zip');
        $employee->date_joined = Input::get('djoined');
	    $employee->bank_id = Input::get('bank_id');
	    $employee->bank_branch_id = Input::get('bbranch_id');
	    $employee->branch_id = Input::get('branch_id');
	    $employee->department_id = Input::get('department_id');
	    $employee->job_group_id = Input::get('jgroup_id');
		$employee->type_id = Input::get('type_id');
		if(Input::get('i_tax') != null ){
		$employee->income_tax_applicable = '1';
	    }else{
	    $employee->income_tax_applicable = '0';
	    }
	    if(Input::get('i_tax_relief') != null ){
	    $employee->income_tax_relief_applicable = '1';
	    }else{
	    $employee->income_tax_relief_applicable = '0';
	    }
	    if(Input::get('a_nhif') != null ){
	    $employee->hospital_insurance_applicable = '1';
	    }else{
	    $employee->hospital_insurance_applicable = '0';
	    }
	    if(Input::get('a_nssf') != null ){
		$employee->social_security_applicable = '1';
	    }else{
	    $employee->social_security_applicable = '0';
	    }
	    if(Input::get('active') != null ){
		$employee->in_employment = 'Y';
	    }else{
	    $employee->in_employment = 'N';
	    }
	    $employee->start_date = Input::get('startdate');
        $employee->end_date = Input::get('enddate');
        $employee->custom_field1 = Input::get('omode');
		$employee->update();

    $c = Supervisor::where('employee_id', $employee->id)->count();


    if($c>0){

    $supervisor = Supervisor::where('employee_id',$employee->id)->first();

    $supervisor->supervisor_id = Input::get('supervisor');

    $supervisor->employee_id = $employee->id;
        
        $supervisor->update();
        }


    else if(Input::get('supervisor') != null || Input::get('supervisor') != ""){

    $supervisor = new Supervisor;

    $supervisor->supervisor_id = Input::get('supervisor');

    $supervisor->employee_id = $employee->id;
        
        $supervisor->save();
        }

		 Audit::logaudit('Employee', 'update', 'updated: '.$employee->personal_file_number.'-'.$employee->first_name.' '.$employee->last_name);

    Nextofkin::where('employee_id', $id)->delete();
    for($i=0;$i<count(Input::get('kin_first_name'));$i++){
        if((Input::get('kin_first_name')[$i] != '' || Input::get('kin_first_name')[$i] != null) && (Input::get('kin_last_name')[$i] != '' || Input::get('kin_last_name')[$i] != null)){
        $kin = new Nextofkin;
        $kin->employee_id=$id;
        $kin->first_name = Input::get('kin_first_name')[$i];
        $kin->last_name = Input::get('kin_last_name')[$i];
        $kin->middle_name = Input::get('kin_middle_name')[$i];
        $kin->relationship = Input::get('relationship')[$i];
        $kin->contact = Input::get('contact')[$i];
        $kin->id_number = Input::get('id_number')[$i];

        $kin->save();

        Audit::logaudit('NextofKins', 'create', 'created: '.Input::get('kin_first_name')[$i].' for '.Employee::getEmployeeName($id));
       }
     }

      Document::where('employee_id', $id)->delete();
      $files = Input::file('path');
      $j = 0;
      if(isset($files)){
       foreach($files as $file){
       
       if ( Input::get('doc_name')[$j] != null || Input::get('doc_name')[$j] != ''){
       $document= new Document;
       $document->employee_id=$id;
       $name = '';
       if ( $file){
            if($file->getClientOriginalName() == null){
              $name = Input::get('curpath')[$j];
            }else{
              $name = time().'-'.$file->getClientOriginalName();
            }
            $file = $file->move('public/uploads/employees/documents/', $name);
            $input['file'] = '/public/uploads/employees/documents/'.$name;
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $document->document_path = $name;
            $document->document_name = Input::get('doc_name')[$j].'.'.$extension;
        
        }else{
            $name = Input::get('curpath')[$j];
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $document->document_path = $name;
            $document->document_name = Input::get('doc_name')[$j].'.'.$extension;

        }

        $document->description = Input::get('description')[$j];

        $document->from_date = Input::get('fdate')[$j];

        $document->expiry_date = Input::get('edate')[$j];

        $document->save();

       Audit::logaudit('Documents', 'create', 'created: '.Input::get('doc_name')[$j].' for '.Employee::getEmployeeName($id));
       $j=$j+1;
       }
     }
  }


		 if(Confide::user()->user_type == 'member'){
		 	return Redirect::to('dashboard');
		 } else {
		 	return Redirect::route('employees.index')->withFlashMessage('Employee successfully updated!');
		 }
		
	}

	/**
	 * Remove the specified branch from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{

		$employee = Employee::findOrFail($id);
		
		Employee::destroy($id);

		 Audit::logaudit('Employee', 'delete', 'deleted: '.$employee->personal_file_number.'-'.$employee->first_name.' '.$employee->last_name);


		return Redirect::route('employees.index')->withDeleteMessage('Employee successfully deleted!');
	}

	public function deactivate($id)
	{

		$employee = Employee::findOrFail($id);
		
		DB::table('employee')->where('id',$id)->update(array('in_employment'=>'N'));

		Audit::logaudit('Employee', 'deactivate', 'deactivated: '.$employee->personal_file_number.'-'.$employee->first_name.' '.$employee->last_name);


		return Redirect::route('employees.index')->withDeleteMessage('Employee successfully deactivated!');
	}

	public function activate($id)
	{

		$employee = Employee::findOrFail($id);
		
		DB::table('employee')->where('id',$id)->update(array('in_employment'=>'Y'));

		Audit::logaudit('Employee', 'activate', 'activated: '.$employee->personal_file_number.'-'.$employee->first_name.' '.$employee->last_name);


		return Redirect::to('deactives')->withFlashMessage($employee->personal_file_number.'-'.$employee->first_name.' '.$employee->last_name.' successfully activated!');
	}

	public function view($id){

		$employee = Employee::find($id);

		$appraisals = Appraisal::where('employee_id', $id)->get();

        $kins = Nextofkin::where('employee_id', $id)->get();

        $occurences = Occurence::where('employee_id', $id)->get();

        $properties = Property::where('employee_id', $id)->get();

        $documents = Document::where('employee_id', $id)->get();

        $benefits = Employeebenefit::where('jobgroup_id', $employee->job_group_id)->get();

        $count = Employeebenefit::where('jobgroup_id', $employee->job_group_id)->count();

		$organization = Organization::find(1);

		return View::make('employees.view', compact('employee','appraisals','kins','documents','occurences','properties','count','benefits'));
		
	}


  public function approve($id){

    $employee = Employee::find($id);

    $appraisals = Appraisal::where('employee_id', $id)->get();

        $kins = Nextofkin::where('employee_id', $id)->get();

        $occurences = Occurence::where('employee_id', $id)->get();

        $properties = Property::where('employee_id', $id)->get();

        $documents = Document::where('employee_id', $id)->get();

        $benefits = Employeebenefit::where('jobgroup_id', $employee->job_group_id)->get();

        $count = Employeebenefit::where('jobgroup_id', $employee->job_group_id)->count();

    $organization = Organization::find(1);

    return View::make('employees.approve', compact('employee','appraisals','kins','documents','occurences','properties','count','benefits'));
    
  }

   public function doapprove($id){

    $employee = Employee::find($id);

    $employee->is_approved = 1;

    $employee->update();

    return Redirect::route('employees.index')->withFlashMessage('Employee successfully approved!');
    
  }

	public function viewdeactive($id){

		$employee = Employee::find($id);

		$appraisals = Appraisal::where('employee_id', $id)->get();

        $kins = Nextofkin::where('employee_id', $id)->get();

        $occurences = Occurence::where('employee_id', $id)->get();

        $properties = Property::where('employee_id', $id)->get();

        $documents = Document::where('employee_id', $id)->get();

        $benefits = Employeebenefit::where('jobgroup_id', $employee->job_group_id)->get();

        $count = Employeebenefit::where('jobgroup_id', $employee->job_group_id)->count();

		$organization = Organization::find(1);

		return View::make('employees.viewdeactive', compact('employee','appraisals','kins','documents','occurences','properties','count','benefits'));
		
	}


  public function disp(){
      $display = "";
      $postedit = Input::all();
      parse_str(Input::get('formdata'), $postedit);
      $salary = str_replace( ',', '', $postedit['salary']);
      $gross = 0.00;
      $allowances = 0.00;
      $deductions = 0.00;


      for($i=0;$i<count($postedit['allowance']);$i++){
         $allowances = $allowances + str_replace( ',', '', $postedit['allowance'][$i]);
      }

      for($i=0;$i<count($postedit['deduction']);$i++){
         $deductions = $deductions + str_replace( ',', '', $postedit['deduction'][$i]);
      }

      $gross = $gross + $salary + $allowances;

        $paye = number_format(Payroll::payecalc($gross),2);
        $nssf = number_format(Payroll::nssfcalc($gross),2);
        $nhif = number_format(Payroll::nhifcalc($gross),2);
        $totaldeductions = number_format(Payroll::dedcalc($gross,$deductions),2);
        $net  = Payroll::asMoney(Payroll::netcalc($gross,$deductions));

         return json_encode(["paye"=>$paye,"nssf"=>$nssf,"nhif"=>$nhif,"net"=>$net,"gross"=>number_format($gross, 2),"salary"=>number_format($salary, 2),"allowances"=>number_format($allowances, 2),"deductions"=>number_format($deductions, 2),"totaldeductions"=>$totaldeductions]);
        //echo json_encode(array("paye"=>$paye,"nssf"=>$nssf,"nhif"=>$nhif));
        //$net = number_format(Payroll::netcalc($employee->id,$fperiod),2);
       /*

        $display .="
          <input class='form-control' placeholder='' type='text' name='gross' id='gross' value='$gross'>
          <input readonly class='form-control' placeholder='' type='text' name='paye' id='paye' value='$paye'>
          <input readonly class='form-control' placeholder='' type='text' name='nssf' id='nssf' value='$nssf'>
          <input readonly class='form-control' placeholder='' type='text' name='nssf' id='nhif' value='$nhif'>
          <input readonly class='form-control' placeholder='' type='text' name='net' id='net' value='0'>
        
        ";
    
        return $display;
        exit();*/
        $currency = Currency::find(1);
        //return View::make('payroll.payroll_calculator', compact('gross','paye','nssf','nhif','currency'));


        echo json_encode(array("paye"=>$paye,"nssf"=>$nssf,"nhif"=>$nhif));
        //return $display;
        exit();

    }


  public static function grosscalc($net){
      
        $total = 0;
        $gross = $net;
        $y =0 ;
        $x =0 ;
        
        for($i=$net;$i>0;$i--){
        
        $total = $net-static::payencalc($net)-static::nssfncalc($net)-static::nhifncalc($net);
      
        $gross=($gross-$total)+$net;
        $net=$total;
        $y=$x;
        $x=($gross-$net)/2;
        $i=$x-$y;
        }

    return round($gross,2);

    }

    public function dispgross(){
      $display = "";
      $postedit = Input::all();
      parse_str(Input::get('formdata'), $postedit);
      $net = str_replace( ',', '', $postedit['net1']);
      //print_r($searcharray['net1']); 

       $total = 0;
        $gross = $net;
        $y =0 ;
        $x =0 ;
        $a =0 ;
        $z =  str_replace( ',', '', $postedit['net1']);


        $paye1 = 0;
        $nssf1 = 0;
        $nhif1 = 0;
        $salary = 0;
        $allowances = 0;
        $deductions = 0;
        $totded = 0;

        for($i=0;$i<count($postedit['netallowance']);$i++){
         $allowances = $allowances + str_replace( ',', '', $postedit['netallowance'][$i]);
        }

        for($i=0;$i<count($postedit['netdeduction']);$i++){
         $deductions = $deductions + str_replace( ',', '', $postedit['netdeduction'][$i]);
        } 
        
    for($i=$net;;$i--){

    $gross = $gross;

    $nssf1 = DB::table('social_security')->whereRaw($gross.' between income_from and income_to')->pluck('ss_amount_employee');
    
    $nhif1 = DB::table('hospital_insurance')->whereRaw($gross.' between income_from and income_to')->pluck('hi_amount'); 

    $taxable = $gross-$nssf1;
    
    if($taxable>=11180 && $taxable<21715){
    $paye1 = (1118+($taxable-11180)*15/100)-1280;
    }else if($taxable>=21715 && $taxable<32249){
    $paye1 = (2698.03+($taxable-21715)*20/100)-1280;
    }else if($taxable>=32249 && $taxable<42783){
    $paye1 = (4804.73+($taxable-32249)*25/100)-1280;
    }else if($taxable>=42783){
    $paye1 = (7438.11+($taxable-42783)*30/100)-1280;
    }else{
    $paye1 = 0.00;
    }

    $total = $net-$paye1-$nssf1-$nhif1-$deductions;  
    $gross=($z-$total)+$net;
    $net=$total;
    $y=$x;
    $x=($gross-$net)/2;
    if($net+$x == 40000){
    $i=($x-$y);
    }else{
    if(round($a-($x-$y),2) == 0){
      if($gross<0){
        $gross = 0;
      }else{
        $gross=$gross;
        $salary = $gross - $allowances;
        $totded = $paye1+$nssf1+$nhif1+$deductions;  
      }
      break;
    }else{
    $i=$a-($x-$y);
    }
    }
    $a= ($x-$y);
    //echo $gross.'<br>';
    }


   // echo $nssf1;
        //return $display;

     return json_encode(["paye1"=>number_format($paye1,2),"nssf1"=>number_format($nssf1,2),"nhif1"=>number_format($nhif1,2),"netv"=>number_format($z,2),"gross1"=>number_format($gross, 2),"salary1"=>number_format($salary, 2),"netded"=>number_format($totded, 2)]);
     
        //$net = number_format(Payroll::netcalc($employee->id,$fperiod),2);

   $currency = Currency::find(1);
        

    }

	
}
