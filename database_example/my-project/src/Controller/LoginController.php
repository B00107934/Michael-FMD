<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Login;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function index()
    {
        /*
        // This blob puts a new record in the database
        $entityManager = $this->getDoctrine()->getManager();

        $login = new Login();
        $login->setUsername('john');
        $login->setPassword('12345678');
	
      
        $entityManager->persist($login);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        */
        // this bit just renders a template after it does the insert statement.
        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
        ]);
    }
}
