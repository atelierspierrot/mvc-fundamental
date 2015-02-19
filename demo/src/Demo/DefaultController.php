<?php
/**
 * This file is part of the CarteBlanche PHP framework.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * License Apache-2.0 <http://github.com/php-carteblanche/carteblanche/blob/master/LICENSE>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Demo;

use \MVCFundamental\FrontController;
use \MVCFundamental\Interfaces\FrontControllerInterface;
use \MVCFundamental\Interfaces\ControllerInterface;
use \MVCFundamental\Interfaces\TemplateEngineInterface;

class DefaultController
    implements ControllerInterface
{

    /**
     * The directory where to search the views files
     */
    static $views_dir = 'src/templates/';

    /**
     * The home page of the controller
     */
    public function indexAction($name)
    {
        return "Hello $name (from ".__METHOD__.")";
    }

    public function mytestAction($name)
    {
        return "Hello $name (from ".__METHOD__.")";
    }

    public function argsAction($name, $id)
    {
        return 'Hello '.$name.' | I received ID='.$id;
    }

    public function altargsAction($id, $name)
    {
        return 'Hello '.$name.' | I received ID='.$id;
    }

    public function myviewAction($name)
    {
        return array(self::$views_dir.'test.php', array(
            'name' => $name.' from '.__METHOD__,
        ));
    }

    public function formAction()
    {
        $content  = FrontController::get('template_engine')
            ->renderTemplate(self::$views_dir.'form.php');
        return FrontController::get('template_engine')
            ->renderDefault($content);
    }

    public function saveFormAction($request)
    {
        $data = $request->getData();
        return 'I received data: '.var_export($data,1);
    }

    public function composePartsAction(TemplateEngineInterface $template_engine, FrontControllerInterface $app)
    {
        $parts = array();
        $req = $app->get('request')->getBaseUrl();
        $parts['title'] = 'A composite rendering';
        $parts['breadcrumb'] = array(
            'home'=>$req.'/',
            'composeparts'=>$req.'/composeparts'
        );
        $parts['left_block'] = $template_engine->renderTemplate(self::$views_dir.'left_block.php');
        $parts['content'] = $template_engine->renderTemplate(self::$views_dir.'lorem_ipsum_content.php');
        return array(self::$views_dir.'layout.php', $parts);
    }

    public function composeLayoutAction(FrontControllerInterface $app)
    {
        $layout = $app->get('layout_item');
        $params = array();
        $req = $app->get('request')->getBaseUrl();
        $params['title'] = 'A layout rendering';
        $params['breadcrumb'] = array(
            'home'=>$req.'/',
            'composelayout'=>$req.'/composelayout'
        );
        return $layout
            ->setLayout(self::$views_dir.'layout.php')
            ->setChild('left_block', self::$views_dir.'left_block.php')
            ->setChild('right_block', self::$views_dir.'left_block.php')
            ->setChild('content', self::$views_dir.'lorem_ipsum_content.php')
            ->renderLayout($params);
    }
}

// Endfile