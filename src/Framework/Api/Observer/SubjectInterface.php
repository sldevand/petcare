<?php

namespace Framework\Api\Observer;

/**
 * Interface SubjectInterface
 * @package Framework\Api\Observer
 */
interface SubjectInterface
{
    /**
     * @param ObserverInterface $observer
     */
    public function attach(ObserverInterface $observer);

    /**
     * @param ObserverInterface $observer
     */
    public function detach(ObserverInterface $observer);

    public function getState();

    /**
     * @param $state
     */
    public function setState($state);

    public function notify();

    /**
     * @return array
     */
    public function getObservers();
}
