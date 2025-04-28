<?php
// src/Controller/EmployeeController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends AbstractController
{
    public function dashboard(): Response
    {
        return $this->render('employee/dashboard.html.twig');
    }
}
