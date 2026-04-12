<?php
namespace Controllers;

class PartialController extends Controller
{
    public function layout(): void
    {
        app_start_session();
        session_write_close();
        $this->render('partials/layout');
    }

    public function adminLayout(): void
    {
        app_start_session();
        session_write_close();
        $this->render('partials/admin-layout');
    }
}
