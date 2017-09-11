<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class StreetsController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for streets
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Streets', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "id";

        $streets = Streets::find($parameters);

        if (count($streets) == 0) {
            $this->flash->notice("The search did not find any streets");

            $this->dispatcher->forward([
                "controller" => "streets",
                "action" => "index"
            ]);
            return;
        }

        $frontCache = new Phalcon\Cache\Frontend\Data(array(
            "lifetime" => 172800
        ));
        $cache = new Phalcon\Cache\Backend\File($frontCache, array(
            "cacheDir" => "/var/www/street.local/cache/"
        ));
        $cacheKey = 'streets_order_id.cache';
        $st = $cache->get($cacheKey);
        if ($st === null) {
            $robots = Streets::find(array("order" => "id"));
            $cache->save($cacheKey, $robots);
        }




        $paginator = new Paginator([
            'data' => $streets,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {

    }

    /**
     * Edits a street
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {

            $street = Streets::findFirstByid($id);
            if (!$street) {
                $this->flash->error("street was not found");

                $this->dispatcher->forward([
                    'controller' => "streets",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $street->id;

            $this->tag->setDefault("id", $street->id);
            $this->tag->setDefault("title", $street->title);
            
        }
    }

    /**
     * Creates a new street
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "streets",
                'action' => 'index'
            ]);

            return;
        }

        $street = new Streets();
        $street->title = $this->request->getPost("title");
        

        if (!$street->save()) {
            foreach ($street->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "streets",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("street was created successfully");

        $this->dispatcher->forward([
            'controller' => "streets",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a street edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "streets",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $street = Streets::findFirstByid($id);

        if (!$street) {
            $this->flash->error("street does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "streets",
                'action' => 'index'
            ]);

            return;
        }

        $street->title = $this->request->getPost("title");
        

        if (!$street->save()) {

            foreach ($street->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "streets",
                'action' => 'edit',
                'params' => [$street->id]
            ]);

            return;
        }

        $this->flash->success("street was updated successfully");

        $this->dispatcher->forward([
            'controller' => "streets",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a street
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $street = Streets::findFirstByid($id);
        if (!$street) {
            $this->flash->error("street was not found");

            $this->dispatcher->forward([
                'controller' => "streets",
                'action' => 'index'
            ]);

            return;
        }

        if (!$street->delete()) {

            foreach ($street->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "streets",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("street was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "streets",
            'action' => "index"
        ]);
    }

}
