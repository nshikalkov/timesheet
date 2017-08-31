<?php
namespace Application\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\AdminDbHandler;
use Zend\Db\Adapter\AdapterInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Hydrator\Reflection as ReflectionHydrator;
use Application\Model\Customer;
use Application\Model\Project;


class AdminDbHandlerFactory implements FactoryInterface 
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AdminDbHandler(
            $container->get(AdapterInterface::class),
            new ReflectionHydrator(),
            new Customer(''),
            new Project('')
            );
    }
}
