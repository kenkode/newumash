<?php

class EmergencyContactsController extends \BaseController {

	/**
	 * Display a listing of kins
	 *
	 * @return Response
	 */
	public function index()
	{
		$contacts = DB::table('employee')
		          ->join('emergencycontacts', 'employee.id', '=', 'emergencycontacts.employee_id')
		          ->where('in_employment','=','Y')
		           ->where('is_approved','=','1')
		          ->get();

		Audit::logaudit('Emergency Contacts', 'view', 'viewed employee emergency contacts');

		return View::make('emergencycontacts.index', compact('contacts'));
	}

	/**
	 * Show the form for creating a new kin
	 *
	 * @return Response
	 */
	public function create($id)
	{  
		$id = $id;

		$employees = DB::table('employee')
		          ->where('in_employment','=','Y')
		           ->where('is_approved','=','1')
		          ->get();
		return View::make('emergencycontacts.create', compact('employees','id'));
	}

	/**
	 * Store a newly created kin in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Emergencycontact::$rules,Emergencycontact::$messages);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}


		$contact = new Emergencycontact;

		$contact->employee_id=Input::get('employee_id');
		$contact->name = Input::get('name');
		$contact->relationship = Input::get('rship');
		$contact->id_number = Input::get('id_number');
		$contact->phone1=Input::get('phone1');
		$contact->phone2 = Input::get('phone2');
		if(Input::get('sel') != null){
        $contact->same_address_employee = 1;
		}else{
        $contact->same_address_employee = 0;
		}
		$contact->country = Input::get('country');
		$contact->address1 = Input::get('address1');
		$contact->address2 = Input::get('address2');
		$contact->city=Input::get('city');
		$contact->state = Input::get('state');
		$contact->zip = Input::get('zip');
		$contact->county = Input::get('county');
		$contact->home_phone = Input::get('home_phone');
		$contact->office_phone = Input::get('office_phone');
		$contact->cellular_phone = Input::get('cellular_phone');
		$contact->street_name = Input::get('street');
		$contact->main_road=Input::get('main_road');
		$contact->landmark = Input::get('landmark');
		
		$contact->save();

		Audit::logaudit('Emergency Contact', 'create', 'created: '.$contact->name.' for '.Employee::getEmployeeName(Input::get('employee_id')));


		return Redirect::to('EmergencyContacts/view/'.$contact->id)->withFlashMessage('Employee`s Emergency Contact successfully created!');
	}

	/**
	 * Display the specified kin.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$contact = Emergencycontact::findOrFail($id);

		return View::make('emergencycontacts.show', compact('contact'));
	}

	/**
	 * Show the form for editing the specified kin.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$contact = Emergencycontact::find($id);

		return View::make('emergencycontacts.edit', compact('contact'));
	}

	/**
	 * Update the specified kin in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$contact = Emergencycontact::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Emergencycontact::$rules,Emergencycontact::$messages);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}
        
		$contact->name = Input::get('name');
		$contact->relationship = Input::get('rship');
		$contact->id_number = Input::get('id_number');
		$contact->phone1=Input::get('phone1');
		$contact->phone2 = Input::get('phone2');
		if(Input::get('sel') != null){
        $contact->same_address_employee = 1;
		}else{
        $contact->same_address_employee = 0;
		}
		$contact->country = Input::get('country');
		$contact->address1 = Input::get('address1');
		$contact->address2 = Input::get('address2');
		$contact->city=Input::get('city');
		$contact->state = Input::get('state');
		$contact->zip = Input::get('zip');
		$contact->county = Input::get('county');
		$contact->home_phone = Input::get('home_phone');
		$contact->office_phone = Input::get('office_phone');
		$contact->cellular_phone = Input::get('cellular_phone');
		$contact->street_name = Input::get('street');
		$contact->main_road=Input::get('main_road');
		$contact->landmark = Input::get('landmark');

		$contact->update();

		Audit::logaudit('Emergency Contact', 'update', 'updated: '.$contact->name.' for '.Employee::getEmployeeName($contact->employee_id));

		return Redirect::to('EmergencyContacts/view/'.$id)->withFlashMessage('Employee`s emergency contact successfully updated!');
	}

	/**
	 * Remove the specified kin from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$contact = Emergencycontact::findOrFail($id);
		Emergencycontact::destroy($id);
		Audit::logaudit('Emergency contact', 'delete', 'deleted: '.$contact->name.' for '.Employee::getEmployeeName($contact->employee_id));

		return Redirect::to('employees/view/'.$contact->employee_id)->withDeleteMessage('Employee`s emergercy contact successfully deleted!');
	}

	public function view($id){

		$contact = Emergencycontact::find($id);

		$organization = Organization::find(1);

		return View::make('emergencycontacts.view', compact('contact'));
		
	}


}
