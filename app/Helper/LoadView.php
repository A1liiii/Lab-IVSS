<?php

class LoadView {

    public static function user($role, $view, $data = []) {
        extract($data);

        $viewPath = __DIR__ . '/../Views/' . $view;
        $layout   = __DIR__ . '/../Views/layouts/layout_user.php';

        include $layout;
    }

    public static function public($view, $data = []) {
        extract($data);

        $viewPath = __DIR__ . '/../Views/' . $view;
        $layout   = __DIR__ . '/../Views/layouts/layout_public.php';

        include $layout;
    }
}
