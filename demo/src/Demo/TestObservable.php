<?php
/**
 * Created by PhpStorm.
 * User: pierrecassat
 * Date: 22/02/15
 * Time: 19:22
 */

namespace Demo;

use \Library\Event\AbstractObservable;
use \Library\Event\ObservableInterface;

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
