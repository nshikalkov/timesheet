<?php
namespace Application\Form;

use Zend\Form\Form;

class ProjectForm extends Form
{
    public function init()
    {
        $this->add([
            'name' => 'project',
            'type' => ProjectFieldset::class,
            'options' => [
                'use_as_base_fieldset' => true,
            ],
        ]);
        
        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Insert new Project',
            ],
        ]);
    }
}