<?php
/*LICENSE
+-----------------------------------------------------------------------+
| SilangPHP Framework                                                   |
+-----------------------------------------------------------------------+
| This program is free software; you can redistribute it and/or modify  |
| it under the terms of the GNU General Public License as published by  |
| the Free Software Foundation. You should have received a copy of the  |
| GNU General Public License along with this program.  If not, see      |
| http://www.gnu.org/licenses/.                                         |
| Copyright (C) 2020. All Rights Reserved.                              |
+-----------------------------------------------------------------------+
| Supports: http://www.github.com/silangtech/SilangPHP                  |
+-----------------------------------------------------------------------+
*/
namespace SilangPHP\Rpc;

class Server extends \Swoole\Server implements \SilangPHP\Rpc\Base
{
    public $worker_num = 2;
    public $user = 'www-data';
    public $group = 'www-data';
    // 测试的时候设为false
    public $daemonize = false;
    public $backlog = 128;
    // 异步耗时处理数
    public $task_worker_num = 4;
    public $tmp_path = '/tmp';
    public $pid_file;
    public $log_file;
    public $host = '0.0.0.0';
    public $port = '9501';
    public $processName = 'SilangPHP_server';

    public $service = [];

    public function __construct()
    {
        parent::__construct($this->host,$this->port,SWOOLE_PROCESS,SWOOLE_SOCK_TCP);
    }

    public function config()
    {
        $this->set([
            'worker_num' => $this->worker_num,
            'user' => $this->user,
            'group' => $this->group,
            'daemonize' => $this->daemonize,
            'backlog' => $this->backlog,
            'task_worker_num' => $this->task_worker_num,
            'pid_file' => $this->tmp_path.'server.pid', //设置pid file 结束的时候就比较好处理了
            'log_file' => $this->tmp_path.'swoole9501.log'
        ]);
    }


    /**
     * 设置好事件
     */
    public function event()
    {
        // 事件
        $this->on('Start', array($this, 'onStart'));
        $this->on('Shutdown', array($this, 'onShutdown'));

        $this->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->on('WorkerError', array($this, 'onWorkerError'));
        $this->on('WorkerStop', array($this, 'onWorkerStop'));

        $this->on('ManagerStart', array($this, 'onManagerStart'));
        $this->on('ManagerStop', array($this, 'onManagerStop'));

        $this->on('Task', array($this, 'onTask'));
        $this->on('Finish', array($this, 'onFinish'));
        $this->on('Close', array($this, 'onClose'));

        $this->on('Connect', array($this, 'onConnect'));
        $this->on('Receive', array($this, 'onReceive'));

    }

    /**
     * 开始运行
     */
    public function run()
    {
        $this->config();
        $this->event();
        $this->start();
    }

    /**
     * 开始进程
     * @param \Swoole\Server $server
     */
    public function onStart(\Swoole\Server $server)
    {
        swoole_set_process_name($this->processName);
        $this->task("test");
        $this->task("test2");
        $this->task("test3");

    }

    public function onShutdown(\swoole_server $server)
    {
    }

    public function onWorkerStart(\swoole_server $server, $worker_id)
    {
        if($worker_id >= $server->setting['worker_num']) {
            swoole_set_process_name($this->processName."_taskworker");
        } else {
            swoole_set_process_name($this->processName."_worker");
        }
    }

    public function onWorkerError(\swoole_server $serv, $worker_id, $worker_pid, $exit_code, $signal)
    {
    }

    public function onWorkerStop(\swoole_server $server, $worker_id)
    {
    }

    public function onManagerStart(\swoole_server $serv)
    {
    }

    public function onManagerStop(\swoole_server $serv)
    {
    }

    public function onTask(\swoole_server $serv, $task_id, $from_id, $data)
    {
        // 获取到数据的一个处理
    }

    public function onFinish(\swoole_server $serv, $task_id, $data)
    {

    }

    public function onClose(\swoole_server $server, $fd, $reactorId)
    {
    }

    public function onConnect(\swoole_server $server, $fd, $from_id)
    {
    }

    public function inv($name, $service)
    {
        $this->service[$name] = $service;
    }

    /**
     * 接收客户端数据
     * @param \swoole_server $server
     * @param $fd
     * @param $reactor_id
     * @param $data
     */
    public function onReceive(\swoole_server $server, $fd, $reactor_id, $data)
    {
        // 根据信息，处理完之后返回
        // 获取到数据后的一个处理
        $data = json_decode($data,true);
        $service = $data['service'];
        $action = $data['action'];
        $param = $data['param'];
        if(isset($this->service[$service]))
        {
            $ser = $this->service[$service];
            $res = call_user_func_array([$ser,$action],$param);
            $server->send($fd, json_encode($res));
        }else{
            $server->send($fd, json_encode(['code'=>'10001','msg'=>'service error']));
        }
    }
}