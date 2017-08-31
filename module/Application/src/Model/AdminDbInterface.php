<?php
namespace Application\Model;

interface AdminDbInterface {
    
    public function listCustomers($appUserId, $offset=0, $limit=10);
    public function getCustomer($id, $appUserId);
    public function insertCustomer(Customer $customer);
    public function updateCustomer(Customer $customer);
    public function deleteCustomer(Customer $customer);
    public function listProjects($customerId, $appUserId, $offset=0, $limit=10);
    public function getProject($id, $appUserId);
    public function insertProject(Project $project);
    public function updateProject(Project $project);
    public function deleteProject(Project $project);
    
}
