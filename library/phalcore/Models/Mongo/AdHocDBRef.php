<?php
namespace Phalcore\Models\Mongo;

/**
 * Simple, lightweight DBRef keeping track of the model class
 * to instantiate them back upon query.
 */
abstract class AdHocDBRef {

	static public function create($item) {
		//Might be better with ["class" => $class, "dbref" => DBRef::create()] ?
		return [
				"collection" => $item->getSource(),
				"class" => get_class($item),
				"id" => $item->_id,
			];
	}

	static public function expand($ref) {
		if (!class_exists($ref["class"])) {
			throw new MongoException("Error expanding DB value back into a class ($ref)");
		}

		$cls = $ref["class"];
		return $cls::findById($ref["id"]);
		//return new $cls($ref["id"]);
	}

	static public function isRef($ref) {
		return (is_array($ref)
			&& count($ref) == 3
			&& array_key_exists("collection", $ref)
			&& array_key_exists("class", $ref)
			&& array_key_exists("id", $ref)
			&& class_exists($ref["class"])
			);
	}

}
