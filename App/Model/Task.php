<?php

namespace App\Model;

use App\Model\User;
use App\Model\Category;
use App\Utils\Bdd;

class task
{
    //Attributs
    private int $idTask;
    private string $title;
    private ?string $description;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $endDate;
    private bool $status;
    private ?User $user;
    private array $categories;
    //Bdd
    private \PDO $connexion;

    //Constructeur
    public function __construct()
    {
        $this->connexion = (new Bdd())->connectBdd();
        $this->categories = [];
        $this->createdAt = new \DateTimeImmutable();
    }

    //Getters et Setters
    public function getIdTask(): int
    {
        return $this->idTask;
    }

    public function setIdTask(int $id): self
    {
        $this->idTask = $id;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        $this->categories[] = $category;
        return $this;
    }

    public function removeCategory(Category $category): self
    {
        unset($this->categories[array_search($category, $this->categories)]);
        sort($this->categories);
        return $this;
    }

    //Méthodes
    /** 
     * Ajouter une tache avec son auteur en BDD et les catégories
     * @return Task retourne une tache
     */
    public function saveTask(): Task
    {
        try {
            //Récupération des valeurs
            $title = $this->title;
            $description = $this->description;
            $createdAt = $this->createdAt->format('Y-m-d H:i:s');
            $endDate = $this->endDate->format('Y-m-d H:i:s');
            $status = $this->status;
            $idUser = $this->user->getIdUser();
            $categories = $this->categories;
            //Création de la requête
            $request = "INSERT INTO task(title, description, created_at, end_date, status, id_users) 
            VALUE (?,?,?,?,?,?)";
            //Préparation de la requête
            $req = $this->connexion->prepare($request);
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
            $idTask = (int) $this->connexion->lastInsertId('task');

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
                $req2 = $this->connexion->prepare($requestTaskCategory);
                //Exécution de la requête
                $req2->execute($tabBind);
            }
            //Set id de la tache
            $this->setIdTask((int) $idTask);

            //retourne l'objet Task
            return $this;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Recupérer toutes les taches avec leur auteur et les categories associées
     * @return array Task retourne un tableau de Task
     */
    public function findAllTask(): array
    {
        try {
            $idUser = $this->getUser()->getIdUser();
            $request = "SELECT t.id_task AS idTask, t.title, t.description, t.created_at AS createdAt, 
            t.end_date AS endDate, t.status, t.id_users, u.firstname, u.lastname, 
            GROUP_CONCAT(c.id_category) AS categoriesId,
            GROUP_CONCAT(c.name) AS categoriesName
            FROM task AS t INNER JOIN users AS u            
            ON t.id_users = u.id_users LEFT JOIN task_category AS tc
            ON t.id_task = tc.id_task INNER JOIN category AS c
            ON tc.id_category = c.id_category
            WHERE t.status = 0 AND u.id_users = ? GROUP BY idTask ";
            $req = $this->connexion->prepare($request);
            $req->bindParam(1,$idUser, \PDO::PARAM_INT );
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
        if(isset($value["id_users"])) {
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
     * @return void
     */

    public function updateTask(int $id): void
    {
        try {
            $idTask = $id;
            //Récupération des valeurs
            $title = $this->title;
            $description = $this->description;
            $endDate = $this->endDate->format('Y-m-d H:i:s');
            $categories = $this->categories;
            //Création de la requête
            $request = "UPDATE task SET title = ?, description = ?, end_date = ? WHERE id_task = ?";
            //Préparation de la requête
            $req = $this->connexion->prepare($request);
            //Bind des paramètres (Task)
            $req->bindParam(1, $title, \PDO::PARAM_STR);
            $req->bindParam(2, $description, \PDO::PARAM_STR);
            $req->bindParam(3, $endDate, \PDO::PARAM_STR);
            $req->bindParam(4, $idTask, \PDO::PARAM_INT);
            //Exécution de la requête principale
            $req->execute();

            //Suppression de toutes la catégories ratachées à la tache
            $request2 = "DELETE FROM task_category WHERE id_task = ?";
            $req2 = $this->connexion->prepare($request2);
            $req2->bindParam(1, $idTask, \PDO::PARAM_INT);
            $req2->execute();
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
                $req2 = $this->connexion->prepare($requestTaskCategory);
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
            $req = $this->connexion->prepare($request);
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
     * @Return void
     */
    public function validateTask() {
        try {
            $idTask = $this->getIdTask();
            $request = "UPDATE task set status = 1 WHERE id_task  = ?";
            $req = $this->connexion->prepare($request);
            $req->bindParam(1, $idTask, \PDO::PARAM_INT);
            $req->execute();
        
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function findAllTaskHydrate() : array {
        try {
            $idUser = $this->getUser()->getIdUser();
            $request = "SELECT t.id_task AS idTask, t.title, t.description, t.created_at, 
            t.end_date, t.status, t.id_users, u.firstname, u.lastname, 
            GROUP_CONCAT(c.id_category) categoriesId,
            GROUP_CONCAT(c.name) categoriesName
            FROM task AS t INNER JOIN users AS u            
            ON t.id_users = u.id_users LEFT JOIN task_category AS tc
            ON t.id_task = tc.id_task INNER JOIN category AS c
            ON tc.id_category = c.id_category
            WHERE t.status = 0 AND u.id_users = ? GROUP BY idTask ";
            $req = $this->connexion->prepare($request);
            $req->bindParam(1,$idUser, \PDO::PARAM_INT );
            $req->execute();
            $data = $req->fetchAll(\PDO::FETCH_CLASS, Task::class);
            return $data;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
