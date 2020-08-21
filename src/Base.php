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

interface Base
{
    public function onStart(\Swoole\Server $server);

    public function onShutdown(\swoole_server $server);

    public function onWorkerStart(\swoole_server $server, $worker_id);

    public function onWorkerError(\swoole_server $serv, $worker_id, $worker_pid, $exit_code, $signal);

    public function onWorkerStop(\swoole_server $server, $worker_id);

    public function onManagerStart(\swoole_server $serv);

    public function onManagerStop(\swoole_server $serv);

    public function onTask(\swoole_server $serv, $task_id, $from_id, $data);

    public function onFinish(\swoole_server $serv, $task_id, $data);

    public function onClose(\swoole_server $server, $fd, $reactorId);

    public function onConnect(\swoole_server $server, $fd, $from_id);

    public function onReceive(\swoole_server $server, $fd, $reactor_id, $data);
}