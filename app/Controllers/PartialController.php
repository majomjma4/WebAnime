<?php
namespace Controllers;

class PartialController extends Controller
{
    public function layout(): void
    {
        $this->render('partials/layout');
    }

    public function adminLayout(): void
    {
        $this->render('partials/admin-layout');
    }
}
