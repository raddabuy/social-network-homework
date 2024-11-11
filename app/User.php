<?php

declare(strict_types=1);

namespace Api;

class User {

    public $firstName;
    public $lastName;
    public $birthdate;
    public $biography;
    public $city;

    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    public function setBirthdate($birthdate) {
        $this->birthdate = $birthdate;
    }

    public function setBiography($biography) {
        $this->biography = $biography;
    }

    public function setCity($city) {
        $this->city = $city;
    }
}