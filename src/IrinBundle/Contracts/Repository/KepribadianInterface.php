<?php
/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 06/06/17
 * Time: 14:23
 */

namespace IrinBundle\Contracts\Repository;


use IrinBundle\Entity\Kepribadian;

interface KepribadianInterface
{
    /**
     * @param $id
     * @return Kepribadian
     */
    public function findById($id);

    /**
     * @param $userId
     * @return Kepribadian
     */
    public function findByUserId($userId);

}