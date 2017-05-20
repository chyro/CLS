<?php
namespace Phalcore\Models\Mongo;

/**
 * Trait for Phalcore model classes
 *
 * Classes using this trait will get a created_date on creation and a last_updated on any save.
 */
trait Timestamped {
    public function beforeCreate()
    {
        $this->created_date = date("Y-m-d H:i:s"); // new MongoDate()?
    }

    public function beforeSave()
    {
        $this->last_updated = date("Y-m-d H:i:s"); // new MongoDate()?
    }
}

