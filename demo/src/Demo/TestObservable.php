<?php
/**
 * Created by PhpStorm.
 * User: pierrecassat
 * Date: 22/02/15
 * Time: 19:22
 */

namespace Demo;

use \EventManager\AbstractObservable;
use \EventManager\ObservableInterface;

class TestObservable
    extends AbstractObservable
    implements ObservableInterface
{

    public function __construct($name = 'Anonymous')
    {
        parent::__construct();
        $this->setName($name);
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

}
