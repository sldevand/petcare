<?php

namespace Framework\Api\Observer;

/**
 * Interface ObserverInterface
 * @package Framework\Api\Observer
 */
interface ObserverInterface
{
    /**
     * @param SubjectInterface $subject
     * @return mixed
     */
    public function update(SubjectInterface $subject);
}
