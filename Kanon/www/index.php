<?php declare(strict_types=1);    
require_once("../includeApp.php");
KDebugger::enable();

KRoute::launchDefaultRoute(
    homePageController: new PublicHomePage(),
    accessMiddleware:new test_middleware(),
    homeLayout:  new PublicLayout()
);

//#### ENTER YOUR CODE HERE


KRoute::get(RoutesItems::$COMPONENTS,function()
{
    KApp::getInstance()->setLayout(new PublicLayout());
})->controller(new PublicComponents());

KRoute::get(RoutesItems::$DOCUMENTATION,function()
{
    KApp::getInstance()->setLayout(new PublicLayout());
})->controller(new PublicDocumentation());

KRoute::get(RoutesItems::$PROJECTS,function()
{
    KApp::getInstance()->setLayout(new PublicLayout());
})->controller(new PublicProjects());

//#### END OF YOUR CODE

KRoute::endDefaultRoute();           
