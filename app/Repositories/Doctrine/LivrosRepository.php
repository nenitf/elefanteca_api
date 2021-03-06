<?php

namespace App\Repositories\Doctrine;

use Doctrine\ORM\EntityManagerInterface;

use Core\Models\Livro;

class LivrosRepository extends BaseRepository implements \Core\Repositories\ILivrosRepository
{
    protected string $model = Livro::class;

    public function save(Livro $e): Livro
    {
        return $this->base_save($e);
    }

    public function findById(int $id): ?Livro
    {
        return $this->base_findById($id);
    }

    public function findBy(array $condition, int $page, int $limit = 10): array
    {
        return $this->base_findByWithLikeEqual(
            ['titulo'],
            $condition,
            $limit,
            $page,
        );
    }
}

