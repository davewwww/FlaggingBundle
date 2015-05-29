<?php

namespace Dwo\FlaggingBundle\Cache;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;

/**
 * @author Dave Www <davewwwo@gmail.com>
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
     * @param Connection $connection
     * @param string     $table
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
            return unserialize($item[self::DATA_FIELD]);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return (boolean) $this->findById($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0, $catched = false)
    {
        list($idField, $dataField, $expField) = $this->getFields();

        $qb = $this->connection->createQueryBuilder()
            ->setParameter(':data', serialize($data))
            ->setParameter(':exp', $lifeTime > 0 ? time() + $lifeTime : null)
            ->setParameter(':id', $id);
        $qbClone = clone $qb;

        try {
            if ($this->doContains($id)) {
                $qb->update($this->table)
                    ->set($dataField, ':data')
                    ->set($expField, ':exp')
                    ->where($idField.' = :id');
            } else {
                $qb->insert($this->table)
                    ->values(
                        array(
                            $idField   => ':id',
                            $dataField => ':data',
                            $expField  => ':exp',
                        )
                    );
            }

            return $qb->execute();
        } /**
         * Table not exists
         */
        catch (\Doctrine\DBAL\Exception\TableNotFoundException $e) {
            if ($catched) {
                throw $e;
            }
            $this->createTable();

            return $this->doSave($id, $data, $lifeTime, true);
        } /**
         * Duplicate entry
         */
        catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            $qb = clone $qbClone;
            $qb->update($this->table)
                ->set($dataField, ':data')
                ->set($expField, ':exp')
                ->where($idField.' = :id');

            return $qb->execute();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        list($idField) = $this->getFields();

        $qb = $this->connection->createQueryBuilder()
            ->delete($this->table)
            ->setParameter(':id', $id)
            ->where($idField.' = :id');

        return $qb->execute();
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        $qb = $this->connection->createQueryBuilder()
            ->delete($this->table);

        return $qb->execute();
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        // no-op.
    }

    /**
     * create a table
     */
    private function createTable()
    {
        list($id, $data, $exp) = $this->getFields();

        $sm = $this->connection->getDriver()->getSchemaManager($this->connection);
        $sm->createTable(
            new Table(
                $this->table,
                array(
                    new Column($id, Type::getType('string'), array('NotNull' => true, 'Length' => 256)),
                    new Column($data, Type::getType('blob'), array('NotNull' => false, 'Default' => 'NULL')),
                    new Column($exp, Type::getType('integer'), array('NotNull' => false, 'Default' => 'NULL')),
                ),
                array(
                    new Index($id, [$id], true, true)
                )
            )
        );
    }

    /**
     * @param mixed $id
     *
     * @return array|null
     */
    private function findById($id)
    {
        list($idField) = $fields = $this->getFields();

        try {
            $qb = $this->connection->createQueryBuilder()
                ->select($fields)
                ->from($this->table)
                ->where(sprintf('%s = :id', $idField))
                ->setParameter(':id', $id)
                ->setMaxResults(1);

            $item = $qb->execute()->fetch();
        } catch (\Doctrine\DBAL\Exception\TableNotFoundException $e) {
            return null;
        }

        if (empty($item)) {
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
