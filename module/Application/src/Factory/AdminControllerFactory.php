<?php
namespace Application\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Controller\AdminController;
use Application\Model\AdminDbInterface;
use Application\Form\CustomerForm;

class AdminControllerFactory implements FactoryInterface 
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $formManager = $container->get('FormElementManager');
        
        return new AdminController(
            $container->get(AdminDbInterface::class),
            $formManager->get(CustomerForm::class)
            );
    }
}