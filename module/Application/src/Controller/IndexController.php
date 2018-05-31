<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Constructor. Its purpose is to inject dependencies into the controller.
     */
    public function __construct($entityManager)
    {
       $this->entityManager = $entityManager;
    }

    /**
     * This is the default "index" action of the controller. It displays the
     * Home page.
     */
    public function indexAction()
    {
        return new ViewModel();
    }

    /**
     * This is the "about" action. It is used to display the "About" page.
     */
    public function aboutAction()
    {
        $appName = 'DSS';
        $appDescription = 'This app shows how to implement login authentication';

        // Return variables to view script with the help of
        // ViewObject variable container
        return new ViewModel([
            'appName' => $appName,
            'appDescription' => $appDescription
        ]);
    }
}
