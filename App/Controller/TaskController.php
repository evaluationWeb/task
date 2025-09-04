<?php

namespace App\Controller;

use App\Model\Category;
use App\Model\User;
use App\Model\task;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Utils\Utilitaire;

class TaskController {

    private readonly CategoryRepository $categoryRepository;
    private readonly UserRepository $userRepository;
    private Task $task;

    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
        $this->userRepository = new UserRepository();
        $this->task = new Task();
    }

    public function addTask() {
        $message  = "";
        $categories = $this->categoryRepository->findAllCategory();
        $userId = $_SESSION["id"];
        if( isset($_POST["submit"])) {
            
            if( !empty($_POST["title"]) && !empty($_POST["description"]) && !empty($_POST["endDate"])) {
                $endDate = $_POST["endDate"];
                $title = Utilitaire::sanitize($_POST["title"]);
                $description = Utilitaire::sanitize($_POST["description"]);
                //récupération des categories
                $categories = $_POST["categories"];

                //Assignation de valeurs à l'objet Task
                $this->task->setTitle($title);
                $this->task->setDescription($description);
                $endDate = new \DateTimeImmutable($endDate);
                $this->task->setEndDate($endDate);
                $this->task->setStatus(false);
                $user = new User();
                $user->setIdUser($userId);
                $this->task->setUser($user);
                foreach ($categories as $category) {
                    $cat = (new Category())->setIdCategory($category);
                    $this->task->addCategory($cat);
                }
                $task = $this->task->saveTask();
                
                $message = "La tache a été ajouté";

            } else {
                $message = "Veuillez remplir tous les champs du formulaire";
            }
            
           
        }
        include_once "App/View/viewAddTask.php";
    }

    public function showAllTask() {
        $idUser = $_SESSION["id"];
        $user = new User();
        $user->setIdUser($idUser);
        $this->task->setUser($user);
        $tasks = $this->task->findAllTask();
        //dd($tasks);
        include_once "App/View/viewAllTask.php";
    }

    public function modifyTask() {
        $categories = $this->categoryRepository->findAllCategory();
        $id = Utilitaire::sanitize($_POST["id"]);
        
        if( isset($_POST["submit"])) {
            
            if( !empty($_POST["title"]) && !empty($_POST["description"]) && !empty($_POST["endDate"])) {
                $endDate = $_POST["endDate"];
                $title = Utilitaire::sanitize($_POST["title"]);
                $description = Utilitaire::sanitize($_POST["description"]);
                //récupération des categories
                $categories = $_POST["categories"];

                //Assignation de valeurs à l'objet Task
                $this->task->setTitle($title);
                $this->task->setDescription($description);
                $endDate = new \DateTimeImmutable($endDate);
                $this->task->setEndDate($endDate);
                
                foreach ($categories as $category) {
                    $cat = (new Category())->setIdCategory($category);
                    $this->task->addCategory($cat);
                }
                $this->task->updateTask($id);
                
               header('Location: /task/task/all');

            } else {
                header('Location: /task/task/all');
            }
            
        }
        else{
            //sanitize de l'id 
            $id = Utilitaire::sanitize($_POST["id"]);
            //récupération de la précédente valeur de la catégorie
            $task = $this->task->findTaskById($id);
           
        }
        include_once "App/View/viewModifyTask.php";
    }

    public function terminateTask() {
        if (isset($_POST["valider"])) {
            $idTask = Utilitaire::sanitize($_POST["id"]);
            $this->task->setIdTask($idTask);
            $this->task->validateTask();
            header('Location: /task/task/all');
        }
        header('Location: /task/task/all');
    }

    public function showAllTaskHydrate() {
        $idUser = $_SESSION["id"];
        $user = new User();
        $user->setIdUser($idUser);
        $this->task->setUser($user);
        $tasks = $this->task->findAllTaskHydrate();
        dd($tasks);
    }
}
