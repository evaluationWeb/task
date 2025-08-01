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

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
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
                    $tabBind[$colCat] = $this->categories[$i]->getIdCategory();

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
}
