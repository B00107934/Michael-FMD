<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\OrderItem;
use App\Entity\PurOrder;
use App\Entity\Login;
//use App\Repository\OrderRepository;//
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class OrderController extends AbstractController
{

    /**
     * @Route("/order", name="order") methods={"GET","POST"}
     */
    public function index()
    {
        //   get the cart details and write to database table cartorder, the read the table and render to twig template display cart //
        $request = Request::createFromGlobals (); // the envelope, and were looking inside it.

        $type = $request->request->get ( 'type', 'none' ); // to send ourself in different directions

        $session = new Session();

        if ($type == 'revenue') {

            $datef = ($request->request->get ( 'datef', 'none' ));
            $datet = ($request->request->get ( 'datet', 'none' ));

            $repo = $this->getDoctrine ()->getRepository ( Purorder::class );  // get the responsitory

            $query = $repo->createQueryBuilder ( 'p' )
                ->select ( 'SUM(p.total_price) as turnover' )
                ->where ( 'p.created > :datef' )
                ->andWhere ( 'p.created < :datet' )
                ->setParameter ( 'datef', $datef )
                ->setParameter ( 'datet', $datet )
                //->setParameter('datet' , $datet->format('Y-m-d 00:00:00'));//
                ->getQuery ();
            $turnovers = $query->getResult ();

            $jsonTurnover = array();
            $idp = 0;
            foreach($turnovers as $turnover) {
                $temp = array('revenue' => $turnover);
                $jsonTurnover[$idp++] = $temp;

            }

            return new JsonResponse( $jsonTurnover );//

        }


           // display the address for delivery to the customer after place order button

        if ($type == 'find-addr') {

            $cust_id = $session->get ( 'userId' );
            $repo = $this->getDoctrine ()->getRepository ( Login::class ); // the type of the entity
            $person = $repo->findOneBy ( ['id' => $cust_id,] );
            $cust_shipping = $person->getAddress ();

            return new Response( $cust_shipping );
        }

        //   get the cart details and write to database table cartorder, the read the table and render to twig template display cart //
        $request = Request::createFromGlobals (); // the envelope, and were looking inside it.

        $type = $request->request->get ( 'type', 'none' ); // to send ourself in different directions



        // decode the JSON post from jquery //
        $orderItems = json_decode ($request->request->get ('orderItems' , 'none'));
        $total_cost = ($request->request->get ('totalcost' , 'none'));


        /*$total_cost_num = (int)$total_cost;
        var_dump($total_cost_num);*/

        /*echo'<pre>';
        print_r($total_cost_num);
        echo'</pre>';
        exit(); */

        // create a date format //
        $date = date('Y-m-d H:i:s');
        $datetime = new \DateTime();


        $sess_name = $session->get ( 'userId' );


        //$po = $session->get('po');//

        $repo = $this->getDoctrine ()->getRepository ( Login::class ); // the type of the entity
        $customer = $repo->findOneBy ( ['id' => $sess_name,] );
        $customer_id = $customer->getId();

        //Write the purchase order to database and write the user object (the person ordering) to the User field $purorder->setUser($person);//
        $purorder = new PurOrder();
        //$purorder->setTotalPrice ( $total_cost_num );//
        $purorder->setTotalPrice ( $total_cost );
        $purorder->setStatus ( 'pending' );
        $purorder->setCreated ( $datetime );
        $purorder->setUser ( $customer_id );

        $session->set('po' , $purorder);

        $entityManager = $this->getDoctrine ()->getManager ();
        // write the new purchase order record  to database //
        $entityManager->persist ( $purorder );
        $entityManager->flush ();


        // write the purchase order items to order items table and write the purchase order object to the order_id field
        // extract the cart data from the JSON

        foreach($orderItems as $item) {
            $repo = $this->getDoctrine ()->getRepository ( OrderItem::class ); // the type of the entity

            $po_Id = $purorder->getId();

            $orderitem = new OrderItem();
            $orderitem->setDescription ($item-> name );
            $orderitem->setPrice ( $item-> price );
            $orderitem->setQuantity ($item-> qty );
            $orderitem->setOrd ( $po_Id );
            $entityManager = $this->getDoctrine ()->getManager ();
            // write the new order record  to database //
            $entityManager->persist ( $orderitem );
            $entityManager->flush ();

        }

        return new Response( $po_Id);//


    }



    // display message to customer if order successful

    /**
     * @Route("/order/success", name="success-order") methods={"GET","POST"}
     */
    public function confirmation()
    {
        $session = new Session();
        $po = $session->get( 'po' );
        $OrderID = $po->getId();
        $session->clear();


        return $this->render ( 'order/OrderSuccess.html.twig', ['OrderID' => $OrderID]);
    }



    // display list of previous order to customer when button 'My Order History' buttob pressed in the customer home page

    /**
     * @Route("/order/history", name="previous-order") methods={"GET","POST"}
     */
    public function prevOrder()
    {
        $session = new Session();
        $prevOrder = $session->get( 'userId' );

        $repo = $this->getDoctrine ()->getRepository ( Purorder::class ); // the type of the entity
        $myPrevious = $repo->findBy ( ['user' => $prevOrder,] );

        if (!$myPrevious) {
            throw $this->createNotFoundException(
                'There are no order with the following id: ' . $prevOrder
            );
        }

        //$myPrevious = $myorder->getAddress ();  //

        return $this->render ( 'order/OrderPrevious.html.twig', array('myPrevious' => $myPrevious));
    }



    ///////////////////////////////////////////////////////////////START OF DRIVER FORM CREATION AND PROCESSING ////////////////////////////////////////////


    // create am order list

    /**
     * @Route("/driver/getOrder", name="get-order")
     */

    public function createPOForm(Request $request)
    {

        $driverlist = new Purorder();
        $form = $this->createFormBuilder ( $driverlist )
            //->add ( 'id', TextType::class )//
            ->add ( 'id', TextType::class )
            ->add ( 'total_price', TextType::class )
            ->add ( 'status', TextType::class  )
            ->add ( 'created', TextType::class )
            ->add ( 'user', TextType::class )
            ->add ( 'save', SubmitType::class, array('label' => 'Save') )
            ->getForm ();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $driverlist = $form->getData ();


            $em = $this->getDoctrine ()->getManager ();
            $em->persist ( $driverlist );
            $em->flush ();

            return $this->redirect ( '/driver/viewPO/' . $driverlist->getId() );   // display the id of new object submitted to table

        }

        return $this->render ( 'order/DriverEdit.html.twig', array('form' => $form->createView (),));  //display the orders

    }


    //  create a function to find and display  the detail of the order. This function is called from createPOForm() using the return $this->redirect ( '/driver/viewPO/' .$driverlist->getId() )
    // The twig file has menu to update the ORDER record or view all orders


    /**
     * @Route("/driver/viewPO/{id}", name="view-order") methods={"GET","POST"})
     */
    public function viewPOForm($id) {

        $driverlist = $this->getDoctrine()
            ->getRepository(Purorder:: class)
            ->find($id);

        if (!$driverlist) {
            throw $this->createNotFoundException(
                'There are no order with the following id: ' . $id
            );
        }

        return $this->render(
            'order/viewOrder.html.twig',
            array('driverlist' => $driverlist)
        );

    }



    //  function to display all order on the database, the return renders to a twig file with menu to update order.

    /**
     * @Route("/driver/show" , name="showall-order") methods={"GET","POST"}))
     */
    public function showOrder() {

        $driverlist = $this->getDoctrine()
            ->getRepository(Purorder:: class)
            ->findAll();

        return $this->render(
            'order/dispayOrder.html.twig',
            array('driverlist' => $driverlist)
        );

    }


    // function to update a particular order based on id... the form is updated and submitted to the database. This function is called using a route in the dispayOrder.html.twig file.
    // and updated recorded displayed to the driver.

    /**
     * @Route("/driver/update/{id}", name="driver-update-order") methods={"GET","POST"}))
     */
    public function updateAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();
        $driverlist = $em->getRepository(Purorder:: class)->find($id);

        if (!$driverlist) {
            throw $this->createNotFoundException(
                'There are no orders with the following id: ' . $id
            );
        }

        $form = $this->createFormBuilder($driverlist)
            ->add ( 'id', TextType::class )
            ->add ( 'total_price', TextType::class )
            ->add ( 'status', TextType::class  )
            // ->add ( 'created', TextType::class )//
            ->add ( 'user', TextType::class )
            ->add ( 'save', SubmitType::class, array('label' => 'Save') )
            ->getForm ();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $driverlist = $form->getData();
            $em->flush();

            return $this->redirect ( '/driver/viewPO/'. $id );

        }

        return $this->render(
            'order/editOrder.html.twig',
            array('form' => $form->createView())
        );

    }


    //  function to show customer address

    /**
     * @Route("/driver/address/{user}" , name="show-address") methods={"GET","POST"}))
     */


    public function showAddress($user) {

        $repo = $this->getDoctrine()->getRepository(Login::class);
        $locations = $repo->findBy ( ['id' => $user] );

        // echo'<pre>';
        // print_r($locations);
        // echo'</pre>';
        //exit();


        if (!$locations) {
            throw $this->createNotFoundException (
                'There are no address with the following');

        }

        return $this->render(
            'order/dispayAddr.html.twig',
            array('locations' => $locations)
        );

    }




    //  create a function to display the details of an order for the driver


    /**
     * @Route("/driver/orderdetail/{id}", name="view-order-items") methods={"GET","POST"})
     */
    public function viewOrdItems($id) {

        $driverlist = $this->getDoctrine()
            ->getRepository(OrderItem:: class)
            ->findBy( ['ord' => $id] );

        if (!$driverlist) {
        /*    throw $this->createNotFoundException(
                'There are no order items with the following id: ' . $id
            );*/
        return $this->redirectToRoute('/index#driver', ['error' => 'order deleted']);
        }


        return $this->render(
            'order/viewOrderItems.html.twig',
            array('driverlist' => $driverlist)
        );

    }
    ///////////////////////////////////////////////////////// manager forms start ////////////////////////////////////////////////////////////////////


    // create a form for the manager //

    /**
     * @Route("/mgr/getOrder", name="get-order")
     */

    public function createMgrForm(Request $request)
    {

        $mgrlist = new Purorder();
        $form = $this->createFormBuilder ( $mgrlist )
            //->add ( 'id', TextType::class )//
            ->add ( 'id', TextType::class )
            ->add ( 'total_price', TextType::class )
            ->add ( 'status', TextType::class  )
            ->add ( 'created', TextType::class )
            ->add ( 'user', TextType::class )
            ->add ( 'save', SubmitType::class, array('label' => 'Save') )
            ->getForm ();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $mgrlist = $form->getData ();


            $em = $this->getDoctrine ()->getManager ();
            $em->persist ( $mgrlist );
            $em->flush ();

            return $this->redirect ( '/mgr/viewPO/' . $mgrlist ->getId() );   // display the id of new object submitted to table

        }

        //return $this->render ( 'order/editOrdermgr.html.twig', array('form' => $form->createView (),));  //display the orders//

    }




    /**
     * @Route("/mgr/viewPO/{id}", name="mgr-view") methods={"GET","POST"})
     */
    public function viewMgrForm($id) {

        $mgrlist = $this->getDoctrine()
            ->getRepository(Purorder:: class)
            ->find($id);

        if (!$mgrlist) {
            throw $this->createNotFoundException(
                'There are no order with the following id: ' . $id
            );
        }

        return $this->render(
            'order/viewOrderMgr.html.twig',
            array('mgrlist' => $mgrlist)
        );

    }




    //  function to display all order on the database, the return renders to a twig file with menu to update order.

    /**
     * @Route("/mgr/show" , name="show-mgr-order") methods={"GET","POST"}))
     */
    public function showMgrOrder() {

        $mgrlist = $this->getDoctrine()
            ->getRepository(Purorder:: class)
            ->findAll();

        return $this->render(
            'order/displayMgrDetail.html.twig',
            array('mgrlist' => $mgrlist)
        );

    }

    // function to delete a particular ordeer based on id... this function is called using a route in  the dispayAsset.html.twig file.

    /**
     * @Route("/mgr/delete/{id}", name="manager-delete") methods={"GET","POST"}))
     */
    public function deleteOrder($id)
    {

        $em = $this->getDoctrine ()->getManager ();
        $mgrlist = $em->getRepository ( Purorder:: class )->find ( $id );

        if (!$mgrlist) {
            throw $this->createNotFoundException (
                'There are no asset found in the orders with the following id: ' . $id
            );
        }

        $em->remove ( $mgrlist );
        $em->flush ();

        return $this->redirect ( '/mgr/show' );

    }


}  // class end
