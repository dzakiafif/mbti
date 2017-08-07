<?php
/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 04/06/17
 * Time: 14:00
 */

namespace IrinBundle\Controller;


use IrinBundle\Entity\ImageResize;
use IrinBundle\Entity\Kepribadian;
use IrinBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
//            $user->setEmail($request->get('email'));
            $user->setRole(1);
            $user->setRoles(serialize(['ROLE_USER']));
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

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data = $em->getRepository(Kepribadian::class)->findByUserId($user->getId());


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

            return $this->redirect($this->generateUrl('irin_user_list_kepribadian'));
        }

        return $this->render('IrinBundle:Kepribadian:kepribadian.html.twig');
    }

    public function listKepribadianAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $kepribadian = $this->get('security.token_storage')->getToken()->getUser();

        $data = $em->getRepository(Kepribadian::class)->findByUserId($kepribadian->getId());

//        return var_dump(unserialize($data->getJawaban()));

        if(isset($data)){
            unserialize($data->getJawaban());
        }else{
            return $this->redirect($this->generateUrl('irin_user_kepribadian'));
        }

        return $this->render('IrinBundle:Kepribadian:list-kepribadian.html.twig',[
            'kepribadian'=>unserialize($data->getJawaban())
        ]);
    }

    public function deleteUserAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(User::class)->findById($id);

        $em->remove($data);

        $em->flush();

        return $this->redirect($this->generateUrl('irin_list_user'));
    }

    public function updateProfileAction()
    {

        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('IrinBundle:User:update-profile.html.twig',['user'=>$user]);
    }

    public function homeAction(Request $request)
    {
        
        return $this->render('IrinBundle:Home:home.html.twig');
    }

    public function listAction()
    {

        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('IrinBundle:User:list.html.twig',['user'=>$user]);
    }

    public function logoutAction(Request $request)
    {
        $session = $request->getSession();
        $session->clear();

        return $this->redirect($this->generateUrl('irin_login'));
    }

    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $this->get('security.token_storage')->getToken()->getUser();

        if($request->getMethod() == 'POST'){


            if($data instanceof User){
                    // $data->setPassword($request->get('password'));
                    $data->setEmail($request->get('email'));
                    $data->setNama($request->get('nama'));
                    $data->setAlamat($request->get('alamat'));
                    $data->setNoHp($request->get('no-hp'));

                    if(!is_dir($this->getParameter('profile_directory')['resource'])){
                        @mkdir($this->getParameter('profile_directory')['resource'],0755,true);
                    }

                $file = $request->files->get('profile_picture');

                $filename = md5(uniqid()) . '.' . $file->guessExtension();

                $exAllowed = array('jpg', 'png', 'jpeg');

                $ext = pathinfo($filename, PATHINFO_EXTENSION);

                if (in_array($ext, $exAllowed)) {
                    if ($file instanceof UploadedFile) {
                        if (!($file->getClientSize() > (1024 * 1024 * 1))) {
                            ImageResize::createFromFile(
                                $request->files->get('profile_picture')->getPathName()
                            )->saveTo($this->getParameter('profile_directory')['resource'] . '/' . $filename, 20, true);
                            $data->setProfilePicture($filename);
                        } else {
                            return 'gambar tidak boleh lebih dari 1 MB';
                        }
                    }
                } else {
                    return 'cek kembali extension gambar anda';
                }

//                    if(!empty($request->files->get('profile_picture'))){
//
//                    }


            }

//            return var_dump($data);
             $em->persist($data);
             $em->flush();

            return $this->redirect($this->generateUrl('irin_update_user_view'));
        }

        return $this->render('IrinBundle:User:update.html.twig',['data'=>$data]);
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