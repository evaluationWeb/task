<?php

namespace App\Controller;

use App\Model\Category;
use App\Model\User;
use App\Model\task;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Repository\TaskRepository;
use App\Utils\Utilitaire;

class TaskController {

    private readonly CategoryRepository $categoryRepository;
    private readonly TaskRepository $taskRepository;

    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
        $this->taskRepository = new TaskRepository();
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
                $task = new Task();
                $task->setTitle($title);
                $task->setDescription($description);
                $endDate = new \DateTimeImmutable($endDate);
                $task->setEndDate($endDate);
                $task->setStatus(false);
                $user = new User();
                $user->setIdUser($userId);
                $task->setUser($user);
                foreach ($categories as $category) {
                    $cat = (new Category())->setIdCategory($category);
                    $task->addCategory($cat);
                }
                $this->taskRepository->saveTask($task);
                
                $message = "La tache a été ajouté";

            } else {
                $message = "Veuillez remplir tous les champs du formulaire";
            }
        }
        include_once "App/View/viewAddTask.php";
    }

    public function showAllTask() {
        $idUser = Utilitaire::sanitize($_SESSION["id"]);
        $user = new User();
        $user->setIdUser($idUser);
        $tasks = $this->taskRepository->findAllTask($user);
        //dd($tasks);
        //retourner la vue
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
                $task = new task();
                $task->setTitle($title);
                $task->setDescription($description);
                $endDate = new \DateTimeImmutable($endDate);
                $task->setEndDate($endDate);
                
                foreach ($categories as $category) {
                    $cat = (new Category())->setIdCategory($category);
                    $task->addCategory($cat);
                }
                $this->taskRepository->updateTask($task,$id);
                
               header('Location: /task/task/all');

            } else {
                header('Location: /task/task/all');
            }
        }
        else{
            //sanitize de l'id 
            $id = Utilitaire::sanitize($_POST["id"]);
            //récupération de la précédente valeur de la catégorie
            $task = $this->taskRepository->findTaskById($id);
           
        }
        include_once "App/View/viewModifyTask.php";
    }

    public function terminateTask() {
        if (isset($_POST["valider"])) {
            $idTask = Utilitaire::sanitize($_POST["id"]);
            $task = new Task();
            $task->setIdTask($idTask);
            $this->taskRepository->validateTask($task);
            header('Location: /task/task/all');
        }
        header('Location: /task/task/all');
    }

    public function showAllTaskHydrate() {
        $idUser = $_SESSION["id"];
        $user = new User();
        $user->setIdUser($idUser);
        $tasks = $this->taskRepository->findAllTaskHydrate($user);
        dd($tasks);
    }
}
