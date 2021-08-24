<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

    /**
     * @Route("/admin")
     */
class AdminController extends AbstractController
{
    /**
     * @Route("", name="app_admin_index")
     */
    public function index(): Response
    {
        // $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Oops Nope'); //2 ème manière de gérer la partie admin (cf security.yaml)
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/pins", name="app_admin_pins_index")
     */
    public function pinsIndex(): Response
    {
        // $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/pin_index.html.twig');
    }
}
