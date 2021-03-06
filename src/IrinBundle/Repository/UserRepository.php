<?php

namespace IrinBundle\Repository;

use Doctrine\ORM\EntityRepository;
use IrinBundle\Contracts\Repository\UserInterface;
use IrinBundle\Entity\User;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository implements UserInterface
{
    /**
     * @param $id
     * @return User
     */
    public function findById($id)
    {
       return $this->find($id);
    }

    /**
     * @param $username
     * @return User
     */
    public function findByUsername($username)
    {
        return $this->findOneBy(['username'=>$username]);
    }

    /**
     * @param $role
     * @return User
     */
    public function findByRole($role)
    {
        return $this->findOneBy(['role'=>$role]);
    }

    /**
     * @param $email
     * @return User
     */
    public function findByEmail($email)
    {
        return $this->findOneBy(['email'=>$email]);
    }
}
