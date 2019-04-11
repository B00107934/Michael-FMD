<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Login;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;        
        
class CatchTheData extends AbstractController
{
    /**
     * @Route("/catchTheData", name="catch") methods={"GET","POST"}
     */
    public function index()
    {
        $request = Request::createFromGlobals(); // the envelope, and were looking inside it.

        $type = $request->request->get('type', 'none');
        if($type == 'checkifusernameexists'){
            
             // catch the username
             $name = $request->request->get('un', 'this is the default word');
            
            
             $repository = $this->getDoctrine()->getRepository(Login::class);

          
             $user = $repository->findOneBy(['username' => $name]);
             if($user){
                         return new Response(
                     'user exists!'
                    );
             } else {
        
                    return new Response(
                        'doesnt exist'
                    );
             } 
            
        }
        else if($type == 'insertnewuser'){

        $name = $request->request->get('un', 'this is the default word');
        
      
        
        // to work the objects
        $entityManager = $this->getDoctrine()->getManager();

        // create blank entity of type "Login"
        $login = new Login();
        
        $login->setUsername($name);
        $login->setPassword('123');


      
        $entityManager->persist($login);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        
       
        return new Response(
            'it went into the database'
        );

       }
        
         return new Response(
            'it went into the database'
        );
    }
}
