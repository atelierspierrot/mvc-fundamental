<?php
/**
 * @see <http://github.com/atelierspierrot/mvc-fundamental>.
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

    public function loremipsumAction()
    {
        return 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.';
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
        $content = 'I received data: <ul>';
        foreach ($data as $var=>$val) {
            $content .= '<li>'.$var.' : '.$val.'</li>';
        }
        $content .= '</ul>';
        return $content;
    }

    public function composePartsAction(TemplateEngineInterface $template_engine, FrontControllerInterface $app)
    {
        $parts = array();
        $req = $app->get('request')->getBaseUrl();
        $parts['title'] = 'A composite rendering';
        $parts['breadcrumb'] = array(
            'home'=>$req.'/',
            'compose_parts'=>$req.'/compose_parts'
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
            'compose_layout'=>$req.'/compose_layout'
        );
        return $layout
            ->setLayout(self::$views_dir.'layout.php')
            ->setChild('left_block', self::$views_dir.'left_block.php')
            ->setChild('right_block', self::$views_dir.'left_block.php')
            ->setChild('content', self::$views_dir.'lorem_ipsum_content.php')
            ->renderLayout($params);
    }

    public function defaultLayoutAction(TemplateEngineInterface $template_engine, FrontControllerInterface $app)
    {
        $layout     = $template_engine->getDefaultLayout();
        $req        = $app->get('request')->getBaseUrl();
        $content    = $template_engine->renderTemplate(self::$views_dir.'lorem_ipsum_content.php');
        $content    .= $app->callControllerAction(null, 'loremipsum');
        $content    .= $app->callRoute('/hello/your-name', array('name'=>'your-new-name'));
        $layout
            ->addParam('title', 'My test layout')
            ->addParam('hat', 'a simple bootstrap canvas')
            ->addParam('logo', 'http://lorempixel.com/400/200/')
//            ->addParam('home_link', $req.'/')
            ->addParam('menu', array(
                'home'=>$req.'/',
                'item 1'=>'#',
                'item 2'=>'#',
            ))
            ->addParam('breadcrumb', array(
                'home'=>$req.'/',
                'default_layout'=>$req.'/default_layout'
            ))
            ->addParam('messages', array(
                'This is a system message ...',
                'danger'=>'this is a system "danger" message'
            ))
            ->setChildParam('content', 'title', 'Global test content')
            ->setChildParam('content', 'content', $content)
            ->setChildParam('aside', 'title', 'Test aside column')
            ->setChildParam('aside', 'content',
                $template_engine->renderTemplate(self::$views_dir.'left_block.php'))
            ->setChildParam('extra', 'title', 'Test extra column')
            ->setChildParam('extra', 'content',
                $template_engine->renderTemplate(self::$views_dir.'left_block.php'))
            ->setChildParam('footer', 'content', 'My test footer info ...')
            ->setChildParam('footer', 'content_left', 'My test left footer info ...')
            ->setChildParam('footer', 'content_right', 'My test right footer info ...')
            ;
        return $layout->renderLayout();
    }

    public function callcontrolleractionAction(FrontControllerInterface $app)
    {
        return $app->callControllerAction(null, 'loremipsum');
    }

    public function callrouteAction(FrontControllerInterface $app)
    {
        return $app->callRoute('/hello/your-name', array('name'=>'your-new-name'));
    }

}

// Endfile