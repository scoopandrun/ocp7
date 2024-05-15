<?php

// Path: src/Controller/BaseController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{

    /**
     * Checks if the current user has access to perform a specific action.
     *
     * @param mixed  $attribute The attribute representing the action.
     * @param mixed  $subject   The subject on which the action is performed (optional).
     * @param string $message   The error message to be displayed if access is denied (optional).
     * 
     * @throws AccessDeniedException If access is denied.
     */
    protected function checkAccessGranted(
        $attribute,
        $subject = null,
        string $message = 'Access denied.'
    ): void {
        if (!$this->isGranted($attribute, $subject)) {
            throw $this->createAccessDeniedException($message);
        }
    }
}
