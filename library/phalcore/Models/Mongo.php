<?php
namespace Phalcore\Models;

/**
 * Enhanced ORM, with extra convenience features
 */
class Mongo extends \Phalcon\Mvc\Collection {

	/* //maybe?
	static function find($stuff){
		if (Mongo\AdHocDBRef::isRef($stuff)) {
			return Mongo\AdHocDBRef::expand($stuff);
		}
		return parent::find($stuff);
	}
	*/

	/**
	 * The save function stores the assigned fields into
	 * the database. Overloaded here to handle exceptions.
	 */
	public function save() {
//exit;
		//TODO: Put that in a library class.
		//TODO: use layout
		set_exception_handler(function(\Exception $e)
		{
			if (ob_get_level() > 0) {
				ob_end_clean();
			}

			echo '<html><head><title>Exception - ', get_class($e), ': ', $e->getMessage(), '</title></head><body>';
			echo '<div class="error-main">
				', get_class($e), ': ', $e->getMessage(), '
				<br/><span class="error-file">', $e->getFile(), ' (', $e->getLine(), ')</span>
				</div>';
			echo '<div class="error-backtrace"><table cellspacing="0">';
			//echo "<pre>"; var_dump($e->getTrace()); echo "</pre>";
			foreach ($e->getTrace() as $n => $trace) {
				echo "<hr>";
				if ($has_line = (isset($trace["file"]) && isset($trace["line"])))
					echo "<p>" . $trace["file"] . " line " . $trace["line"] . "</p>";
				if($has_class = (isset($trace["class"]) && isset($trace["function"])))
					echo "<p>" . $trace["class"] . "::" . $trace["function"] . "</p>";
				if (!$has_line && !$has_class) {
					echo "<pre>";
					var_dump($trace);
					echo "</pre>";
				}
			}
			echo '</table></div>';
			echo '</body></html>';
		});
		$success = parent::save();
		restore_exception_handler();
		return $success;
	}

	/**
	 * The assign function stores an array as the fields
	 * of the object. Overloading here to convert Mongo
	 * objects into references.
	 *//*
//TODO: Go deeper into arrays to convert Mongo objects that are contained within
	public function assign($fields)
	{
		foreach ($fields as $key => $val) {
			if ($val instanceof Mongo) {
				if (!isset($val->_id)) $val->save();
				$fields[$key] = Mongo\AdHocDBRef::create($val);
			}
		}
		$super->assign($fields);
	}/**/

	/**
	 * The __set function updates one field of the object.
	 * Overloading here to convert Mongo objects into
	 * references.
NOT CALLED - need to serialize on DB save
	 */
	/*public function __set($key, $val)
	{
echo "set ($key: " . var_export($val,true) . ")<br/>";
//TODO: Go deeper into arrays to convert Mongo objects that are contained within
		if (Mongo\AdHocDBRef::isRef($val)) {
			$val = Mongo\AdHocDBRef::create($val);
		}
		$this->$key = $val;
	}*/

	/**
	 * The __get function returns the fields of the object.
	 * Overloading here to convert Mongo references into
	 * objects.
NOT CALLED - need to unserialize on DB load
	 */
	/*public function __get($key)
	{
echo "get ($key)<br/>";
//TODO: Go deeper into arrays to convert Mongo objects that are contained within
		$val = $this->$key;
		if (Mongo\AdHocDBRef::isRef($val)) {
			return Mongo\AdHocDBRef::expand($val);
		}
		return $val;
	}*/

}
