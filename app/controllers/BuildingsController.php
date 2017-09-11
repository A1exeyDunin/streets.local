<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class BuildingsController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for buildings
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Buildings', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "id";

        $buildings = Buildings::find($parameters);
        if (count($buildings) == 0) {
            $this->flash->notice("The search did not find any buildings");

            $this->dispatcher->forward([
                "controller" => "buildings",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $buildings,
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
     * Edits a building
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {

            $building = Buildings::findFirstByid($id);
            if (!$building) {
                $this->flash->error("building was not found");

                $this->dispatcher->forward([
                    'controller' => "buildings",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $building->id;

            $this->tag->setDefault("id", $building->id);
            $this->tag->setDefault("street_id", $building->street_id);
            $this->tag->setDefault("title", $building->title);
            $this->tag->setDefault("number", $building->number);
            
        }
    }

    /**
     * Creates a new building
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "buildings",
                'action' => 'index'
            ]);

            return;
        }

        $building = new Buildings();
        $building->street_id = $this->request->getPost("street_id");
        $building->title = $this->request->getPost("title");
        $building->number = $this->request->getPost("number");
        

        if (!$building->save()) {
            foreach ($building->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "buildings",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("building was created successfully");

        $this->dispatcher->forward([
            'controller' => "buildings",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a building edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "buildings",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $building = Buildings::findFirstByid($id);

        if (!$building) {
            $this->flash->error("building does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "buildings",
                'action' => 'index'
            ]);

            return;
        }

        $building->street_id = $this->request->getPost("street_id");
        $building->title = $this->request->getPost("title");
        $building->number = $this->request->getPost("number");
        

        if (!$building->save()) {

            foreach ($building->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "buildings",
                'action' => 'edit',
                'params' => [$building->id]
            ]);

            return;
        }

        $this->flash->success("building was updated successfully");

        $this->dispatcher->forward([
            'controller' => "buildings",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a building
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $building = Buildings::findFirstByid($id);
        if (!$building) {
            $this->flash->error("building was not found");

            $this->dispatcher->forward([
                'controller' => "buildings",
                'action' => 'index'
            ]);

            return;
        }

        if (!$building->delete()) {

            foreach ($building->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "buildings",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("building was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "buildings",
            'action' => "index"
        ]);
    }

}
