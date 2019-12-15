<?php

namespace Framework\Observer;

use Framework\Api\Observer\ObserverInterface;

/**
 * Class Observer
 * @package Framework\Observer
 */
class Observer implements ObserverInterface
{
    /**
     * Observer constructor.
     * @param null $subject
     */
    public function __construct($subject = null)
    {
        if (is_object($subject) && $subject instanceof Subject) {
            $subject->attach($this);
        }
    }

    /**
     * @param \Framework\Api\Observer\SubjectInterface $subject
     * @return mixed|void
     */
    public function update($subject)
    {
        if (method_exists($this, $subject->getState())) {
            call_user_func_array(array($this, $subject->getState()), array($subject));
        }
    }
}
