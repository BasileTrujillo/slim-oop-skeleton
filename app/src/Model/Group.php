<?php
namespace App\Model;

use App\Core\Model\MongoModel;

/**
 * Class Group
 * @package App\Model
 */
class Group extends MongoModel
{
    /**
     * @var string Collection name
     */
    protected $collection = 'group';

    /**
     * @var array Indexes
     */
    protected $indexes = [
        'name' => ['index' => 1]
    ];

    /**
     * @var array Required fields
     */
    protected $validator = [
        'name' => ['$type' => 'string']
    ];
}