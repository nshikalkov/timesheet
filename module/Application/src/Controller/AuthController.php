<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController
{
    public function indexAction()
    {
        $request   = $this->getRequest();
        
        if (! $request->isPost()) {
            $this->layout()->setTemplate('layout/layout2');
            return new ViewModel();
        }
        
        $container = new Container('auth');
        
        if (!isset($container->currentUserId)) {
            $container->currentUserId = 2;
        } elseif ($container->currentUserId == 0) {
            $container->currentUserId = 2;
        }
        
        return $this->redirect()->toRoute(
            'home',
            []
            );
    }
    
    public function demoAction()
    {
        $container = new Container('auth');
        
        $container->currentUserId = 1;
        
        return $this->redirect()->toRoute(
            'home',
            []
            );
    }
    
    public function logoutAction()
    {
        $container = new Container('auth');
        
        $container->currentUserId = 0;
        
        return $this->redirect()->toRoute(
            'home',
            []
            );
    }
    
}