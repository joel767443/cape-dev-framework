<?php

namespace WebApp\Models;

use WebApp\Database\Database;

/**
 *
 */
class Item extends Model
{
    protected static string $table = 'items';

    /**
     * @var
     */
    public $name;
    /**
     * @var
     */
    public $description;
    /**
     * @var
     */
    public $brand;
    /**
     * @var
     */
    public $color;
    /**
     * @var
     */
    public $checked;
    /**
     * @var
     */
    public $price;
    /**
     * @var
     */
    public $availability;

    /**
     * @param int $id
     * @return mixed|null
     */
    public static function find(int $id)
    {
        $db = new Database();
        $stmt = $db->query("SELECT * FROM items WHERE id = :id", ['id' => $id]);
        $item = $db->fetchAssoc($stmt);

        if (!$item) {
            return null;
        }

        return $item;
    }

    /**
     * @param array $data
     * @return bool
     */
    public static function update(array $data): bool
    {
        $item = Item::find($data['id']);

        if ($item == null) {
            return false;
        }

        $data = [
            'id' => $data['id'],
            'name' => $data['name'],
            'description' => $data['description'],
            'brand' => $data['brand'],
            'color' => $data['color'],
            'checked' => $data['checked'],
            'price' => $data['price'],
            'availability' => $data['availability'],
        ];

        $db = new Database();
        $db->query("UPDATE items SET 
                            name = :name,
                            description = :description,
                            brand = :brand,
                            color = :color,
                            checked = :checked,
                            price = :price,
                            availability = :availability
                            WHERE id = :id", $data);

        return true;
    }

    /**
     * @param int $id
     * @return true
     */
    public static function delete(int $id): bool
    {
        $db = new Database();
        $db->query("DELETE FROM items WHERE id = :id", ['id' => $id]);
        return true;
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            'name' => [self::RULE_REQUIRED, self::IS_STRING],
            'description' => [self::RULE_REQUIRED, self::IS_STRING],
            'brand' => [self::RULE_REQUIRED],
            'color' => [self::RULE_REQUIRED],
            'price' => [self::RULE_REQUIRED],
            'availability' => [self::RULE_REQUIRED],
        ];
    }
}