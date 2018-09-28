<?php

declare(strict_types=1);

use Behat\Behat\Context\Context as BehatContext;
use PommProject\Foundation\Pomm;
use RulerZ\Test\BaseContext;

class Context extends BaseContext implements BehatContext
{
    /** @var Pomm */
    private $pomm;

    public function initialize()
    {
        $dotenv = new Dotenv\Dotenv(__DIR__.'/../../');
        $dotenv->load();

        $this->pomm = new Pomm(['test_rulerz' => [
            'dsn' => sprintf('pgsql://%s:%s@%s:%d/%s', $_ENV['POSTGRES_USER'], $_ENV['POSTGRES_PASSWD'], $_ENV['POSTGRES_HOST'], $_ENV['POSTGRES_PORT'], $_ENV['POSTGRES_DB']),
            'class:session_builder' => \PommProject\ModelManager\SessionBuilder::class,
        ]]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCompilationTarget(): \RulerZ\Compiler\CompilationTarget
    {
        return new \RulerZ\Pomm\Target\Pomm();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultDataset()
    {
        return $this->pomm['test_rulerz']->getModel(\Entity\Pomm\TestRulerz\PublicSchema\PlayersModel::class);
    }
}
