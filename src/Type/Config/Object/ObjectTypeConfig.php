<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:32 AM
*/

namespace Youshido\GraphQL\Type\Config\Object;

use Youshido\GraphQL\Type\Config\Config;
use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Config\Traits\ArgumentsAwareTrait;
use Youshido\GraphQL\Type\Config\Traits\FieldsAwareTrait;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\TypeService;

class ObjectTypeConfig extends Config implements TypeConfigInterface
{

    use FieldsAwareTrait, ArgumentsAwareTrait;

    public function getRules()
    {
        return [
            'name'        => ['type' => TypeMap::TYPE_STRING, 'required' => true],
            'description' => ['type' => TypeMap::TYPE_STRING],
            'fields'      => ['type' => TypeService::TYPE_ARRAY_OF_FIELDS, 'final' => true],
            'args'        => ['type' => TypeService::TYPE_ARRAY_OF_INPUTS],
            'resolve'     => ['type' => TypeService::TYPE_FUNCTION],
            'interfaces'  => ['type' => TypeService::TYPE_ARRAY_OF_INTERFACES]
        ];
    }

    protected function build()
    {
        $this->buildFields();
        $this->buildArguments();
    }

    public function getInterfaces()
    {
        return $this->get('interfaces', []);
    }

}
