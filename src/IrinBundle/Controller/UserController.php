<?php
/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 04/06/17
 * Time: 14:00
 */

namespace IrinBundle\Controller;


use IrinBundle\Entity\Kepribadian;
use IrinBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if ($request->getMethod() == 'POST') {
            $user = new User();
            $user->setUsername($request->get('username'));
            $user->setPassword($request->get('password'));
            $user->setEmail($request->get('email'));
            $user->setRole(1);
            $user->setIsValidated(0);

            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('irin_login'));
        }

        return $this->render('IrinBundle:User:register.html.twig');
    }

    public function kepribadianAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(Kepribadian::class)->findByUserId($request->getSession()->get('uid'));

        $user = $em->getRepository(User::class)->findById($request->getSession()->get('uid')['value']);

        if($request->getMethod() == 'POST')
        {
            if($data == null){
                $data = new Kepribadian();
                $data->setUserId($user);
                $data->setHasil($request->get('hasil'));
                $data->setJawaban(serialize($request->get('jawaban')));
            }

            $em->persist($data);
            $em->flush();

            return $this->redirect($this->generateUrl('irin_list_kepribadian'));
        }

        return $this->render('IrinBundle:Kepribadian:kepribadian.html.twig');
    }

    public function listKepribadianAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $kepribadian = $em->getRepository(Kepribadian::class)->findByUserId($request->getSession()->get('uid'));

        if(isset($kepribadian)){
            unserialize($kepribadian->getJawaban());
        }else{
            return $this->redirect($this->generateUrl('irin_kepribadian'));
        }

        return $this->render('IrinBundle:Kepribadian:list-kepribadian.html.twig',['kepribadian'=>$kepribadian->getJawaban()]);
    }

    public function homeAction(Request $request)
    {
        if($request->getSession()->get('role')== null){
            return $this->redirect($this->generateUrl('irin_login'));
        }

        $em = $this->getDoctrine()->getEntityManager();

        $user = count($em->getRepository(User::class)->findAll());

        return $this->render('IrinBundle:Home:home.html.twig',['user'=>$user]);
    }

    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(User::class)->findAll();

        return $this->render('IrinBundle:User:list.html.twig',['data'=>$data]);
    }

    public function loginAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if($request->getMethod() == 'POST')
        {
            $username = $request->get('username');
            $password = md5($request->get('password'));

            $data = $em->getRepository(User::class)->findByUsername($username);

            if($data instanceof User)
            {
                if($data != null){
                    if($password == $data->getPassword()){
                        $session = $request->getSession();

                        $session->set('uid',['value'=>$data->getId()]);
                        $session->set('uname',['value'=>$data->getUsername()]);
                        $session->set('email',['value'=>$data->getEmail()]);
                        $session->set('role',['value'=>$data->getRole()]);

                        return $this->redirect($this->generateUrl('irin_home'));
                    }else{
                        return 'ingat kembali password anda';
                    }
                }else{
                    return 'tidak ada data anda';
                }
            }
        }
        return $this->render('IrinBundle:User:login.html.twig');
    }

    public function logoutAction(Request $request)
    {
        $session = $request->getSession();
        $session->clear();

        return $this->redirect($this->generateUrl('irin_login'));
    }

    public function updateAction($id,Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(User::class)->findById($id);

        if($request->getMethod() == 'POST'){

            $file = $request->files->get('profile-picture');

            $filename = md5(uniqid()) . $file->guessExtension();

            $file->move($this->getParameter('profile_directory')['resource'],$filename);

            if($data instanceof User){
                if($data->getRole() == 1){
                    $data->setPassword($request->get('password'));
                    $data->setAlamat($request->get('alamat'));
                    $data->setNoHp($request->get('no-hp'));
                    $data->setProfilePicture($filename);
                }

            }
            $em->persist($data);
            $em->flush();
        }
    }

    public function forgetPasswordAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(User::class)->findByEmail($request->get('email'));

        if($request->getMethod() == 'POST'){
            if($data instanceof User){
                $token = md5(sha1(uniqid()));
                $data->setToken($token);
            }
            $em->persist($data);
            $em->flush();

            $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com',587,'tls')
                        ->setUsername('projectirin2@gmail.com')
                        ->setPassword('irin2017');

            $message = \Swift_Message::newInstance();
            $message->setSubject('Reset Password');
            $message->setFrom('admin@irin.co.id');
            $message->setTo([$request->get('email')]);
            $message->setBody($this->render('IrinBundle:User:forget-tmp.html.twig',['username'=>$data->getUsername(),'host'=>$request->getHost(),'token'=>$token]));

            $mailer = \Swift_Mailer::newInstance($transport);
            $mailer->send($message);

            return 'cek email anda';
        }

        return $this->render('IrinBundle:User:forgot-password.html.twig');
    }

    public function resetAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(User::class)->findByToken($request->get('token'));

        $asem = $data[0];

        if($request->getMethod() == 'POST'){
            if($asem instanceof User){
                $asem->setPassword($request->get('password'));
                $asem->setToken('');
            }
            $em->persist($asem);
            $em->flush();

            return $this->redirect($this->generateUrl('irin_login'));
        }

        return $this->render('IrinBundle:User:reset-password.html.twig');
    }
}