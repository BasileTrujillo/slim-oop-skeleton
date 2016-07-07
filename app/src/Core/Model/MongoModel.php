<?php
namespace App\Core\Model;

use MongoDB\BSON\ObjectID;

/**
 * Class MongoModel
 * @package App\Core\Model
 */
class MongoModel
{
    /**
     * Field name to auto set a created timestamp
     */
    const FIELD_CREATE = 'created_at';

    /**
     * Field name to auto set an udpated timestamp
     */
    const FIELD_UPDATE = 'updated_at';

    /**
     * @var \MongoDB\Database
     */
    private $mongoDB;

    /**
     * @var \MongoDB\Collection
     */
    private $mongoCol;

    /**
     * @var string database name
     */
    protected $database = 'database';

    /**
     * @var string collection name
     */
    protected $collection;

    /**
     * @var array field list with type (_id is not compulsory)
     *            ex:
     *              [
     *                  'name' => [
     *                      'index' => 1,
     *                      'unique' => true,
     *                  ],
     *                  'phone' => [
     *                      'index' => -1,
     *                  ],
     *              ]
     */
    protected $indexes = [];

    /**
     * @var array Validate array for collection creation (Required fields)
     * @see http://mongodb.github.io/mongo-php-library/classes/database/#createcollection
     * @see https://docs.mongodb.com/manual/core/document-validation/
     */
    protected $validator = [];

    /**
     * Model constructor.
     *
     * @param \MongoDB\Client|\MongoDB\Database|\MongoDB\Collection $mongo
     *
     * @throws \Exception
     */
    public function __construct($mongo)
    {
        switch (get_class($mongo)) {
            case 'MongoDB\Client':
                if (isset($this->database) && !empty($this->database)) {
                    $this->mongoDB = $mongo->selectDatabase($this->database);
                    $this->initCollection();
                } else {
                    throw new \Exception('No database name. Please Set $database or provide a \MongoDB\Database.');
                }
                break;
            case 'MongoDB\Database':
                $this->mongoDB = $mongo;
                $this->database = $this->mongoDB->getDatabaseName();
                $this->initCollection();
                break;
            case 'MongoDB\Collection':
                $this->mongoCol = $mongo;
                $this->database = $this->mongoCol->getDatabaseName();
                $this->collection = $this->mongoCol->getCollectionName();
                break;
            default:
                throw new \Exception(
                    'Invalid provided object (' . get_class($mongo) . ').' .
                    'Must be instance of \MongoDB\Client or \MongoDB\Database'
                );
                break;
        }
    }

    /**
     * Initialize Mongo Collection from Mongo Database
     *
     * @throws \Exception
     */
    protected function initCollection()
    {
        if (isset($this->collection) && !empty($this->collection)) {
            $this->mongoCol = $this->mongoDB->selectCollection($this->collection);
            $this->collection = $this->mongoCol->getCollectionName();
        } else {
            throw new \Exception('No collection name. Please Set $collection or provide a \MongoDB\Collection.');
        }
    }

    /**
     * Creates indexes from $this->fields definition
     * For multi indexes, override this method
     *
     * @throws \Exception
     */
    public function createIndexes()
    {
        $this->indexes += [
            self::FIELD_CREATE => 1,
            self::FIELD_UPDATE => 1,
        ];

        foreach ($this->indexes as $field => $opt) {
            if (isset($opt['index'])) {
                $indexOpt = [];
                if (isset($opt['unique']) && $opt['unique'] === true) {
                    $indexOpt = ['unique' => true];
                }
                $this->mongoCol->createIndex([$field => $opt['index']], $indexOpt);
            }
        }

    }

    /**
     * Create collection using validator
     */
    public function createCollection()
    {
        $option = [];
        if (isset($this->validator) && !empty($this->validator)) {
            $option['validator'] = $this->validator;
        }
        $this->mongoDB->createCollection($this->collection, $option);
    }

    /**
     * Update or insert a document or a part of a document
     * Update fields keeping existing values
     *
     * @param array        $data
     * @param string|array $uspertField      Field name to upsert or array of field name
     * @param bool         $updateMostRecent If true => update only if updated_at passed to $data is the most recent
     *                                       In this case, $data['updated_at'] is compulsory
     *                                       If $updateMostRecent == true && !isset($data['updated_at']) => no updates performed
     *
     * @return \MongoDB\UpdateResult
     */
    public function upsert($data, $uspertField = null, $updateMostRecent = false)
    {
        $filter = [];
        if (is_array($uspertField)) {
            foreach ($uspertField as $field) {
                if (isset($data[$field])) {
                    $filter[$field] = $data[$field];
                }
            }
        } else {
            if (isset($data[$uspertField])) {
                $filter = [$uspertField => $data[$uspertField]];
            }
        }

        $doc = $this->findOne($filter);
        if ($doc !== null) {
            if ($updateMostRecent) {
                if (isset($data['updated_at']) && $data['updated_at'] > $doc['updated_at']) {
                    $data += (array)$doc;
                } else {
                    return false;
                }
            } else {
                $data += (array)$doc;
            }
        } else {
            if (!isset($data[self::FIELD_CREATE]) || empty($data[self::FIELD_CREATE])) {
                $data[self::FIELD_CREATE] = time();
            }
        }
        if (!isset($data[self::FIELD_UPDATE]) || empty($data[self::FIELD_UPDATE])) {
            $data[self::FIELD_UPDATE] = time();
        }
        
        return $this->mongoCol->replaceOne($filter, $data, ['upsert' => true]);
    }

    /**
     * Mongo Collection find alias
     *
     * @param array $filter
     * @param array $options
     *
     * @return \MongoDB\Driver\Cursor
     */
    public function find($filter = [], array $options = [])
    {
        return $this->mongoCol->find($filter, $options);
    }

    /**
     * Mongo Collection findOne alias
     *
     * @param array $filter
     * @param array $options
     *
     * @return array|null|object
     */
    public function findOne($filter = [], array $options = [])
    {
        return $this->mongoCol->findOne($filter, $options);
    }

    /**
     * Find document by _id
     *
     * @param integer $id
     * @param array   $options
     *
     * @return array|null|object
     */
    public function findById($id, array $options = [])
    {
        if (is_object($id) && get_class($id) === 'MongoDB\BSON\ObjectID') {
            $_id = $id;
        } else {
            $_id = new ObjectID($id);
        }
        $find = ['_id' => $_id];

        return $this->findOne($find, $options);
    }

    /**
     * Return the MongoCollection instance
     *
     * @return \MongoDB\Collection
     */
    public function getCollection()
    {
        return $this->mongoCol;
    }
}