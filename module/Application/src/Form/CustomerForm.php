<?php
namespace Application\Form;

use Zend\Form\Form;

class CustomerForm extends Form
{
    public function init()
    {
        $this->add([
            'name' => 'customer',
            'type' => CustomerFieldset::class,
            'options' => [
                'use_as_base_fieldset' => true,
            ],
        ]);
        
        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Insert new Customer',
            ],
        ]);
    }
}