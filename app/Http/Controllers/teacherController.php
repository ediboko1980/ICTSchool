<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
//use Illuminate\Http\Request;
//use Illuminate\Foundation\Http\FormRequest;
use App\Student;
use App\Teacher;
use App\SectionModel;
use App\ClassModel;
use App\Subject;
use App\Timetable;
use DB;
class foobar{

}
Class formfoo{

}
class teacherController extends BaseController {

	public function __construct() {
		/*$this->beforeFilter('csrf', array('on'=>'post'));
		$this->beforeFilter('auth');
		$this->beforeFilter('userAccess',array('only'=> array('delete')));*/
	       $this->middleware('auth');
               $this->middleware('auth',array('only'=> array('delete')));
	}
	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index()
	{
	return View('app.teacherCreate');
	}

	public  function getRegi($class,$session,$section)
	{
		$ses =trim($session);
		$stdcount = Student::select(DB::raw('count(*) as total'))->where('class','=',$class)->where('session','=',$ses)->first();

		$stdseccount = Student::select(DB::raw('count(*) as total'))->where('class','=',$class)->where('session','=',$ses)->where('section','=',$section)->first();
		$r = intval($stdcount->total)+1;
		if(strlen($r)<2)
		{
			$r='0'.$r;
		}
		$c = intval($stdseccount->total)+1;
		$cl=substr($class,2);

		$foo = array();
		if(strlen($cl)<2) {
			$foo[0]= substr($ses, 2) .'0'.$cl.$r;
		}
		else
		{
			$foo[0]=  substr($ses, 2) .$cl.$r;
		}
		if(strlen($c)<2) {
			$foo[1] ='0'.$c;
		}
		else
		{
			$foo[1] =$c;
		}

		return $foo;

	}

	/**
	* Show the form for creating a new resource.
	*
	* @return Response
	*/
	public function create()
	{

		$rules=[//'regiNo' => 'required',
		'fname' => 'required',
		'lname' => 'required',
		'gender' => 'required',
		'religion' => 'required',
		'bloodgroup' => 'required',
		'nationality' => 'required',
		'dob' => 'required',
		//'session' => 'required',
		//'class' => 'required',
		//'section' => 'required',
		//'rollNo' => 'required',
		//'shift' => 'required',
		'photo' => 'required|mimes:jpeg,jpg,png',
		'phne' => 'required',
		'emails' => 'required',
		'fatherName' => 'required',
		'fatherCellNo' => 'required',
		//'motherName' => 'required',
		//'motherCellNo' => 'required',
		'presentAddress' => 'required',
		'parmanentAddress' => 'required'
	];
	$validator = \Validator::make(Input::all(), $rules);
	if ($validator->fails())
	{
		return Redirect::to('/teacher/create')->withErrors($validator);
	}
	else {
		$fileName=Input::get('fname').'.'.Input::file('photo')->getClientOriginalExtension();

		$teacher = new Teacher;
		$teacher->firstName= Input::get('fname');
		$teacher->lastName= Input::get('lname');
		$teacher->gender= Input::get('gender');
		$teacher->religion= Input::get('religion');
		$teacher->bloodgroup= Input::get('bloodgroup');
		$teacher->nationality= Input::get('nationality');
		$teacher->dob= Input::get('dob');
		$teacher->photo= $fileName;
		$teacher->nationality= Input::get('nationality');
		$teacher->phone= Input::get('phne');
		$teacher->email= Input::get('emails');

		$teacher->fatherName= Input::get('fatherName');
		$teacher->fatherCellNo= Input::get('fatherCellNo');
		$teacher->presentAddress= Input::get('presentAddress');
		$teacher->parmanentAddress= Input::get('parmanentAddress');

		$hasTeacher = Teacher::where('email','=',Input::get('emails'))->first();
		if ($hasTeacher)
		{
			$messages = $validator->errors();
			$messages->add('Duplicate!', 'Teacher already exits with this Email.');
			return Redirect::to('/teacher/create')->withErrors($messages)->withInput();
		}
		else {
			$teacher->save();
			 Input::file('photo')->move(base_path() .'/public/images/teacher',$fileName);
			//echo request()->photo->move(public_path('images/'), $fileName);
			return Redirect::to('/teacher/create')->with("success","Teacher Created Succesfully.");
		}


	}
}


/**
* Display the specified resource.
*
* @param  int  $id
* @return Response
*/
public function show()
{
	$teachers = DB::table('teacher')
		->select(DB::raw('teacher.*'))
		->get();
	return View("app.teacherList",compact('teachers'));
}
public function getList()
{
	$rules = [
		'class' => 'required',
		'section' => 'required',
		'shift' => 'required',
		'session' => 'required'


	];
	$validator = \Validator::make(Input::all(), $rules);
	if ($validator->fails()) {
		return Redirect::to('/student/list')->withInput(Input::all())->withErrors($validator);
	} else {
		$students = DB::table('Student')
		->join('Class', 'Student.class', '=', 'Class.code')
		->select('Student.id', 'Student.regiNo', 'Student.rollNo', 'Student.firstName', 'Student.middleName', 'Student.lastName', 'Student.fatherName', 'Student.motherName', 'Student.fatherCellNo', 'Student.motherCellNo', 'Student.localGuardianCell',
		'Class.Name as class', 'Student.presentAddress', 'Student.gender', 'Student.religion')
		->where('isActive', '=', 'Yes')
		->where('class',Input::get('class'))
		->where('section',Input::get('section'))
		->where('shift',Input::get('shift'))
		->where('session',trim(Input::get('session')))
		->get();
		if(count($students)<1)
		{
			return Redirect::to('/student/list')->withInput(Input::all())->with('error','No Students Found!');

		}
		else {
			$classes = ClassModel::pluck('name','code');
			$formdata = new formfoo;
			$formdata->class=Input::get('class');
			$formdata->section=Input::get('section');
			$formdata->shift=Input::get('shift');
			$formdata->session=trim(Input::get('session'));
			//return View::Make("app.studentList", compact('students','classes','formdata'));
			return View("app.studentList", compact('students','classes','formdata'));
		}
	}

}

public function view($id)
{
	$teacher=	DB::table('teacher')
	//->join('Class', 'Student.class', '=', 'Class.code')
	->select('*')
	->where('id','=',$id)->first();

	//return View::Make("app.studentView",compact('student'));
	return View("app.teacherView",compact('teacher'));
}
/**
* Show the form for editing the specified resource.
*
* @param  int  $id
* @return Response
*/
public function edit($id)
{
	$classes = ClassModel::pluck('name','code');
	$teacher= Teacher::find($id);
	//dd($teacher);
	$sections = SectionModel::select('name')->get();
	//return View::Make("app.studentEdit",compact('student','classes'));
	return View("app.teacherEdit",compact('teacher'));
}


/**
* Update the specified resource in storage.
*
* @param  int  $id
* @return Response
*/
public function update()
{

	$rules=[
		'fname' => 'required',
		'lname' => 'required',
		'gender' => 'required',
		'religion' => 'required',
		'bloodgroup' => 'required',
		'nationality' => 'required',
		'phone' => 'required',
		'email' => 'required',
		'dob' => 'required',
		'fatherName' => 'required',
		'fatherCellNo' => 'required',
		'presentAddress' => 'required',
		'parmanentAddress' => 'required'
	];
	$validator = \Validator::make(Input::all(), $rules);
	if ($validator->fails())
	{
		return Redirect::to('/teacher/edit/'.Input::get('id'))->withErrors($validator);
	}
	else {

		$teacher = Teacher::find(Input::get('id'));

		if(Input::hasFile('photo'))
		{

			if(substr(Input::file('photo')->getMimeType(), 0, 5) != 'image')
			{
				$messages = $validator->errors();
				$messages->add('Notvalid!', 'Photo must be a image,jpeg,jpg,png!');
				return Redirect::to('/teacher/edit/'.Input::get('id'))->withErrors($messages);
			}
			else {

				$fileName=Input::get('fname').'.'.Input::file('photo')->getClientOriginalExtension();
				$teacher->photo = $fileName;
				Input::file('photo')->move(base_path() .'/public/images',$fileName);
			}

		}
		else {
			$teacher->photo= Input::get('oldphoto');

		}
		//$student->regiNo=Input::get('regiNo');
		//$student->rollNo=Input::get('rollNo');
		$teacher->firstName= Input::get('fname');
		$teacher->lastName= Input::get('lname');
		$teacher->gender= Input::get('gender');
		$teacher->religion= Input::get('religion');
		$teacher->bloodgroup= Input::get('bloodgroup');
		$teacher->nationality= Input::get('nationality');
		$teacher->dob= Input::get('dob');
		$teacher->nationality= Input::get('nationality');
		$teacher->phone= Input::get('phone');
		$teacher->email= Input::get('email');

		$teacher->fatherName= Input::get('fatherName');
		$teacher->fatherCellNo= Input::get('fatherCellNo');
		$teacher->presentAddress= Input::get('presentAddress');
		$teacher->parmanentAddress= Input::get('parmanentAddress');

		$teacher->save();

		return Redirect::to('/teacher/list')->with("success","Teacher Updated Succesfully.");
	}


}


/**
* Remove the specified resource from storage.
*
* @param  int  $id
* @return Response
*/
public function delete($id)
{
	$teacher = Teacher::find($id);
	$teacher->delete();

	return Redirect::to('/teacher/list')->with("success","Teacher Deleted Succesfully.");
}

/**
* Display the specified resource.
*
* @param  int  $id
* @return Response
*/
public function getForMarks($class,$section,$shift,$session)
{
	$students= Student::select('regiNo','rollNo','firstName','middleName','lastName')->where('isActive','=','Yes')->where('class','=',$class)->where('section','=',$section)->where('shift','=',$shift)->where('session','=',$session)->get();
	return $students;
}

public function index_file()
		{
			//return View::Make('app.attendanceCreateFile');
			return View('app.teacherCreateFile');
		}

/**
		* Show the form for creating a new resource.
		*
		* @return Response
		*/
		public function create_file()
		{

			$file = Input::file('fileUpload');
			$ext = strtolower($file->getClientOriginalExtension());
			$validator = \Validator::make(array('ext' => $ext),array('ext' => 'in:xls,xlsx'));

			if ($validator->fails()) {
				return Redirect::to('teacher/create-file')->withErrors($validator);
			}else {
				    try{
						$toInsert = 0;
			            $data = \Excel::load(Input::file('fileUpload'), function ($reader) { })->get();
			                if(!empty($data) && $data->count()){
								DB::beginTransaction();
								try {
			                        foreach ($data->toArray() as $raw) {
										$teacherData= [
											'firstName' => $raw['firstname'],
											'lastName' => $raw['lastname'],
											'gender' =>    $raw['gender'],
											 'phone' => $raw['phone'],
											  'email' => $raw['email']
										];
										$hasTeacher = Teacher::where('email','=',$raw['email'])->first();
											if ($hasTeacher)
											{
												
											}else{
												Teacher::insert($teacherData);
												$toInsert++;
											}
			                        }
										 DB::commit();
								} catch (Exception $e) {
									DB::rollback();
									$errorMessages = new \Illuminate\Support\MessageBag;
									 $errorMessages->add('Error', 'Something went wrong!');
									return Redirect::to('/teacher/create-file')->withErrors($errorMessages);

									// something went wrong
								}
			            }
					   if($toInsert){
			                return Redirect::to('/teacher/create-file')->with("success", $toInsert.' Teacher data upload successfully.');
			            }
						$errorMessages = new \Illuminate\Support\MessageBag;
						 $errorMessages->add('Validation', 'File is empty!!!');
						return Redirect::to('/teacher/create-file')->withErrors($errorMessages);
	                }catch (Exception $e) {
						  $errorMessages = new \Illuminate\Support\MessageBag;
						  $errorMessages->add('Error', 'Something went wrong!');
						   return Redirect::to('/teacher/create-file')->withErrors($errorMessages);
	                }
		}

	}

	public function index_timetable()
	{
		$classes = DB::table('Class')
		->select(DB::raw('Class.*'))
		->get();

      $sections  = DB::table('section')
		->select(DB::raw('section.*'))
		->get();

       $subjects  = DB::table('Subject')
		->select(DB::raw('Subject.*'))
		->get();

      $teachers = DB::table('teacher')
		->select(DB::raw('teacher.*'))
		->get();
      //dd($teachers);
      return View("app.teacherTimetable",compact("classes","sections","teachers","subjects"));

	}

	public function create_timetable()
	{
		$rules=[//'regiNo' => 'required',
		'teacher' => 'required',
		'class' => 'required',
		'section' => 'required',
		'subject' => 'required',
		'startt' => 'required',
		'endt' => 'required',
		'day' => 'required',
	];
	$validator = \Validator::make(Input::all(), $rules);
	if ($validator->fails())
	{
		return Redirect::to('/teacher/create-timetable')->withErrors($validator);
	}
	else {

		$timetable = new Timetable;
		$timetable->teacher_id= Input::get('teacher');
		$timetable->class_id= Input::get('class');
		$timetable->section_id= Input::get('section');
		$timetable->subject_id= Input::get('subject');
		$timetable->stattime= Input::get('startt');
		$timetable->endtime= Input::get('endt');
		$timetable->day= Input::get('day');
	
		/*$hasTimetable = Timetable::where('teacher_id','=',Input::get('teacher'))->where('class_id','=',Input::get('class'))->first();
		if ($hasTimetable)
		{
			$messages = $validator->errors();
			$messages->add('Duplicate!', 'Teacher already exits with this Email.');
			return Redirect::to('/teacher/create')->withErrors($messages)->withInput();
		}
		else {*/
			$timetable->save();
			//echo request()->photo->move(public_path('images/'), $fileName);
			return Redirect::to('/teacher/create-timetable')->with("success","Time Table Created Succesfully.");
	//	}


	}
	}
	public function view_timetable($id)
	{
		$timetables = DB::table('teacher')
		->join('timetable', 'teacher.id', '=', 'timetable.teacher_id')
		->join('Subject', 'Subject.id', '=', 'timetable.subject_id')
		->join('Class', 'Class.id', '=', 'timetable.class_id')
		->select('teacher.*','timetable.stattime','timetable.endtime','timetable.day','timetable.section_id','Subject.name AS subname','Class.name AS classname')
		->where('timetable.teacher_id',$id)
	/*	->where('section',Input::get('section'))
		->where('shift',Input::get('shift'))
		->where('session',trim(Input::get('session')))*/
		->get();
		//dd($timetables);
		 return View("app.teacherViewtimetable",compact('timetables'));
	}
}