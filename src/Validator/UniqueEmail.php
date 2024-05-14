<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueEmail extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'There is already an account with the email address \'{{ value }}\'.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
