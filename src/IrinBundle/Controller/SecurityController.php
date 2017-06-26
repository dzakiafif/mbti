<?php
/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 26/06/17
 * Time: 15:16
 */

namespace IrinBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('irin_admin_index');
        } elseif ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('irin_user_home');
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
//        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('IrinBundle:User:login.html.twig', [
            'error' => $error,
        ]);
    }

    public function loginCheckAction()
    {
        if($this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('irin_admin_index');
        }elseif ($this->isGranted('ROLE_USER')){
            return $this->redirectToRoute('irin_user_home');
        }elseif ($this->isGranted('IS_AUTHENTICATED_ANONYMOUSLY')){
            return $this->redirectToRoute('irin_login');
        }
    }
}