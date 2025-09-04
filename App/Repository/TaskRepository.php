<?php

namespace App\Repository;

use App\Utils\Bdd;
use App\Model\Task;

class TaskRepository 
{
    private readonly \PDO $connection;

    public function __construct()
    {
        $this->connection = (new Bdd())->connectBDD();
    }

    /** 
     * Ajouter une tache avec son auteur en BDD et les catégories
     * @param Task Objet Task
     * @return Task retourne une tache
     */
    public function saveTask(Task $task): Task
    {
        try {
            //Récupération des valeurs
            $title = $task->getTitle();
            $description = $task->getDescription();
            $createdAt = $task->getCreatedAt()->format('Y-m-d H:i:s');
            $endDate = $task->getEndDate()->format('Y-m-d H:i:s');
            $status = $task->getStatus();
            $idUser = $task->getUser()->getIdUser();
            $categories = $task->getCategories();
            //Création de la requête
            $request = "INSERT INTO task(title, description, created_at, end_date, status, id_users) 
            VALUE (?,?,?,?,?,?)";
            //Préparation de la requête
            $req = $this->connection->prepare($request);
            //Bind des paramètres (Task)
            $req->bindParam(1, $title, \PDO::PARAM_STR);
            $req->bindParam(2, $description, \PDO::PARAM_STR);
            $req->bindParam(3, $createdAt, \PDO::PARAM_STR);
            $req->bindParam(4, $endDate, \PDO::PARAM_STR);
            $req->bindParam(5, $status, \PDO::PARAM_BOOL);
            $req->bindParam(6, $idUser, \PDO::PARAM_INT);
            //Exécution de la requête principale
            $req->execute();
            //Récupération de l'id task
            $idTask = (int) $this->connection->lastInsertId('task');

            //Test si la liste des taches posséde des categories
            if (!empty($this->categories)) {
                //Création de la requête pour chaque enregistrement (table asssociation task_category)
                $requestTaskCategory = "INSERT INTO task_category(id_task, id_category) VALUES ";

                //Tableau de BindParam
                $tabBind = [];

                //Boucle pour construire le tableau de BindParam et la requête
                for ($i = 0; $i < count($categories); $i++) {
                    //partie tableau

                    //Ajout de la colonne task
                    $colTask = ":idtask" . ($i + 1);
                    $tabBind[$colTask] = $idTask;

                    //Ajout de la colonne category
                    $colCat = ":idcat" . ($i + 1);
                    $tabBind[$colCat] = $categories[$i]->getIdCategory();

                    //partie requête
                    $requestTaskCategory .= "($colTask, " . $colCat . "),";
                }
                //Suppression du dernier caractère ','
                $requestTaskCategory = rtrim($requestTaskCategory, ',');
                //Préparation de la requête
                $req2 = $this->connection->prepare($requestTaskCategory);
                //Exécution de la requête
                $req2->execute($tabBind);
            }
            //Set id de la tache
            $task->setIdTask((int) $idTask);

            //retourne l'objet Task
            return $task;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}