<?php
namespace Application\Model;

use InvalidArgumentException;
use RuntimeException;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Sql;
use Zend\Hydrator\HydratorInterface;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Update;
use Zend\Db\Sql\Delete;

class AdminDbHandler implements AdminDbInterface
{
    
    private $db;
    private $hydrator;
    private $customerPrototype;
    
    public function __construct(
        AdapterInterface $db,
        HydratorInterface $hydrator,
        Customer $customerPrototype
        ) {
            $this->db            = $db;
            $this->hydrator      = $hydrator;
            $this->customerPrototype = $customerPrototype;
    }

    public function listCustomers($appUserId, $offset=0, $limit=10)
    {
        $sql       = new Sql($this->db);
        $select    = $sql->select('customer');
        $select->where(['app_user_id = ?' => $appUserId]);
        $select->order('name ASC');
        $select->limit($limit);
        $select->offset($offset);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            return [];
        }
        
        $resultSet = new HydratingResultSet($this->hydrator,$this->customerPrototype);
        $resultSet->initialize($result);
        return $resultSet;
    }
    
    public function getCustomer($id, $appUserId)
    {
        $sql       = new Sql($this->db);
        $select    = $sql->select('customer');
        $select->where(['id = ?' => $id, 'app_user_id = ?' => $appUserId]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            throw new RuntimeException(sprintf(
                'Failed retrieving customer with identifier "%s"; unknown database error.',
                $id
                ));
        }
        
        $resultSet = new HydratingResultSet($this->hydrator, $this->customerPrototype);
        $resultSet->initialize($result);
        $customer = $resultSet->current();
        
        if (! $customer) {
            throw new InvalidArgumentException(sprintf(
                'Customer with identifier "%s" not found.',
                $id
                ));
        }
        
        
        
        return $customer;
    }
    
    public function getProject($id, $customerId, $appUserId)
    {
        $sql       = new Sql($this->db);
        $select    = $sql->select('project');
        $select->where(['id = ?' => $id, 'customer_id = ?' => $customerId, 'app_user_id = ?' => $appUserId]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            throw new RuntimeException(sprintf(
                'Failed retrieving project with identifier "%s"; unknown database error.',
                $id
                ));
        }
        
        $resultSet = new HydratingResultSet($this->hydrator, new Project(''));
        $resultSet->initialize($result);
        $project = $resultSet->current();
        
        if (! $project) {
            throw new InvalidArgumentException(sprintf(
                'Project with identifier "%s" not found.',
                $id
                ));
        }
        
        return $project;        
    }

    public function insertProject(Project $project)
    {}

    public function updateProject(Project $project)
    {}

    public function deleteProject(Project $project)
    {}

    public function updateCustomer(Customer $customer)
    {
        if (! $customer->getId()) {
            throw new RuntimeException('Cannot update customer; missing identifier');
        }
        
        $update = new Update('customer');
        $update->set([
            'name' => $customer->getName(),
        ]);
        $update->where(['id = ?' => $customer->getId()]);
        
        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($update);
        $result = $statement->execute();
        
        if (! $result instanceof ResultInterface) {
            throw new RuntimeException(
                'Database error occurred during customer update operation'
                );
        }
        
        return $customer;
    }

    public function insertCustomer(Customer $customer)
    {
        $insert = new Insert('customer');
        $insert->values([
            'name' => $customer->getName(),
            'app_user_id' => $customer->getAppUserId(),
            'status' => $customer->getStatus(),
        ]);
        
        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $result = $statement->execute();
        
        if (! $result instanceof ResultInterface) {
            throw new RuntimeException(
                'Database error occurred during customer insert operation'
                );
        }
        
        $id = $result->getGeneratedValue();
        
        return new Customer(
            $customer->getName(),
            $result->getGeneratedValue()
            );
    }

    public function deleteCustomer(Customer $customer)
    {
        if (! $customer->getId()) {
            throw new RuntimeException('Cannot update customer; missing identifier');
        }
        
        $delete = new Delete('customer');
        $delete->where(['id = ?' => $customer->getId()]);
        
        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($delete);
        $result = $statement->execute();
        
        if (! $result instanceof ResultInterface) {
            return false;
        }
        
        return true;
    }

    public function listProjects($customerId, $appUserId, $offset=0, $limit=10)
    {
        $sql       = new Sql($this->db);
        $select    = $sql->select('project');
        $select->where(['customer_id = ?' => $customerId, 'app_user_id = ?' => $appUserId]);
        $select->order('name ASC');
        $select->limit($limit);
        $select->offset($offset);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            return [];
        }
        
        $resultSet = new HydratingResultSet($this->hydrator,new Project(''));
        $resultSet->initialize($result);
        return $resultSet;
    }
}

