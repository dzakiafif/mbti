<?php
/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 26/06/17
 * Time: 15:42
 */

namespace IrinBundle\Controller;


use IrinBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{

    public function indexAction()
    {
        if($this->isGranted('ROLE_ADMIN')){
        }

        $em = $this->getDoctrine()->getEntityManager();

        $user = count($em->getRepository(User::class)->findAll());

        return $this->render('IrinBundle:Home:home.html.twig',['user'=>$user]);
    }

    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository(User::class)->findAll();

        return $this->render('IrinBundle:Admin:list.html.twig',['user'=>$user]);
    }

}