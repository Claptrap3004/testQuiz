<?php


namespace quiz;
class App
{
    private string $prefix = '\quiz\\';
    private string $method = 'index';

    private function urlExplode(): array
    {
        $url = ltrim($_GET['url'], '/');
        return explode('/', $url);
    }

    public function loadController(): void
    {

        $helper = []; $helper[] = 'user';$helper[] = 'login';
        $url = isset($_SESSION['UserId']) ? $this->urlExplode() :$helper;
        $file = '../App/Controller/' . ucfirst($url[0]) . 'Controller.php';
        if (file_exists($file)) {
            require $file;
            $controller1 = $this->prefix . ucfirst($url[0]) . 'Controller';
            $controller = new $controller1;

            if (!empty($url[1])) {
                if (method_exists($controller, $url[1])) try {
                    $reflexion = new \ReflectionClass($controller);
                    $this->method = $reflexion->getMethod($url[1])->isPublic() ? $url[1] : 'index';;
                } catch (\ReflectionException $e) {
                }
            }
            $data = isset($url[2]) ? [$url[2]] : [];
            call_user_func_array([$controller, $this->method], $data);
        } else UseCase::WELCOME->getController()->index();
    }


}