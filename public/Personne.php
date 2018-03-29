<?php


class Personne
{
    public $prenom;

    public $image;

    public function __construct($prenom, $image)
    {
        $this->prenom = $prenom;
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param mixed $prenom
     * @return Personne
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     * @return Personne
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

}