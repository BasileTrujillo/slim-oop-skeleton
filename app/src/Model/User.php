<?php
namespace App\Model;

use App\Core\Model\MongoModel;

/**
 * Class User
 * @package App\Model
 */
class User extends MongoModel
{
    /**
     * @var string Collection name
     */
    protected $collection = 'user';

    /**
     * @var array Indexes
     */
    protected $indexes = [
        'login'    => ['index' => 1, 'unique' => true],
        'group_id' => ['index' => 1]
    ];

    /**
     * @var array Required fields
     */
    protected $validator = [
          'login'       => ['$type' => 'string'],
          'hash'        => ['$type' => 'string'],
          'group_id'    => ['$type' => 'objectId']
    ];
}