<?php
namespace BlogTest;

header('Access-Control-Allow-Credentials: false', true);
header('Access-Control-Allow-Origin: *');
if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    return;
}

use Examples\Blog\Schema\BannerType;
use Examples\Blog\Schema\LikePost;
use Examples\Blog\Schema\PostType;
use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/Schema/PostType.php';
require_once __DIR__ . '/Schema/PostStatus.php';
require_once __DIR__ . '/Schema/ContentBlockInterface.php';
require_once __DIR__ . '/Schema/LikePost.php';
require_once __DIR__ . '/Schema/BannerType.php';

if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json') {
    $rawBody     = file_get_contents('php://input');
    $requestData = json_decode($rawBody ?: '', true);
} else {
    $requestData = $_POST;
}

$payload   = isset($requestData['query']) ? $requestData['query'] : null;
$variables = isset($requestData['variables']) ? $requestData['variables'] : null;

$rootQueryType = new ObjectType([
    'name'   => 'RootQueryType',
    'fields' => [
        'latestPost' => new PostType(),
        'randomBanner' => new BannerType(),
    ]
]);

$rootMutationType = new ObjectType([
    'name'   => 'RootMutationType',
    'fields' => [
        'likePostInline' => [
            'type'    => new PostType(),
            'args'    => [
                'id' => new NonNullType(new IntType())
            ],
            'resolve' => function ($value, $args, $type) {
                // increase like count
                return $type->resolve($value, $args);
            },
        ],
        'likePost' => new LikePost()
    ]
]);

$processor = new Processor();
$schema    = new Schema([
    'query' => $rootQueryType,
    'mutation' => $rootMutationType,
]);
$processor->setSchema($schema);

$response = $processor->processRequest($payload, $variables)->getResponseData();

header('Content-Type: application/json');
echo json_encode($response);
