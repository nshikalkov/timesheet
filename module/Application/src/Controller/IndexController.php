<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $notLoggedIn = true;
        $isDemo = false;
        
        $container = new Container('auth');
        
        if (isset($container->currentUserId) && $container->currentUserId > 0) {
            $notLoggedIn = false;
            if ($container->currentUserId == 1) {
                $isDemo = true;
            }
        } else {
            $this->layout()->setTemplate('layout/layout2');
        }
        
        return new ViewModel(['notLoggedIn'=>$notLoggedIn, 'isDemo'=>$isDemo]);
    }
}
