<?php
/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 26/06/17
 * Time: 15:42
 */

namespace IrinBundle\Controller;


use IrinBundle\Entity\ImageResize;
use IrinBundle\Entity\User;
use IrinBundle\Services\UserServices;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

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

    public function editAction($id,Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(User::class)->findById($id);

        if($request->getMethod() == 'POST')
        {
            if($data instanceof User)
            {
                $data->setEmail($request->get('email'));
                $data->setNama($request->get('nama'));
                $data->setAlamat($request->get('alamat'));
                $data->setNoHp($request->get('no-hp'));

                $file = $request->files->get('profile_picture');

                $filename = md5(uniqid()) . '.' . $file->guessExtension();

                $extAllowed = array('jpg','png','jpeg');

                $ext = pathinfo($extAllowed,PATHINFO_EXTENSION);

                if(in_array($ext,$extAllowed)){
                    if($file instanceof UploadedFile){
                        if(!($file->getClientSize() > (1024 * 1024 * 1))){
                            ImageResize::createFromFile(
                                $request->files->get('profile_picture')->getPathName()
                            )->saveTo($this->getParameter('profile_directory')['resource'] . '/' . $filename,20,true);
                            $data->setProfilePicture($filename);
                        }else{
                            return 'Ukuran Foto Tidak boleh dari 1 MB';
                        }
                    }
                }else{
                    return 'cek lagi extension';
                }
            }

            $em->persist($data);
            $em->flush();

            return $this->redirect($this->generateUrl('irin_admin_list'));
        }

        return $this->render('IrinBundle:Admin:edit.html.twig',['data'=>$data]);
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(User::class)->findById($id);

        $em->remove($data);

        $em->flush();

        if (file_exists($this->getParameter('profile_directory')['resource'] . '/' . $data->getProfilePicture())) {
            unlink($this->getParameter('profile_directory')['resource'] . '/' . $data->getProfilePicture());
        }


        return $this->redirect($this->generateUrl('irin_admin_list'));
    }
    
    public function editValidateAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(User::class)->findById($id);

        if($request->getMethod() == 'POST') {
            UserServices::changeValidate($data);

            $em->persist($data);
            $em->flush();

            return $this->redirect($this->generateUrl('irin_admin_list'));
        }

        return $this->render('IrinBundle:Admin:edit-validate.html.twig',['data'=>$data]);
    }



}