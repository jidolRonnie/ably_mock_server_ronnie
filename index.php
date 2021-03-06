<?php


require './pdos/DatabasePdo.php';
require './pdos/IndexPdo.php';
require './vendor/autoload.php';

use \Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;

date_default_timezone_set('Asia/Seoul');
ini_set('default_charset', 'utf8mb4');

//에러출력하게 하는 코드
//error_reporting(E_ALL); ini_set("display_errors", 1);

//Main Server API
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    /* ******************   Test   ****************** */
//    $r->addRoute('GET', '/', ['IndexController', 'index']);
//    $r->addRoute('GET', '/test', ['IndexController', 'test']);
//    $r->addRoute('GET', '/test/{testNo}', ['IndexController', 'testDetail']);
//    $r->addRoute('POST', '/test', ['IndexController', 'testPost']);
//    $r->addRoute('GET', '/jwt', ['MainController', 'validateJwt']);
//    $r->addRoute('POST', '/jwt', ['MainController', 'createJwt']);

//    에이블리

    // User
    $r->addRoute('POST', '/signUp', ['UserController', 'createUser']);
    $r->addRoute('POST', '/signIn', ['UserController', 'createLogin']);
    $r->addRoute('GET', '/signIn', ['UserController', 'validJwt']);
    $r->addRoute('GET', '/users', ['UserController', 'getUsers']);

    // Product
    $r->addRoute('GET', '/banner', ['ProductController', 'getBanner']);
    $r->addRoute('GET', '/recommended-products', ['ProductController', 'getRecommendedProducts']);
    $r->addRoute('GET', '/new-products', ['ProductController', 'getNewProducts']);
    $r->addRoute('GET', '/new-products/best', ['ProductController', 'getNewBestProducts']);
    $r->addRoute('GET', '/products/{productIdx}', ['ProductController', 'getProductDetail']);
    $r->addRoute('GET', '/products/{productIdx}/options', ['ProductController', 'getOptions']);
    $r->addRoute('POST', '/product-hearts/{productIdx}', ['ProductController', 'createProductHearts']);
    $r->addRoute('POST', '/drawers', ['ProductController', 'createDrawer']);
    $r->addRoute('GET', '/drawers', ['ProductController', 'getDrawers']);
    $r->addRoute('GET', '/drawers/{drawerIdx}', ['ProductController', 'getDrawerDetail']);
    $r->addRoute('DELETE', '/drawers/{drawerIdx}', ['ProductController', 'deleteDrawer']);

    // Order
    $r->addRoute('POST', '/orders', ['OrderController', 'createOrder']);
    $r->addRoute('GET', '/orders', ['OrderController', 'getOrders']);
    $r->addRoute('GET', '/orders/{orderNum}', ['OrderController', 'getOrderDetail']); // 주문 상세 조회, 작업 중
    $r->addRoute('PATCH', '/orders/{orderNum}/status', ['OrderController', 'modifyOrderStatus']);










//    $r->addRoute('GET', '/users', 'get_all_users_handler');
//    // {id} must be a number (\d+)
//    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
//    // The /{title} suffix is optional
//    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// 로거 채널 생성
$accessLogs = new Logger('ACCESS_LOGS');
$errorLogs = new Logger('ERROR_LOGS');
// log/your.log 파일에 로그 생성. 로그 레벨은 Info
$accessLogs->pushHandler(new StreamHandler('logs/access.log', Logger::INFO));
$errorLogs->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));
// add records to the log
//$log->addInfo('Info log');
// Debug 는 Info 레벨보다 낮으므로 아래 로그는 출력되지 않음
//$log->addDebug('Debug log');
//$log->addError('Error log');

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        echo "404 Not Found";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        echo "405 Method Not Allowed";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        switch ($routeInfo[1][0]) {
            case 'IndexController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/IndexController.php';
                break;
            case 'MainController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/MainController.php';
                break;
            case 'UserController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/UserController.php';
                break;
            case 'ProductController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/ProductController.php';
                break;
            case 'OrderController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/OrderController.php';
                break;
//            case 'ReviewController':
//                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
//                require './controllers/ReviewController.php';
//                break;
//            case 'ElementController':
//                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
//                require './controllers/ElementController.php';
//                break;
//            case 'AskFAQController':
//                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
//                require './controllers/AskFAQController.php';
//                break;
        }

        break;
}
