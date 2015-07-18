<?php
namespace Phalcore\Models;

/**
 * Enhanced ORM, with extra convenience features
 */
abstract class Mongo extends \Phalcon\Mvc\Collection {

	/* //maybe?
	static function find($stuff){
		if (Mongo\AdHocDBRef::isRef($stuff)) {
			return Mongo\AdHocDBRef::expand($stuff);
		}
		return parent::find($stuff);
	}
	*/

	function assign($fields)
	{
		foreach ($fields as $key => $val) {
			if ($val instanceof Mongo) {
				if (!isset($val->_id)) $val->save();
				$fields[$key] = Mongo\AdHocDBRef::create($val);
			}
		}
		$super->assign($fields);
	}

	function __get($key)
	{
		$val = parent::__get($key);
		if (Mongo\AdHocDBRef::isRef($val)) {
			return Mongo\AdHocDBRef::expand($val);
		}
		return $val;
	}

}
