<?php

$appController = new \App\Controllers\Main($view, $conn);
$loginController = new \App\Controllers\Login($view, $conn);
$userController = new \App\Controllers\User($view, $conn);
$notificationController = new \App\Controllers\Notification($view, $conn);

$authorisedMiddleware = new \App\Middleware\Authorised();

$router->get("/", [$appController, 'index']);
$router->get("/login", [$loginController, 'showLoginForm']);
$router->post("/login", [$loginController, 'handleLoginForm']);

$router->get("/signup", [$loginController, 'showSignupForm']);
$router->post("/signup", [$loginController, 'handleSignUpForm']);

$router->middleware([$authorisedMiddleware])->group(function (\Framework\Router $r) use ($userController, $notificationController) {
    $r->get("/user", [$userController, 'test']);
    $r->get("/notification", [$notificationController, 'sendEmail']);
});

$router->get("/subscribe", function () use ($view) {
    $content = $view->render("layout/base", [
        'header' => $view->render("layout/header"),
        'content' => $view->render("layout/subscribe"),
        'footer' => $view->render("layout/footer"),
    ]);

    echo $content;
});

$router->post("/subscribe", function () {
    $email = $_POST["email"] ?? throw new \App\Exceptions\NotFoundException();
    file_put_contents(__DIR__."/../storage/app/emails.txt", $email, FILE_APPEND);
});


$router->set404Handler(function () {
    echo "404 not found";
});
