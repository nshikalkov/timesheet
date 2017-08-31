<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Application\Model\AdminDbInterface;
use Application\Form\CustomerForm;


class AdminController extends AbstractActionController
{
    private $adminDb;
    private $customerForm;
    
    public function __construct(AdminDbInterface $adminDb, CustomerForm $customerForm)
    {
        $this->adminDb = $adminDb;
        $this->customerForm = $customerForm;
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
        
        return new ViewModel([
            'projects' => $this->adminDb->listProjects($id,$container->currentUserId),
            'customerName' => 'Test 123',
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
        $viewModel = new ViewModel(['form' => $this->customerForm]);
        
        if (! $request->isPost()) {
            return $viewModel;
        }
        
//         $this->customerForm->setData($request->getPost());
        
//         if (! $this->customerForm->isValid()) {
//             return $viewModel;
//         }
        
//         $customer = $this->customerForm->getData();
        
//         $customer->setAppUserId($container->currentUserId);
//         $customer->setStatus('active');
        
//         if ($container->currentUserId != 1) {
//             try {
//                 $customer = $this->adminDb->insertCustomer($customer);
//             } catch (\Exception $ex) {
//                 // An exception occurred; we may want to log this later and/or
//                 // report it to the user. For now, we'll just re-throw.
//                 throw $ex;
//             }
//         }
        
//         return $this->redirect()->toRoute(
//             'admin',
//             ['action'=>'listProjects', 'id'=>$project->getCustomerId()]
//             );
        return [];
    }
    
    public function editProjectAction()
    {
    }
    
    public function deleteProjectAction()
    {
    }
}