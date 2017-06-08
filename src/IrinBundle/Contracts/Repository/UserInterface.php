<?php
/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 04/06/17
 * Time: 14:09
 */

namespace IrinBundle\Contracts\Repository;


use IrinBundle\Entity\User;

interface UserInterface
{
    /**
     * @param $id
     * @return User
     */
    public function findById($id);

    /**
     * @param $username
     * @return User
     */
    public function findByUsername($username);

    /**
     * @param $role
     * @return User
     */
    public function findByRole($role);

    /**
     * @param $email
     * @return User
     */
    public function findByEmail($email);
}