<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Entity\User;
use User\Form\UserForm;
use User\Form\PasswordChangeForm;
use User\Form\PasswordResetForm;

/**
 * This controller is responsible for user management (adding, editing,
 * viewing users and changing user's password).
 */
class UserController extends AbstractActionController
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * User manager.
     * @var User\Service\UserManager
     */
    private $userManager;

    /**
     * View Helper.
     * @var User\Service\urlHelper
     */
    private $basePath;

    /**
     * Constructor.
     */
    public function __construct($entityManager, $userManager, $basePath)
    {
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
        $this->basePath = $basePath;
    }

    /**
     * This is the default "index" action of the controller. It displays the
     * list of users.
     */
    public function indexAction()
    {
        $users = $this->entityManager->getRepository(User::class)
                ->findBy([], ['id'=>'ASC']);

        return new ViewModel([
            'users' => $users
        ]);
    }

    /**
     * This action displays the "Reset Password" page.
     */
    public function resetPasswordAction()
    {
        // Create form
        $form = new PasswordResetForm($this->basePath);

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Look for the user with such email.
                $user = $this->entityManager->getRepository(User::class)
                        ->findOneByEmail($data['email']);
                if ($user!=null) {
                    // Generate a new password for user and send an E-mail
                    // notification about that.
                    $this->userManager->generatePasswordResetToken($user);

                    // Redirect to "message" page
                    return $this->redirect()->toRoute('users',
                            ['action'=>'message', 'id'=>'sent']);
                } else {
                    return $this->redirect()->toRoute('users',
                            ['action'=>'message', 'id'=>'invalid-email']);
                }
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    /**
     * This action displays an informational message page.
     * For example "Your password has been resetted" and so on.
     */
    public function messageAction()
    {
        // Get message ID from route.
        $id = (string)$this->params()->fromRoute('id');

        // Validate input argument.
        if($id!='invalid-email' && $id!='sent' && $id!='set' && $id!='failed') {
            throw new \Exception('Invalid message ID specified');
        }

        return new ViewModel([
            'id' => $id
        ]);
    }

    /**
     * This action displays the "Reset Password" page.
     */
    public function setPasswordAction()
    {
        $token = $this->params()->fromQuery('token', null);

        // Validate token length
        if ($token!=null && (!is_string($token) || strlen($token)!=32)) {
            throw new \Exception('Invalid token type or length');
        }

        if($token===null ||
           !$this->userManager->validatePasswordResetToken($token)) {
            return $this->redirect()->toRoute('users',
                    ['action'=>'message', 'id'=>'failed']);
        }

        // Create form
        $form = new PasswordChangeForm('reset');

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                $data = $form->getData();

                // Set new password for the user.
                if ($this->userManager->setNewPasswordByToken($token, $data['new_password'])) {

                    // Redirect to "message" page
                    return $this->redirect()->toRoute('users',
                            ['action'=>'message', 'id'=>'set']);
                } else {
                    // Redirect to "message" page
                    return $this->redirect()->toRoute('users',
                            ['action'=>'message', 'id'=>'failed']);
                }
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    /**
     * The "settings" action displays the info about currently logged in user.
     */
    public function settingsAction()
    {
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($this->identity());

        if ($user==null) {
            throw new \Exception('Not found user with such email');
        }

        return new ViewModel([
            'user' => $user
        ]);
    }
}


