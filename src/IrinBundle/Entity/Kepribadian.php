<?php

namespace IrinBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Kepribadian
 */
class Kepribadian
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $jawaban;

    /**
     * @var string
     */
    private $hasil;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param $userId
     * @return User
     */
    public function setUserId(User $userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Set jawaban
     *
     * @param string $jawaban
     * @return Kepribadian
     */
    public function setJawaban($jawaban)
    {
        $this->jawaban = $jawaban;

        return $this;
    }

    /**
     * Get jawaban
     *
     * @return string 
     */
    public function getJawaban()
    {
        return $this->jawaban;
    }

    /**
     * Set hasil
     *
     * @param string $hasil
     * @return Kepribadian
     */
    public function setHasil($hasil)
    {
        $this->hasil = $hasil;

        return $this;
    }

    /**
     * Get hasil
     *
     * @return string 
     */
    public function getHasil()
    {
        return $this->hasil;
    }
}
