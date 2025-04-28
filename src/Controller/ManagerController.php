<?php
// src/Controller/ManagerController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ManagerController extends AbstractController
{
    public function dashboard(): Response
    {
        return $this->render('manager/dashboard.html.twig');
    }
}
