<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use ValidatesRequests;
use App\Institute;
use DB;
class instituteController extends BaseController {

	public function __construct() {
		/*$this->beforeFilter('csrf', array('on'=>'post'));
		$this->beforeFilter('auth');
		$this->beforeFilter('userAccess',array('only'=> array('index','save')));*/
		    $this->middleware('auth', array('only'=>array('show','create','edit','update')));
	}
	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index()
	{
		$institute= Institute::select("*")->first();
		if(is_null($institute))
		{
			$institute=new Institute;
			$institute->name = "";
			$institute->establish = "";
			$institute->web = "";
			$institute->email = "";
			$institute->phoneNo = "";
			$institute->address = "";
		}

		//return View::Make('app.institute',compact('institute'));
		return View('app.institute',compact('institute'));
	}


	/**
	* Show the form for creating a new resource.
	*
	* @return Response
	*/
	public function save()
	{
		$rules=[
			'name' => 'required',
			'establish' => 'required',
			'web' => 'required',
			'email' => 'required',
			'phoneNo' => 'required',
			'address' => 'required',


		];
		$validator = \Validator::make(Input::all(), $rules);
		if ($validator->fails())
		{
			return Redirect::to('institute')->withinput(Input::all())->withErrors($validator);
		}
		else {

			DB::table("institute")->delete();
			$institue=new Institute;

			$institue->name = Input::get('name');
			$institue->establish = Input::get('establish');
			$institue->web = Input::get('web');
			$institue->email = Input::get('email');
			$institue->phoneNo = Input::get('phoneNo');
			$institue->address = Input::get('address');
			$institue->save();

			return Redirect::to('institute')->with('success', 'Institute  Information saved.');

		}
	}
}
