<?php

namespace WebApp\Models;

use WebApp\Http\Requests\BaseRequest;

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
     * @return array[]
     */
    public function rules(): array
    {
        return [
            'name' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_STRING],
            'description' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_STRING],
            'brand' => [BaseRequest::RULE_REQUIRED],
            'color' => [BaseRequest::RULE_REQUIRED],
            'price' => [BaseRequest::RULE_REQUIRED],
            'availability' => [BaseRequest::RULE_REQUIRED],
            'checked' => [BaseRequest::RULE_REQUIRED, BaseRequest::IS_INT],
        ];
    }
}