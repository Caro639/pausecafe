<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/modif', name: 'admin_modify_')]
class MainProductController extends AbstractController
{
    #[Route('/', name: 'index.modify')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'MainProductController',
        ]);
    }
}
