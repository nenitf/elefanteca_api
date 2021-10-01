<?php

use Core\Services\Usuario\CadastroUsuarioService;
use Core\Models\Usuario;

/**
 * @covers \Core\Services\Usuario\CadastroUsuarioService
 */
class CadastroUsuarioServiceTest extends LumenTestCase
{
    use Doctrine;

    private static $em;
    private static $schemaTool;
    private static $metadata;

    private $repo;
    private $crypt;

    public static function setUpBeforeClass(): void
    {
        self::$em = (new \App\Repositories\Doctrine\EntityManagerFactory())->get();
        self::$schemaTool = new \Doctrine\ORM\Tools\SchemaTool(self::$em);
        self::$metadata = self::$em->getClassMetadata(Usuario::class);
        self::$schemaTool->createSchema([self::$metadata]);
    }

    public static function tearDownAfterClass(): void
    {
        self::$schemaTool->dropSchema([self::$metadata]);
    }

    public function setUp(): void
    {
        parent::setUp();
        self::$em->beginTransaction();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        self::$em->rollback();
    }

    private function newSut()
    {
        $this->repo = (new \App\Repositories\Doctrine\UsuariosRepository(self::$em));
        $this->crypt = (new \App\Adapters\LumenCryptProvider());
        return new CadastroUsuarioService($this->repo, $this->crypt);
    }

    private function usuarioPersistido(Usuario $u): Usuario
    {
        return $this->doctrineFindById(self::$em, $u);
    }

    private function fixture($contexto)
    {
        $t = time();
        switch($contexto){
        case 'ok':
            return [
                'nome'  => "nome$t",
                'cpf'   => "64834139042",
                'email' => "fake$t@mail.com",
                'senha' => "senha$t",
            ];
        default:
            throw new \InvalidArgumentException();
        }
    }

    public function testDeveCadastrarComNomeCpfEmailSenhaCorretos()
    {
        $sut = $this->newSut();

        $fixture = $this->fixture('ok');

        $usuario = $sut(
            nome:   $fixture['nome'],
            cpf:    $fixture['cpf'],
            email:  $fixture['email'],
            senha:  $fixture['senha'],
        );

        $this->assertNotNull($usuario->getId());

        $persistido = $this->usuarioPersistido($usuario);
        $this->assertEquals(
            $usuario->cpf,
            $persistido->cpf
        );
        $this->assertEquals(
            $usuario->email,
            $persistido->email
        );
        $this->assertEquals(
            $usuario->email,
            $persistido->email
        );
        $this->assertEquals(
            $usuario->getSenha(),
            $persistido->getSenha()
        );
    }

    public function testDeveCriptografarSenha()
    {
        $sut = $this->newSut();

        $fixture = $this->fixture('ok');

        $usuario = $sut(
            nome:   $fixture['nome'],
            cpf:    $fixture['cpf'],
            email:  $fixture['email'],
            senha:  $fixture['senha'],
        );

        $this->assertNotEquals($fixture['senha'], $usuario->getSenha());
        $this->assertNotEquals(
            $fixture['senha'],
            $this->usuarioPersistido($usuario)->getSenha()
        );
    }

    public function testDeveCriarAtivo()
    {
        $sut = $this->newSut();

        $fixture = $this->fixture('ok');

        $usuario = $sut(
            nome:   $fixture['nome'],
            cpf:    $fixture['cpf'],
            email:  $fixture['email'],
            senha:  $fixture['senha'],
        );

        $this->assertTrue($usuario->getAtivo());

        $this->assertTrue($this->usuarioPersistido($usuario)->getAtivo());
    }
}
