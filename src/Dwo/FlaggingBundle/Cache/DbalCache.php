<?php

namespace Dwo\FlaggingBundle\Cache;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;

/**
 * @author David Wolter <david@lovoo.com>
 */
class DbalCache extends CacheProvider
{
    /**
     * The ID field will store the cache key.
     */
    const ID_FIELD = 'k';

    /**
     * The data field will store the serialized PHP value.
     */
    const DATA_FIELD = 'd';

    /**
     * The expiration field will store a date value indicating when the
     * cache entry should expire.
     */
    const EXPIRATION_FIELD = 'e';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $table;

    /**
     * Constructor.k
     */
    public function __construct(Connection $connection, $table)
    {
        $this->connection = $connection;
        $this->table = (string) $table;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        if ($item = $this->findById($id)) {
            return json_decode($item[self::DATA_FIELD], 1);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return (boolean) $this->findById($id, false);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0, $catched = false)
    {
        try {
            $statement = $this->connection->prepare(
                sprintf(
                    'INSERT OR REPLACE INTO %s (%s) VALUES (:id, :data, :expire)',
                    $this->table,
                    implode(',', $this->getFields())
                )
            );
        } catch (TableNotFoundException $e) {
            if ($catched) {
                throw $e;
            }
            $this->createTable();

            return $this->doSave($id, $data, $lifeTime, true);
        }

        $statement->bindValue(':id', $id);
        $statement->bindValue(':data', json_encode($data));
        $statement->bindValue(':expire', $lifeTime > 0 ? time() + $lifeTime : null);

        return $statement->execute();
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        list($idField) = $this->getFields();

        $statement = $this->connection->prepare(
            sprintf(
                'DELETE FROM %s WHERE %s = :id',
                $this->table,
                $idField
            )
        );

        $statement->bindValue(':id', $id);

        return $statement->execute();
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        return $this->connection->exec(sprintf('DELETE FROM %s', $this->table));
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        // no-op.
    }

    /**
     *
     */
    public function createTable()
    {
        list($id, $data, $exp) = $this->getFields();

        $table = new Table(
            $this->table,
            array(
                new Column($id, Type::getType('text'), array('NotNull' => true)),
                new Column($data, Type::getType('blob'), array('Default' => 'NULL')),
                new Column($exp, Type::getType('integer'), array('Default' => 'NULL')),
            ),
            array(
                new Index($id, [$id], true, true)
            )
        );

        $sm = $this->connection->getDriver()->getSchemaManager($this->connection);
        $sm->createTable($table);
    }

    /**
     * Find a single row by ID.
     *
     * @param mixed   $id
     * @param boolean $includeData
     *
     * @return array|null
     */
    private function findById($id, $includeData = true)
    {
        list($idField) = $fields = $this->getFields();

        if (!$includeData) {
            $key = array_search(static::DATA_FIELD, $fields);
            unset($fields[$key]);
        }

        try {
            $statement = $this->connection->prepare(
                $a = sprintf(
                    'SELECT %s FROM %s WHERE %s = "%s" LIMIT 1',
                    implode(',', $fields),
                    $this->table,
                    $idField,
                    $id
                )
            );
        } catch (TableNotFoundException $e) {
            return null;
        }

        $statement->execute();

        $item = $statement->fetch();

        if ($item === false) {
            return null;
        }

        if ($this->isExpired($item)) {
            $this->doDelete($id);

            return null;
        }

        return $item;
    }

    /**
     * Gets an array of the fields in our table.
     *
     * @return array
     */
    private function getFields()
    {
        return array(static::ID_FIELD, static::DATA_FIELD, static::EXPIRATION_FIELD);
    }

    /**
     * Check if the item is expired.
     *
     * @param array $item
     *
     * @return boolean
     */
    private function isExpired(array $item)
    {
        return isset($item[static::EXPIRATION_FIELD]) &&
        $item[self::EXPIRATION_FIELD] !== null &&
        $item[self::EXPIRATION_FIELD] < time();
    }
}
