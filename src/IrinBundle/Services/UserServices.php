<?php
/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 27/06/17
 * Time: 14:37
 */

namespace IrinBundle\Services;


use IrinBundle\Entity\User;

class UserServices
{

    public static function changeValidate(User $user)
    {
        $data = $user->getIsValidated();

        if ($data == 1){
            $user->setIsValidated(0);
        }else{
            $user->setIsValidated(1);
        }
    }

}