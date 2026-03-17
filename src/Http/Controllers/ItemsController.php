<?php

namespace WebApp\Http\Controllers;

use PDO;
use WebApp\Database\Database;
use WebApp\Http\Requests\Request;
use WebApp\Http\Responses\Response;
use WebApp\Models\Item;

/**
 * Class ItemsController
 */
class ItemsController
{
    /**
     * @var Response
     */
    private Response $response;

    /**
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return array
     */
    public function index(): array
    {
        $database = new Database();
        $stmt = $database->query("SELECT * FROM items");
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $database->close();
        return $this->response->jsonResponse("All items", 200, $items);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function create(Request $request): array
    {
        $item = new Item();
        $item->loadData($request->getBody());

        if ($item->validate() && $item->create()) {
            return $this->response->jsonResponse("Item created.", 200, $request->getBody());
        }

        return $this->response->jsonResponse("Item could not be created.", 500, $item->errors, false);
    }

    /**
     * @param Request $request
     * @return mixed|null
     */
    public function show(Request $request)
    {
        return Item::find($_GET['id']);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function update(Request $request): array
    {
        $item = new Item();
        $item->loadData($request->getBody());

        if ($item->validate() && $item->update($request->getBody())) {
            return $this->response->jsonResponse("Item updated", 200, $request->getBody());
        }

        return $this->response->jsonResponse("Could not update data", 403, $item->errors, false);
    }

    /**
     * @return array
     */
    public function delete(): array
    {
        if (Item::delete($_GET['id']) ) {
            return $this->response->jsonResponse("Item deleted.", 200, []);
        }
        return $this->response->jsonResponse("Could not delete the item.", 200, []);
    }
}