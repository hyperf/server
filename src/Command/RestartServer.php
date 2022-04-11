<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Hyperf\Server\Command;

use Hyperf\Contract\ConfigInterface;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Swoole\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RestartServer extends Command
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct('restart');
        $this->setDescription('Restart hyperf servers.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->container->get(ConfigInterface::class);
        $file = $config->get('server.settings.pid_file',BASE_PATH . '/runtime/hyperf.pid');
        if (!file_exists($file)) {
            throw new InvalidArgumentException('Please start server firstly.');
        }
        $pid = intval(file_get_contents($file));
        if(!Process::kill($pid,0)){
            throw new InvalidArgumentException('Please start server firstly.');
        }
        Process::kill($pid,SIGUSR1) && $output->writeln(sprintf("pid %d restart success.",$pid));
        return 0;
    }

}
