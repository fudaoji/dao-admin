<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;


class SwooleServer extends Command
{
    protected static $defaultName = 'swoole:server';
    protected static $defaultDescription = 'swoole server';

    /**
     * @return void
     */
    /*protected function configure()
    {
        $this->addArgument('name', InputArgument::OPTIONAL, 'Name description');
    }*/

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    /*protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $output->writeln('Hello swoole:server');
        return self::SUCCESS;
    }*/

    private $server;

    public $agent = [
        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; AcooBrowser; .NET CLR 1.1.4322; .NET CLR 2.0.50727)",
        "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; Acoo Browser; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.0.04506)",
        "Mozilla/4.0 (compatible; MSIE 7.0; AOL 9.5; AOLBuild 4337.35; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)",
        "Mozilla/5.0 (Windows; U; MSIE 9.0; Windows NT 9.0; en-US)",
        "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET CLR 2.0.50727; Media Center PC 6.0)",
        "Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET CLR 1.0.3705; .NET CLR 1.1.4322)",
        "Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.2; .NET CLR 1.1.4322; .NET CLR 2.0.50727; InfoPath.2; .NET CLR 3.0.04506.30)",
        "Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN) AppleWebKit/523.15 (KHTML, like Gecko, Safari/419.3) Arora/0.3 (Change: 287 c9dfb30)",
        "Mozilla/5.0 (X11; U; Linux; en-US) AppleWebKit/527+ (KHTML, like Gecko, Safari/419.3) Arora/0.6",
        "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.2pre) Gecko/20070215 K-Ninja/2.1.1",
        "Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9) Gecko/20080705 Firefox/3.0 Kapiko/3.0",
        "Mozilla/5.0 (X11; Linux i686; U;) Gecko/20070322 Kazehakase/0.4.5",
        "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.8) Gecko Fedora/1.9.0.8-1.fc10 Kazehakase/0.5.6",
        "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/535.20 (KHTML, like Gecko) Chrome/19.0.1036.7 Safari/535.20",
        "Opera/9.80 (Macintosh; Intel Mac OS X 10.6.8; U; fr) Presto/2.9.168 Version/11.52",
    ];

    // ?????????????????????
    protected function configure()
    {
        // setName         ?????????????????????
        // setDescription  ?????????????????????
        $this->setName('swoole:server')
            ->setDescription('Start Swoole Server!');
    }

    // thinkphp??????
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->server = new \swoole_server("127.0.0.1", 9502);
        $this->server->set(array(
            'worker_num'      => 2,
            'daemonize'       => false,  // ????????????,??????????????????SSH???????????????
            'log_file'        => runtime_path() . '/swoole_server.log',
            'task_worker_num' => 200       // task????????????
        ));
        $this->server->on('Start', array($this, 'onStart'));
        $this->server->on('Connect', array($this, 'onConnect'));
        $this->server->on('Receive', array($this, 'onReceive'));
        $this->server->on('Close', array($this, 'onClose'));
        $this->server->on('Task', array($this, 'onTask'));
        $this->server->on('Finish', array($this, 'onFinish'));

        $this->server->start();
    }

    // ?????????????????????
    public function onReceive( \swoole_server $server, $fd, $from_id, $data )
    {
        echo "????????????" . PHP_EOL;
        $list = json_decode($data, true);

        foreach ($list as $key => $value) {
            // ??????????????????
            $server->task($value);
        }
    }

    // ??????????????????
    public function onTask($server, $task_id, $from_id, $data)
    {
        echo "?????????????????????" .$data. PHP_EOL;
        $server->finish($data);
    }

    // ??????????????????????????????
    public function onFinish($server, $task_id, $data) {
        echo date("Y-m-d H:i:s") . "  ????????????". ':'.$data . PHP_EOL;
    }

    // ??????????????????????????????
    public function onStart( $server ) {
        echo "???????????????:" . date("Y-m-d H:i:s") . PHP_EOL;
    }

    // ????????????
    public function onConnect( $server, $fd, $from_id ) {
        echo "?????????????????????:". date("Y-m-d H:i:s") . PHP_EOL;
    }

    // ????????????????????????
    public function onClose( $server, $fd, $from_id ) {
        echo "?????????????????????:". date("Y-m-d H:i:s") . PHP_EOL;
    }
}
