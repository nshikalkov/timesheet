<?php
namespace Application\Form;

use Zend\Form\Fieldset;
use Zend\Hydrator\Reflection as ReflectionHydrator;
use Application\Model\Customer;

class CustomerFieldset extends Fieldset
{
    public function init()
    {
        $this->setHydrator(new ReflectionHydrator());
        $this->setObject(new Customer(''));
        
        $this->add([
            'type' => 'hidden',
            'name' => 'id',
        ]);
        
        $this->add([
            'type' => 'text',
            'name' => 'name',
            'options' => [
                'label' => 'Name',
            ],
        ]);
        
    }
}