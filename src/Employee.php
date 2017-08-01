<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//
namespace rtb;

class Employee {


    static public function loadAllEmployees(\mysqli $conn){
      var_dump($conn);
        $sql = "SELECT * FROM employee";

        $result = $conn->query($sql);
        if($result){
            $employees = array();
            foreach($result as $row){
                $employee = new Employee();
                $employee->setId($row['id']);
                $employee->setEmail($row['email']);
                $employee->setName($row['name']);
                $employee->setHolidayDaysLeft($row['holiday_days_left']);
                $employees[]=$employee;
            }
            return $employees;
        }
        return [];
    }

    private $id;
    private $name;
    private $email;
    private $holidayDaysLeft;


    public function __construct(){
        $this->id=-1;
        $this->email='';
        $this->name='';
        $this->holidayDaysLeft = 0;
    }

    static public function getEmployeeById(\mysqli $conn, $id){
        $sql="SELECT * FROM employee WHERE id = '$id'";
        $result=$conn->query($sql);
        if($result->num_rows == 1){
            $row = $result->fetch_assoc();
            $employee = new Employee();
            $employee->setId($row['id']);
            $employee->setEmail($row['email']);
            $employee->setName($row['name']);
            $employee->setHolidayDaysLeft($row['holiday_days_left']);
            return $employee;
        }
        else{
            return false;
        }
    }

    public function updateEmployee(\mysqli $conn){
      $sql="UPDATE employee SET "
                    . "name='$this->name',"
                    . "email='$this->email',"
                    . "holiday_days_left=$this->holidayDaysLeft "
                    . "WHERE id=$this->id";

      if($conn->query($sql)){
          return $this;
      }
      return false;
    }





    public function setId($id){
        if(is_numeric($id)){
            $this->id = $id;
            return $this;
        }
        return null;
    }


    public function setName($name){
        if(is_string($name)){
            $this->name = $name;
            return $this;
        }
        return null;
    }

    public function setEmail($email){
        if(is_string($email)){
            $this->email = $email;
            return $this;

        }
        return null;
    }

    public function setHolidayDaysLeft($holidayDaysLeft){
        if(is_numeric($holidayDaysLeft)){
            $this->holidayDaysLeft = $holidayDaysLeft;
            return $this;
        }
        return null;

    }


    public function getId(){
        return $this->id;
    }

    public function getEmail(){
        return $this->email;
    }

    public function getName(){
        return $this->name;
    }

    public function getHolidayDaysLeft(){
        return $this->holidayDaysLeft;
    }
}
