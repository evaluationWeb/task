<?php

namespace App\Repository;

use App\Utils\Bdd;
use App\Model\Task;
use App\Model\User;
use App\Model\Category;

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
            if (!empty($categories)) {
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

    /**
     * Recupérer toutes les taches avec leur auteur et les categories associées
     * @param User $user user connecté
     * @return array Task retourne un tableau de Task
     */
    public function findAllTask(User $user): array
    {
        try {
            $idUser = $user->getIdUser();
            $request = "SELECT t.id_task AS idTask, t.title, t.description, t.created_at AS createdAt, 
            t.end_date AS endDate, t.status, t.id_users, u.firstname, u.lastname, 
            GROUP_CONCAT(c.id_category) AS categoriesId,
            GROUP_CONCAT(c.name) AS categoriesName
            FROM task AS t INNER JOIN users AS u            
            ON t.id_users = u.id_users INNER JOIN task_category AS tc
            ON t.id_task = tc.id_task INNER JOIN category AS c
            ON tc.id_category = c.id_category
            WHERE t.status = 0 AND u.id_users = ? GROUP BY idTask ";
            $req = $this->connection->prepare($request);
            $req->bindParam(1, $idUser, \PDO::PARAM_INT);
            $req->execute();
            $data = $req->fetchAll(\PDO::FETCH_ASSOC);
            $tasks = [];
            //hydratation en task obj
            foreach ($data as $taskEntry) {
                //Hydratation en Task
                $task = $this->hydrate($taskEntry);
                //Ajouter à la liste
                $tasks[] = $task;
            }
            return $tasks;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Méthode qui transforme un tableau associatif en Task
     * @param array $value
     * @return Task retourne une Task avec toutes les valeurs assignées.
     */
    public function hydrate(array $value): Task
    {
        $task = new Task();
        $task->setIdTask($value["idTask"]);
        $task->setTitle($value["title"]);
        $task->setDescription($value["description"]);
        $createdAt = new \DateTimeImmutable($value["createdAt"]);
        $endDate = new \DateTimeImmutable($value["endDate"]);
        $task->setCreatedAt($createdAt);
        $task->setEndDate($endDate);
        if (isset($value["id_users"])) {
            $user = new User();
            $user->setIdUser($value["id_users"]);
            $user->setLastname($value["lastname"]);
            $user->setFirstname($value["firstname"]);
            $task->setUser($user);
        }
        $categoriesId = explode(",", $value["categoriesId"]);
        $categoriesName = explode(",", $value["categoriesName"]);
        //Création des category et assignation à la task
        for ($i = 0; $i < count($categoriesId); $i++) {
            $category = new Category();
            $category->setIdCategory($categoriesId[$i]);
            $category->setName($categoriesName[$i]);
            $task->addCategory($category);
        }
        return $task;
    }

    /**
     * Méthode qui met à jour une tache et ces catégories
     * @param Task $task Objet Task
     * @param int $id 
     * @return void
     */
    public function updateTask(Task $task, int $id): void
    {
        try {
            $idTask = $id;
            //Récupération des valeurs
            $title = $task->getTitle();
            $description = $task->getDescription();
            $endDate = $task->getEndDate()->format('Y-m-d H:i:s');
            $categories = $task->getCategories();
            //Création de la requête
            $request = "UPDATE task SET title = ?, description = ?, end_date = ? WHERE id_task = ?";
            //Préparation de la requête
            $req = $this->connection->prepare($request);
            //Bind des paramètres (Task)
            $req->bindParam(1, $title, \PDO::PARAM_STR);
            $req->bindParam(2, $description, \PDO::PARAM_STR);
            $req->bindParam(3, $endDate, \PDO::PARAM_STR);
            $req->bindParam(4, $idTask, \PDO::PARAM_INT);

            //Exécution de la requête principale
            $req->execute();

            //Suppression de toutes la catégories ratachées à la tache
            $request2 = "DELETE FROM task_category WHERE id_task = ?";
            $req2 = $this->connection->prepare($request2);
            $req2->bindParam(1, $idTask, \PDO::PARAM_INT);
            $req2->execute();
            //Test si la liste des taches posséde des categories
            if (!empty($task->getCategories())) {
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
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Méthode qui récupére un tache par son id
     * @param int $id
     * @return Task task
     */
    public function findTaskById(int $id): ?Task
    {
        try {
            $request = "SELECT t.id_task AS idTask, t.title, t.description, t.created_at AS createdAt, 
            t.end_date AS endDate, t.status, 
            GROUP_CONCAT(c.id_category) AS categoriesId,
            GROUP_CONCAT(c.name) AS categoriesName
            FROM task AS t INNER JOIN task_category AS tc
            ON t.id_task = tc.id_task INNER JOIN category AS c
            ON tc.id_category = c.id_category
            WHERE t.id_task = ? GROUP BY idTask ";
            $req = $this->connection->prepare($request);
            $req->bindParam(1, $id, \PDO::PARAM_INT);
            $req->execute();
            $data = $req->fetch(\PDO::FETCH_ASSOC);
            //Hydratation en Task
            $task = $this->hydrate($data);
            return $task;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Méthode qui valide une Task
     * @param Task $task
     * @Return void
     */
    public function validateTask(Task $task)
    {
        try {
            $idTask = $task->getIdTask();
            $request = "UPDATE task set status = 1 WHERE id_task  = ?";
            $req = $this->connection->prepare($request);
            $req->bindParam(1, $idTask, \PDO::PARAM_INT);
            $req->execute();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Méthode qui récupérer toute les taches hydratées
     * @param Task $task
     * @return array<Task>
     */
    public function findAllTaskHydrate(User $user): array
    {
        try {
            $idUser = $user->getIdUser();
            $request = "SELECT t.id_task AS idTask, t.title, t.description, t.created_at, 
            t.end_date, t.status, t.id_users, u.firstname, u.lastname, 
            GROUP_CONCAT(c.id_category) categoriesId,
            GROUP_CONCAT(c.name) categoriesName
            FROM task AS t INNER JOIN users AS u            
            ON t.id_users = u.id_users LEFT JOIN task_category AS tc
            ON t.id_task = tc.id_task INNER JOIN category AS c
            ON tc.id_category = c.id_category
            WHERE t.status = 0 AND u.id_users = ? GROUP BY idTask ";
            $req = $this->connection->prepare($request);
            $req->bindParam(1, $idUser, \PDO::PARAM_INT);
            $req->execute();
            $data = $req->fetchAll(\PDO::FETCH_CLASS, Task::class);
            return $data;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
