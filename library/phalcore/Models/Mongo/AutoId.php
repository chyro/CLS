<?php
namespace Phalcore\Models\Mongo;

/**
 * Trait for Phalcore model classes
 *
 * Classes using this trait will get an auto-incremented field named "id".
 */
trait AutoId {
    public function save()
    //public function beforeCreate() // possibly more appropriate, but causes conflicts with the trait Timestamped
    {
        if (empty($this->id)) {
            $this->id = $this->getNextId();
        }
        parent::save();
    }

    /**
     * Atomic query for next usable ID
     */
    public function getNextId()
    {
        $collection = $this->getSource();
        $idCollection = $this->getConnection()->{'phalcore.autoid'};
        /*$nextIdRecord = $idCollection->findAndModify(
            [ 'collection' => $collection ],
            [ '$inc' => [ 'currentId' => 1] ],
            [ 'upsert' => true, 'new' => true]
        );*/
        $nextIdRecord = $idCollection->findOneAndUpdate(
                        [ 'collection' => $collection ],
                        [ '$set' => [ 'collection' => $collection ], '$inc' => [ 'currentId' => 1 ] ],
                        [ 'upsert' => true, 'returnDocument' => \Phalcon\Db\Adapter\MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER ]
            // should be "returnNewDocument => true" but Phalcon decided otherwise?
                );

        return $nextIdRecord->currentId;
    }
}

