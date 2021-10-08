<?php

use Doctrine\ORM\EntityManagerInterface;

use Doctrine\ORM\Tools\SchemaTool;

use Core\Models\{
    Usuario,
};


trait Doctrine {
    public function doctrineFindById(
        EntityManagerInterface $em,
        string $namespace,
        int $id,
    ) {
        $e = $em->find($namespace, $id);

        // Evita que o Doctrine cacheie id pesquisado,
        // pois pode ocorrer de no próximo teste pesquisar
        // pelo mesmo id mas o conteúdo do banco mudou.
        // Ocorre com rollback de transaction entre testes
        // THANKS: https://stackoverflow.com/a/7957067
        $em->detach($e);

        return $e;
    }

    public function doctrineExecuteQuery(
        EntityManagerInterface $em,
        string $query,
    ) {
        $stmt = self::$em
            ->getConnection()
            ->prepare($query);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function doctrineGetMetadatas(
        EntityManagerInterface $em
    ) {
        $models = [
            Usuario::class
        ];

        return array_map(function($model) use ($em) {
            return $em->getClassMetadata($model);
        }, $models);
    }

    public static function doctrineCreateDatabase(
        EntityManagerInterface $em
    ) {
        $schemaTool = new SchemaTool($em);
        $schemaTool->createSchema(self::doctrineGetMetadatas($em));
    }

    public static function doctrineDeleteDatabase(
        EntityManagerInterface $em
    ) {
        $schemaTool = new SchemaTool($em);
        $schemaTool->dropSchema(self::doctrineGetMetadatas($em));
    }
}
