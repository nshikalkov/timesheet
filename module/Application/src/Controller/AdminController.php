<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Application\Model\AdminDbInterface;
use Application\Form\CustomerForm;
use Application\Form\ProjectForm;


class AdminController extends AbstractActionController
{
    private $adminDb;
    private $customerForm;
    private $projectForm;
    
    public function __construct(AdminDbInterface $adminDb, CustomerForm $customerForm, ProjectForm $projectForm)
    {
        $this->adminDb = $adminDb;
        $this->customerForm = $customerForm;
        $this->projectForm = $projectForm;
    }
    
    public function indexAction()
    {
        $container = new Container('auth');
        
        if (!isset($container->currentUserId) || $container->currentUserId == 0) {
            return $this->redirect()->toRoute(
                'auth',
                []
                );
        }
        
        return new ViewModel([
            'customers' => $this->adminDb->listCustomers($container->currentUserId),
        ]);
    }
    
    public function addCustomerAction()
    {
        $container = new Container('auth');
        
        if (!isset($container->currentUserId) || $container->currentUserId == 0) {
            return $this->redirect()->toRoute(
                'auth',
                []
                );
        }
        
        $request   = $this->getRequest();
        $viewModel = new ViewModel(['form' => $this->customerForm]);
        
        if (! $request->isPost()) {
            return $viewModel;
        }
        
        $this->customerForm->setData($request->getPost());
        
        if (! $this->customerForm->isValid()) {
            return $viewModel;
        }
        
        $customer = $this->customerForm->getData();
        
        $customer->setAppUserId($container->currentUserId);
        $customer->setStatus('active');
        
        if ($container->currentUserId != 1) {
            try {
                $customer = $this->adminDb->insertCustomer($customer);
            } catch (\Exception $ex) {
                // An exception occurred; we may want to log this later and/or
                // report it to the user. For now, we'll just re-throw.
                throw $ex;
            }
        }
        
        return $this->redirect()->toRoute(
            'admin',
            []
            );
    }
    
    public function editCustomerAction()
    {
        $container = new Container('auth');
        
        if (!isset($container->currentUserId) || $container->currentUserId == 0) {
            return $this->redirect()->toRoute(
                'auth',
                []
                );
        }
        
        $id = $this->params()->fromRoute('id');
        if (! $id) {
            return $this->redirect()->toRoute('admin');
        }
        
        try {
            $customer = $this->adminDb->getCustomer($id, $container->currentUserId);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('admin');
        }
        
        $this->customerForm->bind($customer);
        $viewModel = new ViewModel(['form' => $this->customerForm, 'id' => $id]);
        
        $request = $this->getRequest();
        if (! $request->isPost()) {
            return $viewModel;
        }
        
        $this->customerForm->setData($request->getPost());
        
        if (! $this->customerForm->isValid()) {
            return $viewModel;
        }
        
        if ($container->currentUserId != 1) {
            $customer = $this->adminDb->updateCustomer($customer);
        }
        
        
        return $this->redirect()->toRoute(
            'admin',
            []
            );
    }
    
    public function deleteCustomerAction()
    {
        $container = new Container('auth');
        
        if (!isset($container->currentUserId) || $container->currentUserId == 0) {
            return $this->redirect()->toRoute(
                'auth',
                []
                );
        }
        
        $id = $this->params()->fromRoute('id');
        if (! $id) {
            return $this->redirect()->toRoute('admin');
        }
        
        try {
            $customer = $this->adminDb->getCustomer($id, $container->currentUserId);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('admin');
        }
        
        $request = $this->getRequest();
        if (! $request->isPost()) {
            return new ViewModel(['customer' => $customer]);
        }
        
        if ($id != $request->getPost('id')
            || 'Delete' !== $request->getPost('confirm', 'no')
            ) 
        {
            return $this->redirect()->toRoute('admin');
        }
            
        if ($container->currentUserId != 1) {
            $customer = $this->adminDb->deleteCustomer($customer);
        }
        
        return $this->redirect()->toRoute('admin');
    }
    
    public function listProjectsAction()
    {
        $container = new Container('auth');
        
        if (!isset($container->currentUserId) || $container->currentUserId == 0) {
            return $this->redirect()->toRoute(
                'auth',
                []
                );
        }
        
        $id = $this->params()->fromRoute('id');
        if (! $id) {
            return $this->redirect()->toRoute('admin');
        }
        
        $customer = $this->adminDb->getCustomer($id,$container->currentUserId);
        if (!$customer) {
            return $this->redirect()->toRoute('admin');
        }
        
        return new ViewModel([
            'projects' => $this->adminDb->listProjects($id,$container->currentUserId),
            'customerName' => $customer->getName(),
            'customerId' => $id,
        ]);
    }
    
    public function addProjectAction()
    {
        $container = new Container('auth');
        
        if (!isset($container->currentUserId) || $container->currentUserId == 0) {
            return $this->redirect()->toRoute(
                'auth',
                []
                );
        }
        
        $request   = $this->getRequest();
        
        $id = $this->params()->fromRoute('id');
        if (! $id) {
            return $this->redirect()->toRoute('admin');
        }
        
        $viewModel = new ViewModel(['form' => $this->projectForm, 'customerId' => $id]);
        
        if (! $request->isPost()) {
            return $viewModel;
        }
        
        
        
         $this->projectForm->setData($request->getPost());
        
         if (! $this->projectForm->isValid()) {
             return $viewModel;
         }
        
         $project = $this->projectForm->getData();
        
         $project->setCustomerId($id);
         $project->setAppUserId($container->currentUserId);
         $project->setStatus('active');
        
         if ($container->currentUserId != 1) {
             try {
                 $project = $this->adminDb->insertProject($project);
             } catch (\Exception $ex) {
                 // An exception occurred; we may want to log this later and/or
                 // report it to the user. For now, we'll just re-throw.
                 throw $ex;
             }
         }
        
         return $this->redirect()->toRoute(
             'admin',
             ['action'=>'list-projects', 'id'=>$project->getCustomerId()]
             );
    }
    
    public function editProjectAction()
    {
		$container = new Container('auth');
        
        if (!isset($container->currentUserId) || $container->currentUserId == 0) {
            return $this->redirect()->toRoute(
                'auth',
                []
                );
        }
        
        $id = $this->params()->fromRoute('id');
        if (! $id) {
            return $this->redirect()->toRoute('admin');
        }
        
        try {
            $project = $this->adminDb->getProject($id, $container->currentUserId);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('admin');
        }
        
        $this->projectForm->bind($project);
        $viewModel = new ViewModel(['form' => $this->projectForm, 'id' => $id, 'customerId' => $this->params()->fromQuery('customerId')]);
        
        $request = $this->getRequest();
        if (! $request->isPost()) {
            return $viewModel;
        }
        
        $this->projectForm->setData($request->getPost());
        
        if (! $this->projectForm->isValid()) {
            return $viewModel;
        }
        
        if ($container->currentUserId != 1) {
            $project = $this->adminDb->updateProject($project);
        }
        
        
        return $this->redirect()->toRoute(
             'admin',
             ['action'=>'listProjects', 'id'=>$request->getPost()['customerId']]
             );
    }
    
    public function deleteProjectAction()
    {
        $container = new Container('auth');
        
        if (!isset($container->currentUserId) || $container->currentUserId == 0) {
            return $this->redirect()->toRoute(
                'auth',
                []
                );
        }
        
        $id = $this->params()->fromRoute('id');
        if (! $id) {
            return $this->redirect()->toRoute('admin');
        }
        
        try {
            $project = $this->adminDb->getProject($id, $container->currentUserId);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('admin');
        }
        
        $request = $this->getRequest();
        if (! $request->isPost()) {
            return new ViewModel(['project' => $project, 'customerId' => $this->params()->fromQuery('customerId')]);
        }
        
        if ($id != $request->getPost('id')
            || 'Delete' !== $request->getPost('confirm', 'no')
            ) 
        {
            return $this->redirect()->toRoute('admin');
        }
            
        if ($container->currentUserId != 1) {
            $project = $this->adminDb->deleteProject($project);
        }        
        
        return $this->redirect()->toRoute(
             'admin',
             ['action'=>'list-projects', 'id'=>$request->getPost()['customerId']]
             );
    }
}
