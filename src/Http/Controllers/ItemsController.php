<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

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
        $data = $request->getBody();
        $item = new Item();
        $item->loadData($data);

        if (!$request->validate($item->rules(), $data)) {
            return $this->response->jsonResponse("Item could not be created.", 422, $request->errors, false);
        }

        if ($item->create()) {
            return $this->response->jsonResponse("Item created.", 200, $data);
        }

        return $this->response->jsonResponse("Item could not be created.", 500, [], false);
    }

    /**
     * @param Request $request
     * @return mixed|null
     */
    public function show(Request $request)
    {
        $id = $_GET['id'] ?? null;
        if ($id === null || !is_numeric($id)) {
            return $this->response->jsonResponse("Invalid id.", 422, [], false);
        }

        $item = Item::find((int) $id);
        if ($item === null) {
            return $this->response->jsonResponse("Item not found.", 404, [], false);
        }

        return $item;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function update(Request $request): array
    {
        $data = $request->getBody();
        $item = new Item();
        $item->loadData($data);

        if (!$request->validate($item->rules(), $data)) {
            return $this->response->jsonResponse("Could not update data", 422, $request->errors, false);
        }

        if (Item::update($data)) {
            return $this->response->jsonResponse("Item updated", 200, $data);
        }

        return $this->response->jsonResponse("Could not update data", 403, [], false);
    }

    /**
     * @return array
     */
    public function delete(): array
    {
        $id = $_GET['id'] ?? null;
        if ($id === null || !is_numeric($id)) {
            return $this->response->jsonResponse("Invalid id.", 422, [], false);
        }

        if (Item::delete((int) $id)) {
            return $this->response->jsonResponse("Item deleted.", 200, []);
        }
        return $this->response->jsonResponse("Could not delete the item.", 500, [], false);
    }
}