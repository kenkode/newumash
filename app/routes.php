<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/


Route::get('/', function()
{

    $count = count(User::all());

    if($count <= 1 ){
        $organization = Organization::find(1);
        return View::make('login',compact('organization'));
    }

  $organization = Organization::find(1);
  $datetime1 = strtotime(date('Y-m-d'));
  $datetime2 = strtotime($organization->expiry_date);

  $secs = $datetime2 - $datetime1;// == <seconds between the two times>
  $days = $secs / 86400;

  if($organization->expiry_date<=date('Y-m-d')){
  $organization = Organization::find(1);
  $message = "Your annual support has expired! Please contact Lixnet to renew your license";
  return View::make('login',compact('organization','message'));
  }else{

  if($days<=5){
  if (Confide::user()) {

        return Redirect::to('/dashboard');
        } else {
          $organization = Organization::find(1);
          $message = "Your annual support license has ".$days." days to expire! Please contact Lixnet to renew your license";
          return View::make('login',compact('organization','message'));
        }
  }else{
  if (Confide::user()) {

        return Redirect::to('/dashboard');
        } else {
          $organization = Organization::find(1);
          $message = "";
            return View::make('login',compact('organization','message'));
        }
      }
    }
});




Route::get('/dashboard', function()
{

  $organization = Organization::find(1);
  $datetime1 = strtotime(date('Y-m-d'));
  $datetime2 = strtotime($organization->expiry_date);

  $secs = $datetime2 - $datetime1;// == <seconds between the two times>
  $days = $secs / 86400;

  if($organization->expiry_date<=date('Y-m-d')){
  $organization = Organization::find(1);
  $message = "Your annual support has expired! Please contact Lixnet to renew your license";
  return View::make('login',compact('organization','message'));
  }else{

  if($days<=5){
  if (Confide::user()) {
  if(Confide::user()->user_type == 'admin'){

             
          $employees = Employee::getActiveEmployee();
           return View::make('dashboard', compact('employees'));



        } 


        if(Confide::user()->user_type == 'member'){

          $employee_id = DB::table('employee')->where('personal_file_number', '=', Confide::user()->username)->orWhere('email_office', '=', Confide::user()->email)->pluck('id');

             
          $employee = Employee::findorfail($employee_id);
          $supervisor = Supervisor::where('employee_id',$employee_id)->first();

          
          $citizen_id=$employee->citizenship_id;
          $citizenship_name=Citizenship::where('id',$citizen_id)->pluck('name');

           return View::make('empdash', compact('supervisor','employee','citizenship_name'));



        }
        } else {
          $organization = Organization::find(1);
          $message = "Your annual support license has ".$days." days to expire! Please contact Lixnet to renew your license";
          return View::make('login',compact('organization','message'));
        }
  }else{
	if (Confide::user()) {


        if(Confide::user()->user_type == 'admin'){

             
          $employees = Employee::getActiveEmployee();
           return View::make('dashboard', compact('employees'));



        } 


        if(Confide::user()->user_type == 'member'){

          $employee_id = DB::table('employee')->where('personal_file_number', '=', Confide::user()->username)->orWhere('email_office', '=', Confide::user()->email)->pluck('id');

             
          $employee = Employee::findorfail($employee_id);
          $supervisor = Supervisor::where('employee_id',$employee_id)->first();

          
          $citizen_id=$employee->citizenship_id;
          $citizenship_name=Citizenship::where('id',$citizen_id)->pluck('name');

           return View::make('empdash', compact('supervisor','employee','citizenship_name'));



        } 

        

      
        } else {
            $message = "";
            return View::make('login',compact('organization','message'));
        }
      }
    }
});
//

Route::get('fpassword', function(){

  return View::make(Config::get('confide::forgot_password_form'));

});
// Confide routes

Route::get('users/forgot_password',function(){
  $organization=DB::table('organizations')->where('id','=',1)->first();
  return View::make('forgot',compact('organization'));
});

Route::resource('users', 'UsersController');
Route::get('users/create', 'UsersController@create');
Route::get('users/edit/{user}', 'UsersController@edit');
Route::post('users/update/{user}', 'UsersController@update');
Route::post('users', 'UsersController@store');
Route::get('users/add', 'UsersController@add');
Route::post('users/newuser', 'UsersController@newuser');
Route::get('users/login', 'UsersController@login');
Route::post('users/login', 'UsersController@doLogin');
Route::get('users/confirm/{code}', 'UsersController@confirm');
//Route::get('users/forgot_password', 'UsersController@forgotPassword');
Route::post('users/forgot_password', 'UsersController@doForgotPassword');
Route::get('users/reset_password/{token}', 'UsersController@resetPassword');
Route::post('users/reset_password', 'UsersController@doResetPassword');
Route::get('users/logout', 'UsersController@logout');
Route::get('users/activate/{user}', 'UsersController@activate');
Route::get('users/deactivate/{user}', 'UsersController@deactivate');
Route::get('users/destroy/{user}', 'UsersController@destroy');
Route::get('users/password/{user}', 'UsersController@Password');
Route::post('users/password/{user}', 'UsersController@changePassword');
Route::get('users/profile/{user}', 'UsersController@profile');
Route::get('users/show/{user}', 'UsersController@show');



Route::post('users/pass', 'UsersController@changePassword2');

Route::group(['before' => 'manage_roles'], function() {

Route::resource('roles', 'RolesController');
Route::get('roles/create', 'RolesController@create');
Route::get('roles/edit/{id}', 'RolesController@edit');
Route::post('roles/update/{id}', 'RolesController@update');
Route::get('roles/delete/{id}', 'RolesController@destroy');

});

Route::get('import', function(){

    return View::make('import');
});


Route::group(['before' => 'manage_system'], function() {

Route::get('system', function(){


    $organization = Organization::find(1);

    return View::make('system.index', compact('organization'));
});

});



Route::get('license', function(){


    $organization = Organization::find(1);

    return View::make('system.license', compact('organization'));
});




/**
* Organization routes
*/

Route::group(['before' => 'manage_organization'], function() {

Route::resource('organizations', 'OrganizationsController');

Route::post('organizations/update/{id}', 'OrganizationsController@update');
Route::post('organizations/logo/{id}', 'OrganizationsController@logo');

});

Route::get('language/{lang}', 
           array(
                  'as' => 'language.select', 
                  'uses' => 'OrganizationsController@language'
                 )
          );



Route::resource('currencies', 'CurrenciesController');
Route::get('currencies/edit/{id}', 'CurrenciesController@edit');
Route::post('currencies/update/{id}', 'CurrenciesController@update');
Route::get('currencies/delete/{id}', 'CurrenciesController@destroy');
Route::get('currencies/create', 'CurrenciesController@create');



/*
* branches routes
*/

Route::group(['before' => 'manage_branches'], function() {

Route::resource('branches', 'BranchesController');
Route::post('branches/update/{id}', 'BranchesController@update');
Route::get('branches/delete/{id}', 'BranchesController@destroy');
Route::get('branches/edit/{id}', 'BranchesController@edit');
});
/*
* groups routes
*/
Route::group(['before' => 'manage_groups'], function() {

Route::resource('groups', 'GroupsController');
Route::post('groups/update/{id}', 'GroupsController@update');
Route::get('groups/delete/{id}', 'GroupsController@destroy');
Route::get('groups/edit/{id}', 'GroupsController@edit');
});

/*
* accounts routes
*/

Route::group(['before' => 'process_payroll'], function() {

Route::resource('accounts', 'AccountsController');
Route::post('accounts/update/{id}', 'AccountsController@update');
Route::get('accounts/delete/{id}', 'AccountsController@destroy');
Route::get('accounts/edit/{id}', 'AccountsController@edit');
Route::get('accounts/show/{id}', 'AccountsController@show');
Route::get('accounts/create/{id}', 'AccountsController@create');

});

/*
* journals routes
*/
Route::resource('journals', 'JournalsController');
Route::post('journals/update/{id}', 'JournalsController@update');
Route::get('journals/delete/{id}', 'JournalsController@destroy');
Route::get('journals/edit/{id}', 'JournalsController@edit');
Route::get('journals/show/{id}', 'JournalsController@show');



/*
* license routes
*/

Route::post('license/key', 'OrganizationsController@generate_license_key');
Route::post('license/activate', 'OrganizationsController@activate_license');
Route::get('license/activate/{id}', 'OrganizationsController@activate_license_form');

/*
* Audits routes
*/
Route::group(['before' => 'manage_audits'], function() {

Route::resource('audits', 'AuditsController');

});

/*
* backups routes
*/

Route::get('backups', function(){

   
    //$backups = Backup::getRestorationFiles('../app/storage/backup/');

    return View::make('backup');

});


Route::get('backups/create', function(){

    echo '<pre>';

    $instance = Backup::getBackupEngineInstance();

    print_r($instance);

    //Backup::setPath(public_path().'/backups/');

   //Backup::export();
    //$backups = Backup::getRestorationFiles('../app/storage/backup/');

    //return View::make('backup');

});







/*
* #####################################################################################################################
*/
Route::group(['before' => 'manage_holiday'], function() {

Route::resource('holidays', 'HolidaysController');
Route::get('holidays/edit/{id}', 'HolidaysController@edit');
Route::get('holidays/delete/{id}', 'HolidaysController@destroy');
Route::post('holidays/update/{id}', 'HolidaysController@update');

});

Route::group(['before' => 'manage_leavetype'], function() {

Route::resource('leavetypes', 'LeavetypesController');
Route::get('leavetypes/edit/{id}', 'LeavetypesController@edit');
Route::get('leavetypes/delete/{id}', 'LeavetypesController@destroy');
Route::post('leavetypes/update/{id}', 'LeavetypesController@update');

});


Route::resource('leaveapplications', 'LeaveapplicationsController');
Route::get('leaveapplications/edit/{id}', 'LeaveapplicationsController@edit');
Route::get('leaveapplications/delete/{id}', 'LeaveapplicationsController@destroy');
Route::post('leaveapplications/update/{id}', 'LeaveapplicationsController@update');
Route::get('leaveapplications/approve/{id}', 'LeaveapplicationsController@approve');
Route::post('leaveapplications/approve/{id}', 'LeaveapplicationsController@doapprove');
Route::get('leaveapplications/cancel/{id}', 'LeaveapplicationsController@cancel');
Route::get('leaveapplications/reject/{id}', 'LeaveapplicationsController@reject');
Route::get('leaveapplications/show/{id}', 'LeaveapplicationsController@show');
Route::post('createLeave', 'LeaveapplicationsController@createleave');

Route::get('leaveapplications/approvals', 'LeaveapplicationsController@approvals');
Route::get('leaveapplications/rejects', 'LeaveapplicationsController@rejects');
Route::get('leaveapplications/cancellations', 'LeaveapplicationsController@cancellations');
Route::get('leaveapplications/amends', 'LeaveapplicationsController@amended');


Route::get('leaveapprovals', function(){

  $leaveapplications = Leaveapplication::all();

  return View::make('leaveapplications.approved', compact('leaveapplications'));

} );

Route::group(['before' => 'amend_application'], function() {

Route::get('leaveamends', function(){

  $leaveapplications = Leaveapplication::all();

  return View::make('leaveapplications.amended', compact('leaveapplications'));

} );

});

Route::group(['before' => 'reject_application'], function() {

Route::get('leaverejects', function(){

  $leaveapplications = Leaveapplication::all();

  return View::make('leaveapplications.rejected', compact('leaveapplications'));

} );

});

Route::group(['before' => 'manage_settings'], function() {

Route::get('migrate', function(){

    return View::make('migration');

});

});


/*
* Template routes and generators 
*/


Route::get('template/employees', function(){

  $bank_data = Bank::all();

  $data = Employee::all();

  $employees = Employee::all();

  $bankbranch_data = BBranch::all();
 
  $branch_data = Branch::all();

  $department_data = Department::all();

  $employeetype_data = EType::all();

  $jobgroup_data = Jobgroup::all();

  Excel::create('Employees', function($excel) use($bank_data, $bankbranch_data, $branch_data, $department_data, $employeetype_data, $jobgroup_data,$employees, $data) {


    require_once(base_path()."/vendor/phpoffice/phpexcel/Classes/PHPExcel/NamedRange.php");
    require_once(base_path()."/vendor/phpoffice/phpexcel/Classes/PHPExcel/Cell/DataValidation.php");

    

    $excel->sheet('employees', function($sheet) use($bank_data, $bankbranch_data, $branch_data, $department_data, $employeetype_data, $jobgroup_data, $data, $employees){


              $sheet->row(1, array(
     'EMPLOYMENT NUMBER','FIRST NAME', 'SURNAME', 'OTHER NAMES', 'ID NUMBER', 'KRA PIN', 'NSSF NUMBER', 'NHIF NUMBER','EMAIL ADDRESS','BASIC PAY'
));

             
                $empdata = array();

                foreach($employees as $d){

                  $empdata[] = $d->personal_file_number.':'.$d->first_name.' '.$d->last_name.' '.$d->middle_name;
                }

                $emplist = implode(", ", $empdata);

                

                $listdata = array();

                foreach($data as $d){

                  $listdata[] = $d->allowance_name;
                }

                $list = implode(", ", $listdata);
   

    

                

                
        

    });

  })->export('xls');
});


/*
*allowance template
*
*/

Route::get('template/allowances', function(){

  $data = Allowance::all();
  $employees = Employee::all();


  Excel::create('Allowances', function($excel) use($data, $employees) {

    require_once(base_path()."/vendor/phpoffice/phpexcel/Classes/PHPExcel/NamedRange.php");
    require_once(base_path()."/vendor/phpoffice/phpexcel/Classes/PHPExcel/Cell/DataValidation.php");

    

    $excel->sheet('allowances', function($sheet) use($data, $employees){


              $sheet->row(1, array(
              'EMPLOYEE', 'ALLOWANCE TYPE', 'FORMULAR', 'INSTALMENTS','AMOUNT','ALLOWANCE DATE',
              ));

              $sheet->setWidth(array(
                    'A'     =>  30,
                    'B'     =>  30,
                    'C'     =>  30,
                    'D'     =>  30,
                    'E'     =>  30,
                    'F'     =>  30,
              ));

             $sheet->getStyle('F2:F1000')
            ->getNumberFormat()
            ->setFormatCode('yyyy-mm-dd');



                $row = 2;
                $r = 2;
            
            for($i = 0; $i<count($employees); $i++){
            
             $sheet->SetCellValue("YY".$row, $employees[$i]->personal_file_number." : ".$employees[$i]->first_name.' '.$employees[$i]->last_name);
             $row++;
            }  

                $sheet->_parent->addNamedRange(
                        new \PHPExcel_NamedRange(
                        'names', $sheet, 'YY2:YY'.(count($employees)+1)
                        )
                );

                

               for($i = 0; $i<count($data); $i++){
            
             $sheet->SetCellValue("YZ".$r, $data[$i]->allowance_name);
             $r++;
            }  

                $sheet->_parent->addNamedRange(
                        new \PHPExcel_NamedRange(
                        'allowances', $sheet, 'YZ2:YZ'.(count($data)+1)
                        )
                );
   

    for($i=2; $i <= 1000; $i++){

                $objValidation = $sheet->getCell('B'.$i)->getDataValidation();
                $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Value is not in list.');
                $objValidation->setPromptTitle('Pick from list');
                $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('allowances'); //note this!

                $objValidation = $sheet->getCell('A'.$i)->getDataValidation();
                $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Value is not in list.');
                $objValidation->setPromptTitle('Pick from list');
                $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('names'); //note this!

                $objValidation = $sheet->getCell('C'.$i)->getDataValidation();
                $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Value is not in list.');
                $objValidation->setPromptTitle('Pick from list');
                $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('"One Time, Recurring, Instalments"'); //note this!
                }

    });

  })->export('xlsx');



});

/*
*earning template
*
*/


Route::get('template/earnings', function(){
   $data = Employee::all();

 \Excel::create('Earnings', function($excel) use($data) {
            require_once(base_path()."/vendor/phpoffice/phpexcel/Classes/PHPExcel/NamedRange.php");
            require_once(base_path()."/vendor/phpoffice/phpexcel/Classes/PHPExcel/Cell/DataValidation.php");

              

              $excel->sheet('Earnings', function($sheet) use($data) {

              $sheet->row(1, array(
             'EMPLOYEE', 'EARNING TYPE','NARRATIVE', 'FORMULAR', 'INSTALMENTS','AMOUNT','EARNING DATE',
              ));

              $sheet->setWidth(array(
                    'A'     =>  30,
                    'B'     =>  30,
                    'C'     =>  30,
                    'D'     =>  30,
                    'E'     =>  30,
                    'F'     =>  30,
                    'G'     =>  30,
              ));

             $sheet->getStyle('G2:G1000')
            ->getNumberFormat()
            ->setFormatCode('yyyy-mm-dd');

            $row = 2;
            
            for($i = 0; $i<count($data); $i++){
            
             $sheet->SetCellValue("ZZ".$row, $data[$i]->personal_file_number." : ".$data[$i]->first_name.' '.$data[$i]->last_name);
             $row++;
            }  

                $sheet->_parent->addNamedRange(
                        new \PHPExcel_NamedRange(
                        'names', $sheet, 'ZZ2:ZZ'.(count($data)+1)
                        )
                );

                $objPHPExcel = new PHPExcel;
                $objSheet = $objPHPExcel->getActiveSheet();

               $objSheet->protectCells('ZZ2:ZZ'.(count($data)+1), 'PHP');

                $objSheet->getStyle('G2:G1000')->getNumberFormat()->setFormatCode('yyyy-mm-dd');


                for($i=2; $i <= 1000; $i++){

                $objValidation = $sheet->getCell('A'.$i)->getDataValidation();
                $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Value is not in list.');
                $objValidation->setPromptTitle('Pick from list');
                $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('names'); //note this!

                $objValidation = $sheet->getCell('B'.$i)->getDataValidation();
                $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Value is not in list.');
                $objValidation->setPromptTitle('Pick from list');
                $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('"Bonus, Commission, Others"'); //note this!

                $objValidation = $sheet->getCell('D'.$i)->getDataValidation();
                $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Value is not in list.');
                $objValidation->setPromptTitle('Pick from list');
                $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('"One Time, Recurring, Instalments"'); //note this!
                }
            });

            

        })->download("xlsx");

});
/*
*Relief template
*
*/

Route::get('template/reliefs', function(){

  $employees = Employee::all();
  
  $data = Relief::all();

  Excel::create('Reliefs', function($excel) use($employees, $data) {

    require_once(base_path()."/vendor/phpoffice/phpexcel/Classes/PHPExcel/NamedRange.php");
    require_once(base_path()."/vendor/phpoffice/phpexcel/Classes/PHPExcel/Cell/DataValidation.php");

    

    $excel->sheet('reliefs', function($sheet) use($employees, $data){


              $sheet->row(1, array(
     'EMPLOYEE', 'RELIEF TYPE', 'AMOUNT'
));

             
                $sheet->setWidth(array(
                    'A'     =>  30,
                    'B'     =>  30,
                    'C'     =>  30,
              ));



                $row = 2;
                $r = 2;
            
            for($i = 0; $i<count($employees); $i++){
            
             $sheet->SetCellValue("YY".$row, $employees[$i]->personal_file_number." : ".$employees[$i]->first_name.' '.$employees[$i]->last_name);
             $row++;
            }  

                $sheet->_parent->addNamedRange(
                        new \PHPExcel_NamedRange(
                        'names', $sheet, 'YY2:YY'.(count($employees)+1)
                        )
                );

                

               for($i = 0; $i<count($data); $i++){
            
             $sheet->SetCellValue("YZ".$r, $data[$i]->relief_name);
             $r++;
            }  

                $sheet->_parent->addNamedRange(
                        new \PHPExcel_NamedRange(
                        'reliefs', $sheet, 'YZ2:YZ'.(count($data)+1)
                        )
                );
   

    for($i=2; $i <= 1000; $i++){

                $objValidation = $sheet->getCell('B'.$i)->getDataValidation();
                $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Value is not in list.');
                $objValidation->setPromptTitle('Pick from list');
                $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('reliefs'); //note this!



                $objValidation = $sheet->getCell('A'.$i)->getDataValidation();
                $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Value is not in list.');
                $objValidation->setPromptTitle('Pick from list');
                $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('names'); //note this!

    }

                

                
        

    });

  })->export('xlsx');



});



/*
*deduction template
*
*/

Route::get('template/deductions', function(){

  $data = Deduction::all();
  $employees = Employee::all();


  Excel::create('Deductions', function($excel) use($data, $employees) {

    require_once(base_path()."/vendor/phpoffice/phpexcel/Classes/PHPExcel/NamedRange.php");
    require_once(base_path()."/vendor/phpoffice/phpexcel/Classes/PHPExcel/Cell/DataValidation.php");

    

    $excel->sheet('deductions', function($sheet) use($data, $employees){


              $sheet->row(1, array(
     'EMPLOYEE', 'DEDUCTION TYPE', 'FORMULAR','INSTALMENTS','AMOUNT','DATE'
));

             
               $sheet->setWidth(array(
                    'A'     =>  30,
                    'B'     =>  30,
                    'C'     =>  30,
                    'D'     =>  30,
                    'E'     =>  30,
                    'F'     =>  30,
              ));

             $sheet->getStyle('F2:F1000')
            ->getNumberFormat()
            ->setFormatCode('yyyy-mm-dd');

            $row = 2;
                $r = 2;
            
            for($i = 0; $i<count($employees); $i++){
            
             $sheet->SetCellValue("YY".$row, $employees[$i]->personal_file_number." : ".$employees[$i]->first_name.' '.$employees[$i]->last_name);
             $row++;
            }  

                $sheet->_parent->addNamedRange(
                        new \PHPExcel_NamedRange(
                        'names', $sheet, 'YY2:YY'.(count($employees)+1)
                        )
                );

                

               for($i = 0; $i<count($data); $i++){
            
             $sheet->SetCellValue("YZ".$r, $data[$i]->deduction_name);
             $r++;
            }  

                $sheet->_parent->addNamedRange(
                        new \PHPExcel_NamedRange(
                        'deductions', $sheet, 'YZ2:YZ'.(count($data)+1)
                        )
                );
   

    for($i=2; $i <= 1000; $i++){

                $objValidation = $sheet->getCell('B'.$i)->getDataValidation();
                $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Value is not in list.');
                $objValidation->setPromptTitle('Pick from list');
                $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('deductions'); //note this!



                $objValidation = $sheet->getCell('A'.$i)->getDataValidation();
                $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Value is not in list.');
                $objValidation->setPromptTitle('Pick from list');
                $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('names'); //note this!

                $objValidation = $sheet->getCell('C'.$i)->getDataValidation();
                $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Value is not in list.');
                $objValidation->setPromptTitle('Pick from list');
                $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('"One Time, Recurring, Instalments"');

    }

                

                
        

    });

  })->export('xlsx');



});



/* #################### IMPORT EMPLOYEES ################################## */

Route::post('import/employees', function(){

  
  if(Input::hasFile('employees')){

      $destination = public_path().'/migrations/';

      $filename = str_random(12);

      $ext = Input::file('employees')->getClientOriginalExtension();
      $file = $filename.'.'.$ext;



      
      
     
      Input::file('employees')->move($destination, $file);


  


    Excel::selectSheetsByIndex(0)->load(public_path().'/migrations/'.$file, function($reader){

          $results = $reader->get();    
  
    foreach ($results as $result) {



      $employee = new Employee;

      $employee->personal_file_number = $result->employment_number;
      
      $employee->first_name = $result->first_name;
      $employee->last_name = $result->surname;
      $employee->middle_name = $result->other_names;
      $employee->identity_number = $result->id_number;
      $employee->pin = $result->kra_pin;
      $employee->social_security_number = $result->nssf_number;
      $employee->hospital_insurance_number = $result->nhif_number;
      $employee->email_office = $result->email_address;
      $employee->basic_pay = str_replace( ',', '', $result->basic_pay);
      $employee->save();
      
    }
    

    

  });



      
    }



  return Redirect::back()->with('notice', 'Employees have been succeffully imported');



  

});




/* #################### IMPORT EARNINGS ################################## */

Route::post('import/earnings', function(){

  
  if(Input::hasFile('earnings')){

      $destination = public_path().'/migrations/';

      $filename = str_random(12);

      $ext = Input::file('earnings')->getClientOriginalExtension();
      $file = $filename.'.'.$ext;

     
      Input::file('earnings')->move($destination, $file);


    Excel::selectSheetsByIndex(0)->load(public_path().'/migrations/'.$file, function($reader){

          $results = $reader->get();   
        
  
    foreach ($results as $result) {

      if($result->employee != null){


         $name = explode(' : ', $result->employee);

          
    
    $employeeid = DB::table('employee')->where('personal_file_number', '=', $name[0])->pluck('id');

         
    $earning = new Earnings;

    $earning->employee_id = $employeeid;

    $earning->earnings_name = $result->earning_type;

    $earning->narrative = $result->narrative;

    $earning->formular = $result->formular;

     

     if($result->formular == 'Instalments'){
        $earning->instalments = $result->instalments;
        $insts = $result->instalments;

        $a = str_replace( ',', '',$result->amount);
        $earning->earnings_amount = $a;

        $earning->earning_date = $result->earning_date;

        $effectiveDate = date('Y-m-d', strtotime("+".($insts-1)." months", strtotime($result->earning_date)));

        $First  = date('Y-m-01', strtotime($result->earning_date));
        $Last   = date('Y-m-t', strtotime($effectiveDate));

        $earning->first_day_month = $First;

        $earning->last_day_month = $Last;

      }else{
      $earning->instalments = '1';
        $a = str_replace( ',', '', $result->amount );
        $earning->earnings_amount = $a;

        $earning->earning_date = $result->earning_date;

        $First  = date('Y-m-01', strtotime($result->earning_date));
        $Last   = date('Y-m-t', strtotime($result->earning_date));
        

        $earning->first_day_month = $First;

        $earning->last_day_month = $Last;

      }


    $earning->save();


      }

   

  
    }
    

  });



      
    }



 return Redirect::back()->with('notice', 'earnings have been successfully imported');





  

});


/* #################### IMPORT RELIEFS ################################## */

Route::post('import/reliefs', function(){

  
  if(Input::hasFile('reliefs')){

      $destination = public_path().'/migrations/';

      $filename = str_random(12);

      $ext = Input::file('reliefs')->getClientOriginalExtension();
      $file = $filename.'.'.$ext;

     
      Input::file('reliefs')->move($destination, $file);


    Excel::selectSheetsByIndex(0)->load(public_path().'/migrations/'.$file, function($reader){

          $results = $reader->get();    
  
    foreach ($results as $result) {
       if($result->employee != null){

    $name = explode(':', $result->employee);

    
    $employeeid = DB::table('employee')->where('personal_file_number', '=', $name[0])->pluck('id');

    $reliefid = DB::table('relief')->where('relief_name', '=', $result->relief_type)->pluck('id');

    $relief = new ERelief;

    $relief->employee_id = $employeeid;

    $relief->relief_id = $reliefid;

    $relief->relief_amount = $result->amount;

    $relief->save();
      
    }
    
   }
    

  });



      
    }



  return Redirect::back()->with('notice', 'reliefs have been succeffully imported');



  

});



/* #################### IMPORT ALLOWANCES ################################## */

Route::post('import/allowances', function(){

  
  if(Input::hasFile('allowances')){

      $destination = public_path().'/migrations/';

      $filename = str_random(12);

      $ext = Input::file('allowances')->getClientOriginalExtension();
      $file = $filename.'.'.$ext;



      
      
     
      Input::file('allowances')->move($destination, $file);


  


  Excel::selectSheetsByIndex(0)->load(public_path().'/migrations/'.$file, function($reader){

    $results = $reader->get();    
  
    foreach ($results as $result) {

      if($result->employee != null){

    $name = explode(':', $result->employee);
    
    $employeeid = DB::table('employee')->where('personal_file_number', '=', $name[0])->pluck('id');

    $allowanceid = DB::table('allowances')->where('allowance_name', '=', $result->allowance_type)->pluck('id');

    $allowance = new EAllowances;

    $allowance->employee_id = $employeeid;

    $allowance->allowance_id = $allowanceid;

    $allowance->formular = $result->formular;

     

     if($result->formular == 'Instalments'){
        $allowance->instalments = $result->instalments;
        $insts = $result->instalments;

        $a = str_replace( ',', '',$result->amount);
        $allowance->allowance_amount = $a;

        $allowance->allowance_date = $result->allowance_date;

        $effectiveDate = date('Y-m-d', strtotime("+".($insts-1)." months", strtotime($result->allowance_date)));

        $First  = date('Y-m-01', strtotime($result->allowance_date));
        $Last   = date('Y-m-t', strtotime($effectiveDate));

        $allowance->first_day_month = $First;

        $allowance->last_day_month = $Last;

      }else{
      $allowance->instalments = '1';
        $a = str_replace( ',', '', $result->amount );
        $allowance->allowance_amount = $a;

        $allowance->allowance_date = $result->allowance_date;

        $First  = date('Y-m-01', strtotime($result->allowance_date));
        $Last   = date('Y-m-t', strtotime($result->allowance_date));
        

        $allowance->first_day_month = $First;

        $allowance->last_day_month = $Last;

      }

    $allowance->save();

    }
      
    }
    

    

  });



      
    }



  return Redirect::back()->with('notice', 'allowances have been succefully imported');



  

});


/* #################### IMPORT DEDUCTIONS ################################## */

Route::post('import/deductions', function(){

  
  if(Input::hasFile('deductions')){

      $destination = public_path().'/migrations/';

      $filename = str_random(12);

      $ext = Input::file('deductions')->getClientOriginalExtension();
      $file = $filename.'.'.$ext;



      
      
     
      Input::file('deductions')->move($destination, $file);


  


  Excel::selectSheetsByIndex(0)->load(public_path().'/migrations/'.$file, function($reader){

    $results = $reader->get();    
  
    foreach ($results as $result) {

      if($result->employee != null){


    $name = explode(':', $result->employee);
    
    $employeeid = DB::table('employee')->where('personal_file_number', '=', $name[0])->pluck('id');

    $deductionid = DB::table('deductions')->where('deduction_name', '=', $result->deduction_type)->pluck('id');

    $deduction = new EDeduction;

    $deduction->employee_id = $employeeid;

    $deduction->deduction_id = $deductionid;

    $deduction->formular = $result->formular;

     $a = str_replace( ',', '', $result->amount );
        $deduction->deduction_amount = $a;

    $deduction->deduction_date = $result->date;

    if($result->formular == 'Instalments'){
    $deduction->instalments = $result->instalments;
        $insts = $result->instalments;

        $effectiveDate = date('Y-m-d', strtotime("+".($insts-1)." months", strtotime($result->date)));

        $First  = date('Y-m-01', strtotime($result->date));
        $Last   = date('Y-m-t', strtotime($effectiveDate));

        $deduction->first_day_month = $First;

        $deduction->last_day_month = $Last;

      }else{
      $deduction->instalments = '1';

        $First  = date('Y-m-01', strtotime($result->date));
        $Last   = date('Y-m-t', strtotime($result->date));
        

        $deduction->first_day_month = $First;

        $deduction->last_day_month = $Last;

      }

    $deduction->save();

    }
      
    }
    

  });
      
    }

  return Redirect::back()->with('notice', 'deductions have been succefully imported');
  

});



/* #################### IMPORT BANK BRANCHES ################################## */

Route::post('import/bankBranches', function(){

  
  if(Input::hasFile('bbranches')){

      $destination = public_path().'/migrations/';

      $filename = str_random(12);

      $ext = Input::file('bbranches')->getClientOriginalExtension();
      $file = $filename.'.'.$ext;



      
      
     
      Input::file('bbranches')->move($destination, $file);


  


  Excel::selectSheetsByIndex(0)->load(public_path().'/migrations/'.$file, function($reader){

    $results = $reader->get();    
  
    foreach ($results as $result) {
  

    $bbranch = new BBranch;

    $bbranch->branch_code = $result->branch_code;

    $bbranch->bank_branch_name = $result->branch_name;

    $bbranch->bank_id = $result->bank_id;

    $bbranch->organization_id = $result->organization_id;

    $bbranch->save();
      
    }   

  });
      
    }


  return Redirect::back()->with('notice', 'bank branches have been succefully imported');



  

});

/* #################### IMPORT BANKS ################################## */

Route::post('import/banks', function(){

  
  if(Input::hasFile('banks')){

      $destination = public_path().'/migrations/';

      $filename = str_random(12);

      $ext = Input::file('banks')->getClientOriginalExtension();
      $file = $filename.'.'.$ext;



      
      
     
      Input::file('banks')->move($destination, $file);


  


  Excel::selectSheetsByIndex(0)->load(public_path().'/migrations/'.$file, function($reader){

    $results = $reader->get();    
  
    foreach ($results as $result) {
  

    $bank = new Bank;

    $bank->bank_name = $result->bank_name;

    $bank->bank_code = $result->bank_code;

    $bank->organization_id = $result->organization_id;

    $bank->save();
      
    }   

  });
      
    }


  return Redirect::back()->with('notice', 'banks have been succefully imported');



  

});



/*
* #####################################################################################################################
*/
/*
* banks routes
*/

Route::resource('banks', 'BanksController');
Route::post('banks/update/{id}', 'BanksController@update');
Route::get('banks/delete/{id}', 'BanksController@destroy');
Route::get('banks/edit/{id}', 'BanksController@edit');

/*
* departments routes
*/

Route::resource('departments', 'DepartmentsController');
Route::post('departments/update/{id}', 'DepartmentsController@update');
Route::get('departments/delete/{id}', 'DepartmentsController@destroy');
Route::get('departments/edit/{id}', 'DepartmentsController@edit');


/*
* bank branch routes
*/

Route::resource('bank_branch', 'BankBranchController');
Route::post('bank_branch/update/{id}', 'BankBranchController@update');
Route::get('bank_branch/delete/{id}', 'BankBranchController@destroy');
Route::get('bank_branch/edit/{id}', 'BankBranchController@edit');

/*
* allowances routes
*/

Route::resource('allowances', 'AllowancesController');
Route::post('allowances/update/{id}', 'AllowancesController@update');
Route::get('allowances/delete/{id}', 'AllowancesController@destroy');
Route::get('allowances/edit/{id}', 'AllowancesController@edit');

/*
* earningsettings routes
*/

Route::resource('earningsettings', 'EarningsettingsController');
Route::post('earningsettings/update/{id}', 'EarningsettingsController@update');
Route::get('earningsettings/delete/{id}', 'EarningsettingsController@destroy');
Route::get('earningsettings/edit/{id}', 'EarningsettingsController@edit');

/*
* benefits setting routes
*/

Route::resource('benefitsettings', 'BenefitSettingsController');
Route::post('benefitsettings/update/{id}', 'BenefitSettingsController@update');
Route::get('benefitsettings/delete/{id}', 'BenefitSettingsController@destroy');
Route::get('benefitsettings/edit/{id}', 'BenefitSettingsController@edit');

/*
* reliefs routes
*/

Route::resource('reliefs', 'ReliefsController');
Route::post('reliefs/update/{id}', 'ReliefsController@update');
Route::get('reliefs/delete/{id}', 'ReliefsController@destroy');
Route::get('reliefs/edit/{id}', 'ReliefsController@edit');

/*
* deductions routes
*/

Route::resource('deductions', 'DeductionsController');
Route::post('deductions/update/{id}', 'DeductionsController@update');
Route::get('deductions/delete/{id}', 'DeductionsController@destroy');
Route::get('deductions/edit/{id}', 'DeductionsController@edit');

/*
* nontaxables routes
*/

Route::resource('nontaxables', 'NonTaxablesController');
Route::post('nontaxables/update/{id}', 'NonTaxablesController@update');
Route::get('nontaxables/delete/{id}', 'NonTaxablesController@destroy');
Route::get('nontaxables/edit/{id}', 'NonTaxablesController@edit');

/*
* nssf routes
*/

Route::resource('nssf', 'NssfController');
Route::post('nssf/update/{id}', 'NssfController@update');
Route::get('nssf/delete/{id}', 'NssfController@destroy');
Route::get('nssf/edit/{id}', 'NssfController@edit');

/*
* nhif routes
*/

Route::resource('nhif', 'NhifController');
Route::post('nhif/update/{id}', 'NhifController@update');
Route::get('nhif/delete/{id}', 'NhifController@destroy');
Route::get('nhif/edit/{id}', 'NhifController@edit');

/*
* job group routes
*/

Route::resource('job_group', 'JobGroupController');
Route::post('job_group/update/{id}', 'JobGroupController@update');
Route::get('job_group/delete/{id}', 'JobGroupController@destroy');
Route::get('job_group/edit/{id}', 'JobGroupController@edit');
Route::get('job_group/show/{id}', 'JobGroupController@show');

/*
* employee type routes
*/

Route::resource('employee_type', 'EmployeeTypeController');
Route::post('employee_type/update/{id}', 'EmployeeTypeController@update');
Route::get('employee_type/delete/{id}', 'EmployeeTypeController@destroy');
Route::get('employee_type/edit/{id}', 'EmployeeTypeController@edit');

/*
* occurence settings routes
*/

Route::resource('occurencesettings', 'OccurencesettingsController');
Route::post('occurencesettings/update/{id}', 'OccurencesettingsController@update');
Route::get('occurencesettings/delete/{id}', 'OccurencesettingsController@destroy');
Route::get('occurencesettings/edit/{id}', 'OccurencesettingsController@edit');

/*
* citizenship routes
*/

Route::resource('citizenships', 'CitizenshipController');
Route::post('citizenships/update/{id}', 'CitizenshipController@update');
Route::get('citizenships/delete/{id}', 'CitizenshipController@destroy');
Route::get('citizenships/edit/{id}', 'CitizenshipController@edit');

/*
* employees routes
*/

Route::get('deactives', function(){

  $employees = Employee::getDeactiveEmployee();

  return View::make('employees.activate', compact('employees'));

} );


Route::resource('employees', 'EmployeesController');
Route::post('employees/update/{id}', 'EmployeesController@update');
Route::get('employees/deactivate/{id}', 'EmployeesController@deactivate');
Route::get('employees/activate/{id}', 'EmployeesController@activate');
Route::get('employees/edit/{id}', 'EmployeesController@edit');
Route::get('employees/view/{id}', 'EmployeesController@view');
Route::get('employees/approve/{id}', 'EmployeesController@approve');
Route::get('employees/doApprove/{id}', 'EmployeesController@doapprove');
Route::get('employees/viewdeactive/{id}', 'EmployeesController@viewdeactive');

Route::post('createCitizenship', 'EmployeesController@createcitizenship');
Route::post('createEducation', 'EmployeesController@createeducation');
Route::post('createBank', 'EmployeesController@createbank');
Route::post('createBankBranch', 'EmployeesController@createbankbranch');
Route::post('createBranch', 'EmployeesController@createbranch');
Route::post('createDepartment', 'EmployeesController@createdepartment');
Route::post('createType', 'EmployeesController@createtype');
Route::post('createGroup', 'EmployeesController@creategroup');
Route::post('createEmployee', 'EmployeesController@serializeDoc');
Route::get('employeeIndex', 'EmployeesController@getIndex');

Route::get('EmployeeForm', function(){

  /*$organization = Organization::find(1);

  $pdf = PDF::loadView('pdf.employee_form', compact('organization'))->setPaper('a4')->setOrientation('potrait');*/
    
  //return $pdf->stream('Employee_Form.pdf');
  
  $file= public_path(). "/uploads/employees/documents/umash appointment letter.docx";
        
  return Response::download($file, "umash appointment letter.docx");

});

/*
* occurences routes
*/

Route::resource('occurences', 'OccurencesController');
Route::post('occurences/update/{id}', 'OccurencesController@update');
Route::get('occurences/delete/{id}', 'OccurencesController@destroy');
Route::get('occurences/edit/{id}', 'OccurencesController@edit');
Route::get('occurences/view/{id}', 'OccurencesController@view');
Route::get('occurences/download/{id}', 'OccurencesController@getDownload');
Route::post('createOccurence', 'OccurencesController@createoccurence');
/*
* employee earnings routes
*/

Route::resource('other_earnings', 'EarningsController');
Route::post('other_earnings/update/{id}', 'EarningsController@update');
Route::get('other_earnings/delete/{id}', 'EarningsController@destroy');
Route::get('other_earnings/edit/{id}', 'EarningsController@edit');
Route::get('other_earnings/view/{id}', 'EarningsController@view');
Route::post('createEarning', 'EarningsController@createearning');

/*
* employee reliefs routes
*/

Route::resource('employee_relief', 'EmployeeReliefController');
Route::post('employee_relief/update/{id}', 'EmployeeReliefController@update');
Route::get('employee_relief/delete/{id}', 'EmployeeReliefController@destroy');
Route::get('employee_relief/edit/{id}', 'EmployeeReliefController@edit');
Route::get('employee_relief/view/{id}', 'EmployeeReliefController@view');
Route::post('createRelief', 'EmployeeReliefController@createrelief');

/*
* employee allowances routes
*/

Route::resource('employee_allowances', 'EmployeeAllowancesController');
Route::post('employee_allowances/update/{id}', 'EmployeeAllowancesController@update');
Route::get('employee_allowances/delete/{id}', 'EmployeeAllowancesController@destroy');
Route::get('employee_allowances/edit/{id}', 'EmployeeAllowancesController@edit');
Route::get('employee_allowances/view/{id}', 'EmployeeAllowancesController@view');
Route::post('createAllowance', 'EmployeeAllowancesController@createallowance');
Route::post('reloaddata', 'EmployeeAllowancesController@display');

/*
* employee nontaxables routes
*/

Route::resource('employeenontaxables', 'EmployeeNonTaxableController');
Route::post('employeenontaxables/update/{id}', 'EmployeeNonTaxableController@update');
Route::get('employeenontaxables/delete/{id}', 'EmployeeNonTaxableController@destroy');
Route::get('employeenontaxables/edit/{id}', 'EmployeeNonTaxableController@edit');
Route::get('employeenontaxables/view/{id}', 'EmployeeNonTaxableController@view');
Route::post('createNontaxable', 'EmployeeNonTaxableController@createnontaxable');

/*
* employee deductions routes
*/

Route::resource('employee_deductions', 'EmployeeDeductionsController');
Route::post('employee_deductions/update/{id}', 'EmployeeDeductionsController@update');
Route::get('employee_deductions/delete/{id}', 'EmployeeDeductionsController@destroy');
Route::get('employee_deductions/edit/{id}', 'EmployeeDeductionsController@edit');
Route::get('employee_deductions/view/{id}', 'EmployeeDeductionsController@view');
Route::post('createDeduction', 'EmployeeDeductionsController@creatededuction');
/*
* payroll routes
*/


Route::resource('payroll', 'PayrollController');
Route::post('deleterow', 'PayrollController@del_exist');
Route::post('showrecord', 'PayrollController@display');
Route::post('shownet', 'PayrollController@disp');
Route::post('showgross', 'PayrollController@dispgross');
Route::post('employee/shownet', 'EmployeesController@disp');
Route::post('employee/showgross', 'EmployeesController@dispgross');
Route::post('payroll/preview', 'PayrollController@create');
Route::get('payrollpreviewprint/{period}', 'PayrollController@previewprint');
Route::post('createNewAccount', 'PayrollController@createaccount');

Route::get('payrollcalculator', function(){
  $currency = Currency::find(1);
  return View::make('payroll.payroll_calculator',compact('currency'));

});

/*
* advance routes
*/


Route::resource('advance', 'AdvanceController');
Route::post('deleteadvance', 'AdvanceController@del_exist');
Route::post('advance/preview', 'AdvanceController@create');
Route::post('createAccount', 'AdvanceController@createaccount');

/*
* employees routes
*/
Route::resource('employees', 'EmployeesController');
Route::get('employees/show/{id}', 'EmployeesController@show');
Route::group(['before' => 'create_employee'], function() {
Route::get('employees/create', 'EmployeesController@create');
});
Route::get('employees/edit/{id}', 'EmployeesController@edit');
Route::post('employees/update/{id}', 'EmployeesController@update');
Route::get('employees/delete/{id}', 'EmployeesController@destroy');


Route::get('advanceReports', function(){

    return View::make('employees.advancereports');
});


Route::get('payrollReports', function(){

    return View::make('employees.payrollreports');
});

Route::get('statutoryReports', function(){

    return View::make('employees.statutoryreports');
});

Route::get('email/payslip', 'payslipEmailController@index');
Route::post('email/payslip/employees', 'payslipEmailController@sendEmail');

Route::get('reports/employees', function(){

    return View::make('employees.reports');
});

Route::get('reminders', function(){
$employees = Employee::where('type_id',2)->where('in_employment','Y')->whereNotNull('start_date')->whereNotNull('end_date')->get();
        Mail::send('reminders.message', compact('employees'), function($message){
        $message->to('ken.wango@lixnet.net', 'Ken Wango')->subject('Contract Reminders');
        });
     echo 'sent';
     //return Redirect::back()->with('success', 'Email Sent!');
});


Route::get('reports/selectEmployeeStatus', 'ReportsController@selstate');
Route::post('reports/employeelist', 'ReportsController@employees');
Route::get('employee/select', 'ReportsController@emp_id');
Route::post('reports/employee', 'ReportsController@individual');
Route::get('payrollReports/selectPeriod', 'ReportsController@period_payslip');
Route::post('payrollReports/payslip', 'ReportsController@payslip');
Route::get('payrollReports/selectAllowance', 'ReportsController@employee_allowances');
Route::post('payrollReports/allowances', 'ReportsController@allowances');
Route::get('payrollReports/selectEarning', 'ReportsController@employee_earnings');
Route::post('payrollReports/earnings', 'ReportsController@earnings');
Route::get('payrollReports/selectOvertime', 'ReportsController@employee_overtimes');
Route::post('payrollReports/overtimes', 'ReportsController@overtimes');
Route::get('payrollReports/selectRelief', 'ReportsController@employee_reliefs');
Route::post('payrollReports/reliefs', 'ReportsController@reliefs');
Route::get('payrollReports/selectDeduction', 'ReportsController@employee_deductions');
Route::post('payrollReports/deductions', 'ReportsController@deductions');
Route::get('payrollReports/selectnontaxableincome', 'ReportsController@employeenontaxableselect');
Route::post('payrollReports/nontaxables', 'ReportsController@employeenontaxables');
Route::get('payrollReports/selectPayePeriod', 'ReportsController@period_paye');
Route::post('payrollReports/payeReturns', 'ReportsController@payeReturns');
Route::get('payrollReports/selectRemittancePeriod', 'ReportsController@period_rem');
Route::get('payrollReports/selectRemittance/{period}', 'ReportsController@process_rem');
Route::post('payrollReports/payRemittances', 'ReportsController@payeRems');
Route::get('payrollReports/selectSummaryPeriod', 'ReportsController@period_summary');
Route::get('payrollReports/selectSummary/{period}', 'ReportsController@process_summary');
Route::post('payrollReports/payrollSummary', 'ReportsController@paySummary');
Route::get('payrollReports/selectNssfPeriod', 'ReportsController@period_nssf');
Route::post('payrollReports/nssfReturns', 'ReportsController@nssfReturns');
Route::get('payrollReports/selectNhifPeriod', 'ReportsController@period_nhif');
Route::post('payrollReports/nhifReturns', 'ReportsController@nhifReturns');
Route::get('payrollReports/selectNssfExcelPeriod', 'ReportsController@period_excel');
Route::post('payrollReports/nssfExcel', 'ReportsController@export');
Route::get('reports/selectEmployeeOccurence', 'ReportsController@selEmp');
Route::post('reports/occurence', 'ReportsController@occurence');
Route::get('reports/CompanyProperty/selectPeriod', 'ReportsController@propertyperiod');
Route::post('reports/companyproperty', 'ReportsController@property');
Route::get('reports/Appraisals/selectPeriod', 'ReportsController@appraisalperiod');
Route::post('reports/appraisal', 'ReportsController@appraisal');
Route::get('reports/nextofkin/selectEmployee', 'ReportsController@selempkin');
Route::post('reports/EmployeeKin', 'ReportsController@kin');
Route::get('advanceReports/selectRemittancePeriod', 'ReportsController@period_advrem');
Route::post('advanceReports/advanceRemittances', 'ReportsController@payeAdvRems');
Route::get('advanceReports/selectSummaryPeriod', 'ReportsController@period_advsummary');
Route::post('advanceReports/advanceSummary', 'ReportsController@payAdvSummary');





/*
*#################################################################
*/
Route::group(['before' => 'process_payroll'], function() {

    


Route::get('payrollmgmt', function(){

     $employees = Employee::getActiveEmployee();

  return View::make('payrollmgmt', compact('employees'));

});

});

Route::group(['before' => 'leave_mgmt'], function() {

Route::get('leavemgmt', function(){

  $leaveapplications = Leaveapplication::all();

  return View::make('leavemgmt', compact('leaveapplications'));

});

});


/*Route::get('erpmgmt', function(){

  return View::make('erpmgmt');

});*/



Route::get('cbsmgmt', function(){


      if(Confide::user()->user_type == 'admin'){

            $members = Member::all();

            //print_r($members);

            return View::make('cbsmgmt', compact('members'));

        } 

        if(Confide::user()->user_type == 'teller'){

            $members = Member::all();

            return View::make('tellers.dashboard', compact('members'));

        } 


        if(Confide::user()->user_type == 'member'){

            $loans = Loanproduct::all();
            $products = Product::all();

            $rproducts = Product::getRemoteProducts();

            
            return View::make('shop.index', compact('loans', 'products', 'rproducts'));

        } 


  



});





/*
* #####################################################################################################################
*/









Route::get('import', function(){

    return View::make('import');
});


Route::get('automated/loans', function(){

    
    $loanproducts = Loanproduct::all();

    return View::make('autoloans', compact('loanproducts'));
});

Route::get('automated/savings', function(){

    
   $savingproducts = Savingproduct::all();

    return View::make('automated', compact('savingproducts'));
});



Route::post('automated', function(){

    $members = DB::table('members')->where('is_active', '=', true)->get();


    $category = Input::get('category');


    
    
    if($category == 'savings'){

        $savingproduct_id = Input::get('savingproduct');

        $savingproduct = Savingproduct::findOrFail($savingproduct_id);

        

            foreach($savingproduct->savingaccounts as $savingaccount){

                if(($savingaccount->member->is_active) && (Savingaccount::getLastAmount($savingaccount) > 0)){

                    
                    $data = array(
                        'account_id' => $savingaccount->id,
                        'amount' => Savingaccount::getLastAmount($savingaccount), 
                        'date' => date('Y-m-d'),
                        'type'=>'credit'
                        );

                    Savingtransaction::creditAccounts($data);
                    

                    

                }
 
                

            

    }

       Autoprocess::record(date('Y-m-d'), 'saving', $savingproduct); 
      

        

    } else {

        $loanproduct_id = Input::get('loanproduct');

        $loanproduct = Loanproduct::findOrFail($loanproduct_id);


        

        

            foreach($loanproduct->loanaccounts as $loanaccount){

                if(($loanaccount->member->is_active) && (Loanaccount::getEMP($loanaccount) > 0)){

                    
                    
                    $data = array(
                        'loanaccount_id' => $loanaccount->id,
                        'amount' => Loanaccount::getEMP($loanaccount), 
                        'date' => date('Y-m-d')
                        
                        );


                    Loanrepayment::repayLoan($data);
                    

                    
                   

                    

                }
            }


             Autoprocess::record(date('Y-m-d'), 'loan', $loanproduct);
            

    }


    

    return Redirect::back()->with('notice', 'successfully processed');
    

    
});






Route::get('loanrepayments/offprint/{id}', 'LoanrepaymentsController@offprint');



Route::resource('members', 'MembersController');
Route::post('members/update/{id}', 'MembersController@update');
Route::get('members/delete/{id}', 'MembersController@destroy');
Route::get('members/edit/{id}', 'MembersController@edit');

Route::get('members/show/{id}', 'MembersController@show');
Route::get('members/loanaccounts/{id}', 'MembersController@loanaccounts');
Route::get('memberloans', 'MembersController@loanaccounts2');
Route::group(['before' => 'limit'], function() {

    Route::get('members/create', 'MembersController@create');
});

Route::resource('kins', 'KinsController');
Route::post('kins/update/{id}', 'KinsController@update');
Route::get('kins/delete/{id}', 'KinsController@destroy');
Route::get('kins/edit/{id}', 'KinsController@edit');
Route::get('kins/show/{id}', 'KinsController@show');
Route::get('kins/create/{id}', 'KinsController@create');





Route::resource('charges', 'ChargesController');
Route::post('charges/update/{id}', 'ChargesController@update');
Route::get('charges/delete/{id}', 'ChargesController@destroy');
Route::get('charges/edit/{id}', 'ChargesController@edit');
Route::get('charges/show/{id}', 'ChargesController@show');
Route::get('charges/disable/{id}', 'ChargesController@disable');
Route::get('charges/enable/{id}', 'ChargesController@enable');

Route::resource('savingproducts', 'SavingproductsController');
Route::post('savingproducts/update/{id}', 'SavingproductsController@update');
Route::get('savingproducts/delete/{id}', 'SavingproductsController@destroy');
Route::get('savingproducts/edit/{id}', 'SavingproductsController@edit');
Route::get('savingproducts/show/{id}', 'SavingproductsController@show');




Route::resource('savingaccounts', 'SavingaccountsController');
Route::get('savingaccounts/create/{id}', 'SavingaccountsController@create');
Route::get('member/savingaccounts/{id}', 'SavingaccountsController@memberaccounts');



Route::get('savingtransactions/show/{id}', 'SavingtransactionsController@show');
Route::resource('savingtransactions', 'SavingtransactionsController');
Route::get('savingtransactions/create/{id}', 'SavingtransactionsController@create');
Route::get('savingtransactions/receipt/{id}', 'SavingtransactionsController@receipt');
Route::get('savingtransactions/statement/{id}', 'SavingtransactionsController@statement');

Route::post('savingtransactions/import', 'SavingtransactionsController@import');

//Route::resource('savingpostings', 'SavingpostingsController');



Route::resource('shares', 'SharesController');
Route::post('shares/update/{id}', 'SharesController@update');
Route::get('shares/delete/{id}', 'SharesController@destroy');
Route::get('shares/edit/{id}', 'SharesController@edit');
Route::get('shares/show/{id}', 'SharesController@show');



Route::get('sharetransactions/show/{id}', 'SharetransactionsController@show');
Route::resource('sharetransactions', 'SharetransactionsController');
Route::get('sharetransactions/create/{id}', 'SharetransactionsController@create');





Route::post('license/key', 'OrganizationsController@generate_license_key');
Route::post('license/activate', 'OrganizationsController@activate_license');
Route::get('license/activate/{id}', 'OrganizationsController@activate_license_form');



Route::resource('loanproducts', 'LoanproductsController');
Route::post('loanproducts/update/{id}', 'LoanproductsController@update');
Route::get('loanproducts/delete/{id}', 'LoanproductsController@destroy');
Route::get('loanproducts/edit/{id}', 'LoanproductsController@edit');
Route::get('loanproducts/show/{id}', 'LoanproductsController@show');



Route::resource('loanguarantors', 'LoanguarantorsController');
Route::post('loanguarantors/update/{id}', 'LoanguarantorsController@update');
Route::get('loanguarantors/delete/{id}', 'LoanguarantorsController@destroy');
Route::get('loanguarantors/edit/{id}', 'LoanguarantorsController@edit');
Route::get('loanguarantors/create/{id}', 'LoanguarantorsController@create');
Route::get('loanguarantors/css/{id}', 'LoanguarantorsController@csscreate');

Route::post('loanguarantors/cssupdate/{id}', 'LoanguarantorsController@cssupdate');
Route::get('loanguarantors/cssdelete/{id}', 'LoanguarantorsController@cssdestroy');
Route::get('loanguarantors/cssedit/{id}', 'LoanguarantorsController@cssedit');



Route::resource('loans', 'LoanaccountsController');
Route::get('loans/apply/{id}', 'LoanaccountsController@apply');
Route::post('loans/apply', 'LoanaccountsController@doapply');
Route::post('loans/application', 'LoanaccountsController@doapply2');


Route::get('loantransactions/statement/{id}', 'LoantransactionsController@statement');
Route::get('loantransactions/receipt/{id}', 'LoantransactionsController@receipt');

Route::get('loans/application/{id}', 'LoanaccountsController@apply2');
Route::post('shopapplication', 'LoanaccountsController@shopapplication');

Route::get('loans/edit/{id}', 'LoanaccountsController@edit');
Route::post('loans/update/{id}', 'LoanaccountsController@update');

Route::get('loans/approve/{id}', 'LoanaccountsController@approve');
Route::post('loans/approve/{id}', 'LoanaccountsController@doapprove');


Route::get('loans/reject/{id}', 'LoanaccountsController@reject');
Route::post('loans/reject/{id}', 'LoanaccountsController@doreject');

Route::get('loans/disburse/{id}', 'LoanaccountsController@disburse');
Route::post('loans/disburse/{id}', 'LoanaccountsController@dodisburse');

Route::get('loans/show/{id}', 'LoanaccountsController@show');

Route::post('loans/amend/{id}', 'LoanaccountsController@amend');

Route::get('loans/reject/{id}', 'LoanaccountsController@reject');
Route::post('loans/reject/{id}', 'LoanaccountsController@rejectapplication');


Route::get('loanaccounts/topup/{id}', 'LoanaccountsController@gettopup');
Route::post('loanaccounts/topup/{id}', 'LoanaccountsController@topup');

Route::get('memloans/{id}', 'LoanaccountsController@show2');

Route::resource('loanrepayments', 'LoanrepaymentsController');

Route::get('loanrepayments/create/{id}', 'LoanrepaymentsController@create');
Route::get('loanrepayments/offset/{id}', 'LoanrepaymentsController@offset');
Route::post('loanrepayments/offsetloan', 'LoanrepaymentsController@offsetloan');





Route::get('reports', function(){

    return View::make('reports');
});

Route::get('reports/combined', function(){

    $members = Member::all();

    return View::make('members.combined', compact('members'));
});


Route::get('loanreports', function(){

    $loanproducts = Loanproduct::all();

    return View::make('loanaccounts.reports', compact('loanproducts'));
});


Route::get('savingreports', function(){

    $savingproducts = Savingproduct::all();

    return View::make('savingaccounts.reports', compact('savingproducts'));
});


Route::get('financialreports', function(){

    

    return View::make('pdf.financials.reports');
});



Route::get('reports/listing', 'ReportsController@members');
Route::get('reports/remittance', 'ReportsController@remittance');
Route::get('reports/blank', 'ReportsController@template');
Route::get('reports/loanlisting', 'ReportsController@loanlisting');

Route::get('reports/loanproduct/{id}', 'ReportsController@loanproduct');

Route::get('reports/savinglisting', 'ReportsController@savinglisting');

Route::get('reports/savingproduct/{id}', 'ReportsController@savingproduct');

Route::post('reports/financials', 'ReportsController@financials');



Route::get('portal', function(){

    //$members = DB::table('members')->where('is_active', '=', TRUE)->get();

  $employees = Employee::all();

    return View::make('css.employees', compact('employees'));
});

Route::get('portal/activate/{id}', 'MembersController@activateportal');
Route::get('portal/deactivate/{id}', 'MembersController@deactivateportal');
Route::get('css/reset/{id}', 'MembersController@reset');








/*
* Vendor controllers
*/
Route::resource('vendors', 'VendorsController');
Route::get('vendors/create', 'VendorsController@create');
Route::post('vendors/update/{id}', 'VendorsController@update');
Route::get('vendors/edit/{id}', 'VendorsController@edit');
Route::get('vendors/delete/{id}', 'VendorsController@destroy');
Route::get('vendors/products/{id}', 'VendorsController@products');
Route::get('vendors/orders/{id}', 'VendorsController@orders');



Route::get('savings', function(){

    $mem = Confide::user()->username;

   

    $memb = DB::table('members')->where('membership_no', '=', $mem)->pluck('id');

    $member = Member::find($memb);

    
    

    return View::make('css.savingaccounts', compact('member'));
});


Route::post('loanguarantors', function(){

    
    $mem_id = Input::get('member_id');

        $member = Member::findOrFail($mem_id);

        $loanaccount = Loanaccount::findOrFail(Input::get('loanaccount_id'));


        $guarantor = new Loanguarantor;

        $guarantor->member()->associate($member);
        $guarantor->loanaccount()->associate($loanaccount);
        $guarantor->amount = Input::get('amount');
        $guarantor->save();
        


        return Redirect::to('memloans/'.$loanaccount->id);

});




Route::get('backups', function(){

   
    //$backups = Backup::getRestorationFiles('../app/storage/backup/');

    return View::make('backup');

});


Route::get('backups/create', function(){

    echo '<pre>';

    $instance = Backup::getBackupEngineInstance();

    print_r($instance);

    //Backup::setPath(public_path().'/backups/');

   //Backup::export();
    //$backups = Backup::getRestorationFiles('../app/storage/backup/');

    //return View::make('backup');

});


Route::get('memtransactions/{id}', 'MembersController@savingtransactions');


/*
* This route is for testing how license conversion works. its purely for testing purposes
*/
Route::get('convert', function(){




// get the name of the organization from the database
//$org_id = Confide::user()->organization_id;

$organization = Organization::findorfail(1);



$string =  $organization->name;

echo "Organization: ". $string."<br>";


$organization = new Organization;






$license_code = $organization->encode($string);

echo "License Code: ".$license_code."<br>";


$name2 = $organization->decode($license_code, 7);

echo "Decoded L code: ".$name2."<br>";





$license_key = $organization->license_key_generator($license_code);

echo "License Key: ".$license_key."<br>";

echo "__________________________________________________<br>";

$name4 = $organization->license_key_validator($license_key,$license_code,$string);

echo "Decoded L code: ".$name4."<br>";



});




/* ########################  ERP ROUTES ################################ */

/*Route::resource('clients', 'ClientsController');
Route::get('clients/edit/{id}', 'ClientsController@edit');
Route::post('clients/update/{id}', 'ClientsController@update');
Route::get('clients/delete/{id}', 'ClientsController@destroy');

Route::resource('items', 'ItemsController');
Route::get('items/edit/{id}', 'ItemsController@edit');
Route::post('items/update/{id}', 'ItemsController@update');
Route::get('items/delete/{id}', 'ItemsController@destroy');


Route::resource('paymentmethods', 'PaymentmethodsController');


Route::resource('locations', 'LocationsController');
Route::get('locations/edit/{id}', 'LocationsController@edit');
Route::get('locations/delete/{id}', 'LocationsController@destroy');
Route::post('locations/update/{id}', 'LocationsController@update');



Route::resource('expenses', 'ExpensesController');



Route::resource('payments', 'PaymentsController');*/





/*
Leave Reports
*/

Route::get('leaveReports', function(){

    return View::make('leavereports.leavereports');
});

Route::get('leaveReports/selectApplicationPeriod', 'ReportsController@appperiod');
Route::post('leaveReports/leaveapplications', 'ReportsController@leaveapplications');

Route::get('leaveReports/selectApprovedPeriod', 'ReportsController@approvedperiod');
Route::post('leaveReports/approvedleaves', 'ReportsController@approvedleaves');

Route::get('leaveReports/selectRejectedPeriod', 'ReportsController@rejectedperiod');
Route::post('leaveReports/rejectedleaves', 'ReportsController@rejectedleaves');

Route::get('leaveReports/selectLeave', 'ReportsController@balanceselect');
Route::post('leaveReports/leaveBalances', 'ReportsController@leavebalances');

Route::get('leaveReports/selectLeaveType', 'ReportsController@leaveselect');
Route::post('leaveReports/Employeesonleave', 'ReportsController@employeesleave');

Route::get('leaveReports/selectEmployee', 'ReportsController@employeeselect');
Route::post('leaveReports/IndividualEmployeeLeave', 'ReportsController@individualleave');

Route::get('api/dropdown', function(){
    $id = Input::get('option');
    $bbranch = Bank::find($id)->bankbranch;
    return $bbranch->lists('bank_branch_name', 'id');
});

Route::get('api/branchemployee', function(){
    $bid = Input::get('option');
    $did = Input::get('deptid');
    $employee = array();

    if(($bid == 'All' || $bid == '' || $bid == 0) && ($did == 'All' || $did == '' || $did == 0)){
    $employee = Employee::select('id', DB::raw('CONCAT(personal_file_number," : ",first_name ," ", middle_name, " ", last_name) AS full_name'))
    ->lists('full_name', 'id');
    }else if(($bid == Input::get('option')) && ($did == 'All' || $did == '' || $did == 0)){
    $employee = Employee::select('id', DB::raw('CONCAT(personal_file_number," : ",first_name ," ", middle_name, " ", last_name) AS full_name'))
    ->where('branch_id',$bid)
    ->lists('full_name', 'id');
    }else if(($did == Input::get('deptid') ) && ($bid == Input::get('option')) ){
    $employee = Employee::select('id', DB::raw('CONCAT(personal_file_number," : ",first_name ," ", middle_name, " ", last_name) AS full_name'))
    ->where('branch_id',$bid)
    ->where('department_id',$did)
    ->lists('full_name', 'id');
    }

    return $employee;
});

Route::get('api/deptemployee', function(){
    $did = Input::get('option');
    $bid = Input::get('bid');
    $employee = array();

    if(($did == 'All' || $did == '' || $did == 0) && ($bid == 'All' || $bid == '' || $bid == 0)){
    $employee = Employee::select('id', DB::raw('CONCAT(personal_file_number," : ",first_name ," ", middle_name, " ", last_name) AS full_name'))
    ->lists('full_name', 'id');
    }else if(($did == Input::get('option')) && ($bid == 'All' || $bid == '' || $bid == 0)){
    $employee = Employee::select('id', DB::raw('CONCAT(personal_file_number," : ",first_name ," ", middle_name, " ", last_name) AS full_name'))
    ->where('department_id',$did)
    ->lists('full_name', 'id');
    }else if(($did == Input::get('option')) && ($bid == Input::get('bid')) ){
    $employee = Employee::select('id', DB::raw('CONCAT(personal_file_number," : ",first_name ," ", middle_name, " ", last_name) AS full_name'))
    ->where('branch_id',$bid)
    ->where('department_id',$did)
    ->lists('full_name', 'id');
    }

    return $employee;
});

Route::get('api/getDays', function(){
    $id = Input::get('employee');
    $lid = Input::get('leave');
    $d = Input::get('option');
    $total = 0;
    $balance = 0;
 
    $leavedays = DB::table('leavetypes')
                       ->where('id',$lid)
                       ->first();
    

    $leaveapplications = DB::table('leaveapplications')
                       ->join('leavetypes','leaveapplications.leavetype_id','=','leavetypes.id')
                       ->where('employee_id',$id)
                       ->where('leavetype_id',$lid)
                       ->where('date_approved','<>','')
                       ->get();
    foreach ($leaveapplications as $leaveapplication) {
      $total+=Leaveapplication::getLeaveDays($leaveapplication->applied_start_date, $leaveapplication->applied_end_date);
    }
    $balance = $leavedays->days-$total-$d;
    return $balance;
});

Route::get('api/score', function(){
    $id = Input::get('option');
    $rate = Appraisalquestion::find($id);
    return $rate->rate;
});

Route::get('api/pay', function(){
    $id = Input::get('option');
    $employee = Employee::find($id);
    return number_format($employee->basic_pay,2);
});

Route::get('empedit/{id}', function($id){

    $employee = Employee::find($id);
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
    return View::make('employees.cssedit', compact('count','subordinates','supervisor','currency','countk','countd','docs','kins','citizenships','contract','branches','educations','departments','etypes','jgroups','banks','bbranches','employee'));
  
});



Route::get('css/payslips', function(){

  $employeeid = DB::table('employee')->where('personal_file_number', '=', Confide::user()->username)->pluck('id');

  $employee = Employee::findorfail($employeeid);

  return View::make('css.payslip', compact('employee'));
});


Route::get('css/leave', function(){

  $employeeid = DB::table('employee')->where('personal_file_number', '=', Confide::user()->username)->pluck('id');


  $employee = Employee::findorfail($employeeid);

   $leaveapplications = DB::table('leaveapplications')->where('employee_id', '=', $employee->id)->get();

  return View::make('css.leave', compact('employee', 'leaveapplications'));
});


Route::get('css/leaveapply', function(){

  $employeeid = DB::table('employee')->where('personal_file_number', '=', Confide::user()->username)->pluck('id');

  $employee = Employee::findorfail($employeeid);
  $leavetypes = Leavetype::all();

  return View::make('css.leaveapply', compact('employee', 'leavetypes'));
});


Route::get('css/balances', function(){

  $employeeid = DB::table('employee')->where('personal_file_number', '=', Confide::user()->username)->pluck('id');

  $employee = Employee::findorfail($employeeid);
  $leavetypes = Leavetype::all();

  return View::make('css.balances', compact('employee', 'leavetypes'));
});




/*
*overtimes
*/

Route::resource('overtimes', 'OvertimesController');
Route::get('overtimes/edit/{id}', 'OvertimesController@edit');
Route::post('overtimes/update/{id}', 'OvertimesController@update');
Route::get('overtimes/delete/{id}', 'OvertimesController@destroy');
Route::get('overtimes/view/{id}', 'OvertimesController@view');

/*
* employee documents routes
*/

Route::resource('documents', 'DocumentsController');
Route::post('documents/update/{id}', 'DocumentsController@update');
Route::get('documents/delete/{id}', 'DocumentsController@destroy');
Route::get('documents/edit/{id}', 'DocumentsController@edit');
Route::get('documents/download/{id}', 'DocumentsController@getDownload');
Route::get('documents/create/{id}', 'DocumentsController@create');
Route::post('createDoc', 'DocumentsController@serializecheck');

Route::resource('NextOfKins', 'NextOfKinsController');
Route::post('NextOfKins/update/{id}', 'NextOfKinsController@update');
Route::get('NextOfKins/delete/{id}', 'NextOfKinsController@destroy');
Route::get('NextOfKins/edit/{id}', 'NextOfKinsController@edit');
Route::get('NextOfKins/view/{id}', 'NextOfKinsController@view');
Route::get('NextOfKins/create/{id}', 'NextOfKinsController@create');
Route::post('createKin', 'NextOfKinsController@serializecheck');

Route::resource('Appraisals', 'AppraisalsController');
Route::post('Appraisals/update/{id}', 'AppraisalsController@update');
Route::get('Appraisals/delete/{id}', 'AppraisalsController@destroy');
Route::get('Appraisals/edit/{id}', 'AppraisalsController@edit');
Route::get('Appraisals/view/{id}', 'AppraisalsController@view');
Route::post('createQuestion', 'AppraisalsController@createquestion');

Route::resource('Properties', 'PropertiesController');
Route::post('Properties/update/{id}', 'PropertiesController@update');
Route::get('Properties/delete/{id}', 'PropertiesController@destroy');
Route::get('Properties/edit/{id}', 'PropertiesController@edit');
Route::get('Properties/view/{id}', 'PropertiesController@view');

Route::resource('AppraisalSettings', 'AppraisalSettingsController');
Route::post('AppraisalSettings/update/{id}', 'AppraisalSettingsController@update');
Route::get('AppraisalSettings/delete/{id}', 'AppraisalSettingsController@destroy');
Route::get('AppraisalSettings/edit/{id}', 'AppraisalSettingsController@edit');
Route::post('createCategory', 'AppraisalSettingsController@createcategory');

Route::resource('appraisalcategories', 'AppraisalCategoryController');
Route::post('appraisalcategories/update/{id}', 'AppraisalCategoryController@update');
Route::get('appraisalcategories/delete/{id}', 'AppraisalCategoryController@destroy');
Route::get('appraisalcategories/edit/{id}', 'AppraisalCategoryController@edit');


Route::resource('itemcategories', 'ItemcategoriesController');
Route::get('itemcategories/edit/{id}', 'ItemcategoriesController@edit');
Route::get('itemcategories/delete/{id}', 'ItemcategoriesController@destroy');
Route::post('itemcategories/update/{id}', 'ItemcategoriesController@update');


Route::get('erpmigrate', function(){

  return View::make('erpmigrate');

});


Route::post('import/categories', function(){

  
  if(Input::hasFile('category')){

      $destination = public_path().'/migrations/';

      $filename = str_random(12);

      $ext = Input::file('category')->getClientOriginalExtension();
      $file = $filename.'.'.$ext;



      
      
     
      Input::file('category')->move($destination, $file);


  


    Excel::selectSheetsByIndex(0)->load(public_path().'/migrations/'.$file, function($reader){

          $results = $reader->get();    
  
    foreach ($results as $result) {



      $category = new Itemcategory;

      $employee->personal_file_number = $result->employment_number;
      
      $employee->first_name = $result->first_name;
      $employee->last_name = $result->surname;
      $employee->middle_name = $result->other_names;
      $employee->identity_number = $result->id_number;
      $employee->pin = $result->kra_pin;
      $employee->social_security_number = $result->nssf_number;
      $employee->hospital_insurance_number = $result->nhif_number;
      $employee->email_office = $result->email_address;
      $employee->basic_pay = $result->basic_pay;
      $employee->save();
      
    }
    

    

  });



      
    }



  return Redirect::back()->with('notice', 'Employees have been succeffully imported');
  

});

Route::get('reports/AllowanceExcel', 'ReportsController@excelAll');

Route::get('itax/download', 'ReportsController@getDownload');

Route::resource('reminderview', 'RemindersController');
Route::get('reminderdoc/indexdoc', 'RemindersController@indexdoc');
Route::post('reminderview/update/{id}', 'RemindersController@update');
Route::get('reminderdoc/download/{id}', 'RemindersController@getDownload');
Route::get('reminderview/delete/{id}', 'RemindersController@destroy');
Route::get('reminderview/edit/{id}', 'RemindersController@edit');
Route::get('reminderview/show/{id}', 'RemindersController@show');

Route::get('api/ded', function(){
    $id = Input::get('option');
    $employee = Employee::find($id);
    return number_format($employee->basic_pay,2);
});



Route::resource('customers', 'CustomersController');
Route::get('customers/edit/{id}', 'CustomersController@edit');
Route::post('customers/update/{id}', 'CustomersController@update');
Route::get('customers/delete/{id}', 'CustomersController@destroy');

Route::resource('suppliers', 'SuppliersController');
Route::get('suppliers/edit/{id}', 'SuppliersController@edit');
Route::post('suppliers/update/{id}', 'SuppliersController@update');
Route::get('suppliers/delete/{id}', 'SuppliersController@destroy');

Route::resource('items', 'ItemsController');
Route::get('items/edit/{id}', 'ItemsController@edit');
Route::post('items/update/{id}', 'ItemsController@update');
Route::get('items/delete/{id}', 'ItemsController@destroy');

Route::resource('expenses', 'ExpensesController');
Route::get('expenses/edit/{id}', 'ExpensesController@edit');
Route::post('expenses/update/{id}', 'ExpensesController@update');
Route::get('expenses/delete/{id}', 'ExpensesController@destroy');

Route::resource('paymentmethods', 'PaymentmethodsController');
Route::get('paymentmethods/edit/{id}', 'PaymentmethodsController@edit');
Route::post('paymentmethods/update/{id}', 'PaymentmethodsController@update');
Route::get('paymentmethods/delete/{id}', 'PaymentmethodsController@destroy');

Route::resource('payments', 'PaymentsController');
Route::get('payments/edit/{id}', 'PaymentsController@edit');
Route::post('payments/update/{id}', 'PaymentsController@update');
Route::get('payments/delete/{id}', 'PaymentsController@destroy');

/*
*##########################ERP REPORTS#######################################
*/

Route::get('erpReports', function(){

    return View::make('erpreports.erpReports');
});

Route::post('erpReports/clients', 'ErpReportsController@clients');
Route::get('erpReports/selectClientsPeriod', 'ErpReportsController@selectClientsPeriod');


/*Route::post('erpReports/suppliers', 'ErpReportsController@suppliers');
Route::get('erpReports/selectSuppliersPeriod', 'ErpReportsController@selectSuppliersPeriod');*/



Route::post('erpReports/items', 'ErpReportsController@items');
Route::get('erpReports/selectItemsPeriod', 'ErpReportsController@selectItemsPeriod');

Route::post('erpReports/expenses', 'ErpReportsController@expenses');
Route::get('erpReports/selectExpensesPeriod', 'ErpReportsController@selectExpensesPeriod');


Route::get('erpReports/paymentmethods', 'ErpReportsController@paymentmethods');

Route::get('erpReports/payments', 'ErpReportsController@payments');
Route::get('erpReports/selectPaymentsPeriod', 'ErpReportsController@selectPaymentsPeriod');

Route::get('erpReports/invoice/{id}', 'ErpReportsController@invoice');


Route::post('erpReports/sales', 'ErpReportsController@sales');
Route::get('erpReports/sales_summary', 'ErpReportsController@sales_Summary');
Route::get('erpReports/selectSalesPeriod', 'ErpReportsController@selectSalesPeriod');


Route::post('erpReports/purchases', 'ErpReportsController@purchases');
Route::get('erpReports/selectPurchasesPeriod', 'ErpReportsController@selectPurchasesPeriod');



Route::get('erpReports/quotation/{id}', 'ErpReportsController@quotation');
Route::get('erpReports/pricelist', 'ErpReportsController@pricelist');
Route::get('erpReports/receipt/{id}', 'ErpReportsController@receipt');
Route::get('erpReports/PurchaseOrder/{id}', 'ErpReportsController@PurchaseOrder');

Route::get('erpReports/locations', 'ErpReportsController@locations');

Route::post('erpReports/stocks', 'ErpReportsController@stock');
Route::get('erpReports/selectStockPeriod', 'ErpReportsController@selectStockPeriod');


Route::get('erpReports/accounts', 'ErpReportsController@accounts');



Route::resource('taxes', 'TaxController');
Route::post('taxes/update/{id}', 'TaxController@update');
Route::get('taxes/delete/{id}', 'TaxController@destroy');
Route::get('taxes/edit/{id}', 'TaxController@edit');


/*
*#################################################################
*/

Route::get('erpmgmt', function(){

  return View::make('erpmgmt');

});

/*
* products controllers
*/
Route::resource('products', 'ProductsController');
Route::post('products/update/{id}', 'ProductsController@update');
Route::get('products/edit/{id}', 'ProductsController@edit');
Route::get('products/create', 'ProductsController@create');
Route::get('products/delete/{id}', 'ProductsController@destroy');
Route::get('products/orders/{id}', 'ProductsController@orders');
Route::get('shop', 'ProductsController@shop');

/*
* orders controllers
*/
Route::resource('orders', 'OrdersController');
Route::post('orders/update/{id}', 'OrdersControler@update');
Route::get('orders/edit/{id}', 'OrdersControler@edit');
Route::get('orders/delete/{id}', 'OrdersControler@destroy');




/*
* purchase orders controllers
*/
Route::resource('purchases', 'PurchasesController');
Route::post('purchases/update/{id}', 'PurchasesController@update');
Route::get('purchases/edit/{id}', 'PurchasesController@edit');
Route::get('purchases/delete/{id}', 'PurchasesController@destroy');


/*
* quotation controllers
*/
Route::resource('quotations', 'QuotationsController');
Route::post('quotations/update/{id}', 'QuotationsController@update');
Route::get('quotations/edit/{id}', 'QuotationsController@edit');
Route::get('quotations/delete/{id}', 'QuotationsController@destroy');

/* ########################  ERP ROUTES ################################ */

/* 
* items routes here 
*/
Route::resource('items', 'ItemsController');


/*
* client routes come here
*/

Route::resource('clients', 'ClientsController');


Route::resource('paymentmethods', 'PaymentmethodsController');


Route::resource('locations', 'LocationsController');
Route::get('locations/edit/{id}', 'LocationsController@edit');
Route::get('locations/delete/{id}', 'LocationsController@destroy');
Route::post('locations/update/{id}', 'LocationsController@update');


Route::resource('expenses', 'ExpensesController');

Route::resource('erporders', 'ErpordersController');
Route::resource('erppurchases', 'ErppurchasesController');
Route::resource('erpquotations', 'ErpquotationsController');


Route::resource('erporderitems', 'ErporderitemsController');
Route::resource('erppurchaseitems', 'ErppurchaseitemsController');
Route::resource('erpquotationitems', 'ErpquotationitemsController');

Route::resource('payments', 'PaymentsController');


// Route::get('erppurchases/payment/{id}',    'ErppurchasesController@payment');
// Route::post('erppurchases/payment/{id}',    'ErppurchasesController@recordpayment');





Route::get('salesorders', function(){

  $orders = Erporder::all();
  $items = Item::all();
  $locations = Location::all();

  return View::make('erporders.index', compact('items', 'locations', 'orders'));
});



Route::get('purchaseorders', function(){

  $purchases = Erporder::all();
  $items = Item::all();
  $locations = Location::all();


  return View::make('erppurchases.index', compact('items', 'locations', 'purchases'));
});



Route::get('quotationorders', function(){

  $quotations = Erporder::all();
  $items = Item::all();
  $locations = Location::all();
  $items = Item::all();
  $locations = Location::all();

  return View::make('erpquotations.index', compact('items', 'locations', 'quotations'));
});


Route::get('salesorders/create', function(){

  $count = DB::table('erporders')->count();
  $order_number = date("Y/m/d/").str_pad($count+1, 4, "0", STR_PAD_LEFT);
  $items = Item::all();
  $locations = Location::all();

  $clients = Client::all();

  return View::make('erporders.create', compact('items', 'locations', 'order_number', 'clients'));
});


Route::get('purchaseorders/create', function(){

  $count = DB::table('erporders')->count();
  $order_number = date("Y/m/d/").str_pad($count+1, 4, "0", STR_PAD_LEFT);
  $items = Item::all();
  $locations = Location::all();

  $clients = Client::all();

  return View::make('erppurchases.create', compact('items', 'locations', 'order_number', 'clients'));
});


Route::get('quotationorders/create', function(){

  $count = DB::table('erporders')->count();
  $order_number = date("Y/m/d/").str_pad($count+1, 4, "0", STR_PAD_LEFT);;
  $items = Item::all();
  $locations = Location::all();

  $clients = Client::all();

  return View::make('erpquotations.create', compact('items', 'locations', 'order_number', 'clients'));
});











Route::post('erporders/create', function(){

  $data = Input::all();

  $client = Client::findOrFail(array_get($data, 'client'));

/*
  $erporder = array(
    'order_number' => array_get($data, 'order_number'), 
    'client' => $client,
    'date' => array_get($data, 'date')

    );
  */

  Session::put( 'erporder', array(
    'order_number' => array_get($data, 'order_number'), 
    'client' => $client,
    'date' => array_get($data, 'date')

    )
    );
  Session::put('orderitems', []);

  $orderitems =Session::get('orderitems');

 /*
  $erporder = new Erporder;

  $erporder->date = date('Y-m-d', strtotime(array_get($data, 'date')));
  $erporder->order_number = array_get($data, 'order_number');
  $erporder->client()->associate($client);
  $erporder->payment_type = array_get($data, 'payment_type');
  $erporder->type = 'sales';
  $erporder->save();

  */

  $items = Item::all();
  $locations = Location::all();
  $taxes = Tax::all();

  return View::make('erporders.orderitems', compact('erporder', 'items', 'locations', 'taxes','orderitems'));

});



Route::post('erppurchases/create', function(){

  $data = Input::all();

  $client = Client::findOrFail(array_get($data, 'client'));

/*
  $erporder = array(
    'order_number' => array_get($data, 'order_number'), 
    'client' => $client,
    'date' => array_get($data, 'date')

    );
  */

  Session::put( 'erporder', array(
    'order_number' => array_get($data, 'order_number'), 
    'client' => $client,
    'date' => array_get($data, 'date')

    )
    );
  Session::put('purchaseitems', []);

  $orderitems =Session::get('purchaseitems');

 /*
  $erporder = new Erporder;

  $erporder->date = date('Y-m-d', strtotime(array_get($data, 'date')));
  $erporder->order_number = array_get($data, 'order_number');
  $erporder->client()->associate($client);
  $erporder->payment_type = array_get($data, 'payment_type');
  $erporder->type = 'sales';
  $erporder->save();

  */

  $items = Item::all();
  $locations = Location::all();
  $taxes = Tax::all();

  return View::make('erppurchases.purchaseitems', compact('items', 'locations','taxes','orderitems'));

});





Route::post('erpquotations/create', function(){

  $data = Input::all();

  $client = Client::findOrFail(array_get($data, 'client'));

/*
  $erporder = array(
    'order_number' => array_get($data, 'order_number'), 
    'client' => $client,
    'date' => array_get($data, 'date')

    );
  */

  Session::put( 'erporder', array(
    'order_number' => array_get($data, 'order_number'), 
    'client' => $client,
    'date' => array_get($data, 'date')

    )
    );
  Session::put('quotationitems', []);

  $orderitems =Session::get('quotationitems');

 /*
  $erporder = new Erporder;

  $erporder->date = date('Y-m-d', strtotime(array_get($data, 'date')));
  $erporder->order_number = array_get($data, 'order_number');
  $erporder->client()->associate($client);
  $erporder->payment_type = array_get($data, 'payment_type');
  $erporder->type = 'sales';
  $erporder->save();

  */

  $items = Item::all();
  $locations = Location::all();
  $taxes = Tax::all();

  return View::make('erpquotations.quotationitems', compact('items', 'locations','taxes','orderitems'));

});







Route::post('orderitems/create', function(){

  $data = Input::all();

  $item = Item::findOrFail(array_get($data, 'item'));

  $item_name = $item->name;
  $price = $item->selling_price;
  $quantity = Input::get('quantity');
  $duration = Input::get('duration');
  $item_id = $item->id;
  $location = Input::get('location');

   Session::push('orderitems', [
      'itemid' => $item_id,
      'item' => $item_name,
      'price' => $price,
      'quantity' => $quantity,
      'duration' => $duration,
      'location' =>$location
    ]);



  $orderitems = Session::get('orderitems');

   $items = Item::all();
  $locations = Location::all();
  $taxes = Tax::all();

  return View::make('erporders.orderitems', compact('items', 'locations', 'taxes','orderitems'));

});






Route::post('purchaseitems/create', function(){

  $data = Input::all();

  $item = Item::findOrFail(array_get($data, 'item'));

  $item_name = $item->name;
  $price = $item->purchase_price;
  $quantity = Input::get('quantity');
  $duration = Input::get('duration');
  $item_id = $item->id;

   Session::push('purchaseitems', [
      'itemid' => $item_id,
      'item' => $item_name,
      'price' => $price,
      'quantity' => $quantity,
      'duration' => $duration
    ]);



  $orderitems = Session::get('purchaseitems');

   $items = Item::all();
  $locations = Location::all();
  $taxes = Tax::all();

  return View::make('erppurchases.purchaseitems', compact('items', 'locations', 'taxes','orderitems'));

});






Route::post('quotationitems/create', function(){

  $data = Input::all();

  $item = Item::findOrFail(array_get($data, 'item'));

  $item_name = $item->name;
  $price = $item->selling_price;
  $quantity = Input::get('quantity');
  $duration = Input::get('duration');
  $item_id = $item->id;

   Session::push('quotationitems', [
      'itemid' => $item_id,
      'item' => $item_name,
      'price' => $price,
      'quantity' => $quantity,
      'duration' => $duration
    ]);



  $orderitems = Session::get('quotationitems');

   $items = Item::all();
  $locations = Location::all();
  $taxes = Tax::all();

  return View::make('erpquotations.quotationitems', compact('items', 'locations', 'taxes','orderitems'));

});









Route::get('orderitems/remove/{id}', function($id){

 // Session::forget('orderitems', $id);

  

  $orderitems = Session::get('orderitems');


  foreach ($orderitems as $orderitem) {
       if($orderitem['itemid'] == $id){
          Session::forget($orderitem);

          
        
  //Session::forget('orderitems', $id);
       }
     }

     $orderitems = Session::get('orderitems');

  $items = Item::all();
  $locations = Location::all();
  $taxes = Tax::all();

  return View::make('erporders.orderitems', compact('items', 'locations', 'taxes', 'orderitems'));

});


Route::get('purchaseitems/remove/{id}', function($id){

  Session::forget('orderitems', $id);

  $orderitems = Session::get('orderitems');

  $items = Item::all();
  $locations = Location::all();
  $taxes = Tax::all();

  return View::make('erporders.orderitems', compact('items', 'locations', 'taxes', 'orderitems'));

});



Route::get('quotationitems/remove/{id}', function($id){

  Session::forget('orderitems', $id);

  $orderitems = Session::get('orderitems');

  $items = Item::all();
  $locations = Location::all();
  $taxes = Tax::all();

  return View::make('erporders.orderitems', compact('items', 'locations', 'taxes', 'orderitems'));

});







Route::resource('stocks', 'StocksController');
Route::resource('erporders', 'ErporderssController');






Route::post('erporder/commit', function(){

  $erporder = Session::get('erporder');

  $erporderitems = Session::get('orderitems');
  
   $total = Input::all();

 // $client = Client: :findorfail(array_get($erporder, 'client'));

 // print_r($total);


  $order = new Erporder;
  $order->order_number = array_get($erporder, 'order_number');
  $order->client()->associate(array_get($erporder, 'client'));
  $order->date = date('Y-m-d', strtotime(array_get($erporder, 'date')));
  $order->status = 'new';
  $order->discount_amount = array_get($total, 'discount');
  $order->type = 'sales';  
  $order->save();
  

  foreach($erporderitems as $item){


    $itm = Item::findOrFail($item['itemid']);

    $ord = Erporder::findOrFail($order->id);


    
    $location_id = $item['location'];

     $location = Location::find($location_id);    
    
    $date = date('Y-m-d', strtotime(array_get($erporder, 'date')));

    $orderitem = new Erporderitem;
    $orderitem->erporder()->associate($ord);
    $orderitem->item()->associate($itm);
    $orderitem->price = $item['price'];
    $orderitem->quantity = $item['quantity'];
    $orderitem->duration = $item['duration'];
    $orderitem->save();




   Stock::removeStock($itm, $location, $item['quantity'], $date);



  }
 

  $tax = Input::get('tax');
  $rate = Input::get('rate');





  for($i=0; $i < count($rate);  $i++){

    $txOrder = new TaxOrder;

    $txOrder->tax_id = $rate[$i];
    $txOrder->order_number = array_get($erporder, 'order_number');
    $txOrder->amount = $tax[$i];
    $txOrder->save();
    
  }
  
 
//Session::flush('orderitems');
//Session::flush('erporder');  
 
    

return Redirect::to('salesorders')->withFlashMessage('Order Successfully Placed!');



});







Route::get('erppurchase/commit', function(){

  //$orderitems = Session::get('erppurchase');

  $erporder = Session::get('erporder');

  $orderitems = Session::get('purchaseitems');
  
   $total = Input::all();

 // $client = Client: :findorfail(array_get($erporder, 'client'));

 // print_r($total);


  $order = new Erporder;
  $order->order_number = array_get($erporder, 'order_number');
  $order->client()->associate(array_get($erporder, 'client'));
  $order->date = date('Y-m-d', strtotime(array_get($erporder, 'date')));
  $order->status = 'new';
  //$order->discount_amount = array_get($total, 'discount');
  $order->type = 'purchases';
  $order->save();
  

  foreach($orderitems as $item){


    $itm = Item::findOrFail($item['itemid']);

    $ord = Erporder::findOrFail($order->id);

    $orderitem = new Erporderitem;
    $orderitem->erporder()->associate($ord);
    $orderitem->item()->associate($itm);
    $orderitem->price = $item['price'];
    $orderitem->quantity = $item['quantity'];
    //s$orderitem->duration = $item['duration'];
    $orderitem->save();
  }
  
 
//Session::flush('orderitems');
//Session::flush('erporder');
return Redirect::to('purchaseorders');



});


Route::post('erpquotation/commit', function(){

  $erporder = Session::get('erporder');

  $erporderitems = Session::get('quotationitems');
  
   $total = Input::all();

 // $client = Client: :findorfail(array_get($erporder, 'client'));

 // print_r($total);


  $order = new Erporder;
  $order->order_number = array_get($erporder, 'order_number');
  $order->client()->associate(array_get($erporder, 'client'));
  $order->date = date('Y-m-d', strtotime(array_get($erporder, 'date')));
  $order->status = 'new';
  $order->discount_amount = array_get($total, 'discount');
  $order->type = 'quotations';  
  $order->save();
  

  foreach($erporderitems as $item){


    $itm = Item::findOrFail($item['itemid']);

    $ord = Erporder::findOrFail($order->id);


    
    //$location_id = $item['location'];

     //$location = Location::find($location_id);    
    
    $date = date('Y-m-d', strtotime(array_get($erporder, 'date')));

    $orderitem = new Erporderitem;
    $orderitem->erporder()->associate($ord);
    $orderitem->item()->associate($itm);
    $orderitem->price = $item['price'];
    $orderitem->quantity = $item['quantity'];
    $orderitem->duration = $item['duration'];
    $orderitem->save();




     }
 

  $tax = Input::get('tax');
  $rate = Input::get('rate');





  for($i=0; $i < count($rate);  $i++){

    $txOrder = new TaxOrder;

    $txOrder->tax_id = $rate[$i];
    $txOrder->order_number = array_get($erporder, 'order_number');
    $txOrder->amount = $tax[$i];
    $txOrder->save();
    
  }
  
 
//Session::flush('orderitems');
//Session::flush('erporder');  
 
    

return Redirect::to('quotationorders');



});










Route::get('erporders/cancel/{id}', function($id){

  $order = Erporder::findorfail($id);



  $order->status = 'cancelled';
  $order->update();

  return Redirect::to('salesorders');
  
});


Route::get('erporders/delivered/{id}', function($id){

  $order = Erporder::findorfail($id);



  $order->status = 'delivered';
  $order->update();

  return Redirect::to('salesorders');
  
});




Route::get('erppurchases/cancel/{id}', function($id){

  $order = Erporder::findorfail($id);



  $order->status = 'cancelled';
  $order->update();

  return Redirect::to('purchaseorders');
  
});



Route::get('erppurchases/delivered/{id}', function($id){

  $order = Erporder::findorfail($id);



  $order->status = 'delivered';
  $order->update();

  return Redirect::to('purchaseorders');
  
});




Route::get('erpquotations/cancel/{id}', function($id){

  $order = Erporder::findorfail($id);



  $order->status = 'cancelled';
  $order->update();

  return Redirect::to('quotationorders');
  
});




Route::get('erporders/show/{id}', function($id){

  $order = Erporder::findorfail($id);

  return View::make('erporders.show', compact('order'));
  
});



Route::get('erppurchases/show/{id}', function($id){

  $order = Erporder::findorfail($id);

  return View::make('erppurchases.show', compact('order'));
  
});


Route::get('erppurchases/payment/{id}', function($id){

  $payments = Payment::all();

  $purchase = Erporder::findorfail($id);    

  $account = Accounts::all();

  return View::make('erppurchases.payment', compact('payments', 'purchase', 'account'));
  
});



Route::get('erpquotations/show/{id}', function($id){


  $order = Erporder::findorfail($id);

  return View::make('erpquotations.show', compact('order'));
  
});

Route::get('api/getrate', function(){
    $id = Input::get('option');
    $tax = Tax::find($id);
    return $tax->rate;
});

Route::get('api/getmax', function(){
    $id = Input::get('option');
    $stock_in = DB::table('stocks')
         ->join('items', 'stocks.item_id', '=', 'items.id')
         ->where('item_id',$id)
         ->sum('quantity_in');

    $stock_out = DB::table('stocks')
         ->join('items', 'stocks.item_id', '=', 'items.id')
         ->where('item_id',$id)
         ->sum('quantity_out');
    return $stock_in-$stock_out;
});


Route::get('api/total', function(){
    $id = Input::get('option');
    
    $client = Client::find($id);
    $order = 0;
    

          if($client->type == 'Customer'){
           $order = DB::table('erporders')
           ->join('erporderitems','erporders.id','=','erporderitems.erporder_id')
           ->join('clients','erporders.client_id','=','clients.id')
           ->where('clients.id',$id) ->selectRaw('SUM((price * quantity)) as total')
           ->pluck('total');

           $tax = DB::table('erporders')
           ->join('clients','erporders.client_id','=','clients.id')
           ->join('tax_orders','erporders.order_number','=','tax_orders.order_number')
           ->where('clients.id',$id) ->selectRaw('SUM(COALESCE(amount,0))as total')
           ->pluck('total');

           $order = $order + $tax;
           }
            else{
           $order = DB::table('erporders')
           ->join('erporderitems','erporders.id','=','erporderitems.erporder_id')
           ->join('clients','erporders.client_id','=','clients.id')           
           ->where('clients.id',$id) ->selectRaw('SUM((price * quantity))as total')
           ->pluck('total');
         }

    $paid = DB::table('clients')
           ->join('payments','clients.id','=','payments.client_id')
           ->where('clients.id',$id) ->selectRaw('COALESCE(SUM(amount_paid),0) as due')
           ->pluck('due');

    

    return number_format($order-$paid, 2);
});







Route::get('api/getname', function(){
    $id = Input::get('option');
    $adm = Admission::find($id);
    return $adm->surname.' '.$adm->firstname.' '.$adm->other_names;
});


Route::get('api/getcontact', function(){
    $id = Input::get('option');
    $driver_name = Employee::find($id);
    return $driver_name->telephone_mobile;
});


Route::get('morgue', function(){

  return View::make('morgue');
});


Route::resource('enquiries', 'EnquiriesController');
Route::get('enquiries/edit/{id}', 'EnquiriesController@edit');
Route::get('enquiries/delete/{id}', 'EnquiriesController@destroy');
Route::post('enquiries/update/{id}', 'EnquiriesController@update');
Route::get('enquiries/show/{id}', 'EnquiriesController@show');




Route::resource('resolutions', 'ResolutionsController');
Route::get('resolutions/create/{id}', 'ResolutionsController@create');
Route::get('resolutions/edit/{id}', 'ResolutionsController@edit');
Route::get('resolutions/delete/{id}', 'ResolutionsController@destroy');
Route::post('resolutions/update/{id}', 'ResolutionsController@update');





/* ########################  UMASH ROUTES ################################ */

Route::resource('cars', 'CarsController');
Route::get('cars/edit/{id}', 'CarsController@edit');
Route::get('cars/delete/{id}', 'CarsController@destroy');
Route::post('cars/update/{id}', 'CarsController@update');
Route::get('cars/show/{id}', 'CarsController@show');


Route::resource('bookings', 'BookingsController');
Route::get('bookings/edit/{id}', 'BookingsController@edit');
Route::get('bookings/delete/{id}', 'BookingsController@destroy');
Route::post('bookings/update/{id}', 'BookingsController@update');
Route::get('bookings/cancel/{id}', 'BookingsController@cancel');

Route::resource('roombookings', 'RoomBookingsController');
Route::get('roombookings/edit/{id}', 'RoomBookingsController@edit');
Route::get('roombookings/delete/{id}', 'RoomBookingsController@destroy');
Route::post('roombookings/update/{id}', 'RoomBookingsController@update');
Route::get('roombookings/cancel/{id}', 'RoomBookingsController@cancel');



Route::resource('admissions', 'AdmissionsController');
Route::get('admissions/edit/{id}', 'AdmissionsController@edit');
Route::get('admissions/delete/{id}', 'AdmissionsController@destroy');
Route::post('admissions/update/{id}', 'AdmissionsController@update');
Route::get('admissions/release/{id}', 'AdmissionsController@release');
Route::get('admissions/show/{id}', 'AdmissionsController@show');
Route::get('admissions/download1/{id}', 'AdmissionsController@getDownload1');
Route::get('admissions/download2/{id}', 'AdmissionsController@getDownload2');
Route::get('admissions/download3/{id}', 'AdmissionsController@getDownload3');
Route::get('admissions/download4/{id}', 'AdmissionsController@getDownload4');
Route::get('admissions/download5/{id}', 'AdmissionsController@getDownload5');


Route::resource('clothes', 'ClothesController');
Route::get('clothes/edit/{id}', 'ClothesController@edit');
Route::get('clothes/delete/{id}', 'ClothesController@destroy');
Route::post('clothes/update/{id}', 'ClothesController@update');
Route::get('clothes/show/{id}', 'ClothesController@show');
Route::get('umashReports/clothe/{id}', 'UmashReportsController@clothe');



Route::resource('drivers', 'DriversController');
Route::get('drivers/edit/{id}', 'DriversController@edit');
Route::get('drivers/delete/{id}', 'DriversController@destroy');
Route::post('drivers/update/{id}', 'DriversController@update');
Route::get('drivers/show/{id}', 'DriversController@show');


Route::resource('jinsurance', 'JinsuranceController');
Route::get('jinsurance/edit/{id}', 'JinsuranceController@edit');
Route::get('jinsurance/delete/{id}', 'JinsuranceController@destroy');
Route::post('jinsurance/update/{id}', 'JinsuranceController@update');
/*Route::post('jinsurance/show/{id}', 'JinsuranceController@show');*/
Route::get('umashReports/jinsurance/{id}', 'UmashReportsController@jinsurance');



Route::resource('rooms', 'RoomsController');
Route::get('rooms/edit/{id}', 'RoomsController@edit');
Route::get('rooms/delete/{id}', 'RoomsController@destroy');
Route::post('rooms/update/{id}', 'RoomsController@update');
Route::get('rooms/show/{id}', 'RoomsController@show');







Route::get('jinsurance/show/{id}', function($id){

  $jinsurance = Jinsurance::findOrFail($id);

    return View::make('jinsurance.show', compact('jinsurance'));
  
});





Route::get('umashReports/admission/{id}', function($id){

        $organization = Organization::find(1);

        $admission = Admission::findOrFail($id);
        

        $pdf = PDF::loadView('umashreports.admission', compact('organization','admission'))->setPaper('a4')->setOrientation('potrait');
    
        return $pdf->stream('Admission Details.pdf');



});


Route::get('umashReports', function(){

    return View::make('umashreports.umashreports');
});

Route::post('umashReports/enquiries', 'UmashReportsController@enquiries');
Route::post('umashReports/admissions', 'UmashReportsController@admissions');
Route::post('umashReports/bookings', 'UmashReportsController@bookings');
Route::post('umashReports/cars', 'UmashReportsController@cars');
Route::post('umashReports/drivers', 'UmashReportsController@drivers');
Route::post('umashReports/jinsurance', 'UmashReportsController@jinsurances');
Route::post('umashReports/rooms', 'UmashReportsController@rooms');

Route::get('umashreports/selectAdmissionPeriod', 'UmashReportsController@selectAdmissionPeriod');
/*Route::post('umashreports/selectAdmission', 'UmashReportsController@selectAdmission');*/
Route::get('umashreports/selectEnquiryPeriod', 'UmashReportsController@selectEnquiryPeriod');
Route::get('umashreports/selectBookingPeriod', 'UmashReportsController@selectBookingPeriod');
Route::get('umashreports/selectCarPeriod', 'UmashReportsController@selectCarPeriod');
Route::get('umashreports/selectDriverPeriod', 'UmashReportsController@selectDriverPeriod');
Route::get('umashreports/selectJinsurancePeriod', 'UmashReportsController@selectJinsurancePeriod');



//Automated sending report emails roots.
Route::get('email/send', 'ErpReportsController@sendMail');
Route::get('email/send_sales', 'ErpReportsController@sendMail_sales');
Route::get('email/send_sales_summary', 'ErpReportsController@sendMail_sales_summary');
Route::get('email/send_purchases', 'ErpReportsController@sendMail_purchases');
Route::get('email/send_expenses', 'ErpReportsController@sendMail_expenses');
Route::get('email/send_payments', 'ErpReportsController@sendMail_payments');
Route::get('email/send_stock', 'ErpReportsController@sendMail_stock');
Route::get('email/send_account', 'ErpReportsController@sendMail_account');


Route::resource('mails', 'MailsController');
Route::get('mailtest', 'MailsController@test');


Route::get('seedmail', function(){

  $mail = new Mailsender;

  $mail->driver = 'smtp';
  $mail->save();
});


Route::get('mail', function(){
  $mail = Mailsender::find(1);  
  return View::make('system.mail', compact('mail'));

});  

Route::get('css/subordinateleave', function(){

  $employeeid = DB::table('employee')->where('personal_file_number', '=', Confide::user()->username)->pluck('id');
   $c = Supervisor::where('supervisor_id', $employeeid)->count();
    
  $employee = Employee::findorfail($employeeid);

   //$leaveapplications = DB::table('leaveapplications')->where('employee_id', '=', $employee->id)->get();

  return View::make('css.approveleave', compact('c','leaveapplications'));
});

Route::get('employeeleave/view/{id}', 'LeaveapplicationsController@cssleaveapprove');

Route::get('supervisorapproval/{id}', 'LeaveapplicationsController@supervisorapprove');
Route::get('supervisorreject/{id}', 'LeaveapplicationsController@supervisorreject');



