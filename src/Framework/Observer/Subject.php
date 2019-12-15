<?php
/**
 * Created by PhpStorm.
 * User: sebastien
 * Date: 15/12/19
 * Time: 17:00
 */

namespace Framework\Observer;

use Framework\Api\Observer\ObserverInterface;
use Framework\Api\Observer\SubjectInterface;

/**
 * Class Subject
 * @package Framework\Observer
 */
abstract class Subject implements SubjectInterface
{
    /** @var array */
    protected $observers;

    /** @var mixed */
    protected $state;

    /**
     * Subject constructor.
     */
    public function __construct()
    {
        $this->observers = array();
        $this->state = null;
    }

    /**
     * @param ObserverInterface $observer
     */
    public function attach(ObserverInterface $observer)
    {
        $i = array_search($observer, $this->observers);
        if ($i === false) {
            $this->observers[] = $observer;
        }
    }

    /**
     * @param ObserverInterface $observer
     */
    public function detach(ObserverInterface $observer)
    {
        if (!empty($this->observers)) {
            $i = array_search($observer, $this->observers);
            if ($i !== false) {
                unset($this->observers[$i]);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param $state
     */
    public function setState($state)
    {
        $this->state = $state;
        $this->notify();
    }

    /**
     * @return void
     */
    public function notify()
    {
        if (!empty($this->observers)) {
            foreach ($this->observers as $observer) {
                $observer->update($this);
            }
        }
    }

    /**
     * @return array
     */
    public function getObservers()
    {
        return $this->observers;
    }
}
