<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Login;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;   

class Backend extends AbstractController
{

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/backend", name="grab") methods={"GET","POST"}
     */
    public function index()
    {
        $session = new Session();

        $request = Request::createFromGlobals(); // the envelope, and were looking inside it.

        $type = $request->request->get('type', 'none'); // to send ourself in different directions
        
        if($type == 'register')
        {
            // perform register process
            
            // get the variables
            $username = $request->request->get('username', 'none');
            $password = $request->request->get('password', 'none');
            $usertype = $request->request->get('usertype', 'none');
            $name = $request->request->get('name', 'none');
            $address = $request->request->get('address', 'none');




            // put in the database            
            $entityManager = $this->getDoctrine()->getManager();

              $login = new Login();
              $login->setUsername($username);
              $login->setPassword($password);
              $login->setUsertype($usertype);
              $login->setName($name);
              $login->setAddress($address);



              $entityManager->persist($login);

             // actually executes the queries (i.e. the INSERT query)
             $entityManager->flush();
             return new Response($login->getUsername());
            
        }
        else if($type == 'login')
        { // if we had a login
            
            
            // get the username and password
            $username = $request->request->get('username', 'none');
            $password = $request->request->get('password', 'none');



             $repo = $this->getDoctrine()->getRepository(Login::class); // the type of the entity
             $person = $repo->findOneBy(['username' => $username,'password' => $password,]);

             if(!$person) {

                 $content = "";
                 $content .= "<style>";
                 $content .= "#result1{background-color:yellow;}";
                 $content .= "#result2{color:blue;}";
                 $content .= "</style>";
                 $content .= "</br>";
                 $content .= "<div id=activation-success-msg>";
                 $content .= "<b>You are not a registered user, please register for an account</b>";
                 $content .= "</div>";


                 return new Response( $content );
             }

             else {

                 // stores an attribute in the session for later reuse
                 $user_id = $person->getId();
                 $session->set('userId', $user_id);

                 // gets an attribute by name
                 $usersession = $session->get('userId');

                 return new Response( $person->getUsertype () );

             }
        }
        
    }
    
}   
