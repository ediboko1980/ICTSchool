<?php
namespace App;
class ClassModel extends \Eloquent {
	 protected $table = 'Class';
	protected $fillable = ['name','numValue','description'];

}
