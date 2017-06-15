<?php
namespace Phalcore\Models;

/**
 * Enhanced ORM, with extra convenience features
 */
abstract class Mongo extends \Phalcon\Mvc\MongoCollection {

    // __construct is final, so support for object initialization (e.g. default values for fields) is offered via the factory pattern:
    public static function factory()
    {
        $class = get_called_class();
        $object = new $class();
        foreach (static::getDefaults() as $field => $value) {
            $object->{$field} = $value;
        }
        return $object;
    }

    // This function can be overloaded to assign default values to fields when creating a new instance.
    public static function getDefaults()
    {
        return [];
    }

    /**
     * The save function stores the assigned fields into
     * the database. Overloaded to...
     * - handle exceptions
     * - convert objects to references before saving
     */
    public function save() {
        //convert objects into refs
        $this->deepObjToRef();

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

        //convert refs back into objects
        $this->deepRefToObj();

        return $success;
    }

    /**
     * Hook converting references to objects when instantiating queried objects
     */
    public function afterFetch() {
        $this->deepRefToObj();
    }
    //Alternatively, if afterFetch doesn't work, overload find and findFirst (and findByID?)

    /**
     * Recursive function, for deep object -> reference conversion
     * (might be more efficient to pass the array by reference)
     */
    private function _recO2R($blob) {
        $changed = false;
        foreach($blob as $key => $val) {
            if ($val instanceof \Phalcore\Models\Mongo) {
                $val = Mongo\AdHocDBRef::create($val);
                $blob[$key] = $val;
                $changed = true;
            } elseif (is_array($val)) {
                list ($converted, $recChanged) = $this->_recO2R($val);
                if ($recChanged) {
                    $blob[$key] = $converted;
                    $changed = true;
                }
            }
        }
        return [$blob, $changed];
    }

    /**
     * Function converting Mongo objects to Mongo-ish references on all
     * the fields of the current object, as well as any contained in arrays.
     */
    private function deepObjToRef() {
        $allFields = $this->toArray();
        foreach ($allFields as $key => $val) {
            if ($val instanceof \Phalcore\Models\Mongo) {
                if (!isset($val->_id)) $val->save();
                $val = Mongo\AdHocDBRef::create($val);
                $this->$key = $val;
            } elseif (is_array($val)) {
                list ($converted, $changed) = $this->_recO2R($val);
                if ($changed)
                    $this->$key = $converted;
            }
        }
    }

    /**
     * Recursive function, for deep reference -> object conversion
     */
    private function _recR2O($blob) {
        $changed = false;
        foreach($blob as $key => $val) {
            if (Mongo\AdHocDBRef::isRef($val)) {
                $val = Mongo\AdHocDBRef::expand($val);
                $blob[$key] = $val;
                $changed = true;
            } elseif (is_array($val)) {
                list ($converted, $recChanged) = $this->_recR2O($val);
                if ($recChanged) {
                    $blob[$key] = $converted;
                    $changed = true;
                }
            }
        }
        return [$blob, $changed];
    }

    /**
     * Function converting Mongo objects to Mongo-ish references on all
     * the fields of the current object, as well as any contained in arrays.
     */
    private function deepRefToObj() {
        $allFields = $this->toArray();
        foreach ($allFields as $key => $val) {
            if (Mongo\AdHocDBRef::isRef($val)) {
                $val = Mongo\AdHocDBRef::expand($val);
                $this->$key = $val;
            } elseif (is_array($val)) {
                list ($converted, $changed) = $this->_recR2O($val);
                if ($changed)
                    $this->$key = $converted;
            }
        }
    }

}
