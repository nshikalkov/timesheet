<?php
namespace Application\Form;

use Zend\Form\Fieldset;
use Zend\Hydrator\Reflection as ReflectionHydrator;
use Application\Model\Customer;
use Application\Model\Project;

class ProjectFieldset extends Fieldset
{
    public function init()
    {
        $this->setHydrator(new ReflectionHydrator());
        $this->setObject(new Project(''));
        
        $this->add([
            'type' => 'hidden',
            'name' => 'id',
        ]);
        
        $this->add([
            'type' => 'hidden',
            'name' => 'customerId',
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
